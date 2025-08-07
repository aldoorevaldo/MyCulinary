<?php
session_start();
if (isset($_SESSION['role'])) {
  header("Location: dashboard/" . $_SESSION['role'] . ".php");
  exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Login | MyCulinary</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="assets/css/login.css">
  <link rel="shortcut icon" href="assets/img/logo-brand.png">
</head>
<body>
  <div class="login-container">
    <div class="login-box">
       <img src="assets/img/logo-brand.png" alt="" class="login-avatar">

      <h2 class="login-title">Login MyCulinary</h2>
      
      <?php if (isset($_GET['error'])): ?>
        <p class="error-message">Username atau password salah.<br>Mohon coba lagi.</p>
      <?php endif; ?>

      <form action="backend/index.php?action=login" method="POST">
        <input type="text" name="username" placeholder="Nama pengguna" required>
        <input type="password" name="password" placeholder="Kata sandi" required>
        <button type="submit">Masuk</button>
      </form>
    </div>
  </div>
</body>
</html>