<?php
session_start();
$id_meja = $_GET['meja'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Menu | MyCulinary</title>
  <link rel="shortcut icon" href="assets/img/logo-brand.png">
  <link rel="stylesheet" href="assets/css/menus.css">
  <script src="assets/js/menu.js"></script>
</head>
<body class="menu-body">
  <div class="container">
    <h1 style="color:white;">Our Menu</h1>

    <label style="color:white;">Nomor Meja:</label>
    <input type="number" id="id_meja" value="<?= htmlspecialchars($id_meja) ?>" readonly />

    <h2 style="color:white; text-align:center;">Makanan</h2>
    <div id="menu_makanan" class="menu-grid"></div>

    <h2 style="color:white; text-align:center;">Minuman</h2>
    <div id="menu_minuman" class="menu-grid"></div>

    <h2 style="color:white; text-align:center;">Dessert</h2>
    <div id="menu_dessert" class="menu-grid"></div>

    <div class="submit-container">
      <button onclick="kirimPesanan()" class="btn-submit">
        🧾 Kirim Pesanan
      </button>
    </div>

  </div>
</body>
</html>
