<?php
require_once "../koneksi_raihanf.php";
session_start();

// VALIDASI PETUGAS
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'petugas') {
    header("Location: ../raihanf_index.php");
    exit;
}

// SEARCH
$search = isset($_GET['search']) ? mysqli_real_escape_string($koneksi, $_GET['search']) : '';
?>

<!DOCTYPE html>
<html>

<head>
    <title>Data Siswa</title>
    <style>
        body {
            margin: 0;
            font-family: Arial;
            background: #f4f6f9;
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

        .btn {
            text-decoration: none;
            padding: 8px 12px;
            background: #2c3e50;
            color: white;
            border-radius: 5px;
        }

        .btn:hover {
            background: #34495e;
        }

        .search-box {
            margin: 15px 0;
        }

        .search-box input[type="text"] {
            padding: 8px;
            width: 250px;
        }

        .search-box input[type="submit"] {
            padding: 8px 12px;
            background: #2c3e50;
            border: none;
            color: white;
            cursor: pointer;
        }

        .table-container {
            margin-top: 20px;
            background: white;
            padding: 15px;
            border-radius: 10px;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 10px;
            text-align: center;
            white-space: nowrap;
        }

        th {
            background: #2c3e50;
            color: white;
        }

        tr:nth-child(even) {
            background: #f2f2f2;
        }

        .aksi a {
            text-decoration: none;
            padding: 5px 8px;
            background: #2c3e50;
            color: white;
            border-radius: 4px;
            font-size: 12px;
        }

        .aksi .hapus {
            background: #e74c3c;
        }

        img {
            width: 120px;
            margin-bottom: 10px;
            padding-left: 45px;
        }

        /* === TAMBAHAN: HILANGKAN CARET === */
        * {
            caret-color: transparent;
        }

        input, textarea {
            caret-color: auto;
        }
    </style>
</head>

<body>

    <div class="sidebar">
        <img src="../assets/img_buku/Group 1.png">
        <a href="dashboard_petugas.php">🏠 Dashboard</a>
        <a href="data_buku.php">📚 Data Buku</a>
        <a href="data_peminjaman.php">📄 Data Peminjaman</a>
        <a href="data_siswa.php">👨‍🎓 Data Siswa</a>
        <a href="data_pengunjung.php">👁️ Data Pengunjung</a>
        <a href="../raihanf_logout.php">🚪 Logout</a>
    </div>

    <div class="main">

        <div class="header">
            <h2>👨‍🎓 Data Siswa</h2>
        </div>

        <!-- SEARCH -->
        <div class="search-box">
            <form method="get">
                <input type="text" name="search" placeholder="Cari nama, kelas, jurusan..."
                    value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                <input type="submit" value="Cari">
            </form>
        </div>

        <a class="btn" href="tambah_siswa.php">+ Tambah Siswa</a>

        <div class="table-container">
            <table>
                <tr>
                    <th>No</th>
                    <th>ID User</th>
                    <th>RFID</th>
                    <th>NIS</th>
                    <th>Nama</th>
                    <th>Kelas</th>
                    <th>Jurusan</th>
                    <th>No Telpon</th>
                    <th>Aksi</th>
                </tr>

                <?php
                $no = 1;

                $query = mysqli_query($koneksi, "
                    SELECT *
                    FROM raihanf_user
                    WHERE raihanf_role = 'siswa'
                    " . ($search ? "AND (
                        raihanf_nama    LIKE '%$search%' OR
                        raihanf_kelas   LIKE '%$search%' OR
                        raihanf_jurusan LIKE '%$search%'
                    )" : "") . "
                    ORDER BY raihanf_id_user ASC
                ");

                while ($data = mysqli_fetch_assoc($query)) {
                ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= htmlspecialchars($data['raihanf_id_user']) ?></td>
                        <td><?= htmlspecialchars($data['raihanf_rfid']) ?></td>
                        <td><?= htmlspecialchars($data['raihanf_nomor_induk']) ?></td>
                        <td><?= htmlspecialchars($data['raihanf_nama']) ?></td>
                        <td><?= htmlspecialchars($data['raihanf_kelas']) ?></td>
                        <td><?= htmlspecialchars($data['raihanf_jurusan']) ?></td>
                        <td><?= htmlspecialchars($data['raihanf_no_telpon']) ?></td>
                        <td class="aksi">
                            <a href="edit_siswa.php?id=<?= $data['raihanf_id_user'] ?>">Edit</a>
                            <a class="hapus"
                                href="hapus_siswa.php?id=<?= $data['raihanf_id_user'] ?>"
                                onclick="return confirm('Yakin hapus data?')">Hapus</a>
                        </td>
                    </tr>
                <?php } ?>

            </table>
        </div>

    </div>

</body>

</html>