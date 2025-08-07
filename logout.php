<?php
session_start();
session_unset();      // Menghapus semua data dari $_SESSION
session_destroy();    // Mengakhiri sesi login
header("Location: index.php"); // Kembali ke halaman login
exit;