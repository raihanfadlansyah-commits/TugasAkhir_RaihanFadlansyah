<?php
require_once "koneksi_raihanf.php";
$id_siswa = $_GET['raihanf_id_siswa'];

mysqli_query($koneksi, "DELETE FROM raihanf_transaksi_peminjaman WHERE raihanf_id_siswa='$id_siswa'");

mysqli_query($koneksi, "DELETE FROM raihanf_siswa WHERE raihanf_id_siswa='$id_siswa'");

echo "<script>alert('✅ Data Berhasil Dihapus!');
    window.location='data_siswa.php'</script>";
?>