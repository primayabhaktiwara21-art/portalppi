<?php
include_once '../koneksi.php';
include "../cek_akses.php";
$conn = $koneksi;

// === TAMBAH DATA ===
if (isset($_POST['simpan'])) {
  $nama = mysqli_real_escape_string($conn, $_POST['nama_program']);
  $desk = mysqli_real_escape_string($conn, $_POST['deskripsi']);
  $pj = mysqli_real_escape_string($conn, $_POST['penanggung_jawab']);
  $mulai = $_POST['tanggal_mulai'];
  $selesai = $_POST['tanggal_selesai'];

  $file = $_FILES['file_program'];
  $namaFile = basename($file['name']);
  $targetDir = "../uploads/program/";
  $namaUnik = time() . "_" . preg_replace("/[^a-zA-Z0-9_\.-]/", "_", $namaFile);
  $targetFile = $targetDir . $namaUnik;
  $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

  if ($fileType != "pdf") {
    echo "<script>alert('❌ Hanya file PDF yang diizinkan!');window.location='program.php';</script>";
    exit;
  }

  if (move_uploaded_file($file["tmp_name"], $targetFile)) {
    mysqli_query($conn, "INSERT INTO tb_program_ppi (nama_program, deskripsi, penanggung_jawab, tanggal_mulai, tanggal_selesai, file_path)
                         VALUES ('$nama', '$desk', '$pj', '$mulai', '$selesai', '$targetFile')");
    echo "<script>alert('✅ Program berhasil disimpan!');window.location='program.php';</script>";
  } else {
    echo "<script>alert('⚠️ Gagal mengunggah file! Pastikan folder uploads/program dapat ditulis.');window.location='program.php';</script>";
  }
}

// === HAPUS DATA ===
if (isset($_GET['hapus'])) {
  $id = $_GET['hapus'];
  $q = mysqli_query($conn, "SELECT file_path FROM tb_program_ppi WHERE id='$id'");
  $data = mysqli_fetch_assoc($q);
  if ($data && file_exists($data['file_path'])) unlink($data['file_path']);
  mysqli_query($conn, "DELETE FROM tb_program_ppi WHERE id='$id'");
  echo "<script>alert('🗑️ Data program dihapus.');window.location='program.php';</script>";
  exit;
}

// === AMBIL DATA ===
$res = mysqli_query($conn, "SELECT * FROM tb_program_ppi ORDER BY id ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width,initial-scale=1.0" />
<title>Daftar Program PPI | PHBW</title>
<style>
:root {
  --primary:#1a2a80; --secondary:#3b49df;
  --success:#28a745; --danger:#dc2626;
  --bg:#f4f6fb; --card:#ffffff;
  --shadow:0 6px 20px rgba(0,0,0,0.08);
}
body{font-family:'Poppins',sans-serif;background:var(--bg);margin:0;color:#1e293b;}
header{background:linear-gradient(90deg,var(--primary),var(--secondary));color:white;padding:16px 24px;display:flex;justify-content:space-between;align-items:center;box-shadow:var(--shadow);}
header h1{font-size:1.1rem;margin:0;}
.btn{border:none;border-radius:8px;color:white;cursor:pointer;text-decoration:none;font-size:14px;padding:8px 14px;}
.btn-dashboard{background:linear-gradient(135deg,#4f46e5,#06b6d4);}
.btn-dashboard:hover{background:linear-gradient(135deg,#4338ca,#0891b2);}
.btn-tambah{background:var(--success);}
.btn-hapus{background:var(--danger);}
.btn-hapus:hover{background:#b91c1c;}

main {
  max-width: 1100px;
  margin: auto;
  padding: 32px 24px;
}

.btn-tambah {
  background: var(--success);
  margin-bottom: 24px;   /* Tambahkan jarak ke tabel */
  padding: 10px 18px;
  font-weight: 600;
  border: none;
  border-radius: 8px;
  color: #fff;
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-tambah:hover {
  background: #1f8539;
  transform: translateY(-2px);
}



.table-container{background:var(--card);border-radius:10px;box-shadow:var(--shadow);overflow-x:auto;}
table{width:100%;border-collapse:collapse;}
th,td{padding:12px 14px;border-bottom:1px solid #e5e7eb;text-align:left;}
th{background:linear-gradient(90deg,var(--secondary),#37a0ff);color:white;text-transform:uppercase;font-size:.85rem;}
tr:hover{background:#f9fafb;}
.actions{display:flex;gap:8px;}
.overlay{position:fixed;inset:0;background:rgba(0,0,0,0.45);display:none;justify-content:center;align-items:center;z-index:100;}
.overlay.show{display:flex;}
.popup-form{background:white;padding:24px;border-radius:12px;width:90%;max-width:400px;box-shadow:var(--shadow);}
.popup-form h2{text-align:center;color:var(--primary);margin-top:0;}
label{display:block;margin-top:10px;font-weight:500;}
input,textarea{width:100%;padding:8px;border-radius:8px;border:1px solid #ccc;margin-top:4px;}
.btn-cancel{background:#6b7280;}
@media(max-width:600px){th,td{font-size:13px;}header{flex-direction:column;gap:10px;}}
</style>
</head>
<body>
<header>
  <h1>🗂️ Daftar Program PPI - PHBW</h1>
  <a href="/dashboard.php" class="btn btn-dashboard">🏠 Kembali ke Dashboard</a>
</header>

<main>
  <button class="btn btn-tambah" onclick="bukaForm()">+ Tambah Program</button>
  <div class="table-container">
    <table>
      <thead>
        <tr>
          <th>No</th><th>Nama Program</th><th>Deskripsi</th><th>Penanggung Jawab</th><th>Periode</th><th>File</th><th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $no=1;
        if (mysqli_num_rows($res)>0){
          while($r=mysqli_fetch_assoc($res)){
            $periode=date('M Y',strtotime($r['tanggal_mulai'])).' - '.date('M Y',strtotime($r['tanggal_selesai']));
            $file=str_replace("../","",$r['file_path']);
            echo "<tr>
              <td>$no</td>
              <td>{$r['nama_program']}</td>
              <td>{$r['deskripsi']}</td>
              <td>{$r['penanggung_jawab']}</td>
              <td>$periode</td>
              <td><a href='/$file' target='_blank' class='btn btn-dashboard'>Lihat</a></td>
              <td class='actions'>
                <a href='?hapus={$r['id']}' onclick=\"return confirm('Yakin ingin menghapus data ini?')\" class='btn btn-hapus'>🗑️</a>
              </td>
            </tr>";
            $no++;
          }
        } else {
          echo "<tr><td colspan='7' align='center'>Tidak ada data program.</td></tr>";
        }
        ?>
      </tbody>
    </table>
  </div>
</main>

<!-- FORM TAMBAH -->
<div class="overlay" id="formOverlay">
  <div class="popup-form">
    <h2>Tambah Program PPI</h2>
    <form method="POST" enctype="multipart/form-data">
      <label>Nama Program</label>
      <input type="text" name="nama_program" required placeholder="Contoh: Program Hand Hygiene">
      <label>Deskripsi Singkat</label>
      <textarea name="deskripsi" required placeholder="Contoh: Program meningkatkan kepatuhan cuci tangan."></textarea>
      <label>Penanggung Jawab</label>
      <input type="text" name="penanggung_jawab" required placeholder="Contoh: Ketua PPI">
      <label>Tanggal Mulai</label>
      <input type="date" name="tanggal_mulai" required>
      <label>Tanggal Selesai</label>
      <input type="date" name="tanggal_selesai" required>
      <label>Upload File (PDF)</label>
      <input type="file" name="file_program" accept="application/pdf" required>
      <button type="submit" name="simpan" class="btn btn-tambah">💾 Simpan</button>
      <button type="button" class="btn btn-cancel" onclick="tutupForm()">❌ Batal</button>
    </form>
  </div>
</div>

<script>
const overlay=document.getElementById('formOverlay');
function bukaForm(){overlay.classList.add('show');}
function tutupForm(){overlay.classList.remove('show');}
window.onclick=e=>{if(e.target==overlay)tutupForm();}
</script>
</body>
</html>
