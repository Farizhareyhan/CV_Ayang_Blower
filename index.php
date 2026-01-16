<?php session_start(); ?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>CV Ayang Blower — Dekorasi</title>

  <!-- Bootstrap & Icons -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

  <style>
    :root{ --primary:#ff8680; --dark:#2b2d42; }
    body{ background:#fff; color:#333; }
    .thumb { transition: transform .15s ease; }
    .thumb:hover { transform: translateY(-2px); }


    /* HERO ala index toko roti */
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
    .thumb{ width:100%; height:260px; object-fit:cover; border-radius:.75rem; box-shadow:0 10px 24px rgba(0,0,0,.08); }
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
        <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
      </ul>
    </div>
  </nav>

  <!-- HERO -->
  <header class="hero">
    <div class="container py-5">
      <h1 class="display-5 mb-2">Dekorasi Premium untuk Momen Spesial</h1>
      <p class="lead mb-0">Balon, backdrop, set pesta — sesuai tema dan warna keinginan Anda.</p>
    </div>
  </header>

  <!-- CONTENT INTRO -->
  <div class="container mt-5">
    <div class="row">
      <div class="col-md-7">
        <h3 class="mb-3">Tentang Dekorasi Kami</h3>
        <p>Kami menghadirkan dekorasi elegan dan rapi untuk ulang tahun, lamaran, pernikahan, grand opening, hingga event korporat.</p>
        <ul>
          <li>Paket dekorasi tematik & kustom</li>
          <li>Instalasi cepat, hasil rapi</li>
          <li>Harga transparan, konsultasi gratis</li>
        </ul>
        <a href="produk.php" class="btn btn-primary">Lihat Produk</a>
        <a href="login.php" class="btn btn-outline-secondary ml-2">Login</a>
      </div>
      <div class="col-md-5">
        <div class="card card-soft p-4">
          <h5 class="mb-3">Konsultasi Dekorasi</h5>
          <p class="mb-2">Butuh saran tema atau paket? Hubungi kami:</p>
          <p class="mb-0"><i class="far fa-envelope"></i> <a href="mailto:info@cv-ayangblower.local">info@cv-ayangblower.local</a></p>
        </div>
      </div>
    </div>
  </div>

  <!-- GALERI DEKORASI -->
    <!-- GALERI DEKORASI + LIGHTBOX -->
  <div class="container mt-5">
    <h3 class="mb-4 text-center">Galeri Dekorasi</h3>
    <div class="row" id="gallery">
      <?php
        // daftar gambar (src, alt)
        $items = [
          ["upload/Dekor1.jpg","Dekorasi 1"],
          ["upload/Dekor mahal.jpg","Dekorasi 2"],
          ["upload/Dekor 5.jpg","Dekorasi 3"],
          ["upload/dekor tengah.jpg","Dekorasi 4"],
          ["upload/dekor sedeng.jpg","Dekorasi 5"],
        ];
        $i = 0;
        foreach ($items as $it):
      ?>
      <div class="col-sm-6 col-md-4 mb-4">
        <img
          src="<?= $it[0]; ?>"
          alt="<?= htmlspecialchars($it[1]); ?>"
          class="thumb"
          data-index="<?= $i++; ?>"
          style="cursor:zoom-in"
        >
      </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- LIGHTBOX MODAL -->
  <div class="modal fade" id="lightboxModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
      <div class="modal-content" style="background:#000; border:0;">
        <div class="modal-body p-0 position-relative">
          <button type="button" class="close text-white position-absolute" data-dismiss="modal" aria-label="Close" style="right:.75rem; top:.5rem; opacity:.9;">
            <span aria-hidden="true">&times;</span>
          </button>

          <img id="lightboxImage" src="" alt="" style="width:100%; height:auto; display:block;">

          <!-- Nav arrows -->
          <a id="lbPrev" class="position-absolute" style="left:.5rem; top:50%; transform:translateY(-50%); color:#fff; font-size:2rem; text-decoration:none; padding:.25rem .5rem;">&#10094;</a>
          <a id="lbNext" class="position-absolute" style="right:.5rem; top:50%; transform:translateY(-50%); color:#fff; font-size:2rem; text-decoration:none; padding:.25rem .5rem;">&#10095;</a>
        </div>
      </div>
    </div>
  </div>

  <script>
    (function(){
      var thumbs = Array.prototype.slice.call(document.querySelectorAll('#gallery .thumb'));
      var modal  = $('#lightboxModal');
      var imgEl  = document.getElementById('lightboxImage');
      var curIdx = 0;

      function showAt(i){
        if(i < 0) i = thumbs.length - 1;
        if(i >= thumbs.length) i = 0;
        curIdx = i;
        imgEl.src = thumbs[curIdx].getAttribute('src');
        imgEl.alt = thumbs[curIdx].getAttribute('alt') || '';
      }

      // click thumb -> open modal
      thumbs.forEach(function(t){
        t.addEventListener('click', function(e){
          var idx = parseInt(this.getAttribute('data-index'), 10) || 0;
          showAt(idx);
          modal.modal('show');
        });
      });

      document.getElementById('lbPrev').addEventListener('click', function(e){
        e.preventDefault(); showAt(curIdx - 1);
      });
      document.getElementById('lbNext').addEventListener('click', function(e){
        e.preventDefault(); showAt(curIdx + 1);
      });

      // keyboard navigation
      document.addEventListener('keydown', function(e){
        if(!$('#lightboxModal').hasClass('show')) return;
        if(e.key === 'ArrowLeft')  showAt(curIdx - 1);
        if(e.key === 'ArrowRight') showAt(curIdx + 1);
        if(e.key === 'Escape')     modal.modal('hide');
      });
    })();
  </script>


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
