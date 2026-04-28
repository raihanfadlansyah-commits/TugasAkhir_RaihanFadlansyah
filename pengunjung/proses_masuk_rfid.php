<?php
session_start();
date_default_timezone_set('Asia/Jakarta');
require_once "../koneksi_raihanf.php";

$rfid = $_POST['rfid'] ?? '';

if (empty($rfid)) {
    header("Location: pengunjung_masuk_rfid.php");
    exit;
}

// Ambil data user
$query = mysqli_query($koneksi, "SELECT * FROM raihanf_user 
    WHERE raihanf_rfid='$rfid' 
    AND raihanf_role='siswa'");

$data = mysqli_fetch_assoc($query);

if ($data) {

    $id_user = $data['raihanf_id_user'];
    $nama    = $data['raihanf_nama'];
    $kelas   = $data['raihanf_kelas'];
    $jurusan = $data['raihanf_jurusan'];

    $hari_ini = date('Y-m-d');

    // Cek sudah masuk & belum keluar
    $cek = mysqli_query($koneksi, "SELECT * FROM raihanf_riwayat_pengunjung 
        WHERE raihanf_id_user='$id_user'
        AND DATE(raihanf_waktu_masuk)='$hari_ini'
        AND raihanf_waktu_keluar IS NULL");

    if (mysqli_num_rows($cek) > 0) {
        $_SESSION['pesan'] = "⚠️ $nama, kamu sudah masuk dan belum keluar!";
        $_SESSION['last_scan'] = true;
        header("Location: pengunjung_masuk_rfid.php");
        exit;
    }

    // Generate ID
    $q_last = mysqli_query($koneksi, "SELECT raihanf_id_pengunjung 
        FROM raihanf_riwayat_pengunjung 
        ORDER BY raihanf_id_pengunjung DESC 
        LIMIT 1");

    if (mysqli_num_rows($q_last) > 0) {
        $last     = mysqli_fetch_assoc($q_last);
        $last_num = (int) preg_replace('/[^0-9]/', '', $last['raihanf_id_pengunjung']);
        $new_num  = $last_num + 1;
    } else {
        $new_num = 1;
    }

    $id_pengunjung = 'PJG' . str_pad($new_num, 3, '0', STR_PAD_LEFT);
    $waktu_masuk   = date('Y-m-d H:i:s');

    // Insert
    $insert = mysqli_query($koneksi, "INSERT INTO raihanf_riwayat_pengunjung 
        (raihanf_id_pengunjung, raihanf_id_user, raihanf_nama, raihanf_kelas, raihanf_jurusan, raihanf_waktu_masuk) 
        VALUES 
        ('$id_pengunjung', '$id_user', '$nama', '$kelas', '$jurusan', '$waktu_masuk')");

    if ($insert) {
        $_SESSION['pesan'] = "✅ Selamat datang, $nama!";
    } else {
        $_SESSION['pesan'] = "⚠️ Gagal menyimpan data!";
    }

} else {
    $_SESSION['pesan'] = "❌ RFID tidak terdaftar!";
}

// 🔥 WAJIB (trigger log tampil)
$_SESSION['last_scan'] = true;

header("Location: pengunjung_masuk_rfid.php");
exit;