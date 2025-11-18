<?php
include_once '../koneksi.php';
include "../cek_akses.php";
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Hasil Kultur | PPI PHBW</title>
<style>
:root {
  --navy: #1a237e;
  --blue: #3b49df;
  --sky: #eef1ff;
  --green: #43a047;
  --red: #d32f2f;
  --border: #dce0f0;
  --card: #ffffff;
  --transition: all 0.2s ease-in-out;
}
body {
  font-family: "Segoe UI", sans-serif;
  background: var(--sky);
  margin: 0;
  color: #333;
}
header {
  background: linear-gradient(90deg, var(--navy), var(--blue));
  color: white;
  padding: 18px 28px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-weight: bold;
  box-shadow: 0 3px 10px rgba(0,0,0,0.15);
}
header h1 {
  font-size: 1.2em;
  margin: 0;
}
.dashboard-btn {
  background: white;
  color: var(--blue);
  border: none;
  padding: 8px 16px;
  border-radius: 10px;
  font-weight: 600;
  cursor: pointer;
  transition: var(--transition);
}
.dashboard-btn:hover {
  background: var(--blue);
  color: white;
  box-shadow: 0 4px 10px rgba(57,73,223,0.3);
}
main {
  max-width: 1100px;
  margin: 30px auto;
  background: var(--card);
  border-radius: 14px;
  padding: 25px 35px;
  box-shadow: 0 4px 15px rgba(0,0,0,0.08);
}
h2 {
  color: var(--navy);
  border-bottom: 3px solid var(--blue);
  padding-bottom: 6px;
}
.form-section {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 12px;
  margin-bottom: 20px;
}
input, select, textarea {
  padding: 10px;
  border: 1px solid var(--border);
  border-radius: 10px;
  font-size: 0.95em;
  width: 100%;
}
textarea {
  resize: vertical;
}
button {
  border: none;
  padding: 10px 16px;
  border-radius: 10px;
  font-weight: 600;
  cursor: pointer;
  color: white;
  transition: var(--transition);
}
button.save { background: var(--blue); }
button.save:hover { background: #283593; }
button.clear { background: var(--red); }
button.clear:hover { background: #b71c1c; }
.table-wrapper {
  margin-top: 15px;
  overflow-x: auto;
}
table {
  width: 100%;
  border-collapse: collapse;
  font-size: 0.95em;
}
th, td {
  padding: 10px 8px;
  border-bottom: 1px solid var(--border);
  text-align: left;
  vertical-align: top;
}
th {
  background: var(--sky);
  color: var(--navy);
  font-weight: 600;
}
tr:hover td {
  background: #f8f9ff;
}
td.actions {
  text-align: center;
  white-space: nowrap;
}
td.actions button {
  padding: 6px 10px;
  font-size: 0.85em;
  border-radius: 8px;
  margin: 0 3px;
}
.view { background: var(--green); }
.delete { background: var(--red); }
input#search {
  width: 100%;
  padding: 10px;
  border-radius: 10px;
  border: 1px solid var(--border);
  margin: 10px 0 15px 0;
}
footer {
  text-align: center;
  padding: 20px;
  color: gray;
  font-size: 0.9em;
}
</style>
</head>
<body>
<header>
  <h1>🧫 Hasil Kultur Pasien | PPI PHBW</h1>
  <button class="dashboard-btn" onclick="goDashboard()">🏠 Kembali ke Dashboard</button>
</header>

<main>
  <h2>🧾 Input Data Hasil Kultur</h2>

  <div class="form-section">
    <input type="text" id="nama" placeholder="Nama Pasien">
    <input type="text" id="ruangan" placeholder="Ruangan">
    <input type="date" id="tanggal">
    <input type="text" id="spesimen" placeholder="Jenis Spesimen (misal: Urin, Sputum)">
    <input type="text" id="hasil" placeholder="Hasil (misal: E. coli, MRSA)">
    <textarea id="keterangan" rows="2" placeholder="Keterangan tambahan (misal: sensitif, resisten, dll)"></textarea>
  </div>

  <button class="save" onclick="tambahData()">💾 Simpan Data</button>
  <button class="clear" onclick="hapusSemua()">🧹 Hapus Semua</button>

  <input type="search" id="search" placeholder="🔍 Cari nama pasien, spesimen, atau hasil...">

  <div class="table-wrapper">
    <table id="tabelKultur">
      <thead>
        <tr>
          <th>No</th>
          <th>Nama Pasien</th>
          <th>Ruangan</th>
          <th>Tanggal</th>
          <th>Spesimen</th>
          <th>Hasil</th>
          <th>Keterangan</th>
          <th style="text-align:center;">Aksi</th>
        </tr>
      </thead>
      <tbody></tbody>
    </table>
  </div>
</main>

<footer>© 2025 PPI PHBW — Hasil Kultur Pasien | Desain Dashboard Profesional</footer>

<script>
let db;
const DB_NAME="hasil_kultur_ppi";
const STORE="kultur";

async function openDB(){
  return new Promise(res=>{
    const req=homeedDB.open(DB_NAME,1);
    req.onupgradeneeded=e=>{
      e.target.result.createObjectStore(STORE,{keyPath:"id",autoIncrement:true});
    };
    req.onsuccess=e=>{db=e.target.result;res();};
  });
}
function addData(data){
  const tx=db.transaction(STORE,"readwrite");
  tx.objectStore(STORE).add(data);
}
function getAll(){
  return new Promise(res=>{
    const tx=db.transaction(STORE,"readonly");
    tx.objectStore(STORE).getAll().onsuccess=e=>res(e.target.result);
  });
}

async function tambahData(){
  const nama=document.getElementById("nama").value.trim();
  const ruangan=document.getElementById("ruangan").value.trim();
  const tanggal=document.getElementById("tanggal").value;
  const spesimen=document.getElementById("spesimen").value.trim();
  const hasil=document.getElementById("hasil").value.trim();
  const keterangan=document.getElementById("keterangan").value.trim();

  if(!nama || !spesimen || !hasil){
    alert("Nama pasien, spesimen, dan hasil wajib diisi!");
    return;
  }

  addData({
    nama, ruangan, tanggal, spesimen, hasil, keterangan,
    created:new Date().toISOString()
  });

  document.querySelectorAll("input, textarea").forEach(i=>i.value="");
  render();
}

async function render(){
  const data=await getAll();
  const tbody=document.querySelector("#tabelKultur tbody");
  tbody.innerHTML="";
  const search=document.getElementById("search").value.toLowerCase();
  const filtered=data.filter(d=>
    d.nama.toLowerCase().includes(search) ||
    d.spesimen.toLowerCase().includes(search) ||
    d.hasil.toLowerCase().includes(search)
  );
  filtered.sort((a,b)=>new Date(b.created)-new Date(a.created));
  filtered.forEach((d,i)=>{
    const tr=document.createElement("tr");
    tr.innerHTML=`
      <td>${i+1}</td>
      <td>${d.nama}</td>
      <td>${d.ruangan||"-"}</td>
      <td>${d.tanggal?new Date(d.tanggal).toLocaleDateString():"-"}</td>
      <td>${d.spesimen}</td>
      <td>${d.hasil}</td>
      <td>${d.keterangan||"-"}</td>
      <td class="actions">
        <button class="view" onclick="lihatDetail(${d.id})">👁️ Detail</button>
        <button class="delete" onclick="hapusData(${d.id})">🗑️ Hapus</button>
      </td>`;
    tbody.appendChild(tr);
  });
}

function lihatDetail(id){
  getAll().then(all=>{
    const d=all.find(i=>i.id===id);
    alert(
      `🧫 DETAIL HASIL KULTUR\n\n`+
      `Nama Pasien: ${d.nama}\n`+
      `Ruangan: ${d.ruangan||"-"}\n`+
      `Tanggal: ${d.tanggal?new Date(d.tanggal).toLocaleDateString():"-"}\n`+
      `Spesimen: ${d.spesimen}\n`+
      `Hasil: ${d.hasil}\n`+
      `Keterangan: ${d.keterangan||"-"}`
    );
  });
}
function hapusData(id){
  if(!confirm("Hapus data ini?"))return;
  const tx=db.transaction(STORE,"readwrite");
  tx.objectStore(STORE).delete(id).onsuccess=render;
}
function hapusSemua(){
  if(!confirm("Hapus SEMUA hasil kultur?"))return;
  const tx=db.transaction(STORE,"readwrite");
  tx.objectStore(STORE).clear().onsuccess=render;
}
document.getElementById("search").addEventListener("input",()=>render());

(async function init(){
  await openDB();
  render();
})();
function goDashboard(){window.location.href="/dashboard.php";}
</script>
</body>
</html>
