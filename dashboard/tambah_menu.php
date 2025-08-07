<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'chef') {
  header("Location: ../index.php");
  exit;
}

require '../backend/db.php';
$id_chef = $_SESSION['user_id'] ?? null;

// Ambil nama lengkap chef untuk ditampilkan
$stmt = $pdo->prepare("SELECT nama_lengkap FROM user WHERE id_user = ?");
$stmt->execute([$id_chef]);
$nama_chef = $stmt->fetchColumn();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Tambah Menu</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/tambah_menu.css">
  <link rel="shortcut icon" href="../assets/img/logo-brand.png">
  <script>
    async function tambahMenu(event) {
      event.preventDefault();

      const form = document.getElementById('formTambah');
      const formData = new FormData(form);

      try {
        const res = await fetch('../backend/index.php?action=tambah_menu', {
          method: 'POST',
          body: formData
        });
        const data = await res.json();
        if (data.status === 'success') {
          alert('Menu berhasil ditambahkan!');
          window.location.href = 'chef.php';
        } else {
          alert('Gagal menambahkan menu: ' + (data.message || ''));
        }
      } catch (e) {
        alert('Terjadi kesalahan saat mengirim data.');
        console.error(e);
      }
    }
  </script>
</head>
<body class="bg-gray-100 p-6 min-h-screen">
  <div class="max-w-xl mx-auto bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold mb-4">Tambah Menu Baru</h1>

    <p class="mb-4 text-sm text-gray-600">Nama Chef : <strong><?= htmlspecialchars($nama_chef) ?></strong></p>

    <form id="formTambah" onsubmit="tambahMenu(event)" enctype="multipart/form-data">
      <label class="block mb-2 font-medium">Nama Menu</label>
      <input type="text" name="nama_menu" class="w-full border px-3 py-2 mb-4" required>

      <label class="block mb-2 font-medium">Harga (Rp)</label>
      <input type="number" name="harga" class="w-full border px-3 py-2 mb-4" required>

      <label class="block mb-2 font-medium">Stok</label>
      <input type="number" name="stok" class="w-full border px-3 py-2 mb-4" required>

      <label class="block mb-2 font-medium">Status Ketersediaan</label>
      <select name="status_tersedia" class="w-full border px-3 py-2 mb-4" required>
        <option value="tersedia">Tersedia</option>
        <option value="habis">Habis</option>
      </select>

      <label class="block mb-2 font-medium">Kategori</label>
      <select name="kategori" class="w-full border px-3 py-2 mb-4" required>
        <option value="makanan">Makanan</option>
        <option value="minuman">Minuman</option>
        <option value="dessert">Dessert</option>
      </select>

      <label class="block mb-2 font-medium">Gambar Menu</label>
      <input type="file" name="gambar" accept="image/*" class="w-full mb-4" required>

      <input type="hidden" name="id_chef" value="<?= $id_chef ?>">

      <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 w-full">Simpan Menu</button>
    </form>

    <a href="chef.php" class="block mt-4 text-sm text-gray-600 underline text-center">← Kembali ke Dashboard</a>
  </div>
</body>
</html>
