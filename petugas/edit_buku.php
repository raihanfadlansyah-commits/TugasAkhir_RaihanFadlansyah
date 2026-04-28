<?php
require_once "../koneksi_raihanf.php";
session_start();

// [FIX] Validasi login: cek role='petugas', bukan login='admin'
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'petugas') {
    header("Location: ../raihanf_index.php");
    exit;
}

$id_buku = mysqli_real_escape_string($koneksi, $_GET['raihanf_id_buku']);
$data = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM raihanf_buku WHERE raihanf_id_buku='$id_buku'"));

if (!$data) {
    echo "<script>alert('❌ Buku tidak ditemukan!'); window.location='data_buku.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Edit Buku</title>

    <style>
        body {
            margin: 0;
            font-family: Arial;
            background: #f4f6f9;
        }

        .container {
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding: 40px 0;
            min-height: 100vh;
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1);
            width: 420px;
        }

        h2 {
            text-align: center;
        }

        .form-group {
            margin: 10px 0;
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            font-weight: bold;
            margin-bottom: 5px;
            font-size: 14px;
        }

        input,
        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        input[readonly] {
            background: #ecf0f1;
        }

        .file-wrapper {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .file-wrapper input[type="file"] {
            display: none;
        }

        .custom-file {
            background: #2c3e50;
            color: white;
            padding: 8px 12px;
            border-radius: 5px;
            cursor: pointer;
        }

        .file-name {
            color: #aaa;
            font-size: 13px;
        }

        .warning {
            font-size: 12px;
            color: #e74c3c;
            margin-top: 5px;
        }

        .preview-img {
            border-radius: 5px;
            margin-bottom: 10px;
            max-width: 100px;
        }

        button {
            width: 100%;
            background: #2c3e50;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
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
    </style>

</head>

<body>

    <div class="container">
        <div class="card">

            <a href="data_buku.php" class="back">⬅ Kembali</a>

            <h2>Edit Buku</h2>

            <form action="update_buku.php" method="post" enctype="multipart/form-data">

                <div class="form-group">
                    <label>ID Buku</label>
                    <input type="text" name="raihanf_id_buku" value="<?= htmlspecialchars($data['raihanf_id_buku']); ?>" readonly>
                </div>

                <div class="form-group">
                    <label>Judul Buku</label>
                    <input type="text" name="raihanf_judul" value="<?= htmlspecialchars($data['raihanf_judul']); ?>" required>
                </div>

                <div class="form-group">
                    <label>Pengarang</label>
                    <input type="text" name="raihanf_pengarang" value="<?= htmlspecialchars($data['raihanf_pengarang']); ?>" required>
                </div>

                <div class="form-group">
                    <label>Penerbit</label>
                    <input type="text" name="raihanf_penerbit" value="<?= htmlspecialchars($data['raihanf_penerbit']); ?>" required>
                </div>

                <div class="form-group">
                    <label>Tahun Terbit</label>
                    <input type="number" name="raihanf_tahun_terbit" value="<?= htmlspecialchars($data['raihanf_tahun_terbit']); ?>" min="1900" max="2099" required>
                </div>

                <!-- [FIX] Tambah field Kategori yang sebelumnya hilang -->
                <div class="form-group">
                    <label>Kategori</label>
                    <select name="raihanf_kategori" required>
                        <option value="">-- Pilih Kategori --</option>
                        <?php
                        $kategori_list = ['Fiksi','Nonfiksi','Buku Pelajaran','Referensi','Agama','Sains','Sosial','Bahasa','Seni & Olahraga','Teknologi','Sejarah','Geografi','Biografi','Majalah / Koran'];
                        foreach ($kategori_list as $kat) {
                            $selected = ($data['raihanf_kategori'] == $kat) ? 'selected' : '';
                            echo "<option value=\"$kat\" $selected>$kat</option>";
                        }
                        ?>
                    </select>
                </div>

                <!-- [FIX] Tambah field Stok yang sebelumnya hilang -->
                <div class="form-group">
                    <label>Stok Buku</label>
                    <input type="number" name="raihanf_stok_total" value="<?= htmlspecialchars($data['raihanf_stok_total']); ?>" min="0" required>
                </div>

                <!-- FOTO -->
                <div class="form-group">
                    <label>Foto Saat Ini</label>
                    <img src="../assets/img_buku/<?= htmlspecialchars($data['raihanf_img']); ?>" class="preview-img">
                </div>

                <div class="form-group">
                    <label>Ganti Cover Buku</label>

                    <div class="file-wrapper">
                        <label class="custom-file">
                            Pilih File
                            <input type="file" name="raihanf_img" id="fileInput" accept=".jpg,.jpeg,.png">
                        </label>

                        <span class="file-name" id="fileName">Tidak ada file baru</span>
                    </div>

                    <div class="warning">
                        ⚠️ Pastikan foto sesuai dengan judul buku (cover asli)
                    </div>

                    <input type="hidden" name="foto_lama" value="<?= htmlspecialchars($data['raihanf_img']); ?>">
                </div>

                <button type="submit">Update Data</button>

            </form>

        </div>
    </div>

    <script>
        document.getElementById("fileInput").onchange = function() {
            document.getElementById("fileName").textContent = this.files[0].name;
        };
    </script>

</body>

</html>
