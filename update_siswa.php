<?php
require_once "koneksi_raihanf.php";
$id_siswa = $_POST['raihanf_id_siswa'];
$nis = $_POST['raihanf_nis'];
$uid_rfid = $_POST['raihanf_uid_rfid'];
$nama = $_POST['raihanf_nama'];
$kelas = $_POST['raihanf_kelas'];
$no_telp = $_POST['raihanf_no_telp'];

mysqli_query($koneksi, "UPDATE raihanf_siswa SET raihanf_nis='$nis', raihanf_uid_rfid='$uid_rfid', raihanf_nama='$nama', raihanf_kelas='$kelas', raihanf_no_telp='$no_telp' WHERE raihanf_id_siswa='$id_siswa'");

echo "<script>alert('✅ Data Berhasil Diupdate!');
    window.location='data_siswa.php'</script>";
?>