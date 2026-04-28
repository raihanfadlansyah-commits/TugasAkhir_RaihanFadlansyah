<?php
session_start();
require_once "../koneksi_raihanf.php";

// VALIDASI LOGIN PETUGAS
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'petugas') {
    header("Location: ../raihanf_index.php");
    exit;
}

if (isset($_POST['simpan'])) {

    // AMBIL DATA
    $judul     = mysqli_real_escape_string($koneksi, $_POST['raihanf_judul']);
    $pengarang = mysqli_real_escape_string($koneksi, $_POST['raihanf_pengarang']);
    $penerbit  = mysqli_real_escape_string($koneksi, $_POST['raihanf_penerbit']);
    $tahun     = mysqli_real_escape_string($koneksi, $_POST['raihanf_tahun_terbit']);
    $kategori  = mysqli_real_escape_string($koneksi, $_POST['raihanf_kategori']);
    $stok      = mysqli_real_escape_string($koneksi, $_POST['raihanf_stok_total']);

    // VALIDASI KATEGORI
    if (empty($kategori)) {
        echo "<script>alert('Kategori harus dipilih!'); window.location='tambah_buku.php';</script>";
        exit;
    }

    /* ================== CEK JUDUL DUPLIKAT ================== */
    $cek = mysqli_query($koneksi, "SELECT * FROM raihanf_buku WHERE raihanf_judul = '$judul'");
    if (mysqli_num_rows($cek) > 0) {
        echo "<script>alert('Judul buku sudah ada!'); window.location='tambah_buku.php';</script>";
        exit;
    }

    /* ================== AUTO ID BUKU ================== */
    $queryId = mysqli_query($koneksi, "SELECT raihanf_id_buku FROM raihanf_buku ORDER BY raihanf_id_buku DESC LIMIT 1");
    $dataId  = mysqli_fetch_assoc($queryId);

    if ($dataId) {
        $lastId = $dataId['raihanf_id_buku']; // BK005
        $angka  = (int) substr($lastId, 2);
        $angka++;
        $id_buku = "BK" . str_pad($angka, 3, "0", STR_PAD_LEFT);
    } else {
        $id_buku = "BK001";
    }

    /* ================== UPLOAD GAMBAR ================== */
    $namaFile = $_FILES['raihanf_img']['name'];
    $tmpFile  = $_FILES['raihanf_img']['tmp_name'];
    $size     = $_FILES['raihanf_img']['size'];

    $ext = strtolower(pathinfo($namaFile, PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png'];

    if (!in_array($ext, $allowed)) {
        echo "<script>alert('File harus JPG / PNG!'); window.location='tambah_buku.php';</script>";
        exit;
    }

    if ($size > 2000000) {
        echo "<script>alert('Ukuran file max 2MB!'); window.location='tambah_buku.php';</script>";
        exit;
    }

    $namaBaru = time() . "_" . $namaFile;
    $path = "../assets/img_buku/" . $namaBaru;

    if (move_uploaded_file($tmpFile, $path)) {

        $query = "INSERT INTO raihanf_buku 
        (raihanf_id_buku, raihanf_img, raihanf_judul, raihanf_pengarang, raihanf_penerbit, raihanf_tahun_terbit, raihanf_kategori, raihanf_stok_total)
        VALUES 
        ('$id_buku', '$namaBaru', '$judul', '$pengarang', '$penerbit', '$tahun', '$kategori', '$stok')";

        if (mysqli_query($koneksi, $query)) {
            echo "<script>alert('✅ Data berhasil ditambahkan!'); window.location='data_buku.php';</script>";
        } else {
            echo "<script>alert('❌ Gagal menyimpan ke database!'); window.location='tambah_buku.php';</script>";
        }
    } else {
        echo "<script>alert('Upload gambar gagal!'); window.location='tambah_buku.php';</script>";
    }
}
