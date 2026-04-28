<?php
require_once "../koneksi_raihanf.php";
session_start();

// VALIDASI LOGIN PETUGAS
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'petugas') {
    header("Location: ../raihanf_index.php");
    exit;
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Tambah Buku</title>

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

            <h2>Tambah Buku</h2>

            <form method="POST" action="proses_tambah_buku.php" enctype="multipart/form-data">

                <div class="form-group">
                    <label>Judul Buku</label>
                    <input type="text" name="raihanf_judul" placeholder="Masukkan judul buku" required>
                </div>

                <div class="form-group">
                    <label>Pengarang</label>
                    <input type="text" name="raihanf_pengarang" placeholder="Masukkan nama pengarang" required>
                </div>

                <div class="form-group">
                    <label>Penerbit</label>
                    <input type="text" name="raihanf_penerbit" placeholder="Masukkan penerbit" required>
                </div>

                <div class="form-group">
                    <label>Tahun Terbit</label>
                    <input type="number" name="raihanf_tahun_terbit" min="1900" max="2099" placeholder="Contoh: 2024" required>
                </div>

                <div class="form-group">
                    <label>Kategori</label>
                    <select name="raihanf_kategori" required>
                        <option value="">-- Pilih Kategori --</option>
                        <option value="Fiksi">Fiksi</option>
                        <option value="Nonfiksi">Nonfiksi</option>
                        <option value="Buku Pelajaran">Buku Pelajaran</option>
                        <option value="Referensi">Referensi</option>
                        <option value="Agama">Agama</option>
                        <option value="Sains">Sains</option>
                        <option value="Sosial">Sosial</option>
                        <option value="Bahasa">Bahasa</option>
                        <option value="Seni & Olahraga">Seni & Olahraga</option>
                        <option value="Teknologi">Teknologi</option>
                        <option value="Sejarah">Sejarah</option>
                        <option value="Geografi">Geografi</option>
                        <option value="Biografi">Biografi</option>
                        <option value="Majalah / Koran">Majalah / Koran</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Stok Buku</label>
                    <input type="number" name="raihanf_stok_total" min="0" placeholder="Masukkan jumlah stok" required>
                </div>

                <!-- FOTO -->
                <div class="form-group">
                    <label>Cover Buku</label>

                    <div class="file-wrapper">
                        <label class="custom-file">
                            Pilih File
                            <input type="file" name="raihanf_img" id="fileInput" required>
                        </label>

                        <span class="file-name" id="fileName">Belum ada file</span>
                    </div>

                    <div class="warning">
                        ⚠️ Pastikan foto sesuai dengan judul buku (cover asli)
                    </div>
                </div>

                <button type="submit" name="simpan">Simpan</button>

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