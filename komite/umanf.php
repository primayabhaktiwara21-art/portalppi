<?php
include_once '../koneksi.php';
include "../cek_akses.php";
$conn = $koneksi;

// ============ SIMPAN DATA ============
if (isset($_POST['simpan'])) {
  $jenis = mysqli_real_escape_string($conn, $_POST['jenis_rapat']);
  $targetDir = "../uploads/umanf/";
  if (!file_exists($targetDir)) mkdir($targetDir, 0775, true);
  $allowed = ['pdf', 'jpg', 'jpeg', 'png'];

  // Upload file tunggal
  $fields = ['undangan', 'materi', 'absensi', 'notulen'];
  $uploaded = [];
  foreach ($fields as $f) {
    $name = $_FILES["file_$f"]['name'] ?? '';
    if ($name != '') {
      $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
      if (in_array($ext, $allowed)) {
        $newName = time() . '_' . preg_replace("/[^a-zA-Z0-9_\.-]/", "_", $name);
        move_uploaded_file($_FILES["file_$f"]['tmp_name'], $targetDir . $newName);
        $uploaded[$f] = $targetDir . $newName;
      }
    } else $uploaded[$f] = '';
  }

  // Upload banyak foto
  $fotoArr = [];
  if (!empty($_FILES['file_foto']['name'][0])) {
    foreach ($_FILES['file_foto']['name'] as $i => $fn) {
      $ext = strtolower(pathinfo($fn, PATHINFO_EXTENSION));
      if (in_array($ext, $allowed)) {
        $newName = time() . '_' . preg_replace("/[^a-zA-Z0-9_\.-]/", "_", $fn);
        move_uploaded_file($_FILES['file_foto']['tmp_name'][$i], $targetDir . $newName);
        $fotoArr[] = $targetDir . $newName;
      }
    }
  }
  $fotoJson = json_encode($fotoArr);

  $insert = mysqli_query($conn, "INSERT INTO tb_umanf 
    (jenis_rapat,file_undangan,file_materi,file_absensi,file_notulen,file_foto)
    VALUES('$jenis','{$uploaded['undangan']}','{$uploaded['materi']}','{$uploaded['absensi']}','{$uploaded['notulen']}','$fotoJson')");

  echo $insert
    ? "<script>alert('✅ Data berhasil disimpan');location.href='umanf.php';</script>"
    : "<script>alert('❌ Gagal menyimpan data');history.back();</script>";
  exit;
}

// ============ HAPUS DATA ============
if (isset($_GET['hapus'])) {
  $id = $_GET['hapus'];
  $q = mysqli_query($conn, "SELECT * FROM tb_umanf WHERE id='$id'");
  $d = mysqli_fetch_assoc($q);
  foreach (['file_undangan', 'file_materi', 'file_absensi', 'file_notulen'] as $f)
    if (!empty($d[$f]) && file_exists($d[$f])) unlink($d[$f]);
  if (!empty($d['file_foto'])) {
    $ff = json_decode($d['file_foto'], true);
    foreach ($ff as $f) if (file_exists($f)) unlink($f);
  }
  mysqli_query($conn, "DELETE FROM tb_umanf WHERE id='$id'");
  header("Location: umanf.php");
  exit;
}

// ============ AMBIL DATA ============
$data = mysqli_query($conn, "SELECT * FROM tb_umanf ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>UmanF (Dokumen Rapat) | PPI PHBW</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
<style>
:root {
  --primary: #1a2a80;
  --secondary: #2563eb;
  --success: #22c55e;
  --danger: #dc2626;
  --light: #f8fafc;
}
body {
  font-family: 'Poppins', sans-serif;
  background: #f5f7fa;
  margin: 0;
  color: #1e293b;
}
header {
  background: linear-gradient(90deg, var(--primary), var(--secondary));
  color: #fff;
  padding: 18px 28px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}
header h3 {
  margin: 0;
  font-weight: 600;
}
.btn {
  padding: 8px 14px;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  color: #fff;
  text-decoration: none;
  font-size: 14px;
  transition: all 0.2s ease;
}
.btn-green {
  background: linear-gradient(135deg, #22c55e, #16a34a);
  margin-bottom: 20px;
  box-shadow: 0 3px 8px rgba(34, 197, 94, 0.3);
}
.btn-green:hover { background: linear-gradient(135deg, #16a34a, #15803d); transform: translateY(-2px);}
.btn-blue { background: linear-gradient(135deg, #3b82f6, #1d4ed8);}
.btn-blue:hover { background: linear-gradient(135deg, #1d4ed8, #1e40af);}
.btn-red { background: linear-gradient(135deg, #ef4444, #b91c1c);}
.btn-red:hover { background: linear-gradient(135deg, #b91c1c, #991b1b);}

main {
  max-width: 1100px;
  margin: 40px auto;
  padding: 24px;
}
.table {
  width: 100%;
  border-collapse: collapse;
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
  overflow: hidden;
}
th, td {
  padding: 14px 16px;
  border-bottom: 1px solid #e2e8f0;
  text-align: left;
}
th {
  background: linear-gradient(90deg, var(--primary), var(--secondary));
  color: white;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}
tr:hover { background: var(--light); transition: background 0.2s; }

/* Overlay & Form */
.overlay {
  display: none;
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.45);
  justify-content: center;
  align-items: center;
  z-index: 999;
  transition: all 0.3s ease;
}
.overlay.show { display: flex; }

.form-box {
  background: #ffffff;
  padding: 32px 35px;
  border-radius: 16px;
  width: 90%;
  max-width: 480px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
  animation: fadeInUp 0.3s ease;
}
@keyframes fadeInUp {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}
.form-box h3 {
  text-align: center;
  color: var(--primary);
  margin-top: 0;
  margin-bottom: 20px;
  font-weight: 600;
}
.form-box label {
  font-weight: 500;
  color: #374151;
  display: flex;
  align-items: center;
  gap: 8px;
  margin-top: 12px;
  margin-bottom: 6px;
}
.form-box label span.icon {
  font-size: 16px;
  color: var(--secondary);
}
.form-box input[type="text"],
.form-box input[type="file"] {
  width: 100%;
  padding: 10px 12px;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  font-family: 'Poppins', sans-serif;
  font-size: 14px;
  background-color: #f9fafb;
  color: #374151;
  transition: all 0.2s ease;
}
.form-box input::placeholder {
  color: #9ca3af;
}
.form-box input:hover {
  border-color: #94a3b8;
}
.form-box input:focus {
  outline: none;
  border-color: var(--secondary);
  background: #fff;
  box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.2);
}

.form-actions {
  display: flex;
  justify-content: space-between;
  gap: 12px;
  margin-top: 22px;
}

.form-actions button {
  flex: 1;
  height: 44px; /* 🔥 tinggi seragam */
  border: none;
  border-radius: 8px;
  font-weight: 600;
  font-size: 14px;
  color: #fff;
  cursor: pointer;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px; /* jarak antara ikon dan teks */
  box-shadow: 0 3px 8px rgba(0, 0, 0, 0.15);
}

.form-actions button:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 14px rgba(0, 0, 0, 0.2);
}


</style>
</head>
<body>
<header>
  <h3>📋 UmanF (Dokumen Rapat) - PPI PHBW</h3>
  <a href="/dashboard.php" class="btn btn-blue">🏠 Kembali ke Dashboard</a>
</header>

<main>
  <button class="btn btn-green" id="btnTambah">+ Tambah Data Rapat</button>
  <table class="table">
    <tr>
      <th>No</th><th>Jenis Rapat</th><th>Undangan</th><th>Materi</th><th>Absensi</th><th>Notulen</th><th>Foto</th><th>Aksi</th>
    </tr>
    <?php $no=1; while($r=mysqli_fetch_assoc($data)){ ?>
    <tr>
      <td><?= $no++ ?></td>
      <td><?= htmlspecialchars($r['jenis_rapat']) ?></td>
      <td><?= $r['file_undangan']?"<a href='".str_replace('../','/',$r['file_undangan'])."' class='btn btn-blue' target='_blank'>Lihat</a>":"-" ?></td>
      <td><?= $r['file_materi']?"<a href='".str_replace('../','/',$r['file_materi'])."' class='btn btn-blue' target='_blank'>Lihat</a>":"-" ?></td>
      <td><?= $r['file_absensi']?"<a href='".str_replace('../','/',$r['file_absensi'])."' class='btn btn-blue' target='_blank'>Lihat</a>":"-" ?></td>
      <td><?= $r['file_notulen']?"<a href='".str_replace('../','/',$r['file_notulen'])."' class='btn btn-blue' target='_blank'>Lihat</a>":"-" ?></td>
      <td>
        <?php 
        if($r['file_foto']){
          $fotos=json_decode($r['file_foto'],true);
          foreach($fotos as $f){
            echo "<a href='".str_replace('../','/',$f)."' target='_blank' class='btn btn-blue' style='margin:2px;'>📷</a>";
          }
        } else echo '-';
        ?>
      </td>
      <td style="display:flex;gap:6px;">
  <a href="umanf_view.php?id=<?= $r['id'] ?>" class="btn btn-blue" title="Lihat semua file">🔍 Lihat Semua</a>
  <a href="?hapus=<?= $r['id'] ?>" onclick="return confirm('Hapus data ini?')" class="btn btn-red" title="Hapus data">🗑️</a>
</td>

    </tr>
    <?php } ?>
    <?php if(mysqli_num_rows($data)==0) echo "<tr><td colspan='8' align='center'>Belum ada data</td></tr>"; ?>
  </table>
</main>

<!-- Form Popup -->
<div class="overlay" id="formOverlay">
  <div class="form-box">
    <h3>Tambah Data Rapat</h3>
    <form method="POST" enctype="multipart/form-data">
      <label><span class="icon">🗂️</span> Jenis Rapat</label>
      <input type="text" name="jenis_rapat" required placeholder="Contoh: Rapat Evaluasi PPI">
      <label><span class="icon">📄</span> Undangan</label>
      <input type="file" name="file_undangan">
      <label><span class="icon">🧾</span> Materi</label>
      <input type="file" name="file_materi">
      <label><span class="icon">👥</span> Absensi</label>
      <input type="file" name="file_absensi">
      <label><span class="icon">🖋️</span> Notulen</label>
      <input type="file" name="file_notulen">
      <label><span class="icon">🖼️</span> Foto (boleh lebih dari 1)</label>
      <input type="file" name="file_foto[]" multiple>
      <div class="form-actions">
        <button type="submit" name="simpan" class="btn-green">💾 Simpan</button>
        <button type="button" class="btn-red" id="btnBatal">❌ Batal</button>
      </div>
    </form>
  </div>
</div>

<script>
const overlay = document.getElementById('formOverlay');
document.getElementById('btnTambah').onclick = () => overlay.classList.add('show');
document.getElementById('btnBatal').onclick = () => overlay.classList.remove('show');
overlay.onclick = e => { if (e.target === overlay) overlay.classList.remove('show'); };
</script>
</body>
</html>
