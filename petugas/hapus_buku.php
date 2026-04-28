<?php
session_start();
require_once "../koneksi_raihanf.php";

if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'petugas') {
    header("Location: ../raihanf_index.php");
    exit;
}

if (!isset($_GET['raihanf_id_buku'])) {
    header("Location: data_buku.php");
    exit;
}

$id_buku = mysqli_real_escape_string($koneksi, $_GET['raihanf_id_buku']);

// Ambil data buku
$data = mysqli_fetch_assoc(mysqli_query($koneksi,
    "SELECT * FROM raihanf_buku WHERE raihanf_id_buku='$id_buku'"
));

if (!$data) {
    echo "<script>alert('❌ Buku tidak ditemukan!'); window.location='data_buku.php';</script>";
    exit;
}

// Cek apakah buku masih dipinjam (lewat detail_peminjaman → peminjaman)
$cek_pinjam = mysqli_query($koneksi, "
    SELECT d.raihanf_id_detail
    FROM raihanf_detail_peminjaman d
    JOIN raihanf_peminjaman p ON d.raihaf_id_peminjaman = p.raihanf_id_peminjaman
    WHERE d.raihanf_id_buku = '$id_buku'
      AND p.raihanf_status  = 'Dipinjam'
    LIMIT 1
");

if (mysqli_num_rows($cek_pinjam) > 0) {
    echo "<script>alert('❌ Buku tidak bisa dihapus karena masih dipinjam!'); window.location='data_buku.php';</script>";
    exit;
}

// Hapus foto dari folder jika ada
$folder = "../assets/img_buku/";
if (!empty($data['raihanf_img']) && file_exists($folder . $data['raihanf_img'])) {
    unlink($folder . $data['raihanf_img']);
}

// Hapus dari database
$hapus = mysqli_query($koneksi,
    "DELETE FROM raihanf_buku WHERE raihanf_id_buku='$id_buku'"
);

if ($hapus) {
    echo "<script>alert('✅ Buku berhasil dihapus!'); window.location='data_buku.php';</script>";
} else {
    echo "<script>alert('❌ Gagal menghapus buku: " . mysqli_error($koneksi) . "'); window.location='data_buku.php';</script>";
}
?>