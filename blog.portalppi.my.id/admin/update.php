<?php
include '../koneksi.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Ambil data lama (untuk hapus gambar lama jika diganti)
$q = mysqli_query($conn, "SELECT * FROM posts WHERE id=$id");
if (mysqli_num_rows($q) == 0) {
  echo "Data tidak ditemukan.";
  exit;
}
$row = mysqli_fetch_assoc($q);

// Ambil input
$judul = $_POST['judul'];
$kategori = $_POST['kategori'];
$isi = $_POST['isi'];

// Proses upload gambar jika ada file baru
$gambarName = $row['gambar']; // default tetap gambar lama
if (!empty($_FILES['gambar']['name'])) {
  $ext = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
  $gambarName = uniqid() . '.' . $ext;
  $target = '../uploads/' . $gambarName;

  // Upload file baru
  if (move_uploaded_file($_FILES['gambar']['tmp_name'], $target)) {
    // Hapus gambar lama
    if (!empty($row['gambar']) && file_exists('../uploads/' . $row['gambar'])) {
      unlink('../uploads/' . $row['gambar']);
    }
  } else {
    echo "Upload gambar gagal.";
    exit;
  }
}

// Update data ke database
$stmt = mysqli_prepare($conn, "UPDATE posts SET judul=?, kategori=?, konten=?, gambar=? WHERE id=?");
mysqli_stmt_bind_param($stmt, "ssssi", $judul, $kategori, $isi, $gambarName, $id);

if (mysqli_stmt_execute($stmt)) {
  header("Location: dashboard.php?status=updated");
  exit;
}

else {
  echo "Gagal memperbarui data: " . mysqli_error($conn);
}
?>
