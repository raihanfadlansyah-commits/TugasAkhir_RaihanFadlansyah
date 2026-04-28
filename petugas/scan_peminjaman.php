<?php
session_start();
require_once "../koneksi_raihanf.php";

if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'petugas') {
    header("Location: ../raihanf_index.php");
    exit;
}

$keranjang = $_SESSION['keranjang'] ?? [];

// [FIX] Redirect ke pinjam_buku.php (bukan data_peminjaman.php) jika keranjang kosong
if (empty($keranjang)) {
    header("Location: pinjam_buku.php");
    exit;
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Scan Peminjaman</title>

    <style>
        body {
            font-family: Arial;
            background: #f4f6f9;
        }

        *:focus {
            outline: none;
        }

        .box {
            width: 380px;
            margin: 60px auto;
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 3px 8px rgba(0,0,0,0.1);
            text-align: center;
        }

        h2 {
            margin-top: 0;
        }

        label {
            display: block;
            text-align: left;
            font-weight: bold;
            font-size: 14px;
            margin-top: 12px;
            margin-bottom: 4px;
        }

        input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        button {
            width: 100%;
            padding: 10px;
            background: #2c3e50;
            color: white;
            border: none;
            border-radius: 5px;
            margin-top: 15px;
            cursor: pointer;
        }

        button:hover {
            background: #34495e;
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

        hr {
            margin: 20px 0;
        }

        .buku-list {
            text-align: left;
        }

        .buku-list p {
            margin: 5px 0;
            padding: 6px 10px;
            background: #f4f6f9;
            border-radius: 5px;
            font-size: 14px;
        }
    </style>
</head>

<body>

    <div class="box">

        <a href="pinjam_buku.php" class="back">⬅ Kembali</a>

        <h2>📡 Scan RFID</h2>
        <p>Scan kartu RFID siswa untuk memproses peminjaman</p>

        <form action="proses_multi_pinjam.php" method="POST">

            <label>RFID Siswa</label>
            <input type="text" name="rfid" placeholder="Scan / ketik RFID..." autofocus required>

            <!-- [FIX] Input tenggat datetime-local agar sesuai kolom DATETIME di database -->
            <label>Tenggat Pengembalian</label>
            <input type="datetime-local" name="tenggat"
                value="<?= date('Y-m-d\TH:i', strtotime('+7 days')) ?>" required>

            <button type="submit">✅ Proses Peminjaman</button>

        </form>

        <hr>

        <h3>📚 Buku Dipilih</h3>
        <div class="buku-list">
        <?php
        foreach ($keranjang as $id => $qty) {
            $q = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT raihanf_judul FROM raihanf_buku WHERE raihanf_id_buku='" . mysqli_real_escape_string($koneksi, $id) . "'"));
            if ($q) {
                echo "<p>📖 " . htmlspecialchars($q['raihanf_judul']) . " (x$qty)</p>";
            }
        }
        ?>
        </div>

    </div>

</body>

</html>
