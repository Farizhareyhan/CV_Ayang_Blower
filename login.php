<?php 
session_start();
include 'koneksi.php'; // pastikan path ke koneksi benar

/* ====== PROSES LOGIN (mempertahankan logika lama) ====== */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = isset($_POST['username']) ? $_POST['username'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    // NOTE: Ini mengikuti pola asli project kamu (tanpa hashing).
    // Jika di DB pakai hashing, ganti verifikasi sesuai kebutuhannya.
    $cek_data = mysqli_query($koneksi, "SELECT * FROM user WHERE username = '$user' AND password = '$password'");
    $hasil    = mysqli_fetch_array($cek_data);
    $row      = mysqli_num_rows($cek_data);

    if ($row > 0) {
        $status     = $hasil['status'];
        $login_user = $hasil['username'];

        $_SESSION['login_user'] = $login_user;
        $_SESSION['id_user']    = $hasil['id_user']; // jika ada di tabel user
        $_SESSION['status']     = $hasil['status'];  // optional

        if ($status === 'admin') {
            header('Location: admin.php');
            exit;
        } elseif ($status === 'user') {
            header('Location: user.php');
            exit;
        } else {
            // fallback jika status tak dikenali
            header('Location: index.php');
            exit;
        }
    } else {
        $error_login = 'Username atau password salah!';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>CV Ayang Blower — Login | Dekorasi</title>

  <!-- Bootstrap & Icons -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

  <style>
    :root{
      --primary:#ff8680; /* nuansa dari toko roti */
      --dark:#2b2d42;
    }
    body{
      background:#fff; color:#333;
    }
    /* HERO ala index toko roti */
    .hero{
      background: linear-gradient(180deg, rgba(255,134,128,.85), rgba(255,134,128,.65)), url('image/home/1.jpg');
      background-size:cover; background-position:center;
      min-height:260px; display:flex; align-items:center; color:#fff;
    }
    .hero h1{ font-weight:700; letter-spacing:.5px; }
    /* Kartu Login */
    .card-login{
      margin-top:-80px; border:0; border-radius:1rem;
      box-shadow:0 15px 35px rgba(0,0,0,.1);
    }
    .brand-badge{
      background:#fff; color:var(--primary); font-weight:700; display:inline-block;
      padding:.35rem .75rem; border-radius:2rem; box-shadow:0 6px 16px rgba(0,0,0,.08);
    }
    .btn-primary{ background:var(--primary); border-color:var(--primary); }
    .btn-primary:hover{ filter:brightness(.95); }
    .footer{ margin-top:64px; background:var(--primary); color:#fff; }
    .input-group-text{ background:#fff; }
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
        <li class="nav-item"><a class="nav-link" href="tentang.php">Tentang Dekorasi</a></li>
        <li class="nav-item active"><a class="nav-link" href="login.php">Login</a></li>
      </ul>
    </div>
  </nav>

  <!-- HERO -->
  <header class="hero">
    <div class="container py-5">
      <h1 class="display-5 mb-2">Dekorasi yang Membuat Acara Lebih Berkesan</h1>
      <p class="lead mb-0">Masuk untuk mengelola pesanan, katalog dekorasi, dan transaksi Anda.</p>
    </div>
  </header>

  <!-- LOGIN CARD -->
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-7 col-lg-5">
        <div class="card card-login p-4 p-md-5">
          <div class="card-body">
            <h4 class="mb-1">Masuk Akun</h4>
            <p class="text-muted mb-4">Gunakan akun Anda untuk mengakses dashboard <strong>CV Ayang Blower</strong>.</p>

            <?php if (!empty($error_login)): ?>
              <div class="alert alert-danger" role="alert">
                <?php echo $error_login; ?>
              </div>
            <?php endif; ?>

            <!-- Form Login -->
            <form method="POST" action="">
              <div class="form-group">
                <label>Username</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <div class="input-group-text"><i class="fas fa-user"></i></div>
                  </div>
                  <input type="text" class="form-control" placeholder="Masukkan Username" name="username" required>
                </div>
              </div>
              <div class="form-group">
                <label>Password</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <div class="input-group-text"><i class="fas fa-lock"></i></div>
                  </div>
                  <input type="password" class="form-control" placeholder="Masukkan Password" name="password" required>
                </div>
              </div>
              <button type="submit" name="submit" class="btn btn-primary btn-block">Login</button>
            </form>
          </div>
        </div>
        <p class="text-center mt-3 text-muted">Belum punya akun? <a href="register.php">Daftar</a></p>
      </div>
    </div>

    <!-- Section tentang dekorasi singkat -->
    <div class="row align-items-center mt-5">
      <div class="col-md-6">
        <h3 class="mb-3">Tentang Dekorasi Kami</h3>
        <p>Kami menyediakan dekorasi backdrop, dan set pesta yang elegan untuk lamaran, hingga pernikahan</p>
        <ul class="mb-0">
          <li>Paket dekorasi tematik </li>
          <li>Instalasi cepat dan rapi</li>
          <li>Harga transparan, hasil memuaskan</li>
        </ul>
      </div>
      <div class="col-md-6">
        <div class="p-3 p-md-4 rounded" style="background:#fff5f5; border:1px dashed var(--primary)">
          <h5 class="mb-2">Butuh bantuan?</h5>
          <p class="mb-0">Hubungi kami: <a href="mailto:info@cv-ayangblower.local">info@cv-ayangblower.local</a></p>
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
