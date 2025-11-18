<?php
include_once '../koneksi.php';
include "../cek_akses.php";
$conn = $koneksi;

// === TAMBAH DATA ===
if (isset($_POST['simpan'])) {
  $nomor = mysqli_real_escape_string($conn, $_POST['nomor_sk']);
  $judul = mysqli_real_escape_string($conn, $_POST['judul_sk']);
  $tanggal = $_POST['tanggal'];

  $file = $_FILES['file_sk'];
  $namaFile = basename($file['name']);
  $targetDir = "../uploads/sk/";
  $namaUnik = time() . "_" . preg_replace("/[^a-zA-Z0-9_\.-]/", "_", $namaFile);
  $targetFile = $targetDir . $namaUnik;
  $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

  // Validasi tipe file
  if ($fileType != "pdf") {
    echo "<script>alert('❌ Hanya file PDF yang diizinkan!');window.location='sk.php';</script>";
    exit;
  }

  // Upload file
  if (move_uploaded_file($file["tmp_name"], $targetFile)) {
    // Simpan ke database (gunakan kolom link_file)
    mysqli_query($conn, "INSERT INTO tb_sk (nomor_sk, judul_sk, tanggal, link_file)
                         VALUES ('$nomor', '$judul', '$tanggal', '$targetFile')");
    echo "<script>alert('✅ Data SK berhasil disimpan!');window.location='sk.php';</script>";
    exit;
  } else {
    echo "<script>alert('⚠️ Gagal mengunggah file! Pastikan folder uploads/sk dapat ditulis.');window.location='sk.php';</script>";
  }
}

// === HAPUS DATA ===
if (isset($_GET['hapus'])) {
  $id = $_GET['hapus'];
  $q = mysqli_query($conn, "SELECT link_file FROM tb_sk WHERE id='$id'");
  $data = mysqli_fetch_assoc($q);
  if ($data && file_exists($data['link_file'])) unlink($data['link_file']);
  mysqli_query($conn, "DELETE FROM tb_sk WHERE id='$id'");
  echo "<script>alert('🗑️ Data berhasil dihapus!');window.location='sk.php';</script>";
  exit;
}

// === PENCARIAN ===
$cari = $_GET['cari'] ?? '';
$where = $cari ? "WHERE nomor_sk LIKE '%$cari%' OR judul_sk LIKE '%$cari%'" : '';
$res = mysqli_query($conn, "SELECT * FROM tb_sk $where ORDER BY tanggal DESC");
?>


<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Daftar SK | PPI PHBW</title>
<style>
:root {
  --primary:#1a2a80;
  --secondary:#3b49df;
  --success:#16a34a;
  --danger:#dc2626;
  --bg:#f4f6fb;
  --card:#ffffff;
  --shadow:0 6px 18px rgba(20,24,66,0.08);
  --radius:12px;
}
body{margin:0;font-family:'Poppins',sans-serif;background:var(--bg);color:#222;}
header{background:var(--primary);color:white;padding:14px 24px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;}
header h1{font-size:1.2rem;margin:0;font-weight:600;}
a.btn{background:var(--secondary);color:white;padding:8px 14px;border-radius:8px;text-decoration:none;font-weight:600;}
a.btn:hover{background:#2531a1;}
main{max-width:1100px;margin:auto;padding:24px;}
button{border:none;border-radius:8px;padding:8px 14px;font-weight:600;cursor:pointer;}
.btn-add{background:var(--success);color:white;}
.btn-add:hover{background:#15803d;}
.btn-del{background:var(--danger);color:white;}
.btn-del:hover{background:#b91c1c;}

/* Search */
.search-box{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:16px;}
.search-box input{padding:8px;border:1px solid #ccc;border-radius:8px;min-width:240px;}
.search-box button{background:var(--secondary);color:white;}
.search-box button:hover{background:#1e3a8a;}

/* Table */
.table-container{background:var(--card);border-radius:var(--radius);box-shadow:var(--shadow);overflow-x:auto;}
table{width:100%;border-collapse:collapse;min-width:700px;}
th,td{padding:12px 10px;border-bottom:1px solid #e5e7eb;text-align:left;}
th{background:linear-gradient(180deg,#1e3a8a,#3b49df);color:white;text-transform:uppercase;font-size:.9rem;}
tr:hover td{background:#f9fafb;}
.actions{display:flex;gap:6px;flex-wrap:wrap;}

/* Modal */
.overlay{position:fixed;inset:0;background:rgba(0,0,0,0.45);display:none;align-items:center;justify-content:center;z-index:100;}
.overlay.show{display:flex;}
.popup-form{background:white;border-radius:12px;padding:24px;width:90%;max-width:450px;box-shadow:var(--shadow);}
.popup-form h2{text-align:center;color:var(--primary);margin-top:0;}
label{display:block;margin-top:10px;font-weight:500;}
input[type=text],input[type=date],input[type=file]{width:100%;padding:8px;border:1px solid #ccc;border-radius:8px;margin-top:4px;}
button.full{width:100%;margin-top:10px;}
.btn-cancel{background:#6b7280;color:white;}
.btn-cancel:hover{background:#555;}
@media(max-width:768px){
  main{padding:16px;}
  .search-box{flex-direction:column;}
  .search-box input,button{width:100%;}
  .btn-add{width:100%;}
  table{font-size:13px;}
}
</style>
</head>
<body>
<header>
  <h1>📜 Daftar Surat Keputusan (SK) - PPI PHBW</h1>
  <a href="/dashboard.php" class="btn">🏠 Dashboard</a>
</header>

<main>
  <div class="search-box">
    <form method="get" style="display:flex;gap:8px;width:100%;flex-wrap:wrap;">
      <input type="text" name="cari" placeholder="🔍 Cari Nomor atau Judul SK..." value="<?= htmlspecialchars($cari) ?>">
      <button type="submit">Cari</button>
      <button type="button" class="btn-add" onclick="bukaForm()">+ Tambah SK</button>
    </form>
  </div>

  <div class="table-container">
    <table>
      <thead>
        <tr><th>No</th><th>Nomor SK</th><th>Judul SK</th><th>Tanggal</th><th>File</th><th>Aksi</th></tr>
      </thead>
      <tbody>
        <?php
        $no=1;
        if (mysqli_num_rows($res)>0) {
          while($r=mysqli_fetch_assoc($res)){
            $tgl=date('d F Y',strtotime($r['tanggal']));
            $file = str_replace("../", "", $r['link_file']);
            echo "<tr>
              <td>$no</td>
              <td>{$r['nomor_sk']}</td>
              <td>{$r['judul_sk']}</td>
              <td>$tgl</td>
              <td><a href='/$file' target='_blank' class='btn' style='background:var(--secondary)'>Lihat</a></td>
              <td class='actions'><a href='?hapus={$r['id']}' onclick=\"return confirm('Yakin hapus data ini?')\" class='btn-del'>Hapus</a></td>
            </tr>";
            $no++;
          }
        } else {
          echo "<tr><td colspan='6' align='center'>Tidak ada data ditemukan.</td></tr>";
        }
        ?>
      </tbody>
    </table>
  </div>
</main>

<!-- FORM TAMBAH -->
<div class="overlay" id="formOverlay">
  <div class="popup-form">
    <h2>Tambah Data SK</h2>
    <form method="POST" enctype="multipart/form-data">
      <label>Nomor SK</label>
      <input type="text" name="nomor_sk" required placeholder="Contoh: SK/003/PPI/2025">
      <label>Judul SK</label>
      <input type="text" name="judul_sk" required placeholder="Contoh: SK Koordinator Hand Hygiene">
      <label>Tanggal</label>
      <input type="date" name="tanggal" required>
      <label>Unggah File (PDF)</label>
      <input type="file" name="file_sk" accept="application/pdf" required>
      <button type="submit" name="simpan" class="btn-add full">💾 Simpan</button>
      <button type="button" class="btn-cancel full" onclick="tutupForm()">❌ Batal</button>
    </form>
  </div>
</div>

<script>
const overlay=document.getElementById('formOverlay');
function bukaForm(){overlay.classList.add('show');}
function tutupForm(){overlay.classList.remove('show');}
window.onclick=e=>{if(e.target==overlay)overlay.classList.remove('show');};
</script>
</body>
</html>
