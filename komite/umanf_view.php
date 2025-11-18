<?php
include_once '../koneksi.php';
include "../cek_akses.php";
$conn = $koneksi;

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$q = mysqli_query($conn, "SELECT * FROM tb_umanf WHERE id='$id'");
$data = mysqli_fetch_assoc($q);

if (!$data) {
  echo "<script>alert('Data tidak ditemukan');location.href='umanf.php';</script>";
  exit;
}

function pathToUrl($path) {
  return str_replace('../', '/', $path);
}

// === ZIP DOWNLOAD HANDLER ===
if (isset($_GET['download']) && $_GET['download'] == 'zip') {
  $zipname = "dokumen_rapat_" . $id . ".zip";
  $zip = new ZipArchive();
  $tmpZip = tempnam(sys_get_temp_dir(), "zip");
  if ($zip->open($tmpZip, ZipArchive::CREATE) === TRUE) {
    foreach (['file_undangan', 'file_materi', 'file_absensi', 'file_notulen'] as $f) {
      if (!empty($data[$f]) && file_exists($data[$f])) {
        $zip->addFile($data[$f], basename($data[$f]));
      }
    }
    if (!empty($data['file_foto'])) {
      $fotos = json_decode($data['file_foto'], true);
      foreach ($fotos as $foto) {
        if (file_exists($foto)) $zip->addFile($foto, "foto/" . basename($foto));
      }
    }
    $zip->close();
    header('Content-Type: application/zip');
    header('Content-Disposition: attachment; filename="' . $zipname . '"');
    readfile($tmpZip);
    unlink($tmpZip);
    exit;
  } else {
    echo "Gagal membuat ZIP.";
  }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Detail Dokumen Rapat | PPI PHBW</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
<style>
body {
  font-family: 'Poppins', sans-serif;
  background: #f8fafc;
  margin: 0;
  color: #1e293b;
}
header {
  background: linear-gradient(90deg, #1a2a80, #2563eb);
  color: #fff;
  padding: 18px 28px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.btn {
  padding: 8px 14px;
  border-radius: 8px;
  text-decoration: none;
  color: #fff;
  font-weight: 500;
  transition: 0.2s;
}
.btn-green { background: linear-gradient(135deg,#22c55e,#16a34a); }
.btn-blue { background: linear-gradient(135deg,#3b82f6,#1d4ed8); }
.btn-red { background: linear-gradient(135deg,#ef4444,#b91c1c); }
.btn-blue:hover { background: linear-gradient(135deg,#1d4ed8,#1e40af); }
.btn-green:hover { background: linear-gradient(135deg,#16a34a,#15803d); }

.container {
  max-width: 900px;
  margin: 40px auto;
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 4px 15px rgba(0,0,0,0.08);
  padding: 30px 40px;
}
h2 {
  text-align: center;
  color: #1a2a80;
  margin-top: 0;
  margin-bottom: 24px;
}
.section {
  margin-bottom: 30px;
}
.section h3 {
  color: #2563eb;
  border-bottom: 2px solid #e5e7eb;
  padding-bottom: 6px;
}
.file-viewer {
  margin-top: 10px;
}
.file-viewer embed, .file-viewer img {
  width: 100%;
  border-radius: 8px;
  box-shadow: 0 2px 6px rgba(0,0,0,0.1);
  margin-bottom: 16px;
}
.file-viewer img {
  max-height: 350px;
  object-fit: contain;
}
.actions {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 25px;
}
</style>
</head>
<body>
<header>
  <h3>📋 Detail Dokumen Rapat</h3>
  <a href="umanf.php" class="btn btn-green">⬅️ Kembali</a>
</header>

<div class="container">
  <div class="actions">
    <h2><?= htmlspecialchars($data['jenis_rapat']) ?></h2>
    <a href="?id=<?= $id ?>&download=zip" class="btn btn-blue">⬇️ Unduh Semua (ZIP)</a>
  </div>

  <!-- Undangan -->
  <div class="section">
    <h3>📄 Undangan</h3>
    <div class="file-viewer">
      <?php if ($data['file_undangan']) {
        $url = pathToUrl($data['file_undangan']);
        $ext = pathinfo($url, PATHINFO_EXTENSION);
        if ($ext == 'pdf')
          echo "<embed src='$url' type='application/pdf' height='500px'>";
        else
          echo "<img src='$url'>";
      } else echo "<i>Tidak ada file undangan</i>"; ?>
    </div>
  </div>

  <!-- Materi -->
  <div class="section">
    <h3>🧾 Materi</h3>
    <div class="file-viewer">
      <?php if ($data['file_materi']) {
        $url = pathToUrl($data['file_materi']);
        $ext = pathinfo($url, PATHINFO_EXTENSION);
        if ($ext == 'pdf')
          echo "<embed src='$url' type='application/pdf' height='500px'>";
        else
          echo "<img src='$url'>";
      } else echo "<i>Tidak ada file materi</i>"; ?>
    </div>
  </div>

  <!-- Absensi -->
  <div class="section">
    <h3>👥 Absensi</h3>
    <div class="file-viewer">
      <?php if ($data['file_absensi']) {
        $url = pathToUrl($data['file_absensi']);
        $ext = pathinfo($url, PATHINFO_EXTENSION);
        if ($ext == 'pdf')
          echo "<embed src='$url' type='application/pdf' height='500px'>";
        else
          echo "<img src='$url'>";
      } else echo "<i>Tidak ada file absensi</i>"; ?>
    </div>
  </div>

  <!-- Notulen -->
  <div class="section">
    <h3>🖋️ Notulen</h3>
    <div class="file-viewer">
      <?php if ($data['file_notulen']) {
        $url = pathToUrl($data['file_notulen']);
        $ext = pathinfo($url, PATHINFO_EXTENSION);
        if ($ext == 'pdf')
          echo "<embed src='$url' type='application/pdf' height='500px'>";
        else
          echo "<img src='$url'>";
      } else echo "<i>Tidak ada file notulen</i>"; ?>
    </div>
  </div>

  <!-- Foto -->
  <div class="section">
    <h3>🖼️ Dokumentasi Foto</h3>
    <div class="file-viewer">
      <?php
      if ($data['file_foto']) {
        $fotos = json_decode($data['file_foto'], true);
        foreach ($fotos as $f) {
          $url = pathToUrl($f);
          echo "<img src='$url'>";
        }
      } else echo "<i>Tidak ada dokumentasi foto</i>";
      ?>
    </div>
  </div>
</div>
</body>
</html>
