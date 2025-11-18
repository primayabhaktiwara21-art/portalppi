    <aside class="sidebar" id="sb">
      <div class="brand"><span class="dot"></span><div class="name">PPI PHBW</div></div>
      <nav class="nav">
        <div class="section">Menu Utama</div>
        <a class="root-link active" href="/dashboard.php">🏠 Dashboard</a>

        <details data-type="reg">
          <summary>📄 Regulasi <span class="chev">▶</span></summary>
          <ul>
            <li><a href="/regulasi/referensi.php">Referensi</a></li>
            <li><a href="/regulasi/regulasi.php">SPO, Pedoman, Panduan</a></li>
            <li><a href="/regulasi/mou.php">MOU & Perizinan</a></li>
          </ul>
        </details>

        <details data-type="komite">
          <summary>👥 Komite PPI <span class="chev">▶</span></summary>
          <ul>
            <li><a href="/komite/kalender.php">Kalender PPI</a></li>
            <li><a href="/komite/sk.php">SK Komite</a></li>
            <li><a href="/komite/struktur.php">Struktur Komite</a></li>
            <li><a href="/komite/program.php">Program Komite</a></li>
            <li><a href="/komite/umanf.php">Uman Rapat</a></li>
          </ul>
        </details>

        <details data-type="surv">
          <summary>🧪 Surveilans <span class="chev">▶</span></summary>
          <ul>
            <li><a href="/surveilance/surveilancehais.php">HAIs (VAP, ISAK, IADP, IDO)</a></li>
            <li><a href="/surveilance/antibiotik.php">Antibiotik & MDRO</a></li>
            <li><a href="/surveilance/emerging.php">Infeksi Emerging</a></li>
          </ul>
        </details>

        <details data-type="audit">
          <summary>📊 Audit dan Supervisi <span class="chev">▶</span></summary>
          <ul>
            <li><a href="/audit/kebersihantangan.php">Cuci Tangan</a></li>
            <li><a href="/audit/audit_apd.php">APD</a></li>
            <li><a href="/audit/audit_unit.php">Audit Unit & Fasilitas</a></li>
          </ul>
        </details>

        <details data-type="diklat">
          <summary>🎓 Diklat / Pelatihan <span class="chev">▶</span></summary>
          <ul>
            <li><a href="/diklat/jadwalpelatihan.php">Jadwal</a></li>
            <li><a href="/diklat/pelatihan_terlaksana.php">Pelaksanaan</a></li>
            <li><a href="/diklat/sertifikat.php">Sertifikat</a></li>
            <li><a href="/diklat/materi.php">Materi</a></li>
          </ul>
        </details>

        <details data-type="doc">
          <summary>📁 Dokumen / Media <span class="chev">▶</span></summary>
          <ul>
            <li><a href="/dokumen/atk-formulir.php">Formulir & Brosur</a></li>
            <li><a href="/dokumen/foto_video.php">Foto & Video</a></li>
          </ul>
        </details>

        <details data-type="lap">
          <summary>📋 Laporan PPI <span class="chev">▶</span></summary>
          <ul>
            <li><a href="/laporan/lap_bulanan.php">Bulanan</a></li>
            <li><a href="/laporan/lap_tahuanan.php">Quartal & Tahunan</a></li>
            <li><a href="/laporan/lap_icraprogram.php">ICRA Program</a></li>
            <li><a href="/laporan/hasilkultur.php">Hasil Kultur</a></li>
          </ul>
        </details>
        
        <details data-type="drive">
          <summary>☁️ Drive PPI <span class="chev">▶</span></summary>
          <ul>
            <li><a href="/drive/drive.php">DRIVE PPI</a></li>

          </ul>
        </details>

        <div class="section">Lainnya</div>
        <ul style="list-style:none; padding:0; margin:0;">
          <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
          <li>
            <a class="root-link" href="users.php">👥 Manajemen User</a>
          </li>
          <?php endif; ?>
          <li>
            <a class="root-link" href="#logout">🚪 Logout</a>
          </li>
        </ul>

      </nav>

      <div class="profile">
        <div class="avatar">A</div>
        <div><b>Administrator</b><br><small>ppi.bwp@primayahospital.com</small></div>
      </div>
    </aside>
