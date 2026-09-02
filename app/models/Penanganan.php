<?php

/**
 * ============================================================
 * Model: Penanganan
 * ============================================================
 * Mengelola query ke tabel `penanganan` (Segmentasi Penanganan Jalan).
 * Status: Rencana, Proses (Sedang Dikerjakan), Selesai
 */

class Penanganan
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
        self::autoCreateTable();
    }

    /**
     * Buat tabel penanganan jika belum ada secara otomatis.
     */
    public static function autoCreateTable(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS `penanganan` (
            `id`               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `ruas_id`          INT UNSIGNED NOT NULL,
            `tahun`            INT NOT NULL COMMENT 'Tahun pelaksanaan/anggaran, contoh: 2026, 2027',
            `sta_awal`         DECIMAL(10,2) NOT NULL DEFAULT 0.00 COMMENT 'STA Awal segmen penanganan (meter)',
            `sta_akhir`        DECIMAL(10,2) NOT NULL DEFAULT 0.00 COMMENT 'STA Akhir segmen penanganan (meter)',
            `panjang`          DECIMAL(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Panjang segmen penanganan (meter)',
            `jenis_penanganan` VARCHAR(100) NOT NULL COMMENT 'Rekonstruksi, Rehabilitasi, Pemeliharaan Berkala, Pemeliharaan Rutin, dll',
            `jenis_pelaksana`  ENUM('pihak_ke3_rigid','pihak_ke3_aspal','rutin_uptd','urc_overlay_tanpa_finisher','urc_overlay_dengan_finisher','urc_rigid','urc_base') NULL COMMENT 'Pelaksana/jenis teknis penanganan sesuai matriks strip map',
            `perkerasan_hasil` ENUM('rigid','aspal','agregat_tanah','belum_tembus') NULL COMMENT 'Prediksi jenis perkerasan hasil setelah penanganan',
            `kondisi_prediksi` ENUM('baik','sedang','rusak_ringan','rusak_berat') NULL COMMENT 'Prediksi kondisi jalan setelah penanganan',
            `perlu_verifikasi` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Flag: 1 = perlu verifikasi manual',
            `status`           ENUM('rencana', 'proses', 'selesai') NOT NULL DEFAULT 'rencana' COMMENT 'Status penanganan',
            `nama_paket`       VARCHAR(255) NULL COMMENT 'Nama paket pekerjaan / tender',
            `anggaran`         DECIMAL(15,2) NULL DEFAULT 0.00 COMMENT 'Nilai anggaran dalam Rupiah',
            `sumber_dana`      VARCHAR(100) NULL COMMENT 'APBD, APBN, DAK, DBH, dll',
            `warna`            VARCHAR(20) NULL COMMENT 'Warna penanda kustom (hex code) opsional',
            `keterangan`       TEXT NULL,
            `created_at`       DATETIME DEFAULT CURRENT_TIMESTAMP,
            `updated_at`       DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            KEY `idx_ruas_id` (`ruas_id`),
            KEY `idx_ruas_tahun` (`ruas_id`, `tahun`),
            CONSTRAINT `fk_penanganan_ruas`
                FOREIGN KEY (`ruas_id`)
                REFERENCES `ruas_jalan` (`id`)
                ON DELETE CASCADE
                ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

        // Migrasi otomatis: tambah kolom baru jika tabel sudah ada
        $migrations = [
            "ALTER TABLE `penanganan` ADD COLUMN IF NOT EXISTS `jenis_pelaksana` ENUM('pihak_ke3_rigid','pihak_ke3_aspal','rutin_uptd','urc_overlay_tanpa_finisher','urc_overlay_dengan_finisher','urc_rigid','urc_base') NULL AFTER `jenis_penanganan`",
            "ALTER TABLE `penanganan` ADD COLUMN IF NOT EXISTS `perkerasan_hasil` ENUM('rigid','aspal','agregat_tanah','belum_tembus') NULL AFTER `jenis_pelaksana`",
            "ALTER TABLE `penanganan` ADD COLUMN IF NOT EXISTS `kondisi_prediksi` ENUM('baik','sedang','rusak_ringan','rusak_berat') NULL AFTER `perkerasan_hasil`",
            "ALTER TABLE `penanganan` ADD COLUMN IF NOT EXISTS `perlu_verifikasi` TINYINT(1) NOT NULL DEFAULT 0 AFTER `kondisi_prediksi`",
        ];

        try {
            $db = Database::getInstance()->getConnection();
            $db->exec($sql);
            foreach ($migrations as $migSql) {
                try { $db->exec($migSql); } catch (\PDOException $me) { /* kolom sudah ada */ }
            }
        } catch (\PDOException $e) {
            error_log('[Penanganan::autoCreateTable] ' . $e->getMessage());
        }
    }

    /**
     * Ambil semua data penanganan berdasarkan ruas_id dan filter tahun opsional
     */
    public function getByRuasId(int $ruasId, ?int $tahun = null): array
    {
        if ($tahun !== null && $tahun > 0) {
            $stmt = $this->db->prepare(
                'SELECT * FROM penanganan WHERE ruas_id = :ruas_id AND tahun = :tahun ORDER BY tahun DESC, sta_awal ASC'
            );
            $stmt->execute(['ruas_id' => $ruasId, 'tahun' => $tahun]);
        } else {
            $stmt = $this->db->prepare(
                'SELECT * FROM penanganan WHERE ruas_id = :ruas_id ORDER BY tahun DESC, sta_awal ASC'
            );
            $stmt->execute(['ruas_id' => $ruasId]);
        }
        return $stmt->fetchAll();
    }

    /**
     * Ambil semua data penanganan hingga tahun tertentu (kumulatif: tahun <= $tahun)
     */
    public function getByRuasIdUpTo(int $ruasId, int $tahun): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM penanganan WHERE ruas_id = :ruas_id AND tahun <= :tahun ORDER BY tahun ASC, sta_awal ASC'
        );
        $stmt->execute(['ruas_id' => $ruasId, 'tahun' => $tahun]);
        return $stmt->fetchAll();
    }

    /**
     * Ambil satu data penanganan berdasarkan ID
     */
    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM penanganan WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Simpan penanganan baru
     */
    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO penanganan (ruas_id, tahun, sta_awal, sta_akhir, panjang, jenis_penanganan, jenis_pelaksana, perkerasan_hasil, kondisi_prediksi, perlu_verifikasi, status, nama_paket, anggaran, sumber_dana, warna, keterangan)
             VALUES (:ruas_id, :tahun, :sta_awal, :sta_akhir, :panjang, :jenis_penanganan, :jenis_pelaksana, :perkerasan_hasil, :kondisi_prediksi, :perlu_verifikasi, :status, :nama_paket, :anggaran, :sumber_dana, :warna, :keterangan)'
        );
        $stmt->execute([
            'ruas_id'          => $data['ruas_id'],
            'tahun'            => (int) $data['tahun'],
            'sta_awal'         => $data['sta_awal'],
            'sta_akhir'        => $data['sta_akhir'],
            'panjang'          => $data['panjang'],
            'jenis_penanganan' => $data['jenis_penanganan'],
            'jenis_pelaksana'  => $data['jenis_pelaksana'] ?? null,
            'perkerasan_hasil' => $data['perkerasan_hasil'] ?? null,
            'kondisi_prediksi' => $data['kondisi_prediksi'] ?? null,
            'perlu_verifikasi' => (int)($data['perlu_verifikasi'] ?? 0),
            'status'           => $data['status'] ?? 'rencana',
            'nama_paket'       => $data['nama_paket'] ?? null,
            'anggaran'         => $data['anggaran'] ?? 0.00,
            'sumber_dana'      => $data['sumber_dana'] ?? null,
            'warna'            => $data['warna'] ?? null,
            'keterangan'       => $data['keterangan'] ?? null,
        ]);
        return (int) $this->db->lastInsertId();
    }

    /**
     * Update data penanganan
     */
    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE penanganan
             SET tahun = :tahun,
                 sta_awal = :sta_awal,
                 sta_akhir = :sta_akhir,
                 panjang = :panjang,
                 jenis_penanganan = :jenis_penanganan,
                 jenis_pelaksana = :jenis_pelaksana,
                 perkerasan_hasil = :perkerasan_hasil,
                 kondisi_prediksi = :kondisi_prediksi,
                 perlu_verifikasi = :perlu_verifikasi,
                 status = :status,
                 nama_paket = :nama_paket,
                 anggaran = :anggaran,
                 sumber_dana = :sumber_dana,
                 warna = :warna,
                 keterangan = :keterangan
             WHERE id = :id'
        );
        return $stmt->execute([
            'id'               => $id,
            'tahun'            => (int) $data['tahun'],
            'sta_awal'         => $data['sta_awal'],
            'sta_akhir'        => $data['sta_akhir'],
            'panjang'          => $data['panjang'],
            'jenis_penanganan' => $data['jenis_penanganan'],
            'jenis_pelaksana'  => $data['jenis_pelaksana'] ?? null,
            'perkerasan_hasil' => $data['perkerasan_hasil'] ?? null,
            'kondisi_prediksi' => $data['kondisi_prediksi'] ?? null,
            'perlu_verifikasi' => (int)($data['perlu_verifikasi'] ?? 0),
            'status'           => $data['status'] ?? 'rencana',
            'nama_paket'       => $data['nama_paket'] ?? null,
            'anggaran'         => $data['anggaran'] ?? 0.00,
            'sumber_dana'      => $data['sumber_dana'] ?? null,
            'warna'            => $data['warna'] ?? null,
            'keterangan'       => $data['keterangan'] ?? null,
        ]);
    }

    /**
     * Hapus data penanganan
     */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM penanganan WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Ambil daftar tahun yang tersedia (distinct)
     */
    public function getAvailableYears(?int $ruasId = null): array
    {
        if ($ruasId !== null) {
            $stmt = $this->db->prepare('SELECT DISTINCT tahun FROM penanganan WHERE ruas_id = :ruas_id ORDER BY tahun DESC');
            $stmt->execute(['ruas_id' => $ruasId]);
        } else {
            $stmt = $this->db->query('SELECT DISTINCT tahun FROM penanganan ORDER BY tahun DESC');
        }
        $rows = $stmt->fetchAll(PDO::FETCH_COLUMN);
        return array_map('intval', $rows ?: []);
    }

    /**
     * Hitung total ringkasan penanganan untuk sebuah ruas
     */
    public function getSummary(int $ruasId, ?int $tahun = null): array
    {
        $sql = 'SELECT
                    COUNT(id) as total_segmen,
                    COALESCE(SUM(panjang), 0) as total_panjang,
                    COALESCE(SUM(CASE WHEN status = "rencana" THEN panjang ELSE 0 END), 0) as total_rencana,
                    COALESCE(SUM(CASE WHEN status = "proses"  THEN panjang ELSE 0 END), 0) as total_proses,
                    COALESCE(SUM(CASE WHEN status = "selesai" THEN panjang ELSE 0 END), 0) as total_selesai,
                    COALESCE(SUM(anggaran), 0) as total_anggaran
                FROM penanganan
                WHERE ruas_id = :ruas_id';

        $params = ['ruas_id' => $ruasId];
        if ($tahun !== null && $tahun > 0) {
            $sql .= ' AND tahun = :tahun';
            $params['tahun'] = $tahun;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch() ?: [
            'total_segmen'   => 0,
            'total_panjang'  => 0,
            'total_rencana'  => 0,
            'total_proses'   => 0,
            'total_selesai'  => 0,
            'total_anggaran' => 0,
        ];
    }

    /**
     * Hitung ringkasan global penanganan seluruh ruas jalan
     */
    public function getGlobalSummary(?int $tahun = null): array
    {
        $sql = 'SELECT
                    COUNT(id) as total_segmen,
                    COALESCE(SUM(panjang), 0) as total_panjang,
                    COALESCE(SUM(CASE WHEN status = "rencana" THEN panjang ELSE 0 END), 0) as total_rencana,
                    COALESCE(SUM(CASE WHEN status = "proses"  THEN panjang ELSE 0 END), 0) as total_proses,
                    COALESCE(SUM(CASE WHEN status = "selesai" THEN panjang ELSE 0 END), 0) as total_selesai,
                    COALESCE(SUM(anggaran), 0) as total_anggaran
                FROM penanganan';

        $params = [];
        if ($tahun !== null && $tahun > 0) {
            $sql .= ' WHERE tahun = :tahun';
            $params['tahun'] = $tahun;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch() ?: [
            'total_segmen'   => 0,
            'total_panjang'  => 0,
            'total_rencana'  => 0,
            'total_proses'   => 0,
            'total_selesai'  => 0,
            'total_anggaran' => 0,
        ];
    }

    /**
     * Ringkasan penanganan per Kabupaten / Kota
     */
    public function getSummaryByKabupaten(?int $tahun = null): array
    {
        $sql = 'SELECT
                    r.kabupaten_kota,
                    COUNT(p.id) as total_segmen,
                    COALESCE(SUM(p.panjang), 0) as total_panjang,
                    COALESCE(SUM(CASE WHEN p.status = "rencana" THEN p.panjang ELSE 0 END), 0) as total_rencana,
                    COALESCE(SUM(CASE WHEN p.status = "proses"  THEN p.panjang ELSE 0 END), 0) as total_proses,
                    COALESCE(SUM(CASE WHEN p.status = "selesai" THEN p.panjang ELSE 0 END), 0) as total_selesai,
                    COALESCE(SUM(p.anggaran), 0) as total_anggaran
                FROM ruas_jalan r
                LEFT JOIN penanganan p ON p.ruas_id = r.id ' . ($tahun ? 'AND p.tahun = :tahun ' : '') . '
                WHERE r.kabupaten_kota IS NOT NULL AND r.kabupaten_kota != ""
                GROUP BY r.kabupaten_kota
                ORDER BY total_panjang DESC';

        $stmt = $this->db->prepare($sql);
        $params = $tahun ? ['tahun' => $tahun] : [];
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
