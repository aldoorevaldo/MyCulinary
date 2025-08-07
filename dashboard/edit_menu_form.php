<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'owner') {
  header('Location: ../index.php');
  exit;
}

require '../backend/db.php';

// Ambil semua meja dari database
$stmt = $pdo->query("SELECT * FROM meja ORDER BY id_meja ASC");
$mejaList = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Edit Meja</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link rel="shortcut icon" href="../assets/img/log.png">
  <script>
    async function simpanPerubahan(id_meja) {
      const nama_meja = document.getElementById('nama_meja_' + id_meja).value;
      const status = document.getElementById('status_' + id_meja).value;

      const res = await fetch('../backend/index.php?action=update_meja', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id_meja, nama_meja, status })
      });

      const data = await res.json();

      if (data.status === 'success') {
        alert('Berhasil mengubah meja');
        location.reload(); 
      } else {
        alert('Gagal mengubah meja: ' + (data.message || 'Terjadi kesalahan.'));
      }
    }
  </script>
</head>
<body class="bg-gray-100 p-6">
  <div class="max-w-3xl mx-auto bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold mb-4">Edit Meja</h1>

    <?php foreach ($mejaList as $meja): ?>
      <div class="border rounded p-4 mb-4 shadow-sm">
        <h2 class="text-lg font-semibold mb-2">Meja ID: <?= $meja['id_meja'] ?></h2>

        <label class="block mb-1 text-sm">Nama Meja</label>
        <input type="text" id="nama_meja_<?= $meja['id_meja'] ?>" value="<?= htmlspecialchars($meja['nama_meja']) ?>" class="border w-full px-3 py-2 mb-2">

        <label class="block mb-1 text-sm">Status</label>
        <select id="status_<?= $meja['id_meja'] ?>" class="border w-full px-3 py-2 mb-2">
          <option value="kosong" <?= $meja['status'] === 'kosong' ? 'selected' : '' ?>>Kosong</option>
          <option value="terisi" <?= $meja['status'] === 'terisi' ? 'selected' : '' ?>>Terisi</option>
          <option value="selesai" <?= $meja['status'] === 'selesai' ? 'selected' : '' ?>>Selesai</option>
        </select>

        <button onclick="simpanPerubahan(<?= $meja['id_meja'] ?>)" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Simpan</button>
      </div>
    <?php endforeach; ?>

    <a href="owner.php" class="text-sm text-blue-600 underline mt-4 block">← Kembali ke Dashboard Owner</a>
  </div>
</body>
</html>
