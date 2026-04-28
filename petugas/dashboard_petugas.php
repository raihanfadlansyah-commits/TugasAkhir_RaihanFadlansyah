<?php
session_start();
require_once "../koneksi_raihanf.php";

// VALIDASI LOGIN PETUGAS
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'petugas') {
    header("Location: ../raihanf_index.php");
    exit;
}

date_default_timezone_set('Asia/Jakarta');

/* ================== DATA ================== */
$pengunjung = mysqli_fetch_assoc(mysqli_query($koneksi, "
    SELECT COUNT(*) AS total FROM raihanf_riwayat_pengunjung
"));

$siswa = mysqli_fetch_assoc(mysqli_query($koneksi, "
    SELECT COUNT(*) AS total FROM raihanf_user WHERE raihanf_role='siswa'
"));

$buku = mysqli_fetch_assoc(mysqli_query($koneksi, "
    SELECT COUNT(*) AS total FROM raihanf_buku
"));

// [FIX] Nama tabel: raihanf_transaksi_peminjaman → raihanf_peminjaman
// [FIX] Nilai status ENUM: 'dipinjam' → 'Dipinjam'
$pinjam = mysqli_fetch_assoc(mysqli_query($koneksi, "
    SELECT COUNT(*) AS total FROM raihanf_peminjaman 
    WHERE raihanf_status='Dipinjam'
"));

$total_peminjaman = mysqli_fetch_assoc(mysqli_query($koneksi, "
    SELECT COUNT(*) AS total FROM raihanf_peminjaman
"));

// [FIX] Nama tabel + kolom deadline: raihanf_tgl_kembali → raihanf_tenggat_waktu
//       tgl_kembali NULL artinya belum dikembalikan, jadi cek tenggat_waktu yang sudah lewat
$telat_total = mysqli_fetch_assoc(mysqli_query($koneksi, "
    SELECT COUNT(*) AS total FROM raihanf_peminjaman 
    WHERE raihanf_status='Dipinjam'
    AND raihanf_tenggat_waktu < NOW()
"));

$visitor_hari = mysqli_fetch_assoc(mysqli_query($koneksi, "
    SELECT COUNT(*) AS total FROM raihanf_riwayat_pengunjung 
    WHERE DATE(raihanf_waktu_masuk)=CURDATE()
"));

// [FIX] Nama tabel
$pinjam_hari = mysqli_fetch_assoc(mysqli_query($koneksi, "
    SELECT COUNT(*) AS total FROM raihanf_peminjaman 
    WHERE DATE(raihanf_tgl_pinjam)=CURDATE()
"));
?>

<!DOCTYPE html>
<html>

<head>
    <title>Dashboard Petugas</title>

    <style>
        * {
            caret-color: transparent !important;
            outline: none !important;
        }

        body {
            margin: 0;
            font-family: Arial;
            background: #f4f6f9;
        }

        .sidebar {
            width: 220px;
            height: 100vh;
            background: #2c3e50;
            position: fixed;
            padding-top: 20px;
        }

        .sidebar a {
            display: block;
            color: white;
            padding: 12px;
            text-decoration: none;
        }

        .sidebar a:hover {
            background: #34495e;
        }

        .main {
            margin-left: 220px;
            padding: 20px;
        }

        .header {
            background: white;
            padding: 15px;
            border-radius: 8px;
        }

        .cards {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            margin-top: 20px;
        }

        .card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            width: 200px;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1);
            user-select: none;
        }

        .card h3 {
            margin: 0;
            font-size: 14px;
            color: gray;
        }

        .card h1 {
            margin: 10px 0 0;
        }

        .info {
            margin-top: 25px;
            background: white;
            padding: 15px;
            border-radius: 10px;
        }

        img {
            width: 120px;
            margin-bottom: 10px;
            padding-left: 45px;
        }
    </style>

</head>

<body>

    <div class="sidebar">
        <img src="../assets/img_buku/Group 1.png" alt="">
        <a href="dashboard_petugas.php">🏠 Dashboard</a>
        <a href="data_buku.php">📚 Data Buku</a>
        <a href="data_peminjaman.php">📄 Data Peminjaman</a>
        <a href="data_siswa.php">👨‍🎓 Data Siswa</a>
        <a href="data_pengunjung.php">👁️ Data Pengunjung</a>
        <a href="../raihanf_logout.php">🚪 Logout</a>
    </div>

    <div class="main">

        <div class="header">
            <h2>Dashboard Petugas Perpustakaan</h2>
            <p>Selamat datang, <?php echo htmlspecialchars($_SESSION['username']); ?></p>
        </div>

        <div class="cards">

            <div class="card">
                <h3>Total Pengunjung</h3>
                <h1><?php echo $pengunjung['total']; ?></h1>
            </div>

            <div class="card">
                <h3>Total Siswa</h3>
                <h1><?php echo $siswa['total']; ?></h1>
            </div>

            <div class="card">
                <h3>Total Buku</h3>
                <h1><?php echo $buku['total']; ?></h1>
            </div>

            <div class="card">
                <h3>Buku Dipinjam</h3>
                <h1><?php echo $pinjam['total']; ?></h1>
            </div>

            <div class="card">
                <h3>Total Peminjaman</h3>
                <h1><?php echo $total_peminjaman['total']; ?></h1>
            </div>

            <div class="card">
                <h3>Total Buku Terlambat</h3>
                <h1 style="color:red;">
                    <?php echo $telat_total['total']; ?>
                </h1>
            </div>

        </div>

        <div class="info">
            <h3>📊 Aktivitas Hari Ini</h3>
            <p>👁️ Visitor hari ini: <b><?php echo $visitor_hari['total']; ?></b></p>
            <p>📌 Peminjaman hari ini: <b><?php echo $pinjam_hari['total']; ?></b></p>
        </div>

    </div>

</body>

</html>