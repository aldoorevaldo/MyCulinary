<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'owner') {
  header("Location: ../index.php");
  exit;
}

require '../backend/db.php';
$username = $_GET['username'] ?? '';
$stmt = $pdo->prepare("SELECT * FROM user WHERE username = ?");
$stmt->execute([$username]);
$user = $stmt->fetch();

if (!$user) {
  echo "User tidak ditemukan."; exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Edit User</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link rel="shortcut icon" href="../assets/img/log.png">
  <script>
    async function updateUser(event) {
      event.preventDefault();
      const payload = {
        username: document.getElementById('username').value,
        nama_lengkap: document.getElementById('nama_lengkap').value,
        role: document.getElementById('role').value
      };

      const res = await fetch('../backend/index.php?action=update_user', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      });

      const data = await res.json();
      if (data.status === 'success') {
        alert('User berhasil diperbarui!');
        window.location.href = 'owner.php';
      } else {
        alert('Gagal memperbarui user');
      }
    }
  </script>
</head>
<body class="p-6 bg-gray-100 min-h-screen">
  <div class="max-w-md mx-auto bg-white p-6 rounded shadow">
    <h1 class="text-xl font-bold mb-4">Edit User</h1>
    <form onsubmit="updateUser(event)">
      <label>Username</label>
      <input id="username" type="text" class="w-full border px-3 py-2 mb-4 bg-gray-100" value="<?= htmlspecialchars($user['username']) ?>" readonly>

      <label>Nama Lengkap</label>
      <input id="nama_lengkap" type="text" class="w-full border px-3 py-2 mb-4" value="<?= htmlspecialchars($user['nama_lengkap']) ?>">

      <label>Role</label>
      <select id="role" class="w-full border px-3 py-2 mb-4">
        <option value="chef" <?= $user['role'] === 'chef' ? 'selected' : '' ?>>Chef</option>
        <option value="cashier" <?= $user['role'] === 'cashier' ? 'selected' : '' ?>>Kasir</option>
        <option value="waiter" <?= $user['role'] === 'waiter' ? 'selected' : '' ?>>Pelayan</option>
        <option value="owner" <?= $user['role'] === 'owner' ? 'selected' : '' ?>>Owner</option>
      </select>

      <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Simpan</button>
    </form>
  </div>
</body>
</html>
