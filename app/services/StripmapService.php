<?php

/**
 * ============================================================
 * Service: StripmapService
 * ============================================================
 * Business logic untuk Strip Map.
 */

class StripmapService
{
    private Stripmap $model;

    public function __construct()
    {
        $this->model = new Stripmap();
    }

    /**
     * Ambil semua stripmap berdasarkan ruas ID
     */
    public function getByRuasId(int $ruasId): array
    {
        return $this->model->getByRuasId($ruasId);
    }

    /**
     * Ambil satu stripmap
     */
    public function findById(int $id): ?array
    {
        return $this->model->findById($id);
    }

    /**
     * Ambil ringkasan kondisi
     */
    public function getSummary(int $ruasId): array
    {
        return $this->model->getSummaryByRuasId($ruasId);
    }

    /**
     * Validasi & simpan stripmap baru
     */
    public function create(int $ruasId, array $input): array
    {
        $errors = $this->validate($input, $ruasId);
        if (!empty($errors)) {
            return ['success' => false, 'message' => implode('<br>', $errors), 'errors' => $errors];
        }

        $staAwal  = sta_to_meter($input['sta_awal']);
        $staAkhir = sta_to_meter($input['sta_akhir']);
        $panjang  = $staAkhir - $staAwal;

        $id = $this->model->create([
            'ruas_id'      => $ruasId,
            'sta_awal'     => $staAwal,
            'sta_akhir'    => $staAkhir,
            'panjang'      => $panjang,
            'baik'         => (float) $input['baik'],
            'sedang'       => (float) $input['sedang'],
            'rusak_ringan' => (float) $input['rusak_ringan'],
            'rusak_berat'  => (float) $input['rusak_berat'],
        ]);

        return ['success' => true, 'message' => 'Data strip map berhasil ditambahkan.', 'id' => $id];
    }

    /**
     * Simpan banyak segmen sekaligus (batch insert)
     * Input: array of rows dari form multi-segmen
     */
    public function batchCreate(int $ruasId, array $rows): array
    {
        $errors = [];
        $clean  = [];

        foreach ($rows as $i => $row) {
            $rowErrors = $this->validate($row, $ruasId, null, false);
            if (!empty($rowErrors)) {
                foreach ($rowErrors as $e) {
                    $errors[] = "Baris " . ($i + 1) . ": $e";
                }
            } else {
                $staAwal  = sta_to_meter($row['sta_awal']);
                $staAkhir = sta_to_meter($row['sta_akhir']);
                $clean[]  = [
                    'original_index' => $i + 1,
                    'sta_awal_str'  => $row['sta_awal'],
                    'sta_akhir_str' => $row['sta_akhir'],
                    'ruas_id'      => $ruasId,
                    'sta_awal'     => $staAwal,
                    'sta_akhir'    => $staAkhir,
                    'panjang'      => $staAkhir - $staAwal,
                    'baik'         => (float) $row['baik'],
                    'sedang'       => (float) $row['sedang'],
                    'rusak_ringan' => (float) $row['rusak_ringan'],
                    'rusak_berat'  => (float) $row['rusak_berat'],
                ];
            }
        }

        if (!empty($errors)) {
            return ['success' => false, 'message' => implode('<br>', $errors), 'errors' => $errors];
        }

        // Validasi Overlap & Duplikasi Segmen
        usort($clean, fn($a, $b) => $a['sta_awal'] <=> $b['sta_awal']);
        $overlapErrors = SegmentValidator::detectOverlaps($clean);

        if (!empty($overlapErrors)) {
            return ['success' => false, 'message' => implode('<br>', $overlapErrors), 'errors' => $overlapErrors];
        }

        foreach ($clean as $data) {
            // Hapus index bantu original_index dkk sebelum insert ke model
            unset($data['original_index']);
            unset($data['sta_awal_str']);
            unset($data['sta_akhir_str']);
            $this->model->create($data);
        }

        return ['success' => true, 'message' => count($clean) . ' segmen berhasil disimpan.'];
    }

    /**
     * Validasi & update stripmap
     */
    public function update(int $id, array $input): array
    {
        $existing = $this->model->findById($id);
        if (!$existing) {
            return ['success' => false, 'message' => 'Data strip map tidak ditemukan.'];
        }

        $errors = $this->validate($input, $existing['ruas_id'], $id, true);
        if (!empty($errors)) {
            return ['success' => false, 'message' => implode('<br>', $errors), 'errors' => $errors];
        }

        $staAwal  = sta_to_meter($input['sta_awal']);
        $staAkhir = sta_to_meter($input['sta_akhir']);
        $panjang  = $staAkhir - $staAwal;

        $this->model->update($id, [
            'sta_awal'     => $staAwal,
            'sta_akhir'    => $staAkhir,
            'panjang'      => $panjang,
            'baik'         => (float) $input['baik'],
            'sedang'       => (float) $input['sedang'],
            'rusak_ringan' => (float) $input['rusak_ringan'],
            'rusak_berat'  => (float) $input['rusak_berat'],
        ]);

        return ['success' => true, 'message' => 'Data strip map berhasil diperbarui.', 'ruas_id' => $existing['ruas_id']];
    }

    /**
     * Hapus stripmap
     */
    public function delete(int $id): array
    {
        $existing = $this->model->findById($id);
        if (!$existing) {
            return ['success' => false, 'message' => 'Data strip map tidak ditemukan.'];
        }

        $this->model->delete($id);
        return ['success' => true, 'message' => 'Data strip map berhasil dihapus.', 'ruas_id' => $existing['ruas_id']];
    }

    /**
     * Hapus semua stripmap berdasarkan ruas_id
     */
    public function deleteByRuasId(int $ruasId): bool
    {
        return $this->model->deleteByRuasId($ruasId);
    }

    private array $accumulatedCache = [];

    /**
     * Ambil ringkasan kondisi global seluruh ruas jalan
     */
    public function getGlobalSummary(?array $ruasIds = null, ?int $tahun = null): array
    {
        if ($ruasIds !== null) {
            return $this->model->getGlobalSummary($ruasIds, $tahun);
        }
        if ($tahun === 2025) {
            return $this->model->getGlobalSummary(null, 2025);
        }
        $data = $this->getAccumulatedConditionData($tahun);
        return $data['globalSummary'];
    }

    /**
     * Ambil ringkasan kondisi per ruas jalan
     */
    public function getConditionSummaryPerRuas(?int $tahun = null): array
    {
        if ($tahun === 2025) {
            return $this->model->getConditionSummaryPerRuas();
        }
        $data = $this->getAccumulatedConditionData($tahun);
        return $data['summaryPerRuas'];
    }

    /**
     * Ambil ringkasan kemantapan per kabupaten/kota (untuk chart dashboard & rekap)
     */
    public function getSummaryByKabupaten(?int $tahun = null): array
    {
        if ($tahun === 2025) {
            return $this->model->getSummaryByKabupaten(2025);
        }
        $data = $this->getAccumulatedConditionData($tahun);
        return $data['summaryByKabupaten'];
    }

    /**
     * Ambil ringkasan kemantapan per koridor
     */
    public function getSummaryByKoridor(?int $tahun = null): array
    {
        if ($tahun === 2025) {
            return $this->model->getSummaryByKoridor(2025);
        }
        $data = $this->getAccumulatedConditionData($tahun);
        return $data['summaryByKoridor'];
    }

    /**
     * Hitung data kondisi akumulatif (Base Survey 2025 + Penanganan <= $tahun)
     */
    public function getAccumulatedConditionData(?int $tahun = null): array
    {
        $cacheKey = $tahun === null ? 'all' : (string)$tahun;
        if (isset($this->accumulatedCache[$cacheKey])) {
            return $this->accumulatedCache[$cacheKey];
        }

        $ruasModel       = new RuasJalan();
        $penangananModel = new Penanganan();
        $prediksiService = new PrediksiService();
        $ruasList        = $ruasModel->getAll();

        $totalPanjang = 0.0;
        $totalBaik    = 0.0;
        $totalSedang  = 0.0;
        $totalRR      = 0.0;
        $totalRB      = 0.0;

        $kabMap = [];
        $korMap = [];
        $ruasSummary = [];

        foreach ($ruasList as $ruas) {
            $ruasId      = (int)$ruas['id'];
            $kabupaten   = !empty($ruas['kabupaten_kota']) ? trim($ruas['kabupaten_kota']) : 'Lainnya';
            $koridor     = !empty($ruas['koridor']) ? trim($ruas['koridor']) : 'Lainnya';
            $panjangRuas = (float)$ruas['panjang'];

            if (!isset($kabMap[$kabupaten])) {
                $kabMap[$kabupaten] = [
                    'kabupaten_kota'     => $kabupaten,
                    'total_panjang'      => 0.0,
                    'total_mantap'       => 0.0,
                    'total_tidak_mantap' => 0.0,
                    'total_baik'         => 0.0,
                    'total_sedang'       => 0.0,
                    'total_rusak_ringan' => 0.0,
                    'total_rusak_berat'  => 0.0,
                ];
            }
            if (!isset($korMap[$koridor])) {
                $korMap[$koridor] = [
                    'koridor'            => $koridor,
                    'total_panjang'      => 0.0,
                    'total_mantap'       => 0.0,
                    'total_tidak_mantap' => 0.0,
                ];
            }

            $stripmapList = $this->model->getByRuasId($ruasId);

            if ($tahun === 2025) {
                $penangananList = $penangananModel->getByRuasId($ruasId, 2025);
            } elseif ($tahun !== null && $tahun > 2025) {
                $penangananList = $penangananModel->getByRuasIdUpTo($ruasId, $tahun);
            } else {
                $penangananList = $penangananModel->getByRuasId($ruasId, null);
            }

            $summary = $prediksiService->hitungSummary($penangananList, $stripmapList);
            $kondisi = $summary['sesudah'];

            $rBaik   = (float)$kondisi['baik'];
            $rSedang = (float)$kondisi['sedang'];
            $rRR     = (float)$kondisi['rusak_ringan'];
            $rRB     = (float)$kondisi['rusak_berat'];
            $rMantap = $rBaik + $rSedang;
            $rTM     = $rRR + $rRB;

            $totalPanjang += $panjangRuas;
            $totalBaik    += $rBaik;
            $totalSedang  += $rSedang;
            $totalRR      += $rRR;
            $totalRB      += $rRB;

            $kabMap[$kabupaten]['total_panjang']      += $panjangRuas;
            $kabMap[$kabupaten]['total_mantap']       += $rMantap;
            $kabMap[$kabupaten]['total_tidak_mantap'] += $rTM;
            $kabMap[$kabupaten]['total_baik']         += $rBaik;
            $kabMap[$kabupaten]['total_sedang']       += $rSedang;
            $kabMap[$kabupaten]['total_rusak_ringan'] += $rRR;
            $kabMap[$kabupaten]['total_rusak_berat']  += $rRB;

            $korMap[$koridor]['total_panjang']      += $panjangRuas;
            $korMap[$koridor]['total_mantap']       += $rMantap;
            $korMap[$koridor]['total_tidak_mantap'] += $rTM;

            $ruasSummary[] = [
                'id'                 => $ruasId,
                'kode_ruas'          => $ruas['kode_ruas'],
                'nama_ruas'          => $ruas['nama_ruas'],
                'sta_awal'           => $ruas['sta_awal'] ?? 0,
                'sta_akhir'          => $ruas['sta_akhir'] ?? 0,
                'total_panjang'      => $panjangRuas,
                'kabupaten_kota'     => $kabupaten,
                'koridor'            => $koridor,
                'panjang'            => $panjangRuas,
                'baik'               => $rBaik,
                'sedang'             => $rSedang,
                'rusak_ringan'       => $rRR,
                'rusak_berat'        => $rRB,
                'mantap'             => $rMantap,
                'tidak_mantap'       => $rTM,
                'total_terisi'       => $rBaik + $rSedang + $rRR + $rRB,
                'total_baik'         => $rBaik,
                'total_sedang'       => $rSedang,
                'total_rusak_ringan' => $rRR,
                'total_rusak_berat'  => $rRB,
                'total_mantap'       => $rMantap,
                'total_tidak_mantap' => $rTM,
            ];
        }

        ksort($kabMap);
        ksort($korMap);

        $result = [
            'globalSummary' => [
                'total_panjang'      => $totalPanjang,
                'total_baik'         => $totalBaik,
                'total_sedang'       => $totalSedang,
                'total_rusak_ringan' => $totalRR,
                'total_rusak_berat'  => $totalRB,
                'total_mantap'       => $totalBaik + $totalSedang,
                'total_tidak_mantap' => $totalRR + $totalRB,
            ],
            'summaryByKabupaten' => array_values($kabMap),
            'summaryByKoridor'   => array_values($korMap),
            'summaryPerRuas'     => $ruasSummary,
        ];

        $this->accumulatedCache[$cacheKey] = $result;
        return $result;
    }

    /**
     * Ambil daftar tahun tersedia di tabel stripmap
     */
    public function getAvailableYears(): array
    {
        return $this->model->getAvailableYears();
    }

    /**
     * Validasi input stripmap
     */
    private function validate(array $input, int $ruasId, ?int $excludeId = null, bool $checkDbOverlap = true): array
    {
        $errors = [];

        // Validasi STA
        $staAwalRaw  = trim((string)($input['sta_awal'] ?? ''));
        $staAkhirRaw = trim((string)($input['sta_akhir'] ?? ''));

        if ($staAwalRaw === '') {
            $errors[] = 'STA Awal wajib diisi.';
        }
        if ($staAkhirRaw === '') {
            $errors[] = 'STA Akhir wajib diisi.';
        }

        if ($staAwalRaw !== '' && $staAkhirRaw !== '') {
            $staAwal  = sta_to_meter($input['sta_awal']);
            $staAkhir = sta_to_meter($input['sta_akhir']);
            $panjang  = $staAkhir - $staAwal;

            if ($staAwal < 0) {
                $errors[] = 'STA Awal tidak boleh negatif.';
            }
            if ($staAkhir < 0) {
                $errors[] = 'STA Akhir tidak boleh negatif.';
            }
            if ($staAkhir <= $staAwal) {
                $errors[] = 'STA Akhir harus lebih besar dari STA Awal.';
            }

            // Validasi kondisi jalan
            $baik        = (float) ($input['baik'] ?? 0);
            $sedang      = (float) ($input['sedang'] ?? 0);
            $rusakRingan = (float) ($input['rusak_ringan'] ?? 0);
            $rusakBerat  = (float) ($input['rusak_berat'] ?? 0);

            if ($baik < 0 || $sedang < 0 || $rusakRingan < 0 || $rusakBerat < 0) {
                $errors[] = 'Nilai kondisi jalan tidak boleh negatif.';
            }

            $totalKondisi = $baik + $sedang + $rusakRingan + $rusakBerat;

            if ($panjang > 0 && abs($totalKondisi - $panjang) > 0.01) {
                $errors[] = "Jumlah kondisi ({$totalKondisi} m) harus sama dengan panjang segmen ({$panjang} m).";
            }

            // Deteksi tumpang tindih dengan segmen yang sudah ada di database
            if ($checkDbOverlap) {
                $existingSegments = $this->model->getByRuasId($ruasId);
                foreach ($existingSegments as $es) {
                    if ($excludeId && (int)$es['id'] === $excludeId) {
                        continue;
                    }
                    $esAwal = (float)$es['sta_awal'];
                    $esAkhir = (float)$es['sta_akhir'];
                    if (max($staAwal, $esAwal) < min($staAkhir, $esAkhir)) {
                        $errors[] = "Segmen ini tumpang tindih dengan segmen yang sudah ada: STA " . meter_to_sta($esAwal) . " s/d " . meter_to_sta($esAkhir) . ".";
                        break;
                    }
                }
            }
        }

        return $errors;
    }
}
