<?php 
include 'koneksi.php';

// Ambil data dari database

// --- Pagination ---
$limit = 6; // jumlah artikel per halaman
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Total data
$total_posts = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM posts"))['total'];
$total_pages = ceil($total_posts / $limit);

// Featured (1 artikel terbaru)
$featured = mysqli_query($conn, "SELECT * FROM posts ORDER BY created_at DESC LIMIT 1");

// Artikel lainnya (dengan pagination)
$recent = mysqli_query($conn, "SELECT * FROM posts ORDER BY created_at DESC LIMIT $offset, $limit");

$populer = mysqli_query($conn, "SELECT * FROM posts ORDER BY RAND() LIMIT 5");
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Smart_Tech | Blog Teknologi & Inspirasi Digital</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    :root {
      --primary: #1e63c3;
      --secondary: #154b92;
      --light: #f8faff;
      --text-dark: #333;
    }

    body {
      background: linear-gradient(180deg, #f9f9f9 0%, #f2f5ff 100%);
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      color: var(--text-dark);
      scroll-behavior: smooth;
    }

    /* HEADER */
    .navbar {
      background: linear-gradient(90deg, var(--primary), var(--secondary));
      box-shadow: 0 3px 10px rgba(0,0,0,0.1);
      transition: all 0.3s;
    }
    .navbar-brand {
      font-weight: 800;
      color: #fff !important;
      font-size: 1.6rem;
      letter-spacing: 0.6px;
    }
    .navbar-nav .nav-link {
      color: #e3e9ff !important;
      font-weight: 500;
      margin-right: 15px;
      transition: 0.3s;
    }
    .navbar-nav .nav-link:hover {
      color: #ffffff !important;
      transform: scale(1.05);
    }
    
    /* Tombol Login & Langganan */
.btn-login {
  border: 1.8px solid #fff;
  color: #fff !important;
  border-radius: 25px;
  padding: 6px 18px;
  font-weight: 600;
  transition: all 0.3s ease;
  background: transparent;
}

.btn-login:hover {
  background: #fff;
  color: var(--primary) !important;
  transform: scale(1.05);
}

.btn-subscribe {
  background: #fff;
  color: var(--primary) !important;
  font-weight: 600;
  border: none;
  border-radius: 25px;
  padding: 7px 20px;
  transition: all 0.3s ease;
}

.btn-subscribe:hover {
  background: #eaf2ff;
  transform: scale(1.05);
}

/* Hilangkan garis bawah di tombol Langganan */
.btn-subscribe, 
.btn-subscribe:hover, 
.btn-login, 
.btn-login:hover {
  text-decoration: none !important;
}

/* Pastikan tombol tetap sejajar di navbar HP */
@media (max-width: 991px) {
  .navbar-nav {
    display: flex;
    flex-direction: column;
    align-items: center;
  }

  /* Supaya tombol sejajar di bawah menu */
  .btn-login, .btn-subscribe {
    display: inline-block;
    margin: 5px 6px;
  }

  /* Tambahkan sedikit jarak antar tombol */
  .nav-item.ms-3, .nav-item.ms-2 {
    margin-left: 0 !important;
  }
}

/* Untuk layar kecil banget (HP kecil) tombol disusun lebih rapi */
@media (max-width: 576px) {
  .btn-login, .btn-subscribe {
    width: auto;
    padding: 7px 16px;
    font-size: 0.9rem;
  }
}

    
    .navbar-toggler {
      border: none;
      outline: none;
    }
    
    .navbar-toggler-icon {
      background-image: url("data:image/svg+xml;charset=utf8,%3Csvg viewBox='0 0 30 30' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath stroke='rgba(255,255,255,1)' stroke-width='3' stroke-linecap='round' stroke-miterlimit='10' d='M4 7h22M4 15h22M4 23h22'/%3E%3C/svg%3E");
    }

    

    /* FEATURED POST */
    .featured {
      position: relative;
      border-radius: 14px;
      overflow: hidden;
      margin-bottom: 45px;
      box-shadow: 0 6px 25px rgba(0,0,0,0.08);
      transition: all 0.3s;
    }
    .featured img {
      width: 100%;
      height: 420px;
      object-fit: cover;
      filter: brightness(0.85);
      transition: transform 0.5s ease, filter 0.5s ease;
    }
    .featured:hover img {
      transform: scale(1.04);
      filter: brightness(0.9);
    }
    .featured-content {
      position: absolute;
      bottom: 0;
      left: 0;
      width: 100%;
      background: linear-gradient(180deg, rgba(0,0,0,0.1), rgba(0,0,0,0.9));
      color: #fff;
      padding: 30px;
    }
    .featured-label {
      background: var(--primary);
      color: white;
      font-size: 0.8rem;
      font-weight: 700;
      padding: 6px 16px;
      border-radius: 6px;
      display: inline-block;
      margin-bottom: 10px;
      letter-spacing: 0.5px;
      text-transform: uppercase;
      box-shadow: 0 2px 8px rgba(0,0,0,0.2);
    }
    .featured-content h2 {
      font-weight: 700;
      margin-bottom: 5px;
      font-size: 1.9rem;
      text-shadow: 1px 1px 6px rgba(0,0,0,0.4);
    }

    /* POST LIST */
    .post-item {
      background: #fff;
      border-radius: 10px;
      padding: 15px;
      margin-bottom: 25px;
      box-shadow: 0 3px 10px rgba(0,0,0,0.05);
      transition: all 0.3s;
      border-left: 4px solid transparent;
    }
    .post-item:hover {
      transform: translateY(-5px);
      border-left: 4px solid var(--primary);
      box-shadow: 0 6px 20px rgba(0,0,0,0.08);
    }
    .post-item img {
      width: 100%;
      height: 160px;
      object-fit: cover;
      border-radius: 8px;
    }
    .post-item h5 {
      color: var(--secondary);
      font-weight: 700;
      transition: color 0.2s;
    }
    .post-item h5:hover {
      color: var(--primary);
    }

    /* Tombol Selengkapnya */
    .btn-read {
      background: linear-gradient(90deg, var(--primary), var(--secondary));
      color: #fff !important;
      font-weight: 600;
      border: none;
      border-radius: 25px;
      padding: 6px 18px;
      transition: 0.3s;
      text-decoration: none;
      display: inline-block;
    }
    .btn-read:hover {
      opacity: 0.9;
      transform: scale(1.05);
    }

    /* SIDEBAR */
    .sidebar-card {
      background: #fff;
      border-radius: 10px;
      box-shadow: 0 3px 10px rgba(0,0,0,0.05);
      padding: 20px;
      margin-bottom: 25px;
      transition: all 0.3s ease;
    }
    .sidebar-card h5 {
      color: var(--primary);
      font-weight: 700;
      border-left: 5px solid var(--primary);
      padding-left: 10px;
      margin-bottom: 18px;
      letter-spacing: 0.5px;
    }
    .sidebar-card a {
      text-decoration: none;
      color: var(--text-dark);
      display: block;
      margin-bottom: 8px;
      transition: all 0.2s;
    }
    .sidebar-card a:hover {
      color: var(--primary);
      transform: translateX(4px);
    }

    /* POPULER ITEM */
    .populer-item {
      display: flex;
      align-items: center;
      gap: 10px;
      margin-bottom: 12px;
      transition: all 0.3s ease;
    }
    .populer-item img {
      width: 65px;
      height: 65px;
      object-fit: cover;
      border-radius: 6px;
    }
    .populer-item span {
      font-size: 0.9rem;
      font-weight: 600;
      color: var(--text-dark);
      line-height: 1.2;
    }
    .populer-item span:hover {
      color: var(--primary);
    }

    .pagination .page-item.active .page-link {
      background-color: var(--primary);
      border-color: var(--primary);
      color: #fff;
    }
    
    .pagination .page-link {
      color: var(--secondary);
      border-radius: 8px;
      margin: 0 3px;
    }
    
    .pagination .page-link:hover {
      background-color: var(--primary);
      color: #fff;
    }




    /* FOOTER */
    footer {
      background: linear-gradient(90deg, var(--secondary), var(--primary));
      color: #e9f1ff;
      padding: 45px 0 25px;
    }
    .footer-title {
      font-weight: 700;
      font-size: 1.1rem;
      color: #ffffff;
      margin-bottom: 15px;
      position: relative;
    }
    .footer-title::after {
      content: '';
      position: absolute;
      bottom: -5px;
      left: 0;
      width: 40px;
      height: 2px;
      background: #fff;
    }
    .footer-link {
      display: block;
      color: #cfdfff;
      text-decoration: none;
      margin-bottom: 6px;
      transition: color 0.2s;
    }
    .footer-link:hover {
      color: #fff;
    }
    .social-icon a {
      color: #cfdfff;
      margin-right: 12px;
      font-size: 1.3rem;
      transition: all 0.2s;
    }
    .social-icon a:hover {
      color: #fff;
      transform: scale(1.2);
    }
    .footer-bottom {
      text-align: center;
      border-top: 1px solid rgba(255,255,255,0.15);
      margin-top: 35px;
      padding-top: 15px;
      color: #cbdafc;
      font-size: 0.9rem;
    }

    .fade-in {
      animation: fadeIn 1s ease-in-out;
    }
    @keyframes fadeIn {
      from {opacity: 0; transform: translateY(20px);}
      to {opacity: 1; transform: translateY(0);}
    }
  </style>
</head>
<body>


<!-- HEADER -->
<!-- HEADER -->
<nav class="navbar navbar-expand-lg sticky-top">
  <div class="container">
    <a class="navbar-brand" href="index.php">Smart_Tech</a>

    <!-- Tombol hamburger untuk HP -->
    <button class="navbar-toggler text-white border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Menu navigasi -->
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto align-items-center">
        <li class="nav-item"><a href="index.php" class="nav-link">Beranda</a></li>
        <li class="nav-item"><a href="post/kategori.php?k=Teknologi" class="nav-link">Teknologi</a></li>
        <li class="nav-item"><a href="post/kategori.php?k=Gadget" class="nav-link">Gadget</a></li>
        <li class="nav-item"><a href="post/kategori.php?k=AI" class="nav-link">AI</a></li>
        <li class="nav-item"><a href="post/kategori.php?k=Coding" class="nav-link">Coding</a></li>
        <li class="nav-item"><a href="post/kategori.php?k=Internet" class="nav-link">Internet</a></li>

        <!-- Tombol kanan -->
        <li class="nav-item ms-3">
          <a href="#" class="btn-login">Masuk</a>
        </li>
      </ul>
    </div>
  </div>
</nav>



<!-- KONTEN -->
<div class="container mt-4 fade-in">
  <div class="row g-4">
    <!-- KIRI -->
    <div class="col-lg-8">
      <div class="featured">
        <?php if($f = mysqli_fetch_assoc($featured)): ?>
          <?php if(!empty($f['gambar'])): ?>
            <img src="uploads/<?php echo htmlspecialchars($f['gambar']); ?>" alt="">
          <?php endif; ?>
          <div class="featured-content">
            <div class="featured-label">FEATURED POST</div>
            <h2><?php echo htmlspecialchars($f['judul']); ?></h2>
            <p><?php echo substr(strip_tags($f['konten']), 0, 120); ?>...</p>
               <a href="post/<?php echo $f['slug']; ?>">Baca Selengkapnya</a>
          </div>
        <?php endif; ?>
      </div>

      <h5 class="fw-bold mb-3 text-uppercase" style="color:var(--secondary);">Postingan Terbaru</h5>
      <?php while($r = mysqli_fetch_assoc($recent)): ?>
      <div class="post-item">
        <div class="row g-3">
          <div class="col-md-4">
            <?php if(!empty($r['gambar'])): ?>
              <img src="uploads/<?php echo htmlspecialchars($r['gambar']); ?>" alt="">
            <?php endif; ?>
          </div>
          <div class="col-md-8">
            <h5>
              <a href="post/<?php echo $r['slug']; ?>" style="text-decoration:none; color:var(--secondary);">
                <?php echo htmlspecialchars($r['judul']); ?>
              </a>
            </h5>
            <p class="small text-muted"><?php echo date('d M Y', strtotime($r['created_at'])); ?> | <?php echo htmlspecialchars($r['kategori']); ?></p>
            <p><?php echo substr(strip_tags($r['konten']), 0, 150); ?>...</p>
            <a href="post/<?php echo $r['slug']; ?>" class="btn-read">Selengkapnya</a>
          </div>
        </div>
      </div>
      <?php endwhile; ?>
    </div>

    <!-- KANAN -->
    <div class="col-lg-4">
      <div class="sidebar-card">
        <h5>Mengenai Saya</h5>
        <img src="https://i.pravatar.cc/100" class="rounded-circle mb-2 shadow-sm" alt="Profil" width="80">
        <p><b>Mas Admin</b><br><small>Blogger & Tech Enthusiast</small></p>
        <p class="text-muted">Berbagi inspirasi dan pengetahuan tentang dunia digital dan teknologi modern.</p>
      </div>

      <div class="sidebar-card">
        <h5>Populer</h5>
        <?php while($p = mysqli_fetch_assoc($populer)): ?>
        <div class="populer-item">
          <?php if(!empty($p['gambar'])): ?>
            <img src="uploads/<?php echo htmlspecialchars($p['gambar']); ?>" alt="populer">
          <?php endif; ?>
          <a href="post/<?php echo $p['slug']; ?>"><span><?php echo htmlspecialchars(substr($p['judul'],0,45)); ?>...</span></a>
        </div>
        <?php endwhile; ?>
      </div>
    </div>
  </div>
</div>

<!-- PAGINATION -->
<div class="container text-center my-4">
  <nav aria-label="Page navigation">
    <ul class="pagination justify-content-center">
      <?php if ($page > 1): ?>
        <li class="page-item">
          <a class="page-link" href="?page=<?php echo $page - 1; ?>">&laquo; Prev</a>
        </li>
      <?php endif; ?>

      <?php for ($i = 1; $i <= $total_pages; $i++): ?>
        <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
          <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
        </li>
      <?php endfor; ?>

      <?php if ($page < $total_pages): ?>
        <li class="page-item">
          <a class="page-link" href="?page=<?php echo $page + 1; ?>">Next &raquo;</a>
        </li>
      <?php endif; ?>
    </ul>
  </nav>
</div>


<!-- FOOTER -->
<footer>
  <div class="container">
    <div class="row">
      <div class="col-md-4 mb-3">
        <h6 class="footer-title">Tentang Smart_Tech</h6>
        <p>Kami berbagi wawasan dan informasi seputar teknologi, inovasi digital, dan inspirasi masa depan. Temukan berita dan artikel terbaru setiap hari.</p>
      </div>
      <div class="col-md-4 mb-3">
        <h6 class="footer-title">Navigasi</h6>
        <a href="index.php" class="footer-link">Beranda</a>
        <a href="post/tentang" class="footer-link">Tentang</a>
        <a href="post/kebijakan-privasi" class="footer-link">Privasi</a>
        <a href="post/hubungi-kami" class="footer-link">Kontak</a>
      </div>
      <div class="col-md-4">
        <h6 class="footer-title">Ikuti Kami</h6>
        <div class="social-icon">
          <a href="https://www.facebook.com/"><i class="bi bi-facebook"></i></a>
          <a href="https://www.instagram.com/"><i class="bi bi-instagram"></i></a>
          <a href="https://x.com/"><i class="bi bi-twitter-x"></i></a>
          <a href="https://www.youtube.com/"><i class="bi bi-youtube"></i></a>
        </div>
      </div>
    </div>
    <div class="footer-bottom">
      © <?php echo date('Y'); ?> Smart_Tech — Dibuat dengan 💻 oleh Anda
    </div>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
