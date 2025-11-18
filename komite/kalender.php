<?php
include_once '../koneksi.php';
include "../cek_akses.php";
$conn = $koneksi;

// ======================================
// TAMBAH ACARA
// ======================================
if (isset($_POST['simpan'])) {
  $judul = mysqli_real_escape_string($conn, $_POST['judul']);
  $kategori = $_POST['kategori'];
  $tanggal = $_POST['tanggal'];
  $waktu = $_POST['waktu'] ?: null;
  $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']);

  if ($judul && $tanggal) {
    mysqli_query($conn, "INSERT INTO tb_kalender (judul, kategori, tanggal, waktu, keterangan)
                         VALUES ('$judul','$kategori','$tanggal','$waktu','$keterangan')");
    echo "<script>alert('✅ Acara berhasil disimpan!');window.location='kalender.php';</script>";
    exit;
  }
}

// ======================================
// HAPUS ACARA
// ======================================
if (isset($_GET['hapus'])) {
  $id = $_GET['hapus'];
  mysqli_query($conn, "DELETE FROM tb_kalender WHERE id='$id'");
  echo "<script>alert('🗑️ Acara berhasil dihapus!');window.location='kalender.php';</script>";
  exit;
}

// ======================================
// AMBIL SEMUA DATA
// ======================================
$events = [];
$res = mysqli_query($conn, "SELECT * FROM tb_kalender ORDER BY tanggal ASC");
while ($r = mysqli_fetch_assoc($res)) {
  $events[] = $r;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>Kalender PPI | PPI PHBW</title>
<style>
  :root {
    --primary:#1a2a80;
    --secondary:#3b49df;
    --bg:#f4f6fb;
    --card:#ffffff;
    --text:#222;
    --muted:#6b7280;
    --radius:10px;
    --shadow:0 6px 18px rgba(20,24,66,0.08);
  }

  body {font-family:'Poppins',sans-serif;margin:0;background:var(--bg);color:var(--text);}
  header {background:var(--primary);color:white;padding:15px 25px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;}
  header h1 {font-size:18px;margin:0;}
  a.btn {padding:8px 14px;border-radius:6px;color:white;text-decoration:none;}
  .btn-dashboard {background:var(--secondary);}
  .btn-dashboard:hover {background:#2531a1;}
  main {padding:20px;max-width:1200px;margin:auto;}

  .controls {display:flex;justify-content:space-between;align-items:center;margin-bottom:15px;flex-wrap:wrap;gap:10px;}
  .month {font-size:20px;font-weight:600;}
  .navs button {background:#e5e8ff;border:none;border-radius:8px;padding:6px 12px;margin:0 3px;cursor:pointer;font-size:16px;}
  .calendar {display:grid;grid-template-columns:repeat(7,1fr);gap:8px;}
  .weekday {text-align:center;font-weight:600;color:var(--muted);font-size:13px;}
  .day {background:white;border-radius:10px;min-height:90px;padding:6px;border:1px solid #e6e9f2;position:relative;box-shadow:var(--shadow);cursor:pointer;transition:transform .1s;}
  .day:hover {transform:scale(1.02);}
  .day.inactive {opacity:0.45;}
  .dateNum {font-size:16px;font-weight:600;position:absolute;top:6px;right:10px;}
  .event {padding:4px 6px;border-radius:6px;font-size:12px;color:white;margin-top:22px;}

  /* Tombol Tambah */
  .add-btn {
    background:#16a34a;
    color:white;
    border:none;
    border-radius:8px;
    padding:10px 16px;
    font-weight:600;
    display:flex;
    align-items:center;
    gap:6px;
    cursor:pointer;
    box-shadow:0 2px 6px rgba(0,0,0,.15);
  }
  .add-btn:hover {background:#15803d;}

  /* TABEL */
  .table-wrapper {overflow-x:auto;margin-top:20px;background:white;border-radius:10px;box-shadow:var(--shadow);}
  table {width:100%;border-collapse:collapse;min-width:700px;}
  th,td {padding:10px;border-bottom:1px solid #eee;text-align:left;font-size:14px;}
  th {background:var(--secondary);color:white;position:sticky;top:0;}
  tr:hover {background:#f0f7ff;}
  .btn-delete {background:#ff4444;color:white;border:none;padding:6px 10px;border-radius:6px;cursor:pointer;}

  /* MODAL */
  .modal {
    display:none;
    position:fixed;
    top:0;left:0;width:100%;height:100%;
    background:rgba(0,0,0,0.4);
    backdrop-filter:blur(3px);
    justify-content:center;
    align-items:center;
    z-index:100;
  }
  .modal-content {
    background:white;
    padding:25px;
    border-radius:12px;
    width:90%;
    max-width:450px;
    box-shadow:var(--shadow);
    animation:fadeUp .3s ease;
  }
  @keyframes fadeUp{from{transform:translateY(20px);opacity:0;}to{transform:translateY(0);opacity:1;}}
  .modal h2{text-align:center;color:var(--primary);margin-bottom:10px;}
  label{font-weight:600;font-size:14px;margin-top:10px;display:block;}
  input,select,textarea{
    width:100%;padding:10px;border-radius:8px;border:1px solid #ccc;margin-top:5px;
  }
  .btn-save{
    background:#2563eb;color:white;padding:10px;border:none;border-radius:8px;width:100%;margin-top:15px;font-weight:600;cursor:pointer;
  }
  .btn-cancel{
    background:#6b7280;color:white;padding:10px;border:none;border-radius:8px;width:100%;margin-top:8px;font-weight:600;cursor:pointer;
  }

  @media (max-width:768px){
    .calendar{grid-template-columns:repeat(7,1fr);}
    table{font-size:12px;}
    .add-btn{width:100%;justify-content:center;}
  }
  @media (max-width:500px){
    .calendar{grid-template-columns:repeat(4,1fr);}
  }
</style>
</head>
<body>

<header>
  <h1>📅 Kalender PPI</h1>
  <a href="/dashboard.php" class="btn btn-dashboard">🏠 Dashboard</a>
</header>

<main>
  <div class="controls">
    <div>
      <button id="prevBtn">‹</button>
      <span class="month" id="monthLabel">Bulan Ini</span>
      <button id="nextBtn">›</button>
    </div>
    <button class="add-btn" id="openModal">➕ Tambah Acara</button>
  </div>

  <div id="calendar" class="calendar"></div>

  <!-- MODAL FORM -->
  <div class="modal" id="modalForm">
    <div class="modal-content">
      <h2>Tambah Acara</h2>
      <form method="POST">
        <label>Judul Acara</label>
        <input type="text" name="judul" placeholder="Contoh: Rapat Tim PPI" required>

        <label>Kategori</label>
        <select name="kategori">
          <option>Audit Internal</option>
          <option>Audit External</option>
          <option>Supervisi</option>
          <option>Rapat Rutin</option>
          <option>Pelatihan</option>
          <option>Laporan Bulanan</option>
        </select>

        <label>Tanggal</label>
        <input type="date" name="tanggal" required>

        <label>Waktu (opsional)</label>
        <input type="time" name="waktu">

        <label>Keterangan</label>
        <textarea name="keterangan" placeholder="Tuliskan lokasi atau detail acara..."></textarea>

        <button type="submit" class="btn-save" name="simpan">💾 Simpan</button>
        <button type="button" class="btn-cancel" id="closeModal">❌ Batal</button>
      </form>
    </div>
  </div>

  <!-- TABEL EVENT -->
  <h3 style="margin-top:40px;">📋 Daftar Acara</h3>
  <div class="table-wrapper">
    <table>
      <thead>
        <tr>
          <th>No</th><th>Tanggal</th><th>Judul</th><th>Kategori</th><th>Waktu</th><th>Keterangan</th><th>Aksi</th>
        </tr>
      </thead>
      <tbody id="eventTable">
        <?php
        if (count($events) > 0) {
          $no=1;
          foreach ($events as $e) {
            echo "<tr data-date='{$e['tanggal']}'>
                    <td>$no</td>
                    <td>{$e['tanggal']}</td>
                    <td>{$e['judul']}</td>
                    <td>{$e['kategori']}</td>
                    <td>".($e['waktu'] ?: "-")."</td>
                    <td>{$e['keterangan']}</td>
                    <td><a href='?hapus={$e['id']}' onclick=\"return confirm('Hapus acara ini?')\" class='btn-delete'>Hapus</a></td>
                  </tr>";
            $no++;
          }
        } else {
          echo "<tr><td colspan='7' align='center'>Belum ada acara.</td></tr>";
        }
        ?>
      </tbody>
    </table>
  </div>
</main>

<script>
const events = <?php echo json_encode($events); ?>;
const cal = document.getElementById("calendar");
const monthLabel = document.getElementById("monthLabel");
let date = new Date();

function renderCalendar(){
  const year = date.getFullYear(), month = date.getMonth();
  monthLabel.textContent = date.toLocaleString('id-ID',{month:'long',year:'numeric'});
  const first = new Date(year, month, 1).getDay();
  const lastDate = new Date(year, month+1, 0).getDate();
  const days = [];

  for(let i=0;i<first;i++) days.push({num:'',inactive:true});
  for(let i=1;i<=lastDate;i++) days.push({num:i, inactive:false});

  cal.innerHTML='';
  const weekdays = ['Min','Sen','Sel','Rab','Kam','Jum','Sab'];
  weekdays.forEach(d=>{
    const h=document.createElement('div');
    h.className='weekday';
    h.textContent=d;
    cal.appendChild(h);
  });

  days.forEach(d=>{
    const cell=document.createElement('div');
    cell.className='day'+(d.inactive?' inactive':'');
    cell.innerHTML=d.num?`<div class='dateNum'>${d.num}</div>`:'';
    const iso=`${year}-${String(month+1).padStart(2,'0')}-${String(d.num).padStart(2,'0')}`;
    const dayEvents=events.filter(e=>e.tanggal===iso);
    dayEvents.forEach(e=>{
      const tag=document.createElement('div');
      tag.className='event';
      tag.style.background=getColor(e.kategori);
      tag.textContent=e.judul;
      cell.appendChild(tag);
    });
    if(d.num){
      cell.onclick=()=>highlightDate(iso);
    }
    cal.appendChild(cell);
  });
}

function getColor(cat){
  const colors={"Audit Internal":"#1f77b4","Audit External":"#ff7f0e","Supervisi":"#2ca02c","Rapat Rutin":"#9467bd","Pelatihan":"#d62728","Laporan Bulanan":"#8c564b"};
  return colors[cat]||"#555";
}

function highlightDate(tanggal){
  document.querySelectorAll('#eventTable tr').forEach(row=>{
    if(row.dataset.date===tanggal) row.style.background='#dbeafe';
    else row.style.background='';
  });
  const targetRow=document.querySelector(`#eventTable tr[data-date="${tanggal}"]`);
  if(targetRow) targetRow.scrollIntoView({behavior:'smooth',block:'center'});
}

document.getElementById("prevBtn").onclick=()=>{date.setMonth(date.getMonth()-1);renderCalendar();};
document.getElementById("nextBtn").onclick=()=>{date.setMonth(date.getMonth()+1);renderCalendar();};

// modal control
const modal=document.getElementById('modalForm');
document.getElementById('openModal').onclick=()=>modal.style.display='flex';
document.getElementById('closeModal').onclick=()=>modal.style.display='none';
window.onclick=(e)=>{if(e.target==modal)modal.style.display='none';};

renderCalendar();
</script>
</body>
</html>
