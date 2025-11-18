<?php
// ===========================================================
// KONFIGURASI KONEKSI DATABASE - PORTAL PPI
// ===========================================================

$host = "localhost";                 // host database
$user = "porx9725_ppi_user";         // username database
$pass = "Ppi@2025!";                 // password database
$db   = "porx9725_myppi";            // nama database

// ===========================================================
// Membuat koneksi ke database
// ===========================================================
$koneksi = mysqli_connect($host, $user, $pass, $db);

// ===========================================================
// Mengecek apakah koneksi berhasil
// ===========================================================
if (!$koneksi) {
    // Jika gagal, tampilkan pesan error ke log, bukan ke layar
    error_log("Koneksi database gagal: " . mysqli_connect_error());
    die("<h3 style='color:red; text-align:center; margin-top:50px;'>⚠️ Tidak dapat terhubung ke database.<br>Silakan hubungi administrator sistem.</h3>");
}

// Opsional: set charset agar kompatibel dengan karakter Indonesia
mysqli_set_charset($koneksi, "utf8mb4");

// ===========================================================
// ✅ Tambahan agar file baru & lama tetap kompatibel
// ===========================================================
$conn = $koneksi;
?>
