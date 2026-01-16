<?php
include 'koneksi.php';

if (!isset($_GET['id_menu'])) {
    echo "ID Produk tidak ditemukan.";
    exit;
}

$id_menu = $_GET['id_menu'];

// Ambil data produk berdasarkan id
$query = mysqli_query($koneksi, "SELECT * FROM produk WHERE id_menu = '$id_menu'");
$produk = mysqli_fetch_assoc($query);

if (!$produk) {
    echo "Produk tidak ditemukan.";
    exit;
}

// Proses edit jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_menu = $_POST['nama_menu'];
    $jenis_menu = $_POST['jenis_menu'];
    $stok = $_POST['stok'];
    $harga = $_POST['harga'];
    $deskripsi = $_POST['deskripsi'];
    $gambar_lama = $_POST['gambar_lama'];
    $gambar = $gambar_lama;

    // Cek jika ada gambar baru diupload
    if (!empty($_FILES['gambar']['name'])) {
        $gambar = time() . '_' . $_FILES['gambar']['name'];
        move_uploaded_file($_FILES['gambar']['tmp_name'], 'upload/' . $gambar);
        // Optional: hapus gambar lama
        if (file_exists("upload/$gambar_lama")) {
            unlink("upload/$gambar_lama");
        }
    }

    // Update ke database
    $query = "UPDATE produk SET 
                nama_menu = '$nama_menu',
                jenis_menu = '$jenis_menu',
                stok = '$stok',
                harga = '$harga',
                deskripsi = '$deskripsi',
                gambar = '$gambar'
              WHERE id_menu = '$id_menu'";

    $update = mysqli_query($koneksi, $query);

    if ($update) {
        echo "<script>alert('Produk berhasil diperbarui!'); window.location='daftar_menu.php?id_menu=$id_menu';</script>";
        exit;
    } else {
        echo "Gagal memperbarui produk: " . mysqli_error($koneksi);
    }
}
?>

<!-- Form HTML -->
<!DOCTYPE html>
<html>
<head>
    <title>Edit Produk</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
</head>
<body class="container mt-5">
    <h2>Edit Produk</h2>
    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label>Nama Produk</label>
            <input type="text" name="nama_menu" class="form-control" value="<?php echo htmlspecialchars($produk['nama_menu']); ?>" required>
        </div>
        <div class="form-group">
            <label>Jenis Produk</label>
            <input type="text" name="jenis_menu" class="form-control" value="<?php echo htmlspecialchars($produk['jenis_menu']); ?>">
        </div>
        <div class="form-group">
            <label>Stok</label>
            <input type="number" name="stok" class="form-control" value="<?php echo $produk['stok']; ?>" required>
        </div>
        <div class="form-group">
            <label>Harga</label>
            <input type="number" name="harga" class="form-control" value="<?php echo $produk['harga']; ?>" required>
        </div>
        <div class="form-group">
            <label>Deskripsi</label>
            <textarea name="deskripsi" class="form-control" rows="5"><?php echo htmlspecialchars($produk['deskripsi']); ?></textarea>
        </div>
        <div class="form-group">
            <label>Gambar Saat Ini:</label><br>
            <img src="upload/<?php echo $produk['gambar']; ?>" width="200">
            <input type="hidden" name="gambar_lama" value="<?php echo $produk['gambar']; ?>">
        </div>
        <div class="form-group">
            <label>Upload Gambar Baru (jika ingin ganti)</label>
            <input type="file" name="gambar" class="form-control-file">
        </div>
        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        <a href="daftar_menu.php?id_menu=<?php echo $id_menu; ?>" class="btn btn-secondary">Batal</a>
    </form>
</body>
</html>
