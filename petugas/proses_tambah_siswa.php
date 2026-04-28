<?php
session_start();
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'petugas') {
    header("Location: ../raihanf_index.php");
    exit;
}

require_once "../koneksi_raihanf.php";

function generateIdSiswa($koneksi) {
    $hasil = mysqli_fetch_assoc(mysqli_query(
        $koneksi,
        "SELECT raihanf_id_user FROM raihanf_user 
         WHERE raihanf_id_user LIKE 'SIS%' 
         ORDER BY raihanf_id_user DESC LIMIT 1"
    ));

    $nomor = $hasil ? ((int) substr($hasil['raihanf_id_user'], 3)) + 1 : 1;

    // Pastikan tidak duplikat
    do {
        $id_baru = 'SIS' . str_pad($nomor, 3, '0', STR_PAD_LEFT);
        $cek = mysqli_fetch_assoc(mysqli_query(
            $koneksi,
            "SELECT raihanf_id_user FROM raihanf_user WHERE raihanf_id_user='$id_baru'"
        ));
        if ($cek) $nomor++;
    } while ($cek);

    return $id_baru;
}

if (isset($_POST['simpan'])) {

    $nis     = mysqli_real_escape_string($koneksi, $_POST['raihanf_nis_siswa']);
    $nama    = mysqli_real_escape_string($koneksi, $_POST['raihanf_nama_siswa']);
    $kelas   = mysqli_real_escape_string($koneksi, $_POST['raihanf_kelas']);
    $jurusan = mysqli_real_escape_string($koneksi, $_POST['raihanf_jurusan']);
    $no_telp = mysqli_real_escape_string($koneksi, $_POST['raihanf_no_telpon']);
    $rfid    = mysqli_real_escape_string($koneksi, $_POST['rfid']);

    // Validasi input tidak boleh kosong
    if (empty($nis) || empty($nama) || empty($kelas) || empty($jurusan) || empty($rfid)) {
        echo "<script>alert('❌ Semua field wajib diisi!'); window.history.back();</script>";
        exit;
    }

    // Cek RFID sudah digunakan
    $cekRfid = mysqli_query($koneksi, "SELECT 1 FROM raihanf_user WHERE raihanf_rfid='$rfid'");
    if (mysqli_num_rows($cekRfid) > 0) {
        echo "<script>alert('❌ RFID sudah digunakan!'); window.location='tambah_siswa.php';</script>";
        exit;
    }

    // Cek NIS sudah terdaftar
    $cekNis = mysqli_query($koneksi, "SELECT 1 FROM raihanf_user WHERE raihanf_nomor_induk='$nis'");
    if (mysqli_num_rows($cekNis) > 0) {
        echo "<script>alert('❌ NIS sudah terdaftar!'); window.location='tambah_siswa.php';</script>";
        exit;
    }

    $id_user  = generateIdSiswa($koneksi);
    $role     = 'siswa';

    $sql = "INSERT INTO raihanf_user (
        raihanf_id_user,
        raihanf_nama,
        raihanf_nomor_induk,
        raihanf_rfid,
        raihanf_username,
        raihanf_password,
        raihanf_kelas,
        raihanf_jurusan,
        raihanf_no_telpon,
        raihanf_role
    ) VALUES (
        '$id_user',
        '$nama',
        '$nis',
        '$rfid',
        NULL,
        NULL,
        '$kelas',
        '$jurusan',
        '$no_telp',
        '$role'
    )";

    if (mysqli_query($koneksi, $sql)) {
        echo "<script>alert('✅ Siswa berhasil ditambahkan! ID: $id_user'); window.location='data_siswa.php';</script>";
    } else {
        echo "<script>alert('❌ Gagal: " . mysqli_error($koneksi) . "'); window.location='tambah_siswa.php';</script>";
    }
}
?>