<?php

/**
 * ============================================================
 * Controller: PrediksiController
 * ============================================================
 * Menyajikan halaman Prediksi Kondisi Jalan berdasarkan
 * matriks penanganan dari Ide Strip Map.xlsx.
 */

class PrediksiController
{
    private PrediksiService   $prediksiService;
    private PenangananService $penangananService;
    private StripmapService   $stripmapService;
    private RuasService       $ruasService;

    public function __construct()
    {
        $this->prediksiService   = new PrediksiService();
        $this->penangananService = new PenangananService();
        $this->stripmapService   = new StripmapService();
        $this->ruasService       = new RuasService();
    }

    /**
     * Halaman utama: Prediksi Kondisi Jalan (seluruh jaringan, per ruas)
     */
    public function index(): void
    {
        $ruasList = $this->ruasService->getAll();

        // Default tahun = tahun berjalan (tahun yang ada datanya di DB), bukan hardcode TAHUN_AWAL
        // Cari tahun terbaru yang memiliki data penanganan, fallback ke tahun saat ini atau TAHUN_AWAL+1
        $tahunDefault = (function () {
            $db   = Database::getInstance()->getConnection();
            $stmt = $db->query('SELECT MAX(tahun) as maks FROM penanganan');
            $maks = (int)($stmt->fetchColumn() ?: 0);
            $cur  = (int)date('Y');
            // Gunakan tahun terbaru penanganan, tapi batasi dalam range valid
            if ($maks >= TahunHelper::awal() && $maks <= TahunHelper::akhir()) {
                return $maks;
            }
            // Fallback: tahun sekarang jika dalam range, atau TAHUN_AWAL
            return ($cur >= TahunHelper::awal() && $cur <= TahunHelper::akhir())
                ? $cur
                : TahunHelper::awal();
        })();

        // 1. Parameter Grid Atas (Ringkasan Jaringan / KPI & Chart)
        $tahunSummaryRaw  = $_GET['tahun_summary'] ?? (isset($_GET['tahun']) && !isset($_GET['tahun_prediksi']) ? $_GET['tahun'] : (string)$tahunDefault);
        $modeSemuaSummary = ($tahunSummaryRaw === 'semua');
        $tahunSummary     = $modeSemuaSummary ? null : (int)$tahunSummaryRaw;

        // 2. Parameter Grid Bawah (Detail Per Ruas Jalan)
        $tahunBaseline    = (int)($_GET['tahun_baseline'] ?? TahunHelper::awal());
        // Default tahun_prediksi mengikuti tahun_summary jika belum ditentukan di URL
        $tahunPrediksiRaw = $_GET['tahun_prediksi'] ?? $_GET['tahun_tabel'] ?? (isset($_GET['tahun']) && !isset($_GET['tahun_summary']) ? $_GET['tahun'] : $tahunSummaryRaw);
        $modeSemuaTabel   = ($tahunPrediksiRaw === 'semua');
        $tahunPrediksi    = $modeSemuaTabel ? null : (int)$tahunPrediksiRaw;

        // Fungsi helper: hitung summary jaringan
        // $tahunPred  = tahun prediksi (sesudah penanganan s/d tahun ini)
        // $tahunBase  = tahun kondisi baseline:
        //   null = sebelum selalu dari stripmap murni (untuk Grid Atas / chart)
        //   int  = sebelum dari kondisi setelah penanganan kumulatif s/d tahunBase (Grid Bawah)
        $hitungUntukTahun = function (?int $tahunPred, ?int $tahunBase = null) use ($ruasList): array {
            $totalSebelum = ['baik' => 0.0, 'sedang' => 0.0, 'rusak_ringan' => 0.0, 'rusak_berat' => 0.0];
            $totalSesudah = ['baik' => 0.0, 'sedang' => 0.0, 'rusak_ringan' => 0.0, 'rusak_berat' => 0.0];
            $perRuasOut   = [];

            foreach ($ruasList as $ruas) {
                $ruasId       = (int)$ruas['id'];
                $stripmapList = $this->stripmapService->getByRuasId($ruasId);

                // ── SEBELUM ────────────────────────────────────────────────
                // Helper: sum raw stripmap sebagai baseline murni
                $sumStripmap = function () use ($stripmapList): array {
                    $r = ['baik' => 0.0, 'sedang' => 0.0, 'rusak_ringan' => 0.0, 'rusak_berat' => 0.0];
                    foreach ($stripmapList as $sm) {
                        foreach (['baik', 'sedang', 'rusak_ringan', 'rusak_berat'] as $k) {
                            $r[$k] += (float)($sm[$k] ?? 0);
                        }
                    }
                    return $r;
                };

                $sebelumRuas = ['baik' => 0.0, 'sedang' => 0.0, 'rusak_ringan' => 0.0, 'rusak_berat' => 0.0];
                if ($tahunBase !== null) {
                    // Grid Bawah: sebelum = kondisi setelah penanganan kumulatif s/d tahunBase
                    // Ini agar ganti Tahun Kondisi benar-benar mengubah nilai sebelum
                    $pkgBase = $this->penangananService->getByRuasIdUpTo($ruasId, $tahunBase);
                    if (!empty($pkgBase)) {
                        $smryBase    = $this->prediksiService->hitungSummary($pkgBase, $stripmapList);
                        $sebelumRuas = $smryBase['sesudah'];
                    } else {
                        $sebelumRuas = $sumStripmap();
                    }
                } else {
                    // Grid Atas / chart: sebelum = stripmap murni (referensi tetap)
                    $sebelumRuas = $sumStripmap();
                }

                // ── SESUDAH ────────────────────────────────────────────────
                // Kondisi setelah penanganan kumulatif s/d tahunPred
                if ($tahunPred !== null) {
                    $penangananList = $this->penangananService->getByRuasIdUpTo($ruasId, $tahunPred);
                } else {
                    $penangananList = $this->penangananService->getByRuasId($ruasId, null);
                }

                $summary      = $this->prediksiService->hitungSummary($penangananList, $stripmapList);
                $totalPanjang = (float)$ruas['panjang'];

                $sebelumMantapM      = $sebelumRuas['baik'] + $sebelumRuas['sedang'];
                $sebelumTidakMantapM = $sebelumRuas['rusak_ringan'] + $sebelumRuas['rusak_berat'];
                $sesudahMantapM      = $summary['sesudah']['baik'] + $summary['sesudah']['sedang'];
                $sesudahTidakMantapM = $summary['sesudah']['rusak_ringan'] + $summary['sesudah']['rusak_berat'];

                $perRuasOut[] = [
                    'id'               => $ruasId,
                    'kode_ruas'        => $ruas['kode_ruas'],
                    'nama_ruas'        => $ruas['nama_ruas'],
                    'koridor'          => $ruas['koridor'] ?? '',
                    'panjang_km'       => round($totalPanjang / 1000, 2),
                    'sebelum'          => [
                        'baik_km'          => round($sebelumRuas['baik'] / 1000, 2),
                        'sedang_km'        => round($sebelumRuas['sedang'] / 1000, 2),
                        'rusak_ringan_km'  => round($sebelumRuas['rusak_ringan'] / 1000, 2),
                        'rusak_berat_km'   => round($sebelumRuas['rusak_berat'] / 1000, 2),
                        'mantap_km'        => round($sebelumMantapM / 1000, 2),
                        'tidak_mantap_km'  => round($sebelumTidakMantapM / 1000, 2),
                        'pct_mantap'       => $totalPanjang > 0
                            ? round(($sebelumMantapM / $totalPanjang) * 100, 1) : 0,
                        'pct_tidak_mantap' => $totalPanjang > 0
                            ? round(($sebelumTidakMantapM / $totalPanjang) * 100, 1) : 0,
                    ],
                    'sesudah'          => [
                        'baik_km'          => round($summary['sesudah']['baik'] / 1000, 2),
                        'sedang_km'        => round($summary['sesudah']['sedang'] / 1000, 2),
                        'rusak_ringan_km'  => round($summary['sesudah']['rusak_ringan'] / 1000, 2),
                        'rusak_berat_km'   => round($summary['sesudah']['rusak_berat'] / 1000, 2),
                        'mantap_km'        => round($sesudahMantapM / 1000, 2),
                        'tidak_mantap_km'  => round($sesudahTidakMantapM / 1000, 2),
                        'pct_mantap'       => $totalPanjang > 0
                            ? round(($sesudahMantapM / $totalPanjang) * 100, 1) : 0,
                        'pct_tidak_mantap' => $totalPanjang > 0
                            ? round(($sesudahTidakMantapM / $totalPanjang) * 100, 1) : 0,
                    ],
                    'ada_penanganan'   => !empty($penangananList),
                    'total_penanganan' => count($penangananList),
                ];

                foreach (['baik', 'sedang', 'rusak_ringan', 'rusak_berat'] as $k) {
                    $totalSebelum[$k] += $sebelumRuas[$k];
                    $totalSesudah[$k] += $summary['sesudah'][$k];
                }
            }

            $totalPanjangJaringan = array_sum(array_column($ruasList, 'panjang'));
            $sebelumMantap        = $totalSebelum['baik'] + $totalSebelum['sedang'];
            $sebelumTidakMantap   = $totalSebelum['rusak_ringan'] + $totalSebelum['rusak_berat'];
            $sesudahMantap        = $totalSesudah['baik'] + $totalSesudah['sedang'];
            $sesudahTidakMantap   = $totalSesudah['rusak_ringan'] + $totalSesudah['rusak_berat'];

            return [
                'perRuas'      => $perRuasOut,
                'totalSebelum' => [
                    'baik_km'          => round($totalSebelum['baik'] / 1000, 2),
                    'sedang_km'        => round($totalSebelum['sedang'] / 1000, 2),
                    'rusak_ringan_km'  => round($totalSebelum['rusak_ringan'] / 1000, 2),
                    'rusak_berat_km'   => round($totalSebelum['rusak_berat'] / 1000, 2),
                    'mantap_km'        => round($sebelumMantap / 1000, 2),
                    'tidak_mantap_km'  => round($sebelumTidakMantap / 1000, 2),
                    'pct_mantap'       => $totalPanjangJaringan > 0
                        ? round(($sebelumMantap / $totalPanjangJaringan) * 100, 1) : 0,
                    'pct_tidak_mantap' => $totalPanjangJaringan > 0
                        ? round(($sebelumTidakMantap / $totalPanjangJaringan) * 100, 1) : 0,
                ],
                'totalSesudah' => [
                    'baik_km'          => round($totalSesudah['baik'] / 1000, 2),
                    'sedang_km'        => round($totalSesudah['sedang'] / 1000, 2),
                    'rusak_ringan_km'  => round($totalSesudah['rusak_ringan'] / 1000, 2),
                    'rusak_berat_km'   => round($totalSesudah['rusak_berat'] / 1000, 2),
                    'mantap_km'        => round($sesudahMantap / 1000, 2),
                    'tidak_mantap_km'  => round($sesudahTidakMantap / 1000, 2),
                    'pct_mantap'       => $totalPanjangJaringan > 0
                        ? round(($sesudahMantap / $totalPanjangJaringan) * 100, 1) : 0,
                    'pct_tidak_mantap' => $totalPanjangJaringan > 0
                        ? round(($sesudahTidakMantap / $totalPanjangJaringan) * 100, 1) : 0,
                ],
                'totalPanjangKm' => round($totalPanjangJaringan / 1000, 2),
            ];
        };

        // Hitung data Grid Atas (Ringkasan KPI & Chart Distribusi)
        // sebelum = stripmap murni (referensi tetap, tanpa tahunBase)
        $hasilSummary = $hitungUntukTahun($tahunSummary);

        // Hitung data Grid Bawah (Tabel Detail Per Ruas)
        // sebelum = kondisi setelah penanganan s/d tahunBaseline (Tahun Kondisi yang dipilih user)
        $hasilTabel   = $hitungUntukTahun($tahunPrediksi, $tahunBaseline);

        // Jika mode semua pada Grid Atas: hitung juga semua tahun untuk chart multi-tahun
        $allYearsData = [];
        if ($modeSemuaSummary) {
            foreach (TahunHelper::getList() as $thn) {
                $h = $hitungUntukTahun($thn);
                $allYearsData[$thn] = [
                    'pct_sebelum' => $h['totalSebelum']['pct_mantap'],
                    'pct_sesudah' => $h['totalSesudah']['pct_mantap'],
                    'sebelum'     => $h['totalSebelum'],
                    'sesudah'     => $h['totalSesudah'],
                ];
            }
        }

        $data = [
            'title'                  => 'Prediksi Kondisi Jalan Setelah Penanganan',
            
            // Variabel Grid Atas
            'tahunSummaryRaw'        => $tahunSummaryRaw,
            'tahunPenanganan'        => $modeSemuaSummary ? 'semua' : $tahunSummary,
            'modeSemua'              => $modeSemuaSummary,
            'totalSebelum'           => $hasilSummary['totalSebelum'],
            'totalSesudah'           => $hasilSummary['totalSesudah'],
            'totalPanjangKm'         => $hasilSummary['totalPanjangKm'],
            'allYearsData'           => $allYearsData,
            
            // Variabel Grid Bawah (Tabel)
            'tahunBaseline'          => $tahunBaseline,
            'tahunPrediksiRaw'       => $tahunPrediksiRaw,
            'tahunPrediksi'          => $modeSemuaTabel ? 'semua' : $tahunPrediksi,
            'perRuas'                => $hasilTabel['perRuas'],
            'totalSebelumTabel'      => $hasilTabel['totalSebelum'],
            'totalSesudahTabel'      => $hasilTabel['totalSesudah'],

            'pelaksanaLabels'        => PrediksiService::PELAKSANA_LABELS,
            'kondisiLabels'          => PrediksiService::KONDISI_LABELS,
            'kondisiColors'          => PrediksiService::KONDISI_COLORS,
        ];

        view('layouts.app', array_merge($data, ['content' => 'rekap.prediksi']));
    }

    /**
     * Detail prediksi untuk satu ruas jalan (AJAX / halaman detail)
     * Mendukung pencarian berdasarkan numeric ID ataupun kode_ruas string (contoh: "008")
     */
    public function detail($ruasId): void
    {
        $ruas = null;
        if (is_numeric($ruasId)) {
            $ruas = $this->ruasService->findById((int)$ruasId);
        }
        if (!$ruas) {
            $ruas = $this->ruasService->findByKode((string)$ruasId);
        }
        if (!$ruas) {
            flash('error', 'Ruas jalan tidak ditemukan.');
            redirect(base_url('rekap/prediksi'));
            return;
        }

        $id = (int)$ruas['id'];
        $tahunRaw = $_GET['tahun'] ?? '2026';
        $modeSemua = ($tahunRaw === 'semua');
        $tahunPenanganan = $modeSemua ? null : (int)$tahunRaw;

        // Ambil penanganan untuk tahun aktif (kumulatif jika tahun > 2025)
        if ($tahunPenanganan === 2025) {
            $penangananList = $this->penangananService->getByRuasId($id, 2025);
        } elseif ($tahunPenanganan !== null && $tahunPenanganan > 2025) {
            $penangananList = $this->penangananService->getByRuasIdUpTo($id, $tahunPenanganan);
        } else {
            $penangananList = $this->penangananService->getByRuasId($id, null);
        }
        $stripmapList   = $this->stripmapService->getByRuasId($id);

        $summary = $this->prediksiService->hitungSummary($penangananList, $stripmapList);

        // Hitung Base 2025
        $totalPanjangM = (float)$ruas['panjang'];
        $baseMantapM = (float)($summary['sebelum']['baik'] ?? 0) + (float)($summary['sebelum']['sedang'] ?? 0);
        $basePctMantap = $totalPanjangM > 0 ? round(($baseMantapM / $totalPanjangM) * 100, 1) : 0;

        // Hitung multi-tahun (2025 s/d 2029) untuk chart batang vertikal
        $yearlyRuasData = [];
        $yearlyRuasData[2025] = [
            'tahun'       => 2025,
            'tipe'        => 'Base',
            'pct_mantap'  => $basePctMantap,
            'mantap_km'   => round($baseMantapM / 1000, 2),
            'panjang_km'  => round($totalPanjangM / 1000, 2),
            'delta_pct'   => 0.0,
            'delta_km'    => 0.0,
            'kondisi_km'  => [
                'baik'         => round(($summary['sebelum']['baik'] ?? 0) / 1000, 2),
                'sedang'       => round(($summary['sebelum']['sedang'] ?? 0) / 1000, 2),
                'rusak_ringan' => round(($summary['sebelum']['rusak_ringan'] ?? 0) / 1000, 2),
                'rusak_berat'  => round(($summary['sebelum']['rusak_berat'] ?? 0) / 1000, 2),
            ],
            'total_paket' => 0,
        ];

        foreach (TahunHelper::getList() as $thn) {
            if ($thn <= 2025) continue;
            $pkgList = $this->penangananService->getByRuasIdUpTo($id, $thn);
            $smry    = $this->prediksiService->hitungSummary($pkgList, $stripmapList);
            $mMantap = (float)($smry['sesudah']['baik'] ?? 0) + (float)($smry['sesudah']['sedang'] ?? 0);
            $pMantap = $totalPanjangM > 0 ? round(($mMantap / $totalPanjangM) * 100, 1) : 0;
            $kMantap = round($mMantap / 1000, 2);

            $yearlyRuasData[$thn] = [
                'tahun'       => $thn,
                'tipe'        => 'Prediksi',
                'pct_mantap'  => $pMantap,
                'mantap_km'   => $kMantap,
                'panjang_km'  => round($totalPanjangM / 1000, 2),
                'delta_pct'   => round($pMantap - $basePctMantap, 1),
                'delta_km'    => round($kMantap - round($baseMantapM / 1000, 2), 2),
                'kondisi_km'  => [
                    'baik'         => round(($smry['sesudah']['baik'] ?? 0) / 1000, 2),
                    'sedang'       => round(($smry['sesudah']['sedang'] ?? 0) / 1000, 2),
                    'rusak_ringan' => round(($smry['sesudah']['rusak_ringan'] ?? 0) / 1000, 2),
                    'rusak_berat'  => round(($smry['sesudah']['rusak_berat'] ?? 0) / 1000, 2),
                ],
                'total_paket' => count($pkgList),
            ];
        }

        $data = [
            'title'             => 'Detail Prediksi — ' . $ruas['nama_ruas'],
            'ruas'              => $ruas,
            'tahunPenanganan'   => $modeSemua ? 'semua' : $tahunPenanganan,
            'modeSemua'         => $modeSemua,
            'penangananList'    => $penangananList,
            'summary'           => $summary,
            'basePctMantap'     => $basePctMantap,
            'baseMantapKm'      => round($baseMantapM / 1000, 2),
            'yearlyRuasData'    => $yearlyRuasData,
            'pelaksanaLabels'   => PrediksiService::PELAKSANA_LABELS,
            'kondisiLabels'     => PrediksiService::KONDISI_LABELS,
            'kondisiColors'     => PrediksiService::KONDISI_COLORS,
        ];

        view('layouts.app', array_merge($data, ['content' => 'rekap.prediksi_detail']));
    }
}
