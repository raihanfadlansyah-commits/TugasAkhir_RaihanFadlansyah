-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 23, 2026 at 09:47 AM
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
-- Table structure for table `raihanf_buku`
--

CREATE TABLE `raihanf_buku` (
  `raihanf_id_buku` varchar(10) NOT NULL,
  `raihanf_img` varchar(255) NOT NULL,
  `raihanf_judul` varchar(150) NOT NULL,
  `raihanf_pengarang` varchar(100) NOT NULL,
  `raihanf_penerbit` varchar(100) NOT NULL,
  `raihanf_tahun_terbit` year(4) NOT NULL,
  `raihanf_kategori` enum('Fiksi','Nonfiksi','Buku Pelajaran','Referensi','Agama','Sains','Sosial','Bahasa','Seni & Olahraga','Teknologi','Sejarah','Geografi','Biografi','Majalah / Koran') DEFAULT NULL,
  `raihanf_stok_total` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `raihanf_buku`
--

INSERT INTO `raihanf_buku` (`raihanf_id_buku`, `raihanf_img`, `raihanf_judul`, `raihanf_pengarang`, `raihanf_penerbit`, `raihanf_tahun_terbit`, `raihanf_kategori`, `raihanf_stok_total`) VALUES
('BK001', '1776823058_Laskar Pelangi Design_ Andreas Kusumahadi.jpg', 'Laskar Pelangi', 'Andrea Hirata', 'Bentang Pustaka', '2005', 'Nonfiksi', 10),
('BK002', '1776831268_bumi manusia.jpg', 'Bumi Manusia', 'Pramoedya Ananta Toer', 'Lentera Dipantara', '1980', 'Nonfiksi', 10),
('BK003', '1776823342_Gramedia Makassar - RONGGENG DUKUH PARUK - HARD COVER.jpg', 'Ronggeng Dukuh Paruk', 'Ahmad Tohari', 'Gramedia', '1982', 'Nonfiksi', 10),
('BK004', '1776823480_Buku Sejarah nasional Indonesia Full Jilid 1 sampai 6.jpg', 'Sejarah Nasional Indonesia', 'Marwati Djoened Poesponegoro dan Nugroho Notosusanto', 'Balai Pustaka', '1975', 'Nonfiksi', 10),
('BK005', '1776823510_Negeri 5 Menara.jpg', 'Negeri 5 Menara', 'Ahmad Fuadi', 'Gramedia Pustaka Utama', '2009', 'Nonfiksi', 10);

-- --------------------------------------------------------

--
-- Table structure for table `raihanf_detail_peminjaman`
--

CREATE TABLE `raihanf_detail_peminjaman` (
  `raihanf_id_detail` varchar(10) NOT NULL,
  `raihaf_id_peminjaman` varchar(10) NOT NULL,
  `raihanf_id_buku` varchar(10) NOT NULL,
  `raihanf_total_buku` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `raihanf_detail_peminjaman`
--

INSERT INTO `raihanf_detail_peminjaman` (`raihanf_id_detail`, `raihaf_id_peminjaman`, `raihanf_id_buku`, `raihanf_total_buku`) VALUES
('DTL001', 'PJM001', 'BK001', 1),
('DTL002', 'PJM002', 'BK002', 1),
('DTL003', 'PJM002', 'BK003', 1),
('DTL004', 'PJM002', 'BK005', 1);

-- --------------------------------------------------------

--
-- Table structure for table `raihanf_peminjaman`
--

CREATE TABLE `raihanf_peminjaman` (
  `raihanf_id_peminjaman` varchar(10) NOT NULL,
  `raihanf_id_user` varchar(10) NOT NULL,
  `raihanf_tgl_pinjam` date NOT NULL,
  `raihanf_tenggat_waktu` datetime NOT NULL,
  `raihanf_tgl_kembali` date DEFAULT NULL,
  `raihanf_status` enum('Dipinjam','Dikembalikan','Terlambat','Lebih Cepat','Tepat Waktu') NOT NULL DEFAULT 'Dipinjam'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `raihanf_peminjaman`
--

INSERT INTO `raihanf_peminjaman` (`raihanf_id_peminjaman`, `raihanf_id_user`, `raihanf_tgl_pinjam`, `raihanf_tenggat_waktu`, `raihanf_tgl_kembali`, `raihanf_status`) VALUES
('PJM001', 'SIS001', '2026-04-23', '2026-04-30 09:45:00', NULL, 'Dipinjam'),
('PJM002', 'SIS002', '2026-04-23', '2026-04-30 09:46:00', NULL, 'Dipinjam');

-- --------------------------------------------------------

--
-- Table structure for table `raihanf_riwayat_pengunjung`
--

CREATE TABLE `raihanf_riwayat_pengunjung` (
  `raihanf_id_pengunjung` varchar(10) NOT NULL,
  `raihanf_id_user` varchar(10) NOT NULL,
  `raihanf_nama` varchar(50) NOT NULL,
  `raihanf_kelas` varchar(5) NOT NULL,
  `raihanf_jurusan` varchar(50) NOT NULL,
  `raihanf_waktu_masuk` datetime NOT NULL,
  `raihanf_waktu_keluar` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `raihanf_riwayat_pengunjung`
--

INSERT INTO `raihanf_riwayat_pengunjung` (`raihanf_id_pengunjung`, `raihanf_id_user`, `raihanf_nama`, `raihanf_kelas`, `raihanf_jurusan`, `raihanf_waktu_masuk`, `raihanf_waktu_keluar`) VALUES
('PJG001', 'SIS001', 'Raihan Fadlansyah', 'XI', 'Perkembangan Perangkat Lunak', '2026-04-23 14:36:24', '2026-04-23 14:36:30'),
('PJG002', 'SIS002', 'Salma Ashanadiya', 'XI', 'Perkembangan Perangkat Lunak', '2026-04-23 14:36:26', '2026-04-23 14:36:32');

-- --------------------------------------------------------

--
-- Table structure for table `raihanf_user`
--

CREATE TABLE `raihanf_user` (
  `raihanf_id_user` varchar(10) NOT NULL,
  `raihanf_nama` varchar(50) DEFAULT NULL,
  `raihanf_nomor_induk` varchar(25) DEFAULT NULL,
  `raihanf_rfid` varchar(50) DEFAULT NULL,
  `raihanf_username` varchar(25) DEFAULT NULL,
  `raihanf_password` varchar(50) DEFAULT NULL,
  `raihanf_kelas` enum('X','XI','XII') DEFAULT NULL,
  `raihanf_jurusan` enum('Mekatronika','Kimia Industri','Perkembangan Perangkat Lunak','Animasi','Desain Komunikasi Visual','Teknik Permesinan') NOT NULL,
  `raihanf_no_telpon` varchar(50) DEFAULT NULL,
  `raihanf_role` enum('siswa','admin','petugas') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `raihanf_user`
--

INSERT INTO `raihanf_user` (`raihanf_id_user`, `raihanf_nama`, `raihanf_nomor_induk`, `raihanf_rfid`, `raihanf_username`, `raihanf_password`, `raihanf_kelas`, `raihanf_jurusan`, `raihanf_no_telpon`, `raihanf_role`) VALUES
('ADM001', 'admin', NULL, NULL, 'admin', '202cb962ac59075b964b07152d234b70', NULL, 'Mekatronika', NULL, 'admin'),
('PTG001', 'petugas', NULL, NULL, 'petugas', '202cb962ac59075b964b07152d234b70', NULL, 'Mekatronika', '32113213', 'petugas'),
('SIS001', 'Raihan Fadlansyah', '10243312', '0001707462', NULL, NULL, 'XI', 'Perkembangan Perangkat Lunak', '812345677', 'siswa'),
('SIS002', 'Salma Ashanadiya', '10243317', '0001461705', NULL, NULL, 'XI', 'Perkembangan Perangkat Lunak', '812345677', 'siswa');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `raihanf_buku`
--
ALTER TABLE `raihanf_buku`
  ADD PRIMARY KEY (`raihanf_id_buku`);

--
-- Indexes for table `raihanf_detail_peminjaman`
--
ALTER TABLE `raihanf_detail_peminjaman`
  ADD PRIMARY KEY (`raihanf_id_detail`),
  ADD KEY `raihaf_id_peminjaman` (`raihaf_id_peminjaman`,`raihanf_id_buku`),
  ADD KEY `raihanf_id_buku` (`raihanf_id_buku`);

--
-- Indexes for table `raihanf_peminjaman`
--
ALTER TABLE `raihanf_peminjaman`
  ADD PRIMARY KEY (`raihanf_id_peminjaman`),
  ADD KEY `raihanf_id_siswa` (`raihanf_id_user`),
  ADD KEY `raihanf_id_user` (`raihanf_id_user`);

--
-- Indexes for table `raihanf_riwayat_pengunjung`
--
ALTER TABLE `raihanf_riwayat_pengunjung`
  ADD PRIMARY KEY (`raihanf_id_pengunjung`),
  ADD KEY `raihanf_id_user` (`raihanf_id_user`);

--
-- Indexes for table `raihanf_user`
--
ALTER TABLE `raihanf_user`
  ADD PRIMARY KEY (`raihanf_id_user`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `raihanf_detail_peminjaman`
--
ALTER TABLE `raihanf_detail_peminjaman`
  ADD CONSTRAINT `raihanf_detail_peminjaman_ibfk_1` FOREIGN KEY (`raihanf_id_buku`) REFERENCES `raihanf_buku` (`raihanf_id_buku`),
  ADD CONSTRAINT `raihanf_detail_peminjaman_ibfk_2` FOREIGN KEY (`raihaf_id_peminjaman`) REFERENCES `raihanf_peminjaman` (`raihanf_id_peminjaman`);

--
-- Constraints for table `raihanf_peminjaman`
--
ALTER TABLE `raihanf_peminjaman`
  ADD CONSTRAINT `raihanf_peminjaman_ibfk_3` FOREIGN KEY (`raihanf_id_user`) REFERENCES `raihanf_user` (`raihanf_id_user`);

--
-- Constraints for table `raihanf_riwayat_pengunjung`
--
ALTER TABLE `raihanf_riwayat_pengunjung`
  ADD CONSTRAINT `raihanf_riwayat_pengunjung_ibfk_1` FOREIGN KEY (`raihanf_id_user`) REFERENCES `raihanf_user` (`raihanf_id_user`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
