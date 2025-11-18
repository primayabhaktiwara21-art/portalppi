<?php
include_once '../koneksi.php';
include "../cek_akses.php";
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Surveilans Infeksi RS | PPI PHBW</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

  <style>
    :root {
      --primary: #1a2a80;
      --secondary: #3b49df;
      --accent: #5b6ef5;
      --background: #f4f7ff; /* 🌤 Lebih cerah dan lembut */
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

    /* Main */
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
      transition: all 0.2s ease;
      background-color: #f9faff;
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

    /* Tabel */
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

    /* RESPONSIVE */
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
    <div>📊 Surveilans Infeksi Rumah Sakit | PPI PHBW</div>
    <button class="dashboard-btn" onclick="kembaliDashboard()">🏠 Kembali ke Dashboard</button>
  </header>

  <nav>
    <button class="active" onclick="showTab('input')">🧾 Input Data</button>
    <button onclick="showTab('ISK')">🧫 Rekap ISK</button>
    <button onclick="showTab('IDO')">🩹 Rekap IDO</button>
    <button onclick="showTab('VAP')">🫁 Rekap VAP</button>
    <button onclick="showTab('IADP')">💉 Rekap IADP</button>
  </nav>

  <main>
    <!-- TAB INPUT -->
    <div id="input" class="tab active">
      <h2>🧾 Form Input Data Surveilans</h2>

      <form id="formSurveilans">
        <label>Tahun</label>
        <input type="number" id="tahun" placeholder="Misal: 2025" min="2020" required>

        <label>Bulan</label>
        <select id="bulan" required>
          <option value="">Pilih Bulan</option>
          <option>Januari</option><option>Februari</option><option>Maret</option>
          <option>April</option><option>Mei</option><option>Juni</option>
          <option>Juli</option><option>Agustus</option><option>September</option>
          <option>Oktober</option><option>November</option><option>Desember</option>
        </select>

        <label>Jenis Surveilans</label>
        <select id="jenis" required>
          <option value="">Pilih Jenis</option>
          <option value="ISK">ISK (Infeksi Saluran Kemih)</option>
          <option value="IDO">IDO (Infeksi Daerah Operasi)</option>
          <option value="VAP">VAP (Ventilator Associated Pneumonia)</option>
          <option value="IADP">IADP (Infeksi Aliran Darah Primer)</option>
        </select>

        <label>Numerator (Kasus Infeksi)</label>
        <input type="number" id="num" placeholder="Jumlah kasus infeksi" min="0" required>

        <label>Denominator (Pasien berisiko)</label>
        <input type="number" id="denum" placeholder="Jumlah pasien berisiko" min="1" required>

        <label>Jenis Hasil</label>
        <select id="tipeHasil">
          <option value="persentase">Persentase (%)</option>
          <option value="permil">Permil (‰)</option>
        </select>

        <div class="result" id="hasil">Hasil: -</div>

        <button type="button" class="save" onclick="simpanData()">💾 Simpan Data</button>
      </form>
    </div>

    <!-- TAB REKAP -->
    <div id="ISK" class="tab">
        
    <h2>🧫 Rekap ISK (Infeksi Saluran Kemih)</h2>

        <!-- Filter Tahun -->
        <div style="margin-bottom:15px;">
          <label for="filterISK">Filter Tahun:</label>
          <select id="filterISK" onchange="filterTable('ISK')">
            <option value="">Semua Tahun</option>
          </select>
        </div>
        
        <div class="table-container">
            
            
        <button onclick="exportPDF('ISK')" 
          style="background:#ef4444;color:white;padding:8px 14px;border:none;border-radius:8px;margin-bottom:12px;cursor:pointer;">
          📄 Simpan PDF
        </button>


          
        <table id="tableISK">
          <thead>
            <tr>
              <th>Tahun</th>
              <th>Bulan</th>
              <th>Numerator</th>
              <th>Denominator</th>
              <th>Hasil</th>
              <th>Satuan</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>

      </div>
    </div>

<!-- ================= REKAP IDO ================= -->
<div id="IDO" class="tab">
  <h2>🩹 Rekap IDO (Infeksi Daerah Operasi)</h2>
  <div style="margin-bottom:15px;">
    <label for="filterIDO">Filter Tahun:</label>
    <select id="filterIDO" onchange="filterTable('IDO')">
      <option value="">Semua Tahun</option>
    </select>
  </div>

  <div class="table-container">
    <button onclick="exportPDF('IDO')" 
      style="background:#ef4444;color:white;padding:8px 14px;border:none;border-radius:8px;margin-bottom:12px;cursor:pointer;">
      📄 Simpan PDF
    </button>

    <table id="tableIDO">
      <thead>
        <tr>
          <th>Tahun</th>
          <th>Bulan</th>
          <th>Numerator</th>
          <th>Denominator</th>
          <th>Hasil</th>
          <th>Satuan</th>
          <th>Aksi</th>
        </tr>
      </thead>
  <tbody></tbody>
</table>

    
    
  </div>
</div>

<!-- ================= REKAP VAP ================= -->
<div id="VAP" class="tab">
  <h2>🫁 Rekap VAP (Ventilator Associated Pneumonia)</h2>
  <div style="margin-bottom:15px;">
    <label for="filterVAP">Filter Tahun:</label>
    <select id="filterVAP" onchange="filterTable('VAP')">
      <option value="">Semua Tahun</option>
    </select>
  </div>

  <div class="table-container">
    <button onclick="exportPDF('VAP')" 
      style="background:#ef4444;color:white;padding:8px 14px;border:none;border-radius:8px;margin-bottom:12px;cursor:pointer;">
      📄 Simpan PDF
    </button>

    <table id="tableVAP">
      <thead>
        <tr>
          <th>Tahun</th>
          <th>Bulan</th>
          <th>Numerator</th>
          <th>Denominator</th>
          <th>Hasil</th>
          <th>Satuan</th>
          <th>Aksi</th>
        </tr>
      </thead>
  <tbody></tbody>
</table>

    
    
  </div>
</div>

<!-- ================= REKAP IADP ================= -->
<div id="IADP" class="tab">
  <h2>💉 Rekap IADP (Infeksi Aliran Darah Primer)</h2>
  <div style="margin-bottom:15px;">
    <label for="filterIADP">Filter Tahun:</label>
    <select id="filterIADP" onchange="filterTable('IADP')">
      <option value="">Semua Tahun</option>
    </select>
  </div>

  <div class="table-container">
    <button onclick="exportPDF('IADP')" 
      style="background:#ef4444;color:white;padding:8px 14px;border:none;border-radius:8px;margin-bottom:12px;cursor:pointer;">
      📄 Simpan PDF
    </button>

    <table id="tableIADP">
      <thead>
        <tr>
          <th>Tahun</th>
          <th>Bulan</th>
          <th>Numerator</th>
          <th>Denominator</th>
          <th>Hasil</th>
          <th>Satuan</th>
          <th>Aksi</th>
        </tr>
      </thead>
  <tbody></tbody>
</table>

    
    
  </div>
</div>

  </main>

  <footer>© 2025 PPI PHBW — Dashboard Surveilans Infeksi RS</footer>

  <!-- ✅ Tambahkan pustaka PDF di luar script utama -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
function showTab(tabId) {
  document.querySelectorAll('nav button').forEach(btn => btn.classList.remove('active'));
  document.querySelectorAll('.tab').forEach(tab => tab.classList.remove('active'));
  document.querySelector(`nav button[onclick="showTab('${tabId}')"]`).classList.add('active');
  document.getElementById(tabId).classList.add('active');
}

function kembaliDashboard() {
  window.location.href = "/dashboard.php";
}

// === FILTER TAHUN ===
function filterTable(jenis) {
  const select = document.getElementById("filter" + jenis);
  const tahun = select.value;
  const table = document.getElementById("table" + jenis);
  const rows = table.querySelectorAll("tbody tr");

  rows.forEach(row => {
    const cellTahun = row.cells[0].textContent;
    row.style.display = (tahun === "" || cellTahun === tahun) ? "" : "none";
  });
}

// === UPDATE DROPDOWN TAHUN OTOMATIS ===
function updateYearOptions(jenis) {
  const table = document.getElementById("table" + jenis);
  const select = document.getElementById("filter" + jenis);
  const years = new Set();

  table.querySelectorAll("tbody tr").forEach(row => {
    const year = row.cells[0].textContent;
    if (year) years.add(year);
  });

  select.innerHTML = '<option value="">Semua Tahun</option>';
  years.forEach(y => {
    const opt = document.createElement("option");
    opt.value = y;
    opt.textContent = y;
    select.appendChild(opt);
  });
}

// === EKSPOR PDF ===
function exportPDF(jenis) {
  const table = document.getElementById("table" + jenis);
  const title = document.querySelector(`#${jenis} h2`).textContent;
  const filter = document.getElementById("filter" + jenis)?.value || "";
  const tahunInfo = filter ? `Tahun ${filter}` : "Semua Tahun";

  const laporan = document.createElement("div");
  laporan.style.fontFamily = "Poppins, sans-serif";
     laporan.style.padding = "20px";
    laporan.style.transform = "scale(0.93)";
    laporan.style.transformOrigin = "top center";
 
  laporan.style.color = "#1e293b";
  laporan.innerHTML = `
  
  
    <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:5px;">
      <img src="https://portalppi.my.id/surveilance/assets/Primaya.png" alt="Logo Primaya" style="height:42px;margin-top:2px;">
      <div style="flex-grow:1;text-align:center;line-height:1.2;">
               <h2 style="color:#1a2a80;margin:0;font-size:16px;border:none;">${title}</h2>

        <h4 style="margin:3px 0 2px 0;color:#2563eb;font-size:13px;">${tahunInfo}</h4>
      </div>
    </div>
    <p style="text-align:right;font-size:11px;color:#555;margin:0;">
      Tanggal Cetak: ${new Date().toLocaleDateString('id-ID')}
    </p>

    
    <table border="1" cellspacing="0" cellpadding="6" 
      style="width:100%;border-collapse:collapse;font-size:13px;text-align:center;">
      <thead style="background:#2563eb;color:white;">
        <tr>
          <th>Tahun</th>
          <th>Bulan</th>
          <th>Numerator</th>
          <th>Denominator</th>
          <th>Hasil</th>
          <th>Satuan</th>
        </tr>
      </thead>
      <tbody>
        ${Array.from(table.querySelectorAll("tbody tr"))
          .filter(row => row.style.display !== "none")
          .map(row => `
            <tr>
              ${Array.from(row.cells).slice(0, 6)
                .map(cell => `<td>${cell.textContent}</td>`).join("")}
            </tr>
          `).join("")}
      </tbody>
    </table>
    <br><br>
    <p style="font-size:13px;text-align:left;">Catatan: Data ini dihasilkan otomatis oleh sistem dashboard surveilans infeksi rumah sakit PPI PHBW.</p>
    <p style="font-size:13px;text-align:center;margin-top:40px;">
      Mengetahui,<br><br><br><br><br><strong>Ketua Komite PPI</strong>
    </p>
  `;

  const opt = {
    margin: 0.5,
    filename: title.replaceAll(" ", "_") + "_" + (filter || "SemuaTahun") + ".pdf",
    image: { type: "jpeg", quality: 0.98 },
    html2canvas: { scale: 2 },
    jsPDF: { unit: "in", format: "a4", orientation: "portrait" }
  };

  html2pdf().set(opt).from(laporan).save();
}

// === SIMPAN DATA + UPDATE DROPDOWN ===
function simpanData() {
  const tahun = document.getElementById("tahun").value;
  const bulan = document.getElementById("bulan").value;
  const jenis = document.getElementById("jenis").value;
  const num = parseFloat(document.getElementById("num").value);
  const denum = parseFloat(document.getElementById("denum").value);
  const tipe = document.getElementById("tipeHasil").value;
  const hasilBox = document.getElementById("hasil");

  if (!tahun || !bulan || !jenis || !num || !denum) {
    alert("⚠️ Lengkapi semua data!");
    return;
  }

  const hasil = tipe === "persentase" ? (num / denum) * 100 : (num / denum) * 1000;
  const satuan = tipe === "persentase" ? "%" : "‰";
  hasilBox.textContent = `Hasil: ${hasil.toFixed(2)} ${satuan}`;

  const formData = new FormData();
  formData.append("tahun", tahun);
  formData.append("bulan", bulan);
  formData.append("jenis", jenis);
  formData.append("numerator", num);
  formData.append("denominator", denum);
  formData.append("hasil", hasil.toFixed(2));
  formData.append("satuan", satuan);

  fetch("save_data.php", {
    method: "POST",
    body: formData
  })
  .then(res => res.text())
  .then(res => {
    if (res === "success") {
      alert(`✅ Data ${jenis} berhasil disimpan!`);
      loadTable(jenis);
      document.getElementById("formSurveilans").reset();
      hasilBox.textContent = "Hasil: -";
    } else {
      alert("❌ Gagal menyimpan data!");
    }
  });
}

function loadTable(jenis) {
  const tbody = document.querySelector(`#table${jenis} tbody`);
  tbody.innerHTML = "";

  fetch(`load_data.php?jenis=${jenis}`)
    .then(res => res.json())
    .then(data => {
      data.forEach(row => {
        const tr = document.createElement("tr");
        tr.innerHTML = `
          <td>${row.tahun}</td>
          <td>${row.bulan}</td>
          <td>${row.numerator}</td>
          <td>${row.denominator}</td>
          <td>${row.hasil}</td>
          <td>${row.satuan}</td>
          <td><button class="delete-btn" onclick="deleteData(${row.id}, '${jenis}')">🗑️ Hapus</button></td>
        `;
        tbody.appendChild(tr);
      });
      updateYearOptions(jenis);
    });
}

function deleteData(id, jenis) {
  if (!confirm("Yakin ingin menghapus data ini?")) return;
  const fd = new FormData();
  fd.append("id", id);
  fetch("delete_data.php", {
    method: "POST",
    body: fd
  })
  .then(res => res.text())
  .then(res => {
    if (res === "deleted") {
      alert("🗑️ Data berhasil dihapus!");
      loadTable(jenis);
    } else {
      alert("❌ Gagal menghapus data!");
    }
  });
}

// Auto load data saat buka halaman
["ISK", "IDO", "VAP", "IADP"].forEach(loadTable);
</script>

</body>
</html>
