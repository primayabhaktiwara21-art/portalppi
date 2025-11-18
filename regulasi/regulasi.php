<?php
include_once '../koneksi.php';
include "../cek_akses.php";
$conn = $koneksi;

// ===============================
// SIMPAN DATA KE DATABASE
// ===============================
if (isset($_POST['submit'])) {
  $jenis = $_POST['jenis'];
  $nomor_dokumen = $_POST['nomor_dokumen'];
  $judul = $_POST['judul'];
  $tanggal_terbit = $_POST['tanggal_terbit'];
  $klasifikasi = $_POST['klasifikasi'];
  $berkas = '';


  // Folder upload
  $uploadDir = '../uploads/regulasi/';
  if (!file_exists($uploadDir)) mkdir($uploadDir, 0777, true);

  // Upload file jika ada
  if (!empty($_FILES['berkas']['name'])) {
    $filename = time() . '_' . preg_replace("/[^a-zA-Z0-9_\.-]/", "_", $_FILES['berkas']['name']);
    $filePath = $uploadDir . $filename;
    move_uploaded_file($_FILES['berkas']['tmp_name'], $filePath);
    $berkas = $filename;
  } else {
    $berkas = $_POST['link'] ?? '';
  }

$query = "INSERT INTO tb_regulasi (jenis, nomor_dokumen, judul, tanggal_terbit, klasifikasi, berkas)
          VALUES ('$jenis', '$nomor_dokumen', '$judul', '$tanggal_terbit', '$klasifikasi', '$berkas')";

  mysqli_query($conn, $query);
  echo "<script>alert('✅ Regulasi berhasil disimpan!'); window.location.href='regulasi.php';</script>";
  exit;
}

// ===============================
// HAPUS DATA
// ===============================
if (isset($_GET['hapus'])) {
  $id = $_GET['hapus'];
  $data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_regulasi WHERE id='$id'"));
  if ($data && file_exists("../uploads/regulasi/" . $data['berkas'])) {
    unlink("../uploads/regulasi/" . $data['berkas']);
  }
  mysqli_query($conn, "DELETE FROM tb_regulasi WHERE id='$id'");
  echo "<script>alert('🗑️ Data regulasi berhasil dihapus!'); window.location.href='regulasi.php';</script>";
  exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Regulasi PPI PHBW</title>
<style>
:root {
  --brand:#004aad;
  --brand-dark:#003580;
  --brand-light:#0074f0;
  --bg:#f6f8fc;
  --card:#ffffff;
  --line:#e7edf3;
  --ink:#0f172a;
  --muted:#607085;
  --shadow:0 8px 24px rgba(2,6,23,.06);
}
body {
  margin:0;
  font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;
  background:var(--bg);
  color:var(--ink);
}
/* ===== HEADER ===== */
.topbar {
  background:#fff;
  padding:14px 24px;
  display:flex;
  justify-content:space-between;
  align-items:center;
  box-shadow:0 2px 6px rgba(0,0,0,.04);
}
.topbar h2 {
  color:var(--brand-dark);
  margin:0;
  font-weight:700;
  display:flex;
  align-items:center;
  gap:8px;
}
a.btn {
  background:var(--brand);
  color:#fff;
  padding:10px 16px;
  border-radius:8px;
  text-decoration:none;
  font-weight:600;
}
a.btn:hover { background:var(--brand-dark); }
/* ===== CONTENT ===== */
.container {max-width:1100px;margin:0 auto;padding:24px;}
h1 {color:var(--brand-dark);}
.subtitle {color:var(--muted);}
.actions {display:flex;gap:8px;flex-wrap:wrap;justify-content:space-between;margin-bottom:14px;}
input[type="text"], select {
  padding:8px 10px;
  border:1px solid var(--line);
  border-radius:8px;
  min-width:250px;
}
/* ===== TABLE ===== */
.table-container {
  box-shadow:var(--shadow);
  border-radius:12px;
  overflow-x:auto;
}
table {
  width:100%;
  border-collapse:collapse;
  font-size:.95rem;
  background:var(--card);
}
th, td {
  padding: 6px 8px;
  border-bottom: 1px solid var(--line);
  font-size: 0.9rem;
}

th {
  background:linear-gradient(90deg, #002b6b 0%, #005fc8 100%);
  color:#fff;
  text-align:left;
  font-weight:700;
  text-transform:uppercase;
  letter-spacing:0.3px;
  box-shadow:inset 0 -1px 0 rgba(255,255,255,0.15);
}
tr:hover td {background:#f9fafc;}
/* ===== BUTTONS ===== */
button {
  border:none;
  padding:6px 10px;
  border-radius:8px;
  cursor:pointer;
  font-weight:600;
  transition:all .2s ease;
}
button.delete {background:#fee2e2;color:#b91c1c;}
button.delete:hover {background:#fecaca;}
button.add {background:var(--brand);color:#fff;}
button.add:hover {background:var(--brand-dark);}
button.view {background:#e0f2fe;color:#0369a1;}
/* ===== MODAL FORM ===== */
.form-modal {
  display:none;
  position:fixed;
  inset:0;
  background:rgba(0,0,0,0.45);
  align-items:center;
  justify-content:center;
  z-index:50;
}
.form-box {
  background:#fff;
  border-radius:12px;
  padding:24px;
  width:90%;
  max-width:400px;
  box-shadow:var(--shadow);
  animation:fadeIn .25s ease;
}
.form-box h3 {margin-top:0;color:var(--brand);}
.form-box input, .form-box select {
  width:100%;
  margin-bottom:12px;
  padding:8px;
  border:1px solid var(--line);
  border-radius:8px;
}
.form-box .btn-group {display:flex;justify-content:flex-end;gap:8px;}
@keyframes fadeIn {from{opacity:0;transform:scale(.9);}to{opacity:1;transform:scale(1);} }
/* ===== RESPONSIVE ===== */
@media (max-width: 768px) {
  .container {padding:16px;}
  .topbar {flex-direction:column;align-items:flex-start;gap:8px;}
  .actions {flex-direction:column;width:100%;}
  input[type="text"], select, button.add {width:100%;}
  th, td {padding:10px 8px;font-size:0.9rem;}
}
</style>
</head>

<body>
<div class="topbar">
  <h2>📘 Regulasi PPI PHBW</h2>
  <a href="/dashboard.php" class="btn">🏠 Kembali ke Dashboard</a>
</div>

<div class="container">
  <h1>Daftar Regulasi Rumah Sakit</h1>
  <p class="subtitle">Daftar SPO, Pedoman, Panduan, serta Kebijakan & Internal Memo yang berlaku di RS Primaya Bhakti Wara.</p>

  <div class="actions">
    <input type="text" id="searchInput" placeholder="🔍 Cari dokumen...">
    <button class="add" id="openForm">+ Tambah Regulasi</button>
  </div>

  <div class="table-container">
    <table id="regulasiTable">
      <thead>
        <tr>
          <th>No</th>
          <th>Nomor Dokumen</th>
          <th>Jenis</th>
          <th>Judul Dokumen</th>
          <th>Tanggal Terbit</th>
          <th>Klasifikasi</th>
          <th>Berkas</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $result = mysqli_query($conn, "SELECT * FROM tb_regulasi ORDER BY id DESC");
        $no = 1;
        if (mysqli_num_rows($result) > 0) {
          while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>
              <td>{$no}</td>
                <td>{$row['nomor_dokumen']}</td>
                <td>{$row['jenis']}</td>
                <td>{$row['judul']}</td>
                <td>{$row['tanggal_terbit']}</td>
                <td>{$row['klasifikasi']}</td>
              <td>";
              if (preg_match('/^https?:\/\//', $row['berkas'])) {
                echo "<a href='{$row['berkas']}' target='_blank' style='color:#004aad;font-weight:600;'>📄 Lihat</a>";
              } else {
                echo "<a href='../uploads/regulasi/{$row['berkas']}' target='_blank' style='color:#004aad;font-weight:600;'>📄 Lihat</a>";
              }
              echo "</td>
              <td><button class='delete' onclick=\"hapusData({$row['id']})\">Hapus</button></td>
            </tr>";
            $no++;
          }
        } else {
          echo "<tr><td colspan='8' style='text-align:center;'>Belum ada data regulasi</td></tr>";

        }
        ?>
      </tbody>
    </table>
  </div>
</div>

<!-- FORM TAMBAH -->
<div class="form-modal" id="formModal">
  <div class="form-box">
    <h3>Tambah Dokumen Regulasi</h3>
    <form method="POST" enctype="multipart/form-data">
        <label>Jenis Dokumen</label>
        <select name="jenis" required>
          <option value="">Pilih jenis...</option>
          <option>SPO</option>
          <option>Pedoman</option>
          <option>Panduan</option>
          <option>Kebijakan / Internal Memo</option>
        </select>
        
        <!-- ✅ Tambahkan di sini -->
        <label>Nomor Dokumen</label>
        <input type="text" name="nomor_dokumen" placeholder="Misal: SPO/PPI/001" required>
        
        <label>Judul Dokumen</label>
        <input type="text" name="judul" required>


        <label>Tanggal Terbit</label>
        <input type="date" name="tanggal_terbit" required>
        
        <label>Klasifikasi</label>
            <select name="klasifikasi" required>
              <option value="">Pilih klasifikasi...</option>
              <option>Kebersihan Tangan</option>
              <option>APD (Alat Pelindung Diri)</option>
              <option>Pengolahan Limbah</option>
              <option>Pengendalian Lingkungan</option>
              <option>Pengelolaan Linen</option>
              <option>Etika Batuk</option>
              <option>Dekontaminasi Alat</option>
              <option>Penyuntikan Aman</option>
              <option>Perlindungan Kesehatan Petugas</option>
              <option>Penempatan Pasien</option>
              <option>Praktek Lumbal Pungsi</option>
              <option>Pencegahan Infeksi Airborne</option>
              <option>Pencegahan Infeksi Kontak</option>
              <option>Pencegahan Infeksi Droplet</option>
              <option>Isolasi</option>
              <option>Bundle Hais</option>
              <option>Surveilans</option>
              <option>Gizi</option>
              <option>CSSD</option>
              <option>ICU</option>
              <option>NICU/Perina</option>
              <option>UKB</option>
              <option>Kamar Jenazah</option>
              <option>Ambulance</option>
              <option>ICRA Renovasi</option>
              <option>ICRA Program dan Unit</option>
              <option>PPI</option>
              <option>Internal PHBW</option>
              <option>Lainnya</option>
            </select>


      <label>Berkas (PDF) atau Link</label>
      <input type="file" name="berkas" accept=".pdf,.doc,.docx">
      <input type="text" name="link" placeholder="https://contoh-link.pdf">

      <div class="btn-group">
        <button type="button" id="closeForm">Batal</button>
        <button type="submit" class="add" name="submit">Simpan</button>
      </div>
    </form>
  </div>
</div>

<script>
const modal = document.getElementById('formModal');
document.getElementById('openForm').onclick = () => modal.style.display = 'flex';
document.getElementById('closeForm').onclick = () => modal.style.display = 'none';
window.onclick = e => { if (e.target == modal) modal.style.display = 'none'; };

function hapusData(id) {
  if (confirm('Yakin ingin menghapus dokumen ini?')) {
    window.location.href = '?hapus=' + id;
  }
}

// cari data
const searchInput = document.getElementById('searchInput');
searchInput.addEventListener('keyup', () => {
  const term = searchInput.value.toLowerCase();
  document.querySelectorAll('#regulasiTable tbody tr').forEach(row => {
    const text = row.textContent.toLowerCase();
    row.style.display = text.includes(term) ? '' : 'none';
  });
});
</script>
</body>
</html>
