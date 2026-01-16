<?php
    include('koneksi.php');
    session_start();
    
    if (!isset($_SESSION['login_user'])) {
        header("location: login.php");
        exit;
    }

    
    $id_menu = intval($_GET['id_menu']);
    
    // Ambil Data Produk Berdasarkan ID
    $query = mysqli_query($koneksi, "SELECT * FROM produk WHERE id_menu = $id_menu");
    $produk = mysqli_fetch_assoc($query);
    
    if (!$produk) {
        echo "<p>Produk tidak ditemukan.</p>";
        exit;
    }
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="produk-detail.css">

    
<title>Detail Produk - <?php echo $produk['nama_produk']; ?></title>
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-6">
                <img src="upload/<?php echo $produk['gambar']; ?>" class="img-fluid img-thumbnail" alt="<?php echo $produk['nama_menu']; ?>">
            </div>
            <div class="col-md-6">
                <h2><?php echo $produk['nama_menu']; ?></h2>
                <p><strong>Harga:</strong> Rp <?php echo number_format($produk['harga'], 0, ',', '.'); ?></p>
                <p><strong>Deskripsi Produk:</strong></p>
                <?php
                $delimiter_regex = '/[\r\n;\-•]+/';
                $items = preg_split($delimiter_regex, $produk['deskripsi']);

                if (count($items) > 1) {
                    echo "<ul>";
                    foreach ($items as $item) {
                        $item = trim($item);
                        if (!empty($item)) {
                            echo "<li>" . htmlspecialchars($item) . "</li>";
                        }
                    }
                    echo "</ul>";
                } else {
                    echo "<p>" . htmlspecialchars($produk['deskripsi']) . "</p>";
                }
                ?>


                
                <a href="beli.php?id_menu=<?php echo $produk['id_menu']; ?>" class="btn btn-success btn-lg mt-3">Pesan Sekarang</a>
                <a href="menu_pembeli.php" class="btn btn-secondary btn-lg mt-3">Kembali</a>
            </div>
        </div>
    </div>
</body>
</html>