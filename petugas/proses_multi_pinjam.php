<?php
session_start();
require_once "../koneksi_raihanf.php";

if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'petugas') {
    header("Location: ../raihanf_index.php");
    exit;
}

$rfid    = mysqli_real_escape_string($koneksi, $_POST['rfid'] ?? '');
$tenggat = mysqli_real_escape_string($koneksi, $_POST['tenggat'] ?? '');

if (empty($rfid) || empty($tenggat)) {
    echo "<script>alert('❌ Data tidak lengkap!'); window.history.back();</script>";
    exit;
}

$keranjang = $_SESSION['keranjang'] ?? [];

if (empty($keranjang)) {
    header("Location: pinjam_buku.php");
    exit;
}

// Cari user berdasarkan RFID
$user = mysqli_fetch_assoc(mysqli_query(
    $koneksi,
    "SELECT * FROM raihanf_user WHERE raihanf_rfid='$rfid' AND raihanf_role='siswa'"
));

if (!$user) {
    echo "<script>alert('❌ RFID tidak valid atau bukan siswa!'); window.history.back();</script>";
    exit;
}

$id_user = $user['raihanf_id_user'];

// Cek apakah siswa masih punya pinjaman aktif (Dipinjam)
$cek = mysqli_query($koneksi, "
    SELECT * FROM raihanf_peminjaman 
    WHERE raihanf_id_user='$id_user' AND raihanf_status='Dipinjam'
");

if (mysqli_num_rows($cek) > 0) {
    echo "<script>alert('❌ Siswa ini masih memiliki pinjaman yang belum dikembalikan!'); window.history.back();</script>";
    exit;
}

// Format tenggat datetime-local → MySQL DATETIME
$tenggat_dt = date('Y-m-d H:i:s', strtotime($tenggat));

// ===== GENERATE ID PEMINJAMAN =====
function generateIdPeminjaman($koneksi)
{
    $hasil = mysqli_fetch_assoc(mysqli_query(
        $koneksi,
        "SELECT raihanf_id_peminjaman FROM raihanf_peminjaman 
         WHERE raihanf_id_peminjaman LIKE 'PJM%' 
         ORDER BY raihanf_id_peminjaman DESC LIMIT 1"
    ));
    $nomor = $hasil ? ((int) substr($hasil['raihanf_id_peminjaman'], 3)) + 1 : 1;

    do {
        $id_baru = 'PJM' . str_pad($nomor, 3, '0', STR_PAD_LEFT);
        $cek_id  = mysqli_fetch_assoc(mysqli_query(
            $koneksi,
            "SELECT raihanf_id_peminjaman FROM raihanf_peminjaman 
             WHERE raihanf_id_peminjaman='$id_baru'"
        ));
        if ($cek_id) $nomor++;
    } while ($cek_id);

    return $id_baru;
}

// ===== GENERATE ID DETAIL =====
function generateIdDetail($koneksi)
{
    $hasil = mysqli_fetch_assoc(mysqli_query(
        $koneksi,
        "SELECT raihanf_id_detail FROM raihanf_detail_peminjaman 
         WHERE raihanf_id_detail LIKE 'DTL%' 
         ORDER BY raihanf_id_detail DESC LIMIT 1"
    ));
    $nomor = $hasil ? ((int) substr($hasil['raihanf_id_detail'], 3)) + 1 : 1;

    do {
        $id_baru = 'DTL' . str_pad($nomor, 3, '0', STR_PAD_LEFT);
        $cek_id  = mysqli_fetch_assoc(mysqli_query(
            $koneksi,
            "SELECT raihanf_id_detail FROM raihanf_detail_peminjaman 
             WHERE raihanf_id_detail='$id_baru'"
        ));
        if ($cek_id) $nomor++;
    } while ($cek_id);

    return $id_baru;
}

// ===== MULAI TRANSAKSI =====
mysqli_begin_transaction($koneksi);

$id_pinjam = generateIdPeminjaman($koneksi);

// 1) INSERT ke raihanf_peminjaman (1 record per transaksi pinjam)
$ins_pinjam = mysqli_query($koneksi, "
    INSERT INTO raihanf_peminjaman 
        (raihanf_id_peminjaman, raihanf_id_user, 
         raihanf_tgl_pinjam, raihanf_tenggat_waktu, 
         raihanf_tgl_kembali, raihanf_status)
    VALUES (
        '$id_pinjam',
        '$id_user',
        CURDATE(),
        '$tenggat_dt',
        NULL,
        'Dipinjam'
    )
");

if (!$ins_pinjam) {
    mysqli_rollback($koneksi);
    echo "<script>alert('❌ Gagal membuat data peminjaman: " . mysqli_error($koneksi) . "'); window.history.back();</script>";
    exit;
}

// 2) INSERT tiap buku ke raihanf_detail_peminjaman
$sukses = 0;
$gagal  = 0;

foreach ($keranjang as $id_buku => $qty) {
    if ($qty <= 0) continue;

    $id_buku_esc = mysqli_real_escape_string($koneksi, $id_buku);

    // Cek stok tersedia
    $stok_row = mysqli_fetch_assoc(mysqli_query($koneksi, "
        SELECT 
            b.raihanf_stok_total,
            COALESCE(SUM(CASE WHEN p.raihanf_status = 'Dipinjam' THEN d.raihanf_total_buku ELSE 0 END), 0) AS total_dipinjam
        FROM raihanf_buku b
        LEFT JOIN raihanf_detail_peminjaman d ON b.raihanf_id_buku = d.raihanf_id_buku
        LEFT JOIN raihanf_peminjaman p ON d.raihaf_id_peminjaman = p.raihanf_id_peminjaman
        WHERE b.raihanf_id_buku = '$id_buku_esc'
        GROUP BY b.raihanf_id_buku
    "));

    if (!$stok_row) {
        $gagal++;
        continue;
    }

    $tersedia = $stok_row['raihanf_stok_total'] - $stok_row['total_dipinjam'];
    if ($tersedia < $qty) {
        $gagal++;
        continue;
    }

    $id_detail = generateIdDetail($koneksi);

    $ins_detail = mysqli_query($koneksi, "
        INSERT INTO raihanf_detail_peminjaman 
            (raihanf_id_detail, raihaf_id_peminjaman, raihanf_id_buku, raihanf_total_buku)
        VALUES (
            '$id_detail',
            '$id_pinjam',
            '$id_buku_esc',
            $qty
        )
    ");

    if ($ins_detail) {
        $sukses++;
    } else {
        $gagal++;
    }
}

// Jika tidak ada buku yang berhasil dimasukkan, rollback semua
if ($sukses === 0) {
    mysqli_rollback($koneksi);
    echo "<script>alert('❌ Tidak ada buku yang berhasil diproses. Stok mungkin habis.'); window.history.back();</script>";
    exit;
}

mysqli_commit($koneksi);
unset($_SESSION['keranjang']);

if ($gagal === 0) {
    echo "<script>
        alert('✅ Berhasil meminjamkan $sukses buku untuk " . htmlspecialchars($user['raihanf_nama']) . "!');
        window.location='data_peminjaman.php';
    </script>";
} else {
    echo "<script>
        alert('⚠️ $sukses buku berhasil, $gagal buku gagal (stok habis).');
        window.location='data_peminjaman.php';
    </script>";
}
