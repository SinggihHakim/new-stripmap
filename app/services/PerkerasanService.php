<?php

/**
 * ============================================================
 * Service: PerkerasanService
 * ============================================================
 * Business logic untuk Jenis Perkerasan Jalan (Rigid, Aspal, Agregat/Tanah, Belum Tembus).
 */

class PerkerasanService
{
    private Perkerasan $model;

    public function __construct()
    {
        $this->model = new Perkerasan();
    }

    /**
     * Ambil semua perkerasan berdasarkan ruas ID
     */
    public function getByRuasId(int $ruasId): array
    {
        return $this->model->getByRuasId($ruasId);
    }

    /**
     * Ambil satu perkerasan
     */
    public function findById(int $id): ?array
    {
        return $this->model->findById($id);
    }

    /**
     * Ambil ringkasan perkerasan
     */
    public function getSummary(int $ruasId): array
    {
        return $this->model->getSummaryByRuasId($ruasId);
    }

    /**
     * Validasi & simpan perkerasan baru (single insert)
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
            'ruas_id'       => $ruasId,
            'sta_awal'      => $staAwal,
            'sta_akhir'     => $staAkhir,
            'panjang'       => $panjang,
            'rigid'         => (float) ($input['rigid'] ?? 0),
            'aspal'         => (float) ($input['aspal'] ?? 0),
            'agregat_tanah' => (float) ($input['agregat_tanah'] ?? 0),
            'belum_tembus'  => (float) ($input['belum_tembus'] ?? 0),
        ]);

        return ['success' => true, 'message' => 'Data perkerasan jalan berhasil ditambahkan.', 'id' => $id, 'ruas_id' => $ruasId];
    }

    /**
     * Simpan banyak segmen perkerasan sekaligus (batch insert)
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
                    'sta_awal_str'   => $row['sta_awal'],
                    'sta_akhir_str'  => $row['sta_akhir'],
                    'ruas_id'       => $ruasId,
                    'sta_awal'      => $staAwal,
                    'sta_akhir'     => $staAkhir,
                    'panjang'       => $staAkhir - $staAwal,
                    'rigid'         => (float) ($row['rigid'] ?? 0),
                    'aspal'         => (float) ($row['aspal'] ?? 0),
                    'agregat_tanah' => (float) ($row['agregat_tanah'] ?? 0),
                    'belum_tembus'  => (float) ($row['belum_tembus'] ?? 0),
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
            unset($data['original_index']);
            unset($data['sta_awal_str']);
            unset($data['sta_akhir_str']);
            $this->model->create($data);
        }

        return ['success' => true, 'message' => count($clean) . ' segmen perkerasan berhasil disimpan.'];
    }

    /**
     * Validasi & update perkerasan
     */
    public function update(int $id, array $input): array
    {
        $existing = $this->model->findById($id);
        if (!$existing) {
            return ['success' => false, 'message' => 'Data perkerasan tidak ditemukan.'];
        }

        $errors = $this->validate($input, $existing['ruas_id'], $id, true);
        if (!empty($errors)) {
            return ['success' => false, 'message' => implode('<br>', $errors), 'errors' => $errors];
        }

        $staAwal  = sta_to_meter($input['sta_awal']);
        $staAkhir = sta_to_meter($input['sta_akhir']);
        $panjang  = $staAkhir - $staAwal;

        $this->model->update($id, [
            'sta_awal'      => $staAwal,
            'sta_akhir'     => $staAkhir,
            'panjang'       => $panjang,
            'rigid'         => (float) ($input['rigid'] ?? 0),
            'aspal'         => (float) ($input['aspal'] ?? 0),
            'agregat_tanah' => (float) ($input['agregat_tanah'] ?? 0),
            'belum_tembus'  => (float) ($input['belum_tembus'] ?? 0),
        ]);

        return ['success' => true, 'message' => 'Data perkerasan jalan berhasil diperbarui.', 'ruas_id' => $existing['ruas_id']];
    }

    /**
     * Hapus perkerasan
     */
    public function delete(int $id): array
    {
        $existing = $this->model->findById($id);
        if (!$existing) {
            return ['success' => false, 'message' => 'Data perkerasan tidak ditemukan.'];
        }

        $this->model->delete($id);
        return ['success' => true, 'message' => 'Data perkerasan berhasil dihapus.', 'ruas_id' => $existing['ruas_id']];
    }

    /**
     * Hapus semua perkerasan berdasarkan ruas_id
     */
    public function deleteByRuasId(int $ruasId): bool
    {
        return $this->model->deleteByRuasId($ruasId);
    }

    /**
     * Validasi input perkerasan
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

            // Validasi jenis perkerasan
            $rigid        = (float) ($input['rigid'] ?? 0);
            $aspal        = (float) ($input['aspal'] ?? 0);
            $agregatTanah = (float) ($input['agregat_tanah'] ?? 0);
            $belumTembus  = (float) ($input['belum_tembus'] ?? 0);

            if ($rigid < 0 || $aspal < 0 || $agregatTanah < 0 || $belumTembus < 0) {
                $errors[] = 'Nilai jenis perkerasan tidak boleh negatif.';
            }

            $totalPerkerasan = $rigid + $aspal + $agregatTanah + $belumTembus;

            if ($panjang > 0 && abs($totalPerkerasan - $panjang) > 0.01) {
                $errors[] = "Jumlah perkerasan ({$totalPerkerasan} m) harus sama dengan panjang segmen ({$panjang} m).";
            }

            // Deteksi tumpang tindih dengan segmen perkerasan yang sudah ada di database
            if ($checkDbOverlap) {
                $existingSegments = $this->model->getByRuasId($ruasId);
                foreach ($existingSegments as $es) {
                    if ($excludeId && (int)$es['id'] === $excludeId) {
                        continue;
                    }
                    $esAwal = (float)$es['sta_awal'];
                    $esAkhir = (float)$es['sta_akhir'];
                    if (max($staAwal, $esAwal) < min($staAkhir, $esAkhir)) {
                        $errors[] = "Segmen perkerasan ini tumpang tindih dengan segmen yang sudah ada: STA " . meter_to_sta($esAwal) . " s/d " . meter_to_sta($esAkhir) . ".";
                        break;
                    }
                }
            }
        }

        return $errors;
    }

    /**
     * Ambil ringkasan perkerasan global seluruh ruas jalan
     */
    public function getGlobalSummary(?array $ruasIds = null, ?int $tahun = null): array
    {
        return $this->model->getGlobalSummary($ruasIds, $tahun);
    }

    /**
     * Ambil daftar tahun tersedia di tabel perkerasan
     */
    public function getAvailableYears(): array
    {
        return $this->model->getAvailableYears();
    }
}
