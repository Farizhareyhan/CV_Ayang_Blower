<?php
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_menu = $_POST['nama_menu'];
    $jenis_menu = $_POST['jenis_menu'];
    $stok = $_POST['stok'];
    $harga = $_POST['harga'];
    $deskripsi = $_POST['deskripsi'];
    $id_user = 0; // default untuk admin/non-user login

    // Validasi dan upload gambar
    $tmp_gambar = $_FILES['gambar']['tmp_name'];
    $nama_asli = $_FILES['gambar']['name'];
    $mime = mime_content_type($tmp_gambar);
    $info = getimagesize($tmp_gambar);

    $allowed_mimes = ['image/jpeg', 'image/png', 'image/gif'];
    if (!$info || !in_array($mime, $allowed_mimes)) {
        echo "<script>alert('Gambar tidak valid. Hanya JPEG, PNG, GIF yang diperbolehkan.');</script>";
        exit;
    }

    // Tentukan nama file dan folder simpan
    $ext = pathinfo($nama_asli, PATHINFO_EXTENSION);
    $nama_baru = uniqid('menu_') . '.' . $ext;
    $folder = 'upload/' . $nama_baru;
    if (!move_uploaded_file($tmp_gambar, $folder)) {
        echo "<script>alert('Gagal menyimpan gambar ke folder upload/.');</script>";
        exit;
    }

    // Simpan ke database
    $query = "INSERT INTO produk (id_menu, nama_menu, jenis_menu, stok, harga, deskripsi, gambar)
              VALUES ('$id_menu', '$nama_menu', '$jenis_menu', '$stok', '$harga', '$deskripsi', '$nama_baru')";

    if (mysqli_query($koneksi, $query)) {
        header("Location: daftar_menu.php?status=sukses");
        exit;
    } else {
        echo "<script>alert('Gagal menyimpan ke database: " . mysqli_error($koneksi) . "');</script>";
    }
}
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Tambah Menu</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
  <h3 class="text-center mb-4">Form Tambah Produk Dekorasi</h3>
  <form method="POST" enctype="multipart/form-data">
    <div class="form-group">
      <label>Nama Produk</label>
      <input type="text" class="form-control" name="nama_menu" required>
    </div>

    <div class="form-group">
      <label>Jenis Produk</label><br>
      <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="jenis_menu" value="Barang" checked>
        <label class="form-check-label">Barang</label>
      </div>
      <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="jenis_menu" value="Jasa">
        <label class="form-check-label">Jasa</label>
      </div>
    </div>

    <div class="form-group">
      <label>Stok</label>
      <input type="number" class="form-control" name="stok" required>
    </div>

    <div class="form-group">
      <label>Harga</label>
      <input type="number" class="form-control" name="harga" required>
    </div>

    <div class="form-group">
      <label>Deskripsi Produk</label>
      <textarea class="form-control" name="deskripsi" rows="4" required></textarea>
    </div>

    <div class="form-group">
      <label>Gambar Produk</label>
      <input type="file" class="form-control-file" name="gambar" accept="image/*" required>
    </div>

    <button type="submit" class="btn btn-primary">Tambah</button>
    <button type="reset" class="btn btn-secondary">Reset</button>
  </form>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
