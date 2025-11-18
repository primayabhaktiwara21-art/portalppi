<?php include_once 'header.php'; ?>
<?php include_once 'sidebar.php'; ?>


<main>
  <div class="topbar">
    <button class="hamb" id="toggleSidebar">☰</button>
    <div style="font-weight:700;color:var(--brand)">Dashboard</div>
    <div style="border:1px solid var(--line);padding:6px 10px;border-radius:999px;background:#fff">Periode: Agustus 2025</div>
  </div>

  <div class="container">
    <h2>Halo, <span style="color:var(--brand)">Administrator</span> 👋</h2>

    <section class="grid">
      <div class="card">
        <div class="kpi">
          <div class="icon" style="background:#eaf4ff;color:var(--brand)">🖐️</div>
          <div class="meta"><div class="label">Kepatuhan Cuci Tangan</div><div class="val">92.5%</div></div>
        </div>
      </div>
      <div class="card">
        <div class="kpi">
          <div class="icon" style="background:#fff7ed;color:#f59e0b">🧤</div>
          <div class="meta"><div class="label">Kepatuhan APD</div><div class="val">88%</div></div>
        </div>
      </div>
      <div class="card">
        <div class="kpi">
          <div class="icon" style="background:#fef2f2;color:#dc2626">⚕️</div>
          <div class="meta"><div class="label">Rate HAI</div><div class="val">1.7</div></div>
        </div>
      </div>
    </section>

    <section class="grid">
      <div class="card">
        <h3>📊 Audit Terakhir</h3>
        <table>
          <thead><tr><th>Tanggal</th><th>Unit</th><th>Jenis</th><th>%</th><th>Auditor</th></tr></thead>
          <tbody>
            <tr><td>2025-08-12</td><td>ICU</td><td>Cuci Tangan</td><td>92%</td><td>IPCN A</td></tr>
            <tr><td>2025-08-10</td><td>OK</td><td>APD</td><td>88%</td><td>IPCN B</td></tr>
          </tbody>
        </table>
      </div>

      <div class="card">
        <h3>📘 Regulasi Terbaru</h3>
        <a href="#">📄 SPO Cuci Tangan v3</a>
        <a href="#">📄 Panduan APD 2025</a>
        <a href="#">📄 Pedoman PPI 2025</a>
      </div>
    </section>
  </div>
</main>

<?php include_once 'footer.php'; ?>
