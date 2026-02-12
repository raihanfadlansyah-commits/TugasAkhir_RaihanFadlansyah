-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 12, 2026 at 08:19 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_perpus_raihanf`
--

-- --------------------------------------------------------

--
-- Table structure for table `raihanf_admin`
--

CREATE TABLE `raihanf_admin` (
  `raihanf_id_admin` varchar(10) NOT NULL,
  `raihanf_nip` varchar(20) NOT NULL,
  `raihanf_nama` varchar(100) NOT NULL,
  `raihanf_username` varchar(50) NOT NULL,
  `raihanf_password` varchar(100) NOT NULL,
  `raihanf_alamat` varchar(150) DEFAULT NULL,
  `raihanf_no_telp` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `raihanf_admin`
--

INSERT INTO `raihanf_admin` (`raihanf_id_admin`, `raihanf_nip`, `raihanf_nama`, `raihanf_username`, `raihanf_password`, `raihanf_alamat`, `raihanf_no_telp`) VALUES
('ADM001', '1987654321', 'Admin Perpus', 'admin', 'admin123', 'Perpustakaan Sekolah', '08123456789');

-- --------------------------------------------------------

--
-- Table structure for table `raihanf_buku`
--

CREATE TABLE `raihanf_buku` (
  `raihanf_id_buku` varchar(10) NOT NULL,
  `raihanf_judul` varchar(150) NOT NULL,
  `raihanf_pengarang` varchar(100) NOT NULL,
  `raihanf_penerbit` varchar(100) NOT NULL,
  `raihanf_tahun_terbit` year(4) NOT NULL,
  `raihanf_stok_total` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `raihanf_buku`
--

INSERT INTO `raihanf_buku` (`raihanf_id_buku`, `raihanf_judul`, `raihanf_pengarang`, `raihanf_penerbit`, `raihanf_tahun_terbit`, `raihanf_stok_total`) VALUES
('BK001', 'Basis Data', 'Abdul Kadir', 'Informatika', '2021', 10);

-- --------------------------------------------------------

--
-- Table structure for table `raihanf_siswa`
--

CREATE TABLE `raihanf_siswa` (
  `raihanf_id_siswa` varchar(10) NOT NULL,
  `raihanf_nis` varchar(20) NOT NULL,
  `raihanf_uid_rfid` varchar(20) NOT NULL,
  `raihanf_nama` varchar(100) NOT NULL,
  `raihanf_kelas` varchar(20) NOT NULL,
  `raihanf_no_telp` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `raihanf_siswa`
--

INSERT INTO `raihanf_siswa` (`raihanf_id_siswa`, `raihanf_nis`, `raihanf_uid_rfid`, `raihanf_nama`, `raihanf_kelas`, `raihanf_no_telp`) VALUES
('SIS001', '12345678', 'UID001', 'Raihan Fadlansyah', 'XI RPL B', '085134694484');

-- --------------------------------------------------------

--
-- Table structure for table `raihanf_transaksi_peminjaman`
--

CREATE TABLE `raihanf_transaksi_peminjaman` (
  `raihanf_id_transaksi` varchar(10) NOT NULL,
  `raihanf_id_siswa` varchar(10) NOT NULL,
  `raihanf_id_buku` varchar(10) NOT NULL,
  `raihanf_tgl_pinjam` date NOT NULL,
  `raihanf_tgl_kembali` date DEFAULT NULL,
  `raihanf_status` enum('dipinjam','dikembalikan') NOT NULL DEFAULT 'dipinjam'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `raihanf_transaksi_peminjaman`
--

INSERT INTO `raihanf_transaksi_peminjaman` (`raihanf_id_transaksi`, `raihanf_id_siswa`, `raihanf_id_buku`, `raihanf_tgl_pinjam`, `raihanf_tgl_kembali`, `raihanf_status`) VALUES
('TR001', 'SIS001', 'BK001', '2026-02-02', '2026-02-12', 'dikembalikan');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `raihanf_admin`
--
ALTER TABLE `raihanf_admin`
  ADD PRIMARY KEY (`raihanf_id_admin`);

--
-- Indexes for table `raihanf_buku`
--
ALTER TABLE `raihanf_buku`
  ADD PRIMARY KEY (`raihanf_id_buku`);

--
-- Indexes for table `raihanf_siswa`
--
ALTER TABLE `raihanf_siswa`
  ADD PRIMARY KEY (`raihanf_id_siswa`);

--
-- Indexes for table `raihanf_transaksi_peminjaman`
--
ALTER TABLE `raihanf_transaksi_peminjaman`
  ADD PRIMARY KEY (`raihanf_id_transaksi`),
  ADD KEY `raihanf_id_siswa` (`raihanf_id_siswa`),
  ADD KEY `raihanf_id_buku` (`raihanf_id_buku`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `raihanf_transaksi_peminjaman`
--
ALTER TABLE `raihanf_transaksi_peminjaman`
  ADD CONSTRAINT `raihanf_transaksi_peminjaman_ibfk_1` FOREIGN KEY (`raihanf_id_siswa`) REFERENCES `raihanf_siswa` (`raihanf_id_siswa`),
  ADD CONSTRAINT `raihanf_transaksi_peminjaman_ibfk_2` FOREIGN KEY (`raihanf_id_buku`) REFERENCES `raihanf_buku` (`raihanf_id_buku`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
