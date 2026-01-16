<?php
session_start();
include 'koneksi.php';

// Ambil data pesanan dengan tanggal pembayaran
$result = mysqli_query($koneksi, "
    SELECT p.id_pemesanan, p.tanggal_pemesanan, p.total_belanja,
           u.nama_lengkap,
           pb.tanggal_bayar
    FROM pemesanan p
    JOIN user u ON p.id_user = u.id_user
    LEFT JOIN pembayaran pb ON p.id_pemesanan = pb.id_pemesanan
    ORDER BY p.id_pemesanan DESC
");
?>

<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="index.css">
    <link rel="stylesheet" type="text/css" href="fontawesome/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css">

    <title>CV Ayang Blower</title>
  </head>
  <body>
  <!-- Jumbotron -->
      <div class="jumbotron jumbotron-fluid text-center">
        <div class="container">
          <h1 class="display-4"><span class="font-weight-bold">CV Ayang Blower</span></h1>
          <hr>
          <p class="lead font-weight-bold">Silahkan Pilih Produk Dekorasi Anda <br> 
          Enjoy Your Wedding</p>
        </div>
      </div>
  <!-- Akhir Jumbotron -->

  <!-- Navbar -->
      <nav class="navbar navbar-expand-lg  bg-dark">
        <div class="container">
        <a class="navbar-brand text-white" href="admin.php"><strong>CV</strong> Ayang Blower</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
          <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav ml-auto">
            <li class="nav-item"><a class="nav-link mr-4" href="admin.php">HOME</a></li>
            <li class="nav-item"><a class="nav-link mr-4" href="daftar_menu.php">DAFTAR PRODUK DEKORASI</a></li>
            <li class="nav-item"><a class="nav-link mr-4" href="pesanan.php">PESANAN</a></li>
            <li class="nav-item"><a class="nav-link mr-4" href="logout.php">LOGOUT</a></li>
          </ul>
        </div>
       </div> 
      </nav>
  <!-- Akhir Navbar -->

  <!-- Menu -->
<div class="container mt-5">
    <h2>Data Pesanan</h2>

    <?php if (isset($_GET['berhasil'])): ?>
        <div class="alert alert-success">Pembayaran dikonfirmasi!</div>
    <?php endif; ?>
    <?php if (isset($_GET['hapus'])): ?>
        <div class="alert alert-danger">Pesanan berhasil dihapus.</div>
    <?php endif; ?>

    <table class="table table-bordered">
        <thead>
          <tr>
            <th>Nama Pemesanan</th>
            <th>Tanggal Booking</th>
            <th>Tanggal Pembayaran</th>
            <th>Total</th>
            <th>Aksi</th>
          </tr>
        </thead>

        <tbody>
        <?php while($row = mysqli_fetch_assoc($result)) { ?>
          <tr>
            <td><?php echo $row['nama_lengkap']; ?></td>
            <td><?php echo $row['tanggal_pemesanan']; ?></td>
            <td><?php echo !empty($row['tanggal_bayar']) ? $row['tanggal_bayar'] : '-'; ?></td>
            <td>Rp <?php echo number_format($row['total_belanja'], 0, ',', '.'); ?></td>
            <td>
              <a href="clear_pesanan.php?id=<?php echo $row['id_pemesanan']; ?>" class="btn btn-danger">Hapus</a>
            </td>
          </tr>
        <?php } ?>
        </tbody>
    </table>
</div>
  <!-- Akhir Menu -->
    

  <!-- Awal Footer -->
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
              <label class="font-weight-bold">Follow Us </label>
              <a href="#"><img src="images/icon/fb.png" class="mr-3 ml-4"></a>
              <a href="#"><img src="images/icon/ig.png" class="mr-3"></a>
              <a href="#"><img src="images/icon/twitter.png"></a>
            </div>
          </div>
        </div>
      </div>
  <!-- Akhir Footer -->

    <!-- JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>
    <script>
      $(document).ready(function() {
          $('.table').DataTable();
      });
    </script>
  </body>
</html>
