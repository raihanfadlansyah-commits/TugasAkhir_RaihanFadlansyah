<?php
session_start();
require_once "../koneksi_raihanf.php";

// Cek login admin
if (!isset($_SESSION['login']) || $_SESSION['login'] != "admin") {
    echo "<script>
            alert('Akses ditolak!');
            window.location='../raihanf_index.php';
          </script>";
    exit;
}

// Sesuai link di data_siswa.php: ?id=
$id_user = $_GET['id'] ?? '';

if (empty($id_user)) {
    echo "<script>
            alert('Data tidak valid!');
            window.location='data_siswa.php';
          </script>";
    exit;
}

// Pastikan user ada dan memang role siswa
$query = mysqli_query($koneksi, "
    SELECT raihanf_id_user 
    FROM raihanf_user 
    WHERE raihanf_id_user = '$id_user' 
    AND raihanf_role = 'siswa'
");
$data = mysqli_fetch_assoc($query);

if (!$data) {
    echo "<script>
            alert('Data siswa tidak ditemukan!');
            window.location='data_siswa.php';
          </script>";
    exit;
}

// Hapus data terkait dulu (urutan: detail -> peminjaman -> pengunjung -> user)
// Ambil id_peminjaman milik siswa ini dulu untuk hapus detail_peminjaman
$qPinjam = mysqli_query($koneksi, "
    SELECT raihanf_id_peminjaman 
    FROM raihanf_peminjaman 
    WHERE raihanf_id_user = '$id_user'
");

while ($row = mysqli_fetch_assoc($qPinjam)) {
    $idPinjam = $row['raihanf_id_peminjaman'];
    mysqli_query($koneksi, "
        DELETE FROM raihanf_detail_peminjaman 
        WHERE raihaf_id_peminjaman = '$idPinjam'
    ");
}

// Hapus peminjaman
$hapus1 = mysqli_query($koneksi, "
    DELETE FROM raihanf_peminjaman 
    WHERE raihanf_id_user = '$id_user'
");

// Hapus riwayat pengunjung (kolom yang benar: raihanf_id_user)
$hapus2 = mysqli_query($koneksi, "
    DELETE FROM raihanf_riwayat_pengunjung 
    WHERE raihanf_id_user = '$id_user'
");

// Hapus user
$hapus3 = mysqli_query($koneksi, "
    DELETE FROM raihanf_user 
    WHERE raihanf_id_user = '$id_user'
");

if ($hapus1 !== false && $hapus2 !== false && $hapus3 !== false) {
    echo "<script>
            alert('✅ Data siswa berhasil dihapus!');
            window.location='data_siswa.php';
          </script>";
} else {
    echo "<script>
            alert('❌ Gagal menghapus data: " . mysqli_error($koneksi) . "');
            window.location='data_siswa.php';
          </script>";
}
?>