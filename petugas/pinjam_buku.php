<?php
session_start();
require_once "../koneksi_raihanf.php";

if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'petugas') {
    header("Location: ../raihanf_index.php");
    exit;
}

if (!isset($_SESSION['keranjang'])) {
    $_SESSION['keranjang'] = [];
}

$search = isset($_GET['search']) ? mysqli_real_escape_string($koneksi, $_GET['search']) : '';
?>

<!DOCTYPE html>
<html>

<head>
    <title>Peminjaman Buku</title>

    <style>
        body {
            margin: 0;
            font-family: Arial;
            background: #f4f6f9;
        }

        *:focus {
            outline: none;
        }

        body,
        div,
        h1,
        h2,
        h3,
        p,
        span {
            user-select: none;
        }

        input {
            user-select: text;
            cursor: text;
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

        .header-box {
            background: white;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 15px;
        }

        .search-box {
            margin-top: 10px;
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
            border-radius: 5px;
            cursor: pointer;
        }

        .keranjang-info {
            display: inline-block;
            margin-left: 15px;
            padding: 8px 15px;
            background: #27ae60;
            color: white;
            border-radius: 5px;
            font-size: 14px;
        }

        .proses {
            margin-top: 10px;
            padding: 10px;
            background: #27ae60;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .proses:hover {
            background: #219a52;
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
            text-align: center;
        }

        .card.dipilih {
            border: 2px solid #27ae60;
        }

        .img-box {
            height: 150px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .img-box img {
            max-width: 100%;
            max-height: 100%;
        }

        .qty-box {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 5px;
            margin-top: 10px;
        }

        .qty-btn {
            width: 30px;
            height: 30px;
            border: none;
            background: #2c3e50;
            color: white;
            border-radius: 5px;
            cursor: pointer;
        }

        .qty-btn:disabled {
            background: #bdc3c7;
            cursor: not-allowed;
        }

        .qty-input {
            width: 60px;
            text-align: center;
            padding: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .stok-habis {
            color: #e74c3c;
            font-weight: bold;
            font-size: 13px;
            margin-top: 5px;
        }

        img.logo {
            width: 120px;
            margin-bottom: 10px;
            padding-left: 45px;
        }
    </style>

    <script>
        // Keranjang disimpan di session server, tapi kita juga track local untuk UI
        var keranjangLokal = <?= json_encode($_SESSION['keranjang'] ?? []) ?>;

        function updateQty(id, aksi) {
            let input = document.getElementById("qty_" + id);
            let val = parseInt(input.value) || 0;

            if (aksi === 'plus') val = 1;
            if (aksi === 'minus') val = 0;

            input.value = val;
            kirim(id, val);
        }

        function manualInput(id) {
            let input = document.getElementById("qty_" + id);
            let val = parseInt(input.value) || 0;
            if (val > 1) val = 1;
            if (val < 0) val = 0;
            input.value = val;
            kirim(id, val);
        }

        function kirim(id, qty) {
            // Update UI card border
            let card = document.getElementById("card_" + id);
            if (card) {
                if (qty > 0) card.classList.add('dipilih');
                else card.classList.remove('dipilih');
            }

            // Update counter
            if (qty > 0) keranjangLokal[id] = qty;
            else delete keranjangLokal[id];

            let jumlah = Object.keys(keranjangLokal).length;
            let el = document.getElementById('keranjang-count');
            if (el) el.textContent = jumlah + ' buku dipilih';

            fetch("set_keranjang.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: "id=" + encodeURIComponent(id) + "&qty=" + qty
            });
        }
    </script>
</head>

<body>

    <div class="sidebar">
        <img src="../assets/img_buku/Group 1.png" class="logo">
        <a href="dashboard_petugas.php">🏠 Dashboard</a>
        <a href="data_buku.php">📚 Data Buku</a>
        <a href="data_peminjaman.php">📄 Peminjaman</a>
        <a href="data_siswa.php">👨‍🎓 Data Siswa</a>
        <a href="data_pengunjung.php">👁️ Pengunjung</a>
        <a href="../raihanf_logout.php">🚪 Logout</a>
    </div>

    <div class="main">

        <div class="header-box">
            <h2>📚 Pilih Buku untuk Dipinjam</h2>
            <p>Centang buku yang ingin dipinjam, lalu klik <b>"Proses Semua"</b>.</p>
        </div>

        <!-- SEARCH -->
        <div class="search-box">
            <form method="GET" style="display:inline">
                <input type="text" name="search"
                    placeholder="Cari judul, kategori, pengarang..."
                    value="<?= htmlspecialchars($search) ?>">
                <input type="submit" value="Cari">
            </form>
            <span class="keranjang-info" id="keranjang-count">
                <?= count($_SESSION['keranjang'] ?? []) ?> buku dipilih
            </span>
        </div>

        <!-- PROSES -->
        <form action="scan_peminjaman.php" method="POST" style="margin-top:10px">
            <button class="proses">🛒 Proses Semua (Scan RFID)</button>
        </form>

        <div class="container">

            <?php
            // Hitung stok tersedia lewat detail_peminjaman
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
                    b.raihanf_kategori  LIKE '%$search%'" : "") . "
                GROUP BY b.raihanf_id_buku
            ");

            while ($b = mysqli_fetch_assoc($query)) {
                $id             = $b['raihanf_id_buku'];
                $qty            = $_SESSION['keranjang'][$id] ?? 0;
                $stok_tersedia  = max(0, $b['raihanf_stok_total'] - (int)$b['total_dipinjam']);
                $dipilih_class  = $qty > 0 ? 'dipilih' : '';
            ?>

                <div class="card <?= $dipilih_class ?>" id="card_<?= $id ?>">

                    <div class="img-box">
                        <?php if ($b['raihanf_img']) { ?>
                            <img src="../assets/img_buku/<?= htmlspecialchars($b['raihanf_img']); ?>">
                        <?php } ?>
                    </div>

                    <h3><?= htmlspecialchars($b['raihanf_judul']); ?></h3>
                    <p><b>Kategori:</b> <?= htmlspecialchars($b['raihanf_kategori']); ?></p>
                    <p><b>Pengarang:</b> <?= htmlspecialchars($b['raihanf_pengarang']); ?></p>
                    <p><b>Penerbit:</b> <?= htmlspecialchars($b['raihanf_penerbit']); ?></p>
                    <p><b>Tahun:</b> <?= htmlspecialchars($b['raihanf_tahun_terbit']); ?></p>
                    <p><b>Stok Total:</b> <?= $b['raihanf_stok_total']; ?></p>
                    <p><b>Tersedia:</b>
                        <?php if ($stok_tersedia > 0): ?>
                            <span style="color:#27ae60;font-weight:bold"><?= $stok_tersedia; ?></span>
                        <?php else: ?>
                            <span class="stok-habis">Habis</span>
                        <?php endif; ?>
                    </p>

                    <?php if ($stok_tersedia > 0) { ?>
                        <div class="qty-box">
                            <button class="qty-btn" onclick="updateQty('<?= $id ?>','minus')">−</button>
                            <input type="number"
                                id="qty_<?= $id ?>"
                                class="qty-input"
                                value="<?= $qty ?>"
                                min="0" max="1"
                                onchange="manualInput('<?= $id ?>')">
                            <button class="qty-btn" onclick="updateQty('<?= $id ?>','plus')">+</button>
                        </div>
                    <?php } else { ?>
                        <p class="stok-habis">⚠️ Stok Habis</p>
                        <input type="number" id="qty_<?= $id ?>" value="0" style="display:none">
                    <?php } ?>

                </div>

            <?php } ?>

        </div>

    </div>

</body>

</html>