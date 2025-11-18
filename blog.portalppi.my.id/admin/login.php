<?php
session_start();
include '../koneksi.php';
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = $_POST['username'];
    $pass = $_POST['password'];

    $stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE username = ?");
    mysqli_stmt_bind_param($stmt, "s", $user);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $u = mysqli_fetch_assoc($res);
    if ($password == $row['password']) {
        $_SESSION['admin'] = $u['username'];
        header("Location: dashboard.php");
        exit;
    } else {
        $err = "Username atau password salah.";
    }
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login Admin - Smart_tekno</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container">
  <div class="row justify-content-center mt-5">
    <div class="col-md-5">
      <div class="card p-4">
        <h3 class="mb-3">Login Admin</h3>
        <?php if($err): ?>
          <div class="alert alert-danger"><?php echo $err; ?></div>
        <?php endif; ?>
        <form method="POST">
          <div class="mb-3"><input class="form-control" name="username" placeholder="username" required></div>
          <div class="mb-3"><input class="form-control" name="password" type="password" placeholder="password" required></div>
          <button class="btn btn-primary">Login</button>
          <a class="btn btn-secondary" href="../index.php">Kembali</a>
        </form>
      </div>
    </div>
  </div>
</div>
</body>
</html>
