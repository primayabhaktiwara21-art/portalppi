<?php
include_once '../koneksi.php';
include "../cek_akses.php";
$conn = $koneksi;

// ===============================
// SIMPAN DATA KE DATABASE
// ===============================
if (isset($_POST['submit'])) {
    $tanggal = $_POST['tanggal'];
    $pelatihan = $_POST['pelatihan'];
    $jenis = $_POST['jenis'];
    $penyelenggara = $_POST['penyelenggara'];
    $tanggal_baru = date('Y-m-d', strtotime($tanggal));

    // Upload file
    $uploadDir = '../uploads/materi/';
    if (!file_exists($uploadDir)) mkdir($uploadDir, 0777, true);

    $materiArray = [];
    if (!empty($_FILES['materi']['name'][0])) {
        foreach ($_FILES['materi']['name'] as $key => $filename) {
            $tmpName = $_FILES['materi']['tmp_name'][$key];
            $safeFilename = time() . '_' . preg_replace("/[^a-zA-Z0-9_\.-]/", "_", basename($filename));
            $filePath = $uploadDir . $safeFilename;

            if (move_uploaded_file($tmpName, $filePath)) {
                $materiArray[] = $safeFilename;
            }
        }
    }

    $file_materi = implode(', ', $materiArray);

    $query = "INSERT INTO tb_materi (tanggal, pelatihan, jenis, penyelenggara, file_materi)
              VALUES ('$tanggal_baru', '$pelatihan', '$jenis', '$penyelenggara', '$file_materi')";

    if (mysqli_query($conn, $query)) {
        echo "<script>
            alert('✅ Materi pelatihan berhasil disimpan!');
            window.location.href='materi.php';
        </script>";
        exit;
    } else {
        echo '<pre style="color:white;background:black;padding:20px;">';
        echo '❌ Gagal menyimpan data: ' . mysqli_error($conn) . "\n";
        echo 'Query: ' . $query;
        echo '</pre>';
        exit;
    }
}

// ===============================
// HAPUS DATA DARI DATABASE
// ===============================
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $q = mysqli_query($conn, "SELECT file_materi FROM tb_materi WHERE id='$id'");
    if ($r = mysqli_fetch_assoc($q)) {
        $files = explode(', ', $r['file_materi']);
        foreach ($files as $f) {
            $path = '../uploads/materi/' . $f;
            if (file_exists($path)) unlink($path);
        }
    }

    mysqli_query($conn, "DELETE FROM tb_materi WHERE id='$id'");
    echo "<script>
        alert('🗑️ Data materi berhasil dihapus!');
        window.location.href='materi.php';
    </script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>📚 Materi Pelatihan | PPI PHBW</title>
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
}
.dashboard-btn:hover { background-color: #2832b8; }

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
}
nav button:hover, nav button.active { background-color: #16225a; }

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
}
label {
  display: block;
  margin-top: 12px;
  font-weight: 600;
}
input, select {
  width: 100%;
  padding: 10px;
  border-radius: 8px;
  border: 1px solid var(--border);
  margin-top: 5px;
}
input[type=file] {
  border: 2px dashed var(--border);
  background-color: #f5f7ff;
  padding: 10px;
}
button.save {
  margin-top: 20px;
  background-color: var(--secondary);
  color: white;
  padding: 10px 18px;
  border: none;
  border-radius: 8px;
  font-weight: bold;
  cursor: pointer;
}
button.save:hover { background-color: #2f3cbf; }

table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 15px;
}
th, td {
  border: 1px solid var(--border);
  padding: 10px;
  text-align: center;
  vertical-align: top;
}
th {
  background-color: var(--primary);
  color: white;
}
tr:nth-child(even) { background-color: #f1f4ff; }

.delete-btn {
  background-color: #d93025;
  color: white;
  border: none;
  border-radius: 5px;
  padding: 5px 10px;
  cursor: pointer;
}
.delete-btn:hover { background-color: #b32118; }

footer {
  text-align: center;
  padding: 20px;
  font-size: 0.9em;
  color: gray;
}
.tab { display: none; }
.tab.active { display: block; }

@media (max-width: 768px) {
  main { width: 95%; padding: 15px; }
  th, td { font-size: 0.85em; padding: 6px; }
}
</style>
</head>

<body>
<header>
  <div>📚 Penyimpanan Materi Pelatihan | PPI PHBW</div>
  <button class="dashboard-btn" onclick="window.location.href='/dashboard.php'">🏠 Kembali ke Dashboard</button>
</header>

<nav>
  <button class="active" onclick="showTab('input')">🧾 Input Materi</button>
  <button onclick="showTab('rekap')">📋 Rekap Materi</button>
</nav>

<main>
  <!-- TAB INPUT -->
  <div id="input" class="tab active">
    <h2>🧾 Form Input Materi Pelatihan</h2>
    <form method="POST" enctype="multipart/form-data">
      <label>Tanggal Upload</label>
      <input type="date" name="tanggal" required>

      <label>Nama Pelatihan</label>
      <input type="text" name="pelatihan" required>

      <label>Jenis Materi</label>
      <select name="jenis" required>
        <option value="">Pilih Jenis</option>
        <option>Modul / Panduan</option>
        <option>Slide Presentasi</option>
        <option>Video Pelatihan</option>
        <option>Poster / Brosur</option>
        <option>Lainnya</option>
      </select>

      <label>Penyelenggara</label>
      <input type="text" name="penyelenggara" required>

      <label>Upload File Materi</label>
      <input type="file" name="materi[]" multiple required accept=".pdf,.ppt,.pptx,.mp4,.jpg,.jpeg,.png">

      <button type="submit" class="save" name="submit">💾 Simpan Materi</button>
    </form>
  </div>

  <!-- TAB REKAP -->
  <div id="rekap" class="tab">
    <h2>📋 Rekap Materi Pelatihan</h2>
    <table>
      <thead>
        <tr>
          <th>No</th>
          <th>Tanggal</th>
          <th>Nama Pelatihan</th>
          <th>Jenis</th>
          <th>Penyelenggara</th>
          <th>File Materi</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $result = mysqli_query($conn, "SELECT * FROM tb_materi ORDER BY tanggal DESC");
        if (mysqli_num_rows($result) > 0) {
          $no = 1;
          while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>
              <td>{$no}</td>
              <td>{$row['tanggal']}</td>
              <td>{$row['pelatihan']}</td>
              <td>{$row['jenis']}</td>
              <td>{$row['penyelenggara']}</td>
              <td>";
              if (!empty($row['file_materi'])) {
                $files = explode(', ', $row['file_materi']);
                foreach ($files as $f) {
                  echo "<a href='../uploads/materi/$f' target='_blank'>📄 $f</a><br>";
                }
              } else {
                echo "-";
              }
              echo "</td>
              <td><button class='delete-btn' onclick=\"hapusData({$row['id']})\">🗑️</button></td>
            </tr>";
            $no++;
          }
        } else {
          echo "<tr><td colspan='7' style='text-align:center;'>Belum ada data materi</td></tr>";
        }
        ?>
      </tbody>
    </table>
  </div>
</main>

<footer>© 2025 PPI PHBW — Materi Pelatihan</footer>

<script>
function showTab(tabId) {
  document.querySelectorAll('nav button').forEach(btn => btn.classList.remove('active'));
  document.querySelectorAll('.tab').forEach(tab => tab.classList.remove('active'));
  document.querySelector(`nav button[onclick="showTab('${tabId}')"]`).classList.add('active');
  document.getElementById(tabId).classList.add('active');
}

function hapusData(id) {
  if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
    window.location.href = '?hapus=' + id;
  }
}
</script>
</body>
</html>
