<?php
include_once '../koneksi.php';
include "../cek_akses.php";

// === SIMPAN DATA ===
if (isset($_POST['action']) && $_POST['action'] == 'save') {
  $tahun = $_POST['tahun'];
  $bulan = $_POST['bulan'];
  $jenis = $_POST['jenis'];
  $unit = $_POST['unit'];
  $numerator = $_POST['numerator'];
  $denominator = $_POST['denominator'];
  $hasil = $_POST['hasil'];
  $satuan = $_POST['satuan'];

  $sql = "INSERT INTO tb_surveillance_antibiotik_mdro 
          (tahun, bulan, jenis, unit, numerator, denominator, hasil, satuan)
          VALUES ('$tahun', '$bulan', '$jenis', '$unit', '$numerator', '$denominator', '$hasil', '$satuan')";
  mysqli_query($conn, $sql);
  exit("success");
}

// === HAPUS DATA ===
if (isset($_POST['action']) && $_POST['action'] == 'delete') {
  $id = $_POST['id'];
  mysqli_query($conn, "DELETE FROM tb_surveillance_antibiotik_mdro WHERE id='$id'");
  exit("deleted");
}

// === AMBIL DATA ===
if (isset($_GET['load'])) {
  $jenis = $_GET['jenis'];
  $sql = "SELECT * FROM tb_surveillance_antibiotik_mdro WHERE jenis='$jenis' ORDER BY id DESC";
  $res = mysqli_query($conn, $sql);
  $data = [];
  while ($row = mysqli_fetch_assoc($res)) {
    $data[] = $row;
  }
  header('Content-Type: application/json');
  echo json_encode($data);
  exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Surveilans Antibiotik & MDRO | PPI PHBW</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

  <style>
    :root {
      --primary: #1a2a80;
      --secondary: #3b49df;
      --accent: #5b6ef5;
      --background: #f4f7ff; /* 🌤 lembut & profesional */
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
    <div>💊 Surveilans Antibiotik & MDRO | PPI PHBW</div>
    <button class="dashboard-btn" onclick="kembaliDashboard()">🏠 Kembali ke Dashboard</button>
  </header>

  <nav>
    <button class="active" onclick="showTab('input')">🧾 Input Data</button>
    <button onclick="showTab('Antibiotik')">💊 Rekap Antibiotik</button>
    <button onclick="showTab('MDRO')">🦠 Rekap MDRO</button>
  </nav>

  <main>
    <!-- TAB INPUT -->
    <div id="input" class="tab active">
      <h2>🧾 Input Data Surveilans</h2>
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
          <option value="Antibiotik">Penggunaan Antibiotik</option>
          <option value="MDRO">MDRO (Organisme Multi Drug Resistant)</option>
        </select>

        <label>Nama Unit / Ruangan</label>
        <input type="text" id="unit" placeholder="Contoh: ICU, Ruang Bedah, dll" required>

        <label>Numerator (Kasus / Isolat Positif)</label>
        <input type="number" id="num" min="0" required>

        <label>Denominator (Total Pasien / Kultur)</label>
        <input type="number" id="denum" min="1" required>

        <label>Jenis Hasil</label>
        <select id="tipeHasil">
          <option value="persentase">Persentase (%)</option>
          <option value="permil">Permil (‰)</option>
        </select>

        <div class="result" id="hasil">Hasil: -</div>
        <button type="button" class="save" onclick="simpanData()">💾 Simpan Data</button>
      </form>
    </div>

<!-- TAB REKAP ANTIBIOTIK -->
<div id="Antibiotik" class="tab">
  <h2>💊 Rekap Penggunaan Antibiotik</h2>

  <!-- Filter Tahun -->
  <div style="margin-bottom:15px;">
    <label for="filterAntibiotik">Filter Tahun:</label>
    <select id="filterAntibiotik" onchange="filterTable('Antibiotik')">
      <option value="">Semua Tahun</option>
    </select>
  </div>

  <!-- Tombol Simpan PDF -->
  <button onclick="exportPDF('Antibiotik')" 
    style="background:#ef4444;color:white;padding:8px 14px;border:none;border-radius:8px;margin-bottom:12px;cursor:pointer;">
    📄 Simpan PDF
  </button>

  <div class="table-container">
    <table id="tableAntibiotik">
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
      <tbody></tbody>
    </table>
  </div>
</div>

<!-- TAB REKAP MDRO -->
<div id="MDRO" class="tab">
  <h2>🦠 Rekap MDRO (Multi Drug Resistant Organisms)</h2>

  <!-- Filter Tahun -->
  <div style="margin-bottom:15px;">
    <label for="filterMDRO">Filter Tahun:</label>
    <select id="filterMDRO" onchange="filterTable('MDRO')">
      <option value="">Semua Tahun</option>
    </select>
  </div>

  <!-- Tombol Simpan PDF -->
  <button onclick="exportPDF('MDRO')" 
    style="background:#ef4444;color:white;padding:8px 14px;border:none;border-radius:8px;margin-bottom:12px;cursor:pointer;">
    📄 Simpan PDF
  </button>

  <div class="table-container">
    <table id="tableMDRO">
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
      <tbody></tbody>
    </table>
  </div>
</div>

    
  </main>

  <footer>© 2025 PPI PHBW — Surveilans Antibiotik & MDRO</footer>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>


<script>
function showTab(tabId){
  document.querySelectorAll('nav button').forEach(b=>b.classList.remove('active'));
  document.querySelectorAll('.tab').forEach(t=>t.classList.remove('active'));
  document.querySelector(`nav button[onclick="showTab('${tabId}')"]`).classList.add('active');
  document.getElementById(tabId).classList.add('active');
}

function kembaliDashboard(){ window.location.href="/dashboard.php"; }

function simpanData(){
  const tahun=document.getElementById("tahun").value;
  const bulan=document.getElementById("bulan").value;
  const jenis=document.getElementById("jenis").value;
  const unit=document.getElementById("unit").value;
  const num=parseFloat(document.getElementById("num").value);
  const denum=parseFloat(document.getElementById("denum").value);
  const tipe=document.getElementById("tipeHasil").value;
  const hasilBox=document.getElementById("hasil");
  if(!tahun||!bulan||!jenis||!unit||!num||!denum){alert("⚠️ Lengkapi semua data!");return;}
  let hasil=tipe==="persentase"?(num/denum)*100:(num/denum)*1000;
  const satuan=tipe==="persentase"?"%":"‰";
  hasil=hasil.toFixed(2);
  hasilBox.textContent=`Hasil: ${hasil} ${satuan}`;
  const fd=new FormData();
  fd.append("action","save");
  fd.append("tahun",tahun);fd.append("bulan",bulan);fd.append("jenis",jenis);
  fd.append("unit",unit);fd.append("numerator",num);fd.append("denominator",denum);
  fd.append("hasil",hasil);fd.append("satuan",satuan);
  fetch("",{method:"POST",body:fd}).then(r=>r.text()).then(r=>{
    if(r==="success"){alert("✅ Data berhasil disimpan!");loadTable(jenis);document.getElementById("formSurveilans").reset();hasilBox.textContent="Hasil: -";}
    else alert("❌ Gagal menyimpan data!");
  });
}

function loadTable(jenis){
  const tbody=document.querySelector(`#table${jenis} tbody`);
  tbody.innerHTML="";
  fetch(`?load=1&jenis=${jenis}`).then(r=>r.json()).then(data=>{
    data.forEach(row=>{
      const tr=document.createElement("tr");
      tr.innerHTML=`<td>${row.tahun}</td><td>${row.bulan}</td><td>${row.unit}</td><td>${row.numerator}</td><td>${row.denominator}</td><td>${row.hasil}</td><td>${row.satuan}</td><td><button class='delete-btn' onclick="deleteData(${row.id}, '${jenis}')">🗑️</button></td>`;
      tbody.appendChild(tr);
    });
    updateYearOptions(jenis);
  });
}

function deleteData(id,jenis){
  if(!confirm("Yakin ingin menghapus data ini?"))return;
  const fd=new FormData();fd.append("action","delete");fd.append("id",id);
  fetch("",{method:"POST",body:fd}).then(r=>r.text()).then(r=>{
    if(r==="deleted"){alert("🗑️ Data dihapus!");loadTable(jenis);}else alert("❌ Gagal hapus!");
  });
}

// === FILTER DAN PDF ===
function updateYearOptions(jenis){
  const table=document.getElementById("table"+jenis);
  const select=document.getElementById("filter"+jenis);
  const years=new Set();
  table.querySelectorAll("tbody tr").forEach(row=>{const y=row.cells[0].textContent;if(y)years.add(y);});
  select.innerHTML='<option value="">Semua Tahun</option>';
  years.forEach(y=>{const opt=document.createElement("option");opt.value=y;opt.textContent=y;select.appendChild(opt);});
}

function filterTable(jenis){
  const tahun=document.getElementById("filter"+jenis).value;
  const rows=document.querySelectorAll("#table"+jenis+" tbody tr");
  rows.forEach(r=>{r.style.display=(tahun===""||r.cells[0].textContent===tahun)?"":"none";});
}

function exportPDF(jenis){
  const table=document.getElementById("table"+jenis);
  const title=document.querySelector(`#${jenis} h2`).textContent;
  const filter=document.getElementById("filter"+jenis).value||"";
  const tahunInfo=filter?`Tahun ${filter}`:"Semua Tahun";
  const laporan=document.createElement("div");
  laporan.innerHTML=`
    <div style='text-align:center;font-family:Poppins'>
      <h2 style='color:#1a2a80;'>${title}</h2>
      <p style='color:#2563eb'>${tahunInfo}</p>
    </div>
    <table border='1' cellspacing='0' cellpadding='6' style='width:100%;border-collapse:collapse;font-size:12px;text-align:center'>
      <thead style='background:#2563eb;color:white'><tr><th>Tahun</th><th>Bulan</th><th>Unit</th><th>Numerator</th><th>Denominator</th><th>Hasil</th><th>Satuan</th></tr></thead>
      <tbody>${Array.from(table.querySelectorAll("tbody tr")).filter(r=>r.style.display!=="none").map(r=>`<tr>${Array.from(r.cells).slice(0,7).map(c=>`<td>${c.textContent}</td>`).join("")}</tr>`).join("")}</tbody>
    </table>
    <p style='text-align:center;font-size:11px;color:#555;margin-top:10px;'>Dicetak oleh Dashboard Surveilans PPI PHBW — ${new Date().toLocaleDateString('id-ID')}</p>`;
  const opt={margin:0.5,filename:title.replaceAll(" ","_")+"_"+(filter||"Semua_Tahun")+".pdf",image:{type:"jpeg",quality:0.98},html2canvas:{scale:2},jsPDF:{unit:"in",format:"a4",orientation:"portrait"}};
  html2pdf().set(opt).from(laporan).save();
}

["Antibiotik","MDRO"].forEach(loadTable);
</script>


</body>
</html>
