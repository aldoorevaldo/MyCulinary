<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'chef') {
  header("Location: ../index.php");
  exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Chef | MyCulinary</title>
  <link rel="stylesheet" href="../assets/css/chefs.css">
  <link rel="shortcut icon" href="../assets/img/logo-brand.png">
  <script src="../assets/js/chef.js"></script>
</head>
<body>
  <div class="container">
    <h1>Selamat Datang Chef</h1>
    <div class="top-buttons">
      <a href="tambah_menu.php" class="btn blue">+ Tambah Menu</a>
      <a href="edit_menu.php" class="btn orange">Edit Menu</a>
    </div>
    <div id="listPesanan"></div>
    <div class="logout-container">
      <a href="../logout.php" class="btn logout">Logout</a>
    </div>
  </div>
</body>
</html>
