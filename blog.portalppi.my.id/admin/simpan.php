<?php
include '../koneksi.php';

// Ambil data dari form
$judul = trim($_POST['judul']);
$kategori = trim($_POST['kategori']);
$konten = trim($_POST['isi']); // kolom di database: 'konten'

// Validasi sederhana
if (empty($judul) || empty($konten) || empty($kategori)) {
    echo "<script>alert('Judul, kategori, dan isi artikel wajib diisi.');history.back();</script>";
    exit;
}

// ==== BUAT SLUG OTOMATIS ====
function buat_slug($text) {
    $text = iconv('UTF-8', 'ASCII//TRANSLIT', $text);
    $text = preg_replace('/[^A-Za-z0-9-]+/', '-', $text);
    $text = preg_replace('/-+/', '-', $text);
    $text = strtolower(trim($text, '-'));
    return $text ?: 'artikel';
}

$slug = buat_slug($judul);

// Cek apakah slug sudah ada
$cek = mysqli_query($conn, "SELECT COUNT(*) AS jml FROM posts WHERE slug='$slug'");
$data = mysqli_fetch_assoc($cek);
if ($data['jml'] > 0) {
    $slug .= '-' . time(); // buat unik kalau sudah ada
}

// ==== UPLOAD GAMBAR ====
$gambarName = null;
if (!empty($_FILES['gambar']['name'])) {
    $ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    if (!in_array($ext, $allowed)) {
        echo "<script>alert('Format gambar tidak diizinkan. Gunakan JPG, PNG, atau WEBP.');history.back();</script>";
        exit;
    }

    $gambarName = uniqid() . '.' . $ext;
    $uploadPath = '../uploads/' . $gambarName;

    if (!move_uploaded_file($_FILES['gambar']['tmp_name'], $uploadPath)) {
        echo "<script>alert('Upload gambar gagal. Pastikan folder uploads memiliki izin tulis (CHMOD 777).');history.back();</script>";
        exit;
    }
}

// ==== SIMPAN KE DATABASE ====
// Tambahkan created_at otomatis
if ($gambarName) {
    $sql = "INSERT INTO posts (judul, slug, kategori, konten, gambar, created_at)
            VALUES (?, ?, ?, ?, ?, NOW())";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sssss", $judul, $slug, $kategori, $konten, $gambarName);
} else {
    $sql = "INSERT INTO posts (judul, slug, kategori, konten, created_at)
            VALUES (?, ?, ?, ?, NOW())";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssss", $judul, $slug, $kategori, $konten);
}

if (mysqli_stmt_execute($stmt)) {
    echo "<script>alert('Artikel berhasil disimpan!');window.location='dashboard.php';</script>";
} else {
    echo "<script>alert('Gagal menyimpan data: " . mysqli_error($conn) . "');history.back();</script>";
}
?>
