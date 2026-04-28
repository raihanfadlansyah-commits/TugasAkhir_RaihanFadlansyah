<?php
require_once "../koneksi_raihanf.php";
session_start();

// [FIX] Tambah validasi session login
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'petugas') {
    header("Location: ../raihanf_index.php");
    exit;
}

if (!isset($_POST['raihanf_id_buku'])) {
    header("Location: data_buku.php");
    exit;
}

// [FIX] Semua variabel di-escape dengan mysqli_real_escape_string
$id_buku      = mysqli_real_escape_string($koneksi, $_POST['raihanf_id_buku']);
$judul        = mysqli_real_escape_string($koneksi, $_POST['raihanf_judul']);
$pengarang    = mysqli_real_escape_string($koneksi, $_POST['raihanf_pengarang']);
$penerbit     = mysqli_real_escape_string($koneksi, $_POST['raihanf_penerbit']);
$tahun_terbit = mysqli_real_escape_string($koneksi, $_POST['raihanf_tahun_terbit']);
// [FIX] Tambah kategori dan stok yang sebelumnya tidak di-update
$kategori     = mysqli_real_escape_string($koneksi, $_POST['raihanf_kategori']);
$stok_total   = (int)$_POST['raihanf_stok_total'];
$foto_lama    = $_POST['foto_lama'] ?? "";

$folder = "../assets/img_buku/";

// ================== CEK DUPLIKAT JUDUL ==================
$cek = mysqli_query($koneksi, "
    SELECT * FROM raihanf_buku 
    WHERE raihanf_judul='$judul' 
    AND raihanf_id_buku != '$id_buku'
");

if (mysqli_num_rows($cek) > 0) {
    echo "<script>
        alert('❌ Judul buku sudah ada!');
        window.history.back();
    </script>";
    exit;
}

// ================== CEK UPLOAD FOTO ==================
if (!empty($_FILES['raihanf_img']['name'])) {

    $namaFile = $_FILES['raihanf_img']['name'];
    $tmpFile  = $_FILES['raihanf_img']['tmp_name'];
    $size     = $_FILES['raihanf_img']['size'];

    $ext = strtolower(pathinfo($namaFile, PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png'];

    if (!in_array($ext, $allowed)) {
        echo "<script>alert('❌ File harus JPG / PNG!'); window.history.back();</script>";
        exit;
    }

    if ($size > 2000000) {
        echo "<script>alert('❌ Ukuran file max 2MB!'); window.history.back();</script>";
        exit;
    }

    $namaBaru = time() . "_" . basename($namaFile);
    $path = $folder . $namaBaru;

    if (move_uploaded_file($tmpFile, $path)) {

        if (!empty($foto_lama) && file_exists($folder . $foto_lama)) {
            unlink($folder . $foto_lama);
        }

        // [FIX] Tambah update kategori dan stok_total
        $query = "UPDATE raihanf_buku SET 
            raihanf_judul='$judul',
            raihanf_pengarang='$pengarang',
            raihanf_penerbit='$penerbit',
            raihanf_tahun_terbit='$tahun_terbit',
            raihanf_kategori='$kategori',
            raihanf_stok_total='$stok_total',
            raihanf_img='$namaBaru'
            WHERE raihanf_id_buku='$id_buku'";

    } else {
        echo "<script>alert('❌ Upload foto gagal!'); window.history.back();</script>";
        exit;
    }

} else {

    // [FIX] Tambah update kategori dan stok_total
    $query = "UPDATE raihanf_buku SET 
        raihanf_judul='$judul',
        raihanf_pengarang='$pengarang',
        raihanf_penerbit='$penerbit',
        raihanf_tahun_terbit='$tahun_terbit',
        raihanf_kategori='$kategori',
        raihanf_stok_total='$stok_total'
        WHERE raihanf_id_buku='$id_buku'";
}

// ================== EKSEKUSI ==================
if (mysqli_query($koneksi, $query)) {
    echo "<script>
        alert('✅ Data berhasil diupdate!');
        window.location='data_buku.php';
    </script>";
} else {
    echo "<script>
        alert('❌ Gagal update data: " . mysqli_error($koneksi) . "');
        window.history.back();
    </script>";
}
?>
