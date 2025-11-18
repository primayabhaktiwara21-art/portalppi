<?php
include 'koneksi.php';

// Ambil semua kategori unik dari tabel posts
$sqlKategori = "SELECT DISTINCT kategori FROM posts ORDER BY kategori ASC";
$resultKategori = mysqli_query($conn, $sqlKategori);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Daftar Isi Blog</title>
  <style>
    body {
      font-family: "Segoe UI", Arial, sans-serif;
      background: linear-gradient(to bottom right, #f5f7fa, #c3cfe2);
      margin: 0;
      padding: 0;
      color: #333;
    }

    .container {
      max-width: 900px;
      margin: 50px auto;
      background: white;
      border-radius: 15px;
      padding: 30px 40px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.1);
      animation: fadeIn 0.6s ease-in-out;
    }

    h1 {
      font-size: 26px;
      color: #007acc;
      text-align: center;
      border-bottom: 2px solid #007acc;
      padding-bottom: 10px;
      margin-bottom: 25px;
    }

    .search-box {
      text-align: center;
      margin-bottom: 25px;
    }

    .search-box input {
      width: 80%;
      max-width: 500px;
      padding: 10px 15px;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 15px;
      outline: none;
      transition: 0.3s;
    }

    .search-box input:focus {
      border-color: #007acc;
      box-shadow: 0 0 6px rgba(0,122,204,0.3);
    }

    .btn-back {
      display: inline-block;
      margin-bottom: 20px;
      background-color: #007acc;
      color: white;
      padding: 10px 18px;
      border-radius: 8px;
      text-decoration: none;
      transition: background 0.3s ease, transform 0.2s ease;
    }

    .btn-back:hover {
      background-color: #005fa3;
      transform: translateY(-2px);
    }

    h2 {
      color: #005fa3;
      margin-top: 25px;
      border-left: 4px solid #007acc;
      padding-left: 10px;
      font-size: 20px;
    }

    ul {
      list-style-type: none;
      padding-left: 20px;
    }

    li {
      margin-bottom: 8px;
      font-size: 15px;
      transition: transform 0.2s ease;
    }

    li:hover {
      transform: translateX(5px);
    }

    a {
      text-decoration: none;
      color: #333;
      font-weight: 500;
      transition: color 0.3s ease;
    }

    a:hover {
      color: #007acc;
    }

    small {
      color: gray;
      font-size: 13px;
    }

    .no-post {
      color: #888;
      font-style: italic;
    }

    footer {
      text-align: center;
      font-size: 13px;
      color: #777;
      margin-top: 30px;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }

    @media (max-width: 600px) {
      .container { padding: 20px; }
      h1 { font-size: 22px; }
      h2 { font-size: 18px; }
      .search-box input { width: 95%; }
    }
  </style>
</head>
<body>

  <div class="container">
    <a href="index.php" class="btn-back">← Kembali ke Beranda</a>
    <h1>📚 Daftar Isi Blog</h1>

    <div class="search-box">
      <input type="text" id="searchInput" placeholder="🔍 Cari judul atau kategori...">
    </div>

    <div id="contentList">
      <?php 
      while ($rowKategori = mysqli_fetch_assoc($resultKategori)) {
          $kategori = $rowKategori['kategori'];
          echo "<h2 class='kategori'>" . htmlspecialchars($kategori) . "</h2>";
          echo "<ul>";

          $sqlPosts = "
              SELECT judul, slug, created_at 
              FROM posts 
              WHERE LOWER(kategori) = LOWER('$kategori') 
              ORDER BY created_at DESC
          ";
          $resultPosts = mysqli_query($conn, $sqlPosts);

          if (mysqli_num_rows($resultPosts) > 0) {
              while ($post = mysqli_fetch_assoc($resultPosts)) {
                  $judul = htmlspecialchars($post['judul']);
                  $slug = urlencode($post['slug']);
                  $tanggal = date('d M Y', strtotime($post['created_at']));
                  echo "<li class='post-item'><a href='/post/$slug'>$judul</a> <small>($tanggal)</small></li>";
              }
          } else {
              echo "<li class='no-post'>Tidak ada posting di kategori ini.</li>";
          }

          echo "</ul>";
      }
      ?>
    </div>

    <footer>
      © <?= date('Y'); ?> PortalPPI.my.id — Semua Hak Dilindungi
    </footer>
  </div>

  <script>
    // Fitur pencarian langsung
    document.getElementById('searchInput').addEventListener('keyup', function() {
      const filter = this.value.toLowerCase();
      const items = document.querySelectorAll('.post-item, .kategori');
      items.forEach(item => {
        const text = item.textContent.toLowerCase();
        if (text.includes(filter)) {
          item.style.display = '';
        } else {
          item.style.display = 'none';
        }
      });
    });
  </script>

</body>
</html>
