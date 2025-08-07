<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'owner') {
  header('Location: ../index.php');
  exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Owner | MyCulinary</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="shortcut icon" href="../assets/img/logo-brand.png">
  <link rel="stylesheet" href="../assets/css/owners.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="../assets/js/owner.js"></script>
</head>
<body>
  <div class="container">
    <h1 class="judul" style="color:white;">Selamat Datang Owner</h1>

    <div class="ringkasan-box">
      <div class="ringkasan-item1">
        <h2>Total Pendapatan Hari Ini</h2>
        <p id="totalPendapatan">...</p>
      </div>
      <div class="ringkasan-item2">
        <h2>Total Pesanan Hari Ini</h2>
        <p id="totalPesanan">...</p>
      </div>
    </div>

    <div class="box laporan">
      <div class="laporan-header">
        <h2>Laporan Pembayaran</h2>
        <button onclick="exportLaporan('excel')">Export Excel</button>
        
      </div>

      <div class="laporan-filter">
        <input type="date" id="filterTanggal">
        <button onclick="loadLaporan(true)">Filter</button>
      </div>

      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Meja</th>
            <th>Total</th>
            <th>Waktu</th>
          </tr>
        </thead>
        <tbody id="laporanBody"></tbody>
      </table>
    </div>

    <div class="box">
      <h2>Manajemen User</h2>
      <table>
        <thead>
          <tr>
            <th>Username</th>
            <th>Role</th>
            <th>Nama Lengkap</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody id="userBody"></tbody>
      </table>
    </div>

    <div class="box">
      <h2>Tambah Meja</h2>
      <p>Masukkan nomor meja baru untuk menambah meja baru ke sistem.</p>
      <p style="font-weight: bold;">*Pastikan nomor meja belum ada sebelumnya.</p>
      <form onsubmit="tambahMeja(event)">
        <input type="number" id="nomorMejaBaru" placeholder="Nomor Meja" required>
        <button type="submit">Tambah Meja</button>
      </form>
      <p id="notifMeja" class="notif">Meja berhasil ditambahkan!</p>
    </div>

    <div class="box grafik">
      <h2>Grafik Pendapatan 7 Hari Terakhir</h2>
      <canvas id="grafikPendapatan" height="100"></canvas>
    </div>

    <div class="logout-container">
      <a href="../logout.php" class="btn logout">Logout</a>
    </div>
  </div>
</body>
</html>
