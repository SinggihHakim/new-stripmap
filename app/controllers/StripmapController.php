<?php

/**
 * ============================================================
 * Controller: StripmapController
 * ============================================================
 * CRUD untuk data strip map per ruas jalan.
 */

class StripmapController
{
    private StripmapService   $service;
    private RuasService       $ruasService;
    private PerkerasanService $perkerasanService;
    private FotoLapanganService $fotoService;
    private PenangananService $penangananService;

    public function __construct()
    {
        $this->service           = new StripmapService();
        $this->ruasService       = new RuasService();
        $this->perkerasanService = new PerkerasanService();
        $this->fotoService       = new FotoLapanganService();
        $this->penangananService = new PenangananService();
    }

    /**
     * Daftar stripmap untuk sebuah ruas
     */
    public function index(int $ruasId): void
    {
        $ruas = $this->ruasService->findById($ruasId);
        if (!$ruas) {
            flash('error', 'Ruas jalan tidak ditemukan.');
            redirect(base_url('ruas'));
            return;
        }

        $data = [
            'title'             => 'Strip Map: ' . $ruas['nama_ruas'],
            'ruas'              => $ruas,
            'stripmaps'         => $this->service->getByRuasId($ruasId),
            'summary'           => $this->service->getSummary($ruasId),
            'perkerasans'       => $this->perkerasanService->getByRuasId($ruasId),
            'summaryPerkerasan' => $this->perkerasanService->getSummary($ruasId),
            'fotoLapangans'     => $this->fotoService->getByRuasId($ruasId),
            'penanganans'       => $this->penangananService->getByRuasId($ruasId),
            'penangananSummary' => $this->penangananService->getSummary($ruasId),
            'penangananYears'   => $this->penangananService->getAvailableYears($ruasId),
        ];
        view('layouts.app', array_merge($data, ['content' => 'stripmap.index']));
    }

    /**
     * Form tambah stripmap
     */
    public function create(int $ruasId): void
    {
        $ruas = $this->ruasService->findById($ruasId);
        if (!$ruas) {
            flash('error', 'Ruas jalan tidak ditemukan.');
            redirect(base_url('ruas'));
            return;
        }

        // Handle insert_after parameter untuk fitur "Sisipkan Segmen Stripmap"
        $prefillData = null;
        if (isset($_GET['insert_after']) && is_numeric($_GET['insert_after'])) {
            $afterSegmentId = (int) $_GET['insert_after'];
            $afterSegment = $this->service->findById($afterSegmentId);

            if ($afterSegment && $afterSegment['ruas_id'] == $ruasId) {
                $allSegments = $this->service->getByRuasId($ruasId);
                $nextSegment = null;
                foreach ($allSegments as $seg) {
                    if ($seg['sta_awal'] > $afterSegment['sta_akhir']) {
                        $nextSegment = $seg;
                        break;
                    }
                }

                $prefillData = [
                    'sta_awal'  => meter_to_sta($afterSegment['sta_akhir']),
                    'sta_akhir' => $nextSegment ? meter_to_sta($nextSegment['sta_awal']) : '',
                ];
            }
        }

        // Handle insert_after_perkerasan parameter untuk perkerasan
        $prefillPerkerasanData = null;
        if (isset($_GET['insert_after_perkerasan']) && is_numeric($_GET['insert_after_perkerasan'])) {
            $afterId = (int) $_GET['insert_after_perkerasan'];
            $afterSeg = $this->perkerasanService->findById($afterId);

            if ($afterSeg && $afterSeg['ruas_id'] == $ruasId) {
                $allSegs = $this->perkerasanService->getByRuasId($ruasId);
                $nextSeg = null;
                foreach ($allSegs as $seg) {
                    if ($seg['sta_awal'] > $afterSeg['sta_akhir']) {
                        $nextSeg = $seg;
                        break;
                    }
                }

                $prefillPerkerasanData = [
                    'sta_awal'  => meter_to_sta($afterSeg['sta_akhir']),
                    'sta_akhir' => $nextSeg ? meter_to_sta($nextSeg['sta_awal']) : '',
                ];
            }
        }

        $data = [
            'title'                 => 'Tambah Segmen Jalan',
            'ruas'                  => $ruas,
            'prefillData'           => $prefillData,
            'prefillPerkerasanData' => $prefillPerkerasanData,
            'perkerasans'           => $this->perkerasanService->getByRuasId($ruasId),
        ];
        view('layouts.app', array_merge($data, ['content' => 'stripmap.form']));
    }

    /**
     * Proses simpan stripmap baru
     */
    public function store(int $ruasId): void
    {
        // Periksa apakah ini batch insert (array of rows)
        if (isset($_POST['rows']) && is_array($_POST['rows'])) {
            $result = $this->service->batchCreate($ruasId, $_POST['rows']);
        } else {
            // Single insert fallback (untuk edit / lama)
            $result = $this->service->create($ruasId, $_POST);
        }

        if ($result['success']) {
            // Sinkronisasi STA ruas dari data stripmap
            $this->ruasService->syncStaFromStripmap($ruasId);
            flash('success', $result['message']);
            redirect(base_url('stripmap/' . $ruasId));
        } else {
            flash('error', $result['message']);
            if (isset($_POST['rows'])) {
                $_SESSION['old_input'] = $_POST['rows'];
            }
            redirect(base_url('stripmap/create/' . $ruasId));
        }
    }

    /**
     * Proses simpan gabungan segmen strip map & perkerasan baru (1 tombol simpan)
     */
    public function batch(int $ruasId): void
    {
        $ruas = $this->ruasService->findById($ruasId);
        if (!$ruas) {
            flash('error', 'Ruas jalan tidak ditemukan.');
            redirect(base_url('ruas'));
            return;
        }

        $smSaved = false;
        $pkSaved = false;

        // 1. Simpan Segmen Strip Map (Kondisi)
        if (isset($_POST['rows']) && is_array($_POST['rows'])) {
            $rows = array_filter($_POST['rows'], function($row) {
                return !empty(trim($row['sta_awal'] ?? '')) || !empty(trim($row['sta_akhir'] ?? ''));
            });

            if (!empty($rows)) {
                $smResult = $this->service->batchCreate($ruasId, array_values($rows));
                if ($smResult['success']) {
                    $smSaved = true;
                } else {
                    flash('error', 'Gagal menyimpan strip map: ' . $smResult['message']);
                }
            }
        }

        // 2. Simpan Segmen Perkerasan
        if (isset($_POST['perkerasan_rows']) && is_array($_POST['perkerasan_rows'])) {
            $pkRows = array_filter($_POST['perkerasan_rows'], function($row) {
                return !empty(trim($row['sta_awal'] ?? '')) || !empty(trim($row['sta_akhir'] ?? ''));
            });

            if (!empty($pkRows)) {
                $pkResult = $this->perkerasanService->batchCreate($ruasId, array_values($pkRows));
                if ($pkResult['success']) {
                    $pkSaved = true;
                } else {
                    flash('error', 'Gagal menyimpan perkerasan: ' . $pkResult['message']);
                }
            }
        }

        if ($smSaved || $pkSaved) {
            $this->ruasService->syncStaFromStripmap($ruasId);
            flash('success', 'Data segmen strip map dan perkerasan berhasil disimpan.');
        }

        redirect(base_url('stripmap/' . $ruasId));
    }

    /**
     * Form edit stripmap
     */
    public function edit(int $id): void
    {
        $stripmap = $this->service->findById($id);
        if (!$stripmap) {
            flash('error', 'Data strip map tidak ditemukan.');
            redirect(base_url('ruas'));
            return;
        }

        $ruas = $this->ruasService->findById($stripmap['ruas_id']);

        $data = [
            'title'    => 'Edit Strip Map',
            'ruas'     => $ruas,
            'stripmap' => $stripmap,
        ];
        view('layouts.app', array_merge($data, ['content' => 'stripmap.form']));
    }

    /**
     * Proses update stripmap
     */
    public function update(int $id): void
    {
        $input = $_POST;
        if (isset($_POST['rows']) && is_array($_POST['rows']) && isset($_POST['rows'][0])) {
            $input = $_POST['rows'][0];
        }
        $result = $this->service->update($id, $input);

        if ($result['success']) {
            $this->ruasService->syncStaFromStripmap($result['ruas_id']);
            flash('success', $result['message']);
            redirect(base_url('stripmap/' . $result['ruas_id']));
        } else {
            flash('error', $result['message']);
            redirect(base_url('stripmap/edit/' . $id));
        }
    }

    /**
     * Proses hapus stripmap
     */
    public function delete(int $id): void
    {
        $result = $this->service->delete($id);

        if ($result['success']) {
            $this->ruasService->syncStaFromStripmap($result['ruas_id']);
            flash('success', $result['message']);
            redirect(base_url('stripmap/' . $result['ruas_id']));
        } else {
            flash('error', $result['message']);
            redirect(base_url('ruas'));
        }
    }

    /**
     * Preview strip map & perkerasan visual untuk sebuah ruas
     */
    public function preview(int $ruasId): void
    {
        $ruas = $this->ruasService->findById($ruasId);
        if (!$ruas) {
            flash('error', 'Ruas jalan tidak ditemukan.');
            redirect(base_url('ruas'));
            return;
        }

        $data = [
            'title'             => 'Preview Strip Map: ' . $ruas['nama_ruas'],
            'ruas'              => $ruas,
            'stripmaps'         => $this->service->getByRuasId($ruasId),
            'summary'           => $this->service->getSummary($ruasId),
            'perkerasans'       => $this->perkerasanService->getByRuasId($ruasId),
            'summaryPerkerasan' => $this->perkerasanService->getSummary($ruasId),
            'fotoLapangans'     => $this->fotoService->getByRuasId($ruasId),
        ];
        view('layouts.app', array_merge($data, ['content' => 'export.ruas_jalan']));
    }

    // ──────────────────────────────────────────────
    // FOTO LAPANGAN HANDLERS
    // ──────────────────────────────────────────────

    /**
     * Upload foto lapangan (bulk file atau file ZIP)
     */
    public function uploadFoto(int $ruasId): void
    {
        $ruas = $this->ruasService->findById($ruasId);
        if (!$ruas) {
            flash('error', 'Ruas jalan tidak ditemukan.');
            redirect(base_url('ruas'));
            return;
        }

        if (!empty($_FILES['zip_file']['name'])) {
            $result = $this->fotoService->handleZipUpload($ruasId, $_FILES['zip_file']);
        } elseif (!empty($_FILES['foto_files']['name'])) {
            $result = $this->fotoService->handleBatchUpload($ruasId, $_FILES['foto_files']);
        } else {
            $result = ['success' => false, 'message' => 'Tidak ada file foto yang dipilih.'];
        }

        flash($result['success'] ? 'success' : 'error', $result['message']);
        redirect(base_url('stripmap/' . $ruasId));
    }

    /**
     * Hapus foto lapangan
     */
    public function deleteFoto(int $fotoId): void
    {
        $foto = (new FotoLapangan())->findById($fotoId);
        $ruasId = $foto['ruas_id'] ?? null;

        $result = $this->fotoService->deleteFoto($fotoId);
        flash($result['success'] ? 'success' : 'error', $result['message']);

        if ($ruasId) {
            redirect(base_url('stripmap/' . $ruasId));
        } else {
            redirect(base_url('ruas'));
        }
    }

    // ──────────────────────────────────────────────
    // PERKERASAN HANDLERS
    // ──────────────────────────────────────────────

    /**
     * Simpan segmen perkerasan (single/batch)
     */
    public function perkerasanStore(int $ruasId): void
    {
        if (isset($_POST['perkerasan_rows']) && is_array($_POST['perkerasan_rows'])) {
            $result = $this->perkerasanService->batchCreate($ruasId, $_POST['perkerasan_rows']);
        } else {
            $result = $this->perkerasanService->create($ruasId, $_POST);
        }

        if ($result['success']) {
            flash('success', $result['message']);
            redirect(base_url('stripmap/' . $ruasId));
        } else {
            flash('error', $result['message']);
            if (isset($_POST['perkerasan_rows'])) {
                $_SESSION['old_perkerasan_input'] = $_POST['perkerasan_rows'];
            }
            redirect(base_url('stripmap/create/' . $ruasId));
        }
    }

    /**
     * Form edit perkerasan
     */
    public function perkerasanEdit(int $id): void
    {
        $perkerasan = $this->perkerasanService->findById($id);
        if (!$perkerasan) {
            flash('error', 'Data perkerasan tidak ditemukan.');
            redirect(base_url('ruas'));
            return;
        }

        $ruas = $this->ruasService->findById($perkerasan['ruas_id']);

        $data = [
            'title'      => 'Edit Segmen Perkerasan',
            'ruas'       => $ruas,
            'perkerasan' => $perkerasan,
        ];
        view('layouts.app', array_merge($data, ['content' => 'stripmap.form']));
    }

    /**
     * Update perkerasan
     */
    public function perkerasanUpdate(int $id): void
    {
        $input = $_POST;
        if (isset($_POST['perkerasan_rows']) && is_array($_POST['perkerasan_rows']) && isset($_POST['perkerasan_rows'][0])) {
            $input = $_POST['perkerasan_rows'][0];
        }
        $result = $this->perkerasanService->update($id, $input);

        if ($result['success']) {
            flash('success', $result['message']);
            redirect(base_url('stripmap/' . $result['ruas_id']));
        } else {
            flash('error', $result['message']);
            redirect(base_url('perkerasan/edit/' . $id));
        }
    }

    /**
     * Hapus perkerasan
     */
    public function perkerasanDelete(int $id): void
    {
        $result = $this->perkerasanService->delete($id);

        if ($result['success']) {
            flash('success', $result['message']);
            redirect(base_url('stripmap/' . $result['ruas_id']));
        } else {
            flash('error', $result['message']);
            redirect(base_url('ruas'));
        }
    }

    /**
     * Import KML/KMZ route khusus untuk ruas jalan dari menu Strip Map
     * (Memperbarui rute peta tanpa mengubah/menghapus data segmen stripmap & perkerasan)
     */
    public function importKml(int $ruasId): void
    {
        $ruas = $this->ruasService->findById($ruasId);
        if (!$ruas) {
            flash('error', 'Ruas jalan tidak ditemukan.');
            redirect(base_url('ruas'));
            return;
        }

        $koordinatJson = $_POST['koordinat_json'] ?? null;
        if (empty($koordinatJson)) {
            flash('error', 'Data rute KML / KMZ tidak ditemukan.');
            redirect(base_url('stripmap/' . $ruasId));
            return;
        }

        $updateData = [
            'koordinat_json' => $koordinatJson,
        ];

        if (!empty($_POST['lat_awal']))  $updateData['lat_awal']  = (float) $_POST['lat_awal'];
        if (!empty($_POST['lng_awal']))  $updateData['lng_awal']  = (float) $_POST['lng_awal'];
        if (!empty($_POST['lat_akhir'])) $updateData['lat_akhir'] = (float) $_POST['lat_akhir'];
        if (!empty($_POST['lng_akhir'])) $updateData['lng_akhir'] = (float) $_POST['lng_akhir'];

        $res = $this->ruasService->update($ruasId, $updateData);

        if ($res['success']) {
            flash('success', 'Rute KML / KMZ berhasil diimpor dan diperbarui pada peta.');
        } else {
            flash('error', 'Gagal menyimpan rute KML: ' . $res['message']);
        }

        redirect(base_url('stripmap/' . $ruasId));
    }

    /**
     * Halaman Perbandingan Stripmap Antar Tahun
     * Menampilkan visual garis warna kondisi berlapis per tahun (Baseline + prediksi tiap tahun)
     * beserta chart kemantapan dan tabel ringkasan untuk satu ruas.
     */
    public function compare($ruasId = null): void
    {
        if (empty($ruasId)) {
            $all = $this->ruasService->getAll();
            if (!empty($all)) {
                redirect(base_url('stripmap/compare/' . $all[0]['id']));
                return;
            }
            flash('error', 'Belum ada data ruas jalan.');
            redirect(base_url('ruas'));
            return;
        }

        $ruas = is_numeric($ruasId) ? $this->ruasService->findById((int)$ruasId) : $this->ruasService->findByKode((string)$ruasId);
        if (!$ruas) {
            flash('error', 'Ruas jalan tidak ditemukan.');
            redirect(base_url('ruas'));
            return;
        }
        $ruasId = (int)$ruas['id'];

        $ruasList    = $this->ruasService->getAll();
        $stripmaps   = $this->service->getByRuasId($ruasId);
        $perkerasans = $this->perkerasanService->getByRuasId($ruasId);

        $prediksiService = new PrediksiService();
        $totalPanjangM   = (float)$ruas['panjang'];

        // --- Hitung rentang STA keseluruhan ---
        $staBase = (float)($ruas['sta_awal'] ?? 0);
        $staEnd  = (float)($ruas['sta_akhir'] ?? 0);
        if ($totalPanjangM > 0) {
            $staEnd = max($staEnd, $staBase + $totalPanjangM);
        }
        foreach ($stripmaps as $sm) {
            $staEnd = max($staEnd, (float)$sm['sta_akhir']);
        }
        if ($staEnd <= $staBase) {
            $staEnd = $staBase + max($totalPanjangM, 1000.0);
        }
        $rentangM = $staEnd - $staBase;

        // --- Helper: konversi stripmap menjadi array runs kondisi per segmen ---
        // Setiap run: [sta_awal, sta_akhir, kondisi, pct_from, pct_to]
        $buildRunsFromStripmap = function (array $smList) use ($staBase, $rentangM): array {
            $runs = [];
            foreach ($smList as $sm) {
                $cur = (float)$sm['sta_awal'];
                $conditions = [
                    'baik'         => (float)$sm['baik'],
                    'sedang'       => (float)$sm['sedang'],
                    'rusak_ringan' => (float)$sm['rusak_ringan'],
                    'rusak_berat'  => (float)$sm['rusak_berat'],
                ];
                foreach ($conditions as $condKey => $len) {
                    if ($len > 0) {
                        $pctFrom = $rentangM > 0 ? round((($cur - $staBase) / $rentangM) * 100, 4) : 0;
                        $pctTo   = $rentangM > 0 ? round((($cur + $len - $staBase) / $rentangM) * 100, 4) : 0;
                        $runs[]  = [
                            'sta_awal'  => $cur,
                            'sta_akhir' => $cur + $len,
                            'kondisi'   => $condKey,
                            'pct_from'  => $pctFrom,
                            'pct_width' => round($pctTo - $pctFrom, 4),
                        ];
                        $cur += $len;
                    }
                }
            }
            return $runs;
        };

        // --- Helper: simulasi prediksi kondisi per segmen STA ---
        // Menerapkan logika matriks penanganan ke masing-masing segmen stripmap
        $buildPrediksiRuns = function (array $penangananList) use ($stripmaps, $prediksiService, $staBase, $rentangM): array {
            // Buat salinan stripmap dengan kondisi yang bisa dimodifikasi
            $smSim = [];
            foreach ($stripmaps as $sm) {
                $smSim[] = [
                    'sta_awal'     => (float)$sm['sta_awal'],
                    'sta_akhir'    => (float)$sm['sta_akhir'],
                    'panjang'      => (float)$sm['panjang'],
                    'baik'         => (float)$sm['baik'],
                    'sedang'       => (float)$sm['sedang'],
                    'rusak_ringan' => (float)$sm['rusak_ringan'],
                    'rusak_berat'  => (float)$sm['rusak_berat'],
                ];
            }

            // Terapkan setiap penanganan: ubah porsi kondisi di segmen yang di-overlap
            foreach ($penangananList as $p) {
                if (empty($p['jenis_pelaksana'])) continue;

                $pAwal  = (float)$p['sta_awal'];
                $pAkhir = (float)$p['sta_akhir'];

                // Cari kondisi dominan di rentang ini dari smSim
                $kondisiLengths = ['baik' => 0.0, 'sedang' => 0.0, 'rusak_ringan' => 0.0, 'rusak_berat' => 0.0];
                foreach ($smSim as $sm) {
                    $oStart = max($pAwal, $sm['sta_awal']);
                    $oEnd   = min($pAkhir, $sm['sta_akhir']);
                    if ($oEnd <= $oStart) continue;
                    $oLen  = $oEnd - $oStart;
                    $ratio = $sm['panjang'] > 0 ? $oLen / $sm['panjang'] : 0;
                    foreach (['baik', 'sedang', 'rusak_ringan', 'rusak_berat'] as $k) {
                        $kondisiLengths[$k] += $sm[$k] * $ratio;
                    }
                }
                arsort($kondisiLengths);
                $kondisiDominan = array_key_first($kondisiLengths) ?? 'baik';

                $prediksi = $prediksiService->hitung($kondisiDominan, 'aspal', $p['jenis_pelaksana']);
                if (!$prediksi['bisa_dilaksanakan'] || $prediksi['perlu_verifikasi']) continue;

                $targetKondisi = $prediksi['kondisi_prediksi'];

                // Modifikasi smSim: pindahkan porsi kondisi lama ke kondisi prediksi
                foreach ($smSim as &$sm) {
                    $oStart = max($pAwal, $sm['sta_awal']);
                    $oEnd   = min($pAkhir, $sm['sta_akhir']);
                    if ($oEnd <= $oStart) continue;
                    $oLen  = $oEnd - $oStart;
                    $ratio = $sm['panjang'] > 0 ? $oLen / $sm['panjang'] : 0;

                    foreach (['baik', 'sedang', 'rusak_ringan', 'rusak_berat'] as $k) {
                        if ($k !== $targetKondisi) {
                            $porsi   = $sm[$k] * $ratio;
                            $sm[$k]  = max(0.0, $sm[$k] - $porsi);
                            $sm[$targetKondisi] += $porsi;
                        }
                    }
                }
                unset($sm);
            }

            // Bangun runs dari smSim yang sudah dimodifikasi
            $runs = [];
            foreach ($smSim as $sm) {
                $cur = $sm['sta_awal'];
                $conditions = [
                    'baik'         => $sm['baik'],
                    'sedang'       => $sm['sedang'],
                    'rusak_ringan' => $sm['rusak_ringan'],
                    'rusak_berat'  => $sm['rusak_berat'],
                ];
                foreach ($conditions as $condKey => $len) {
                    if ($len > 0.5) { // threshold kecil agar tidak muncul sliver
                        $pctFrom = $rentangM > 0 ? round((($cur - $staBase) / $rentangM) * 100, 4) : 0;
                        $pctTo   = $rentangM > 0 ? round((($cur + $len - $staBase) / $rentangM) * 100, 4) : 0;
                        $runs[]  = [
                            'sta_awal'  => $cur,
                            'sta_akhir' => $cur + $len,
                            'kondisi'   => $condKey,
                            'pct_from'  => $pctFrom,
                            'pct_width' => round($pctTo - $pctFrom, 4),
                        ];
                        $cur += $len;
                    }
                }
            }
            return $runs;
        };

        // --- Hitung kemantapan dari runs ---
        $hitungMantap = function (array $runs) use ($totalPanjangM): float {
            $mantapM = 0.0;
            foreach ($runs as $r) {
                if (in_array($r['kondisi'], ['baik', 'sedang'])) {
                    $mantapM += $r['sta_akhir'] - $r['sta_awal'];
                }
            }
            return $totalPanjangM > 0 ? round(($mantapM / $totalPanjangM) * 100, 1) : 0.0;
        };

        // --- Bangun data per tahun (2025 = Baseline Data Awal, 2026-2029 = Prediksi Penanganan) ---
        $yearlyData = [];
        $tahunWarna = TahunHelper::getWarna();
        $tahunAwal  = TahunHelper::awal();

        // 1. Tahun 2025 (Baseline / Data Awal Eksisting)
        $baselineRuns      = $buildRunsFromStripmap($stripmaps);
        $baselinePctMantap = $hitungMantap($baselineRuns);
        $yearlyData[$tahunAwal] = [
            'label'       => (string)$tahunAwal,
            'sublabel'    => 'Kondisi Awal (Baseline)',
            'tahun'       => $tahunAwal,
            'tipe'        => 'baseline',
            'runs'        => $baselineRuns,
            'pct_mantap'  => $baselinePctMantap,
            'delta_pct'   => 0.0,
            'penanganan'  => [],
            'warna_label' => $tahunWarna[$tahunAwal] ?? '#0284c7',
        ];

        // 2. Tahun 2026 s/d 2029 (Prediksi Kumulatif Penanganan)
        foreach (TahunHelper::getList() as $thn) {
            if ($thn <= $tahunAwal) {
                continue; // 2025 sudah dicatat sebagai baseline di atas
            }

            $pkgList    = $this->penangananService->getByRuasIdUpTo($ruasId, $thn);
            $runs       = $buildPrediksiRuns($pkgList);
            $pctMantap  = $hitungMantap($runs);
            $delta      = round($pctMantap - $baselinePctMantap, 1);

            $yearlyData[$thn] = [
                'label'       => (string)$thn,
                'sublabel'    => 'Prediksi Penanganan',
                'tahun'       => $thn,
                'tipe'        => 'prediksi',
                'runs'        => $runs,
                'pct_mantap'  => $pctMantap,
                'delta_pct'   => $delta,
                'penanganan'  => array_map(fn($p) => [
                    'sta_awal'        => (float)$p['sta_awal'],
                    'sta_akhir'       => (float)$p['sta_akhir'],
                    'tahun'           => (int)$p['tahun'],
                    'jenis_penanganan'=> $p['jenis_penanganan'] ?? '',
                    'nama_paket'      => $p['nama_paket'] ?? '',
                    'pct_from'        => $rentangM > 0
                        ? round((((float)$p['sta_awal'] - $staBase) / $rentangM) * 100, 4)
                        : 0,
                    'pct_width'       => $rentangM > 0
                        ? round((((float)$p['panjang']) / $rentangM) * 100, 4)
                        : 0,
                ], $pkgList),
                'warna_label' => $tahunWarna[$thn] ?? '#6b7280',
            ];
        }

        // --- STA tick marks untuk sumbu X ---
        $tickCount  = 8; // maksimal 8 label STA
        $tickStep   = $rentangM > 0 ? ceil($rentangM / $tickCount / 1000) * 1000 : 1000;
        $ticks      = [];
        for ($sta = $staBase; $sta <= $staEnd + 1; $sta += $tickStep) {
            $pct    = $rentangM > 0 ? round((($sta - $staBase) / $rentangM) * 100, 2) : 0;
            $ticks[] = ['meter' => $sta, 'pct' => $pct, 'label' => meter_to_sta($sta)];
        }

        $data = [
            'title'       => 'Perbandingan Kondisi Ruas — ' . $ruas['nama_ruas'],
            'ruas'        => $ruas,
            'ruasList'    => $ruasList,
            'yearlyData'  => $yearlyData,
            'ticks'       => $ticks,
            'staBase'     => $staBase,
            'staEnd'      => $staEnd,
            'rentangM'    => $rentangM,
            'kondisiColors' => PrediksiService::KONDISI_COLORS,
            'kondisiLabels' => PrediksiService::KONDISI_LABELS,
        ];

        view('layouts.app', array_merge($data, ['content' => 'stripmap.compare']));
    }
}

