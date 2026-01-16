<?php
session_start();
include 'koneksi.php';

/* =========================
   LOGIN FLAG
   Ganti sesuai session milikmu: 'login_user' / 'username' / dsb.
========================= */
$LOGGED_IN = isset($_SESSION['login_user']) || isset($_SESSION['username']);

/* =========================
   PARAM & QUERY DASAR
========================= */
$q      = isset($_GET['q']) ? trim($_GET['q']) : '';
$page   = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit  = 9;
$offset = ($page - 1) * $limit;

$where = "WHERE nama_menu NOT IN ('Dekor 1 Wedding Package', 'Kipas Blower 2')";
if ($q !== "") {
  $safe  = mysqli_real_escape_string($koneksi, $q);
  $where .= " AND nama_menu LIKE '%$safe%'";
}


$sql_count = "SELECT COUNT(*) AS total FROM produk $where";
$res_count = mysqli_query($koneksi, $sql_count);
$row_count = mysqli_fetch_assoc($res_count);
$total     = (int)$row_count['total'];
$pages     = max(1, (int)ceil($total / $limit));

$sql = "SELECT * FROM produk $where ORDER BY id_menu DESC LIMIT $limit OFFSET $offset";
$res = mysqli_query($koneksi, $sql);

/* =========================
   HELPERS
========================= */
function rupiah($n){
  return "Rp. " . number_format((int)$n, 0, ',', '.');
}

/* cari file case-insensitive di direktori */
function find_case_insensitive($dir, $target_basename_lower){
  if (!is_dir($dir)) return null;
  $dh = opendir($dir);
  if (!$dh) return null;
  while (($file = readdir($dh)) !== false) {
    if ($file === '.' || $file === '..') continue;
    if (strtolower($file) === $target_basename_lower) {
      closedir($dh);
      return rtrim($dir,'/').'/'.$file;
    }
  }
  closedir($dh);
  return null;
}

/* resolve URL gambar yang robust */
function asset_url($raw){
  $raw = trim((string)$raw, " \t\n\r\0\x0B\xC2\xA0");
  if ($raw === '') return 'https://via.placeholder.com/640x400?text=Dekorasi';
  if (preg_match('#^https?://#i', $raw)) return $raw;
  $raw = str_replace('\\', '/', $raw);

  $bases = ['images/', 'image/', 'img/', 'assets/images/', ''];

  $candidates = [];
  if (strpos($raw, '/') !== false) $candidates[] = $raw;
  foreach ($bases as $b) $candidates[] = $b . basename($raw);

  $docroot   = rtrim($_SERVER['DOCUMENT_ROOT'], '/\\');
  $scriptDir = rtrim(str_replace('\\','/', dirname($_SERVER['SCRIPT_NAME'])), '/');
  if ($scriptDir === '/' || $scriptDir === '\\') $scriptDir = '';

  foreach ($candidates as $rel) {
    $parts = explode('/', $rel);
    $file  = array_pop($parts);
    $dir   = implode('/', $parts);
    $web   = ($dir ? $dir.'/' : '') . rawurlencode($file);
    $abs   = $docroot . ($scriptDir ? '/'.$scriptDir : '') . '/' . $web;
    if (file_exists($abs)) return $web;
  }

  $targetLower = strtolower(basename($raw));
  foreach ($bases as $b) {
    $dirRel = $b;
    $dirAbs = $docroot . ($scriptDir ? '/'.$scriptDir : '') . '/' . $dirRel;
    $found  = find_case_insensitive($dirAbs, $targetLower);
    if ($found) return $dirRel . basename($found);
  }

  $parts = explode('/', $candidates[0]);
  $file  = array_pop($parts);
  $dir   = implode('/', $parts);
  return ($dir ? $dir.'/' : '') . rawurlencode($file);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Produk — CV Ayang Blower</title>

  <!-- Bootstrap & Icons -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

  <style>
    :root{ --primary:#ff8680; --dark:#2b2d42; }
    body{ background:#fff; color:#333; }
    .hero{
      background: linear-gradient(180deg, rgba(255,134,128,.85), rgba(255,134,128,.65)), url('image/home/1.jpg');
      background-size:cover; background-position:center;
      min-height:220px; display:flex; align-items:center; color:#fff;
    }
    .brand-badge{
      background:#fff; color:var(--primary); font-weight:700; display:inline-block;
      padding:.35rem .75rem; border-radius:2rem; box-shadow:0 6px 16px rgba(0,0,0,.08);
    }
    .footer{ margin-top:64px; background:var(--primary); color:#fff; }
    .card-soft{ border:0; border-radius:1rem; box-shadow:0 15px 35px rgba(0,0,0,.1); }
    .thumb{ width:100%; height:220px; object-fit:cover; border-top-left-radius:1rem; border-top-right-radius:1rem; }
    .price{ font-weight:700; }
    .pagination .page-link{ color:#ff6b6b; }
    .pagination .page-item.active .page-link{ background:#ff6b6b; border-color:#ff6b6b; color:#fff; }
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
        <li class="nav-item active"><a class="nav-link" href="produk.php">Produk</a></li>
        <li class="nav-item"><a class="nav-link" href="tentang.php">Tentang Dekorasi</a></li>
        <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
      </ul>
    </div>
  </nav>

  <!-- HERO -->
  <header class="hero">
    <div class="container py-4">
      <h1 class="h3 mb-0">Katalog Produk Dekorasi</h1>
    </div>
  </header>

  <div class="container mt-4">

    <!-- PAKET FAVORIT (statis) -->
    <h3 class="mb-4 text-center">Paket Dekorasi Favorit</h3>
    <div class="row mb-5">
      <?php
        // gambar, nama, harga, id_menu (dummy id untuk ke login.php)
        $favorit = [
          ['images/dekor 33.jpg','Dekorasi Elegant Garden','14000000',101],
          ['images/dekor sedeng.jpg','Dekorasi Golden Classic','18000000',102],
          ['images/dekor tengah.jpg','Dekorasi Floral Glam','16500000',103],
          ['images/Dekor mahal.jpg','Dekorasi Luxury Pastel','20000000',104],
          ['images/Dekor 1 bagian murah.jpg','Dekorasi Elegant Blue','12000000',105],
        ];
        foreach ($favorit as $f):
          $src  = asset_url($f[0]);
          $name = $f[1];
          $harga= $f[2];
          $id   = (int)$f[3];
      ?>
      <div class="col-sm-6 col-md-4 mb-4">
        <div class="card card-soft h-100">
          <img src="<?php echo htmlspecialchars($src); ?>"
               class="thumb"
               alt="<?php echo htmlspecialchars($name); ?>"
               onerror="this.onerror=null;this.src='https://via.placeholder.com/640x400?text=Gambar+Tidak+Ada';">
          <div class="card-body d-flex flex-column">
            <h5 class="card-title mb-1"><?php echo htmlspecialchars($name); ?></h5>
            <div class="mb-3 price text-primary"><?php echo rupiah($harga); ?></div>

            <?php if (!$LOGGED_IN): ?>
              <a href="login.php"
                 onclick="alert('Silakan login terlebih dahulu');"
                 class="btn btn-outline-primary mt-auto">Lihat Detail</a>
            <?php else: ?>
              <a href="login.php?id_menu=<?php echo $id; ?>"
                 class="btn btn-outline-primary mt-auto">Lihat Detail</a>
            <?php endif; ?>

          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>

    <!-- SEARCH -->
    <form class="mb-4" method="get" action="produk.php">
      <div class="form-row">
        <div class="col-md-9 mb-2">
          <input type="text" name="q" class="form-control" placeholder="Cari dekorasi (mis. balon, backdrop)..." value="<?php echo htmlspecialchars($q); ?>">
        </div>
        <div class="col-md-3">
          <button class="btn btn-primary btn-block" type="submit"><i class="fas fa-search mr-1"></i>Cari</button>
        </div>
      </div>
    </form>

    <!-- GRID PRODUK (database) -->
    <div class="row">
      <?php if ($total === 0): ?>
        <div class="col-12">
          <div class="alert alert-info">
            Belum ada produk<?php echo $q ? " untuk kata kunci <strong>".htmlspecialchars($q)."</strong>" : ""; ?>.
          </div>
        </div>
      <?php endif; ?>

      <?php while($row = mysqli_fetch_assoc($res)): ?>
        <?php
          $img = asset_url(isset($row['gambar']) ? $row['gambar'] : '');
          $idb = (int)$row['id_menu'];
        ?>
        <div class="col-sm-6 col-md-4 mb-4">
          <div class="card card-soft h-100">
            <img
              src="<?php echo htmlspecialchars($img, ENT_QUOTES, 'UTF-8'); ?>"
              class="thumb"
              alt="<?php echo htmlspecialchars($row['nama_menu']); ?>"
              onerror="this.onerror=null;this.src='https://via.placeholder.com/640x400?text=Gambar+Tidak+Ada';">
            <div class="card-body d-flex flex-column">
              <h5 class="card-title mb-1"><?php echo htmlspecialchars($row['nama_menu']); ?></h5>
              <div class="mb-3 price text-primary"><?php echo rupiah($row['harga']); ?></div>

              <?php if (!$LOGGED_IN): ?>
                <a href="login.php"
                   onclick="alert('Silakan login terlebih dahulu');"
                   class="btn btn-outline-primary mt-auto">Lihat Detail</a>
              <?php else: ?>
                <a href="login.php?id_menu=<?php echo $idb; ?>"
                   class="btn btn-outline-primary mt-auto">Lihat Detail</a>
              <?php endif; ?>

            </div>
          </div>
        </div>
      <?php endwhile; ?>
    </div>

    <!-- PAGINATION -->
    <?php if ($pages > 1): ?>
      <nav aria-label="Navigasi halaman">
        <ul class="pagination justify-content-center">
          <?php
            function page_link($p, $q){
              $params = [];
              if ($q !== "") $params['q'] = $q;
              $params['page'] = $p;
              return 'produk.php?' . http_build_query($params);
            }
            $prev_disabled = ($page <= 1) ? ' disabled' : '';
            echo '<li class="page-item'.$prev_disabled.'"><a class="page-link" href="'.($page>1 ? page_link($page-1, $q) : '#').'" aria-label="Sebelumnya">&laquo;</a></li>';

            $start = max(1, $page - 2);
            $end   = min($pages, $page + 2);
            for($i=$start; $i<=$end; $i++){
              $active = ($i === $page) ? ' active' : '';
              echo '<li class="page-item'.$active.'"><a class="page-link" href="'.page_link($i, $q).'">'.$i.'</a></li>';
            }

            $next_disabled = ($page >= $pages) ? ' disabled' : '';
            echo '<li class="page-item'.$next_disabled.'"><a class="page-link" href="'.($page<$pages ? page_link($page+1, $q) : '#').'" aria-label="Berikutnya">&raquo;</a></li>';
          ?>
        </ul>
      </nav>
    <?php endif; ?>

  </div><!-- /container -->

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
