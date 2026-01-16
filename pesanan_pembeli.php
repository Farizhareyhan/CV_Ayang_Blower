<?php
// pesanan_pembeli.php — 2 tabel: Booking vs Pembayaran
date_default_timezone_set('Asia/Jakarta');
session_start();

/* Asumsi saat login Anda set:
   $_SESSION['id_user'] = <id_user>;
*/

$db_host = "127.0.0.1";
$db_user = "root";
$db_pass = "";
$db_name = "dbpemesanan";
$koneksi = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($koneksi->connect_error) die("Koneksi gagal: ".$koneksi->connect_error);

// ---------- Helper ----------
function rupiah($angka){ return "Rp " . number_format((int)$angka, 0, ',', '.'); }
function indo_full_date($dateYmd){
    if (!$dateYmd) return "-";
    $hari  = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
    $bulan = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    $t = strtotime($dateYmd);
    return $hari[(int)date('w',$t)].", ".date('d',$t)." ".$bulan[(int)date('n',$t)]." ".date('Y',$t);
}
function indo_full_datetime($dt){
    if (!$dt) return "-";
    $hari  = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
    $bulan = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    $t = strtotime($dt);
    $tgl = $hari[(int)date('w',$t)].", ".date('d',$t)." ".$bulan[(int)date('n',$t)]." ".date('Y',$t);
    $jam = date('H:i', $t);
    return $tgl." • ".$jam." WIB";
}

// ---------- Identitas pembeli ----------
$login_user_id = isset($_SESSION['id_user']) ? (int)$_SESSION['id_user'] : 0;

// ---------- Query: DAFTAR BOOKING (dari tabel pemesanan) ----------
if ($login_user_id > 0) {
    $stmt = $koneksi->prepare("
        SELECT p.id_pemesanan, u.nama_lengkap AS nama_pemesan,
               p.tanggal_pemesanan, p.total_belanja,
               p.status_pembayaran, p.snap_token
        FROM pemesanan p
        LEFT JOIN user u ON u.id_user = p.id_user
        WHERE p.id_user = ?
        ORDER BY p.id_pemesanan DESC
    ");
    $stmt->bind_param("i", $login_user_id);
} else {
    $stmt = $koneksi->prepare("
        SELECT p.id_pemesanan, u.nama_lengkap AS nama_pemesan,
               p.tanggal_pemesanan, p.total_belanja,
               p.status_pembayaran, p.snap_token
        FROM pemesanan p
        LEFT JOIN user u ON u.id_user = p.id_user
        ORDER BY p.id_pemesanan DESC
    ");
}
$stmt->execute();
$res = $stmt->get_result();
$bookings = [];
while ($r = $res->fetch_assoc()) {
    $r['tanggal_booking_full'] = indo_full_date($r['tanggal_pemesanan']); // << Hari, Tanggal Booking
    $bookings[] = $r;
}

// ---------- Query: RIWAYAT PEMBAYARAN (join pembayaran) ----------
// Menampilkan setiap baris pembayaran (bila satu pesanan punya beberapa pembayaran, semuanya akan tampil)
if ($login_user_id > 0) {
    $stmtPay = $koneksi->prepare("
        SELECT pb.id          AS id_pembayaran,
               p.id_pemesanan,
               u.nama_lengkap AS nama_pemesan,
               p.total_belanja,
               pb.metode_pembayaran,
               pb.tanggal_bayar
        FROM pembayaran pb
        JOIN pemesanan p ON p.id_pemesanan = pb.id_pemesanan
        LEFT JOIN user u ON u.id_user = p.id_user
        WHERE p.id_user = ?
        ORDER BY COALESCE(pb.tanggal_bayar, '1970-01-01') DESC, pb.id DESC
    ");
    $stmtPay->bind_param("i", $login_user_id);
} else {
    $stmtPay = $koneksi->prepare("
        SELECT pb.id          AS id_pembayaran,
               p.id_pemesanan,
               u.nama_lengkap AS nama_pemesan,
               p.total_belanja,
               pb.metode_pembayaran,
               pb.tanggal_bayar
        FROM pembayaran pb
        JOIN pemesanan p ON p.id_pemesanan = pb.id_pemesanan
        LEFT JOIN user u ON u.id_user = p.id_user
        ORDER BY COALESCE(pb.tanggal_bayar, '1970-01-01') DESC, pb.id DESC
    ");
}
$stmtPay->execute();
$resPay = $stmtPay->get_result();
$pembayaran = [];
while ($r = $resPay->fetch_assoc()) {
    // tanggal_bayar bisa NULL pada data lama; tangani aman
    $r['tanggal_bayar_full'] = $r['tanggal_bayar'] ? indo_full_datetime($r['tanggal_bayar']) : "-";
    $pembayaran[] = $r;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pesanan Pembeli</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        :root{
            --blue:#12a4e6; --blue-dark:#0b82b8;
            --muted:#6b7280; --bg:#f3f6fb;
            --ok:#e9e5ff; --okc:#4c3bcf;
        }
        body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;background:var(--bg);margin:0}
        .container{max-width:1100px;margin:32px auto;padding:0 16px}
        h2{margin:0 0 16px 0}
        .card{background:#fff;border:1px solid #e5e7eb;border-radius:16px;box-shadow:0 2px 10px rgba(0,0,0,.04); overflow:hidden; margin-bottom:22px}
        .table{width:100%;border-collapse:collapse}
        .table thead th{background:var(--blue);color:#fff;font-weight:700;padding:14px;text-align:left}
        .table tbody td{padding:14px;border-bottom:1px solid #eef2f7;vertical-align:top}
        .table tbody tr:hover{background:#fafcff}
        .pill{display:inline-block;padding:6px 10px;border-radius:999px;background:var(--ok);color:var(--okc);font-weight:700;font-size:12px}
        .btn{display:inline-block;padding:8px 12px;border-radius:10px;background:var(--blue);color:#fff;text-decoration:none;font-weight:700}
        .btn:hover{background:var(--blue-dark)}
        .muted{color:var(--muted)}
        .date-col .top{font-weight:800}
        .date-col .sub{font-size:12px;color:var(--muted)}
        .header{padding:18px 18px 0 18px}
        .table-wrap{padding:0 0 8px 0}
        .empty{padding:18px;color:var(--muted);text-align:center}
    </style>
</head>
<body>
<div class="container">
    <h2>Daftar Pesanan Saya</h2>

    <!-- Tabel 1: Booking -->
    <div class="card">
        <div class="header"><h3 style="margin:0">Daftar Booking</h3></div>
        <div class="table-wrap">
            <table class="table">
                <thead>
                <tr>
                    <th style="width:30%">Nama Pemesan</th>
                    <th style="width:30%">Tanggal Booking</th>
                    <th style="width:20%">Total Belanja</th>
                    <th style="width:20%">Aksi</th>
                </tr>
                </thead>
                <tbody>
                <?php if (count($bookings) === 0): ?>
                    <tr><td colspan="4" class="empty">Belum ada booking.</td></tr>
                <?php else: ?>
                    <?php foreach($bookings as $b): ?>
                        <tr>
                            <td><?= htmlspecialchars($b['nama_pemesan'] ?: 'Tidak diketahui') ?></td>
                            <td class="date-col">
                                <div class="top"><?= htmlspecialchars($b['tanggal_booking_full']) ?></div>
                                <div class="sub"><?= htmlspecialchars($b['tanggal_pemesanan']) ?></div>
                            </td>
                            <td><?= rupiah($b['total_belanja']) ?></td>
                            <td>
                                <?php
                                if (isset($b['status_pembayaran']) && $b['status_pembayaran'] === 'sudah_dibayar') {
                                    echo '<span class="pill">Lunas</span>';
                                } else if (!empty($b['snap_token'])) {
                                    echo '<a class="btn" href="pembayaran.php?id='.(int)$b['id_pemesanan'].'">Bayar</a>';
                                } else {
                                    echo '<span class="muted">Token tidak tersedia</span>';
                                }
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Tabel 2: Riwayat Pembayaran -->
    <div class="card">
        <div class="header"><h3 style="margin:0">Riwayat Pembayaran</h3></div>
        <div class="table-wrap">
            <table class="table">
                <thead>
                <tr>
                    <th style="width:28%">Nama Pemesan</th>
                    <th style="width:32%">Tanggal Pembayaran</th>
                    <th style="width:20%">Total</th>
                    <th style="width:20%">Metode</th>
                </tr>
                </thead>
                <tbody>
                <?php if (count($pembayaran) === 0): ?>
                    <tr><td colspan="4" class="empty">Belum ada pembayaran.</td></tr>
                <?php else: ?>
                    <?php foreach($pembayaran as $pay): ?>
                        <tr>
                            <td><?= htmlspecialchars($pay['nama_pemesan'] ?: 'Tidak diketahui') ?></td>
                            <td class="date-col">
                                <div class="top"><?= htmlspecialchars($pay['tanggal_bayar_full']) ?></div>
                                <div class="sub">
                                    ID Pesanan: #<?= (int)$pay['id_pemesanan'] ?>
                                </div>
                            </td>
                            <td><?= rupiah($pay['total_belanja']) ?></td>
                            <td><?= htmlspecialchars($pay['metode_pembayaran'] ?: '-') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
