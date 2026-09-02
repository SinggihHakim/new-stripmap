<?php

/**
 * ============================================================
 * Model: Perkerasan
 * ============================================================
 * Mengelola query ke tabel `perkerasan` (Jenis Perkerasan Jalan).
 * Rigid, Aspal, Agregat / Tanah, Belum Tembus
 */

class Perkerasan
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Buat tabel perkerasan jika belum ada.
     * Dipanggil sekali dari migration/installer — BUKAN dari constructor.
     */
    public static function autoCreateTable(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS `perkerasan` (
            `id`            INT UNSIGNED    NOT NULL AUTO_INCREMENT,
            `ruas_id`       INT UNSIGNED    NOT NULL,
            `sta_awal`      DECIMAL(10,2)   NOT NULL DEFAULT 0.00 COMMENT 'STA Awal segmen dalam meter',
            `sta_akhir`     DECIMAL(10,2)   NOT NULL DEFAULT 0.00 COMMENT 'STA Akhir segmen dalam meter',
            `panjang`       DECIMAL(10,2)   NOT NULL DEFAULT 0.00 COMMENT 'Panjang segmen dalam meter',
            `rigid`         DECIMAL(10,2)   NOT NULL DEFAULT 0.00 COMMENT 'Panjang Rigid (meter)',
            `aspal`         DECIMAL(10,2)   NOT NULL DEFAULT 0.00 COMMENT 'Panjang Aspal (meter)',
            `agregat_tanah` DECIMAL(10,2)   NOT NULL DEFAULT 0.00 COMMENT 'Panjang Agregat / Tanah (meter)',
            `belum_tembus`  DECIMAL(10,2)   NOT NULL DEFAULT 0.00 COMMENT 'Panjang Belum Tembus (meter)',
            `created_at`    DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at`    DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

            PRIMARY KEY (`id`),
            KEY `idx_ruas_id` (`ruas_id`),

            CONSTRAINT `fk_perkerasan_ruas`
                FOREIGN KEY (`ruas_id`)
                REFERENCES `ruas_jalan` (`id`)
                ON DELETE CASCADE
                ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

        $db = Database::getInstance()->getConnection();
        try {
            $db->exec($sql);
        } catch (\PDOException $e) {
            // CREATE TABLE IF NOT EXISTS tidak throw jika sudah ada,
            // jadi log error lain yang mungkin serius.
            error_log('[Perkerasan::autoCreateTable] ' . $e->getMessage());
        }
    }

    /**
     * Ambil semua data perkerasan berdasarkan ruas_id
     */
    public function getByRuasId(int $ruasId): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM perkerasan WHERE ruas_id = :ruas_id ORDER BY sta_awal ASC'
        );
        $stmt->execute(['ruas_id' => $ruasId]);
        return $stmt->fetchAll();
    }

    /**
     * Ambil satu data perkerasan berdasarkan ID
     */
    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM perkerasan WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Simpan perkerasan baru
     */
    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO perkerasan (ruas_id, sta_awal, sta_akhir, panjang, rigid, aspal, agregat_tanah, belum_tembus)
             VALUES (:ruas_id, :sta_awal, :sta_akhir, :panjang, :rigid, :aspal, :agregat_tanah, :belum_tembus)'
        );
        $stmt->execute([
            'ruas_id'       => $data['ruas_id'],
            'sta_awal'      => $data['sta_awal'],
            'sta_akhir'     => $data['sta_akhir'],
            'panjang'       => $data['panjang'],
            'rigid'         => $data['rigid'],
            'aspal'         => $data['aspal'],
            'agregat_tanah' => $data['agregat_tanah'],
            'belum_tembus'  => $data['belum_tembus'],
        ]);
        return (int) $this->db->lastInsertId();
    }

    /**
     * Update perkerasan berdasarkan ID
     */
    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE perkerasan
             SET sta_awal      = :sta_awal,
                 sta_akhir     = :sta_akhir,
                 panjang       = :panjang,
                 rigid         = :rigid,
                 aspal         = :aspal,
                 agregat_tanah = :agregat_tanah,
                 belum_tembus  = :belum_tembus
             WHERE id = :id'
        );
        return $stmt->execute([
            'sta_awal'      => $data['sta_awal'],
            'sta_akhir'     => $data['sta_akhir'],
            'panjang'       => $data['panjang'],
            'rigid'         => $data['rigid'],
            'aspal'         => $data['aspal'],
            'agregat_tanah' => $data['agregat_tanah'],
            'belum_tembus'  => $data['belum_tembus'],
            'id'            => $id,
        ]);
    }

    /**
     * Hapus perkerasan berdasarkan ID
     */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM perkerasan WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Hapus semua perkerasan berdasarkan ruas_id
     */
    public function deleteByRuasId(int $ruasId): bool
    {
        $stmt = $this->db->prepare('DELETE FROM perkerasan WHERE ruas_id = :ruas_id');
        return $stmt->execute(['ruas_id' => $ruasId]);
    }

    /**
     * Hitung total segmen perkerasan untuk sebuah ruas
     */
    public function countByRuasId(int $ruasId): int
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) as total FROM perkerasan WHERE ruas_id = :ruas_id');
        $stmt->execute(['ruas_id' => $ruasId]);
        return (int) $stmt->fetch()['total'];
    }

    /**
     * Ambil ringkasan perkerasan untuk sebuah ruas (total per jenis perkerasan)
     */
    public function getSummaryByRuasId(int $ruasId): array
    {
        $stmt = $this->db->prepare(
            'SELECT
                SUM(panjang)       as total_panjang,
                SUM(rigid)         as total_rigid,
                SUM(aspal)         as total_aspal,
                SUM(agregat_tanah) as total_agregat_tanah,
                SUM(belum_tembus)  as total_belum_tembus
             FROM perkerasan
             WHERE ruas_id = :ruas_id'
        );
        $stmt->execute(['ruas_id' => $ruasId]);
        return $stmt->fetch() ?: [
            'total_panjang'       => 0,
            'total_rigid'         => 0,
            'total_aspal'         => 0,
            'total_agregat_tanah' => 0,
            'total_belum_tembus'  => 0,
        ];
    }

    /**
     * Ambil ringkasan perkerasan global seluruh ruas jalan
     */
    public function getGlobalSummary(?array $ruasIds = null, ?int $tahun = null): array
    {
        if ($ruasIds !== null && empty($ruasIds)) {
            return [
                'total_panjang'       => 0,
                'total_rigid'         => 0,
                'total_aspal'         => 0,
                'total_agregat_tanah' => 0,
                'total_belum_tembus'  => 0,
            ];
        }

        $conditions = [];
        if ($ruasIds !== null) {
            $inQuery = implode(',', array_map('intval', $ruasIds));
            $conditions[] = "ruas_id IN ($inQuery)";
        }
        if ($tahun !== null) {
            // Cek apakah data survei terpisah untuk tahun tersebut ada di DB
            $checkStmt = $this->db->prepare('SELECT 1 FROM perkerasan WHERE tahun = :tahun LIMIT 1');
            $checkStmt->execute(['tahun' => $tahun]);
            $effectiveTahun = $checkStmt->fetch() ? (int)$tahun : 2025;
            $conditions[] = "tahun = " . $effectiveTahun;
        }
        $whereClause = $conditions ? ' WHERE ' . implode(' AND ', $conditions) : '';

        $stmt = $this->db->query(
            "SELECT
                SUM(panjang)       as total_panjang,
                SUM(rigid)         as total_rigid,
                SUM(aspal)         as total_aspal,
                SUM(agregat_tanah) as total_agregat_tanah,
                SUM(belum_tembus)  as total_belum_tembus
             FROM perkerasan" . $whereClause
        );
        return $stmt->fetch() ?: [
            'total_panjang'       => 0,
            'total_rigid'         => 0,
            'total_aspal'         => 0,
            'total_agregat_tanah' => 0,
            'total_belum_tembus'  => 0,
        ];
    }

    /**
     * Ambil daftar tahun yang tersedia di tabel perkerasan
     */
    public function getAvailableYears(): array
    {
        $stmt = $this->db->query(
            'SELECT DISTINCT tahun FROM perkerasan WHERE tahun IS NOT NULL ORDER BY tahun DESC'
        );
        return array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'tahun');
    }
}
