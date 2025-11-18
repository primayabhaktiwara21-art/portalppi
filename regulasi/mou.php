<?php
include_once '../koneksi.php';
include "../cek_akses.php";
$conn = $koneksi;

// =================================
// SIMPAN DATA
// =================================
if (isset($_POST['submit'])) {
  $kategori = $_POST['kategori'];
  $uploadDir = '../uploads/' . ($kategori == 'mou' ? 'mou/' : 'izin/');
  if (!file_exists($uploadDir)) mkdir($uploadDir, 0777, true);

  $berkas = '';
  if (!empty($_FILES['berkas']['name'])) {
    $filename = time() . '_' . preg_replace("/[^a-zA-Z0-9_\.-]/", "_", $_FILES['berkas']['name']);
    move_uploaded_file($_FILES['berkas']['tmp_name'], $uploadDir . $filename);
    $berkas = $filename;
  } else {
    $berkas = $_POST['link'] ?? '';
  }

  if ($kategori == 'mou') {
    $nama = $_POST['nama'];
    $jenis = $_POST['jenis'];
    $nomor = $_POST['nomor'];
    $mulai = $_POST['mulai'];
    $berakhir = $_POST['berakhir'];
    mysqli_query($conn, "INSERT INTO tb_mou (nama_mitra, jenis_kerjasama, nomor_dokumen, tanggal_mulai, tanggal_berakhir, berkas)
      VALUES ('$nama','$jenis','$nomor','$mulai','$berakhir','$berkas')");
  } else {
    $nama = $_POST['nama'];
    $jenis = $_POST['jenis'];
    $nomor = $_POST['nomor'];
    $mulai = $_POST['mulai'];
    $berakhir = $_POST['berakhir'];
    mysqli_query($conn, "INSERT INTO tb_izin (jenis_izin, nomor_izin, nomor_dokumen, tanggal_terbit, tanggal_berlaku, berkas)
      VALUES ('$nama','$jenis','$nomor','$mulai','$berakhir','$berkas')");
  }

  echo "<script>alert('✅ Data berhasil disimpan!'); window.location.href='mou.php';</script>";
  exit;
}

// =================================
// HAPUS DATA
// =================================
if (isset($_GET['hapus'])) {
  $id = $_GET['hapus'];
  $kategori = $_GET['kategori'];
  $table = $kategori == 'mou' ? 'tb_mou' : 'tb_izin';
  $folder = $kategori == 'mou' ? 'mou' : 'izin';

  $data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM $table WHERE id='$id'"));
  if ($data && file_exists("../uploads/$folder/" . $data['berkas'])) {
    unlink("../uploads/$folder/" . $data['berkas']);
  }
  mysqli_query($conn, "DELETE FROM $table WHERE id='$id'");
  echo "<script>alert('🗑️ Data berhasil dihapus!'); window.location.href='mou.php';</script>";
  exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>MOU & Izin | PPI PHBW</title>
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
body {margin:0;font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;background:var(--bg);color:var(--ink);}
.topbar{background:#fff;padding:14px 24px;display:flex;justify-content:space-between;align-items:center;box-shadow:0 2px 6px rgba(0,0,0,.04);}
.topbar h2{color:var(--brand-dark);margin:0;font-weight:700;}
a.btn{background:var(--brand);color:#fff;padding:10px 16px;border-radius:8px;text-decoration:none;font-weight:600;}
a.btn:hover{background:var(--brand-dark);}
.container{max-width:1100px;margin:0 auto;padding:24px;}
.tab-btn{background:var(--card);border:1px solid var(--line);padding:10px 20px;border-radius:8px;margin-right:5px;cursor:pointer;font-weight:600;transition:all .2s;}
.tab-btn.active{background:var(--brand);color:white;border-color:var(--brand);}
.tab-btn:hover{background:#e5edff;}
.table-container{box-shadow:var(--shadow);border-radius:12px;overflow-x:auto;margin-top:15px;}
table{width:100%;border-collapse:collapse;font-size:.95rem;background:var(--card);}
th,td{padding:12px;border-bottom:1px solid var(--line);}
th{background:linear-gradient(90deg,#003580 0%,#006be6 100%);color:white;text-align:left;}
tr:hover td{background:#f9fbff;}
button{border:none;padding:6px 10px;border-radius:8px;cursor:pointer;font-weight:600;}
button.add{background:var(--brand);color:#fff;}button.add:hover{background:var(--brand-dark);}
button.delete{background:#fee2e2;color:#b91c1c;}button.delete:hover{background:#fecaca;}

/* === MODAL === */
.form-modal{display:none;position:fixed;inset:0;background:rgba(0,0,0,0.45);backdrop-filter:blur(4px);align-items:center;justify-content:center;z-index:50;animation:fadeIn .3s;}
.form-box{background:#fff;border-radius:16px;padding:28px 32px;width:90%;max-width:480px;box-shadow:0 12px 24px rgba(0,0,0,.2);animation:slideUp .3s;}
@keyframes fadeIn{from{opacity:0;}to{opacity:1;}}@keyframes slideUp{from{transform:translateY(30px);opacity:0;}to{transform:translateY(0);opacity:1;}}
.form-box h3{text-align:center;margin:0 0 16px;color:var(--brand-dark);border-bottom:2px solid var(--brand);padding-bottom:10px;}
.form-box label{display:block;font-weight:600;margin-bottom:6px;color:var(--brand-dark);}
.form-box input,.form-box select{width:100%;padding:10px 12px;border-radius:8px;border:1px solid var(--line);margin-bottom:14px;font-size:0.95rem;background-color:#f9fbff;transition:all .2s;}
.form-box input:focus,.form-box select:focus{border-color:var(--brand);outline:none;box-shadow:0 0 0 2px rgba(0,74,173,0.2);}
.form-box .btn-group{display:flex;justify-content:flex-end;gap:10px;margin-top:10px;}
button.cancel{background:#e2e8f0;color:#1e293b;}button.cancel:hover{background:#cbd5e1;transform:scale(1.03);}
.preview-file{font-size:.9em;color:#333;margin-top:5px;background:#f8fafc;padding:6px 10px;border-radius:8px;border:1px solid #e5e7eb;display:none;}
</style>
</head>
<body>
<div class="topbar">
  <h2>📑 MOU & Izin Resmi</h2>
  <a href="/dashboard.php" class="btn">🏠 Kembali ke Dashboard</a>
</div>

<div class="container">
  <div style="margin-bottom:10px;">
    <button class="tab-btn active" onclick="showTab('mou')">🤝 MOU</button>
    <button class="tab-btn" onclick="showTab('izin')">🪪 Izin Resmi</button>
    <button class="add" id="openForm" style="float:right;">+ Tambah Data</button>
  </div>

  <!-- TAB MOU -->
  <div id="tab_mou" class="table-container">
    <table>
      <thead><tr><th>No</th><th>Nama Mitra</th><th>Jenis Kerjasama</th><th>Nomor Dokumen</th><th>Mulai</th><th>Berakhir</th><th>Berkas</th><th>Aksi</th></tr></thead>
      <tbody>
        <?php
        $no=1;
        $res=mysqli_query($conn,"SELECT * FROM tb_mou ORDER BY id DESC");
        if(mysqli_num_rows($res)>0){
          while($r=mysqli_fetch_assoc($res)){
            echo"<tr>
              <td>$no</td>
              <td>{$r['nama_mitra']}</td>
              <td>{$r['jenis_kerjasama']}</td>
              <td>{$r['nomor_dokumen']}</td>
              <td>{$r['tanggal_mulai']}</td>
              <td>{$r['tanggal_berakhir']}</td>
              <td><a href='../uploads/mou/{$r['berkas']}' target='_blank' style='color:#004aad;font-weight:600;'>📄 Lihat</a></td>
              <td><button class='delete' onclick=\"hapusData('mou',{$r['id']})\">Hapus</button></td>
            </tr>";
            $no++;
          }
        } else echo "<tr><td colspan='8' align='center'>Belum ada data MOU</td></tr>";
        ?>
      </tbody>
    </table>
  </div>

  <!-- TAB IZIN -->
  <div id="tab_izin" class="table-container" style="display:none;">
    <table>
      <thead><tr><th>No</th><th>Jenis Izin</th><th>Nomor Izin</th><th>Nomor Dokumen</th><th>Terbit</th><th>Berlaku</th><th>Berkas</th><th>Aksi</th></tr></thead>
      <tbody>
        <?php
        $no=1;
        $res=mysqli_query($conn,"SELECT * FROM tb_izin ORDER BY id DESC");
        if(mysqli_num_rows($res)>0){
          while($r=mysqli_fetch_assoc($res)){
            echo"<tr>
              <td>$no</td>
              <td>{$r['jenis_izin']}</td>
              <td>{$r['nomor_izin']}</td>
              <td>{$r['nomor_dokumen']}</td>
              <td>{$r['tanggal_terbit']}</td>
              <td>{$r['tanggal_berlaku']}</td>
              <td><a href='../uploads/izin/{$r['berkas']}' target='_blank' style='color:#004aad;font-weight:600;'>📄 Lihat</a></td>
              <td><button class='delete' onclick=\"hapusData('izin',{$r['id']})\">Hapus</button></td>
            </tr>";
            $no++;
          }
        } else echo "<tr><td colspan='8' align='center'>Belum ada data izin</td></tr>";
        ?>
      </tbody>
    </table>
  </div>
</div>

<!-- FORM -->
<div class="form-modal" id="formModal">
  <div class="form-box">
    <h3>Tambah Data Baru</h3>
    <form method="POST" enctype="multipart/form-data">
      <label>Kategori</label>
      <select name="kategori" required>
        <option value="">-- Pilih Jenis Data --</option>
        <option value="mou">🤝 MOU (Kerjasama)</option>
        <option value="izin">🪪 Izin Resmi</option>
      </select>
      <label>Nama Mitra / Jenis Izin</label>
      <input type="text" name="nama" placeholder="Contoh: Universitas Bangka Belitung" required>
      <label>Jenis Kerjasama / Nomor Izin</label>
      <input type="text" name="jenis" placeholder="Contoh: Kerjasama Pendidikan / Nomor Izin RS" required>
      <label>Nomor Dokumen</label>
      <input type="text" name="nomor" placeholder="Contoh: MOU/RS-PHBW/001/2025">
      <label>Tanggal Mulai / Terbit</label>
      <input type="date" name="mulai" required>
      <label>Tanggal Berakhir / Berlaku</label>
      <input type="date" name="berakhir" required>
      <label>Berkas (PDF) atau Link</label>
      <input type="file" name="berkas" id="berkas" accept=".pdf,.doc,.docx">
      <div class="preview-file" id="preview"></div>
      <input type="text" name="link" placeholder="https://contoh-link.pdf">
      <div class="btn-group">
        <button type="button" class="cancel" id="closeForm">Batal</button>
        <button type="submit" class="add" name="submit">Simpan</button>
      </div>
    </form>
  </div>
</div>

<script>
const modal=document.getElementById('formModal');
document.getElementById('openForm').onclick=()=>modal.style.display='flex';
document.getElementById('closeForm').onclick=()=>modal.style.display='none';
window.onclick=e=>{if(e.target==modal)modal.style.display='none';};

function hapusData(kategori,id){
  if(confirm('Yakin ingin menghapus data ini?'))window.location.href='?hapus='+id+'&kategori='+kategori;
}
function showTab(tab){
  document.getElementById('tab_mou').style.display=tab=='mou'?'block':'none';
  document.getElementById('tab_izin').style.display=tab=='izin'?'block':'none';
  document.querySelectorAll('.tab-btn').forEach(btn=>btn.classList.remove('active'));
  document.querySelector(`.tab-btn[onclick="showTab('${tab}')"]`).classList.add('active');
}

// Preview nama file
document.getElementById('berkas').addEventListener('change',function(){
  const preview=document.getElementById('preview');
  if(this.files.length>0){
    preview.textContent='📎 '+this.files[0].name;
    preview.style.display='block';
  }else{
    preview.style.display='none';
  }
});
</script>
</body>
</html>
