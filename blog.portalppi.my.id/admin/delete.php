<?php
session_start();
if (!isset($_SESSION['admin'])) { header("Location: login.php"); exit; }
include '../koneksi.php';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id) {
    // optional: hapus file gambar dulu
    $res = mysqli_query($conn, "SELECT gambar FROM posts WHERE id = $id");
    $r = mysqli_fetch_assoc($res);
    if ($r && !empty($r['gambar']) && file_exists('../uploads/'.$r['gambar'])) {
        @unlink('../uploads/'.$r['gambar']);
    }
    mysqli_query($conn, "DELETE FROM posts WHERE id = $id");
}
header("Location: dashboard.php");
exit;
