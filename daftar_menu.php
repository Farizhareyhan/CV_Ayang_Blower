<?php
include 'koneksi.php';
$query = mysqli_query($koneksi, "SELECT * FROM produk");
$result = mysqli_fetch_all($query, MYSQLI_ASSOC);
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>CV Ayang Blower</title>

  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <link rel="stylesheet" href="index.css">
  <link rel="stylesheet" href="fontawesome/css/all.min.css">

  <!-- Tambahan CSS -->
  <style>
    .card-img-top {
        height: 250px;
        object-fit: cover;
        width: 100%;
        border-top-left-radius: 5px;
        border-top-right-radius: 5px;
    }

    .card.card-produk {
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        border: none;
        transition: 0.3s ease;
    }

    .card-body {
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .card-title {
        font-size: 18px;
        font-weight: bold;
    }

    .harga {
        color: #28a745;
        font-weight: 600;
    }
  </style>
</head>
<body>

<!-- Jumbotron -->
<div class="jumbotron jumbotron-fluid text-center">
  <div class="container">
    <h1 class="display-4"><span class="font-weight-bold">CV Ayang Blower</span></h1>
    <hr>
    <p class="lead font-weight-bold">Silahkan Pilih Produk Dekorasi Anda <br>Enjoy Your Wedding</p>
  </div>
</div>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg bg-dark">
  <div class="container">
    <a class="navbar-brand text-white" href="admin.php"><strong>CV</strong> Ayang Blower</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ml-auto">
        <li class="nav-item"><a class="nav-link mr-4" href="admin.php">HOME</a></li>
        <li class="nav-item"><a class="nav-link mr-4" href="daftar_menu.php">DAFTAR PRODUK</a></li>
        <li class="nav-item"><a class="nav-link mr-4" href="pesanan.php">PESANAN</a></li>
        <li class="nav-item"><a class="nav-link mr-4" href="logout.php">LOGOUT</a></li>
      </ul>
    </div>
  </div>
</nav>

<!-- Menu -->
<div class="container">
  <a href="tambah_menu.php" class="btn btn-success mt-3">TAMBAH DAFTAR MENU</a>
  <div class="row">

    <?php foreach($result as $produk): ?>
      <div class="col-md-3 mt-4">
        <div class="card card-produk">
          <img src="upload/<?php echo $produk['gambar']; ?>" class="card-img-top" alt="<?php echo $produk['nama_menu']; ?>">
          <div class="card-body text-center">
            <h5 class="card-title"><?php echo $produk['nama_menu']; ?></h5>
            <label class="harga">Rp. <?php echo number_format($produk['harga']); ?></label><br>
            <a href="edit_menu.php?id_menu=<?php echo $produk['id_menu']; ?>" class="btn btn-success btn-sm btn-block">EDIT</a>
            <a href="hapus_menu.php?id_menu=<?php echo $produk['id_menu']; ?>" class="btn btn-danger btn-sm btn-block text-light">HAPUS</a>
          </div>
        </div>
      </div>
    <?php endforeach; ?>

  </div>
</div>

<!-- Footer -->
<hr class="footer">
<div class="container">
  <div class="row footer-body">
    <div class="col-md-6">
      <div class="copyright">
        <strong>Copyright</strong> <i class="far fa-copyright"></i> 2025 - Designed by M Farizha Reyhan
      </div>
    </div>
    <div class="col-md-6 d-flex justify-content-end">
      <div class="icon-contact">
        <label class="font-weight-bold">Follow Us</label>
        <a href="#"><img src="images/icon/fb.png" class="mr-3 ml-4" title="Facebook"></a>
        <a href="#"><img src="images/icon/ig.png" class="mr-3" title="Instagram"></a>
        <a href="#"><img src="images/icon/twitter.png" title="Twitter"></a>
      </div>
    </div>
  </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/jquery.js"></script>

</body>
</html>
