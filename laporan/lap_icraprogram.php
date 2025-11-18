<?php
include_once '../koneksi.php';
include "../cek_akses.php";
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Laporan ICRA Tahunan | PPI PHBW</title>
<style>
:root {
  --navy: #1a237e;
  --blue: #3b49df;
  --sky: #eef1ff;
  --red: #d32f2f;
  --green: #43a047;
  --border: #dce0f0;
  --card: #ffffff;
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
header h1 { font-size: 1.1em; margin: 0; }
.dashboard-btn {
  background: white;
  color: var(--blue);
  border: none;
  padding: 8px 16px;
  border-radius: 10px;
  font-weight: 600;
  cursor: pointer;
  transition: 0.2s;
}
.dashboard-btn:hover {
  background: var(--blue);
  color: white;
}
main {
  max-width: 950px;
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
input, textarea {
  width: 100%;
  padding: 10px;
  border: 1px solid var(--border);
  border-radius: 10px;
  font-size: 0.95em;
  margin-bottom: 10px;
  box-sizing: border-box;
}
button {
  border: none;
  padding: 8px 14px;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  color: white;
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
}
th {
  background: var(--sky);
  color: var(--navy);
}
tr:hover td { background: #f8f9ff; }
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
.view { background: var(--blue); }
.download { background: var(--green); }
.delete { background: var(--red); }
input#search {
  width: 100%;
  padding: 10px;
  border-radius: 10px;
  border: 1px solid var(--border);
  margin: 10px 0;
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
  <h1>📋 Laporan ICRA Tahunan | PPI PHBW</h1>
  <button class="dashboard-btn" onclick="goDashboard()">🏠 Kembali ke Dashboard</button>
</header>

<main>
  <h2>🧫 Input Laporan ICRA Tahunan</h2>

  <input type="number" id="tahun" placeholder="Tahun (contoh: 2025)" min="2020" max="2100">
  <input type="text" id="unit" placeholder="Unit / Ruangan">
  <input type="text" id="kegiatan" placeholder="Jenis kegiatan atau proyek pembangunan / renovasi">
  <textarea id="risiko" placeholder="Risiko infeksi yang diidentifikasi (misal: debu, aerosol, air, kontak langsung)"></textarea>
  <textarea id="mitigasi" placeholder="Langkah mitigasi / pengendalian (misal: isolasi area kerja, HEPA filter, cleaning tambahan)"></textarea>
  <input type="text" id="penanggung" placeholder="Penanggung Jawab / PIC">
  <input type="file" id="file" accept=".pdf,.docx,.jpg,.png">

  <div style="margin:10px 0;">
    <button class="save" onclick="simpan()">💾 Simpan</button>
    <button class="clear" onclick="hapusSemua()">🧹 Hapus Semua</button>
  </div>

  <input type="search" id="search" placeholder="🔍 Cari berdasarkan tahun, unit, atau kegiatan...">

  <div class="table-wrapper">
    <table id="tabelICRA">
      <thead>
        <tr>
          <th>No</th>
          <th>Tahun</th>
          <th>Unit / Ruangan</th>
          <th>Kegiatan</th>
          <th>Risiko</th>
          <th>Mitigasi</th>
          <th>Penanggung</th>
          <th style="text-align:center;">Aksi</th>
        </tr>
      </thead>
      <tbody></tbody>
    </table>
  </div>
</main>

<footer>© 2025 PPI PHBW — Laporan ICRA Tahunan</footer>

<script>
let db;
const DB_NAME="ppi_icra_tahunan";
const STORE="icra_tahunan";

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
async function simpan(){
  const tahun=document.getElementById("tahun").value;
  const unit=document.getElementById("unit").value.trim();
  const kegiatan=document.getElementById("kegiatan").value.trim();
  const risiko=document.getElementById("risiko").value.trim();
  const mitigasi=document.getElementById("mitigasi").value.trim();
  const penanggung=document.getElementById("penanggung").value.trim();
  const file=document.getElementById("file").files[0];

  if(!tahun || !unit || !kegiatan){
    alert("Lengkapi Tahun, Unit, dan Kegiatan!");
    return;
  }

  let fileObj=null;
  if(file){ fileObj={nama:file.name,tipe:file.type,blob:file}; }

  addData({tahun,unit,kegiatan,risiko,mitigasi,penanggung,file:fileObj,tanggal:new Date().toISOString()});
  document.querySelectorAll("input, textarea").forEach(i=>i.value="");
  render();
}
async function render(){
  const data=await getAll();
  const tbody=document.querySelector("#tabelICRA tbody");
  const search=document.getElementById("search").value.toLowerCase();
  tbody.innerHTML="";
  data.filter(d=>
    d.tahun.toString().includes(search) ||
    (d.unit||"").toLowerCase().includes(search) ||
    (d.kegiatan||"").toLowerCase().includes(search)
  ).sort((a,b)=>b.tahun-a.tahun).forEach((d,i)=>{
    const tr=document.createElement("tr");
    tr.innerHTML=`
      <td>${i+1}</td>
      <td>${d.tahun}</td>
      <td>${d.unit}</td>
      <td>${d.kegiatan}</td>
      <td>${d.risiko||'-'}</td>
      <td>${d.mitigasi||'-'}</td>
      <td>${d.penanggung||'-'}</td>
      <td class="actions">
        <button class="view" onclick="lihat(${d.id})">👁️</button>
        ${d.file?`<button class="download" onclick="unduh(${d.id})">⬇️</button>`:""}
        <button class="delete" onclick="hapus(${d.id})">🗑️</button>
      </td>`;
    tbody.appendChild(tr);
  });
}
function lihat(id){
  getAll().then(all=>{
    const d=all.find(f=>f.id===id);
    alert(`📋 LAPORAN ICRA TAHUNAN\n\nTahun: ${d.tahun}\nUnit: ${d.unit}\nKegiatan: ${d.kegiatan}\n\nRisiko:\n${d.risiko}\n\nMitigasi:\n${d.mitigasi}\n\nPenanggung: ${d.penanggung}`);
  });
}
function unduh(id){
  getAll().then(all=>{
    const d=all.find(f=>f.id===id);
    if(!d.file)return alert("Tidak ada file!");
    const url=URL.createObjectURL(d.file.blob);
    const a=document.createElement("a");a.href=url;a.download=d.file.nama;a.click();URL.revokeObjectURL(url);
  });
}
function hapus(id){
  if(!confirm("Hapus laporan ini?"))return;
  const tx=db.transaction(STORE,"readwrite");
  tx.objectStore(STORE).delete(id).onsuccess=render;
}
function hapusSemua(){
  if(!confirm("Hapus semua laporan ICRA tahunan?"))return;
  const tx=db.transaction(STORE,"readwrite");
  tx.objectStore(STORE).clear().onsuccess=render;
}
document.getElementById("search").addEventListener("input",()=>render());
(async function init(){ await openDB(); render(); })();
function goDashboard(){ window.location.href="/dashboard.php"; }
</script>
</body>
</html>
