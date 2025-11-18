<?php
session_start();
include "koneksi.php";

// 🔒 Cek login
if (!isset($_SESSION['username'])) {
  header("Location: login.php");
  exit;
}

// ======================
// HAPUS USER
// ======================
if (isset($_GET['hapus'])) {
  $id = intval($_GET['hapus']);
  // Hapus dari tabel users dan user_access
  mysqli_query($koneksi, "DELETE FROM users WHERE id='$id'");
  mysqli_query($koneksi, "DELETE FROM user_access WHERE user_id='$id'");
  echo "<script>alert('🗑️ User berhasil dihapus.'); window.location='users.php';</script>";
  exit;
}

// ======================
// TAMBAH USER BARU
// ======================
if (isset($_POST['tambah'])) {
  $username = trim($_POST['username']);
  $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
  $role = $_POST['role'];

  // 🚫 Cegah username duplikat
  $cek = mysqli_query($koneksi, "SELECT * FROM users WHERE username='$username'");
  if (mysqli_num_rows($cek) > 0) {
    echo "<script>alert('⚠️ Username sudah digunakan!'); window.location='users.php';</script>";
    exit;
  }

  // Simpan user baru
  mysqli_query($koneksi, "INSERT INTO users (username, password, role) VALUES ('$username', '$password', '$role')");
  $user_id = mysqli_insert_id($koneksi); // Ambil ID user baru

  // Simpan hak akses
  if (!empty($_POST['akses'])) {
    foreach ($_POST['akses'] as $halaman) {
      $halaman = basename(trim(strtolower($halaman))); // Bersihkan dari ../ atau .php
      mysqli_query($koneksi, "INSERT INTO user_access (user_id, halaman, diizinkan) VALUES ('$user_id', '$halaman', 1)");
    }
  }

  echo "<script>alert('✅ User baru berhasil ditambahkan!'); window.location='users.php';</script>";
  exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Manajemen User | MyPPI</title>
<style>
:root {
  --primary: #006cb7;
  --secondary: #005999;
  --bg: #f5f8ff;
  --card: #fff;
  --border: #dce0f0;
}
body {
  font-family: "Segoe UI", sans-serif;
  background: var(--bg);
  margin: 0;
}
.container {
  max-width: 950px;
  margin: 40px auto;
  background: var(--card);
  padding: 25px;
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}
h2 {
  color: var(--primary);
  text-align: center;
  margin-top: 0;
}
.table-container {
  overflow-x: auto;
}
table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 20px;
  font-size: 0.95em;
}
th, td {
  border: 1px solid var(--border);
  padding: 10px;
  text-align: center;
}
th {
  background: var(--primary);
  color: white;
}
tr:nth-child(even) { background: #f1f4ff; }
a.btn-delete {
  color: #d93025;
  text-decoration: none;
  font-weight: bold;
}
a.btn-delete:hover { text-decoration: underline; }

form {
  margin-top: 25px;
  display: flex;
  flex-direction: column;
  gap: 10px;
}
form input, select {
  padding: 8px 10px;
  border: 1px solid var(--border);
  border-radius: 8px;
  font-size: 1em;
}
button {
  background: var(--primary);
  color: white;
  border: none;
  padding: 10px;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 600;
}
button:hover { background: var(--secondary); }

.logout {
  display: inline-block;
  margin-top: 20px;
  color: var(--primary);
  text-decoration: none;
  font-weight: bold;
}
.logout:hover { text-decoration: underline; }

/* Checkbox Akses */
.access-box {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
  gap: 8px;
  margin-top: 10px;
}
.access-box label {
  background: #f4f7ff;
  border: 1px solid #dce0f0;
  padding: 8px;
  border-radius: 6px;
  cursor: pointer;
  display: flex;
  align-items: center;
}
.access-box input { margin-right: 6px; }

@media (max-width: 768px) {
  .container { width: 90%; padding: 15px; }
  table { font-size: 0.9em; }
  button, input, select { width: 100%; }
}
</style>
</head>

<body>
<div class="container">
  <h2>👥 Manajemen Pengguna MyPPI</h2>

  <!-- TABEL USER -->
  <div class="table-container">
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Username</th>
          <th>Role</th>
          <th>Halaman yang Diizinkan</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $users = mysqli_query($koneksi, "SELECT * FROM users ORDER BY id DESC");
        if (mysqli_num_rows($users) > 0) {
          while ($row = mysqli_fetch_assoc($users)) {
            echo "<tr>";
            echo "<td>".$row['id']."</td>";
            echo "<td>".$row['username']."</td>";
            echo "<td>".$row['role']."</td>";

            // Ambil daftar izin user
            $akses = mysqli_query($koneksi, "SELECT halaman FROM user_access WHERE user_id='".$row['id']."' AND diizinkan=1");
            $halaman = [];
            while ($a = mysqli_fetch_assoc($akses)) {
              $halaman[] = ucfirst($a['halaman']);
            }

            echo "<td>".(empty($halaman) ? "<i>Tidak ada izin</i>" : implode(', ', $halaman))."</td>";

            // Proteksi akun admin utama
            if (isset($row['protected']) && $row['protected'] == 1) {
              echo "<td><span style='color:gray;'>🔒 Tidak bisa dihapus</span></td>";
            } else {
              echo "<td><a href='users.php?hapus=".$row['id']."' class='btn-delete'>🗑️ Hapus</a></td>";
            }

            echo "</tr>";
          }
        } else {
          echo "<tr><td colspan='5'>Belum ada user</td></tr>";
        }
        ?>
      </tbody>
    </table>
  </div>

  <!-- FORM TAMBAH USER -->
  <h3 style="text-align:center; margin-top:30px;">➕ Tambah User Baru</h3>
  <form method="POST">
    <input type="text" name="username" placeholder="Username" required>
    <input type="text" name="password" placeholder="Password" required>
    <select name="role" required>
      <option value="admin">Admin</option>
      <option value="petugas">Petugas</option>
    </select>

    <label><strong>Pilih Halaman yang Bisa Diakses:</strong></label>
    <div class="access-box">
      <label><input type="checkbox" name="akses[]" value="dashboard"> Dashboard</label>
      <label><input type="checkbox" name="akses[]" value="regulasi"> Regulasi</label>
      <label><input type="checkbox" name="akses[]" value="komite"> Komite PPI</label>
      <label><input type="checkbox" name="akses[]" value="surveilance"> Surveilance</label>
      <label><input type="checkbox" name="akses[]" value="audit"> Audit & Supervisi</label>
      <label><input type="checkbox" name="akses[]" value="diklat"> Diklat & Pelatihan</label>
      <label><input type="checkbox" name="akses[]" value="dokumen"> Dokumen & Formulir</label>
      <label><input type="checkbox" name="akses[]" value="laporan"> Laporan PPI</label>
      <label><input type="checkbox" name="akses[]" value="users"> Manajemen User</label>
    </div>

    <button type="submit" name="tambah">💾 Simpan User</button>
  </form>

  <div style="text-align:center;">
    <a href="dashboard.php" class="logout">⬅ Kembali ke Dashboard</a>
  </div>
</div>
</body>
</html>
