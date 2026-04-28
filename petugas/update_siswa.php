<?php
session_start();

if (!isset($_SESSION['login']) || $_SESSION['login'] != "admin") {
    header("Location: ../raihanf_index.php");
    exit;
}

require_once "../koneksi_raihanf.php";

if (isset($_POST['update'])) {

    $id_user  = mysqli_real_escape_string($koneksi, $_POST['raihanf_id_user']);
    $nama     = mysqli_real_escape_string($koneksi, $_POST['raihanf_nama']);
    $kelas    = mysqli_real_escape_string($koneksi, $_POST['raihanf_kelas']);
    $jurusan  = mysqli_real_escape_string($koneksi, $_POST['raihanf_jurusan']);
    $no_telp  = mysqli_real_escape_string($koneksi, $_POST['raihanf_no_telpon']);
    $rfid     = mysqli_real_escape_string($koneksi, $_POST['raihanf_rfid']);

    // ================= CEK RFID duplikat (kecuali milik sendiri) =================
    $cekRfid = mysqli_query($koneksi, "
        SELECT raihanf_id_user FROM raihanf_user 
        WHERE raihanf_rfid = '$rfid' 
        AND raihanf_id_user != '$id_user'
    ");

    if (mysqli_num_rows($cekRfid) > 0) {
        echo "<script>
            alert('❌ RFID sudah digunakan user lain!');
            window.location='data_siswa.php';
        </script>";
        exit;
    }

    // ================= UPDATE raihanf_user =================
    // Kolom disesuaikan dengan struktur tabel raihanf_user di DB
    $updateUser = mysqli_query($koneksi, "
        UPDATE raihanf_user SET
            raihanf_nama      = '$nama',
            raihanf_kelas     = '$kelas',
            raihanf_jurusan   = '$jurusan',
            raihanf_no_telpon = '$no_telp',
            raihanf_rfid      = '$rfid'
        WHERE raihanf_id_user = '$id_user'
        AND raihanf_role = 'siswa'
    ");

    if ($updateUser) {
        echo "<script>
            alert('✅ Data siswa berhasil diupdate!');
            window.location='data_siswa.php';
        </script>";
    } else {
        echo "<script>
            alert('❌ Gagal update data: " . mysqli_error($koneksi) . "');
            window.location='data_siswa.php';
        </script>";
    }
}
?>