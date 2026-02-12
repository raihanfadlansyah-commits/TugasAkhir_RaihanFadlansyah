<?php
include "koneksi_raihanf.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Siswa</title>
    <link rel="stylesheet" href="2.css">
</head>

<body>
    <center>
        <h1>Edit Data Siswa</h1>
        <?php
        require_once "koneksi_raihanf.php";
        $id_siswa = $_GET['raihanf_id_siswa'];
        $datasiswa = mysqli_query($koneksi, "SELECT * FROM raihanf_siswa WHERE raihanf_id_siswa='$id_siswa'");
        while ($data = mysqli_fetch_array($datasiswa)) {
        ?>
            <form action="update_siswa.php" method="post">
                <table>
                    <tr>
                        <th>ID Siswa</th>
                        <td><input readonly type="text" name="raihanf_id_siswa" value="<?php echo $data['raihanf_id_siswa']; ?>"></td>
                    </tr>

                    <tr>
                        <th>Nis</th>
                        <td><input readonly type="number" name="raihanf_nis" value="<?php echo $data['raihanf_nis'];?>"></td>
                    </tr>

                    <tr>UID RFID</tr>
                        <th>UID RFID</th>
                        <td><input readonly type="text" name="raihanf_uid_rfid" value="<?php echo $data['raihanf_uid_rfid'];?>"></td>
                    </tr>

                    <tr></tr>
                        <th>Nama</th>
                        <td><input type="text" name="raihanf_nama" value="<?php echo $data['raihanf_nama'];?>"></td>
                    </tr>

                    <tr>
                        <th>Kelas</th>
                        <td><input type="text" name="raihanf_kelas" value="<?php echo $data['raihanf_kelas'];?>"></td>
                    </tr>

                    <tr>
                        <th>No Telpon</th>
                        <td><input type="number" name="raihanf_no_telp" value="<?php echo $data['raihanf_no_telp'];?>"></td>
                    </tr>
                    <tr>
                        <td colspan="2" align="center"><input class="button" type="submit" value="Update Data"></td>
                </table>
            <?php
        }
            ?>
    </center>
</body>

</html>