<?php
$host = "localhost";
$user = "porx9725_smarttekno";
$pass = "Sm@rtTekno#2025!";
$db   = "porx9725_smarttekno";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
  die("Koneksi gagal: " . mysqli_connect_error());
}
?>
