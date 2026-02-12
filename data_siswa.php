<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Siswa</title>
    <link rel="stylesheet" href="2.css">
</head>
<body>
    <center>
        <h1>Data Siswa</h1>
        <form action="" method="post">
            <table>
                <tr>
                    <th>ID_Siswa</th>
                    <th>NIS</th>
                    <th>UID_RFID</th>
                    <th>Nama</th>
                    <th>Kelas</th>
                    <th>No Telpon</th>
                    <th>Aksi</th>
                </tr>
                <?php
                require_once "koneksi_raihanf.php";
                $query = mysqli_query($koneksi, "SELECT * FROM raihanf_siswa");
                while ($data = mysqli_fetch_array($query)) {
                    echo "<tr>";
                    echo "<td>" . $data['raihanf_id_siswa'] . "</td>";
                    echo "<td>" . $data['raihanf_nis'] . "</td>";
                    echo "<td>" . $data['raihanf_uid_rfid'] . "</td>";
                    echo "<td>" . $data['raihanf_nama'] . "</td>";
                    echo "<td>" . $data['raihanf_kelas'] . "</td>";
                    echo "<td>" . $data['raihanf_no_telp'] . "</td>";
                    echo "<td><a class='a' href='edit_siswa.php?raihanf_id_siswa=" . $data['raihanf_id_siswa'] . "'>Edit</a> | <a class='a' href='hapus_siswa.php?raihanf_id_siswa=" . $data['raihanf_id_siswa'] . "' onclick=\"return confirm('Apakah Anda yakin ingin menghapus data ini?')\">Hapus</a></td>";
                    echo "</tr>";
                }
                ?>
                <tr>
                <td colspan="7"><a class="a" href="tambah_siswa.php">Tambah siswa</a></td>
            </table>
        </form>
    </center>
</body>
</html>