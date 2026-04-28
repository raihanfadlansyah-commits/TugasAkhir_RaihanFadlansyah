<?php
require_once "../koneksi_raihanf.php";
session_start();

if (!isset($_SESSION['login']) || $_SESSION['login'] != "admin") {
    header("Location: ../raihanf_index.php");
    exit;
}

// Sesuai link di data_siswa.php: ?id=
$id_user = $_GET['id'] ?? '';

if (empty($id_user)) {
    echo "<script>alert('ID tidak valid!'); window.location='data_siswa.php';</script>";
    exit;
}

// Langsung query ke raihanf_user (tidak ada tabel raihanf_siswa di DB)
$data_siswa = mysqli_query($koneksi, "
    SELECT *
    FROM raihanf_user
    WHERE raihanf_id_user = '$id_user'
    AND raihanf_role = 'siswa'
");

$data = mysqli_fetch_assoc($data_siswa);

if (!$data) {
    echo "<script>alert('Data siswa tidak ditemukan!'); window.location='data_siswa.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Edit Siswa</title>

    <style>
        body {
            margin: 0;
            font-family: Arial;
            background: #f4f6f9;
        }

        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1);
            width: 450px;
        }

        h1 {
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            padding: 8px;
        }

        input,
        select {
            width: 100%;
            padding: 10px;
            margin: 5px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 14px;
        }

        .button {
            width: 100%;
            background: #2c3e50;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .button:hover {
            background: #34495e;
        }

        input[readonly] {
            background: #eee;
        }

        .back {
            display: inline-block;
            margin-bottom: 15px;
            text-decoration: none;
            color: white;
            background: #7f8c8d;
            padding: 8px 12px;
            border-radius: 5px;
        }

        .back:hover {
            background: #636e72;
        }
    </style>

</head>

<body>

    <div class="container">
        <div class="card">

            <h1>Edit Siswa</h1>

            <form action="update_siswa.php" method="post">

                <!-- ID User sebagai identifier utama -->
                <input type="hidden" name="raihanf_id_user" value="<?= htmlspecialchars($data['raihanf_id_user']) ?>">

                <table>

                    <tr>
                        <td>ID User</td>
                        <td>
                            <input type="text" value="<?= htmlspecialchars($data['raihanf_id_user']) ?>" readonly>
                        </td>
                    </tr>

                    <tr>
                        <td>NIS</td>
                        <td>
                            <!-- raihanf_nomor_induk sesuai kolom di raihanf_user -->
                            <input type="text" name="raihanf_nomor_induk" value="<?= htmlspecialchars($data['raihanf_nomor_induk']) ?>" readonly>
                        </td>
                    </tr>

                    <tr>
                        <td>RFID</td>
                        <td>
                            <input type="text" name="raihanf_rfid" value="<?= htmlspecialchars($data['raihanf_rfid']) ?>">
                        </td>
                    </tr>

                    <tr>
                        <td>Nama</td>
                        <td>
                            <!-- raihanf_nama sesuai kolom di raihanf_user -->
                            <input type="text" name="raihanf_nama" value="<?= htmlspecialchars($data['raihanf_nama']) ?>">
                        </td>
                    </tr>

                    <tr>
                        <td>Kelas</td>
                        <td>
                            <select name="raihanf_kelas">
                                <option value="">-- Pilih Kelas --</option>
                                <?php foreach (['X', 'XI', 'XII'] as $k): ?>
                                    <option value="<?= $k ?>" <?= $data['raihanf_kelas'] == $k ? 'selected' : '' ?>>
                                        <?= $k ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td>Jurusan</td>
                        <td>
                            <select name="raihanf_jurusan">
                                <option value="">-- Pilih Jurusan --</option>
                                <?php
                                $jurusanList = [
                                    'Mekatronika',
                                    'Kimia Industri',
                                    'Perkembangan Perangkat Lunak',
                                    'Animasi',
                                    'Desain Komunikasi Visual',
                                    'Teknik Permesinan'
                                ];
                                foreach ($jurusanList as $j): ?>
                                    <option value="<?= $j ?>" <?= $data['raihanf_jurusan'] == $j ? 'selected' : '' ?>>
                                        <?= $j ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td>No Telpon</td>
                        <td>
                            <input type="text" name="raihanf_no_telpon" value="<?= htmlspecialchars($data['raihanf_no_telpon']) ?>">
                        </td>
                    </tr>

                    <tr>
                        <td colspan="2">
                            <input class="button" type="submit" name="update" value="Update Data">
                        </td>
                    </tr>

                    <tr>
                        <td colspan="2">
                            <a href="data_siswa.php" class="back">⬅ Kembali</a>
                        </td>
                    </tr>

                </table>

            </form>

        </div>
    </div>

</body>

</html>