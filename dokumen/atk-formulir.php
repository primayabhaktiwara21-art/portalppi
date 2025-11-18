<?php
include_once '../koneksi.php';
include "../cek_akses.php";

// === SIMPAN DATA ===
if (isset($_POST['action']) && $_POST['action'] == 'save') {
  $kategori = $_POST['kategori'];
  $judul = $_POST['judul'];
  $no_dokumen = $_POST['no_dokumen'];
  $deskripsi = $_POST['deskripsi'];
  $file = $_FILES['file'];

  $namaFile = time() . "_" . basename($file['name']);
  $tipe = pathinfo($file['name'], PATHINFO_EXTENSION);
  $ukuran = $file['size'] / 1024;

  $folder = strtolower(str_replace(' ', '_', $kategori));
  $target = "uploads/$folder/" . $namaFile;
  if (!file_exists("uploads/$folder")) mkdir("uploads/$folder", 0777, true);
  move_uploaded_file($file['tmp_name'], $target);

  $stmt = $conn->prepare("INSERT INTO tb_dokumen_ppi (kategori, judul, no_dokumen, deskripsi, nama_file, tipe_file, ukuran_file) VALUES (?, ?, ?, ?, ?, ?, ?)");
  $stmt->bind_param("ssssssi", $kategori, $judul, $no_dokumen, $deskripsi, $namaFile, $tipe, $ukuran);
  $stmt->execute();

  header("Location: " . $_SERVER['PHP_SELF']);
  exit;
}

// === HAPUS DATA ===
if (isset($_POST['action']) && $_POST['action'] == 'delete') {
  $id = $_POST['id'];
  $data = $conn->query("SELECT * FROM tb_dokumen_ppi WHERE id=$id")->fetch_assoc();
  if ($data) {
    $folder = strtolower(str_replace(' ', '_', $data['kategori']));
    $path = "uploads/$folder/" . $data['nama_file'];
    if (file_exists($path)) unlink($path);
    $conn->query("DELETE FROM tb_dokumen_ppi WHERE id=$id");
  }
  header("Location: " . $_SERVER['PHP_SELF']);
  exit;
}

// === FILTER DATA ===
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'semua';
if ($filter == 'semua') {
  $files = $conn->query("SELECT * FROM tb_dokumen_ppi ORDER BY tanggal_upload ASC");
} else {
  $stmt = $conn->prepare("SELECT * FROM tb_dokumen_ppi WHERE kategori = ? ORDER BY tanggal_upload ASC");
  $stmt->bind_param("s", $filter);
  $stmt->execute();
  $files = $stmt->get_result();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>📂 Media Edukasi & Formulir PPI | PPI PHBW</title>
<style>
body{font-family:'Poppins',sans-serif;background:#eef2ff;margin:0;color:#333;}
header{background:linear-gradient(90deg,#1a237e,#3b49df);color:white;padding:16px 25px;
display:flex;justify-content:space-between;align-items:center;font-weight:600;}
header h1{margin:0;font-size:1.1em;}
button{cursor:pointer;border:none;border-radius:8px;padding:8px 14px;font-weight:600;}
.dashboard-btn{background:white;color:#1a237e;} .dashboard-btn:hover{background:#3b49df;color:white;}
main{max-width:1100px;margin:30px auto;background:white;padding:25px;border-radius:14px;
box-shadow:0 3px 12px rgba(0,0,0,0.1);}
h2{border-bottom:3px solid #3b49df;padding-bottom:8px;color:#1a237e;}
.add-btn{background:#3b49df;color:white;float:right;margin-bottom:10px;}
.table-container{margin-top:20px;overflow-x:auto;}
table{width:100%;border-collapse:collapse;}
th,td{padding:10px;border-bottom:1px solid #dce0f0;text-align:left;}
th{background:#f3f5ff;}
.actions form{display:inline;}
.actions button{margin:2px;padding:6px 10px;font-size:0.85em;color:white;}
.view{background:#3b49df;} .delete{background:#d32f2f;}
.view:hover{background:#1a237e;} .delete:hover{background:#9a0007;}
/* Modal */
.modal{display:none;position:fixed;top:0;left:0;width:100%;height:100%;
background:rgba(0,0,0,0.5);justify-content:center;align-items:center;z-index:999;}
.modal-content{background:white;padding:20px 25px;border-radius:10px;width:400px;position:relative;}
.close{position:absolute;top:10px;right:15px;font-size:18px;cursor:pointer;}
label{display:block;margin-top:10px;font-weight:500;}
input,select,textarea{width:100%;padding:8px;margin-top:5px;border-radius:6px;border:1px solid #ccc;}
.save{background:#3b49df;color:white;width:100%;margin-top:15px;}
/* Filter box area */
.filter-box {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: #f9f9ff;
  border: 1px solid #d9dcf2;
  border-radius: 10px;
  padding: 12px 18px;
  margin: 15px 0;
  box-shadow: 0 2px 6px rgba(0,0,0,0.05);
}

.filter-box label {
  font-weight: 600;
  color: #1a237e;
  font-size: 0.95em;
}

.filter-box select {
  padding: 8px 14px;
  border-radius: 8px;
  border: 1px solid #ccd2ff;
  font-size: 0.95em;
  color: #333;
  outline: none;
  background-color: white;
  transition: all 0.2s ease-in-out;
}

.filter-box select:hover {
  border-color: #3b49df;
  box-shadow: 0 0 0 2px rgba(59,73,223,0.2);
}

/* Atur agar di layar kecil tetap bagus */
@media (max-width: 600px) {
  .filter-box {
    flex-direction: column;
    align-items: flex-start;
    gap: 8px;
  }
  .filter-box select {
    width: 100%;
  }
}

.filter-box{display:flex;align-items:center;gap:10px;margin-bottom:15px;}
.filter-box select{padding:8px;border-radius:6px;border:1px solid #ccc;}
</style>
</head>
<body>
<header>
  <h1>📂 Media Edukasi & Formulir PPI | PPI PHBW</h1>
  <button class="dashboard-btn" onclick="window.location.href='../dashboard.php'">🏠 Kembali ke Dashboard</button>
</header>

<main>
  <h2>📁 Daftar Dokumen PPI
    <button class="add-btn" onclick="openModal()">➕ Tambah Dokumen</button>
  </h2>

<div class="filter-box">
  <label for="filter">Filter Kategori:</label>
  <select id="filter" onchange="filterKategori()">
    <option value="semua" <?= $filter=='semua'?'selected':'' ?>>Semua Kategori</option>
    <option value="Formulir" <?= $filter=='Formulir'?'selected':'' ?>>Formulir</option>
    <option value="Checklist" <?= $filter=='Checklist'?'selected':'' ?>>Checklist</option>
    <option value="Brosur" <?= $filter=='Brosur'?'selected':'' ?>>Brosur</option>
    <option value="Leaflet" <?= $filter=='Leaflet'?'selected':'' ?>>Leaflet</option>
    <option value="Media Edukasi" <?= $filter=='Media Edukasi'?'selected':'' ?>>Media Edukasi</option>
  </select>
</div>


  <div class="table-container">
    <table>
        <thead>
          <tr>
            <th>No Dokumen</th>
            <th>Kategori</th>
            <th>Judul</th>
            <th>Deskripsi</th>
            <th>File</th>
            <th>Aksi</th>
          </tr>
        </thead>

    <tbody>
    <?php if ($files->num_rows > 0): ?>
      <?php while ($row = $files->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($row['no_dokumen']) ?></td>         <!-- <--- dipindah ke depan -->
          <td><?= htmlspecialchars($row['kategori']) ?></td>
          <td><?= htmlspecialchars($row['judul']) ?></td>
          <td><?= htmlspecialchars($row['deskripsi']) ?></td>
          <td>
            <a class="view" href="uploads/<?= strtolower(str_replace(' ', '_', $row['kategori'])) ?>/<?= rawurlencode($row['nama_file']) ?>" target="_blank">📄 Lihat</a>

          </td>
          <td class="actions">
            <form method="post" style="display:inline;">
              <input type="hidden" name="action" value="delete">
              <input type="hidden" name="id" value="<?= (int)$row['id'] ?>">
              <button type="submit" class="delete" onclick="return confirm('Yakin hapus dokumen ini?')">🗑️ Hapus</button>
            </form>
          </td>
        </tr>
      <?php endwhile; ?>
    <?php else: ?>
      <tr><td colspan="7" style="text-align:center;color:gray;">Belum ada dokumen untuk kategori ini</td></tr>
    <?php endif; ?>
    </tbody>

    </table>
  </div>
</main>

<!-- Modal Popup -->
<div id="popup" class="modal">
  <div class="modal-content">
    <span class="close" onclick="closeModal()">&times;</span>
    <h3>➕ Tambah Dokumen Baru</h3>
    <form method="post" enctype="multipart/form-data">
      <input type="hidden" name="action" value="save">
      <label>Kategori</label>
      <select name="kategori" required>
        <option value="">Pilih Kategori</option>
        <option value="Formulir">Formulir</option>
        <option value="Checklist">Checklist</option>
        <option value="Brosur">Brosur</option>
        <option value="Leaflet">Leaflet</option>
        <option value="Media Edukasi">Media Edukasi</option>
      </select>
      <label>Judul Dokumen</label>
      <input type="text" name="judul" required>
      <label>No Dokumen</label>
      <input type="text" name="no_dokumen">
      <label>Deskripsi</label>
      <textarea name="deskripsi"></textarea>
      <label>Upload File</label>
      <input type="file" name="file" required>
      <button type="submit" class="save">💾 Simpan Dokumen</button>
    </form>
  </div>
</div>

<script>
function openModal(){document.getElementById("popup").style.display="flex";}
function closeModal(){document.getElementById("popup").style.display="none";}
function filterKategori(){
  const val = document.getElementById('filter').value;
  window.location.href = '?filter=' + encodeURIComponent(val);
}
</script>

</body>
</html>
