<?php
require 'db.php';
session_start();

$action = $_GET['action'] ?? '';
header('Content-Type: application/json');

if ($action === 'login') {
  // Login via FORM (POST biasa, bukan fetch)
  $username = $_POST['username'] ?? '';
  $password = $_POST['password'] ?? '';

  $stmt = $pdo->prepare("SELECT * FROM user WHERE username = ? AND password_hash = ?");
  $stmt->execute([$username, $password]);
  $user = $stmt->fetch();

  if ($user) {
    $_SESSION['user_id'] = $user['id_user'];
    $_SESSION['role'] = $user['role'];
    header("Location: ../dashboard/{$user['role']}.php");
  } else {
    header("Location: ../index.php?error=1");
  }
  exit;
}

// ---------------------------
// GET MENU
// ---------------------------
if ($action === 'get_menu') {
  $stmt = $pdo->query("SELECT * FROM menu");
  echo json_encode($stmt->fetchAll());
  exit;
}

// ---------------------------
// GET MEJA
// ---------------------------
if ($action === 'get_meja') {
  $stmt = $pdo->query("SELECT * FROM meja");
  echo json_encode($stmt->fetchAll());
  exit;
}

// ---------------------------
// BUAT PESANAN (Pelanggan via tablet)
// ---------------------------
if ($action === 'buat_pesanan') {
  try {
    $data = json_decode(file_get_contents("php://input"), true);

    // CEK STATUS MEJA
    $stmt = $pdo->prepare("SELECT status FROM meja WHERE id_meja = ?");
    $stmt->execute([$data['id_meja']]);
    $meja = $stmt->fetch();
    if (!$meja || $meja['status'] !== 'kosong') {
      echo json_encode(['status' => 'fail', 'message' => 'Meja sudah terisi atau tidak tersedia.']);
      exit;
    }

    $pdo->beginTransaction();

    // Buat pelanggan
    $stmt = $pdo->prepare("INSERT INTO pelanggan (id_meja, waktu_masuk) VALUES (?, NOW())");
    $stmt->execute([$data['id_meja']]);
    $id_pelanggan = $pdo->lastInsertId();

    // Buat pesanan
    $stmt = $pdo->prepare("INSERT INTO pesanan (id_pelanggan, id_meja, waktu_pesan, status) VALUES (?, ?, NOW(), 'menunggu')");
    $stmt->execute([$id_pelanggan, $data['id_meja']]);
    $id_pesanan = $pdo->lastInsertId();

    // Tambah detail pesanan + kurangi stok
    $stmtDetail = $pdo->prepare("INSERT INTO detail_pesanan (id_pesanan, id_menu, jumlah, catatan) VALUES (?, ?, ?, ?)");
    $stmtStok = $pdo->prepare("UPDATE menu SET stok = stok - ?, status_tersedia = IF(stok - ? <= 0, 'habis', 'tersedia') WHERE id_menu = ?");

    foreach ($data['items'] as $item) {
      $stmtDetail->execute([$id_pesanan, $item['id_menu'], $item['jumlah'], $item['catatan'] ?? '']);
      $stmtStok->execute([$item['jumlah'], $item['jumlah'], $item['id_menu']]);
    }


    // Update status meja ke 'terisi'
    $stmt = $pdo->prepare("UPDATE meja SET status = 'terisi' WHERE id_meja = ?");
    $stmt->execute([$data['id_meja']]);

    $pdo->commit();
    echo json_encode(['status' => 'success']);
  } catch (Exception $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['status' => 'fail', 'message' => 'Gagal membuat pesanan', 'error' => $e->getMessage()]);
  }
  exit;
}


// ---------------------------
// GET PESANAN UNTUK CHEF
// ---------------------------
if ($action === 'pesanan_chef') {
  $stmt = $pdo->query("SELECT dp.id_detail, m.nama_menu, dp.jumlah, dp.catatan, m.kategori, p.id_meja, p.id_pesanan
    FROM detail_pesanan dp
    JOIN menu m ON m.id_menu = dp.id_menu
    JOIN pesanan p ON p.id_pesanan = dp.id_pesanan
    WHERE p.status = 'menunggu' OR p.status = 'dimasak'");
  echo json_encode($stmt->fetchAll());
  exit;
}

// ---------------------------
// TANDAI MAKANAN SIAP (Chef)
// ---------------------------
if ($action === 'makanan_siap') {
  $data = json_decode(file_get_contents("php://input"), true);
  if (!isset($data['id_pesanan'])) {
    http_response_code(400);
    echo json_encode(['status' => 'fail', 'message' => 'id_pesanan tidak dikirim']);
    exit;
  }

  $stmt = $pdo->prepare("UPDATE pesanan SET status = 'siap' WHERE id_pesanan = ?");
  $stmt->execute([$data['id_pesanan']]);
  echo json_encode(['status' => 'success']);
  exit;
}

// ---------------------------
// TANDAI PESANAN SEDANG DIMASAK (Chef)
// ---------------------------
if ($action === 'pesanan_dimasak') {
  $data = json_decode(file_get_contents("php://input"), true);
  if (!isset($data['id_pesanan'])) {
    http_response_code(400);
    echo json_encode(['status' => 'fail', 'message' => 'id_pesanan tidak dikirim']);
    exit;
  }

  if ($action === 'tandai_dimasak') {
    $data = json_decode(file_get_contents("php://input"), true);
    $stmt = $pdo->prepare("UPDATE pesanan SET status = 'dimasak' WHERE id_pesanan = ?");
    $stmt->execute([$data['id_pesanan']]);
    echo json_encode(['status' => 'success']);
    exit;
  }

  $stmt = $pdo->prepare("UPDATE pesanan SET status = 'dimasak' WHERE id_pesanan = ?");
  $stmt->execute([$data['id_pesanan']]);
  echo json_encode(['status' => 'success']);
  exit;

}


// ---------------------------
// GET PESANAN SIAP UNTUK PELAYAN
// ---------------------------
if ($action === 'pesanan_siap') {
  $stmt = $pdo->query("SELECT dp.id_detail, m.nama_menu, dp.jumlah, p.id_meja, p.id_pesanan
    FROM detail_pesanan dp
    JOIN menu m ON m.id_menu = dp.id_menu
    JOIN pesanan p ON p.id_pesanan = dp.id_pesanan
    WHERE p.status = 'siap'");
  echo json_encode($stmt->fetchAll());
  exit;
}

// ---------------------------
// PESANAN SIAP & DIMASAK UNTUK PELAYAN
// ---------------------------
if ($action === 'pesanan_siap_dimasak') {
  $stmt = $pdo->query("SELECT dp.id_detail, m.nama_menu, m.kategori, dp.jumlah, p.id_meja, p.id_pesanan, p.status
    FROM detail_pesanan dp
    JOIN menu m ON m.id_menu = dp.id_menu
    JOIN pesanan p ON p.id_pesanan = dp.id_pesanan
    WHERE p.status IN ('dimasak', 'siap')
    ORDER BY p.id_pesanan DESC");
  echo json_encode($stmt->fetchAll());
  exit;
}


// ---------------------------
// ANTAR MAKANAN (Pelayan)
// ---------------------------
if ($action === 'antar_makanan') {
  $data = json_decode(file_get_contents("php://input"), true);
  $stmt = $pdo->prepare("UPDATE pesanan SET status = 'diantar' WHERE id_pesanan = ?");
  $stmt->execute([$data['id_pesanan']]);
  echo json_encode(['status' => 'success']);
  exit;
}

//cek tagihan
if ($action === 'cek_tagihan') {
  $id_meja = $_GET['id_meja'] ?? 0;

  // Validasi ID meja
  if (!$id_meja || !is_numeric($id_meja)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'ID Meja tidak valid']);
    exit;
  }

  // Ambil semua pesanan status 'diantar'
  $stmt = $pdo->prepare("SELECT id_pesanan FROM pesanan WHERE id_meja = ? AND status = 'diantar'");
  $stmt->execute([$id_meja]);
  $pesanan_ids = array_column($stmt->fetchAll(), 'id_pesanan');

  if (empty($pesanan_ids)) {
    echo json_encode(['items' => [], 'grand_total' => 0]);
    exit;
  }

  // Bangun IN (?, ?, ...) dengan aman
  $in = implode(',', array_fill(0, count($pesanan_ids), '?'));
  $sql = "SELECT m.nama_menu, dp.jumlah, (m.harga * dp.jumlah) AS total, m.kategori
          FROM detail_pesanan dp
          JOIN menu m ON m.id_menu = dp.id_menu
          WHERE dp.id_pesanan IN ($in)";
  $stmt = $pdo->prepare($sql);
  $stmt->execute($pesanan_ids);
  $items = $stmt->fetchAll();

  $grand_total = array_sum(array_column($items, 'total'));

  echo json_encode([
    'items' => $items,
    'grand_total' => $grand_total
  ]);
  exit;
}

// BAYAR
if ($action === 'bayar') {
  header('Content-Type: application/json; charset=UTF-8');
  $body = file_get_contents('php://input');
  $data = json_decode($body, true);

  // Validasi input
  if (!isset($data['id_meja'], $data['total'])) {
    http_response_code(400);
    echo json_encode([
      'status'  => 'fail',
      'message' => 'Parameter id_meja atau total tidak dikirim'
    ]);
    exit;
  }

  $id_meja = (int) $data['id_meja'];
  $total   = (float) $data['total'];

  try {
    // Mulai transaksi agar atomic
    $pdo->beginTransaction();

    // 1) Cari pelanggan terakhir di meja ini
    $stmt = $pdo->prepare("
      SELECT id_pelanggan
      FROM pelanggan
      WHERE id_meja = ?
      ORDER BY waktu_masuk DESC
      LIMIT 1
    ");
    $stmt->execute([$id_meja]);
    $id_pelanggan = $stmt->fetchColumn();
    if (!$id_pelanggan) {
      throw new Exception("Pelanggan untuk meja {$id_meja} tidak ditemukan");
    }

    // 2) Simpan ke tabel pembayaran
    $stmt = $pdo->prepare("
      INSERT INTO pembayaran (id_pelanggan, id_meja, total, waktu_bayar)
      VALUES (?, ?, ?, NOW())
    ");
    $stmt->execute([$id_pelanggan, $id_meja, $total]);

    // 3) Update status pesanan → 'dibayar'
    $stmt = $pdo->prepare("
      UPDATE pesanan
      SET status = 'dibayar'
      WHERE id_meja = ?
    ");
    $stmt->execute([$id_meja]);

    // 4) Kosongkan meja
    $stmt = $pdo->prepare("
      UPDATE meja
      SET status = 'kosong'
      WHERE id_meja = ?
    ");
    $stmt->execute([$id_meja]);

    // Commit jika semua berhasil
    $pdo->commit();

    echo json_encode([
      'status'  => 'success',
      'message' => 'Pembayaran berhasil'
    ]);
  } catch (Exception $e) {
    // Rollback kalau ada error
    if ($pdo->inTransaction()) {
      $pdo->rollBack();
    }
    http_response_code(500);
    echo json_encode([
      'status'  => 'error',
      'message' => $e->getMessage()
    ]);
  }
  exit;
}



// RINGKASAN OWNER
if ($action === 'ringkasan_owner') {
  $stmt1 = $pdo->query("SELECT SUM(total) AS total_pendapatan FROM pembayaran WHERE DATE(waktu_bayar) = CURDATE()");
  $stmt2 = $pdo->query("SELECT COUNT(*) AS total_pesanan FROM pesanan WHERE DATE(waktu_pesan) = CURDATE()");
  echo json_encode([
    'total_pendapatan' => (int) $stmt1->fetchColumn(),
    'total_pesanan' => (int) $stmt2->fetchColumn()
  ]);
  exit;
}

// LAPORAN PEMBAYARAN (dengan filter tanggal)
if ($action === 'laporan_pembayaran') {
  // ambil tanggal dari query string, format YYYY-MM-DD
  $tanggal = $_GET['tanggal'] ?? '';

  if ($tanggal) {
    // filter berdasarkan DATE(waktu_bayar)
    $stmt = $pdo->prepare("
      SELECT *
      FROM pembayaran
      WHERE DATE(waktu_bayar) = ?
      ORDER BY waktu_bayar DESC
    ");
    $stmt->execute([$tanggal]);
  } else {
    // tanpa filter
    $stmt = $pdo->query("
      SELECT *
      FROM pembayaran
      ORDER BY waktu_bayar DESC
    ");
  }

  echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
  exit;
}


// DAFTAR USER
if ($action === 'daftar_user') {
  $stmt = $pdo->query("SELECT username, role,nama_lengkap FROM user ORDER BY role ASC");
  echo json_encode($stmt->fetchAll());
  exit;
}

// LAPORAN PEMBAYARAN (dengan nama kasir)
if ($action === 'laporan_pembayaran') {
  $tanggal = $_GET['tanggal'] ?? '';
  $meja = $_GET['meja'] ?? '';
  $kasir = $_GET['kasir'] ?? '';

  $query = "SELECT p.*, u.nama_lengkap AS nama_kasir
            FROM pembayaran p
            LEFT JOIN user u ON p.id_kasir = u.id_user
            WHERE 1=1";
  $params = [];

  if ($tanggal) {
    $query .= " AND DATE(p.waktu_bayar) = ?";
    $params[] = $tanggal;
  }
  if ($meja) {
    $query .= " AND p.id_meja = ?";
    $params[] = $meja;
  }
  if ($kasir) {
    $query .= " AND u.nama_lengkap LIKE ?";
    $params[] = "%$kasir%";
  }

  $query .= " ORDER BY p.waktu_bayar DESC";
  $stmt = $pdo->prepare($query);
  $stmt->execute($params);
  echo json_encode($stmt->fetchAll());
  exit;
}

if ($action === 'laporan_pembayaran') {
  $tanggal = $_GET['tanggal'] ?? '';

  $query = "SELECT * FROM pembayaran WHERE 1";
  $params = [];

  if (!empty($tanggal)) {
    $query .= " AND DATE(waktu_bayar) = ?";
    $params[] = $tanggal;
  }

  $stmt = $pdo->prepare($query);
  $stmt->execute($params);
  echo json_encode($stmt->fetchAll());
  exit;
}

if ($action === 'grafik_pendapatan') {
  $stmt = $pdo->query("
    SELECT DATE(waktu_bayar) AS tanggal, SUM(total) AS total
    FROM pembayaran
    WHERE DATE(waktu_bayar) >= CURDATE() - INTERVAL 6 DAY
    GROUP BY DATE(waktu_bayar)
    ORDER BY tanggal ASC
  ");
  echo json_encode($stmt->fetchAll());
  exit;
}

// ---------------------------
// TAMBAH MENU (Chef)
// ---------------------------
if ($action === 'tambah_menu') {
  try {
    if (!isset($_POST['nama_menu'], $_POST['harga'], $_POST['stok'], $_POST['status_tersedia'], $_POST['kategori'], $_POST['id_chef'])) {
      throw new Exception("Data tidak lengkap.");
    }

    // Validasi dan ambil data
    $nama_menu = $_POST['nama_menu'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];
    $status_tersedia = $_POST['status_tersedia'];
    $kategori = $_POST['kategori'];
    $id_chef = $_POST['id_chef'];

    // Upload Gambar
    if (!isset($_FILES['gambar']) || $_FILES['gambar']['error'] !== UPLOAD_ERR_OK) {
      throw new Exception("Gagal upload gambar.");
    }

    $uploadDir = '../uploads/menu/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

    $ext = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
    $filename = uniqid('menu_') . '.' . $ext;
    $targetFile = $uploadDir . $filename;

    if (!move_uploaded_file($_FILES['gambar']['tmp_name'], $targetFile)) {
      throw new Exception("Gagal menyimpan gambar.");
    }

    // Simpan ke DB
    $stmt = $pdo->prepare("INSERT INTO menu (nama_menu, harga, stok, status_tersedia, kategori, gambar, id_chef) 
      VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$nama_menu, $harga, $stok, $status_tersedia, $kategori, $filename, $id_chef]);

    echo json_encode(['status' => 'success']);
  } catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'fail', 'message' => $e->getMessage()]);
  }
  exit;
}

// ---------------------------
// GET MEJA KOSONG (Untuk pelayan)
// ---------------------------
if ($action === 'semua_meja') {
  $stmt = $pdo->query("SELECT * FROM meja ORDER BY id_meja ASC");
  echo json_encode($stmt->fetchAll());
  exit;
}

if ($action === 'update_menu') {
  $data = json_decode(file_get_contents("php://input"), true);
  $stmt = $pdo->prepare("UPDATE menu SET nama_menu = ?, harga = ?, stok = ?, status_tersedia = ?, kategori = ? WHERE id_menu = ?");
  $stmt->execute([
    $data['nama_menu'],
    $data['harga'],
    $data['stok'],
    $data['status_tersedia'],
    $data['kategori'],
    $data['id_menu']
  ]);
  echo json_encode(['status' => 'success']);
  exit;
}

// TAMBAH MEJA
if ($action === 'tambah_meja') {
  $data = json_decode(file_get_contents("php://input"), true);
  $nomor = intval($data['nomor'] ?? 0);

  if ($nomor <= 0) {
    echo json_encode(['status' => 'fail', 'message' => 'Nomor meja tidak valid']);
    exit;
  }

  // Cek apakah nomor meja sudah ada
  $cek = $pdo->prepare("SELECT COUNT(*) FROM meja WHERE id_meja = ?");
  $cek->execute([$nomor]);
  if ($cek->fetchColumn() > 0) {
    echo json_encode(['status' => 'fail', 'message' => 'Meja sudah ada']);
    exit;
  }

  // Tambah meja
  $id_chef = $_POST['id_chef'] ?? ($_SESSION['user_id'] ?? null);

  $stmt = $pdo->prepare("INSERT INTO menu (nama_menu, harga, stok, status_tersedia, kategori, gambar, id_chef) VALUES (?, ?, ?, ?, ?, ?, ?)");
  $stmt->execute([$nama, $harga, $stok, $status, $kategori, $filename, $id_chef]);

  echo json_encode(['status' => 'success']);
  exit;
}

if ($action === 'edit_meja') {
  $data = json_decode(file_get_contents("php://input"), true);
  $id_meja = $data['id_meja'] ?? null;
  $status = $data['status'] ?? '';

  if (!$id_meja || !in_array($status, ['kosong', 'terisi', 'selesai'])) {
    echo json_encode(['status' => 'fail', 'message' => 'Data tidak valid']);
    exit;
  }

  $stmt = $pdo->prepare("UPDATE meja SET status = ? WHERE id_meja = ?");
  $stmt->execute([$status, $id_meja]);
  echo json_encode(['status' => 'success']);
  exit;
}

// Edit meja
if ($action === 'edit_meja') {
  $data = json_decode(file_get_contents("php://input"), true);
  $stmt = $pdo->prepare("UPDATE meja SET id_meja = ?, status = ? WHERE id_meja = ?");
  try {
    $stmt->execute([$data['id_meja'], $data['status'], $data['id_awal']]);
    echo json_encode(['status' => 'success']);
  } catch (Exception $e) {
    echo json_encode(['status' => 'fail', 'message' => $e->getMessage()]);
  }
  exit;
}

// ---------------------------
// UPDATE MEJA
// ---------------------------
if ($action === 'update_meja') {
  $data = json_decode(file_get_contents("php://input"), true);
  $stmt = $pdo->prepare("UPDATE meja SET nama_meja = ?, status = ? WHERE id_meja = ?");
  $success = $stmt->execute([$data['nama_meja'], $data['status'], $data['id_meja']]);

  echo json_encode(['status' => $success ? 'success' : 'fail']);
  exit;
}

if ($action === 'update_user') {
  $data = json_decode(file_get_contents("php://input"), true);
  $stmt = $pdo->prepare("UPDATE user SET nama_lengkap = ?, role = ? WHERE username = ?");
  $success = $stmt->execute([$data['nama_lengkap'], $data['role'], $data['username']]);

  echo json_encode(['status' => $success ? 'success' : 'fail']);
  exit;
}


if ($action === 'hapus_user') {
  $data = json_decode(file_get_contents("php://input"), true);
  $stmt = $pdo->prepare("DELETE FROM user WHERE username = ?");
  $success = $stmt->execute([$data['username']]);

  echo json_encode(['status' => $success ? 'success' : 'fail']);
  exit;
}


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

http_response_code(400);
echo json_encode(['status' => 'unknown action']);