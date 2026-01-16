<?php session_start(); ?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Tentang Dekorasi — CV Ayang Blower</title>

  <!-- Bootstrap & Icons -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

  <style>
    :root{ --primary:#ff8680; --dark:#2b2d42; }
    body{ background:#fff; color:#333; }
    .hero{
      background: linear-gradient(180deg, rgba(255,134,128,.85), rgba(255,134,128,.65)), url('image/home/1.jpg');
      background-size:cover; background-position:center;
      min-height:260px; display:flex; align-items:center; color:#fff;
    }
    .hero h1{ font-weight:700; letter-spacing:.5px; }
    .brand-badge{
      background:#fff; color:var(--primary); font-weight:700; display:inline-block;
      padding:.35rem .75rem; border-radius:2rem; box-shadow:0 6px 16px rgba(0,0,0,.08);
    }
    .footer{ margin-top:64px; background:var(--primary); color:#fff; }
    .card-soft{ border:0; border-radius:1rem; box-shadow:0 15px 35px rgba(0,0,0,.1); }
  </style>
</head>
<body>

  <!-- NAVBAR -->
  <nav class="navbar navbar-expand-lg navbar-light" style="background:#fff; box-shadow:0 6px 16px rgba(0,0,0,.06)">
    <a class="navbar-brand" href="index.php">
      <span class="brand-badge">CV Ayang Blower</span>
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navDekor" aria-controls="navDekor" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navDekor">
      <ul class="navbar-nav ml-auto">
        <li class="nav-item"><a class="nav-link" href="index.php">Beranda</a></li>
        <li class="nav-item"><a class="nav-link" href="produk.php">Produk</a></li>
        <li class="nav-item active"><a class="nav-link" href="tentang.php">Tentang Dekorasi</a></li>
        <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
      </ul>
    </div>
  </nav>

  <!-- HERO -->
  <header class="hero">
    <div class="container py-5">
      <h1 class="display-5 mb-2">Tentang Dekorasi CV Ayang Blower</h1>
      <p class="lead mb-0">Kami fokus pada detail agar momen Anda tak terlupakan.</p>
    </div>
  </header>

  <!-- CONTENT -->
  <div class="container mt-5">
    <div class="row mb-4">
      <div class="col-md-6">
        <h3>Visi & Misi</h3>
        <p>Kami membantu Anda mewujudkan tema impian dengan tim profesional berpengalaman, memadukan seni dan kreativitas dalam setiap sentuhan dekorasi.</p>
        <ul>
          <li>Balon & Backdrop premium</li>
          <li>Set meja dessert & signage</li>
          <li>Dekor kustom sesuai brand/event</li>
        </ul>
      </div>
      <div class="col-md-6">
        <div class="card card-soft p-4">
          <h5 class="mb-2">Area Layanan</h5>
          <p class="mb-0">Pengantaran & instalasi ke area sekitar (isi sesuai domisili bisnis Anda). Kami melayani acara indoor maupun outdoor.</p>
        </div>
      </div>
    </div>

    <!-- Highlight -->
    <div class="row text-center">
      <div class="col-md-4 mb-4">
        <div class="card card-soft p-4 h-100">
          <i class="fas fa-gem fa-2x mb-3 text-primary"></i>
          <h5>Kualitas Premium</h5>
          <p>Material pilihan untuk tampilan yang mewah dan tahan lama.</p>
        </div>
      </div>
      <div class="col-md-4 mb-4">
        <div class="card card-soft p-4 h-100">
          <i class="fas fa-clock fa-2x mb-3 text-primary"></i>
          <h5>Instalasi Tepat Waktu</h5>
          <p>Tim profesional memastikan pemasangan dekorasi selesai sesuai jadwal.</p>
        </div>
      </div>
      <div class="col-md-4 mb-4">
        <div class="card card-soft p-4 h-100">
          <i class="fas fa-handshake fa-2x mb-3 text-primary"></i>
          <h5>Konsultasi Gratis</h5>
          <p>Dapatkan saran tema dan layout dekorasi tanpa biaya tambahan.</p>
        </div>
      </div>
    </div>
  </div>

  <!-- FOOTER -->
  <footer class="footer mt-5">
    <div class="container py-3 text-center">
      <small>&copy; <?php echo date('Y'); ?> CV Ayang Blower — Dekorasi</small>
    </div>
  </footer>

  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
