-- ============================================================
-- Strip Map Ruas Jalan - Database Schema (Production Ready)
-- Dinas Bina Marga & Bina Konstruksi Provinsi Lampung
-- ============================================================

SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE DATABASE IF NOT EXISTS `stripmap_db`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `stripmap_db`;

-- ------------------------------------------------------------
-- 1. Tabel: ruas_jalan
-- Menyimpan master data ruas jalan, STA, koordinat peta & polyline
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `ruas_jalan` (
    `id`             INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `kode_ruas`      VARCHAR(50)     NOT NULL,
    `nama_ruas`      VARCHAR(255)    NOT NULL,
    `sta_awal`       DECIMAL(10,2)   NOT NULL DEFAULT 0.00 COMMENT 'STA Awal dalam meter',
    `sta_akhir`      DECIMAL(10,2)   NOT NULL DEFAULT 0.00 COMMENT 'STA Akhir dalam meter',
    `panjang`        DECIMAL(10,2)   NOT NULL DEFAULT 0.00 COMMENT 'Panjang total dalam meter',
    `koridor`        VARCHAR(100)    NULL COMMENT 'Koridor jalan',
    `kabupaten_kota` VARCHAR(100)    NULL COMMENT 'Kabupaten / Kota lokasi ruas',
    `lat_awal`       DECIMAL(10,7)   NULL COMMENT 'Latitude titik awal ruas',
    `lng_awal`       DECIMAL(10,7)   NULL COMMENT 'Longitude titik awal ruas',
    `lat_akhir`      DECIMAL(10,7)   NULL COMMENT 'Latitude titik akhir ruas',
    `lng_akhir`      DECIMAL(10,7)   NULL COMMENT 'Longitude titik akhir ruas',
    `koordinat_json` LONGTEXT        NULL COMMENT 'Polyline rute jalan (array [lng,lat]) hasil impor KML/KMZ, format JSON',
    `created_at`     DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`     DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_kode_ruas` (`kode_ruas`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 2. Tabel: stripmap
-- Menyimpan data kondisi jalan per segmen (Baik, Sedang, Rusak Ringan, Rusak Berat)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `stripmap` (
    `id`            INT UNSIGNED        NOT NULL AUTO_INCREMENT,
    `ruas_id`       INT UNSIGNED        NOT NULL,
    `tahun`         SMALLINT UNSIGNED   NULL COMMENT 'Tahun survei/data kondisi jalan',
    `sta_awal`      DECIMAL(10,2)       NOT NULL DEFAULT 0.00 COMMENT 'STA Awal segmen dalam meter',
    `sta_akhir`     DECIMAL(10,2)       NOT NULL DEFAULT 0.00 COMMENT 'STA Akhir segmen dalam meter',
    `panjang`       DECIMAL(10,2)       NOT NULL DEFAULT 0.00 COMMENT 'Panjang segmen dalam meter',
    `baik`          DECIMAL(10,2)       NOT NULL DEFAULT 0.00 COMMENT 'Panjang kondisi Baik (meter)',
    `sedang`        DECIMAL(10,2)       NOT NULL DEFAULT 0.00 COMMENT 'Panjang kondisi Sedang (meter)',
    `rusak_ringan`  DECIMAL(10,2)       NOT NULL DEFAULT 0.00 COMMENT 'Panjang kondisi Rusak Ringan (meter)',
    `rusak_berat`   DECIMAL(10,2)       NOT NULL DEFAULT 0.00 COMMENT 'Panjang kondisi Rusak Berat (meter)',
    `created_at`    DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`    DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (`id`),
    KEY `idx_ruas_id` (`ruas_id`),
    KEY `idx_stripmap_tahun` (`tahun`),

    CONSTRAINT `fk_stripmap_ruas`
        FOREIGN KEY (`ruas_id`)
        REFERENCES `ruas_jalan` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 3. Tabel: perkerasan
-- Menyimpan data jenis perkerasan per segmen (Rigid, Aspal, Agregat/Tanah, Belum Tembus)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `perkerasan` (
    `id`            INT UNSIGNED        NOT NULL AUTO_INCREMENT,
    `ruas_id`       INT UNSIGNED        NOT NULL,
    `tahun`         SMALLINT UNSIGNED   NULL COMMENT 'Tahun survei/data perkerasan jalan',
    `sta_awal`      DECIMAL(10,2)       NOT NULL DEFAULT 0.00 COMMENT 'STA Awal segmen dalam meter',
    `sta_akhir`     DECIMAL(10,2)       NOT NULL DEFAULT 0.00 COMMENT 'STA Akhir segmen dalam meter',
    `panjang`       DECIMAL(10,2)       NOT NULL DEFAULT 0.00 COMMENT 'Panjang segmen dalam meter',
    `rigid`         DECIMAL(10,2)       NOT NULL DEFAULT 0.00 COMMENT 'Panjang Rigid (meter)',
    `aspal`         DECIMAL(10,2)       NOT NULL DEFAULT 0.00 COMMENT 'Panjang Aspal (meter)',
    `agregat_tanah` DECIMAL(10,2)       NOT NULL DEFAULT 0.00 COMMENT 'Panjang Agregat / Tanah (meter)',
    `belum_tembus`  DECIMAL(10,2)       NOT NULL DEFAULT 0.00 COMMENT 'Panjang Belum Tembus (meter)',
    `created_at`    DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`    DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (`id`),
    KEY `idx_ruas_id` (`ruas_id`),
    KEY `idx_perkerasan_tahun` (`tahun`),

    CONSTRAINT `fk_perkerasan_ruas`
        FOREIGN KEY (`ruas_id`)
        REFERENCES `ruas_jalan` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 4. Tabel: foto_lapangan
-- Menyimpan data dokumentasi foto kondisi lapangan per STA
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `foto_lapangan` (
    `id`         INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `ruas_id`    INT UNSIGNED    NOT NULL,
    `sta_titik`  VARCHAR(20)     NOT NULL COMMENT 'Format tampilan STA, contoh: 0+100',
    `sta_meter`  INT             NOT NULL COMMENT 'Posisi meter untuk kalkulasi presisi',
    `file_name`  VARCHAR(255)    NOT NULL,
    `file_path`  VARCHAR(255)    NOT NULL,
    `file_size`  INT             DEFAULT 0,
    `keterangan` TEXT            NULL,
    `created_at` DATETIME        DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME        DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (`id`),
    KEY `idx_ruas_id` (`ruas_id`),
    KEY `idx_ruas_sta` (`ruas_id`, `sta_meter`),

    CONSTRAINT `fk_foto_lapangan_ruas`
        FOREIGN KEY (`ruas_id`)
        REFERENCES `ruas_jalan` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 5. Tabel: penanganan
-- Menyimpan usulan & paket pekerjaan penanganan jalan beserta hasil prediksi matriks
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `penanganan` (
    `id`               INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `ruas_id`          INT UNSIGNED    NOT NULL,
    `tahun`            INT             NOT NULL COMMENT 'Tahun pelaksanaan/anggaran, contoh: 2026, 2027',
    `sta_awal`         DECIMAL(10,2)   NOT NULL DEFAULT 0.00 COMMENT 'STA Awal segmen penanganan (meter)',
    `sta_akhir`        DECIMAL(10,2)   NOT NULL DEFAULT 0.00 COMMENT 'STA Akhir segmen penanganan (meter)',
    `panjang`          DECIMAL(10,2)   NOT NULL DEFAULT 0.00 COMMENT 'Panjang segmen penanganan (meter)',
    `jenis_penanganan` VARCHAR(100)    NOT NULL COMMENT 'Rekonstruksi, Rehabilitasi, Pemeliharaan Berkala, Pemeliharaan Rutin, dll',
    `jenis_pelaksana`  ENUM(
        'pihak_ke3_rigid',
        'pihak_ke3_aspal',
        'rutin_uptd',
        'urc_overlay_tanpa_finisher',
        'urc_overlay_dengan_finisher',
        'urc_rigid',
        'urc_base'
    ) NULL COMMENT 'Pelaksana/jenis teknis penanganan sesuai matriks strip map',
    `perkerasan_hasil` ENUM(
        'rigid',
        'aspal',
        'agregat_tanah',
        'belum_tembus'
    ) NULL COMMENT 'Prediksi jenis perkerasan hasil setelah penanganan (dihitung otomatis)',
    `kondisi_prediksi` ENUM(
        'baik',
        'sedang',
        'rusak_ringan',
        'rusak_berat'
    ) NULL COMMENT 'Prediksi kondisi jalan setelah penanganan (dihitung otomatis)',
    `perlu_verifikasi` TINYINT(1)      NOT NULL DEFAULT 0 COMMENT 'Flag: 1 = perlu verifikasi manual (kasus URC Base dll)',
    `status`           ENUM('rencana', 'proses', 'selesai') NOT NULL DEFAULT 'rencana' COMMENT 'Status penanganan',
    `nama_paket`       VARCHAR(255)    NULL COMMENT 'Nama paket pekerjaan / tender',
    `anggaran`         DECIMAL(15,2)   DEFAULT 0.00 COMMENT 'Nilai anggaran dalam Rupiah',
    `sumber_dana`      VARCHAR(100)    NULL COMMENT 'APBD, APBN, DAK, DBH, dll',
    `warna`            VARCHAR(20)     NULL COMMENT 'Warna penanda kustom (hex code) opsional',
    `keterangan`       TEXT            NULL,
    `created_at`       DATETIME        DEFAULT CURRENT_TIMESTAMP,
    `updated_at`       DATETIME        DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (`id`),
    KEY `idx_ruas_id` (`ruas_id`),
    KEY `idx_ruas_tahun` (`ruas_id`, `tahun`),

    CONSTRAINT `fk_penanganan_ruas`
        FOREIGN KEY (`ruas_id`)
        REFERENCES `ruas_jalan` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
