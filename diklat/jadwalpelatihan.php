<?php
include_once '../koneksi.php';
include "../cek_akses.php";

// ===============================
// SIMPAN DATA KE DATABASE
// ===============================
if (isset($_POST['submit'])) {
    // Tangkap data dari form
    $tanggal = $_POST['tanggal'];
    $nama = $_POST['nama'];
    $penyelenggara = $_POST['penyelenggara'];
    $tempat = $_POST['tempat'];
    $keterangan = $_POST['keterangan'];

    // Pastikan format tanggal sesuai MySQL (YYYY-MM-DD)
    $tanggal_baru = date('Y-m-d', strtotime($tanggal));

    // Query simpan data
    $query = "INSERT INTO tb_pelatihan (tanggal, nama, penyelenggara, tempat, keterangan)
              VALUES ('$tanggal_baru', '$nama', '$penyelenggara', '$tempat', '$keterangan')";

    if (mysqli_query($conn, $query)) {
        echo "<script>
            alert('✅ Jadwal pelatihan berhasil disimpan!');
            window.location.href='jadwalpelatihan.php';
        </script>";
        exit;
    } else {
        echo '<pre style="color:white;background:black;padding:20px;">';
        echo '❌ Query Error: ' . mysqli_error($conn) . "\n";
        echo 'Query: ' . $query;
        echo '</pre>';
        exit;
    }
}

// ===============================
// HAPUS DATA DARI DATABASE
// ===============================
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $hapus = mysqli_query($conn, "DELETE FROM tb_pelatihan WHERE id='$id'");

    if ($hapus) {
        echo "<script>
            alert('🗑️ Data berhasil dihapus!');
            window.location.href='jadwalpelatihan.php';
        </script>";
        exit;
    } else {
        echo "<script>
            alert('❌ Gagal menghapus data!');
            window.history.back();
        </script>";
        exit;
    }
}
?>



<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Jadwal Pelatihan PPI | PPI PHBW</title>
  <style>
    :root {
      --primary: #1a2a80;
      --secondary: #3b49df;
      --bg: #f7f8ff;
      --card: #ffffff;
      --border: #dce0f0;
    }

    body {
      font-family: "Segoe UI", sans-serif;
      background-color: var(--bg);
      color: #222;
      margin: 0;
    }

    header {
      background-color: var(--primary);
      color: white;
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 15px 30px;
      font-size: 1.3em;
      font-weight: bold;
    }

    .dashboard-btn {
      background-color: #3b49df;
      color: white;
      border: none;
      padding: 8px 16px;
      border-radius: 8px;
      font-size: 0.9em;
      cursor: pointer;
      transition: background 0.2s;
    }

    .dashboard-btn:hover {
      background-color: #2832b8;
    }

    nav {
      display: flex;
      justify-content: center;
      background-color: var(--secondary);
      flex-wrap: wrap;
    }

    nav button {
      background: none;
      color: white;
      border: none;
      padding: 12px 20px;
      font-size: 1em;
      cursor: pointer;
      transition: background 0.2s;
    }

    nav button:hover,
    nav button.active {
      background-color: #16225a;
    }

    main {
      max-width: 1000px;
      margin: 30px auto;
      background: var(--card);
      border-radius: 12px;
      padding: 25px 30px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.08);
      border: 1px solid var(--border);
    }

    h2 {
      color: var(--primary);
      border-bottom: 2px solid var(--secondary);
      padding-bottom: 5px;
    }

    label {
      display: block;
      margin-top: 12px;
      font-weight: 600;
    }

    input, textarea {
      width: 100%;
      padding: 10px;
      border-radius: 8px;
      border: 1px solid var(--border);
      margin-top: 5px;
      box-sizing: border-box;
      font-size: 1em;
    }

    button.save {
      margin-top: 20px;
      background-color: var(--secondary);
      color: white;
      padding: 10px 18px;
      border: none;
      border-radius: 8px;
      font-size: 1em;
      font-weight: bold;
      cursor: pointer;
    }

    button.save:hover {
      background-color: #2f3cbf;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }

    th, td {
      border: 1px solid var(--border);
      padding: 8px;
      text-align: center;
    }

    th {
      background-color: var(--primary);
      color: white;
    }

    tr:nth-child(even) {
      background-color: #f1f4ff;
    }

    .edit-btn { background-color: #f4b400; color: white; border: none; border-radius: 5px; padding: 5px 10px; cursor: pointer; }
    .delete-btn { background-color: #d93025; color: white; border: none; border-radius: 5px; padding: 5px 10px; cursor: pointer; }
    .edit-btn:hover { background-color: #db9a00; }
    .delete-btn:hover { background-color: #b32118; }

    footer {
      text-align: center;
      padding: 20px;
      font-size: 0.9em;
      color: gray;
    }

    .tab { display: none; }
    .tab.active { display: block; }

    /* Kalender */
    .calendar {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }

    .calendar th {
      background: var(--secondary);
      color: white;
      padding: 10px;
    }

    .calendar td {
      width: 14.2%;
      height: 90px;
      vertical-align: top;
      border: 1px solid var(--border);
      padding: 6px;
      font-size: 0.9em;
    }

    .calendar .today {
      background-color: #e7f0ff;
      border: 2px solid var(--secondary);
    }

    .event {
      background-color: #3b49df;
      color: white;
      padding: 2px 5px;
      border-radius: 4px;
      display: block;
      margin-top: 3px;
      font-size: 0.8em;
    }

    @media (max-width: 768px) {
      main { padding: 15px; width: 95%; }
      table, .calendar { display: block; overflow-x: auto; white-space: nowrap; }
      th, td { font-size: 0.85em; padding: 6px; }
      h2 { font-size: 1.1em; }
      button.save, .dashboard-btn, nav button { font-size: 0.85em; padding: 8px 10px; }
      .calendar td { height: 70px; font-size: 0.8em; }
    }
  </style>
</head>
<body>
  <header>
    <div>📅 Jadwal Pelatihan PPI | PPI PHBW</div>
    <button class="dashboard-btn" onclick="kembaliDashboard()">🏠 Kembali ke Dashboard</button>
  </header>

  <nav>
    <button class="active" onclick="showTab('input')">🧾 Input Jadwal</button>
    <button onclick="showTab('rekap')">📋 Daftar Jadwal</button>
    <button onclick="showTab('kalender')">📆 Kalender Pelatihan</button>
  </nav>

  <main>
    <!-- TAB INPUT -->
    <div id="input" class="tab active">
      <h2>🧾 Tambah / Edit Jadwal Pelatihan</h2>
      <form method="POST" action="">
        <label>Tanggal Pelatihan</label>
        <input type="date" name="tanggal" required>

        <label>Nama Pelatihan</label>
        <input type="text" name="nama" placeholder="Contoh: Pelatihan Hand Hygiene" required>

        <label>Penyelenggara</label>
        <input type="text" name="penyelenggara" placeholder="Contoh: Komite PPI RS PHBW" required>

        <label>Tempat / Lokasi</label>
        <input type="text" name="tempat" placeholder="Contoh: Aula Utama RS PHBW" required>

        <label>Keterangan</label>
        <textarea name="keterangan" rows="2" placeholder="Opsional: Narasumber atau catatan tambahan"></textarea>

        <button type="submit" class="save" name="submit">💾 Simpan Jadwal</button>
      </form>
    </div>

    <!-- TAB REKAP -->
    <div id="rekap" class="tab">
      <h2>📋 Daftar Jadwal Pelatihan</h2>
      <table>
        <thead>
          <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>Nama Pelatihan</th>
            <th>Penyelenggara</th>
            <th>Tempat</th>
            <th>Keterangan</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $no = 1;
          $result = mysqli_query($conn, "SELECT * FROM tb_pelatihan ORDER BY tanggal DESC");
          if (mysqli_num_rows($result) > 0) {
              while ($row = mysqli_fetch_assoc($result)) {
                  echo "<tr>
                          <td>{$no}</td>
                          <td>{$row['tanggal']}</td>
                          <td>{$row['nama']}</td>
                          <td>{$row['penyelenggara']}</td>
                          <td>{$row['tempat']}</td>
                          <td>{$row['keterangan']}</td>
                          <td>
                            <button class='delete-btn' onclick=\"hapusData({$row['id']})\">🗑️</button>
                          </td>
                        </tr>";
                  $no++;
              }
          } else {
              echo "<tr><td colspan='7' style='text-align:center;'>Belum ada data pelatihan</td></tr>";
          }
          ?>
        </tbody>
      </table>
    </div>

    <!-- TAB KALENDER -->
    <div id="kalender" class="tab">
      <h2>📆 Kalender Pelatihan</h2>
      <div id="calendar"></div>
    </div>
  </main>

  <footer>© 2025 PPI PHBW — Jadwal Pelatihan</footer>

<script>
  // ==============================
  // Navigasi Tab
  // ==============================
  function showTab(tabId) {
    document.querySelectorAll('nav button').forEach(btn => btn.classList.remove('active'));
    document.querySelectorAll('.tab').forEach(tab => tab.classList.remove('active'));
    document.querySelector(`nav button[onclick="showTab('${tabId}')"]`).classList.add('active');
    document.getElementById(tabId).classList.add('active');
    if (tabId === 'kalender') renderCalendar();
  }

  function hapusData(id) {
    if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
      window.location.href = '?hapus=' + id;
    }
  }

  function kembaliDashboard() {
    window.location.href = "/dashboard.php";
  }

  // ==============================
  // Data Pelatihan dari PHP
  // ==============================
  const pelatihanData = <?php
    $dataKalender = [];
    $query = mysqli_query($conn, "SELECT * FROM tb_pelatihan");
    while ($r = mysqli_fetch_assoc($query)) {
      $dataKalender[] = $r;
    }
    echo json_encode($dataKalender);
  ?>;

  // ==============================
  // Render Kalender Dinamis
  // ==============================
  let currentMonth = new Date().getMonth();
  let currentYear = new Date().getFullYear();

  function renderCalendar() {
    const monthNames = ["Januari","Februari","Maret","April","Mei","Juni","Juli","Agustus","September","Oktober","November","Desember"];
    const firstDay = new Date(currentYear, currentMonth, 1);
    const lastDay = new Date(currentYear, currentMonth + 1, 0);
    const today = new Date();

    let calendarHTML = `
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:15px;">
        <button onclick="changeMonth(-1)" style="background:#3b49df;color:white;border:none;padding:6px 12px;border-radius:6px;cursor:pointer;">⬅️</button>
        <h3 style="margin:0;color:#1a2a80;">${monthNames[currentMonth]} ${currentYear}</h3>
        <button onclick="changeMonth(1)" style="background:#3b49df;color:white;border:none;padding:6px 12px;border-radius:6px;cursor:pointer;">➡️</button>
      </div>
      <table class="calendar">
        <thead>
          <tr>
            <th>Minggu</th><th>Senin</th><th>Selasa</th><th>Rabu</th><th>Kamis</th><th>Jumat</th><th>Sabtu</th>
          </tr>
        </thead>
        <tbody>
          <tr>
    `;

    // Isi kosong sebelum tanggal 1
    let dayOfWeek = firstDay.getDay();
    for (let i = 0; i < dayOfWeek; i++) {
      calendarHTML += "<td></td>";
    }

    // Isi tanggal
    for (let date = 1; date <= lastDay.getDate(); date++) {
      const dateStr = `${currentYear}-${String(currentMonth + 1).padStart(2,"0")}-${String(date).padStart(2,"0")}`;
      const events = pelatihanData.filter(e => e.tanggal === dateStr);

      const isToday = today.getDate() === date && 
                      today.getMonth() === currentMonth && 
                      today.getFullYear() === currentYear;

      calendarHTML += `<td class="${isToday ? 'today' : ''}">
        <strong>${date}</strong>`;

      events.forEach(e => {
        calendarHTML += `<div class="event" title="${e.keterangan || ''}">
          ${e.nama}<br>
          <small style="opacity:0.8;">${e.tempat}</small>
        </div>`;
      });

      calendarHTML += "</td>";

      if ((dayOfWeek + date) % 7 === 0) calendarHTML += "</tr><tr>";
    }

    calendarHTML += "</tr></tbody></table>";
    document.getElementById("calendar").innerHTML = calendarHTML;
  }

  function changeMonth(offset) {
    currentMonth += offset;
    if (currentMonth < 0) {
      currentMonth = 11;
      currentYear--;
    } else if (currentMonth > 11) {
      currentMonth = 0;
      currentYear++;
    }
    renderCalendar();
  }

  // Auto render kalender pertama kali
  renderCalendar();
</script>

</body>
</html>
