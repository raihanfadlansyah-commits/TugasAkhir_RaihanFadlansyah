<?php
require_once "koneksi_raihanf.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <link rel="stylesheet" href="2.css">
</head>

<body>
    <center>
        <h1>Welcome to the Admin Dashboard</h1>
    </center>
    <ul>
        <li><a href="dashboard_admin.php">Dashboard</a></li>
        <li><a href="data_siswa.php">Data Siswa</a></li>
        <li><a href="#contact">Contact</a></li>
        <li><a href="#about">About</a></li>
    </ul>
    <br>
    <center>
        <form action="">
            <table>
                <tr>
                    <th>Total Siswa</th>
                    <th>Total Buku</th>
                    <th>Buku Dipinjam</th>
                    <th>Total Transaksi</th>
                </tr>

                <tr>
                    <td><?php $querysiswa = mysqli_query($koneksi, "SELECT COUNT(*) AS total_siswa FROM raihanf_siswa");
                        $siswa = mysqli_fetch_array($querysiswa);
                        echo $siswa['total_siswa']; ?>
                    </td>

                    <td><?php $querybuku = mysqli_query($koneksi, "SELECT COUNT(*) AS total_buku FROM raihanf_buku");
                        $buku = mysqli_fetch_array($querybuku);
                        echo $buku['total_buku']; ?>
                    </td>

                    <td><?php $querystatus = mysqli_query($koneksi, "SELECT COUNT(*) AS total_dipinjam FROM raihanf_transaksi_peminjaman WHERE raihanf_status = 'dipinjam'");
                        $status = mysqli_fetch_array($querystatus);
                        echo $status['total_dipinjam']; ?>
                    </td>

                    <td><?php $querytransaksi = mysqli_query($koneksi, "SELECT COUNT(*) AS total_transaksi FROM raihanf_transaksi_peminjaman");
                        $transaksi = mysqli_fetch_array($querytransaksi);
                        echo $transaksi['total_transaksi']; ?>
                    </td>
                </tr>
            </table>
        </form>
    </center>
</body>

</html>