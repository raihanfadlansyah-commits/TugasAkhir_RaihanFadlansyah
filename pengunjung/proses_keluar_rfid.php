<?php
session_start();
date_default_timezone_set('Asia/Jakarta');
require_once "../koneksi_raihanf.php";

$rfid = $_POST['rfid'] ?? '';

if (empty($rfid)) {
    header("Location: pengunjung_keluar_rfid.php");
    exit;
}

// Ambil user
$query = mysqli_query($koneksi, "SELECT * FROM raihanf_user 
    WHERE raihanf_rfid='$rfid' 
    AND raihanf_role='siswa'");

$data = mysqli_fetch_assoc($query);

if ($data) {

    $id_user = $data['raihanf_id_user'];
    $nama    = $data['raihanf_nama'];

    // Cari yang belum keluar
    $q_kunjungan = mysqli_query($koneksi, "SELECT * FROM raihanf_riwayat_pengunjung 
        WHERE raihanf_id_user='$id_user' 
        AND raihanf_waktu_keluar IS NULL 
        ORDER BY raihanf_waktu_masuk DESC 
        LIMIT 1");

    $kunjungan = mysqli_fetch_assoc($q_kunjungan);

    if ($kunjungan) {

        $id_pengunjung = $kunjungan['raihanf_id_pengunjung'];
        $waktu_keluar  = date('Y-m-d H:i:s');

        $update = mysqli_query($koneksi, "UPDATE raihanf_riwayat_pengunjung 
            SET raihanf_waktu_keluar='$waktu_keluar' 
            WHERE raihanf_id_pengunjung='$id_pengunjung'");

        if ($update) {
            $_SESSION['pesan'] = "👋 Sampai jumpa, $nama!";
        } else {
            $_SESSION['pesan'] = "⚠️ Gagal menyimpan waktu keluar!";
        }

    } else {
        $_SESSION['pesan'] = "⚠️ $nama, kamu belum masuk!";
    }

} else {
    $_SESSION['pesan'] = "❌ RFID tidak terdaftar!";
}

// 🔥 WAJIB (trigger log tampil)
$_SESSION['last_scan'] = true;

header("Location: pengunjung_keluar_rfid.php");
exit;