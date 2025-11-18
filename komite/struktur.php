<?php
include_once '../koneksi.php';
include "../cek_akses.php";
$conn = $koneksi;

// Ambil data dari database
$q = mysqli_query($conn, "SELECT * FROM tb_struktur_ppi ORDER BY id DESC LIMIT 1");
$data = mysqli_fetch_assoc($q);

if (!$data) {
  // Jika belum ada data, buat entri kosong
  mysqli_query($conn, "INSERT INTO tb_struktur_ppi (pimpinan, ketua, sekretaris, ipcd, ipcn, ipcln, pj)
                       VALUES ('[Nama Pimpinan RS]', '[Nama Ketua Komite]', '[Nama Sekretaris]', '[Nama IPCD]', '[Nama IPCN]', '[]', '[]')");
  $q = mysqli_query($conn, "SELECT * FROM tb_struktur_ppi ORDER BY id DESC LIMIT 1");
  $data = mysqli_fetch_assoc($q);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Struktur Komite PPI | PPI PHBW</title>
<style>
/* [CSS kamu tetap sama, tidak diubah] */
:root {
  --primary:#1a2a80; --secondary:#3b49df; --accent:#37a0ff;
  --bg:#f4f6fb; --card:#ffffff; --muted:#6b7280;
  --radius:14px; --shadow:0 6px 20px rgba(0,0,0,0.08);
}
body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;background:var(--bg);color:#1e293b;margin:0;}
header{background:linear-gradient(90deg,var(--primary),var(--secondary));color:white;padding:14px 22px;display:flex;justify-content:space-between;align-items:center;box-shadow:var(--shadow);}
header h1{font-size:1.2rem;margin:0;}
a.btn-dashboard{background:linear-gradient(135deg,#4f46e5,#06b6d4);color:white;padding:10px 18px;border-radius:10px;text-decoration:none;font-weight:600;}
main{padding:24px;display:flex;justify-content:center;}
.org-chart{display:flex;flex-direction:column;align-items:center;max-width:900px;width:100%;}
.box{background:var(--card);box-shadow:var(--shadow);border-radius:var(--radius);padding:16px;min-width:260px;margin:12px;text-align:center;}
.box h3{margin:0;color:var(--primary);}
.name{background:#f8f9ff;border:1px solid #e2e8f0;border-radius:8px;padding:6px 8px;margin-top:5px;}
.connector-vertical{width:2px;height:30px;background:#ccc;}
.branch{display:flex;justify-content:center;align-items:flex-start;flex-wrap:wrap;gap:20px;margin-top:10px;position:relative;}
.branch::before{content:"";position:absolute;top:0;left:5%;right:5%;height:2px;background:#d1d5db;}
.group{background:var(--card);border-radius:var(--radius);box-shadow:var(--shadow);padding:16px;width:360px;text-align:left;}
.group h4{text-align:center;color:var(--secondary);}
.member{background:#f9f9ff;border:1px solid #e0e4ff;border-radius:8px;padding:8px;margin:5px 0;}
.add-btn{background:var(--secondary);color:white;border:none;border-radius:8px;padding:8px;margin-top:8px;cursor:pointer;width:100%;}
.add-btn:hover{background:#2531a1;}
footer{text-align:center;font-size:.85rem;color:var(--muted);margin:40px 0 20px;}
@media(max-width:900px){.branch{flex-direction:column;align-items:center;}.branch::before{display:none;}.group{width:90%;}}
</style>
</head>
<body>

<header>
  <h1>🏥 Struktur Komite PPI Rumah Sakit</h1>
  <a href="/dashboard.php" class="btn-dashboard">🏠 Kembali ke Dashboard</a>
</header>

<main>
  <div class="org-chart">
    <div class="box">
      <h3>Pimpinan Rumah Sakit</h3>
      <p>Penanggung Jawab PPI</p>
      <div class="name" contenteditable="true" data-role="pimpinan"><?= htmlspecialchars($data['pimpinan']) ?></div>
    </div>

    <div class="connector-vertical"></div>

    <div class="box">
      <h3>Ketua Komite PPI</h3>
      <div class="name" contenteditable="true" data-role="ketua"><?= htmlspecialchars($data['ketua']) ?></div>
    </div>

    <div class="connector-vertical"></div>

    <div class="branch">
      <div class="box">
        <h3>Sekretaris Komite PPI</h3>
        <div class="name" contenteditable="true" data-role="sekretaris"><?= htmlspecialchars($data['sekretaris']) ?></div>
      </div>

      <div class="box">
        <h3>IPCD (Doctor)</h3>
        <div class="name" contenteditable="true" data-role="ipcd"><?= htmlspecialchars($data['ipcd']) ?></div>
      </div>

      <div class="box">
        <h3>IPCN (Nurse)</h3>
        <div class="name" contenteditable="true" data-role="ipcn"><?= htmlspecialchars($data['ipcn']) ?></div>
      </div>

      <div class="group" id="ipclnGroup">
        <h4>IPCLN (Link Nurse)</h4>
      </div>

      <div class="group" id="pjGroup">
        <h4>Penanggung Jawab Unit</h4>
      </div>
    </div>
  </div>
</main>

<footer>© 2025 Komite PPI PHBW — Struktur sesuai PMK No. 27 Tahun 2017</footer>

<script>
const struktur = <?= json_encode($data) ?>;
const ipclnGroup = document.getElementById('ipclnGroup');
const pjGroup = document.getElementById('pjGroup');

function renderAnggota() {
  const ipcln = struktur.ipcln ? JSON.parse(struktur.ipcln) : [];
  const pj = struktur.pj ? JSON.parse(struktur.pj) : [];

  ipcln.forEach(nama => buatAnggota('ipcln', nama));
  pj.forEach(nama => buatAnggota('pj', nama));

  buatTombolTambah('ipcln');
  buatTombolTambah('pj');
}

function buatAnggota(role, nama) {
  const group = role === 'ipcln' ? ipclnGroup : pjGroup;
  const div = document.createElement('div');
  div.className = 'member';
  div.contentEditable = true;
  div.textContent = nama;
  div.dataset.role = role;
  div.addEventListener('input', simpanData);
  group.appendChild(div);
}

function buatTombolTambah(role) {
  const group = role === 'ipcln' ? ipclnGroup : pjGroup;
  const btn = document.createElement('button');
  btn.className = 'add-btn';
  btn.textContent = role === 'ipcln' ? '+ Tambah IPCLN' : '+ Tambah PJ Unit';
  btn.onclick = () => {
    buatAnggota(role, '[Nama Baru]');
    simpanData();
  };
  group.appendChild(btn);
}

function simpanData() {
  const data = {
    pimpinan: document.querySelector('[data-role="pimpinan"]').textContent,
    ketua: document.querySelector('[data-role="ketua"]').textContent,
    sekretaris: document.querySelector('[data-role="sekretaris"]').textContent,
    ipcd: document.querySelector('[data-role="ipcd"]').textContent,
    ipcn: document.querySelector('[data-role="ipcn"]').textContent,
    ipcln: JSON.stringify([...document.querySelectorAll('[data-role="ipcln"]')].map(e => e.textContent)),
    pj: JSON.stringify([...document.querySelectorAll('[data-role="pj"]')].map(e => e.textContent))
  };

  fetch('struktur_simpan.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify(data)
  });
}

document.querySelectorAll('.name').forEach(el => el.addEventListener('input', simpanData));
renderAnggota();
</script>
</body>
</html>
