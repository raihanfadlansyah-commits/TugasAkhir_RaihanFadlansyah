<?php
session_start();
date_default_timezone_set('Asia/Jakarta');
require_once "koneksi_raihanf.php";

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

// Validasi input
if (empty($username) || empty($password)) {
    $_SESSION['pesan'] = "⚠️ Username dan password tidak boleh kosong!";
    header("Location: raihanf_index.php");
    exit;
}

// Amankan input
$username = mysqli_real_escape_string($koneksi, $username);

// Ambil data user
$query = mysqli_query($koneksi, "SELECT * FROM raihanf_user 
    WHERE raihanf_username='$username'");

$user = mysqli_fetch_assoc($query);

if ($user) {

    $id_user     = $user['raihanf_id_user'];
    $role        = $user['raihanf_role'];
    $password_db = $user['raihanf_password'];

    // Cek password
    if ($password_db === md5($password)) {

        // Validasi prefix ID sesuai role
        $valid = false;

        if ($role === 'admin' && substr($id_user, 0, 3) === 'ADM') {
            $valid = true;
        } elseif ($role === 'petugas' && substr($id_user, 0, 3) === 'PTG') {
            $valid = true;
        }

        if (!$valid) {
            $_SESSION['pesan'] = "❌ ID tidak sesuai dengan role!";
            header("Location: raihanf_index.php");
            exit;
        }

        // Set session
        $_SESSION['id_user']  = $id_user;
        $_SESSION['username'] = $user['raihanf_username'];
        $_SESSION['nama']     = $user['raihanf_nama'];
        $_SESSION['role']     = $role;
        $_SESSION['login']    = true;

        // Redirect sesuai role
        if ($role === 'admin') {
            header("Location: admin/dashboard_admin.php");
            exit;
        } elseif ($role === 'petugas') {
            header("Location: petugas/dashboard_petugas.php");
            exit;
        }

    } else {
        $_SESSION['pesan'] = "❌ Password salah!";
        header("Location: raihanf_index.php");
        exit;
    }

} else {
    $_SESSION['pesan'] = "❌ Username tidak ditemukan!";
    header("Location: raihanf_index.php");
    exit;
}
?>