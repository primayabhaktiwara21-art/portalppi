<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}
include '../koneksi.php';
$res = mysqli_query($conn, "SELECT * FROM posts ORDER BY id DESC");
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Dashboard - Smart_tekno</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand" href="#">Admin Smart_tekno</a>
    <div>
      <a class="btn btn-outline-light" href="logout.php">Logout</a>
    </div>
  </div>
</nav>
<div class="container mt-4">
    <?php if (isset($_GET['status']) && $_GET['status'] == 'updated'): ?>
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    ✅ Artikel berhasil diperbarui!
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
<?php endif; ?>

  <a href="add.php" class="btn btn-success mb-3">Tambah Artikel</a>
  <table class="table table-striped">
    <thead><tr><th>#</th><th>Judul</th><th>Kategori</th><th>Tanggal</th><th>Aksi</th></tr></thead>
    <tbody>
      <?php while($row = mysqli_fetch_assoc($res)): ?>
      <tr>
        <td><?php echo $row['id']; ?></td>
        <td><?php echo htmlspecialchars($row['judul']); ?></td>
        <td><?php echo htmlspecialchars($row['kategori']); ?></td>
        <td><?php echo $row['created_at']; ?></td>
        <td>
          <a class="btn btn-sm btn-primary" href="edit.php?id=<?php echo $row['id']; ?>">Edit</a>
          <a class="btn btn-sm btn-danger" href="delete.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Hapus?')">Hapus</a>
        </td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
