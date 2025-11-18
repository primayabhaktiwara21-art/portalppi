<?php
include_once '../koneksi.php';
include "../cek_akses.php";
$conn = $koneksi; // sinkronisasi koneksi

// ===============================
// SIMPAN DATA KE DATABASE
// ===============================
if (isset($_POST['submit'])) {
    $tanggal = $_POST['tanggal'];
    $pelatihan = $_POST['pelatihan'];
    $peserta = $_POST['peserta'];
    $unit = $_POST['unit'];

    $tanggal_baru = date('Y-m-d', strtotime($tanggal));

    // Upload sertifikat
    $uploadDir = '../uploads/sertifikat/';
    if (!file_exists($uploadDir)) mkdir($uploadDir, 0777, true);

    $sertifikatFiles = [];
    if (!empty($_FILES['sertifikat']['name'][0])) {
        foreach ($_FILES['sertifikat']['name'] as $key => $fileName) {
            $tmp = $_FILES['sertifikat']['tmp_name'][$key];
            $filePath = $uploadDir . basename($fileName);
            if (move_uploaded_file($tmp, $filePath)) {
                $sertifikatFiles[] = $fileName;
            }
        }
    }
    $sertifikat = implode(', ', $sertifikatFiles);

    $query = "INSERT INTO tb_sertifikat (tanggal, pelatihan, peserta, unit, sertifikat)
              VALUES ('$tanggal_baru', '$pelatihan', '$peserta', '$unit', '$sertifikat')";

    if (mysqli_query($conn, $query)) {
        echo "<script>
            alert('✅ Sertifikat berhasil disimpan!');
            window.location.href='sertifikat.php';
        </script>";
        exit;
    } else {
        echo '❌ Gagal menyimpan: ' . mysqli_error($conn);
        exit;
    }
}

// ===============================
// HAPUS DATA
// ===============================
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];

    // hapus file di folder
    $q = mysqli_query($conn, "SELECT sertifikat FROM tb_sertifikat WHERE id='$id'");
    if ($r = mysqli_fetch_assoc($q)) {
        $files = explode(', ', $r['sertifikat']);
        foreach ($files as $f) {
            $path = '../uploads/sertifikat/' . $f;
            if (file_exists($path)) unlink($path);
        }
    }

    mysqli_query($conn, "DELETE FROM tb_sertifikat WHERE id='$id'");
    echo "<script>alert('🗑️ Sertifikat berhasil dihapus!'); window.location.href='sertifikat.php';</script>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Penyimpanan Sertifikat Pelatihan | PPI PHBW</title>
  <style>
    :root {
      --primary: #1a2a80;
      --secondary: #3b49df;
      --bg: #f7f8ff;
      --card: #ffffff;
      --border: #dce0f0;
    }
    body { font-family: "Segoe UI", sans-serif; background: var(--bg); color: #222; margin: 0; }
    header { background: var(--primary); color: white; display: flex; justify-content: space-between; align-items: center; padding: 15px 30px; font-size: 1.3em; font-weight: bold; }
    .dashboard-btn { background: var(--secondary); color: white; border: none; padding: 8px 16px; border-radius: 8px; font-size: 0.9em; cursor: pointer; transition: background 0.2s; }
    .dashboard-btn:hover { background-color: #2832b8; }
    nav { display: flex; justify-content: center; background: var(--secondary); flex-wrap: wrap; }
    nav button { background: none; color: white; border: none; padding: 12px 20px; font-size: 1em; cursor: pointer; transition: background 0.2s; }
    nav button:hover, nav button.active { background-color: #16225a; }
    main { max-width: 1000px; margin: 30px auto; background: var(--card); border-radius: 12px; padding: 25px 30px; box-shadow: 0 4px 10px rgba(0,0,0,0.08); border: 1px solid var(--border); }
    h2 { color: var(--primary); border-bottom: 2px solid var(--secondary); padding-bottom: 5px; margin-top: 10px; }
    label { display: block; margin-top: 12px; font-weight: 600; }
    input, textarea { width: 100%; padding: 10px; border-radius: 8px; border: 1px solid var(--border); margin-top: 5px; box-sizing: border-box; font-size: 1em; }
    input[type=file] { border: 2px dashed var(--border); background-color: #f5f7ff; padding: 10px; }
    .file-preview { margin-top: 10px; background: #f9faff; border-radius: 8px; padding: 10px; border: 1px solid var(--border); font-size: 0.9em; }
    button.save { margin-top: 20px; background: var(--secondary); color: white; padding: 10px 18px; border: none; border-radius: 8px; font-size: 1em; font-weight: bold; cursor: pointer; }
    button.save:hover { background-color: #2f3cbf; }
    table { width: 100%; border-collapse: collapse; margin-top: 15px; }
    th, td { border: 1px solid var(--border); padding: 10px; text-align: center; vertical-align: top; }
    th { background: var(--primary); color: white; }
    tr:nth-child(even) { background-color: #f1f4ff; }
    .delete-btn { background: #d93025; color: white; border: none; border-radius: 5px; padding: 6px 10px; cursor: pointer; }
    .delete-btn:hover { background: #b32118; }
    footer { text-align: center; padding: 20px; font-size: 0.9em; color: gray; }
    .tab { display: none; } .tab.active { display: block; }
  </style>
</head>
<body>
<header>
  <div>🏅 Penyimpanan Sertifikat Pelatihan | PPI PHBW</div>
  <button class="dashboard-btn" onclick="window.location.href='/dashboard.php'">🏠 Kembali ke Dashboard</button>
</header>

<nav>
  <button class="active" onclick="showTab('input')">🧾 Input Sertifikat</button>
  <button onclick="showTab('rekap')">📋 Rekap Sertifikat</button>
</nav>

<main>
  <!-- TAB INPUT -->
  <div id="input" class="tab active">
    <h2>🧾 Form Input Sertifikat Pelatihan</h2>
    <form method="POST" enctype="multipart/form-data">
      <label>Tanggal Pelatihan</label>
      <input type="date" name="tanggal" required>

      <label>Nama Pelatihan</label>
      <input type="text" name="pelatihan" required placeholder="Contoh: Pelatihan Hand Hygiene">

      <label>Nama Peserta</label>
      <input type="text" name="peserta" required placeholder="Contoh: Siti Rahma, A.Md.Kep">

      <label>Unit / Bagian</label>
      <input type="text" name="unit" required placeholder="Contoh: Ruang Rawat Inap, ICU">

      <label>Upload Sertifikat (PDF / Gambar)</label>
      <input type="file" name="sertifikat[]" multiple accept=".jpg,.jpeg,.png,.pdf" required>

      <button type="submit" class="save" name="submit">💾 Simpan Sertifikat</button>
    </form>
  </div>

  <!-- TAB REKAP -->
  <div id="rekap" class="tab">
    <h2>📋 Rekap Sertifikat Pelatihan</h2>
    <table>
      <thead>
        <tr>
          <th>No</th>
          <th>Tanggal</th>
          <th>Nama Pelatihan</th>
          <th>Peserta</th>
          <th>Unit</th>
          <th>Berkas Sertifikat</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $no = 1;
        $result = mysqli_query($conn, "SELECT * FROM tb_sertifikat ORDER BY tanggal DESC");
        if (mysqli_num_rows($result) > 0) {
          while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>
              <td>{$no}</td>
              <td>{$row['tanggal']}</td>
              <td>{$row['pelatihan']}</td>
              <td>{$row['peserta']}</td>
              <td>{$row['unit']}</td>
              <td>";
                if ($row['sertifikat'] != '') {
                  $files = explode(', ', $row['sertifikat']);
                  foreach ($files as $f) {
                    echo "<a href='../uploads/sertifikat/$f' target='_blank'>📄 Lihat</a><br>";
                  }
                } else {
                  echo '-';
                }
              echo "</td>
              <td><button class='delete-btn' onclick=\"hapusData({$row['id']})\">🗑️ Hapus</button></td>
            </tr>";
            $no++;
          }
        } else {
          echo "<tr><td colspan='7'>Belum ada data sertifikat</td></tr>";
        }
        ?>
      </tbody>
    </table>
  </div>
</main>

<footer>© 2025 PPI PHBW — Penyimpanan Sertifikat Pelatihan</footer>

<script>
  function showTab(tabId) {
    document.querySelectorAll('nav button').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
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
