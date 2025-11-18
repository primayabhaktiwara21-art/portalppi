<?php
include '../koneksi.php';

// Ambil parameter kategori dari URL
$kategori = isset($_GET['k']) ? trim($_GET['k']) : '';

if ($kategori == '') {
  echo "Kategori tidak ditemukan.";
  exit;
}

// Ambil artikel berdasarkan kategori
$sql = "SELECT * FROM posts WHERE kategori = ? ORDER BY created_at DESC";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $kategori);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Kategori: <?php echo htmlspecialchars($kategori); ?> - Smart_Tech</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<style>
  :root {
    --primary: #1e63c3;
    --secondary: #154b92;
    --light: #f9f9f9;
    --shadow: 0 3px 10px rgba(0,0,0,0.05);
  }

  * {margin:0; padding:0; box-sizing:border-box;}
  body {
    font-family: "Inter", "Helvetica Neue", Arial, sans-serif;
    background: var(--light);
    color: #222;
    line-height: 1.7;
  }

  header {
    background: #fff;
    box-shadow: var(--shadow);
  }

  .header-top {
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:12px 80px;
    border-bottom:1px solid #eee;
  }

  .logo a {
    text-decoration:none;
    color:var(--primary);
    font-size:1.8rem;
    font-weight:800;
  }

  .nav-blue {
    background:var(--primary);
    padding:8px 0;
    text-align:center;
  }

  .nav-blue a {
    color:#fff;
    text-decoration:none;
    margin:0 15px;
    font-weight:500;
  }
  .nav-blue a:hover {text-decoration:underline;}

  .container {
    max-width:1150px;
    margin:40px auto;
    padding:0 15px;
  }

  h1 {
    font-size:1.9rem;
    font-weight:800;
    color:var(--primary);
    margin-bottom:30px;
    text-align:center;
  }

  .article-list {
    display:grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap:25px;
  }

  .card {
    background:#fff;
    border-radius:14px;
    box-shadow:var(--shadow);
    overflow:hidden;
    transition:transform 0.2s ease;
  }
  .card:hover {
    transform:translateY(-3px);
  }

  .card img {
    width:100%;
    height:190px;
    object-fit:cover;
  }

  .card-body {
    padding:20px;
  }

  .card-body h3 {
    font-size:1.1rem;
    margin-bottom:10px;
  }

  .card-body a {
    text-decoration:none;
    color:#111;
    font-weight:600;
  }
  .card-body a:hover {
    color:var(--primary);
  }

  .meta {
    font-size:0.85rem;
    color:#777;
    margin-bottom:8px;
  }

  .excerpt {
    color:#444;
    font-size:0.93rem;
    line-height:1.6;
  }

  footer {
    background: linear-gradient(180deg, var(--primary), var(--secondary));
    color:#fff;
    text-align:center;
    padding:25px;
    margin-top:60px;
  }

</style>
</head>
<body>

<header>
  <div class="header-top">
    <div class="logo"><a href="/index.php">Smart_Tech</a></div>
  </div>
      <div class="nav-blue">
      <a href="/index.php">Beranda</a>
      <a href="/kategori/Teknologi">Teknologi</a>
      <a href="/kategori/AI">AI</a>
      <a href="/kategori/Gadget">Gadget</a>
      <a href="/kategori/Coding">Coding</a>
      <a href="/kategori/Internet">Internet</a>
    </div>

</header>

<div class="container">
  <h1>Kategori: <?php echo htmlspecialchars($kategori); ?></h1>

  <div class="article-list">
    <?php if (mysqli_num_rows($result) > 0): ?>
      <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <div class="card">
          <?php if(!empty($row['gambar'])): ?>
            <img src="/uploads/<?php echo htmlspecialchars($row['gambar']); ?>" alt="<?php echo htmlspecialchars($row['judul']); ?>">
          <?php endif; ?>
          <div class="card-body">
            <div class="meta"><?php echo date('d M Y', strtotime($row['created_at'])); ?></div>
            <h3><a href="/post/<?php echo urlencode($row['slug']); ?>"><?php echo htmlspecialchars($row['judul']); ?></a></h3>
            <p class="excerpt">
              <?php
                $konten = strip_tags($row['konten']);
                echo substr($konten, 0, 120) . '...';
              ?>
            </p>
          </div>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p style="text-align:center; font-size:1rem;">Tidak ada artikel dalam kategori ini.</p>
    <?php endif; ?>
  </div>
</div>

<footer>
  © <?php echo date('Y'); ?> Smart_Tech — Inspirasi Teknologi Setiap Hari
</footer>

</body>
</html>
