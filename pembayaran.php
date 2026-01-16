<?php
// pembayaran.php
// ==============================
// KONFIG
// ==============================
date_default_timezone_set('Asia/Jakarta');

// DB
$db_host = "127.0.0.1";
$db_user = "root";
$db_pass = "";
$db_name = "dbpemesanan";

// Midtrans (Sandbox)
$MIDTRANS_SERVER_KEY = "Mid-server-cNo8mT5sRxGIF9VrtfjBnot4"; // TODO: ganti server key
$MIDTRANS_CLIENT_KEY = "Mid-client-XL6o6vjJisHxXCZ0";  // TODO: ganti client key
$MIDTRANS_SNAP_URL   = "https://app.sandbox.midtrans.com/snap/v1/transactions"; // sandbox
$MIDTRANS_SNAP_JS    = "https://app.sandbox.midtrans.com/snap/snap.js";         // sandbox
$is_production       = false; // set true jika sudah produksi

// ==============================
// KONEKSI DB
// ==============================
$koneksi = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}

// ==============================
// HELPER
// ==============================
function rupiah($angka) {
    return "Rp " . number_format((int)$angka, 0, ',', '.');
}
function indo_full_date($dateYmd) {
    if (!$dateYmd) return "-";
    $hari = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
    $bulan = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    $t = strtotime($dateYmd);
    return $hari[(int)date('w', $t)] . ", " . date('d', $t) . " " . $bulan[(int)date('n', $t)] . " " . date('Y', $t);
}

// ==============================
// PARAM & UPDATE FINISH (opsional dari callback JS)
// ==============================
$id_pemesanan = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id_pemesanan <= 0) die("Parameter id pemesanan tidak valid.");

if (isset($_GET['finish']) && $_GET['finish'] == '1') {
    // Sederhana: update status berdasarkan hasil client-side (final sebaiknya pakai notifikasi server)
    $order_id = $_GET['order_id'] ?? '';
    $trx_status = $_GET['transaction_status'] ?? '';
    if ($order_id && $trx_status) {
        // Tandai sudah dibayar jika status sukses/settlement/capture
        if (in_array($trx_status, ['capture','settlement','success'])) {
            // Update pemesanan
            $stmtUpd = $koneksi->prepare("UPDATE pemesanan SET status_pembayaran='sudah_dibayar' WHERE id_pemesanan=?");
            $stmtUpd->bind_param("i", $id_pemesanan);
            $stmtUpd->execute();

            // Opsional: catat ke tabel pembayaran (jika diperlukan)
            // INSERT sederhana; sesuaikan skema Anda sendiri
            $now = date('Y-m-d H:i:s');
            $stmtBayar = $koneksi->prepare("INSERT INTO pembayaran (id_user, id_menu, id_pemesanan, id_pemesanan_produk, bukti_pembayaran, metode_pembayaran, tanggal_bayar, jumlah) VALUES (0, 0, ?, 0, NULL, 'Midtrans', ?, NULL)");
            $stmtBayar->bind_param("is", $id_pemesanan, $now);
            $stmtBayar->execute();
        }
        // redirect bersih tanpa query finish
        header("Location: pembayaran.php?id=" . $id_pemesanan);
        exit;
    }
}

// ==============================
// QUERY DATA PEMESANAN + NAMA PELANGGAN
// ==============================
$stmt = $koneksi->prepare("
    SELECT p.id_pemesanan,
           p.id_user,
           u.nama_lengkap AS nama_pelanggan,
           p.id_menu,
           p.tanggal_pemesanan,
           p.total_belanja,
           p.status_pembayaran,
           p.snap_token,              -- kolom sudah ada di dump
           p.midtrans_order_id        -- kolom sudah ada di dump
    FROM pemesanan p
    LEFT JOIN user u ON u.id_user = p.id_user
    WHERE p.id_pemesanan = ?
");
$stmt->bind_param("i", $id_pemesanan);
$stmt->execute();
$res = $stmt->get_result();
if (!$res || $res->num_rows === 0) die("Data pemesanan tidak ditemukan.");

$pemesanan = $res->fetch_assoc();

// Ambil item
$stmtItem = $koneksi->prepare("
    SELECT pp.id_pemesanan_produk, pr.id_menu, pr.nama_menu, pr.harga, pp.jumlah
    FROM pemesanan_produk pp
    JOIN produk pr ON pr.id_menu = pp.id_menu
    WHERE pp.id_pemesanan = ?
");
$stmtItem->bind_param("i", $id_pemesanan);
$stmtItem->execute();
$resItem = $stmtItem->get_result();
$items = [];
$total_hitung = 0;
while ($row = $resItem->fetch_assoc()) {
    $row['subtotal'] = ((int)$row['harga']) * ((int)$row['jumlah']);
    $total_hitung += $row['subtotal'];
    $items[] = $row;
}

// ==============================
// BUAT / AMBIL SNAP TOKEN
// ==============================
// Kolom 'snap_token' & 'midtrans_order_id' sudah tersedia di tabel pemesanan :contentReference[oaicite:1]{index=1}.
$snapToken = $pemesanan['snap_token'];
$orderId   = $pemesanan['midtrans_order_id'];

if (empty($snapToken)) {
    // Buat order_id unik: contoh "ORDER-{id}-{timestamp}" seperti data contoh di dump
    $orderId = "ORDER-{$id_pemesanan}-" . time();

    // Payload Midtrans
    $payload = [
        "transaction_details" => [
            "order_id"      => $orderId,
            // Gunakan total dari database agar konsisten
            "gross_amount"  => (int)$pemesanan['total_belanja'],
        ],
        "credit_card" => [
            "secure" => true
        ],
        "customer_details" => [
            "first_name" => $pemesanan['nama_pelanggan'] ?: "Pelanggan",
        ],
        "item_details" => array_map(function($it){
            return [
                "id"       => (string)$it['id_menu'],
                "price"    => (int)$it['harga'],
                "quantity" => (int)$it['jumlah'],
                "name"     => substr($it['nama_menu'], 0, 50),
            ];
        }, $items),
        // Optional: callback URLs (jika ingin)
        // "callbacks" => [
        //     "finish" => "https://domain-anda.com/midtrans/finish"
        // ]
    ];

    // Request Snap Token
    $ch = curl_init($MIDTRANS_SNAP_URL);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_HTTPHEADER     => [
            "Content-Type: application/json",
            "Accept: application/json",
            "Authorization: Basic " . base64_encode($MIDTRANS_SERVER_KEY . ":")
        ],
        CURLOPT_POSTFIELDS     => json_encode($payload)
    ]);
    $response = curl_exec($ch);
    if ($response === false) {
        die("Gagal menghubungi Midtrans: " . curl_error($ch));
    }
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpcode >= 200 && $httpcode < 300) {
        $respData = json_decode($response, true);
        if (!isset($respData['token'])) {
            // Respons tidak mengandung token
            // Anda bisa var_dump($response) untuk debug
            die("Tidak menerima Snap Token dari Midtrans.");
        }
        $snapToken = $respData['token'];

        // Simpan ke DB (snap_token & midtrans_order_id) agar tidak minta token berulang
        $stmtUpd = $koneksi->prepare("UPDATE pemesanan SET snap_token=?, midtrans_order_id=? WHERE id_pemesanan=?");
        $stmtUpd->bind_param("ssi", $snapToken, $orderId, $id_pemesanan);
        $stmtUpd->execute();
    } else {
        // Tampilkan error dari Midtrans
        die("Midtrans error ($httpcode): " . htmlspecialchars($response));
    }
}

// ==============================
// DATA UNTUK TAMPILAN
// ==============================
$tanggal_booking_full = indo_full_date($pemesanan['tanggal_pemesanan']);
$nama_pelanggan = $pemesanan['nama_pelanggan'] ?: "Tidak diketahui";
$total_db = (int)$pemesanan['total_belanja'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pembayaran</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="<?= htmlspecialchars($MIDTRANS_SNAP_JS) ?>" data-client-key="<?= htmlspecialchars($MIDTRANS_CLIENT_KEY) ?>"></script>
    <style>
        body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif; padding:24px; background:#f9fafb;}
        .wrap{max-width:900px;margin:auto;display:grid;grid-template-columns:1fr;gap:16px}
        .card{background:#fff;border:1px solid #e5e7eb;border-radius:16px; padding:20px; box-shadow:0 2px 8px rgba(0,0,0,0.04);}
        .title{margin:0 0 8px 0}
        table{width:100%; border-collapse:collapse; margin-top:8px}
        th,td{padding:10px;border-bottom:1px solid #e5e7eb;text-align:left}
        tfoot td{font-weight:700}
        .muted{color:#6b7280}
        button{padding:12px 16px;border:0;border-radius:12px;background:#111827;color:#fff;font-weight:600;cursor:pointer}
        button:hover{opacity:.9}
        .row{display:flex;gap:12px;flex-wrap:wrap}
        .kv{background:#f3f4f6;border-radius:12px;padding:10px 12px}
        .kv b{display:inline-block;min-width:140px}
    </style>
</head>
<body>
<div class="wrap">
    <div class="card">
        <h2 class="title">Detail Pemesanan #<?= htmlspecialchars($pemesanan['id_pemesanan']) ?></h2>

        <!-- Status DIHILANGKAN (sesuai permintaan) -->

        <div class="row">
            <div class="kv"><b>Nama Pelanggan</b> <?= htmlspecialchars($nama_pelanggan) ?></div>
            <div class="kv"><b>Tanggal Booking</b> <?= htmlspecialchars($tanggal_booking_full) ?></div>
        </div>

        <h3>Ringkasan Item</h3>
        <table>
            <thead>
            <tr>
                <th>Produk</th>
                <th>Harga</th>
                <th>Jumlah</th>
                <th>Subtotal</th>
            </tr>
            </thead>
            <tbody>
            <?php if (count($items) === 0): ?>
                <tr><td colspan="4" class="muted">Belum ada item detail pada pemesanan ini.</td></tr>
            <?php else: ?>
                <?php foreach($items as $it): ?>
                    <tr>
                        <td><?= htmlspecialchars($it['nama_menu']) ?></td>
                        <td><?= rupiah($it['harga']) ?></td>
                        <td><?= (int)$it['jumlah'] ?></td>
                        <td><?= rupiah($it['subtotal']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
            <tfoot>
            <tr>
                <td colspan="3">Total Pemesanan</td>
                <td><?= rupiah($total_db) ?></td>
            </tr>
            </tfoot>
        </table>
    </div>

    <div class="card">
        <h3 class="title">Pembayaran</h3>
        <p class="muted">Klik tombol di bawah untuk membayar lewat Midtrans Snap.</p>
        <button id="payBtn">Bayar Sekarang</button>
    </div>
</div>

<script>
document.getElementById('payBtn').addEventListener('click', function () {
    // Panggil popup Snap
    window.snap.pay("<?= htmlspecialchars($snapToken) ?>", {
        onSuccess: function (result) {
            // Sukses (capture/settlement). Untuk final, gunakan notifikasi server Midtrans.
            const url = "pembayaran.php?id=<?= (int)$pemesanan['id_pemesanan'] ?>&finish=1&order_id="+encodeURIComponent(result.order_id)+"&transaction_status="+encodeURIComponent(result.transaction_status || 'success');
            window.location.href = url;
        },
        onPending: function (result) {
            alert('Transaksi pending. Anda bisa menyelesaikan nanti.');
            console.log(result);
        },
        onError: function (result) {
            alert('Terjadi kesalahan pada pembayaran.');
            console.error(result);
        },
        onClose: function () {
            // User menutup popup tanpa membayar
            console.log('Snap closed by user');
        }
    });
});
</script>
</body>
</html>
