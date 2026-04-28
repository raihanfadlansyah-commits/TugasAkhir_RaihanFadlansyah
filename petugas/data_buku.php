<?php
session_start();
require_once "../koneksi_raihanf.php";

if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'petugas') {
    header("Location: ../raihanf_index.php");
    exit;
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Data Buku</title>

    <style>
        body {
            margin: 0;
            font-family: Arial;
            background: #f4f6f9;
        }

        *:focus {
            outline: none;
        }

        * {
            user-select: none;
            -webkit-user-select: none;
            caret-color: transparent;
        }

        input {
            user-select: text;
            -webkit-user-select: text;
            caret-color: auto;
            cursor: text;
        }

        img {
            pointer-events: none;
        }

        h2 {
            text-align: center;
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
            cursor: pointer;
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

        .search-box {
            margin-top: 15px;
        }

        .search-box input[type="text"] {
            padding: 8px;
            width: 280px;
        }

        .search-box input[type="submit"] {
            padding: 8px 12px;
            background: #2c3e50;
            border: none;
            color: white;
            cursor: pointer;
            border-radius: 4px;
        }

        .container {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-top: 20px;
        }

        .card {
            background: white;
            padding: 15px;
            border-radius: 10px;
            width: 220px;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .img-box {
            width: 100%;
            height: 180px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f4f6f9;
            border-radius: 8px;
            overflow: hidden;
        }

        .img-box img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .a {
            text-decoration: none;
            padding: 6px 10px;
            background: #2c3e50;
            color: white;
            border-radius: 5px;
            font-size: 12px;
            margin: 2px;
            display: inline-block;
            cursor: pointer;
        }

        .a:hover {
            background: #34495e;
        }

        .a.hapus {
            background: #e74c3c;
        }

        .a.hapus:hover {
            background: #c0392b;
        }

        .aksi {
            margin-top: 10px;
        }

        .stok-habis {
            color: #e74c3c;
            font-size: 12px;
            font-weight: bold;
        }

        img.logo {
            width: 120px;
            margin-bottom: 10px;
            padding-left: 45px;
            pointer-events: none;
        }
    </style>

</head>

<body>

    <div class="sidebar">
        <img src="../assets/img_buku/Group 1.png" class="logo">
        <a href="dashboard_petugas.php">🏠 Dashboard</a>
        <a href="data_buku.php">📚 Data Buku</a>
        <a href="data_peminjaman.php">📄 Data Peminjaman</a>
        <a href="data_siswa.php">👨‍🎓 Data Siswa</a>
        <a href="data_pengunjung.php">👁️ Data Pengunjung</a>
        <a href="../raihanf_logout.php">🚪 Logout</a>
    </div>

    <div class="main">

        <div class="header">
            <h2>📚 Data Buku Perpustakaan</h2>
        </div>

        <div class="search-box">
            <form method="get">
                <input type="text" name="search"
                    placeholder="Cari judul, pengarang, penerbit, kategori..."
                    value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                <input type="submit" value="Cari">
            </form>
        </div>

        <br>
        <a class="a" href="tambah_buku.php">+ Tambah Buku</a>
        <a class="a" href="pinjam_buku.php">📖 Pinjam Buku</a>
        <br>

        <div class="container">

            <?php
            $search = isset($_GET['search']) ? mysqli_real_escape_string($koneksi, $_GET['search']) : '';

            $query = mysqli_query($koneksi, "
                SELECT 
                    b.*,
                    COALESCE(SUM(
                        CASE WHEN p.raihanf_status = 'Dipinjam' THEN d.raihanf_total_buku ELSE 0 END
                    ), 0) AS total_dipinjam
                FROM raihanf_buku b
                LEFT JOIN raihanf_detail_peminjaman d ON b.raihanf_id_buku = d.raihanf_id_buku
                LEFT JOIN raihanf_peminjaman p ON d.raihaf_id_peminjaman = p.raihanf_id_peminjaman
                " . ($search ? "WHERE 
                    b.raihanf_judul     LIKE '%$search%' OR
                    b.raihanf_pengarang LIKE '%$search%' OR
                    b.raihanf_penerbit  LIKE '%$search%' OR
                    b.raihanf_kategori  LIKE '%$search%'" : "") . "
                GROUP BY b.raihanf_id_buku
            ");

            while ($data = mysqli_fetch_assoc($query)) {
                $stok_total    = $data['raihanf_stok_total'];
                $dipinjam      = (int)$data['total_dipinjam'];
                $stok_tersedia = max(0, $stok_total - $dipinjam);
            ?>

                <div class="card">

                    <div class="img-box">
                        <?php if ($data['raihanf_img']) { ?>
                            <img src="../assets/img_buku/<?= htmlspecialchars($data['raihanf_img']); ?>">
                        <?php } else { ?>
                            <p>Tidak ada gambar</p>
                        <?php } ?>
                    </div>

                    <h3><?= htmlspecialchars($data['raihanf_judul']); ?></h3>

                    <p><b>Kategori:</b> <?= htmlspecialchars($data['raihanf_kategori']); ?></p>
                    <p><b>Pengarang:</b> <?= htmlspecialchars($data['raihanf_pengarang']); ?></p>
                    <p><b>Penerbit:</b> <?= htmlspecialchars($data['raihanf_penerbit']); ?></p>
                    <p><b>Tahun:</b> <?= htmlspecialchars($data['raihanf_tahun_terbit']); ?></p>
                    <p><b>Stok Total:</b> <?= $stok_total; ?></p>
                    <p><b>Tersedia:</b>
                        <?php if ($stok_tersedia > 0): ?>
                            <span style="color:#27ae60;font-weight:bold"><?= $stok_tersedia; ?></span>
                        <?php else: ?>
                            <span class="stok-habis">Habis</span>
                        <?php endif; ?>
                    </p>

                    <div class="aksi">
                        <a class="a" href="edit_buku.php?raihanf_id_buku=<?= $data['raihanf_id_buku']; ?>">Edit</a>
                        <a class="a hapus" href="hapus_buku.php?raihanf_id_buku=<?= $data['raihanf_id_buku']; ?>"
                            onclick="return confirm('Yakin hapus buku ini?')">Hapus</a>
                    </div>

                </div>

            <?php } ?>

        </div>
    </div>

</body>

</html>