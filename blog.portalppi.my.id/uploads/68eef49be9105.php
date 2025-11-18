<?php
include 'koneksi.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$sql = "SELECT * FROM posts WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$post = mysqli_fetch_assoc($res);
if (!$post) {
  echo "Artikel tidak ditemukan.";
  exit;
}

$related = [];
if (!empty($post['kategori'])) {
  $kategori = mysqli_real_escape_string($conn, $post['kategori']);
  $rel_q = "SELECT * FROM posts WHERE kategori='$kategori' AND id != $id ORDER BY created_at DESC LIMIT 3";
  $related = mysqli_query($conn, $rel_q);
}
$populer = mysqli_query($conn, "SELECT * FROM posts ORDER BY RAND() LIMIT 3");
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo htmlspecialchars($post['judul']); ?> - Smart_Tech</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<style>
  :root {
    --primary: #1e63c3;
    --secondary: #154b92;
    --light: #f8faff;
    --shadow: 0 3px 10px rgba(0,0,0,0.05);
  }
  * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
  }

  /* ===== TIPOGRAFI HELLOSEHAT STYLE ===== */
body {
  font-family: "Inter", "Helvetica Neue", Arial, sans-serif;
  font-size: 16px;
  font-weight: 400;
  line-height: 1.85;
  letter-spacing: 0.1px;
  color: #262626;
  background-color: #f9f9f9;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  }

h1, h2, h3, h4, h5 {
  font-weight: 700;
  color: #111;
  letter-spacing: -0.3px;
  line-height: 1.35;
  margin-bottom: 1rem;
}

p {
  font-size: 1rem;
  color: #262626;
  margin-bottom: 1.2rem;
  line-height: 1.85;
}

a {
  color: #1e63c3;
  text-decoration: none;
  font-weight: 500;
}
a:hover {
  color: #154b92;
  text-decoration: underline;
}

small, .text-muted {
  color: #737373 !important;
  font-size: 0.875rem;
}

.article-content {
  font-size: 1.05rem;
  color: #222;
  line-height: 1.9;
  letter-spacing: 0.1px;
}

.meta {
  color: #737373;
  font-size: 0.9rem;
  margin-bottom: 1.5rem;
}

.badge {
  background: #1e63c3;
  color: #fff;
  font-size: 0.85rem;
  padding: 6px 14px;
  border-radius: 20px;
  font-weight: 600;
}

.related-article a {
  font-size: 0.92rem;
  font-weight: 600;
  color: #1e63c3;
}

.related-article a:hover {
  text-decoration: underline;
  color: #154b92;
}


  /* ===== HEADER ===== */
  header {
    position: sticky;
    top: 0;
    z-index: 1000;
    background: #fff;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
  }

  .header-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 80px;
    border-bottom: 1px solid #e6e6e6;
  }

  .logo a {
    text-decoration: none;
    font-size: 1.8rem;
    font-weight: 800;
    color: var(--primary);
    letter-spacing: -0.5px;
  }

  .search-bar {
    flex: 1;
    display: flex;
    justify-content: center;
    position: relative;
  }

  .search-bar input {
    width: 60%;
    padding: 9px 15px;
    border-radius: 50px;
    border: 1px solid #ccc;
    font-size: 0.95rem;
    transition: 0.3s;
  }

  .search-bar input:focus {
    border-color: var(--primary);
    box-shadow: 0 0 5px rgba(30,99,195,0.3);
  }

  .search-bar button {
    position: absolute;
    right: 21%;
    background: none;
    border: none;
    font-size: 1.2rem;
    color: #777;
    cursor: pointer;
  }

  .search-bar button:hover {
    color: var(--primary);
  }

  .menu-right a {
    margin-left: 20px;
    text-decoration: none;
    color: #333;
    font-weight: 600;
    transition: 0.3s;
  }

  .menu-right a:hover {
    color: var(--primary);
  }

  .btn-login {
    border: 1px solid var(--primary);
    padding: 6px 16px;
    border-radius: 25px;
  }

  .btn-subscribe {
    background: var(--primary);
    color: #fff !important;
    padding: 7px 18px;
    border-radius: 25px;
    font-weight: 600;
  }

  .btn-subscribe:hover {
    background: var(--secondary);
  }

  .nav-blue {
    background: var(--primary);
    padding: 8px 0;
  }

  .nav-container {
    width: 1150px;
    margin: auto;
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
  }

  .nav-blue a {
    color: #fff;
    text-decoration: none;
    margin: 0 12px;
    font-weight: 500;
    transition: 0.3s;
  }

  .nav-blue a:hover {
    text-decoration: underline;
  }

  /* ===== KONTEN ===== */
  .container {
    width: 1150px;
    margin: 25px auto 70px auto;
    display: flex;
    gap: 30px;
  }

  .main {
    width: 70%;
  }

  .article {
    background: #fff;
    padding: 40px;
    border-radius: 16px;
    box-shadow: var(--shadow);
  }

  .badge {
    background: #ff6b00;
    color: #fff;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 800;
  }

  .article h1 {
    margin-top: 10px;
    font-size: 1.9rem;
    line-height: 1.3;
  }

  .article img {
    width: 100%;
    border-radius: 10px;
    margin: 20px 0;
  }

  .article-content {
    max-width: 780px;
    margin: auto;
  }

  .article-content p {
    line-height: 1.8;
    text-align: justify;
    margin-bottom: 1rem;
  }

  .article-content p + p {
    margin-top: 1rem;
  }


  

  /* ===== SHARE ICONS ===== */
  .share-icons {
    margin-top: 25px;
  }

  .share-icons a {
    display: inline-block;
    margin: 0 5px;
    color: #fff;
    font-size: 1.2rem;
    border-radius: 50%;
    width: 36px;
    height: 36px;
    line-height: 36px;
    text-align: center;
  }

  .share-wa { background: #25d366; }
  .share-fb { background: #1877f2; }
  .share-x { background: #000; }
  .share-in { background: #0a66c2; }

  /* ===== SIDEBAR ===== */
  .sidebar {
    width: 30%;
    display: flex;
    flex-direction: column;
    gap: 25px;
  }

  .side-box {
    background: #fff;
    border-radius: 16px;
    padding: 25px;
    box-shadow: var(--shadow);
  }

  .side-box h3 {
    color: var(--primary);
    margin-bottom: 15px;
    font-weight: 700;
  }

  .related-article {
    display: flex;
    align-items: center;
    margin-bottom: 15px;
  }

  .related-article img {
    width: 60px;
    height: 60px;
    border-radius: 8px;
    margin-right: 10px;
    object-fit: cover;
  }

  .related-article a {
    color: #1e63c3;
    text-decoration: none;
    font-weight: 600;
    font-size: 0.9rem;
  }

  .related-article a:hover {
    text-decoration: underline;
  }

  .about-box p {
    font-size: 0.95rem;
    text-align: justify;
  }

  /* ===== FOOTER ===== */
  footer {
    background: linear-gradient(180deg, var(--primary), var(--secondary));
    color: #fff;
    margin-top: 60px;
  }

  .promo-bar {
    text-align: center;
    padding: 40px 20px;
    border-bottom: 1px solid rgba(255,255,255,0.2);
  }

  .promo-bar img {
    width: 140px;
    margin: 10px;
  }

  .footer-container {
    width: 1150px;
    margin: auto;
    display: flex;
    justify-content: space-between;
    padding: 50px 0;
  }

  .footer-col h4 {
    margin-bottom: 15px;
    font-weight: 700;
  }

  .footer-col a {
    color: #fff;
    text-decoration: none;
    display: block;
    margin-bottom: 8px;
    font-size: 0.95rem;
  }

  .footer-col a:hover {
    text-decoration: underline;
  }

  .footer-social a {
    display: inline-block;
    background: #fff;
    color: var(--primary);
    width: 35px;
    height: 35px;
    line-height: 35px;
    text-align: center;
    border-radius: 50%;
    margin-right: 10px;
  }

  .footer-bottom {
    text-align: center;
    padding: 20px;
    background: rgba(0,0,0,0.1);
    font-size: 0.9rem;
  }

  /* ===== CHAT BUBBLE ===== */
  .chat-bubble {
    position: fixed;
    bottom: 20px;
    right: 25px;
    background: var(--primary);
    color: #fff;
    padding: 12px 20px;
    border-radius: 30px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.3);
    font-weight: 600;
    cursor: pointer;
    z-index: 999;
  }

  .chat-bubble:hover {
    transform: scale(1.05);
    background: #1e7880ff;
    transition: all 0.3s ease;
  }

  .chat-bubble i {
    margin-right: 8px;
  }

  /* ===== RESPONSIVE ===== */
  @media (max-width: 900px) {
    .header-top {
      flex-direction: column;
      padding: 12px 20px;
    }
    .search-bar input {
      width: 90%;
    }
    .container, .footer-container {
      flex-direction: column;
      width: 95%;
    }
    .main, .sidebar {
      width: 100%;
    }
  }

  html, body, h1, h2, h3, p, a {
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
}


</style>
</head>
<body>

<header>
  <div class="header-top">
    <div class="logo"><a href="index.php">Smart_Tech</a></div>
    <div class="search-bar">
      <input type="text" placeholder="Cari artikel teknologi, gadget, atau AI...">
      <button><i class="bi bi-search"></i></button>
    </div>
    <div class="menu-right">
      <a href="/smart_tekno/index.php">Beranda</a>
      <a href="#">Kategori</a>
      <a href="#">Tentang</a>
      <a href="#" class="btn-login">Masuk</a>
      <a href="#" class="btn-subscribe">Langganan</a>
    </div>
  </div>
  <div class="nav-blue">
    <div class="nav-container">
      <a href="#">Teknologi</a>
      <a href="#">Gadget</a>
      <a href="#">AI</a>
      <a href="#">Startup</a>
      <a href="#">Coding</a>
      <a href="#">Internet</a>
    </div>
  </div>
</header>

<div class="container">
  <div class="main">
    <div class="article">
      <span class="badge"><?php echo htmlspecialchars($post['kategori']); ?></span>
      <h1><?php echo htmlspecialchars($post['judul']); ?></h1>
      <div class="meta">Dipublikasikan: <?php echo date('d M Y', strtotime($post['created_at'])); ?></div>
      <?php if(!empty($post['gambar'])): ?>
        <img src="uploads/<?php echo htmlspecialchars($post['gambar']); ?>" alt="">
      <?php endif; ?>
      <div class="article-content"><?php echo $post['isi']; ?></div>
      <div class="share-icons">
        <a href="#" class="share-fb"><i class="bi bi-facebook"></i></a>
        <a href="#" class="share-wa"><i class="bi bi-whatsapp"></i></a>
        <a href="#" class="share-x"><i class="bi bi-twitter-x"></i></a>
        <a href="#" class="share-in"><i class="bi bi-linkedin"></i></a>
      </div>
    </div>
  </div>

  <div class="sidebar">
    <div class="side-box">
      <h3>Artikel Terkait</h3>
      <?php while($r = mysqli_fetch_assoc($related)): ?>
        <div class="related-article">
          <img src="uploads/<?php echo htmlspecialchars($r['gambar']); ?>">
          <div><a href="post.php?id=<?php echo $r['id']; ?>"><?php echo htmlspecialchars($r['judul']); ?></a></div>
        </div>
      <?php endwhile; ?>
    </div>

    <div class="side-box">
      <h3>Artikel Populer</h3>
      <?php while($p = mysqli_fetch_assoc($populer)): ?>
        <div class="related-article">
          <img src="uploads/<?php echo htmlspecialchars($p['gambar']); ?>">
          <div><a href="post.php?id=<?php echo $p['id']; ?>"><?php echo htmlspecialchars($p['judul']); ?></a></div>
        </div>
      <?php endwhile; ?>
    </div>

    <div class="side-box">
      <h3>Kategori</h3>
      <a href="#">Teknologi</a><br>
      <a href="#">AI</a><br>
      <a href="#">Gadget</a><br>
      <a href="#">Coding</a><br>
      <a href="#">Internet</a>
    </div>

    <div class="side-box about-box">
      <h3>Tentang Smart_Tech</h3>
      <p>Smart_Tech adalah portal informasi seputar teknologi, inovasi, gadget, dan dunia digital yang menghadirkan wawasan menarik dan inspiratif setiap hari.</p>
    </div>
  </div>
</div>

<footer>
  <div class="promo-bar">
    <h3>Chat lebih cepat lewat aplikasi Smart_Tech!</h3>
    <p>Respon Cepat, Jawaban Akurat, dan Info Teknologi Terkini!</p>
    <img src="https://upload.wikimedia.org/wikipedia/commons/7/78/Google_Play_Store_badge_EN.svg">
    <img src="https://developer.apple.com/assets/elements/badges/download-on-the-app-store.svg">
  </div>

  <div class="footer-container">
    <div class="footer-col">
      <h4>Smart_Tech</h4>
      <a href="#">Tentang Kami</a>
      <a href="#">Hubungi</a>
      <a href="#">Tim Redaksi</a>
    </div>

    <div class="footer-col">
      <h4>Lainnya</h4>
      <a href="#">Syarat & Ketentuan</a>
      <a href="#">Kebijakan Privasi</a>
      <a href="#">Iklan</a>
    </div>

    <div class="footer-col">
      <h4>Ikuti Kami</h4>
      <div class="footer-social">
        <a href="#"><i class="bi bi-facebook"></i></a>
        <a href="#"><i class="bi bi-twitter-x"></i></a>
        <a href="#"><i class="bi bi-instagram"></i></a>
        <a href="#"><i class="bi bi-youtube"></i></a>
      </div>
    </div>
  </div>

  <div class="footer-bottom">
    © <?php echo date('Y'); ?> Smart_Tech — Inspirasi Teknologi Setiap Hari
  </div>
</footer>

<div class="chat-bubble"><i class="bi bi-chat-dots"></i> Chat Admin Sekarang</div>

</body>
</html>
