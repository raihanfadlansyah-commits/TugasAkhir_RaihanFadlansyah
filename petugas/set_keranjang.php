<?php
session_start();

// [FIX] Validasi session login
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'petugas') {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

// [FIX] Validasi input POST
if (!isset($_POST['id']) || !isset($_POST['qty'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Parameter tidak lengkap']);
    exit;
}

// [FIX] Sanitasi input
$id  = preg_replace('/[^a-zA-Z0-9_\-]/', '', $_POST['id']); // hanya karakter aman
$qty = (int)$_POST['qty'];

// Pastikan qty tidak negatif dan tidak lebih dari 1 (sesuai logika 1 buku per pinjaman)
if ($qty < 0) $qty = 0;
if ($qty > 1) $qty = 1;

if (!isset($_SESSION['keranjang'])) {
    $_SESSION['keranjang'] = [];
}

if ($qty > 0) {
    $_SESSION['keranjang'][$id] = $qty;
} else {
    unset($_SESSION['keranjang'][$id]);
}

// [FIX] Kirim response JSON agar AJAX bisa konfirmasi sukses
echo json_encode(['status' => 'ok', 'id' => $id, 'qty' => $qty]);
?>
