-- PAKAI FILE INI KALAU database db_puskesmas KAMU SUDAH ADA ISINYA
-- dan kamu cuma mau nambah kolom gambar & kategori tanpa hapus data lama.
-- Jalankan di phpMyAdmin (tab SQL) pada database db_puskesmas.
--
-- (Kalau ini import database BARU dari nol, tidak perlu file ini,
--  cukup import puskesmas.sql ATAU puskesmas_akurat.sql saja,
--  keduanya sudah termasuk kolom gambar & kategori.)
--
-- Catatan: admin.php versi baru juga otomatis menambahkan kolom ini sendiri
-- kalau belum ada saat halaman admin dibuka, jadi file ini sifatnya jaga-jaga saja.

ALTER TABLE `puskesmas`
  ADD COLUMN IF NOT EXISTS `gambar` VARCHAR(255) NULL AFTER `telepon`;

ALTER TABLE `puskesmas`
  ADD COLUMN IF NOT EXISTS `kategori` VARCHAR(20) NOT NULL DEFAULT 'puskesmas' AFTER `gambar`;

ALTER TABLE `puskesmas`
  ADD COLUMN IF NOT EXISTS `jam_operasional` VARCHAR(100) NULL AFTER `kategori`;

ALTER TABLE `puskesmas`
  ADD COLUMN IF NOT EXISTS `no_izin` VARCHAR(100) NULL AFTER `jam_operasional`;

ALTER TABLE `puskesmas`
  ADD COLUMN IF NOT EXISTS `jenis_layanan` VARCHAR(20) NOT NULL DEFAULT 'offline' AFTER `no_izin`;
