<?php
require 'db.php';

$format = $_GET['format'] ?? 'excel';
$tanggal = $_GET['tanggal'] ?? '';

$query = "SELECT * FROM pembayaran WHERE 1";
$params = [];

if (!empty($tanggal)) {
  $query .= " AND DATE(waktu_bayar) = ?";
  $params[] = $tanggal;
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$data = $stmt->fetchAll();

if ($format === 'excel') {
  header("Content-Type: application/vnd.ms-excel");
  header("Content-Disposition: attachment; filename=laporan_$tanggal.xls");

  echo "ID Pembayaran\tID Meja\tTotal\tWaktu Bayar\n";
  foreach ($data as $row) {
    echo "{$row['id_pembayaran']}\t{$row['id_meja']}\t{$row['total']}\t{$row['waktu_bayar']}\n";
  }
} else if ($format === 'pdf') {
  require('vendor/autoload.php'); 

  $mpdf = new \Mpdf\Mpdf();
  $html = "<h2>Laporan Pembayaran ($tanggal)</h2><table border='1' cellpadding='4' cellspacing='0'><tr><th>ID</th><th>Meja</th><th>Total</th><th>Waktu</th></tr>";
  foreach ($data as $row) {
    $html .= "<tr><td>{$row['id_pembayaran']}</td><td>Meja {$row['id_meja']}</td><td>Rp {$row['total']}</td><td>{$row['waktu_bayar']}</td></tr>";
  }
  $html .= "</table>";
  $mpdf->WriteHTML($html);
  $mpdf->Output("laporan_$tanggal.pdf", 'D');
}
