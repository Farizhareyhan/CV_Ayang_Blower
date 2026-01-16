<?php
// beli.php — auto isi Nama Pelanggan dari session + prefill produk dari produk-detail.php
date_default_timezone_set('Asia/Jakarta');
session_start();

/*
  Pastikan saat login Anda sudah set session:
  $_SESSION['id_user'] = <id_user>;
*/

$db_host = "127.0.0.1";
$db_user = "root";
$db_pass = "";
$db_name = "dbpemesanan";
$koneksi = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($koneksi->connect_error) die("Koneksi gagal: ".$koneksi->connect_error);

function rupiah($angka){ return "Rp " . number_format((int)$angka, 0, ',', '.'); }

// ---------- Ambil user dari session (auto nama pelanggan) ----------
$login_user_id = isset($_SESSION['id_user']) ? (int)$_SESSION['id_user'] : 0;
$login_user_nama = null;

if ($login_user_id > 0) {
    $stmtU = $koneksi->prepare("SELECT nama_lengkap FROM user WHERE id_user=?");
    $stmtU->bind_param("i", $login_user_id);
    $stmtU->execute();
    $userRow = $stmtU->get_result()->fetch_assoc();
    if ($userRow) $login_user_nama = $userRow['nama_lengkap']; // kolom ada di tabel user :contentReference[oaicite:1]{index=1}
}

// ---------- Prefill produk dari produk-detail.php ----------
$prefill_id_menu = isset($_GET['id_menu']) ? (int)$_GET['id_menu'] : 0;
$prefill_jumlah  = isset($_GET['jumlah'])  ? max(1, (int)$_GET['jumlah']) : 1;

$prefill_produk = null;
if ($prefill_id_menu > 0) {
    $stmtP = $koneksi->prepare("SELECT id_menu, nama_menu, harga FROM produk WHERE id_menu=?");
    $stmtP->bind_param("i", $prefill_id_menu);
    $stmtP->execute();
    $prefill_produk = $stmtP->get_result()->fetch_assoc();
    if (!$prefill_produk) $prefill_id_menu = 0;
}

// ---------- Data dropdown (fallback kalau tidak login / tidak prefill) ----------
$produk = [];
if ($prefill_id_menu === 0) {
    $resProduk = $koneksi->query("SELECT id_menu, nama_menu, harga FROM produk ORDER BY nama_menu ASC");
    while ($row = $resProduk->fetch_assoc()) $produk[] = $row;
}

$pelanggan = [];
if ($login_user_id === 0) {
    // hanya butuh dropdown jika belum login
    $resPelanggan = $koneksi->query("SELECT id_user, nama_lengkap FROM user ORDER BY nama_lengkap ASC");
    while ($row = $resPelanggan->fetch_assoc()) $pelanggan[] = $row;
}

// ---------- Submit ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_user = ($login_user_id > 0) ? $login_user_id : (int)($_POST['id_user'] ?? 0); // pakai session jika ada
    $id_menu = (int)($_POST['id_menu'] ?? 0);
    $jumlah  = max(1, (int)($_POST['jumlah'] ?? 1));
    $tanggal_booking = $_POST['tanggal_booking'] ?? date('Y-m-d'); // disimpan ke pemesanan.tanggal_pemesanan :contentReference[oaicite:2]{index=2}

    if ($id_user <= 0 || $id_menu <= 0) die("Input tidak valid.");

    $stmtH = $koneksi->prepare("SELECT harga FROM produk WHERE id_menu=?");
    $stmtH->bind_param("i", $id_menu);
    $stmtH->execute();
    $hargaRow = $stmtH->get_result()->fetch_assoc();
    if (!$hargaRow) die("Produk tidak ditemukan.");
    $harga = (int)$hargaRow['harga'];
    $total_belanja = $harga * $jumlah;

    $status_awal = 'menunggu_konfirmasi';
    $stmtPesan = $koneksi->prepare("
        INSERT INTO pemesanan (id_user, id_menu, tanggal_pemesanan, total_belanja, status_pembayaran)
        VALUES (?,?,?,?,?)
    ");
    // kolom-kolom di atas ada di tabel pemesanan :contentReference[oaicite:3]{index=3}
    $stmtPesan->bind_param("iisis", $id_user, $id_menu, $tanggal_booking, $total_belanja, $status_awal);
    if (!$stmtPesan->execute()) die("Gagal membuat pemesanan: " . $stmtPesan->error);
    $id_pemesanan_baru = $stmtPesan->insert_id;

    $stmtItem = $koneksi->prepare("INSERT INTO pemesanan_produk (id_pemesanan, id_menu, jumlah) VALUES (?,?,?)");
    $stmtItem->bind_param("iii", $id_pemesanan_baru, $id_menu, $jumlah);
    if (!$stmtItem->execute()) die("Gagal menyimpan detail produk: " . $stmtItem->error);

    header("Location: pembayaran.php?id=".$id_pemesanan_baru);
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Beli</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;background:#f9fafb;padding:24px}
    .card{background:#fff;border:1px solid #e5e7eb;border-radius:16px;padding:20px;max-width:640px;margin:auto;box-shadow:0 2px 8px rgba(0,0,0,.04)}
    label{display:block;font-weight:600;margin-bottom:6px}
    input,select{width:100%;padding:10px;border:1px solid #e5e7eb;border-radius:10px;margin-bottom:12px}
    .readonly{background:#f3f4f6;border:1px dashed #e5e7eb;border-radius:10px;padding:10px;margin-bottom:12px}
    .row{display:flex;gap:12px;flex-wrap:wrap}
    button{padding:12px 16px;border:0;border-radius:12px;background:#111827;color:#fff;font-weight:700;cursor:pointer}
    button:hover{opacity:.9}
    .muted{color:#6b7280;font-size:12px}
  </style>
</head>
<body>
<div class="card">
  <h2>Form Pemesanan</h2>
  <form method="post">

    <?php if ($login_user_id > 0 && $login_user_nama): ?>
      <!-- Nama pelanggan otomatis dari session -->
      <input type="hidden" name="id_user" value="<?= (int)$login_user_id ?>">
      <div class="readonly">
        <b>Pelanggan:</b> <?= htmlspecialchars($login_user_nama) ?>
      </div>
    <?php else: ?>
      <!-- Fallback: pilih pelanggan manual jika belum login -->
      <label for="id_user">Pelanggan</label>
      <select name="id_user" id="id_user" required>
        <option value="">-- Pilih Nama Pelanggan --</option>
        <?php foreach($pelanggan as $p): ?>
          <option value="<?= (int)$p['id_user'] ?>"><?= htmlspecialchars($p['nama_lengkap']) ?></option>
        <?php endforeach; ?>
      </select>
    <?php endif; ?>

    <?php if ($prefill_id_menu > 0 && $prefill_produk): ?>
      <!-- Produk dari produk-detail: terkunci -->
      <input type="hidden" name="id_menu" value="<?= (int)$prefill_produk['id_menu'] ?>">
      <div class="readonly">
        <b>Produk:</b> <?= htmlspecialchars($prefill_produk['nama_menu']) ?><br>
        <b>Harga:</b> <?= rupiah($prefill_produk['harga']) ?>
      </div>
    <?php else: ?>
      <!-- Pilih produk manual -->
      <label for="id_menu">Produk</label>
      <select name="id_menu" id="id_menu" required>
        <option value="">-- Pilih Produk --</option>
        <?php foreach($produk as $pr): ?>
          <option value="<?= (int)$pr['id_menu'] ?>">
            <?= htmlspecialchars($pr['nama_menu']) ?> (<?= rupiah($pr['harga']) ?>)
          </option>
        <?php endforeach; ?>
      </select>
    <?php endif; ?>

    <div class="row">
      <div style="flex:1;min-width:160px">
        <label for="jumlah">Jumlah</label>
        <input type="number" id="jumlah" name="jumlah" value="<?= (int)$prefill_jumlah ?>" min="1" required>
      </div>
      <div style="flex:1;min-width:200px">
        <label for="tanggal_booking">Tanggal Booking</label>
        <input type="date" id="tanggal_booking" name="tanggal_booking" value="<?= date('Y-m-d') ?>" required>
      </div>
    </div>

    <button type="submit">Buat Pemesanan</button>
  </form>
</div>
</body>
</html>
