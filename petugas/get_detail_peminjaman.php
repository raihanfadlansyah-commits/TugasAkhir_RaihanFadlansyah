<?php
session_start();
require_once "../koneksi_raihanf.php";

if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'petugas') {
    http_response_code(403);
    echo json_encode([]);
    exit;
}

$id = mysqli_real_escape_string($koneksi, $_GET['id'] ?? '');

if (empty($id)) {
    echo json_encode([]);
    exit;
}

$query = mysqli_query($koneksi, "
    SELECT
        b.raihanf_judul     AS judul,
        b.raihanf_pengarang AS pengarang,
        d.raihanf_total_buku AS jumlah
    FROM raihanf_detail_peminjaman d
    JOIN raihanf_buku b ON d.raihanf_id_buku = b.raihanf_id_buku
    WHERE d.raihaf_id_peminjaman = '$id'
    ORDER BY b.raihanf_judul ASC
");

$result = [];
while ($row = mysqli_fetch_assoc($query)) {
    $result[] = [
        'judul'     => htmlspecialchars($row['judul']),
        'pengarang' => htmlspecialchars($row['pengarang']),
        'jumlah'    => (int)$row['jumlah'],
    ];
}

header('Content-Type: application/json');
echo json_encode($result);
