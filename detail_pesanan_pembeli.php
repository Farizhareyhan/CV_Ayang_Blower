<?php 
    include('koneksi.php');
    session_start();
    if (!isset($_SESSION['login_user'])) {
        header("location: login.php");
        exit;
    }
    
    // Ambil ID Pemesanan
    if (!isset($_GET['id'])) {
        echo "<script>alert('ID pesanan tidak ditemukan.'); location='menu_pembeli.php';</script>";
        exit;
    }
    $id_pemesanan = intval($_GET['id']);
    
    // Ambil Data Pemesanan
    $query = mysqli_query($koneksi, "SELECT * FROM pemesanan WHERE id_pemesanan = $id_pemesanan");
    $pemesanan = mysqli_fetch_assoc($query);
    
    if (!$pemesanan) {
        echo "<p>Pesanan tidak ditemukan.</p>";
        exit;
    }
    
    // Ambil Bukti Pembayaran
    $query_bukti = mysqli_query($koneksi, "SELECT bukti_pembayaran FROM pembayaran WHERE id_pemesanan = $id_pemesanan");
    $data_bukti = mysqli_fetch_assoc($query_bukti);
    $bukti_pembayaran = $data_bukti ? $data_bukti['bukti_pembayaran'] : null;
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <title>Detail Pesanan</title>
</head>
<body>
    <div class="container mt-5">
        <h3>Detail Pesanan</h3>
        <p>ID Pesanan: <?php echo $id_pemesanan; ?></p>
        <p>Nama Pemesanan: <?php echo $pemesanan['id_pemesanan']; ?></p>
        <p>Produk: <?php echo $pemesanan['id_pemesanan']; ?></p>
        <p>Total Harga: Rp <?php echo number_format($pemesanan['total_belanja'], 0, ',', '.'); ?></p>

        <?php if ($bukti_pembayaran): ?>
            <div class="mt-4">
                <h5>Bukti Pembayaran:</h5>
                <img src="uploads/<?php echo $bukti_pembayaran; ?>" class="img-fluid img-thumbnail" alt="Bukti Pembayaran">
            </div>
        <?php else: ?>
            <p><i>Bukti pembayaran belum diunggah.</i></p>
        <?php endif; ?>
        
        <a href="menu_pembeli.php" class="btn btn-primary mt-3">Kembali</a>
    </div>
</body>
</html>
""
