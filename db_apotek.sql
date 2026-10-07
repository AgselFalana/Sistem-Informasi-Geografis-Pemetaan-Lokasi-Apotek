-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 06, 2026 at 10:53 PM
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
-- Database: `db_apotek`
--

-- --------------------------------------------------------

--
-- Table structure for table `apotek`
--

CREATE TABLE `apotek` (
  `id` int(11) NOT NULL,
  `nama` varchar(150) NOT NULL,
  `alamat` text NOT NULL,
  `latitude` double NOT NULL,
  `longitude` double NOT NULL,
  `telepon` varchar(20) DEFAULT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `kategori` varchar(20) NOT NULL DEFAULT 'apotek',
  `jam_operasional` varchar(100) DEFAULT NULL,
  `no_izin` varchar(100) DEFAULT NULL,
  `jenis_layanan` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `apotek`
--

INSERT INTO `apotek` (`id`, `nama`, `alamat`, `latitude`, `longitude`, `telepon`, `gambar`, `kategori`, `jam_operasional`, `no_izin`, `jenis_layanan`) VALUES
(11, 'apotek remaja', 'JALAN MAYJEND SUTOYO No.89 A, RT.44, Sungai Pinang Dalam, Kec. Sungai Pinang, Kota Samarinda, Kalimantan Timur 75122', -0.476697, 117.165666, '082158771551', 'puskesmas_6ab58ffaade9d_1790283770.jpeg', 'apotek', 'Senin-Jumat 08:00-14:00', 'T-500.16.7.2/1483/DPMPTSP.03', 'bisa delivery by kurir'),
(12, 'Apotek Mitra Sehat', 'Jl. Ahmad Yani No.2, RT.34, Sungai Pinang Dalam, Kec. Sungai Pinang, Kota Samarinda, Kalimantan Timur 75117', -0.47284, 117.166321, '+62 852-5082-0018', 'puskesmas_6ab58a73de964_1790282355.jpeg', 'apotek', '07.45 - 21.45', 'T-500.16.7.2/395/DPMPTSP.03', 'bisa delivery by kur'),
(13, 'Apotek K-24 Ahmad Yani Samarinda', 'Jl. Jenderal Ahmad Yani I No.10, Sungai Pinang Dalam, Kec. Sungai Pinang, Kota Samarinda, Kalimantan Timur 75117', -0.475347, 117.161024, '08115583907', 'puskesmas_6ab9c88a16824_1790560394.jpeg', 'apotek', 'Senin-Jumat 08:00-14:00', 'T-500.16.7.2/395/DPMPTSP.03', 'bisa delivery by kur'),
(14, 'Apotek Julia', 'Jl. Jenderal Ahmad Yani I No.36, Sungai Pinang Dalam, Kec. Sungai Pinang, Kota Samarinda, Kalimantan Timur 75117', -0.47563, 117.161333, '082159885109', 'puskesmas_6ab9c8988d7e8_1790560408.jpeg', 'apotek', '07.45 - 21.45', 'T-500.16.7.2/395/DPMPTSP.03', 'bisa delivery by kur'),
(15, 'Apotek Happy Farma', 'G5C8+W42, Jl. Kemakmuran, Sungai Pinang Dalam, Kec. Sungai Pinang, Kota Samarinda, Kalimantan Timur 75117', -0.477735, 117.16527, '08115583907', 'puskesmas_6ab9c8a459768_1790560420.jpeg', 'apotek', 'Senin-Jumat 08:00-14:00', 'T-500.16.7.2/395/DPMPTSP.03', 'bisa delivery by kur'),
(16, 'Apotek Bunda Farma', 'Jl. Ahmad Yani No.2, Temindung Permai, Sungai Pinang, Samarinda City, East Kalimantan 75242', -0.471584, 117.167493, '08115583907', 'puskesmas_6ab9c8b673af5_1790560438.jpeg', 'apotek', 'Senin-Jumat 08:00-14:00', 'T-500.16.7.2/395/DPMPTSP.03', 'bisa delivery by kur'),
(17, 'Apotek Kimia Farma 273 A. Yani', 'Komplek Mitra Mas, Jl. Ahmad Yani No.4A, Temindung Permai, Kec. Sungai Pinang, Kota Samarinda, Kalimantan Timur 75119', -0.475644, 117.160625, '+62 852-5082-0018', 'puskesmas_6ab9c8d2008e3_1790560466.jpeg', 'apotek', 'Senin-Jumat 08:00-14:00', 'T-500.16.7.2/395/DPMPTSP.03', 'bisa delivery by kur'),
(18, 'Apotek Avicenna Farma', 'Jl. Sentosa No.18, RT.031, Sungai Pinang Dalam, Kec. Sungai Pinang, Kota Samarinda, Kalimantan Timur 75117', -0.475166, 117.168819, '082158771551', 'puskesmas_6ab9c8e0a4109_1790560480.jpeg', 'apotek', 'Senin-Jumat 08:00-14:00', 'T-500.16.7.2/395/DPMPTSP.03', 'bisa delivery by kur'),
(19, 'Apotek Mitra Sahabat', 'Jl. A. Yani Ruko CTC A3, Temindung Permai, Sungai Pinang, Samarinda City, East Kalimantan 75242', -0.4767, 117.158645, '+62 852-5082-0018', 'puskesmas_6ab9c8f791a8b_1790560503.jpeg', 'apotek', 'Senin-Jumat 08:00-14:00', 'T-500.16.7.2/1483/DPMPTSP.03', 'bisa delivery by kur'),
(20, 'Apotek Sehat Abadi', 'Jl. Gerilya No.RT 51, Sungai Pinang Dalam, Kec. Sungai Pinang, Kota Samarinda, Kalimantan Timur 75117', -0.48365, 117.17089, '082158771551', 'puskesmas_6ab9c90a6356d_1790560522.jpeg', 'apotek', 'Senin-Jumat 08:00-14:00', 'T-500.16.7.2/1483/DPMPTSP.03', 'bisa delivery by kur'),
(21, 'Apotek Merak', 'G5C3+6W6, Jl. Hasan Basri, Bandara, Kec. Sungai Pinang, Kota Samarinda, Kalimantan Timur 75242', -0.4794465, 117.154777, '0541733601', 'puskesmas_6ab9c917f02ee_1790560535.jpeg', 'apotek', 'Senin-Jumat 08:00-14:00', 'T-500.16.7.2/1483/DPMPTSP.03', 'bisa delivery by kur'),
(22, 'Apotek JULIA HB', 'Jl. Hasan Basri No.67, Temindung Permai, Kec. Sungai Pinang, Kota Samarinda, Kalimantan Timur 75117', -0.479458, 117.154106, '+62 852-5082-0018', 'puskesmas_6ab9c9266a117_1790560550.jpeg', 'apotek', 'Senin-Jumat 08:00-14:00', 'T-500.16.7.2/1483/DPMPTSP.03', 'bisa delivery by kur'),
(23, 'Apotek Sehat Barokah', 'G5HJ+27W, Jl. Damanhuri, Mugirejo, Kec. Sungai Pinang, Kota Samarinda, Kalimantan Timur 75117', -0.472381, 117.180712, '08115583907', 'puskesmas_6ab9c9416d6d5_1790560577.jpeg', 'apotek', 'Senin-Jumat 08:00-14:00', 'T-500.16.7.2/395/DPMPTSP.03', 'bisa delivery by kur'),
(24, 'Apotek Sahabat Mulawarman', 'Jl. M. Yamin No.22, Gn. Kelua, Kec. Samarinda Ulu, Kota Samarinda, Kalimantan Timur 75123', -0.466847, 117.148432, '0541733601', 'puskesmas_6ab9c950744e5_1790560592.jpeg', 'apotek', 'Senin-Jumat 08:00-14:00', 'T-500.16.7.2/395/DPMPTSP.03', 'bisa delivery by kur'),
(25, 'Apotek Kimia Farma 105 M. Yamin', 'Ruko M. Yamin Square, Jl. M. Yamin No.6, Gn. Kelua, Kec. Samarinda Ulu, Kota Samarinda, Kalimantan Timur 75123', -0.467329, 117.148227, '08115583907', 'puskesmas_6ab9c962b3be9_1790560610.jpeg', 'apotek', 'Senin-Jumat 08:00-14:00', 'T-500.16.7.2/395/DPMPTSP.03', 'bisa delivery by kur'),
(26, 'Apotek Kurniawan', 'Jl. Pramuka No.14, Gn. Kelua, Kec. Samarinda Ulu, Kota Samarinda, Kalimantan Timur 75123', -0.4635, 117.15217, '082158771551', 'puskesmas_6ab9c97139f7d_1790560625.jpeg', 'apotek', 'Senin-Jumat 08:00-14:00', 'T-500.16.7.2/1483/DPMPTSP.03', 'bisa delivery by kur'),
(27, 'Apotek Pradita Medica Farma', 'Jl. Pramuka No.28, Gn. Kelua, Kec. Samarinda Ulu, Kota Samarinda, Kalimantan Timur 75243', -0.463064, 117.151308, '+62 852-5082-0018', 'puskesmas_6ab9c97d97db9_1790560637.jpeg', 'apotek', 'Senin-Jumat 08:00-14:00', 'T-500.16.7.2/1483/DPMPTSP.03', 'bisa delivery by kur'),
(28, 'APOTEK BAKTI MEDIKA SAMARINDA', 'Jl. Perjuangan No.19, RW.21, Sempaja Sel., Kec. Samarinda Utara, Kota Samarinda, Kalimantan Timur 75243', -0.463672, 117.156552, '082159885109', 'puskesmas_6ab9c9898c703_1790560649.jpeg', 'apotek', 'Senin-Jumat 08:00-14:00', 'T-500.16.7.2/1483/DPMPTSP.03', 'bisa delivery by kur'),
(29, 'Apotek Famro', 'Kampus Unmul, Jl. Perjuangan No.23, Sempaja Sel., Kec. Samarinda Utara, Kota Samarinda, Kalimantan Timur 75119', -0.462718, 117.156619, '+62 852-5082-0018', 'puskesmas_6ab9c9944f46c_1790560660.jpeg', 'apotek', 'Senin-Jumat 08:00-14:00', 'T-500.16.7.2/1483/DPMPTSP.03', 'bisa delivery by kur'),
(30, 'APOTEK SANTA', 'Jl.KH WAHID HASYI\'M I No.18, Sempaja Sel., Kec. Samarinda Utara, Kota Samarinda, Kalimantan Timur 75119', -0.462019, 117.15063, '+62 852-5082-0018', 'puskesmas_6ab9c9a14f3e7_1790560673.jpeg', 'apotek', 'Senin-Jumat 08:00-14:00', 'T-500.16.7.2/1483/DPMPTSP.03', 'bisa delivery by kur'),
(31, 'apotek shinta dewi', 'Jl. Yos Sudarso IV, Tlk. Lingga, Kec. Sangatta Utara, Kabupaten Kutai Timur, Kalimantan Timur 75683', 0.532422, 117.526988, '082158771551', 'puskesmas_6ab592716b928_1790284401.png', 'apotek', 'Senin-Jumat 08:00-14:00', 'T-500.16.7.2/1483/DPMPTSP.03', 'bisa delivery by kurir'),
(32, 'Apotek XS Mart Lambung Mangkurat', 'Jl. Yos Sudarso IV, Tlk. Lingga, Kec. Sangatta Utara, Kabupaten Kutai Timur, Kalimantan Timur 75683', -0.489235, 117.160271, '082158771551', 'puskesmas_6ab5933ae8ff7_1790284602.jpeg', 'apotek', 'Senin-Jumat 08:00-14:00', 'T-500.16.7.2/1483/DPMPTSP.03', 'bisa delivery by kurir'),
(34, 'Shinta Dewi 2', 'Jl. Yos Sudarso IV, Tlk. Lingga, Kec. Sangatta Utara, Kabupaten Kutai Timur, Kalimantan Timur 75683', 0.532422, 117.526988, '81258555440', 'puskesmas_6ac54e7b3106e_1791315579.jpeg', 'apotek', '07.45 - 21.45', 'T-500.16.7.2/395/DPMPTSP.03', 'bisa delivery by kurir'),
(35, 'Krishna Farma', 'Jl. Yos Sudarso IV, Tlk. Lingga, Kec. Sangatta Utara, Kabupaten Kutai Timur, Kalimantan Timur 75683', 0.520604, 117.530462, '85211239698', '', 'apotek', '07.45 - 21.45', 'T-500.16.7.2/1483/DPMPTSP.03', 'bisa delivery by kurir'),
(36, 'Krishna Farma', 'Jl. Yos Sudarso IV, Tlk. Lingga, Kec. Sangatta Utara, Kabupaten Kutai Timur, Kalimantan Timur 75683', 0.520604, 117.530462, '85211239698', 'puskesmas_6ac54ee80fd9d_1791315688.jpeg', 'apotek', '07.45 - 21.45', 'T-500.16.7.2/1483/DPMPTSP.03', 'bisa delivery by kurir'),
(37, 'Medika Farma 2', 'Jl. Yos Sudarso IV No.005, RT.23/RW.08, Tlk. Lingga, Kec. Sangatta Utara, Kabupaten Kutai Timur, Kalimantan Timur 75611', 0.525046, 117.527773, '81251881646', '', 'apotek', '07.30 - 21.30', 'nanti lihat excel', 'bisa delivery by kurir'),
(38, 'Medika Farma 2', 'Jl. Yos Sudarso IV No.005, RT.23/RW.08, Tlk. Lingga, Kec. Sangatta Utara, Kabupaten Kutai Timur, Kalimantan Timur 75611', 0.525046, 117.527773, '81251881646', 'puskesmas_6ac54f521e66b_1791315794.jpeg', 'apotek', '07.30 - 21.30', 'nanti lihat excel', 'bisa delivery by kurir'),
(39, 'PMI Farma', 'Jl. Yos Sudarso IV No.4, Tlk. Lingga, Kec. Sangatta Utara, Kabupaten Kutai Timur, Kalimantan Timur 75683', 0.527158, 117.527252, '81380098283', 'puskesmas_6ac54f95acfff_1791315861.jpeg', 'apotek', '07.00 - 21.00', '503/016/DPMPTSP-PPNP/FARMASI-SIA/IX/2021', 'bisa delivery by kurir'),
(40, 'Gavi Farma', 'Jl. Pendidikan RT. 04/1, Desa/Kelurahan Teluk Lingga, Kutai Timur', 0.513866, 117.542939, '8971298422', 'puskesmas_6ac54fee9e934_1791315950.jpeg', 'apotek', '07.45 - 21.45', 'T-500.16.7.2/1482/DPMPTSP.03', 'harus offline'),
(41, 'Azka Medika 2', 'Jl. AW, Syahrani No.35, RT.56, Teluk Lingga, Sangatta Utara, East Kutai Regency, East Kalimantan 75683', 0.513952, 117.55076, '82266170614', '', 'apotek', '07.30 - 21.30', '26082200603410004', 'bisa delivery by kurir'),
(42, 'Azka Medika 2', 'Jl. AW, Syahrani No.35, RT.56, Teluk Lingga, Sangatta Utara, East Kutai Regency, East Kalimantan 75683', 0.513952, 117.55076, '82266170614', 'puskesmas_6ac55038e6f51_1791316024.jpeg', 'apotek', '07.30 - 21.30', '26082200603410004', 'bisa delivery by kurir'),
(43, 'Kirana Medika', 'nanti di cek', 0, 0, '85392029208', 'puskesmas_6ac5508624881_1791316102.jpeg', 'apotek', '08.00 - 22.00', '02204048920760001', 'harus offline'),
(44, 'Apotek Ananda Firdaus', 'Jl. A. Wahab Syahranie No.RT. 004, Tlk. Lingga, Kec. Sangatta Utara, Sangatta, Kalimantan Timur 75683', 0.514048, 117.547053, '81348571720', 'puskesmas_6ac550ef25522_1791316207.jpeg', 'apotek', '07.30 - 21.30', '503/017/DMPTSP-PPNP/FARMASI-SIA/IX/2022', 'bisa delivery by kurir'),
(45, 'Apotek Ivana', 'Jl. Inpres No.RT 64, Sangatta Utara, Kec. Sangatta Utara, Kabupaten Kutai Timur, Kalimantan Timur 75611', 0.507084, 117.539508, '82220763968', 'puskesmas_6ac551359c46b_1791316277.jpeg', 'apotek', '08.00 - 23.00', 'adanya sip', 'bisa delivery by kurir'),
(46, 'Apotek Tamahita', 'FGXQ+W98, Jl. Inpres, Sangatta Utara, Kec. Sangatta Utara, Kabupaten Kutai Timur, Kalimantan Timur 75683', 0.500051, 117.538448, '82337833354', 'puskesmas_6ac5516fc865d_1791316335.jpeg', 'apotek', '08.00 - 21.30', '02072500243570001', 'bisa delivery by kurir'),
(47, 'Apotek Rezky Farma', 'Jl. Jenderal Sudirman, Sangatta Utara, Kec. Sangatta Utara, Kabupaten Kutai Timur, Kalimantan Timur 75683', 0.496712, 117.53651, '82131247228', 'puskesmas_6ac551b96e424_1791316409.jpeg', 'apotek', '08.00 - 23.00', 'T-500.16.7.21889/DPMPTSP.03', 'bisa delivery by kurir'),
(48, 'Apotek Athirah Farma', 'Jl. APT Pranoto No.A 32, RT.058, Sangatta Utara, Kec. Sangatta Utara, Kabupaten Kutai Timur, Kalimantan Timur 75683', 0.494995, 117.536066, '85255089993', 'puskesmas_6ac5520618fc6_1791316486.jpeg', 'apotek', '08.00 - 23.00', '17062300444760001', 'bisa delivery by kurir'),
(49, 'Apotek Alfaza', 'Jl. APT Pranoto No.Rt.013, Sangatta Utara, Kec. Sangatta Utara, Kabupaten Kutai Timur, Kalimantan Timur 75611', 0.494678, 117.536839, '85250825583', 'puskesmas_6ac55248d6cbc_1791316552.jpeg', 'apotek', '08.00 - 22.00', '503/028/DPMPTSP-PPNP/FARMASI-SIA/XL/2021', 'bisa delivery by kurir'),
(50, 'Apotek Kenizio', 'Jl. APT Pranoto, RT.10/RW.No.262, Sangatta Utara, Kec. Sangatta Utara, Kabupaten Kutai Timur, Kalimantan Timur 75683', 0.494548, 117.536474, '82252534505', 'puskesmas_6ac552801c36b_1791316608.jpeg', 'apotek', '08.00 - 21.30', '23092300586540001', 'bisa delivery by kurir'),
(51, 'Apotek Rani Jaya', 'Jl. APT Pranoto No.141, Sangatta Utara, Kec. Sangatta Utara, Kabupaten Kutai Timur, Kalimantan Timur 75683', 0.484049, 117.5450111, '83891338629', 'puskesmas_6ac552d3e925c_1791316691.jpeg', 'apotek', '07.00 - 22.00', '02122200010120001', 'bisa delivery by kurir'),
(52, 'Apotek Kembar Sentosa', 'FGRJ+8Q5, Jl. Diponegoro, Sangatta Utara, Kec. Sangatta Utara, Kabupaten Kutai Timur, Kalimantan Timur 75683', 0.490993, 117.531962, '82156328399', 'puskesmas_6ac5530fd346f_1791316751.jpeg', 'apotek', '08.00 - 22.00', '19012200462720001', 'harus offline'),
(53, 'Apotek Qiosta Farma', 'Jl. I. Abdul Muis Pinang Baru 01 No.25, RT.11, Sangatta Utara, Kec. Sangatta Utara, Kabupaten Kutai Timur, Kalimantan Timur 75683', 0.496353, 117.529409, '82293649384', 'puskesmas_6ac55350e3cd9_1791316816.jpeg', 'apotek', '08.00 - 22.00', '30122200233350001', 'harus offline'),
(54, 'Apotek Komang Farma 1', 'Jl. Yos Sudarso II No. 5 Rt. 26 , Teluk Lingga, Kec. Sangatta Utara, Kabupaten Kutai Timur, Kalimantan Timur 75683', 0.498863, 117.532284, '81311856707', 'puskesmas_6ac55389e39e5_1791316873.jpeg', 'apotek', '08.00 - 22.00', '603/003/DPMPTSP:PPNP/FARMASI-SIA/VI2018', 'bisa delivery by kurir'),
(55, 'Apotek Shinta Dewi 1', 'Jl. Yos Sudarso II, RT.14/RW.03, Sangatta Utara, Kec. Sangatta Utara, Kabupaten Kutai Timur, Kalimantan Timur 75683', 0.501517, 117.533182, '81351911063', 'puskesmas_6ac553da8c0df_1791316954.jpeg', 'apotek', '07.45 - 21.45', '91200012412430003', 'bisa delivery by kurir'),
(56, 'Apotek Kimia Farma', 'Jl. Yos Sudarso II No.41, Sangatta Utara, Kec. Sangatta Utara, Kabupaten Kutai Timur, Kalimantan Timur 75611', 0.505257, 117.534338, '8115583927', 'puskesmas_6ac55420a8f14_1791317024.jpeg', 'apotek', '08.00 - 00.00', '81200049025081785', 'bisa delivery by kurir'),
(57, 'Apotek Duta Farma', 'Jl. Yos Sudarso II, Sangatta Utara, Kec. Sangatta Utara, Kabupaten Kutai Timur, Kalimantan Timur 75683', 0.508397, 117.535468, '82229418700', 'puskesmas_6ac554588fa42_1791317080.jpeg', 'apotek', '08.00 - 22.00', '503/021/DPMPTSP-PPNP/SDMNAKES-SIPA/V/2021', 'bisa delivery by kurir'),
(58, 'Apotek K-24', 'GG5P+W6C, Sangatta Utara, Kec. Sangatta Utara, Kabupaten Kutai Timur, Kalimantan Timur 75683', 0.509854, 117.535531, '82159045952', 'puskesmas_6ac554a727b3e_1791317159.jpeg', 'apotek', '00.00-23.59', '12330003011970002', 'bisa delivery by kurir'),
(59, 'Apotek Medika Farma 1', 'Jl. Yos Sudarso IV No.16, Sangatta Utara, Kec. Sangatta Utara, Kabupaten Kutai Timur, Kalimantan Timur 75611', 0.51343, 117.536175, '85752316061', '', 'apotek', '08.00 - 21.30', '503/015/DPMPTSP-PPNP/FARMASI-SIA/VII/2021', 'bisa delivery by kurir'),
(60, 'Apotek Enola Farma', 'GG6P+RJH, Jl. Yos Sudarso II, Sangatta Utara, Kec. Sangatta Utara, Kabupaten Kutai Timur, Kalimantan Timur 75776', 0.512073, 117.536608, '81254421928', 'puskesmas_6ac5553195568_1791317297.jpeg', 'apotek', '07.00 - 01.00', 'gatau nanti ditanyakan ke apoteker', 'bisa delivery by kurir'),
(61, 'Apotek Komang Farma 2', 'Jl. Yos Sudarso II No. 5 Rt. 26 Gg. Perantau, Sangatta Utara, Kec. Sangatta Utara, Kabupaten Kutai Timur, Kalimantan Timur 75683', 0.505541, 117.534851, '81337114467', 'puskesmas_6ac55581cd8fb_1791317377.jpeg', 'apotek', '08.00 - 15.00', '05092300921080002', 'bisa delivery by kurir'),
(62, 'Apotek Tiga Putra', 'Jl. Yos Sudarso II No. 5 Rt. 26 Gg. Masjid, Sangatta Utara, Kec. Sangatta Utara, Kabupaten Kutai Timur, Kalimantan Timur 75683', 0.503396, 117.533874, '82152376672', 'puskesmas_6ac555c080db5_1791317440.jpeg', 'apotek', '07.30 - 22.00', 'gatau nanti ditanyakan ke apoteker', 'bisa delivery by kurir'),
(63, 'Apotek Adi Kurnia', 'Jl. Pusaka No.Rt.05, Sangatta Sel., Kec. Sangatta Utara, Kabupaten Kutai Timur, Kalimantan Timur 75683', 0.484606, 117.530901, '81219860950', 'puskesmas_6ac5561c93c2a_1791317532.jpeg', 'apotek', '08.00 - 21.00', '25112100508240001', 'bisa delivery by kurir'),
(64, 'Apotek Sehat', 'Jl. Thomas Square blok C/8, Sangatta Utara, Kec. Sangatta Utara, Kabupaten Kutai Timur, Kalimantan Timur 75683', 0.497023, 117.531177, '85287493903', 'puskesmas_6ac55659ec6b0_1791317593.jpeg', 'apotek', '08.00 - 21.00', '09032300875530001', 'bisa delivery by kurir'),
(65, 'Apotek Medika Farma \"Karya Etam\"', 'Jl. Karya Etam No.94D, RT.14, Sangatta Utara, Kec. Sangatta Utara, Kabupaten Kutai Timur, Kalimantan Timur 75611', 0.500042, 117.5371, '82148031938', 'puskesmas_6ac556a483124_1791317668.jpeg', 'apotek', '07.30 - 21.30', '07062200098590005', 'bisa delivery by kurir'),
(66, 'Apotek Sentral Medika Sangatta', 'GG7V+JQH, Jl. Ilham Maulana, Sangatta Utara, Kec. Sangatta Utara, Kabupaten Kutai Timur, Kalimantan Timur 75683', 0.514073, 117.544447, '85343840803', 'puskesmas_6ac556eba2828_1791317739.jpeg', 'apotek', '07.00 - 21.40', '2803230048420001', 'harus offline'),
(67, 'Apotek Dayung Farma', 'GGHJ+2XX, Jl. Dayung No.24, Tlk. Lingga, Kec. Sangatta Utara, Kabupaten Kutai Timur, Kalimantan Timur 75683', 0.52763, 117.532476, '82256688665', 'puskesmas_6ac557229d406_1791317794.jpeg', 'apotek', '07.30 - 21.30', '24032200422530007', 'bisa delivery by kurir'),
(68, 'Apotek Azzam Farma', 'GGHF+8W9, Jl. Munthe, Tlk. Lingga, Kec. Sangatta Utara, Kabupaten Kutai Timur, Kalimantan Timur 75683', 0.528349, 117.524837, '85250591703', 'puskesmas_6ac55770e0869_1791317872.jpeg', 'apotek', '08.00 - 23.00', 'T-500.16.7.2/ 502 /DPMPTSP.03', 'bisa delivery by kurir'),
(69, 'Apotek Azka Medika 1', 'GGMC+89X, Jl. Kabo Jaya, Swarga Bara, Kec. Sangatta Utara, Kabupaten Kutai Timur, Kalimantan Timur 75611', 0.533407, 117.520971, '81251887327', 'puskesmas_6ac557aaa2e58_1791317930.jpeg', 'apotek', '07.30 - 21.30', '26082200603410001', 'bisa delivery by kurir'),
(70, 'Apotek Violet Farma', 'Jl. Yos Sudarso IV No.06, Swarga Bara, Kec. Sangatta Utara, Kabupaten Kutai Timur, Kalimantan Timur 75681', 0.531875, 117.527081, '82251000044', 'puskesmas_6ac557e9bf7ad_1791317993.jpeg', 'apotek', '08.00 - 23.00', '02072200082660001', 'bisa delivery by kurir'),
(71, 'Apotek Medika Farma 3', 'Jl. Yos Sudarso IV No.43A, Sangatta Utara, Kec. Sangatta Utara, Kabupaten Kutai Timur, Kalimantan Timur 75683', 0.53058, 117.527238, '85245621306', 'puskesmas_6ac55831400ac_1791318065.jpeg', 'apotek', '07.30 - 21.30', '26082200603410002', 'bisa delivery by kurir'),
(72, 'Apotek Daily Med', 'Jl. APT Pranoto No.78B, Sangatta Utara, Kec. Sangatta Utara, Kabupaten Kutai Timur, Kalimantan Timur 75683', 0.497019, 117.533139, '81234898969', 'puskesmas_6ac5587c79af8_1791318140.jpeg', 'apotek', '7.30 - 21.30', '18062501244820001', 'bisa delivery by kurir'),
(73, 'Apotek Azzahra', 'GJ82+HVR, Jl. A Wahab Syaharanie, Tlk. Lingga, Kec. Sangatta Utara, Kabupaten Kutai Timur, Kalimantan Timur 75683', 0.516452, 117.602151, '81333920584', 'puskesmas_6ac5590352b08_1791318275.jpeg', 'apotek', '07.30 - 21.30', 'gatau nanti ditanyakan ke apoteker', 'bisa delivery by kurir'),
(74, 'Apotek Miracle', 'Jl. Yos Sudarso II No. 176 Rt. 15 Rw. 15, Sangatta Utara, Kec. Sangatta Utara, Kabupaten Kutai Timur, Kalimantan Timur 75611', 0.504045, 117.534132, '81326871658', 'puskesmas_6ac55961c1f1b_1791318369.jpeg', 'apotek', '08.00 - 22.00', '503/012/DPMPTSP-PPNP/FARMASI-SIA/VIII2022', 'bisa delivery by kurir');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `apotek`
--
ALTER TABLE `apotek`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `apotek`
--
ALTER TABLE `apotek`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=75;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
