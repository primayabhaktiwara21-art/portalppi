<?php
include_once '../koneksi.php';
include "../cek_akses.php";
?>


<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Laporan Triwulan & Tahunan Komite PPI | PPI PHBW</title>
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
nav {
  background: var(--blue);
  display: flex;
  justify-content: center;
}
nav button {
  background: transparent;
  border: none;
  color: white;
  padding: 14px 22px;
  cursor: pointer;
  font-weight: 600;
  transition: 0.2s;
}
nav button.active { background: var(--navy); }
main {
  max-width: 950px;
  margin: 30px auto;
  background: var(--card);
  border-radius: 14px;
  padding: 25px 35px;
  box-shadow: 0 4px 15px rgba(0,0,0,0.08);
}
h2 { color: var(--navy); border-bottom: 3px solid var(--blue); padding-bottom: 6px; }
input, textarea, select {
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
th { background: var(--sky); color: var(--navy); }
tr:hover td { background: #f8f9ff; }
td.actions { text-align: center; white-space: nowrap; }
td.actions button {
  padding: 6px 10px;
  font-size: 0.85em;
  border-radius: 8px;
  margin: 0 3px;
}
.view { background: var(--blue); }
.download { background: var(--green); }
.delete { background: var(--red); }
input#searchTri, input#searchTahunan {
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
.tab { display: none; }
.tab.active { display: block; }
</style>
</head>
<body>
<header>
  <h1>📑 Laporan Triwulan & Tahunan Komite PPI | PPI PHBW</h1>
  <button class="dashboard-btn" onclick="goDashboard()">🏠 Kembali ke Dashboard</button>
</header>

<nav>
  <button class="active" onclick="showTab('triwulan', this)">🗓️ Laporan Triwulan</button>
  <button onclick="showTab('tahunan', this)">📅 Laporan Tahunan</button>
</nav>

<main>
  <!-- TRIWIULAN -->
  <section id="triwulan" class="tab active">
    <h2>🗓️ Laporan Triwulan Komite PPI</h2>
    <select id="periode">
      <option value="">Pilih Triwulan</option>
      <option>Triwulan I (Jan–Mar)</option>
      <option>Triwulan II (Apr–Jun)</option>
      <option>Triwulan III (Jul–Sep)</option>
      <option>Triwulan IV (Okt–Des)</option>
    </select>
    <input type="number" id="tahunTri" placeholder="Tahun (contoh: 2025)" min="2020" max="2100">
    <input type="text" id="penanggungTri" placeholder="Penanggung Jawab">
    <textarea id="ringkasanTri" placeholder="Ringkasan kegiatan selama triwulan"></textarea>
    <textarea id="rekomendasiTri" placeholder="Rekomendasi & tindak lanjut"></textarea>
    <input type="file" id="fileTri" accept=".pdf,.docx,.xlsx,.jpg,.png">

    <div style="margin:10px 0;">
      <button class="save" onclick="simpan('Tri')">💾 Simpan</button>
      <button class="clear" onclick="hapusSemua('Tri')">🧹 Hapus Semua</button>
    </div>

    <input type="search" id="searchTri" placeholder="🔍 Cari berdasarkan tahun, triwulan, atau penanggung...">

    <div class="table-wrapper">
      <table id="tabelTri">
        <thead>
          <tr>
            <th>No</th>
            <th>Tahun</th>
            <th>Periode</th>
            <th>Penanggung</th>
            <th>Ringkasan</th>
            <th>Rekomendasi</th>
            <th style="text-align:center;">Aksi</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
  </section>

  <!-- TAHUNAN -->
  <section id="tahunan" class="tab">
    <h2>📅 Laporan Tahunan Komite PPI</h2>
    <input type="number" id="tahunTahunan" placeholder="Tahun (contoh: 2025)" min="2020" max="2100">
    <input type="text" id="penanggungTahunan" placeholder="Penanggung Jawab">
    <textarea id="ringkasanTahunan" placeholder="Ringkasan kegiatan tahunan"></textarea>
    <textarea id="rekomendasiTahunan" placeholder="Rekomendasi & tindak lanjut tahun berikutnya"></textarea>
    <input type="file" id="fileTahunan" accept=".pdf,.docx,.xlsx,.jpg,.png">

    <div style="margin:10px 0;">
      <button class="save" onclick="simpan('Tahunan')">💾 Simpan</button>
      <button class="clear" onclick="hapusSemua('Tahunan')">🧹 Hapus Semua</button>
    </div>

    <input type="search" id="searchTahunan" placeholder="🔍 Cari berdasarkan tahun atau penanggung...">

    <div class="table-wrapper">
      <table id="tabelTahunan">
        <thead>
          <tr>
            <th>No</th>
            <th>Tahun</th>
            <th>Penanggung</th>
            <th>Ringkasan</th>
            <th>Rekomendasi</th>
            <th style="text-align:center;">Aksi</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
  </section>
</main>

<footer>© 2025 PPI PHBW — Laporan Triwulan & Tahunan Komite PPI</footer>

<script>
let db;
const DB_NAME="ppi_triwulan_tahunan";
const STORE_TRI="triwulan";
const STORE_TAHUN="tahunan";

async function openDB(){
  return new Promise(res=>{
    const req=homeedDB.open(DB_NAME,1);
    req.onupgradeneeded=e=>{
      const db=e.target.result;
      db.createObjectStore(STORE_TRI,{keyPath:"id",autoIncrement:true});
      db.createObjectStore(STORE_TAHUN,{keyPath:"id",autoIncrement:true});
    };
    req.onsuccess=e=>{db=e.target.result;res();};
  });
}

function addData(store,data){
  const tx=db.transaction(store,"readwrite");
  tx.objectStore(store).add(data);
}
function getAll(store){
  return new Promise(res=>{
    const tx=db.transaction(store,"readonly");
    tx.objectStore(store).getAll().onsuccess=e=>res(e.target.result);
  });
}

/* SIMPAN */
async function simpan(type){
  const store = type==="Tri"?STORE_TRI:STORE_TAHUN;
  const tahun=document.getElementById(`tahun${type}`).value;
  const penanggung=document.getElementById(`penanggung${type}`).value.trim();
  const ringkasan=document.getElementById(`ringkasan${type}`).value.trim();
  const rekomendasi=document.getElementById(`rekomendasi${type}`).value.trim();
  const file=document.getElementById(`file${type}`).files[0];
  const periode = type==="Tri" ? document.getElementById("periode").value : null;

  if(!tahun || !penanggung || !ringkasan){
    alert("Lengkapi Tahun, Penanggung, dan Ringkasan!");
    return;
  }

  let fileObj=null;
  if(file){ fileObj={nama:file.name,tipe:file.type,blob:file}; }

  addData(store,{tahun,periode,penanggung,ringkasan,rekomendasi,file:fileObj,tanggal:new Date().toISOString()});
  document.querySelectorAll(`#${type==="Tri"?"triwulan":"tahunan"} input, #${type==="Tri"?"triwulan":"tahunan"} textarea, #${type==="Tri"?"triwulan":"tahunan"} select`).forEach(i=>i.value="");
  render(type);
}

/* RENDER */
async function render(type){
  const store = type==="Tri"?STORE_TRI:STORE_TAHUN;
  const data=await getAll(store);
  const tbody=document.querySelector(`#tabel${type} tbody`);
  const search=document.getElementById(`search${type}`).value.toLowerCase();
  tbody.innerHTML="";

  data.filter(d=>
    d.tahun.toString().includes(search) ||
    (d.penanggung||"").toLowerCase().includes(search) ||
    (d.ringkasan||"").toLowerCase().includes(search)
  ).sort((a,b)=>b.tahun-a.tahun).forEach((d,i)=>{
    const tr=document.createElement("tr");
    tr.innerHTML=`
      <td>${i+1}</td>
      <td>${d.tahun}</td>
      ${type==="Tri"?`<td>${d.periode||"-"}</td>`:""}
      <td>${d.penanggung}</td>
      <td>${(d.ringkasan||"").slice(0,60)}${d.ringkasan?.length>60?'...':''}</td>
      <td>${(d.rekomendasi||"").slice(0,60)}${d.rekomendasi?.length>60?'...':''}</td>
      <td class="actions">
        <button class="view" onclick="lihat('${type}',${d.id})">👁️</button>
        ${d.file?`<button class="download" onclick="unduh('${type}',${d.id})">⬇️</button>`:""}
        <button class="delete" onclick="hapus('${type}',${d.id})">🗑️</button>
      </td>`;
    tbody.appendChild(tr);
  });
}

/* AKSI */
function lihat(type,id){
  const store=type==="Tri"?STORE_TRI:STORE_TAHUN;
  getAll(store).then(all=>{
    const d=all.find(f=>f.id===id);
    alert(`📄 LAPORAN ${type==="Tri"?"TRIWULAN":"TAHUNAN"}\n\nTahun: ${d.tahun}\n${d.periode?`Periode: ${d.periode}\n`:''}Penanggung: ${d.penanggung}\n\nRingkasan:\n${d.ringkasan}\n\nRekomendasi:\n${d.rekomendasi||'-'}`);
  });
}
function unduh(type,id){
  const store=type==="Tri"?STORE_TRI:STORE_TAHUN;
  getAll(store).then(all=>{
    const d=all.find(f=>f.id===id);
    if(!d.file)return alert("Tidak ada file!");
    const url=URL.createObjectURL(d.file.blob);
    const a=document.createElement("a");a.href=url;a.download=d.file.nama;a.click();URL.revokeObjectURL(url);
  });
}
function hapus(type,id){
  const store=type==="Tri"?STORE_TRI:STORE_TAHUN;
  if(!confirm("Hapus laporan ini?"))return;
  const tx=db.transaction(store,"readwrite");
  tx.objectStore(store).delete(id).onsuccess=()=>render(type);
}
function hapusSemua(type){
  const store=type==="Tri"?STORE_TRI:STORE_TAHUN;
  if(!confirm(`Hapus semua laporan ${type==="Tri"?"triwulan":"tahunan"}?`))return;
  const tx=db.transaction(store,"readwrite");
  tx.objectStore(store).clear().onsuccess=()=>render(type);
}

/* TAB SWITCH */
function showTab(id,btn){
  document.querySelectorAll("nav button").forEach(b=>b.classList.remove("active"));
  btn.classList.add("active");
  document.querySelectorAll(".tab").forEach(t=>t.classList.remove("active"));
  document.getElementById(id).classList.add("active");
}
document.getElementById("searchTri").addEventListener("input",()=>render("Tri"));
document.getElementById("searchTahunan").addEventListener("input",()=>render("Tahunan"));

(async function init(){
  await openDB();
  render("Tri");
  render("Tahunan");
})();
function goDashboard(){ window.location.href="/dashboard.php"; }
</script>
</body>
</html>
