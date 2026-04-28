<?php
require_once "../koneksi_raihanf.php";
session_start();

if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'petugas') {
    header("Location: ../raihanf_index.php");
    exit;
}

// ===== HANDLE AKSI POST =====
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $aksi      = $_POST['aksi'] ?? '';
    $id_pinjam = mysqli_real_escape_string($koneksi, $_POST['id_peminjaman'] ?? '');
    $rfid_scan = mysqli_real_escape_string($koneksi, $_POST['rfid_scan'] ?? '');

    // Verifikasi RFID cocok dengan siswa yang punya peminjaman ini
    $cekRfid = mysqli_fetch_assoc(mysqli_query($koneksi, "
        SELECT u.raihanf_rfid 
        FROM raihanf_peminjaman p
        JOIN raihanf_user u ON p.raihanf_id_user = u.raihanf_id_user
        WHERE p.raihanf_id_peminjaman = '$id_pinjam'
    "));

    if (!$cekRfid || $cekRfid['raihanf_rfid'] !== $rfid_scan) {
        $_SESSION['rfid_error'] = $id_pinjam . '_' . $aksi;
        header("Location: data_peminjaman.php");
        exit;
    }

    if ($aksi === 'kembalikan' && $id_pinjam) {
        $row = mysqli_fetch_assoc(mysqli_query(
            $koneksi,
            "SELECT raihanf_tenggat_waktu FROM raihanf_peminjaman 
             WHERE raihanf_id_peminjaman='$id_pinjam'"
        ));

        if ($row) {
            $now     = new DateTime();
            $tenggat = new DateTime($row['raihanf_tenggat_waktu']);
            if ($now < $tenggat) {
                $status_baru = 'Lebih Cepat';
            } elseif ($now->format('Y-m-d') === $tenggat->format('Y-m-d')) {
                $status_baru = 'Tepat Waktu';
            } else {
                $status_baru = 'Terlambat';
            }

            mysqli_query($koneksi, "
                UPDATE raihanf_peminjaman 
                SET raihanf_status='$status_baru', raihanf_tgl_kembali=CURDATE()
                WHERE raihanf_id_peminjaman='$id_pinjam'
            ");
        }
        header("Location: data_peminjaman.php");
        exit;
    }

    if ($aksi === 'perpanjang' && $id_pinjam) {
        $tambah_hari = (int)($_POST['tambah_hari'] ?? 7);
        mysqli_query($koneksi, "
            UPDATE raihanf_peminjaman 
            SET raihanf_tenggat_waktu = DATE_ADD(raihanf_tenggat_waktu, INTERVAL $tambah_hari DAY)
            WHERE raihanf_id_peminjaman='$id_pinjam' AND raihanf_status='Dipinjam'
        ");
        header("Location: data_peminjaman.php");
        exit;
    }
}

// Ambil error RFID jika ada (buka modal yang gagal otomatis)
$rfid_error = $_SESSION['rfid_error'] ?? '';
unset($_SESSION['rfid_error']);
?>

<!DOCTYPE html>
<html>

<head>
    <title>Data Peminjaman</title>

    <style>
        body {
            margin: 0;
            font-family: Arial;
            background: #f4f6f9;
        }

        h2 { text-align: center; }

        * {
            user-select: none;
            -webkit-user-select: none;
            caret-color: transparent;
        }

        input, select, textarea {
            user-select: text;
            -webkit-user-select: text;
            caret-color: auto;
        }

        img { pointer-events: none; }

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

        .sidebar a:hover { background: #34495e; }

        .main {
            margin-left: 220px;
            padding: 20px;
        }

        .header {
            background: white;
            padding: 15px;
            border-radius: 8px;
        }

        .search-box { margin-top: 15px; }

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
            border-radius: 4px;
        }

        .table-container {
            margin-top: 20px;
            background: white;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1);
            overflow-x: auto;
        }

        table { width: 100%; border-collapse: collapse; }

        table th, table td {
            padding: 10px;
            text-align: center;
            white-space: nowrap;
        }

        table th { background: #2c3e50; color: white; }
        table tr:nth-child(even) { background: #f2f2f2; }

        .badge {
            padding: 4px 9px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
            color: white;
            white-space: nowrap;
            display: inline-block;
        }

        .badge.dipinjam     { background: #3498db; }
        .badge.terlambat    { background: #e74c3c; }
        .badge.dikembalikan { background: #27ae60; }
        .badge.tepat        { background: #2ecc71; }
        .badge.cepat        { background: #f39c12; }

        .btn {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 12px;
            border: none;
            cursor: pointer;
            color: white;
            margin: 2px;
            text-decoration: none;
        }

        .btn-detail     { background: #2980b9; }
        .btn-kembali    { background: #27ae60; }
        .btn-perpanjang { background: #e67e22; }
        .btn:hover      { opacity: .85; }

        /* ===== MODAL ===== */
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, .5);
            z-index: 999;
            align-items: center;
            justify-content: center;
        }

        .modal-overlay.active { display: flex; }

        .modal-box {
            background: white;
            border-radius: 10px;
            padding: 25px;
            width: 480px;
            max-width: 95vw;
            max-height: 85vh;
            overflow-y: auto;
            box-shadow: 0 5px 20px rgba(0, 0, 0, .3);
        }

        .modal-box h3 { margin-top: 0; }

        .modal-close {
            float: right;
            cursor: pointer;
            font-size: 18px;
            color: #aaa;
            background: none;
            border: none;
        }

        .modal-close:hover { color: #333; }

        .detail-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .detail-table th, .detail-table td {
            padding: 8px;
            border: 1px solid #ddd;
            text-align: left;
            font-size: 14px;
        }

        .detail-table th { background: #2c3e50; color: white; }

        .form-perpanjang {
            margin-top: 15px;
            display: flex;
            gap: 8px;
            align-items: center;
            flex-wrap: wrap;
        }

        .form-perpanjang select {
            padding: 7px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        /* ===== RFID SCAN AREA ===== */
        .rfid-scan-area {
            margin-top: 18px;
            text-align: center;
        }

        .rfid-scan-area p {
            font-size: 13px;
            color: #555;
            margin-bottom: 8px;
        }

        .rfid-scan-area input[type="text"] {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            text-align: center;
            border: 2px dashed #2c3e50;
            border-radius: 8px;
            box-sizing: border-box;
            background: #f9f9f9;
        }

        .rfid-scan-area input[type="text"]:focus {
            outline: none;
            border-color: #27ae60;
            background: #fff;
        }

        .rfid-error {
            color: #e74c3c;
            font-size: 13px;
            margin-top: 8px;
            font-weight: bold;
        }

        .aksi-group {
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
            justify-content: center;
        }

        img.logo {
            width: 120px;
            margin-bottom: 10px;
            padding-left: 45px;
        }
    </style>
</head>

<body>

    <div class="sidebar">
        <img src="../assets/img_buku/Group 1.png" class="logo" alt="">
        <a href="dashboard_petugas.php">🏠 Dashboard</a>
        <a href="data_buku.php">📚 Data Buku</a>
        <a href="data_peminjaman.php">📄 Data Peminjaman</a>
        <a href="data_siswa.php">👨‍🎓 Data Siswa</a>
        <a href="data_pengunjung.php">👁️ Data Pengunjung</a>
        <a href="../raihanf_logout.php">🚪 Logout</a>
    </div>

    <div class="main">

        <div class="header">
            <h2>📄 Data Peminjaman</h2>
        </div>

        <div class="search-box">
            <form method="get">
                <input type="text" name="search" placeholder="Cari nama, ID peminjaman, status..."
                    value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                <input type="submit" value="Cari">
            </form>
        </div>

        <div class="table-container">
            <table>
                <tr>
                    <th>No</th>
                    <th>ID Peminjaman</th>
                    <th>Nama Siswa</th>
                    <th>Jumlah Buku</th>
                    <th>Tgl Pinjam</th>
                    <th>Tenggat</th>
                    <th>Tgl Kembali</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>

                <?php
                $no     = 1;
                $search = isset($_GET['search']) ? mysqli_real_escape_string($koneksi, $_GET['search']) : '';

                $where = '';
                if (!empty($search)) {
                    $where = "WHERE 
                        p.raihanf_id_peminjaman LIKE '%$search%' OR 
                        u.raihanf_nama           LIKE '%$search%' OR 
                        p.raihanf_status         LIKE '%$search%'";
                }

                $query = mysqli_query($koneksi, "
                    SELECT
                        p.raihanf_id_peminjaman,
                        u.raihanf_nama,
                        p.raihanf_tgl_pinjam,
                        p.raihanf_tenggat_waktu,
                        p.raihanf_tgl_kembali,
                        p.raihanf_status,
                        COUNT(d.raihanf_id_detail) AS jumlah_judul,
                        SUM(d.raihanf_total_buku)  AS jumlah_buku
                    FROM raihanf_peminjaman p
                    JOIN raihanf_user u ON p.raihanf_id_user = u.raihanf_id_user
                    LEFT JOIN raihanf_detail_peminjaman d ON p.raihanf_id_peminjaman = d.raihaf_id_peminjaman
                    $where
                    GROUP BY p.raihanf_id_peminjaman
                    ORDER BY p.raihanf_tgl_pinjam DESC
                ");

                if (!$query) {
                    echo "<tr><td colspan='9' style='color:red'>Query Error: " . mysqli_error($koneksi) . "</td></tr>";
                } else {
                    while ($data = mysqli_fetch_assoc($query)) {
                        $status       = $data['raihanf_status'];
                        $id_pinjam    = $data['raihanf_id_peminjaman'];
                        $badge_class  = match ($status) {
                            'Dipinjam'     => 'dipinjam',
                            'Terlambat'    => 'terlambat',
                            'Dikembalikan' => 'dikembalikan',
                            'Tepat Waktu'  => 'tepat',
                            'Lebih Cepat'  => 'cepat',
                            default        => 'dipinjam',
                        };
                        $masih_pinjam    = ($status === 'Dipinjam');
                        $err_kembali    = ($rfid_error === $id_pinjam . '_kembalikan');
                        $err_perpanjang = ($rfid_error === $id_pinjam . '_perpanjang');
                ?>

                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= htmlspecialchars($id_pinjam); ?></td>
                            <td><?= htmlspecialchars($data['raihanf_nama']); ?></td>
                            <td><?= (int)$data['jumlah_buku']; ?> buku (<?= (int)$data['jumlah_judul']; ?> judul)</td>
                            <td><?= htmlspecialchars($data['raihanf_tgl_pinjam']); ?></td>
                            <td><?= htmlspecialchars($data['raihanf_tenggat_waktu']); ?></td>
                            <td><?= $data['raihanf_tgl_kembali'] ? htmlspecialchars($data['raihanf_tgl_kembali']) : '-'; ?></td>
                            <td><span class="badge <?= $badge_class ?>"><?= htmlspecialchars($status); ?></span></td>
                            <td>
                                <div class="aksi-group">
                                    <button class="btn btn-detail"
                                        onclick="lihatDetail('<?= $id_pinjam ?>')">
                                        📚 Detail
                                    </button>

                                    <?php if ($masih_pinjam): ?>
                                        <button class="btn btn-perpanjang"
                                            onclick="bukaModal('modal-perpanjang-<?= $id_pinjam ?>')">
                                            🕐 Perpanjang
                                        </button>

                                        <button class="btn btn-kembali"
                                            onclick="bukaModal('modal-kembali-<?= $id_pinjam ?>')">
                                            ✅ Kembalikan
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>

                        <?php if ($masih_pinjam): ?>

                            <!-- Modal Perpanjang -->
                            <div id="modal-perpanjang-<?= $id_pinjam ?>" class="modal-overlay <?= $err_perpanjang ? 'active' : '' ?>">
                                <div class="modal-box">
                                    <button class="modal-close" onclick="tutupModal('modal-perpanjang-<?= $id_pinjam ?>')">✕</button>
                                    <h3>🕐 Perpanjang Peminjaman</h3>
                                    <p><b>ID:</b> <?= htmlspecialchars($id_pinjam) ?></p>
                                    <p><b>Siswa:</b> <?= htmlspecialchars($data['raihanf_nama']) ?></p>
                                    <p><b>Tenggat sekarang:</b> <?= htmlspecialchars($data['raihanf_tenggat_waktu']) ?></p>

                                    <form method="POST" action="data_peminjaman.php">
                                        <input type="hidden" name="aksi" value="perpanjang">
                                        <input type="hidden" name="id_peminjaman" value="<?= $id_pinjam ?>">

                                        <div class="form-perpanjang">
                                            <label>Tambah:</label>
                                            <select name="tambah_hari">
                                                <option value="3">3 Hari</option>
                                                <option value="7" selected>7 Hari</option>
                                                <option value="14">14 Hari</option>
                                                <option value="30">30 Hari</option>
                                            </select>
                                        </div>

                                        <div class="rfid-scan-area">
                                            <p>🪪 Tempel kartu RFID siswa untuk konfirmasi</p>
                                            <input
                                                type="text"
                                                name="rfid_scan"
                                                placeholder="Scan RFID..."
                                                autocomplete="off"
                                                onchange="this.form.submit()">
                                            <?php if ($err_perpanjang): ?>
                                                <div class="rfid-error">❌ RFID tidak cocok, coba lagi.</div>
                                            <?php endif; ?>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <!-- Modal Kembalikan -->
                            <div id="modal-kembali-<?= $id_pinjam ?>" class="modal-overlay <?= $err_kembali ? 'active' : '' ?>">
                                <div class="modal-box">
                                    <button class="modal-close" onclick="tutupModal('modal-kembali-<?= $id_pinjam ?>')">✕</button>
                                    <h3>✅ Konfirmasi Pengembalian</h3>
                                    <p><b>ID:</b> <?= htmlspecialchars($id_pinjam) ?></p>
                                    <p><b>Siswa:</b> <?= htmlspecialchars($data['raihanf_nama']) ?></p>
                                    <p><b>Tenggat:</b> <?= htmlspecialchars($data['raihanf_tenggat_waktu']) ?></p>

                                    <form method="POST" action="data_peminjaman.php">
                                        <input type="hidden" name="aksi" value="kembalikan">
                                        <input type="hidden" name="id_peminjaman" value="<?= $id_pinjam ?>">

                                        <div class="rfid-scan-area">
                                            <p>🪪 Tempel kartu RFID siswa untuk konfirmasi</p>
                                            <input
                                                type="text"
                                                name="rfid_scan"
                                                placeholder="Scan RFID..."
                                                autocomplete="off"
                                                onchange="this.form.submit()">
                                            <?php if ($err_kembali): ?>
                                                <div class="rfid-error">❌ RFID tidak cocok, coba lagi.</div>
                                            <?php endif; ?>
                                        </div>
                                    </form>
                                </div>
                            </div>

                        <?php endif; ?>

                        <!-- Modal Detail Buku -->
                        <div id="modal-detail-<?= $id_pinjam ?>" class="modal-overlay">
                            <div class="modal-box">
                                <button class="modal-close" onclick="tutupModal('modal-detail-<?= $id_pinjam ?>')">✕</button>
                                <h3>📚 Detail Buku Dipinjam</h3>
                                <p><b>ID Peminjaman:</b> <?= htmlspecialchars($id_pinjam) ?></p>
                                <p><b>Siswa:</b> <?= htmlspecialchars($data['raihanf_nama']) ?></p>
                                <div id="detail-content-<?= $id_pinjam ?>">
                                    <p style="color:#aaa">Memuat data...</p>
                                </div>
                            </div>
                        </div>

                <?php   }
                } ?>
            </table>
        </div>

    </div>

    <script>
        function bukaModal(id) {
            var modal = document.getElementById(id);
            modal.classList.add('active');
            // Auto fokus ke input RFID supaya langsung bisa scan
            var rfidInput = modal.querySelector('input[name="rfid_scan"]');
            if (rfidInput) setTimeout(function() { rfidInput.focus(); }, 100);
        }

        function tutupModal(id) {
            document.getElementById(id).classList.remove('active');
        }

        document.querySelectorAll('.modal-overlay').forEach(function(el) {
            el.addEventListener('click', function(e) {
                if (e.target === el) el.classList.remove('active');
            });
        });

        // Auto fokus modal yang error (dari session)
        <?php if ($rfid_error): ?>
        window.addEventListener('load', function() {
            var modalId = 'modal-<?= str_replace('_', '-', $rfid_error) ?>';
            var modal = document.getElementById(modalId);
            if (modal) {
                var rfidInput = modal.querySelector('input[name="rfid_scan"]');
                if (rfidInput) setTimeout(function() { rfidInput.focus(); }, 100);
            }
        });
        <?php endif; ?>

        function lihatDetail(idPinjam) {
            bukaModal('modal-detail-' + idPinjam);

            var container = document.getElementById('detail-content-' + idPinjam);
            if (container.dataset.loaded) return;

            container.innerHTML = '<p style="color:#aaa">Memuat data...</p>';

            fetch('get_detail_peminjaman.php?id=' + encodeURIComponent(idPinjam))
                .then(r => r.json())
                .then(function(data) {
                    if (data.length === 0) {
                        container.innerHTML = '<p style="color:#e74c3c">Tidak ada data buku.</p>';
                        return;
                    }
                    var html = '<table class="detail-table"><tr><th>No</th><th>Judul Buku</th><th>Pengarang</th><th>Jumlah</th></tr>';
                    data.forEach(function(b, i) {
                        html += '<tr><td>' + (i + 1) + '</td><td>' + b.judul + '</td><td>' + b.pengarang + '</td><td>' + b.jumlah + '</td></tr>';
                    });
                    html += '</table>';
                    container.innerHTML = html;
                    container.dataset.loaded = '1';
                })
                .catch(function() {
                    container.innerHTML = '<p style="color:red">Gagal memuat data.</p>';
                });
        }
    </script>

</body>

</html>