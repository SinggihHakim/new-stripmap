<?php

/**
 * ============================================================
 * Service: PenangananService
 * ============================================================
 * Menangani logika bisnis untuk data segmentasi penanganan jalan.
 */

class PenangananService
{
    private Penanganan $model;
    private RuasJalan  $ruasModel;
    private Stripmap   $stripmapModel;

    // Palette warna default berdasarkan status
    public const STATUS_COLORS = [
        'rencana' => '#0284c7', // Sky Blue
        'proses'  => '#6366f1', // Indigo
        'selesai' => '#10b981', // Emerald
    ];

    public const STATUS_LABELS = [
        'rencana' => 'Rencana',
        'proses'  => 'Sedang Dikerjakan',
        'selesai' => 'Selesai',
    ];

    public const STATUS_BADGES = [
        'rencana' => 'bg-sky-50 text-sky-700 border-sky-200',
        'proses'  => 'bg-indigo-50 text-indigo-700 border-indigo-200',
        'selesai' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
    ];

    // Palette warna tahun — sumber tunggal via TahunHelper::getWarna()
    // Tambahan warna 2024 untuk data historis di luar range utama
    public const YEAR_COLORS = [
        2024 => '#64748b', // slate-500 (historis)
    ];

    /**
     * Kembalikan warna untuk tahun tertentu (gabungan historis + TahunHelper)
     */
    public static function getYearColor(int $tahun): string
    {
        $historic = self::YEAR_COLORS;
        $main     = TahunHelper::getWarna();
        return $main[$tahun] ?? $historic[$tahun] ?? '#6b7280';
    }

    public function __construct()
    {
        $this->model         = new Penanganan();
        $this->ruasModel     = new RuasJalan();
        $this->stripmapModel = new Stripmap();
    }

    /**
     * Ambil data penanganan per ruas jalan
     */
    public function getByRuasId(int $ruasId, ?int $tahun = null): array
    {
        $rows = $this->model->getByRuasId($ruasId, $tahun);
        foreach ($rows as &$r) {
            $r['status_label'] = self::STATUS_LABELS[$r['status']] ?? ucfirst($r['status']);
            $r['status_badge'] = self::STATUS_BADGES[$r['status']] ?? 'bg-gray-100 text-gray-700 border-gray-200';
            $r['display_color'] = !empty($r['warna']) ? $r['warna'] : (self::STATUS_COLORS[$r['status']] ?? '#6366f1');
        }
        return $rows;
    }

    /**
     * Ambil data penanganan per ruas jalan hingga tahun tertentu (kumulatif)
     */
    public function getByRuasIdUpTo(int $ruasId, int $tahun): array
    {
        $rows = $this->model->getByRuasIdUpTo($ruasId, $tahun);
        foreach ($rows as &$r) {
            $r['status_label'] = self::STATUS_LABELS[$r['status']] ?? ucfirst($r['status']);
            $r['status_badge'] = self::STATUS_BADGES[$r['status']] ?? 'bg-gray-100 text-gray-700 border-gray-200';
            $r['display_color'] = !empty($r['warna']) ? $r['warna'] : (self::STATUS_COLORS[$r['status']] ?? '#6366f1');
        }
        return $rows;
    }

    /**
     * Ambil satu data penanganan berdasarkan ID
     */
    public function findById(int $id): ?array
    {
        $row = $this->model->findById($id);
        if ($row) {
            $row['status_label']  = self::STATUS_LABELS[$row['status']] ?? ucfirst($row['status']);
            $row['status_badge']  = self::STATUS_BADGES[$row['status']] ?? 'bg-gray-100 text-gray-700 border-gray-200';
            $row['display_color'] = !empty($row['warna']) ? $row['warna'] : (self::STATUS_COLORS[$row['status']] ?? '#6366f1');
        }
        return $row;
    }

    /**
     * Validasi dan simpan penanganan baru
     */
    public function create(array $data): array
    {
        $errors = $this->validate($data);
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        $id = $this->model->create($data);
        return ['success' => true, 'id' => $id];
    }

    /**
     * Validasi dan update penanganan
     */
    public function update(int $id, array $data): array
    {
        $errors = $this->validate($data, $id);
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        $success = $this->model->update($id, $data);
        return ['success' => $success, 'errors' => []];
    }

    /**
     * Hapus penanganan
     */
    public function delete(int $id): bool
    {
        return $this->model->delete($id);
    }

    /**
     * Ambil daftar tahun unik
     */
    public function getAvailableYears(?int $ruasId = null): array
    {
        return $this->model->getAvailableYears($ruasId);
    }

    /**
     * Ambil ringkasan penanganan untuk ruas tertentu
     */
    public function getSummary(int $ruasId, ?int $tahun = null): array
    {
        return $this->model->getSummary($ruasId, $tahun);
    }

    /**
     * Ambil ringkasan global
     */
    public function getGlobalSummary(?int $tahun = null): array
    {
        return $this->model->getGlobalSummary($tahun);
    }

    /**
     * Ambil ringkasan per Kabupaten
     */
    public function getSummaryByKabupaten(?int $tahun = null): array
    {
        return $this->model->getSummaryByKabupaten($tahun);
    }

    /**
     * Terapkan hasil penanganan "Selesai" ke kondisi Stripmap menjadi "Baik"
     */
    public function applyKondisiToStripmap(int $penangananId): array
    {
        $penanganan = $this->findById($penangananId);
        if (!$penanganan) {
            return ['success' => false, 'message' => 'Data penanganan tidak ditemukan.'];
        }

        if ($penanganan['status'] !== 'selesai') {
            return ['success' => false, 'message' => 'Hanya penanganan dengan status "Selesai" yang dapat diterapkan ke kondisi jalan.'];
        }

        $ruasId = (int) $penanganan['ruas_id'];
        $pAwal  = (float) $penanganan['sta_awal'];
        $pAkhir = (float) $penanganan['sta_akhir'];

        $stripmaps = $this->stripmapModel->getByRuasId($ruasId);
        if (empty($stripmaps)) {
            return ['success' => false, 'message' => 'Belum ada data strip map untuk ruas ini.'];
        }

        $updatedCount = 0;
        foreach ($stripmaps as $sm) {
            $smAwal  = (float) $sm['sta_awal'];
            $smAkhir = (float) $sm['sta_akhir'];

            // Cek apakah ada overlap antara segmen stripmap dan penanganan
            $overlapStart = max($pAwal, $smAwal);
            $overlapEnd   = min($pAkhir, $smAkhir);

            if ($overlapEnd > $overlapStart) {
                $overlapLen = $overlapEnd - $overlapStart;
                $smPanjang  = (float) $sm['panjang'];

                // Jika penanganan meng-cover seluruh segmen ini
                if ($pAwal <= $smAwal && $pAkhir >= $smAkhir) {
                    $this->stripmapModel->update((int)$sm['id'], [
                        'sta_awal'     => $sm['sta_awal'],
                        'sta_akhir'    => $sm['sta_akhir'],
                        'panjang'      => $sm['panjang'],
                        'baik'         => $smPanjang,
                        'sedang'       => 0.0,
                        'rusak_ringan' => 0.0,
                        'rusak_berat'  => 0.0,
                    ]);
                } else {
                    // Penanganan sebagian: kurangi kondisi rusak dari berat ke ringan lalu sedang
                    $rem = $overlapLen;
                    $rb  = (float) $sm['rusak_berat'];
                    $rr  = (float) $sm['rusak_ringan'];
                    $sd  = (float) $sm['sedang'];
                    $b   = (float) $sm['baik'];

                    if ($rem > 0 && $rb > 0) {
                        $deduct = min($rem, $rb);
                        $rb -= $deduct;
                        $b  += $deduct;
                        $rem -= $deduct;
                    }
                    if ($rem > 0 && $rr > 0) {
                        $deduct = min($rem, $rr);
                        $rr -= $deduct;
                        $b  += $deduct;
                        $rem -= $deduct;
                    }
                    if ($rem > 0 && $sd > 0) {
                        $deduct = min($rem, $sd);
                        $sd -= $deduct;
                        $b  += $deduct;
                        $rem -= $deduct;
                    }

                    $this->stripmapModel->update((int)$sm['id'], [
                        'sta_awal'     => $sm['sta_awal'],
                        'sta_akhir'    => $sm['sta_akhir'],
                        'panjang'      => $sm['panjang'],
                        'baik'         => min($smPanjang, $b),
                        'sedang'       => max(0.0, $sd),
                        'rusak_ringan' => max(0.0, $rr),
                        'rusak_berat'  => max(0.0, $rb),
                    ]);
                }
                $updatedCount++;
            }
        }

        return [
            'success' => true,
            'message' => "Kondisi jalan pada {$updatedCount} segmen strip map berhasil diperbarui menjadi kondisi Baik."
        ];
    }

    /**
     * Validasi data input penanganan
     */
    private function validate(array &$data, ?int $id = null): array
    {
        $errors = [];

        // Validasi ruas_id
        $ruas = $this->ruasModel->findById((int) ($data['ruas_id'] ?? 0));
        if (!$ruas) {
            $errors['ruas_id'] = 'Ruas jalan tidak valid.';
            return $errors;
        }

        // Validasi tahun
        $tahun = (int) ($data['tahun'] ?? 0);
        if ($tahun < 2000 || $tahun > 2100) {
            $errors['tahun'] = 'Tahun penanganan harus valid (contoh: 2026, 2027).';
        }

        // Parse STA
        $staAwal = is_numeric($data['sta_awal'] ?? null)
            ? (float) $data['sta_awal']
            : (isset($data['sta_awal']) ? (float) sta_to_meter($data['sta_awal']) : 0.0);

        $staAkhir = is_numeric($data['sta_akhir'] ?? null)
            ? (float) $data['sta_akhir']
            : (isset($data['sta_akhir']) ? (float) sta_to_meter($data['sta_akhir']) : 0.0);

        if ($staAwal < 0) {
            $errors['sta_awal'] = 'STA Awal tidak boleh negatif.';
        }
        if ($staAkhir <= $staAwal) {
            $errors['sta_akhir'] = 'STA Akhir harus lebih besar dari STA Awal.';
        }

        $panjangRuas = (float) $ruas['panjang'];
        if ($panjangRuas > 0 && $staAkhir > $panjangRuas + 50) { // toleransi 50m
            $errors['sta_akhir'] = 'STA Akhir (' . format_number($staAkhir) . ' m) melebihi panjang ruas jalan (' . format_number($panjangRuas) . ' m).';
        }

        $panjang = $staAkhir - $staAwal;
        $data['sta_awal']  = $staAwal;
        $data['sta_akhir'] = $staAkhir;
        $data['panjang']   = $panjang;

        // Validasi jenis penanganan
        if (empty($data['jenis_penanganan'])) {
            $errors['jenis_penanganan'] = 'Jenis penanganan wajib diisi.';
        }

        // Validasi status
        $allowedStatus = ['rencana', 'proses', 'selesai'];
        if (!in_array($data['status'] ?? 'rencana', $allowedStatus, true)) {
            $data['status'] = 'rencana';
        }

        // Sanitasi anggaran
        $anggaran = $data['anggaran'] ?? 0;
        if (is_string($anggaran)) {
            $anggaran = preg_replace('/[^0-9.]/', '', str_replace(',', '.', $anggaran));
        }
        $data['anggaran'] = (float) $anggaran;

        return $errors;
    }
}
