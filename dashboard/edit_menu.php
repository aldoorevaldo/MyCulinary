<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'chef') {
  header("Location: ../index.php");
  exit;
}
require '../backend/db.php';
$stmt = $pdo->query("SELECT * FROM menu ORDER BY kategori, nama_menu");
$menus = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Menu</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link rel="shortcut icon" href="../assets/img/logo-brand.png">
  <link rel="stylesheet" href="../assets/css/edit_menu.css">
  <script>
    async function updateMenu(e, id) {
      e.preventDefault();
      const form = document.getElementById('form-' + id);
      const payload = {
        id_menu: id,
        nama_menu: form.nama_menu.value,
        harga: form.harga.value,
        stok: form.stok.value,
        status_tersedia: form.status_tersedia.value,
        kategori: form.kategori.value
      };

      const res = await fetch('../backend/index.php?action=update_menu', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      });

      const data = await res.json();
      if (data.status === 'success') {
        alert('Menu berhasil diperbarui!');
      } else {
        alert('Gagal: ' + (data.message || 'Unknown error'));
      }
    }
  </script>
</head>
<body class="bg-gray-100 p-6">
  <h1 class="text-2xl font-bold mb-6">Edit Semua Menu</h1>

  <div class="space-y-6">
    <?php foreach ($menus as $menu): ?>
      <form id="form-<?= $menu['id_menu'] ?>" class="bg-white p-4 rounded shadow" onsubmit="updateMenu(event, <?= $menu['id_menu'] ?>)">
        <h2 class="text-lg font-semibold mb-2"><?= htmlspecialchars($menu['nama_menu']) ?></h2>

        <label class="block mb-1">Nama Menu:</label>
        <input type="text" name="nama_menu" value="<?= htmlspecialchars($menu['nama_menu']) ?>" class="w-full border px-2 py-1 mb-2">

        <label class="block mb-1">Harga:</label>
        <input type="number" name="harga" value="<?= $menu['harga'] ?>" class="w-full border px-2 py-1 mb-2">

        <label class="block mb-1">Stok:</label>
        <input type="number" name="stok" value="<?= $menu['stok'] ?>" class="w-full border px-2 py-1 mb-2">

        <label class="block mb-1">Status Tersedia:</label>
        <select name="status_tersedia" class="w-full border px-2 py-1 mb-2">
          <option value="tersedia" <?= $menu['status_tersedia'] === 'tersedia' ? 'selected' : '' ?>>Tersedia</option>
          <option value="habis" <?= $menu['status_tersedia'] === 'habis' ? 'selected' : '' ?>>Habis</option>
        </select>

        <label class="block mb-1">Kategori:</label>
        <select name="kategori" class="w-full border px-2 py-1 mb-4">
          <option value="makanan" <?= $menu['kategori'] === 'makanan' ? 'selected' : '' ?>>Makanan</option>
          <option value="minuman" <?= $menu['kategori'] === 'minuman' ? 'selected' : '' ?>>Minuman</option>
          <option value="dessert" <?= $menu['kategori'] === 'dessert' ? 'selected' : '' ?>>Dessert</option>
        </select>

        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Simpan</button>
      </form>
    <?php endforeach; ?>
  </div>

  <a href="chef.php" class="mt-6 inline-block text-sm text-gray-600 underline">← Kembali ke Dashboard</a>
</body>
</html>
