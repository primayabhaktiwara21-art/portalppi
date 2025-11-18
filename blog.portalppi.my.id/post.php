<?php
include 'koneksi.php';

// Ambil berdasarkan slug dari URL
$slug = isset($_GET['slug']) ? $_GET['slug'] : '';

if ($slug != '') {
    $sql = "SELECT * FROM posts WHERE slug = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $slug);
} else {
    // fallback kalau masih pakai ?id=
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    $sql = "SELECT * FROM posts WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
}

mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$post = mysqli_fetch_assoc($res);

if (!$post) {
  echo "Artikel tidak ditemukan.";
  exit;
}

// Artikel terkait
$related = mysqli_query($conn, "SELECT * FROM posts WHERE kategori = '".mysqli_real_escape_string($conn, $post['kategori'])."' AND id != ".$post['id']." ORDER BY created_at DESC LIMIT 3");

// Artikel populer
$populer = mysqli_query($conn, "SELECT * FROM posts ORDER BY RAND() LIMIT 5");

// Artikel sebelumnya & berikutnya
$prev = mysqli_query($conn, "SELECT slug, judul FROM posts WHERE id < ".$post['id']." ORDER BY id DESC LIMIT 1");
$next = mysqli_query($conn, "SELECT slug, judul FROM posts WHERE id > ".$post['id']." ORDER BY id ASC LIMIT 1");

// Estimasi waktu baca
$wordCount = str_word_count(strip_tags($post['konten']));
$readingTime = ceil($wordCount / 200);
// Artikel baca juga (2 artikel dari kategori yang sama, acak)
$baca_juga = mysqli_query(
  $conn,
  "SELECT * FROM posts 
   WHERE kategori = '".mysqli_real_escape_string($conn, $post['kategori'])."' 
   AND id != ".$post['id']." 
   ORDER BY RAND() 
   LIMIT 2"
);

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

html, body {
  max-width: 100%;
  overflow-x: hidden;
}

img, iframe {
  max-width: 100%;
  height: auto;
}

/* Biar chat bubble gak nutup footer */
footer {
  padding-bottom: 80px;
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

html, body {
  max-width: 100%;
  overflow-x: hidden;
}

img, iframe {
  max-width: 100%;
  height: auto;
}



  /* ===== HEADER ===== */
    header {
      position: relative;
      background: #fff;
      box-shadow: 0 2px 8px rgba(0,0,0,0.08);
      z-index: 100;
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
  
  /* Style bar pencarian agar sejajar */
.search-bar {
  display: flex;
  align-items: center;
  background: #fff;
  border-radius: 30px;
  padding: 5px 15px;
  box-shadow: 0 2px 6px rgba(0,0,0,0.1);
}

.search-bar input {
  border: none;
  outline: none;
  flex: 1;
  font-size: 0.95rem;
  padding: 8px 10px;
}

.search-bar button {
  background: none;
  border: none;
  color: var(--primary);
  font-size: 1.1rem;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  height: 100%;
}

.search-bar i {
  vertical-align: middle;
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
  box-shadow: 0 0 0 2px rgba(30, 99, 195, 0.3);
  border-radius: 25px;
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

.nav-container,
.container,
.footer-container {
  width: 100%;
  max-width: 1150px;
  margin: 0 auto;
}

/* versi masing-masing dengan properti lain tetap aman */
.nav-container {
  display: flex;
  justify-content: center;
  flex-wrap: wrap;
}

.container {
  display: flex;
  gap: 30px;
  margin: 25px auto 70px auto;
}

.footer-container {
  display: flex;
  justify-content: space-between;
  flex-wrap: wrap;
  padding: 50px 0;
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
    font-weight: 430;
  }

  .article-content p + p {
    margin-top: 1rem;
    font-weight: 430;

  }

/* ===== BLOCKQUOTE STYLE DARI CKEDITOR ===== */
.article-content blockquote {
  border-left: 4px solid #1e63c3;
  background: #f1f5f9;
  color: #333;
  font-style: italic;
  padding: 15px 20px;
  margin: 20px 0;
  border-radius: 6px;
}

.article-content blockquote p {
  margin: 0;
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
/* ===== FOOTER DENGAN GRADIENT BIRU & ANIMASI GARIS PUTIH ===== */
footer {
  background: linear-gradient(180deg, var(--primary), var(--secondary));
  color: #fff;
  margin-top: 60px;
  position: relative;
  overflow: hidden;
  box-shadow: 0 -2px 15px rgba(0,0,0,0.15);
}

/* Promo bar di atas footer */
.promo-bar {
  text-align: center;
  padding: 45px 20px;
  border-bottom: 1px solid rgba(255,255,255,0.2);
  background: rgba(255,255,255,0.05);
}

.promo-bar img {
  width: 140px;
  margin: 10px;
  transition: transform 0.3s ease;
}
.promo-bar img:hover {
  transform: scale(1.05);
}

/* Kontainer utama footer */


/* Judul kolom */
.footer-col h4 {
  position: relative;
  font-weight: 700;
  margin-bottom: 25px;
  color: #fff;
  font-size: 1.1rem;
  letter-spacing: 0.5px;
  text-transform: capitalize;
}

/* Animasi garis putih di bawah judul */
.footer-col h4::after {
  content: "";
  position: absolute;
  left: 0;
  bottom: -6px;
  width: 40px;
  height: 3px;
  background: #fff;
  animation: underlineMove 2.5s infinite ease-in-out;
  border-radius: 5px;
}

@keyframes underlineMove {
  0% { width: 0; opacity: 0.4; }
  50% { width: 40px; opacity: 1; }
  100% { width: 0; opacity: 0.4; }
}

/* Tautan di footer */
.footer-col a {
  color: rgba(255,255,255,0.85);
  text-decoration: none;
  display: block;
  margin-bottom: 10px;
  font-size: 0.95rem;
  transition: all 0.3s ease;
}

.footer-col a:hover {
  color: #fff;
  transform: translateX(6px);
}

/* Ikon sosial */
.footer-social a {
  display: inline-block;
  background: rgba(255,255,255,0.2);
  color: #fff;
  width: 40px;
  height: 40px;
  line-height: 40px;
  text-align: center;
  border-radius: 50%;
  margin-right: 10px;
  transition: all 0.3s ease;
}

.footer-social a:hover {
  background: #fff;
  color: var(--primary);
  transform: scale(1.15);
}

/* Garis animasi di atas footer (neon line) */
footer::before {
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 3px;
  background: linear-gradient(90deg, #00d4ff, #ffffff, #ffd500, #ff6b00);
  background-size: 300% 100%;
  animation: lineMove 6s linear infinite;
}

@keyframes lineMove {
  0% { background-position: 0% 0; }
  100% { background-position: 100% 0; }
}

/* Footer bawah */
.footer-bottom {
  text-align: center;
  padding: 20px;
  background: rgba(0,0,0,0.1);
  font-size: 0.9rem;
  color: rgba(255,255,255,0.9);
  border-top: 1px solid rgba(255,255,255,0.2);
  letter-spacing: 0.3px;
}


  /* ===== CHAT BUBBLE ===== */
  /* ===== CHAT BUBBLE WHATSAPP ===== */
.chat-bubble {
  display: inline-flex;              /* ikon dan teks sejajar */
  align-items: center;               /* posisi vertikal sejajar */
  justify-content: center;
  gap: 10px;                         /* jarak antara ikon dan teks */
  background: var(--primary, #1e63c3);  /* biru default */
  color: #fff !important;            /* teks putih */
  text-decoration: none;             /* hilangkan garis bawah */
  padding: 12px 20px;
  border-radius: 30px;
  box-shadow: 0 3px 10px rgba(0,0,0,0.3);
  font-weight: 600;
  cursor: pointer;
  z-index: 999;
  position: fixed;
  bottom: 20px;
  right: 25px;
  transition: all 0.3s ease;
  animation: pulse 2.5s infinite;    /* efek berdenyut */
}

/* Saat diarahkan kursor */
.chat-bubble:hover {
  background: #1e7880ff;             /* warna hijau toska saat hover */
  transform: scale(1.05);
}

/* Ikon */
.chat-bubble i {
  font-size: 1.2rem;
}

/* Efek berdenyut lembut */
@keyframes pulse {
  0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(30, 99, 195, 0.5); }
  70% { transform: scale(1.05); box-shadow: 0 0 0 10px rgba(30, 99, 195, 0); }
  100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(30, 99, 195, 0); }
}

/* Responsif di layar kecil */
@media (max-width: 600px) {
  .chat-bubble {
    bottom: 15px;
    right: 15px;
    padding: 10px 16px;
    font-size: 0.9rem;
    gap: 8px;
  }
}

/* === Format List (daftar) natural & rapi === */
.article-content ol, 
.article-content ul {
  margin-left: 1.6em;
  margin-bottom: 1em;
  line-height: 1.8;
}

/* Bullet biasa tetap pakai titik (•) */
.article-content ul {
  list-style-type: disc;
}

/* Jika ada bullet di dalam numbered list, buat lebih menjorok */
.article-content ol ul {
  margin-left: 2.2em;
  list-style-type: circle;
}

/* Jarak antar poin */
.article-content li {
  margin-bottom: 0.4em;
  text-align: justify;
}

/* Nomor list pakai default agar urut otomatis */
.article-content ol {
  list-style: decimal;
}

/* Warna teks tebal */
.article-content strong {
  color: #111;
}

/* Heading lebih seragam */
.article-content h2, 
.article-content h3 {
  color: #111;
  font-weight: 800;
  margin-top: 1.5em;
}






.nav-article {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 40px;
  padding-top: 25px;
  border-top: 1px solid #e0e0e0;
  gap: 15px;
}

.nav-btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: #c9dcfb;               /* biru sedang - lembut tapi jelas */
  color: #0f3fa0;                    /* teks biru tua */
  font-weight: 600;
  padding: 10px 20px;
  border-radius: 30px;
  text-decoration: none;
  transition: all 0.3s ease;
  box-shadow: 0 3px 8px rgba(0, 0, 0, 0.12);
  border: 1px solid #b5cdfa;
}

.nav-btn:hover {
  background: #b3ccfa;               /* sedikit lebih pekat saat hover */
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.nav-btn i {
  font-size: 1rem;
}

.nav-btn.prev {
  justify-content: flex-start;
}

.nav-btn.next {
  justify-content: flex-end;
}



@media (max-width: 700px) {
  .nav-article {
    flex-direction: column;
    gap: 12px;
  }
  .nav-btn {
    width: 100%;
    justify-content: center;
  }
}




/* ===== RESPONSIVE ===== */
@media (max-width: 900px) {
  /* Header */
  .header-top {
    flex-direction: column;
    padding: 12px 20px;
    text-align: center;
  }
  .search-bar {
    width: 100%;
    justify-content: center;
  }
  .search-bar input {
    width: 90%;
    font-size: 0.9rem;
  }
  .menu-right {
    margin-top: 10px;
  }
  .menu-right a {
    display: inline-block;
    margin: 6px 8px;
    font-size: 0.9rem;
  }

  /* Navigation bar */
  .nav-container {
    flex-wrap: wrap;
    width: 100%;
    justify-content: center;
  }
  .nav-blue a {
    margin: 5px 10px;
    font-size: 0.9rem;
  }

  /* Konten utama */
  .container {
    flex-direction: column;
    width: 95%;
    margin: 20px auto 60px auto;
    gap: 15px;
  }

  .main, .sidebar {
    width: 100%;
  }

  .article {
    padding: 20px;
    font-size: 0.95rem;
  }

  .article h1 {
    font-size: 1.4rem;
  }

  .article-content {
    max-width: 100%;
  }
  

  .article img {
    border-radius: 10px;
  }

  /* Sidebar */
  .side-box {
    padding: 20px;
  }

  .related-article img {
    width: 55px;
    height: 55px;
  }

  /* Footer */
  .footer-container {
    width: 90%;
    flex-direction: column;
    text-align: center;
    align-items: center;
    padding: 30px 0;
  }

  .footer-col {
    margin-bottom: 25px;
  }

  .footer-col h4 {
    font-size: 1rem;
  }

  .footer-col a {
    font-size: 0.9rem;
  }

  .footer-social a {
    margin: 0 6px;
  }

  /* Promo bar */
  .promo-bar {
    padding: 30px 10px;
  }
  .promo-bar h3 {
    font-size: 1.1rem;
  }

  /* Chat bubble */
  .chat-bubble {
    right: 15px;
    bottom: 15px;
    padding: 10px 16px;
    font-size: 0.85rem;
  }
}


  html, body, h1, h2, h3, p, a {
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
}

.category-button {
  display: inline-block;
  border: 1.5px solid var(--primary);
  color: var(--primary);
  border-radius: 25px;
  padding: 6px 18px;
  margin: 5px 6px 8px 0;
  font-weight: 600;
  font-size: 0.9rem;
  text-decoration: none;
  transition: all 0.25s ease;
}

.category-button:hover {
  background: var(--primary);
  color: #fff;
  transform: scale(1.05);
}

<style>
/* === Format List (daftar) dalam Artikel === */
article ol, article ul {
  margin-left: 1.6em;
  margin-bottom: 1em;
  line-height: 1.7;
}

/* Bullet di dalam numbered list lebih menjorok */
article ol ul {
  margin-left: 2em;
  list-style-type: disc;
}

/* Jarak antar poin */
article li {
  margin-bottom: 0.4em;
}

/* Warna teks tebal biar mirip editor */
article strong {
  color: #1e63c3;
}

/* Heading tampil lebih konsisten */
article h2, article h3 {
  color: #1e63c3;
  font-weight: 700;
  margin-top: 1.5em;
}
</style>


</style>
</head>
<body>

<header>
  <div class="header-top">
    <div class="logo"><a href="/index.php">Smart_Tech</a></div>
    <div class="search-bar">
      <input type="text" placeholder="Cari artikel teknologi, gadget, atau AI...">
      <button><i class="bi bi-search"></i></button>
    </div>
    <div class="menu-right">
      <a href="/index.php">Beranda</a>
      <a href="/daftar-isi.php">Kategori</a>
      <a href="tentang">Tentang</a>
      <a href="#" class="btn-login">Masuk</a>
      <a href="#" class="btn-subscribe">Langganan</a>
    </div>
  </div>
  <div class="nav-blue">
    <div class="nav-container">
      <a href="kategori.php?k=Teknologi">Teknologi</a>
      <a href="kategori.php?k=Gadget">Gadget</a>
      <a href="kategori.php?k=AI">AI</a>
      <a href="https://finance.detik.com/solusiukm/d-6351790/startup-artinya-apa-ini-ciri-contoh-dan-bedanya-dengan-perusahaan">Startup</a>
      <a href="kategori.php?k=Coding">Coding</a>
      <a href="kategori.php?k=Internet">Internet</a>
      <a href="kategori.php?k=Lainnya">Lainnya</a>
    </div>
  </div>
</header>

<div class="container">
  <div class="main">
    <div class="article">
      <span class="badge"><?php echo htmlspecialchars($post['kategori']); ?></span>
      <h1><?php echo htmlspecialchars($post['judul']); ?></h1>
      <div class="meta">Dipublikasikan: <?php echo date('d M Y', strtotime($post['created_at'])); ?></div>
      <div class="reading-time">⏱️ Estimasi waktu baca: <?= $readingTime; ?> menit</div>

      
    <?php if(!empty($post['gambar'])): ?>
      <img src="/uploads/<?php echo htmlspecialchars($post['gambar']); ?>" alt="">
    <?php endif; ?>

      <div class="article-content">
<?php
// bagi konten jadi paragraf
$paragraphs = explode('</p>', $post['konten']);
$total_paragraphs = count($paragraphs);
$baca_juga_inserted = false;

foreach ($paragraphs as $i => $para) {
  echo $para . '</p>';
  
  // sisipkan setelah paragraf ke-3
  if ($i == 2 && !$baca_juga_inserted) {
    echo '<div style="background:#f2f7ff;border-left:4px solid #1e63c3;padding:15px 20px;border-radius:10px;margin:25px 0;">';
    echo '<strong style="color:#1e63c3;">Baca juga:</strong><br>';
    while($bj = mysqli_fetch_assoc($baca_juga)) {
      echo '• <a href="/post/'.urlencode($bj['slug']).'" style="color:#1e63c3;font-weight:600;text-decoration:none;">'
           .htmlspecialchars($bj['judul']).'</a><br>';
    }
    echo '</div>';
    $baca_juga_inserted = true;
  }
}
?>
</div>


      <div class="share-icons">
        <a href="https://www.facebook.com/" class="share-fb"><i class="bi bi-facebook"></i></a>
        <a href="https://web.whatsapp.com/" class="share-wa"><i class="bi bi-whatsapp"></i></a>
        <a href="https://x.com/" class="share-x"><i class="bi bi-twitter-x"></i></a>
        <a href="https://www.linkedin.com/" class="share-in"><i class="bi bi-linkedin"></i></a>
      </div>
      
      
      
<div class="nav-article">
  <?php if($p = mysqli_fetch_assoc($prev)): ?>
    <a class="nav-btn prev" href="/post/<?php echo urlencode($p['slug']); ?>">
      <i class="bi bi-arrow-left"></i> <?php echo htmlspecialchars($p['judul']); ?>
    </a>
  <?php else: ?>
    <span></span>
  <?php endif; ?>

  <?php if($n = mysqli_fetch_assoc($next)): ?>
    <a class="nav-btn next" href="/post/<?php echo urlencode($n['slug']); ?>">
      <?php echo htmlspecialchars($n['judul']); ?> <i class="bi bi-arrow-right"></i>
    </a>
  <?php endif; ?>
</div>





    </div>
  </div>

  <div class="sidebar">
    <div class="side-box">
      <h3>Artikel Terkait</h3>
      <?php while($r = mysqli_fetch_assoc($related)): ?>
        <div class="related-article">
           <img src="/uploads/<?php echo htmlspecialchars($r['gambar']); ?>">
          <div><a href="/post/<?php echo urlencode($r['slug']); ?>"><?php echo htmlspecialchars($r['judul']); ?></a></div>
        </div>
      <?php endwhile; ?>
    </div>

    <div class="side-box">
      <h3>Artikel Populer</h3>
      <?php while($p = mysqli_fetch_assoc($populer)): ?>
        <div class="related-article">
          <img src="/uploads/<?php echo htmlspecialchars($p['gambar']); ?>">
          <div><a href="/post/<?php echo urlencode($p['slug']); ?>"><?php echo htmlspecialchars($p['judul']); ?></a></div>
        </div>
      <?php endwhile; ?>
    </div>

    <div class="side-box">
      <h3>Kategori</h3>
      <div class="category-list">
        <a href="kategori.php?k=Teknologi" class="category-button">Teknologi</a>
        <a href="kategori.php?k=AI" class="category-button">AI</a>
        <a href="kategori.php?k=Gadget" class="category-button">Gadget</a>
        <a href="kategori.php?k=Coding" class="category-button">Coding</a>
        <a href="kategori.php?k=Internet" class="category-button">Internet</a>
        <a href="kategori.php?k=Lainnya" class="category-button">Lainnya</a>
      </div>
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
      <a href="tentang">Tentang Kami</a>
      <a href="hubungi-kami">Hubungi</a>
      <a href="#">Tim Redaksi</a>
    </div>

    <div class="footer-col">
      <h4>Lainnya</h4>
      <a href="syarat-dan-ketentuan">Syarat & Ketentuan</a>
      <a href="kebijakan-privasi">Kebijakan Privasi</a>
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

<a href="https://wa.me/6282255127746" target="_blank" class="chat-bubble">
  <i class="bi bi-chat-dots"></i>
  Chat Admin Sekarang
</a>

</body>
</html>
