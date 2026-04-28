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

// FILTER TANGGAL
$filter_tgl = isset($_GET['filter_tgl']) ? mysqli_real_escape_string($koneksi, $_GET['filter_tgl']) : '';
?>

<!DOCTYPE html>
<html>

<head>
    <title>Data Pengunjung</title>
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

        .filter-bar {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin: 15px 0;
            align-items: center;
        }

        .filter-bar input[type="text"],
        .filter-bar input[type="date"] {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
        }

        .filter-bar input[type="text"] {
            width: 220px;
        }

        .filter-bar input[type="submit"] {
            padding: 8px 14px;
            background: #2c3e50;
            border: none;
            color: white;
            cursor: pointer;
            border-radius: 5px;
            font-size: 14px;
        }

        .filter-bar input[type="submit"]:hover {
            background: #34495e;
        }

        .filter-bar a.reset {
            padding: 8px 12px;
            background: #7f8c8d;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 14px;
        }

        .filter-bar a.reset:hover {
            background: #636e72;
        }

        .info-total {
            font-size: 14px;
            color: #555;
            margin-bottom: 8px;
        }

        .table-container {
            margin-top: 10px;
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

        .badge-durasi {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 12px;
            background: #dfe6e9;
            color: #2d3436;
        }

        img {
            width: 120px;
            margin-bottom: 10px;
            padding-left: 45px;
        }

        .empty-msg {
            text-align: center;
            color: #999;
            padding: 20px;
        }

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
        <h2>👁️ Data Pengunjung</h2>
    </div>

    <div class="filter-bar">
        <form method="get" style="display:flex; flex-wrap:wrap; gap:10px;">
            <input type="text" name="search" placeholder="Cari nama..."
                value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
            <input type="date" name="filter_tgl"
                value="<?= htmlspecialchars($_GET['filter_tgl'] ?? '') ?>">
            <input type="submit" value="🔍 Cari">
            <?php if ($search || $filter_tgl): ?>
                <a href="data_pengunjung.php" class="reset">✖ Reset</a>
            <?php endif; ?>
        </form>
    </div>

    <?php
    $where = [];

    if ($search) {
        $where[] = "(
            p.raihanf_nama LIKE '%$search%' OR
            p.raihanf_kelas LIKE '%$search%' OR
            p.raihanf_jurusan LIKE '%$search%' OR
            p.raihanf_id_user LIKE '%$search%'
        )";
    }

    if ($filter_tgl) {
        $where[] = "DATE(p.raihanf_waktu_masuk) = '$filter_tgl'";
    }

    $whereSQL = count($where) ? "WHERE " . implode(" AND ", $where) : "";

    // TOTAL DATA
    $totalQuery = mysqli_query($koneksi, "
        SELECT COUNT(*) AS total
        FROM raihanf_riwayat_pengunjung p
        $whereSQL
    ");
    $totalRow = mysqli_fetch_assoc($totalQuery);
    $total = $totalRow['total'];

    $query = mysqli_query($koneksi, "
        SELECT *
        FROM raihanf_riwayat_pengunjung p
        $whereSQL
        ORDER BY p.raihanf_waktu_masuk DESC
    ");
    ?>

    <!-- TOTAL INFO -->
    <div class="info-total">
        Total data: <strong><?= $total ?></strong> kunjungan
        <?= $filter_tgl ? " pada tanggal <strong>" . htmlspecialchars($filter_tgl) . "</strong>" : "" ?>
        <?= $search ? " | Kata kunci: <strong>" . htmlspecialchars($search) . "</strong>" : "" ?>
    </div>

    <div class="table-container">
        <table>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Masuk</th>
                <th>Keluar</th>
                <th>Durasi</th>
            </tr>

            <?php
            $no = 1;

            if (mysqli_num_rows($query) == 0): ?>
                <tr>
                    <td colspan="5" class="empty-msg">📭 Tidak ada data pengunjung.</td>
                </tr>
            <?php else:
                while ($data = mysqli_fetch_assoc($query)):

                    if (empty($data['raihanf_waktu_keluar'])) {
                        $keluar = "-";
                        $labelDurasi = "-";
                    } else {
                        $keluar = date('d-m-Y H:i', strtotime($data['raihanf_waktu_keluar']));
                        $durasi = (strtotime($data['raihanf_waktu_keluar']) - strtotime($data['raihanf_waktu_masuk'])) / 60;

                        if ($durasi < 1) {
                            $labelDurasi = "< 1 menit";
                        } elseif ($durasi < 60) {
                            $labelDurasi = floor($durasi) . " menit";
                        } else {
                            $jam = floor($durasi / 60);
                            $menit = floor($durasi % 60);
                            $labelDurasi = $jam . " jam " . ($menit ? $menit . " menit" : "");
                        }
                    }
            ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars($data['raihanf_nama']) ?></td>
                    <td><?= date('d-m-Y H:i', strtotime($data['raihanf_waktu_masuk'])) ?></td>
                    <td><?= $keluar ?></td>
                    <td><span class="badge-durasi"><?= $labelDurasi ?></span></td>
                </tr>
            <?php endwhile;
            endif; ?>

        </table>
    </div>

</div>

</body>

</html>