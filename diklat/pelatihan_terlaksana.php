<?php
include_once '../koneksi.php';
include "../cek_akses.php";
$conn = $koneksi; // sinkronisasi agar tetap kompatibel

// ===============================
// SIMPAN DATA KE DATABASE
// ===============================
if (isset($_POST['submit'])) {
    // Ambil data
    $tanggal = $_POST['tanggal'];
    $nama = $_POST['nama'];
    $penyelenggara = $_POST['penyelenggara'];
    $peserta = $_POST['peserta'];
    $jumlah = $_POST['jumlah'];
    $keterangan = $_POST['keterangan'];

    // Format tanggal jadi YYYY-MM-DD
    $tanggal_baru = date('Y-m-d', strtotime($tanggal));

    // Upload lampiran
    $lampiran = '';
    if (!empty($_FILES['lampiran']['name'][0])) {
        $uploadDir = '../uploads/pelatihan_terlaksana/';
        if (!file_exists($uploadDir)) mkdir($uploadDir, 0777, true);

        $lampiranArray = [];
        foreach ($_FILES['lampiran']['name'] as $key => $filename) {
            $tmpName = $_FILES['lampiran']['tmp_name'][$key];
            $filePath = $uploadDir . basename($filename);
            if (move_uploaded_file($tmpName, $filePath)) {
                $lampiranArray[] = $filename;
            }
        }
        $lampiran = implode(', ', $lampiranArray);
    }

    // Simpan data ke DB
    $query = "INSERT INTO tb_pelatihan_terlaksana 
              (tanggal, nama, penyelenggara, peserta, jumlah, keterangan, lampiran)
              VALUES ('$tanggal_baru', '$nama', '$penyelenggara', '$peserta', '$jumlah', '$keterangan', '$lampiran')";

    if (mysqli_query($conn, $query)) {
        echo "<script>
            alert('✅ Data pelatihan berhasil disimpan!');
            window.location.href='./pelatihan_terlaksana.php';
        </script>";
        exit; // 🔹 penting agar tidak looping ulang halaman
    } else {
        echo '<pre style="color:white;background:black;padding:20px;">';
        echo '❌ Query Error: ' . mysqli_error($conn) . "\n";
        echo 'Query: ' . $query;
        echo '</pre>';
        exit;
    }
}

// ===============================
// HAPUS DATA
// ===============================
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $hapus = mysqli_query($conn, "DELETE FROM tb_pelatihan_terlaksana WHERE id='$id'");

    if ($hapus) {
        echo "<script>
            alert('🗑️ Data berhasil dihapus!');
            window.location.href='./pelatihan_terlaksana.php';
        </script>";
        exit;
    } else {
        echo "<script>
            alert('❌ Gagal menghapus data!');
            window.history.back();
        </script>";
        exit;
    }
}
?>


<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Pelatihan Terlaksana | PPI PHBW</title>
<style>
:root {
  --primary: #1a2a80;
  --secondary: #3b49df;
  --bg: #f7f8ff;
  --card: #ffffff;
  --border: #dce0f0;
}
body {
  font-family: "Segoe UI", sans-serif;
  background-color: var(--bg);
  color: #222;
  margin: 0;
}
header {
  background-color: var(--primary);
  color: white;
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 15px 30px;
  font-size: 1.3em;
  font-weight: bold;
}
.dashboard-btn {
  background-color: var(--secondary);
  color: white;
  border: none;
  padding: 8px 16px;
  border-radius: 8px;
  font-size: 0.9em;
  cursor: pointer;
  transition: background 0.2s;
}
.dashboard-btn:hover {
  background-color: #2832b8;
}
nav {
  display: flex;
  justify-content: center;
  background-color: var(--secondary);
  flex-wrap: wrap;
}
nav button {
  background: none;
  color: white;
  border: none;
  padding: 12px 20px;
  font-size: 1em;
  cursor: pointer;
  transition: background 0.2s;
}
nav button:hover,
nav button.active {
  background-color: #16225a;
}
main {
  max-width: 1000px;
  margin: 30px auto;
  background: var(--card);
  border-radius: 12px;
  padding: 25px 30px;
  box-shadow: 0 4px 10px rgba(0,0,0,0.08);
  border: 1px solid var(--border);
}
h2 {
  color: var(--primary);
  border-bottom: 2px solid var(--secondary);
  padding-bottom: 5px;
  margin-top: 10px;
}
label {
  display: block;
  margin-top: 12px;
  font-weight: 600;
}
input, select, textarea {
  width: 100%;
  padding: 10px;
  border-radius: 8px;
  border: 1px solid var(--border);
  margin-top: 5px;
  box-sizing: border-box;
  font-size: 1em;
}
input[type="file"] {
  border: 2px dashed var(--border);
  background-color: #f5f7ff;
  padding: 10px;
}
.file-preview {
  margin-top: 10px;
  background: #f9faff;
  border-radius: 8px;
  padding: 10px;
  border: 1px solid var(--border);
  font-size: 0.9em;
}
.file-preview ul {
  list-style: none;
  padding: 0;
  margin: 0;
}
.file-preview li {
  margin-bottom: 5px;
  display: flex;
  align-items: center;
  gap: 6px;
}
.file-preview li span {
  color: var(--secondary);
  font-weight: 500;
}
button.save {
  margin-top: 20px;
  background-color: var(--secondary);
  color: white;
  padding: 10px 18px;
  border: none;
  border-radius: 8px;
  font-size: 1em;
  font-weight: bold;
  cursor: pointer;
}
button.save:hover {
  background-color: #2f3cbf;
}
table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 15px;
}
th, td {
  border: 1px solid var(--border);
  padding: 10px;
  text-align: left;
  vertical-align: top;
}
th {
  background-color: var(--primary);
  color: white;
  text-align: center;
}
tr:nth-child(even) {
  background-color: #f1f4ff;
}
.delete-btn {
  background-color: #d93025;
  color: white;
  border: none;
  border-radius: 5px;
  padding: 5px 10px;
  cursor: pointer;
}
footer {
  text-align: center;
  padding: 20px;
  font-size: 0.9em;
  color: gray;
}
.tab { display: none; }
.tab.active { display: block; }

/* Responsif */
@media (max-width: 768px) {
  main { padding: 15px; width: 95%; }
  table { display: block; overflow-x: auto; white-space: nowrap; }
  th, td { font-size: 0.85em; padding: 6px; }
  h2 { font-size: 1.1em; }
  button.save, .dashboard-btn, nav button {
    font-size: 0.85em; padding: 8px 10px;
  }
}
</style>
</head>

<body>
<header>
  <div>🎓 Pelatihan Terlaksana | PPI PHBW</div>
  <button class="dashboard-btn" onclick="kembaliDashboard()">🏠 Kembali ke Dashboard</button>
</header>

<nav>
  <button class="active" onclick="showTab('input')">🧾 Input Pelatihan</button>
  <button onclick="showTab('rekap')">📋 Rekap Pelatihan</button>
</nav>

<main>
  <!-- TAB INPUT -->
  <div id="input" class="tab active">
    <h2>🧾 Form Input Data Pelatihan Terlaksana</h2>
    <form method="POST" enctype="multipart/form-data">
      <label>Tanggal Pelatihan</label>
      <input type="date" name="tanggal" required>

      <label>Nama Pelatihan</label>
      <input type="text" name="nama" placeholder="Contoh: Pelatihan Hand Hygiene" required>

      <label>Penyelenggara</label>
      <input type="text" name="penyelenggara" placeholder="Contoh: Komite PPI RS PHBW" required>

      <label>Peserta (Unit/Bagian)</label>
      <input type="text" name="peserta" placeholder="Contoh: Perawat Ruang Rawat Inap, CSSD, dll" required>

      <label>Jumlah Peserta</label>
      <input type="number" name="jumlah" min="1" placeholder="Masukkan jumlah peserta" required>

      <label>Keterangan Tambahan</label>
      <textarea name="keterangan" rows="3" placeholder="Opsional: isi materi, narasumber, atau catatan lainnya"></textarea>

      <label>📎 Lampiran (Foto / PDF)</label>
      <input type="file" id="lampiran" name="lampiran[]" multiple accept=".jpg,.jpeg,.png,.pdf">
      <div class="file-preview" id="preview"></div>

      <button type="submit" class="save" name="submit">💾 Simpan Data</button>
    </form>
  </div>

  <!-- TAB REKAP -->
  <div id="rekap" class="tab">
    <h2>📋 Daftar Pelatihan Terlaksana</h2>
    <table>
      <thead>
        <tr>
          <th>No</th>
          <th>Tanggal</th>
          <th>Nama Pelatihan</th>
          <th>Penyelenggara</th>
          <th>Peserta</th>
          <th>Jumlah</th>
          <th>Keterangan</th>
          <th>Lampiran</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $no = 1;
        $result = mysqli_query($conn, "SELECT * FROM tb_pelatihan_terlaksana ORDER BY tanggal DESC");
        if (mysqli_num_rows($result) > 0) {
          while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>
              <td>{$no}</td>
              <td>{$row['tanggal']}</td>
              <td>{$row['nama']}</td>
              <td>{$row['penyelenggara']}</td>
              <td>{$row['peserta']}</td>
              <td>{$row['jumlah']}</td>
              <td>{$row['keterangan']}</td>
              <td>";
              if ($row['lampiran'] != '') {
                $files = explode(', ', $row['lampiran']);
                foreach ($files as $file) {
                  echo "<a href='../uploads/pelatihan_terlaksana/$file' target='_blank'>📄 $file</a><br>";
                }
              } else { echo "-"; }
              echo "</td>
              <td><button class='delete-btn' onclick=\"hapusData({$row['id']})\">🗑️</button></td>
            </tr>";
            $no++;
          }
        } else {
          echo "<tr><td colspan='9' style='text-align:center;'>Belum ada data pelatihan</td></tr>";
        }
        ?>
      </tbody>
    </table>
  </div>
</main>

<footer>© 2025 PPI PHBW — Pelatihan Terlaksana</footer>

<script>
function showTab(tabId){
  document.querySelectorAll('nav button').forEach(btn=>btn.classList.remove('active'));
  document.querySelectorAll('.tab').forEach(tab=>tab.classList.remove('active'));
  document.querySelector(`nav button[onclick="showTab('${tabId}')"]`).classList.add('active');
  document.getElementById(tabId).classList.add('active');
}

document.getElementById("lampiran").addEventListener("change", function() {
  const preview = document.getElementById("preview");
  preview.innerHTML = "";
  if (this.files.length > 0) {
    const ul = document.createElement("ul");
    for (const file of this.files) {
      const li = document.createElement("li");
      li.innerHTML = `📎 <span>${file.name}</span>`;
      ul.appendChild(li);
    }
    preview.appendChild(ul);
  }
});

function hapusData(id){
  if(confirm('Apakah Anda yakin ingin menghapus data ini?')){
    window.location.href='?hapus='+id;
  }
}

function kembaliDashboard(){
  window.location.href='/dashboard.php';
}
</script>
</body>
</html>
