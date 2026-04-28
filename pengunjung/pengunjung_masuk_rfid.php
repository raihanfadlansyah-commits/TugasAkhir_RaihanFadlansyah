<?php
session_start();
require_once "../koneksi_raihanf.php";

// Ambil log terakhir
$log = mysqli_query($koneksi, "
    SELECT 
        p.raihanf_waktu_masuk,
        p.raihanf_waktu_keluar,
        u.raihanf_nama,
        u.raihanf_kelas,
        u.raihanf_jurusan
    FROM raihanf_riwayat_pengunjung p
    JOIN raihanf_user u ON p.raihanf_id_user = u.raihanf_id_user
    ORDER BY p.raihanf_waktu_masuk DESC
    LIMIT 1
");

$dataLog = mysqli_fetch_assoc($log);

// Tampilkan hanya setelah scan
$showLog = isset($_SESSION['last_scan']);
unset($_SESSION['last_scan']);
?>

<!DOCTYPE html>
<html>

<head>
    <title>Scan RFID Masuk</title>

    <style>
        * {
            user-select: none;
            caret-color: transparent;
        }

        input {
            user-select: text;
            caret-color: auto;
        }

        body {
            margin: 0;
            font-family: Arial;
            background: url('../assets/bg-sekolah.jpg') no-repeat center center fixed;
            background-size: cover;
        }

        /* overlay gelap */
        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: -1;
        }

        .container {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin-top: 80px;
        }

        /* CARD STYLE (glass) */
        .box,
        .log-box {
            width: 350px;
            padding: 35px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 12px;
            backdrop-filter: blur(10px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            text-align: center;
            color: white;
        }

        input {
            width: 100%;
            padding: 10px;
            font-size: 18px;
            text-align: center;
            border-radius: 6px;
            border: none;

            /* FIX SIMETRIS */
            box-sizing: border-box;
        }

        img {
            width: 120px;
            margin-bottom: 10px;
            pointer-events: none;
        }

        h2,
        h3,
        h4 {
            color: black;
            margin: 10px 0;
        }
    </style>
</head>

<body>

    <div class="overlay"></div>

    <div class="container">

        <!-- SCAN -->
        <div class="box">
            <img src="../assets/img_buku/smkn 2 cimahi.png">

            <h2>📚 Perpustakaan</h2>
            <h3>Scan RFID - Masuk</h3>

            <form action="proses_masuk_rfid.php" method="post">
                <input type="text" name="rfid" placeholder="Tempel kartu..." autofocus required>
            </form>

            <?php
            if (isset($_SESSION['pesan'])) {
                echo "<div>" . $_SESSION['pesan'] . "</div>";
                unset($_SESSION['pesan']);
            }
            ?>
        </div>

        <!-- LOG -->
        <?php if ($dataLog && $showLog): ?>
            <div class="log-box">
                <h4>📋 Log</h4>
                <p><b>Nama:</b> <?= $dataLog['raihanf_nama'] ?></p>
                <p><b>Kelas:</b> <?= $dataLog['raihanf_kelas'] ?></p>
                <p><b>Jurusan:</b> <?= $dataLog['raihanf_jurusan'] ?></p>
                <p><b>Masuk:</b> <?= date('d-m-Y H:i', strtotime($dataLog['raihanf_waktu_masuk'])) ?></p>
                <p><b>Keluar:</b> <?= $dataLog['raihanf_waktu_keluar'] ? date('d-m-Y H:i', strtotime($dataLog['raihanf_waktu_keluar'])) : '-' ?></p>
            </div>

            <script>
                setTimeout(() => {
                    window.location.href = "pengunjung_masuk_rfid.php";
                }, 5000);
            </script>
        <?php endif; ?>

    </div>

</body>

</html>