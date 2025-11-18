<?php
include_once '../koneksi.php';
include "../cek_akses.php";

// === SIMPAN FILE ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'upload') {
  $file = $_FILES['file'];

  if ($file['error'] === UPLOAD_ERR_OK) {
    $tipe = $file['type'];
    $jenis = (strpos($tipe, 'image') !== false) ? 'Foto' : 'Video';
    $elemen = $_POST['elemen'];
    $keterangan = $_POST['keterangan'];

    $namaFile = time() . "_" . basename($file['name']);
    $ukuran = $file['size'] / 1024;

    // path penyimpanan (langsung di bawah public_html/uploads/media)
    $folder = strtolower(str_replace(' ', '_', $jenis . "/" . $elemen));
    $targetDir = dirname(__DIR__) . "/uploads/media/$folder/";

    if (!file_exists($targetDir)) {
      mkdir($targetDir, 0777, true);
    }

    // path fisik untuk simpan file
    $pathFile = $targetDir . $namaFile;

    // path URL untuk ditampilkan
    $urlFile = "https://portalppi.my.id/uploads/media/$folder/" . $namaFile;

    if (move_uploaded_file($file['tmp_name'], $pathFile)) {
      $stmt = $conn->prepare("INSERT INTO tb_media_ppi (jenis_file, elemen, keterangan, nama_file, tipe_file, ukuran_file, path_file) VALUES (?, ?, ?, ?, ?, ?, ?)");
      $stmt->bind_param("ssssdds", $jenis, $elemen, $keterangan, $namaFile, $tipe, $ukuran, $urlFile);
      $stmt->execute();
    }
  }
  header("Location: " . $_SERVER['PHP_SELF']);
  exit;
}









// === HAPUS FILE ===
if (isset($_POST['action']) && $_POST['action'] == 'delete') {
  $id = $_POST['id'];
  $data = $conn->query("SELECT * FROM tb_media_ppi WHERE id=$id")->fetch_assoc();
  if ($data) {
    $path = realpath(__DIR__ . "/../" . $data['path_file']);
    if (file_exists($path)) unlink($path);
    $conn->query("DELETE FROM tb_media_ppi WHERE id=$id");
  }
  header("Location: " . $_SERVER['PHP_SELF']);
  exit;
}

// === FILTER ===
$filter_jenis = isset($_GET['jenis']) ? $_GET['jenis'] : 'semua';
$filter_elemen = isset($_GET['elemen']) ? $_GET['elemen'] : 'semua';

$query = "SELECT * FROM tb_media_ppi WHERE 1";
$params = [];

if ($filter_jenis != 'semua') {
  $query .= " AND jenis_file = ?";
  $params[] = $filter_jenis;
}
if ($filter_elemen != 'semua') {
  $query .= " AND elemen = ?";
  $params[] = $filter_elemen;
}
$query .= " ORDER BY tanggal_upload DESC";

$stmt = $conn->prepare($query);
if ($params) {
  $types = str_repeat("s", count($params));
  $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$media = $stmt->get_result();
?>


<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>📸 Galeri Foto & Video | PPI PHBW</title>
<style>
:root {
  --navy: #1a237e;
  --blue: #3949ab;
  --sky: #eef2ff;
  --white: #ffffff;
  --gray: #444;
  --green: #43a047;
  --red: #d32f2f;
  --shadow: 0 3px 10px rgba(0,0,0,0.1);
}

body {
  font-family: 'Segoe UI', sans-serif;
  background: var(--sky);
  color: var(--gray);
  margin: 0;
}

header {
  background: linear-gradient(90deg, var(--navy), var(--blue));
  color: white;
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 18px 30px;
  font-weight: 600;
  box-shadow: var(--shadow);
}

header h1 { margin: 0; font-size: 1.1em; }

button {
  border: none;
  border-radius: 8px;
  padding: 8px 14px;
  cursor: pointer;
  font-weight: 600;
  transition: all .2s;
}

.dashboard-btn {
  background: white; color: var(--blue);
}
.dashboard-btn:hover {
  background: var(--blue); color: white;
}

main {
  max-width: 1100px;
  margin: 30px auto;
  background: white;
  padding: 25px;
  border-radius: 14px;
  box-shadow: var(--shadow);
}

h2 {
  border-bottom: 3px solid var(--blue);
  padding-bottom: 8px;
  color: var(--navy);
  display: flex;
  align-items: center;
  gap: 8px;
}

.upload-box {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
  margin-bottom: 20px;
  align-items: center;
}

.upload-box input, select, textarea {
  padding: 10px;
  border-radius: 8px;
  border: 1px solid #ccc;
  font-size: 0.9em;
}

.upload-box button {
  background: var(--blue);
  color: white;
}

.upload-box button:hover {
  background: var(--navy);
}

.filter-box {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: 10px;
  margin-bottom: 15px;
  padding: 12px 16px;
  background: #f9f9ff;
  border-radius: 10px;
  border: 1px solid #d9dcf2;
}

.filter-box label {
  font-weight: 600;
  color: var(--navy);
}

.filter-box select {
  padding: 8px 14px;
  border-radius: 8px;
  border: 1px solid #ccd2ff;
  background-color: white;
}

.gallery {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
  gap: 16px;
}

.card {
  background: white;
  border: 1px solid #ddd;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: var(--shadow);
  transition: .3s;
}

.card:hover {
  transform: translateY(-4px);
  box-shadow: 0 6px 14px rgba(0,0,0,0.15);
}

.thumb {
  height: 160px;
  background: #f1f4ff;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
}

.thumb img, .thumb video {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.card-body {
  padding: 10px;
  text-align: center;
}

.card-body strong {
  display: block;
  color: var(--blue);
  font-size: 0.95em;
}

.card-body small {
  color: #666;
  font-size: 0.85em;
}

.actions {
  margin-top: 8px;
  display: flex;
  justify-content: center;
  gap: 4px;
}

.actions a, .actions button {
  border: none;
  border-radius: 6px;
  padding: 6px 10px;
  font-size: 0.85em;
  color: white;
  text-decoration: none;
}

.view { background: var(--blue); }
.download { background: var(--green); }
.delete { background: var(--red); }

.view:hover { background: #283593; }
.download:hover { background: #2e7d32; }
.delete:hover { background: #b71c1c; }

@media (max-width: 700px) {
  .upload-box, .filter-box { flex-direction: column; align-items: stretch; }
}
</style>
</head>
<body>

<header>
  <h1>📸 Galeri Foto & Video | PPI PHBW</h1>
  <button class="dashboard-btn" onclick="window.location.href='../dashboard.php'">🏠 Kembali ke Dashboard</button>
</header>

<main>
  <h2>🧾 Upload Foto / Video Berdasarkan Elemen</h2>

  <form class="upload-box" method="post" enctype="multipart/form-data">
    <input type="hidden" name="action" value="upload">

    <select name="elemen" id="elemen">
      <option value="">🧩 Pilih Elemen</option>
      <?php
      $elemenQ = $conn->query("SELECT DISTINCT elemen FROM tb_media_ppi ORDER BY elemen ASC");
      while ($r = $elemenQ->fetch_assoc()) echo "<option value='{$r['elemen']}'>{$r['elemen']}</option>";
      ?>
    </select>

    <input type="text" id="elemenBaru" placeholder="Tambah Elemen Baru">
    <button type="button" onclick="tambahElemen()">+ Tambah</button>

    <textarea name="keterangan" placeholder="Tuliskan keterangan singkat..." rows="1"></textarea>
    <input type="file" name="file" accept="image/*,video/*" required>
    <button type="submit">⬆️ Upload</button>
  </form>

  <div class="filter-box">
    <label>Filter Jenis:</label>
    <select onchange="filter('jenis', this.value)">
      <option value="semua" <?= $filter_jenis=='semua'?'selected':'' ?>>Semua</option>
      <option value="Foto" <?= $filter_jenis=='Foto'?'selected':'' ?>>📷 Foto</option>
      <option value="Video" <?= $filter_jenis=='Video'?'selected':'' ?>>🎥 Video</option>
    </select>

    <label>Filter Elemen:</label>
    <select onchange="filter('elemen', this.value)">
      <option value="semua" <?= $filter_elemen=='semua'?'selected':'' ?>>Semua</option>
      <?php
      $elemenQ2 = $conn->query("SELECT DISTINCT elemen FROM tb_media_ppi ORDER BY elemen ASC");
      while ($r = $elemenQ2->fetch_assoc()) {
        $sel = ($filter_elemen == $r['elemen']) ? 'selected' : '';
        echo "<option value='{$r['elemen']}' $sel>{$r['elemen']}</option>";
      }
      ?>
    </select>
  </div>

  <div class="gallery">
    <?php while ($row = $media->fetch_assoc()): ?>
    
    
    
    
  <div class="card">
  <div class="thumb">
    <?php
      // Buat path absolut aman
      $filePath = $row['path_file'];
      if (strpos($filePath, 'http') === false) {
          $filePath = 'https://portalppi.my.id' . $filePath;
      }
    ?>

    <?php if (strpos($row['tipe_file'], 'image') !== false): ?>
      <img src="<?= htmlspecialchars($filePath) ?>" alt="">
    <?php elseif (strpos($row['tipe_file'], 'video') !== false): ?>
      <video src="<?= htmlspecialchars($filePath) ?>" controls></video>
    <?php endif; ?>
  </div>

  <div class="card-body">
    <strong><?= htmlspecialchars($row['elemen']) ?></strong>
    <small><?= htmlspecialchars($row['keterangan']) ?></small>

    <div class="actions">
      <a class="view" href="<?= htmlspecialchars($filePath) ?>" target="_blank">👁️</a>
      <a class="download" href="<?= htmlspecialchars($filePath) ?>" download>⬇️</a>
      <form method="post" style="display:inline;">
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="id" value="<?= $row['id'] ?>">
        <button type="submit" class="delete" onclick="return confirm('Hapus file ini?')">🗑️</button>
      </form>
    </div>
  </div>
</div>

      
      
    <?php endwhile; ?>
  </div>
</main>

<script>
function tambahElemen(){
  const baru = document.getElementById("elemenBaru").value.trim();
  if(!baru) return;
  const sel = document.getElementById("elemen");
  const opt = document.createElement("option");
  opt.value = baru;
  opt.textContent = baru;
  sel.appendChild(opt);
  sel.value = baru;
  document.getElementById("elemenBaru").value = "";
  document.getElementById("file").focus();
}

function filter(type, value){
  const params = new URLSearchParams(window.location.search);
  params.set(type, value);
  window.location.search = params.toString();
}
</script>
</body>
</html>
