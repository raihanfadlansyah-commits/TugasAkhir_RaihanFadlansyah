<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Siswa</title>
    <link rel="stylesheet" href="2.css">
</head>

<body>
    <center>
        <h1>Tambah Siswa</h1>
        <?php
        require_once "koneksi_raihanf.php";

        if (isset($_POST['simpan'])) {
            $id_siswa = $_POST['raihanf_id_siswa'];
            $nis = $_POST['raihanf_nis'];
            $uid_rfid = $_POST['raihanf_uid_rfid'];
            $nama = $_POST['raihanf_nama'];
            $kelas = $_POST['raihanf_kelas'];
            $no_telp = $_POST['raihanf_no_telp'];

            $cek = $koneksi->query("SELECT * FROM raihanf_siswa WHERE raihanf_id_siswa='$id_siswa'");

            if ($cek->num_rows > 0) {
                echo "<script>alert('❌ Data sudah ada!');</script>";
            } else {
                $sql = "INSERT INTO raihanf_siswa (raihanf_id_siswa, raihanf_nis, raihanf_uid_rfid, raihanf_nama, raihanf_kelas, raihanf_no_telp) VALUES ('$id_siswa', '$nis', '$uid_rfid', '$nama', '$kelas', '$no_telp')";

                if ($koneksi->query($sql) === TRUE) {
                    echo "<script>alert('✅ Data Berhasil Ditambahkan!');
                    window.location='data_siswa.php'</script>";
                } else {
                    echo "<script>alert('Gagal menambahkan data: " . $koneksi->error . "');</script>";
                }
            }
        }
        ?>
        <form action="tambah_siswa.php" method="post">
            <table>
                <tr>
                    <th>ID Siswa</th>
                    <td><input type="text" name="raihanf_id_siswa" required></td>
                </tr>
                <tr>
                    <th>NIS</th>
                    <td><input type="text" name="raihanf_nis" required></td>
                </tr>
                <tr>
                    <th>UID RFID</th>
                    <td><input type="text" name="raihanf_uid_rfid" required></td>
                </tr>
                <tr>
                    <th>Nama</th>
                    <td><input type="text" name="raihanf_nama" required></td>
                </tr>
                <tr>
                    <th>Kelas</th>
                    <td><input type="text" name="raihanf_kelas" required></td>
                </tr>
                <tr>
                    <th>No Telp</th>
                    <td><input type="text" name="raihanf_no_telp" required></td>
                </tr>
                <tr></tr>
                <td colspan="2"><input class="button" type="submit" name="simpan" value="simpan"></td>
                </tr>
            </table>
    </center>
</body>

</html>