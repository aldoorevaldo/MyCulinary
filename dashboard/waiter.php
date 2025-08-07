<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'waiter') {
  header('Location: ../index.php');
  exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Pelayan | MyCulinary</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../assets/css/waiters.css">
  <link rel="shortcut icon" href="../assets/img/logo-brand.png">
  <script src="../assets/js/waiter.js"></script>
</head>
<body>
  <div class="container">
    <header>
      <h1>Selamat Datang Pelayan</h1>
    </header>

    <section>
      <h2>Daftar Semua Meja</h2>
      <div id="mejaKosong" class="status-meja"></div>
    </section>

    <section>
      <h2>Daftar Pesanan</h2>
      <div id="daftarPesanan"></div>
    </section>
    <div class="logout-container">
      <a href="../logout.php" class="btn logout">Logout</a>
    </div>
  </div>
</body>
</html>
