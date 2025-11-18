<?php
include_once '../koneksi.php';
include "../cek_akses.php";
$conn = $koneksi;

// ===============================
// SIMPAN DATA
// ===============================
if (isset($_POST['submit'])) {
  $judul = $_POST['judul'];
  $jenis = $_POST['jenis'];
  $tahun = $_POST['tahun'];
  $sumber = $_POST['sumber'];
  $berkas = '';

  // Upload file jika ada
  if (!empty($_FILES['berkas']['name'])) {
    $uploadDir = '../uploads/referensi/';
    if (!file_exists($uploadDir)) mkdir($uploadDir, 0777, true);

    $filename = time() . '_' . preg_replace("/[^a-zA-Z0-9_\.-]/", "_", $_FILES['berkas']['name']);
    $filePath = $uploadDir . $filename;
    move_uploaded_file($_FILES['berkas']['tmp_name'], $filePath);
    $berkas = $filename;
  } else {
    // jika user isi link
    $berkas = $_POST['link'] ?? '';
  }

  $query = "INSERT INTO tb_referensi (judul, jenis, tahun, sumber, berkas)
            VALUES ('$judul','$jenis','$tahun','$sumber','$berkas')";
  mysqli_query($conn, $query);
  echo "<script>alert('✅ Referensi berhasil disimpan!'); window.location.href='referensi.php';</script>";
  exit;
}

// ===============================
// HAPUS DATA
// ===============================
if (isset($_GET['hapus'])) {
  $id = $_GET['hapus'];
  $data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_referensi WHERE id='$id'"));
  if ($data && file_exists("../uploads/referensi/" . $data['berkas'])) {
    unlink("../uploads/referensi/" . $data['berkas']);
  }
  mysqli_query($conn, "DELETE FROM tb_referensi WHERE id='$id'");
  echo "<script>alert('🗑️ Referensi berhasil dihapus!'); window.location.href='referensi.php';</script>";
  exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Referensi | PPI PHBW</title>
  <style>
    :root{
      --brand:#006cb7; --brand-dark:#0d3b66; --bg:#f6f8fc; --card:#ffffff;
      --line:#e7edf3; --ink:#0f172a; --muted:#607085; --shadow:0 8px 24px rgba(2,6,23,.06);
    }
    body{margin:0;font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;background:var(--bg);color:var(--ink);}
    .topbar{background:#fff;padding:14px 24px;display:flex;justify-content:space-between;align-items:center;
      box-shadow:0 2px 6px rgba(0,0,0,.04);}
    .topbar h2{color:var(--brand-dark);margin:0;display:flex;align-items:center;gap:8px;font-weight:700;}
    a.btn{background:var(--brand);color:#fff;padding:10px 16px;border-radius:8px;text-decoration:none;font-weight:600;}
    a.btn:hover{background:var(--brand-dark);}
    .container{max-width:1100px;margin:0 auto;padding:24px;}
    h1{color:var(--brand-dark);}
    .subtitle{color:var(--muted);}
    .filter{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:16px;}
    input[type="text"], select{padding:8px;border:1px solid var(--line);border-radius:8px;min-width:220px;}
    .table-container{box-shadow:var(--shadow);border-radius:12px;overflow-x:auto;}
    table{width:100%;border-collapse:collapse;font-size:.95rem;}
    th,td {
  padding:6px 10px; /* dikurangi dari 12px menjadi 6px */
  border-bottom:1px solid var(--line);
  line-height:1.2;  /* menambah kerapatan teks */
}
table {
  width:100%;
  border-collapse:collapse;
  font-size:0.9rem; /* sedikit kecil agar lebih padat */
}

    th{background:linear-gradient(180deg,#1e3a8a 0%,#325da8 60%,#447cc3 100%);color:#fff;text-align:left;}
    button{border:none;padding:6px 10px;border-radius:8px;cursor:pointer;font-weight:600;}
    button.delete{background:#fee2e2;color:#b91c1c;} button.delete:hover{background:#fecaca;}
    button.add{background:var(--brand);color:#fff;} button.add:hover{background:var(--brand-dark);}
    .form-modal{display:none;position:fixed;inset:0;background:rgba(0,0,0,0.45);align-items:center;justify-content:center;z-index:50;}
    .form-box{background:#fff;border-radius:12px;padding:24px;width:90%;max-width:450px;box-shadow:var(--shadow);}
    .form-box input,.form-box select{width:100%;margin-bottom:12px;padding:8px;border:1px solid var(--line);border-radius:8px;}
    .btn-group{display:flex;justify-content:flex-end;gap:8px;}
  </style>
</head>

<body>
  <div class="topbar">
    <h2>📚 Referensi</h2>
    <a href="/dashboard.php" class="btn">🏠 Kembali ke Dashboard</a>
  </div>

  <div class="container">
    <h1>Daftar Referensi Rumah Sakit</h1>
    <p class="subtitle">Kumpulan dokumen acuan, pedoman, peraturan, dan sumber ilmiah yang digunakan dalam kegiatan PPI di RS Primaya Bhakti Wara.</p>

    <div class="filter">
      <input type="text" placeholder="🔍 Cari berdasarkan judul atau jenis referensi..." id="searchInput">
      <button class="add" id="openForm">+ Tambah Referensi</button>
    </div>

    <div class="table-container">
      <table id="refTable">
        <thead>
          <tr>
            <th>No</th>
            <th>Judul Referensi</th>
            <th>Jenis</th>
            <th>Tahun</th>
            <th>Sumber / Penerbit</th>
            <th>Berkas</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $no = 1;
          $result = mysqli_query($conn, "SELECT * FROM tb_referensi ORDER BY id DESC");
          if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
              echo "<tr>
                <td>{$no}</td>
                <td>{$row['judul']}</td>
                <td>{$row['jenis']}</td>
                <td>{$row['tahun']}</td>
                <td>{$row['sumber']}</td>
                <td>";
              if (preg_match('/^https?:\/\//', $row['berkas'])) {
                echo "<a href='{$row['berkas']}' target='_blank' style='color:#006cb7;font-weight:600;'>📄 Lihat</a>";
              } else {
                echo "<a href='../uploads/referensi/{$row['berkas']}' target='_blank' style='color:#006cb7;font-weight:600;'>📄 Lihat</a>";
              }
              echo "</td>
                <td><button class='delete' onclick=\"hapusData({$row['id']})\">Hapus</button></td>
              </tr>";
              $no++;
            }
          } else {
            echo "<tr><td colspan='7' style='text-align:center;'>Belum ada data referensi</td></tr>";
          }
          ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- FORM TAMBAH -->
  <div class="form-modal" id="formModal">
    <div class="form-box">
      <h3>Tambah Referensi Baru</h3>
      <form method="POST" enctype="multipart/form-data">
        <label>Judul Referensi</label>
        <input type="text" name="judul" required>

        <label>Jenis Referensi</label>
        <select name="jenis" required>
          <option value="">Pilih...</option>
          <option>Panduan</option>
          <option>Pedoman</option>
          <option>Buku</option>
          <option>Peraturan</option>
          <option>Artikel Ilmiah</option>
        </select>

        <label>Tahun Terbit</label>
        <input type="number" name="tahun" min="1900" max="2100" required>

        <label>Sumber / Penerbit</label>
        <input type="text" name="sumber" required>

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
      if (confirm('Apakah Anda yakin ingin menghapus referensi ini?')) {
        window.location.href = '?hapus=' + id;
      }
    }

    // pencarian cepat
    const searchInput = document.getElementById('searchInput');
    searchInput.addEventListener('keyup', () => {
      const term = searchInput.value.toLowerCase();
      document.querySelectorAll('#refTable tbody tr').forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(term) ? '' : 'none';
      });
    });
  </script>
</body>
</html>
