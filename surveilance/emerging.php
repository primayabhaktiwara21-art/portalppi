<?php
include_once '../koneksi.php';
include "../cek_akses.php";

// ====== SIMPAN DATA ======
if (isset($_POST['action']) && $_POST['action'] == 'save') {
    $tahun = $_POST['tahun'];
    $bulan = $_POST['bulan'];
    $jenis = $_POST['jenis'];
    $unit = $_POST['unit'];
    $numerator = $_POST['numerator'];
    $denominator = $_POST['denominator'];
    $hasil = $_POST['hasil'];
    $satuan = $_POST['satuan'];

    $sql = "INSERT INTO tb_emerging (tahun, bulan, jenis, unit, numerator, denominator, hasil, satuan)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssidds", $tahun, $bulan, $jenis, $unit, $numerator, $denominator, $hasil, $satuan);
    $stmt->execute();
    header("Location: ".$_SERVER['PHP_SELF']);
exit;

}

// ====== HAPUS DATA ======
if (isset($_POST['action']) && $_POST['action'] == 'delete') {
    $id = $_POST['id'];
    $stmt = $conn->prepare("DELETE FROM tb_emerging WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}


// ====== TAMPILKAN DATA ======
$emerging = $conn->query("SELECT * FROM tb_emerging WHERE jenis='Emerging' ORDER BY id DESC");
$purulen = $conn->query("SELECT * FROM tb_emerging WHERE jenis='Purulen' ORDER BY id DESC");
?>




<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Surveilans Infeksi Emerging & Purulen | PPI PHBW</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

  <style>
    :root {
      --primary: #1a2a80;
      --secondary: #3b49df;
      --accent: #5b6ef5;
      --background: #f4f7ff; /* 🌤 lembut cerah */
      --card: #ffffff;
      --border: #dee3f5;
      --text: #2c2c2c;
      --shadow: 0 4px 12px rgba(0,0,0,0.08);
    }

    body {
      font-family: 'Poppins', sans-serif;
      margin: 0;
      background: var(--background);
      color: var(--text);
      line-height: 1.6;
    }

    /* Header */
    header {
      background: linear-gradient(135deg, var(--primary), var(--secondary));
      color: white;
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 15px 25px;
      flex-wrap: wrap;
      box-shadow: var(--shadow);
    }

    header div {
      font-weight: 600;
      font-size: 1.1rem;
    }

    .dashboard-btn {
      background: linear-gradient(135deg, #3b49df, #2531a1);
      color: white;
      border: none;
      padding: 8px 16px;
      border-radius: 8px;
      font-size: 0.9rem;
      cursor: pointer;
      transition: all 0.2s ease;
      box-shadow: 0 2px 8px rgba(59,73,223,0.3);
    }

    .dashboard-btn:hover {
      transform: translateY(-2px);
    }

    /* Navigation Tabs */
    nav {
      display: flex;
      justify-content: center;
      background: var(--secondary);
      flex-wrap: wrap;
      box-shadow: inset 0 -1px rgba(255,255,255,0.2);
    }

    nav button {
      background: transparent;
      color: white;
      border: none;
      padding: 12px 22px;
      font-size: 1rem;
      cursor: pointer;
      transition: all 0.25s ease;
      border-bottom: 3px solid transparent;
    }

    nav button:hover {
      background: rgba(0,0,0,0.15);
    }

    nav button.active {
      background: rgba(255,255,255,0.1);
      border-bottom: 3px solid #fff;
      font-weight: 600;
    }

    /* Main Content */
    main {
      max-width: 1100px;
      margin: 25px auto;
      background: var(--card);
      border-radius: 14px;
      padding: 25px 30px;
      box-shadow: var(--shadow);
      border: 1px solid var(--border);
    }

    h2 {
      color: var(--primary);
      border-bottom: 2px solid var(--secondary);
      padding-bottom: 6px;
      margin-top: 10px;
      font-size: 1.1rem;
    }

    label {
      display: block;
      margin-top: 14px;
      font-weight: 500;
    }

    select, input {
      width: 100%;
      padding: 10px 12px;
      border-radius: 8px;
      border: 1px solid var(--border);
      margin-top: 6px;
      box-sizing: border-box;
      font-size: 0.95rem;
      background-color: #f9faff;
      transition: all 0.2s ease;
    }

    select:focus, input:focus {
      outline: none;
      border-color: var(--accent);
      box-shadow: 0 0 6px rgba(91,110,245,0.3);
      background-color: #fff;
    }

    .save {
      margin-top: 22px;
      background: linear-gradient(135deg, var(--secondary), var(--primary));
      color: white;
      padding: 10px 18px;
      border: none;
      border-radius: 8px;
      font-size: 1rem;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.2s ease;
      width: 100%;
      box-shadow: 0 2px 10px rgba(59,73,223,0.3);
    }

    .save:hover {
      transform: translateY(-2px);
    }

    /* Table */
    .table-container {
      overflow-x: auto;
      margin-top: 15px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      min-width: 700px;
      border-radius: 8px;
      overflow: hidden;
    }

    th, td {
      padding: 10px 12px;
      border: 1px solid var(--border);
      text-align: center;
      font-size: 0.9rem;
    }

    th {
      background: var(--secondary);
      color: white;
      font-weight: 500;
    }

    tr:nth-child(even) {
      background-color: #f1f4ff;
    }

    .result {
      background: #edf3ff;
      border: 1px solid #c8d3fa;
      padding: 10px;
      border-radius: 8px;
      margin-top: 15px;
      color: var(--primary);
      font-weight: 600;
      text-align: center;
    }

    footer {
      text-align: center;
      padding: 20px;
      font-size: 0.9rem;
      color: #777;
    }

    .tab {
      display: none;
    }

    .tab.active {
      display: block;
      animation: fadeIn 0.3s ease;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }

    /* Responsive */
    @media (max-width: 768px) {
      header {
        flex-direction: column;
        text-align: center;
        gap: 10px;
      }

      main {
        padding: 18px;
        margin: 15px;
      }

      nav button {
        padding: 10px;
        font-size: 0.9rem;
      }

      table {
        font-size: 0.8rem;
      }
    }
  </style>
</head>

<body>
  <header>
    <div>🦠 Surveilans Infeksi Emerging & Purulen | PPI PHBW</div>
    <button class="dashboard-btn" onclick="kembaliDashboard()">🏠 Kembali ke Dashboard</button>
  </header>

  <nav>
    <button class="active" onclick="showTab('input')">🧾 Input Data</button>
    <button onclick="showTab('Emerging')">🌍 Rekap Infeksi Emerging</button>
    <button onclick="showTab('Purulen')">💉 Rekap Infeksi Purulen</button>
  </nav>

  <main>
    <!-- TAB INPUT -->
    <div id="input" class="tab active">
      <h2>🧾 Form Input Surveilans Infeksi</h2>
          <form method="post">
  <input type="hidden" name="action" value="save">
        <label>Tahun</label>
        <input type="number" name="tahun" id="tahun" placeholder="Misal: 2025" min="2020" required>

        <label>Bulan</label>
        <select name="bulan" id="bulan" required>
          <option value="">Pilih Bulan</option>
          <option>Januari</option><option>Februari</option><option>Maret</option>
          <option>April</option><option>Mei</option><option>Juni</option>
          <option>Juli</option><option>Agustus</option><option>September</option>
          <option>Oktober</option><option>November</option><option>Desember</option>
        </select>

        <label>Jenis Surveilans</label>
        <select name="jenis" id="jenis" required>
          <option value="">Pilih Jenis</option>
          <option value="Emerging">Infeksi Emerging</option>
          <option value="Purulen">Infeksi Purulen</option>
        </select>

        <label>Nama Unit / Ruangan</label>
        <input type="text" name="unit" id="unit" placeholder="Contoh: IGD, ICU, Ruang Rawat" required>

        <label>Numerator (Kasus Infeksi Ditemukan)</label>
        <input type="number" name="numerator" id="num" min="0" required>

        <label>Denominator (Total Pasien / Sampel)</label>
        <input type="number" name="denominator" id="denum" min="1" required>

        <label>Jenis Hasil</label>
        <select name="tipeHasil" id="tipeHasil">
          <option value="persentase">Persentase (%)</option>
          <option value="permil">Permil (‰)</option>
        </select>

            <label>Hasil (otomatis)</label>
            <input type="text" name="hasil" id="hasil" readonly required>
            
            <input type="hidden" name="satuan" id="satuan" value="%">

            <button type="submit" class="save">💾 Simpan Data</button>


      </form>
    </div>

<!-- TAB EMERGING -->
<div id="Emerging" class="tab">
  <h2>🌍 Rekap Infeksi Emerging</h2>

  <!-- 🔹 Filter Tahun & Tombol Simpan PDF -->
  <div style="margin-bottom:15px; display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
    <label for="filterTahunEmerging">Filter Tahun:</label>
    <select id="filterTahunEmerging" onchange="filterTahun('Emerging')" style="padding:8px; border-radius:6px; border:1px solid #ccc;">
      <option value="semua">Semua Tahun</option>
      <?php
        $tahunList = $conn->query("SELECT DISTINCT tahun FROM tb_emerging WHERE jenis='Emerging' ORDER BY tahun DESC");
        while ($t = $tahunList->fetch_assoc()) {
          echo "<option value='{$t['tahun']}'>{$t['tahun']}</option>";
        }
      ?>
    </select>
    <button onclick="simpanPDF('Emerging')" style="background:#ff4d4d; color:white; border:none; padding:8px 14px; border-radius:6px; cursor:pointer;">
      📄 Simpan PDF
    </button>
  </div>

  <!-- 🔹 Tabel Emerging -->
  <div class="table-container" id="tableEmergingContainer">
    <table id="tableEmerging">
        <thead>
          <tr>
            <th>Tahun</th>
            <th>Bulan</th>
            <th>Unit</th>
            <th>Numerator</th>
            <th>Denominator</th>
            <th>Hasil</th>
            <th>Satuan</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
        <?php while ($row = $emerging->fetch_assoc()): ?>
        <tr>
          <td><?= $row['tahun'] ?></td>
          <td><?= $row['bulan'] ?></td>
          <td><?= $row['unit'] ?></td>
          <td><?= $row['numerator'] ?></td>
          <td><?= $row['denominator'] ?></td>
          <td><?= $row['hasil'] ?></td>
          <td><?= $row['satuan'] ?></td>
          <td>
            <form method="post" style="display:inline;">
              <input type="hidden" name="action" value="delete">
              <input type="hidden" name="id" value="<?= $row['id'] ?>">
              <button type="submit" class="delete-btn" onclick="return confirm('Yakin hapus data ini?')">🗑️</button>
            </form>
          </td>
        </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
  </div>
</div>


<!-- TAB PURULEN -->
<div id="Purulen" class="tab">
  <h2>💉 Rekap Infeksi Purulen</h2>

  <!-- 🔹 Filter Tahun & Tombol Simpan PDF -->
  <div style="margin-bottom:15px; display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
    <label for="filterTahunPurulen">Filter Tahun:</label>
    <select id="filterTahunPurulen" onchange="filterTahun('Purulen')" style="padding:8px; border-radius:6px; border:1px solid #ccc;">
      <option value="semua">Semua Tahun</option>
      <?php
        $tahunListPurulen = $conn->query("SELECT DISTINCT tahun FROM tb_emerging WHERE jenis='Purulen' ORDER BY tahun DESC");
        while ($tp = $tahunListPurulen->fetch_assoc()) {
          echo "<option value='{$tp['tahun']}'>{$tp['tahun']}</option>";
        }
      ?>
    </select>
    <button type="button" onclick="simpanPDF('Purulen')" style="background:#ff4d4d; color:white; border:none; padding:8px 14px; border-radius:6px; cursor:pointer;">
      📄 Simpan PDF
    </button>
  </div>

  <!-- 🔹 Tabel Purulen -->
  <div class="table-container" id="tablePurulenContainer">
    <table id="tablePurulen">
        <thead>
          <tr>
            <th>Tahun</th>
            <th>Bulan</th>
            <th>Unit</th>
            <th>Numerator</th>
            <th>Denominator</th>
            <th>Hasil</th>
            <th>Satuan</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
        <?php while ($row = $purulen->fetch_assoc()): ?>
        <tr>
          <td><?= $row['tahun'] ?></td>
          <td><?= $row['bulan'] ?></td>
          <td><?= $row['unit'] ?></td>
          <td><?= $row['numerator'] ?></td>
          <td><?= $row['denominator'] ?></td>
          <td><?= $row['hasil'] ?></td>
          <td><?= $row['satuan'] ?></td>
          <td>
            <form method="post" style="display:inline;">
              <input type="hidden" name="action" value="delete">
              <input type="hidden" name="id" value="<?= $row['id'] ?>">
              <button type="submit" class="delete-btn" onclick="return confirm('Yakin hapus data ini?')">🗑️</button>
            </form>
          </td>
        </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
  </div>
</div>

    
  </main>

  <footer>© 2025 PPI PHBW — Surveilans Infeksi Emerging & Purulen</footer>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<script>
// === Hitung hasil otomatis ===
document.getElementById('num').addEventListener('input', hitungHasil);
document.getElementById('denum').addEventListener('input', hitungHasil);
document.getElementById('tipeHasil').addEventListener('change', hitungHasil);

function hitungHasil() {
  const num = parseFloat(document.getElementById('num').value);
  const denum = parseFloat(document.getElementById('denum').value);
  const tipe = document.getElementById('tipeHasil').value;
  const hasilInput = document.getElementById('hasil');
  const satuanInput = document.getElementById('satuan');

  if (num > 0 && denum > 0) {
    const hasil = tipe === 'persentase' ? (num / denum) * 100 : (num / denum) * 1000;
    hasilInput.value = hasil.toFixed(2);
    satuanInput.value = tipe === 'persentase' ? '%' : '‰';
  } else {
    hasilInput.value = '';
  }
}

// === Navigasi antar tab ===
function showTab(id) {
  document.querySelectorAll('nav button').forEach(btn => btn.classList.remove('active'));
  document.querySelectorAll('.tab').forEach(tab => tab.classList.remove('active'));
  document.querySelector(`nav button[onclick="showTab('${id}')"]`).classList.add('active');
  document.getElementById(id).classList.add('active');
}

// === Tombol kembali ke dashboard ===
function kembaliDashboard() {
  window.location.href = "../dashboard.php"; // ubah path sesuai lokasi dashboard kamu
}

// === Filter Tahun ===
function filterTahun(jenis) {
  const tahun = document.getElementById('filterTahun' + jenis).value;
  const rows = document.querySelectorAll(`#table${jenis} tbody tr`);

  rows.forEach(row => {
    const tahunCell = row.children[0].innerText.trim();
    if (tahun === 'semua' || tahunCell === tahun) {
      row.style.display = '';
    } else {
      row.style.display = 'none';
    }
  });
}

// === Simpan PDF ===
async function simpanPDF(jenis) {
  const container = document.getElementById(`table${jenis}Container`);
  const tahun = document.getElementById(`filterTahun${jenis}`).value;
  const judul = jenis === 'Emerging' ? 'Rekap Infeksi Emerging' : 'Rekap Infeksi Purulen';

  const { jsPDF } = window.jspdf;
  const pdf = new jsPDF('p', 'pt', 'a4');

  const header = `
    <h2 style="text-align:center;">🦠 ${judul}</h2>
    <p style="text-align:center;">${tahun === 'semua' ? 'Semua Tahun' : 'Tahun ' + tahun}</p>
  `;

  const tempDiv = document.createElement('div');
  tempDiv.innerHTML = header + container.outerHTML + `
    <p style="text-align:center; font-size:10pt; margin-top:20px;">
      Dicetak oleh Dashboard Surveilans PPI PHBW — ${new Date().toLocaleDateString('id-ID')}
    </p>
  `;
  document.body.appendChild(tempDiv);

  const canvas = await html2canvas(tempDiv, { scale: 2 });
  const imgData = canvas.toDataURL('image/png');
  const imgWidth = 550;
  const imgHeight = (canvas.height * imgWidth) / canvas.width;
  let position = 40;

  pdf.addImage(imgData, 'PNG', 30, position, imgWidth, imgHeight);
  pdf.save(`${judul.replace(/\s+/g, '_')}.pdf`);

  tempDiv.remove();
}
</script>






</body>
</html>
