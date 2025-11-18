<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once 'koneksi.php';

// Proteksi halaman
if (!isset($_SESSION['username'])) {
    header("Location: /login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=3.0"/>
  <title>Dashboard PPI PHBW</title>
  
  <!-- === Link CSS eksternal === -->
  <link rel="stylesheet" href="/assets/css/utama.css">
  <link rel="stylesheet" href="/assets/css/dashboard.css">



</head>
<body>
<div class="layout">
