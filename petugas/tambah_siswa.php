<?php
session_start();
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'petugas') {
    header("Location: ../raihanf_index.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Tambah Siswa</title>

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
    min-height: 100vh;
    padding: 30px 0;
}

.card {
    background: white;
    padding: 25px;
    border-radius: 10px;
    box-shadow: 0 3px 8px rgba(0,0,0,0.1);
    width: 400px;
}

h2 { text-align: center; }

h4 {
    margin: 15px 0 5px;
    color: #2c3e50;
}

.form-group {
    margin-bottom: 10px;
}

.form-group label {
    display: block;
    font-size: 13px;
    font-weight: bold;
    margin-bottom: 4px;
    color: #555;
}

input, select {
    width: 100%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
    box-sizing: border-box;
    font-size: 14px;
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
    font-size: 14px;
}

button:hover { background: #34495e; }

.back {
    display: inline-block;
    margin-bottom: 15px;
    text-decoration: none;
    color: white;
    background: #7f8c8d;
    padding: 8px 12px;
    border-radius: 5px;
}

.back:hover { background: #636e72; }
</style>

</head>

<body>

<div class="container">
    <div class="card">

        <a href="data_siswa.php" class="back">⬅ Kembali</a>

        <h2>Tambah Siswa + RFID</h2>

        <form action="proses_tambah_siswa.php" method="post">

            <h4>Data RFID</h4>
            <div class="form-group">
                <label>RFID</label>
                <input type="text" name="rfid" placeholder="Scan / Input RFID" required>
            </div>

            <h4>Data Siswa</h4>
            <div class="form-group">
                <label>NIS</label>
                <input type="text" name="raihanf_nis_siswa" placeholder="Nomor Induk Siswa" required>
            </div>

            <div class="form-group">
                <label>Nama</label>
                <input type="text" name="raihanf_nama_siswa" placeholder="Nama Lengkap" required>
            </div>

            <div class="form-group">
                <label>Kelas</label>
                <select name="raihanf_kelas" required>
                    <option value="">-- Pilih Kelas --</option>
                    <option value="X">X</option>
                    <option value="XI">XI</option>
                    <option value="XII">XII</option>
                </select>
            </div>

            <div class="form-group">
                <label>Jurusan</label>
                <select name="raihanf_jurusan" required>
                    <option value="">-- Pilih Jurusan --</option>
                    <option value="Mekatronika">Mekatronika</option>
                    <option value="Kimia Industri">Kimia Industri</option>
                    <option value="Perkembangan Perangkat Lunak">Perkembangan Perangkat Lunak</option>
                    <option value="Animasi">Animasi</option>
                    <option value="Desain Komunikasi Visual">Desain Komunikasi Visual</option>
                    <option value="Teknik Permesinan">Teknik Permesinan</option>
                </select>
            </div>

            <div class="form-group">
                <label>No Telepon</label>
                <input type="text" name="raihanf_no_telpon" placeholder="No Telepon" required>
            </div>

            <button type="submit" name="simpan">Simpan</button>

        </form>
    </div>
</div>

</body>
</html>