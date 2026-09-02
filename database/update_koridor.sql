-- ============================================================
-- SQL Update Koridor & Kabupaten Ruas Jalan Provinsi Lampung
-- Berdasarkan Data Resmi UPTD Dinas BMBK Lampung
-- Update: 2026-09-01
-- Format kode_ruas: 3 digit dengan leading zero, titik (bukan spasi)
-- Contoh: 001.11K, 041.12K, 002
-- ============================================================

USE `stripmap_db`;

-- KORIDOR I (UPTD I - Pringsewu, Pesawaran)
UPDATE `ruas_jalan` SET `koridor` = 'KORIDOR I', `kabupaten_kota` = 'Pringsewu' WHERE `kode_ruas` IN ('033','034','035');
UPDATE `ruas_jalan` SET `koridor` = 'KORIDOR I', `kabupaten_kota` = 'Pesawaran' WHERE `kode_ruas` IN ('037','038','039');

-- KORIDOR II (UPTD I - Bandar Lampung, Pesawaran)
UPDATE `ruas_jalan` SET `koridor` = 'KORIDOR II', `kabupaten_kota` = 'Bandar Lampung' WHERE `kode_ruas` IN ('012.11K','041.11K','041.12K');
UPDATE `ruas_jalan` SET `koridor` = 'KORIDOR II', `kabupaten_kota` = 'Pesawaran' WHERE `kode_ruas` IN ('042','043','040');

-- KORIDOR III (UPTD II - Lampung Selatan, Lampung Timur)
UPDATE `ruas_jalan` SET `koridor` = 'KORIDOR III', `kabupaten_kota` = 'Lampung Selatan' WHERE `kode_ruas` IN ('001.11K','002','003','004');
UPDATE `ruas_jalan` SET `koridor` = 'KORIDOR III', `kabupaten_kota` = 'Lampung Timur' WHERE `kode_ruas` IN ('005','006','011');

-- KORIDOR IV (UPTD II - Lampung Timur, Lampung Selatan)
UPDATE `ruas_jalan` SET `koridor` = 'KORIDOR IV', `kabupaten_kota` = 'Lampung Timur' WHERE `kode_ruas` IN ('008','009','010','014','024');
UPDATE `ruas_jalan` SET `koridor` = 'KORIDOR IV', `kabupaten_kota` = 'Lampung Selatan' WHERE `kode_ruas` IN ('013','016');

-- KORIDOR V (UPTD III - Lampung Tengah)
UPDATE `ruas_jalan` SET `koridor` = 'KORIDOR V', `kabupaten_kota` = 'Lampung Tengah' WHERE `kode_ruas` IN ('019','020','021','022');

-- KORIDOR VI (UPTD III - Metro, Lampung Tengah)
UPDATE `ruas_jalan` SET `koridor` = 'KORIDOR VI', `kabupaten_kota` = 'Metro' WHERE `kode_ruas` IN ('007.11K','015.11K','015.12K','017.11K','017.12K','028.11K');
UPDATE `ruas_jalan` SET `koridor` = 'KORIDOR VI', `kabupaten_kota` = 'Lampung Tengah' WHERE `kode_ruas` IN ('018','025','026','027');

-- KORIDOR VII (UPTD III - Lampung Tengah)
UPDATE `ruas_jalan` SET `koridor` = 'KORIDOR VII', `kabupaten_kota` = 'Lampung Tengah' WHERE `kode_ruas` IN ('023','029','030','032');

-- KORIDOR VIII (UPTD IV - Lampung Utara)
UPDATE `ruas_jalan` SET `koridor` = 'KORIDOR VIII', `kabupaten_kota` = 'Lampung Utara' WHERE `kode_ruas` IN ('031','061','062','063','066','067');

-- KORIDOR IX (UPTD IV - Lampung Utara, Way Kanan)
UPDATE `ruas_jalan` SET `koridor` = 'KORIDOR IX', `kabupaten_kota` = 'Lampung Utara' WHERE `kode_ruas` IN ('070','071','072');
UPDATE `ruas_jalan` SET `koridor` = 'KORIDOR IX', `kabupaten_kota` = 'Way Kanan' WHERE `kode_ruas` IN ('073','074','075');

-- KORIDOR X (UPTD IV - Way Kanan)
UPDATE `ruas_jalan` SET `koridor` = 'KORIDOR X', `kabupaten_kota` = 'Way Kanan' WHERE `kode_ruas` IN ('076','077','078','079','080');

-- KORIDOR XI (UPTD IV - Lampung Utara, Way Kanan)
UPDATE `ruas_jalan` SET `koridor` = 'KORIDOR XI', `kabupaten_kota` = 'Lampung Utara' WHERE `kode_ruas` IN ('081');
UPDATE `ruas_jalan` SET `koridor` = 'KORIDOR XI', `kabupaten_kota` = 'Way Kanan' WHERE `kode_ruas` IN ('082','083','084','088','089');

-- KORIDOR XII (UPTD V - Tanggamus)
UPDATE `ruas_jalan` SET `koridor` = 'KORIDOR XII', `kabupaten_kota` = 'Tanggamus' WHERE `kode_ruas` IN ('036','044','045','046','047');

-- KORIDOR XIII (UPTD V - Lampung Barat, Tanggamus, Pesisir Barat)
UPDATE `ruas_jalan` SET `koridor` = 'KORIDOR XIII', `kabupaten_kota` = 'Lampung Barat' WHERE `kode_ruas` IN ('048','049','051.11K','052');
UPDATE `ruas_jalan` SET `koridor` = 'KORIDOR XIII', `kabupaten_kota` = 'Tanggamus' WHERE `kode_ruas` IN ('050');
UPDATE `ruas_jalan` SET `koridor` = 'KORIDOR XIII', `kabupaten_kota` = 'Pesisir Barat' WHERE `kode_ruas` IN ('053.11K','054','055');

-- KORIDOR XIV (UPTD V - Tanggamus, Lampung Barat)
UPDATE `ruas_jalan` SET `koridor` = 'KORIDOR XIV', `kabupaten_kota` = 'Tanggamus' WHERE `kode_ruas` IN ('056','057','058','060');
UPDATE `ruas_jalan` SET `koridor` = 'KORIDOR XIV', `kabupaten_kota` = 'Lampung Barat' WHERE `kode_ruas` IN ('059');

-- KORIDOR XV (UPTD VI - Tulang Bawang Barat, Tulang Bawang)
UPDATE `ruas_jalan` SET `koridor` = 'KORIDOR XV', `kabupaten_kota` = 'Tulang Bawang Barat' WHERE `kode_ruas` IN ('064','065','068','069','087','091');
UPDATE `ruas_jalan` SET `koridor` = 'KORIDOR XV', `kabupaten_kota` = 'Tulang Bawang' WHERE `kode_ruas` IN ('085.11K','086');

-- KORIDOR XVI (UPTD VI - Tulang Bawang, Mesuji, Tulang Bawang Barat)
UPDATE `ruas_jalan` SET `koridor` = 'KORIDOR XVI', `kabupaten_kota` = 'Tulang Bawang' WHERE `kode_ruas` IN ('092','093');
UPDATE `ruas_jalan` SET `koridor` = 'KORIDOR XVI', `kabupaten_kota` = 'Mesuji' WHERE `kode_ruas` IN ('094','095');
UPDATE `ruas_jalan` SET `koridor` = 'KORIDOR XVI', `kabupaten_kota` = 'Tulang Bawang Barat' WHERE `kode_ruas` IN ('090');
