<?php

/**
 * ============================================================
 * Controller: Dashboard
 * ============================================================
 */

class DashboardController
{
    public function index(): void
    {
        $selectedTahun  = TahunHelper::fromRequest();
        $availableTahun = TahunHelper::getList();

        $ruasService = new RuasService();
        $ruasList    = $ruasService->getAll();

        $stripmapService   = new StripmapService();
        $globalSummary     = $stripmapService->getGlobalSummary(null, $selectedTahun);

        $perkerasanService = new PerkerasanService();
        $perkerasanSummary = $perkerasanService->getGlobalSummary(null, $selectedTahun);

        // Hitung semua statistik km & persentase via helper bersama
        $stats = build_road_summary_stats($ruasList, $globalSummary, $perkerasanSummary);

        // 1. Chart Kabupaten
        $summaryByKabupaten = $stripmapService->getSummaryByKabupaten($selectedTahun);
        $kabupatenChartData = [];
        foreach ($summaryByKabupaten as $row) {
            $totalP       = (float)$row['total_panjang'];
            $mantapM      = (float)$row['total_mantap'];
            $tidakMantapM = (float)$row['total_tidak_mantap'];
            $kabupatenChartData[] = [
                'label'            => $row['kabupaten_kota'],
                'short_label'      => Uptd::getShortName($row['kabupaten_kota']),
                'mantap_km'        => round($mantapM / 1000, 2),
                'tidak_mantap_km'  => round($tidakMantapM / 1000, 2),
                'pct_mantap'       => $totalP > 0 ? round(($mantapM / $totalP) * 100, 1) : 0,
                'pct_tidak_mantap' => $totalP > 0 ? round(($tidakMantapM / $totalP) * 100, 1) : 0,
            ];
        }

        // 2. Chart Koridor
        $summaryByKoridor = $stripmapService->getSummaryByKoridor($selectedTahun);
        $koridorChartData = [];
        foreach ($summaryByKoridor as $row) {
            $totalP       = (float)$row['total_panjang'];
            $mantapM      = (float)$row['total_mantap'];
            $tidakMantapM = (float)$row['total_tidak_mantap'];
            $label        = is_numeric($row['koridor']) ? 'Koridor ' . $row['koridor'] : $row['koridor'];
            $koridorChartData[] = [
                'label'            => $label,
                'mantap_km'        => round($mantapM / 1000, 2),
                'tidak_mantap_km'  => round($tidakMantapM / 1000, 2),
                'pct_mantap'       => $totalP > 0 ? round(($mantapM / $totalP) * 100, 1) : 0,
                'pct_tidak_mantap' => $totalP > 0 ? round(($tidakMantapM / $totalP) * 100, 1) : 0,
            ];
        }

        // 3. Chart UPTD
        $uptdMaster = Uptd::all();
        $uptdStats  = [];
        foreach ($uptdMaster as $uptdKey => $kabList) {
            $uptdStats[$uptdKey] = ['panjang' => 0, 'mantap' => 0, 'tidak_mantap' => 0];
        }

        foreach ($summaryByKabupaten as $row) {
            $totalP       = (float)$row['total_panjang'];
            $mantapM      = (float)$row['total_mantap'];
            $tidakMantapM = (float)$row['total_tidak_mantap'];
            $matchedUptds = Uptd::getUptdByKabupaten($row['kabupaten_kota']);

            foreach ($matchedUptds as $u) {
                if (isset($uptdStats[$u])) {
                    $uptdStats[$u]['panjang']      += $totalP;
                    $uptdStats[$u]['mantap']       += $mantapM;
                    $uptdStats[$u]['tidak_mantap'] += $tidakMantapM;
                }
            }
        }

        $uptdChartData = [];
        foreach ($uptdStats as $uptdName => $stat) {
            $totalP       = (float)$stat['panjang'];
            $mantapM      = (float)$stat['mantap'];
            $tidakMantapM = (float)$stat['tidak_mantap'];
            $uptdChartData[] = [
                'label'            => $uptdName,
                'mantap_km'        => round($mantapM / 1000, 2),
                'tidak_mantap_km'  => round($tidakMantapM / 1000, 2),
                'pct_mantap'       => $totalP > 0 ? round(($mantapM / $totalP) * 100, 1) : 0,
                'pct_tidak_mantap' => $totalP > 0 ? round(($tidakMantapM / $totalP) * 100, 1) : 0,
            ];
        }

        $penangananService   = new PenangananService();
        $penangananSummary   = $penangananService->getGlobalSummary($selectedTahun);
        $penangananByKab     = $penangananService->getSummaryByKabupaten($selectedTahun);

        $penangananStats = [
            'selectedTahun'       => $selectedTahun,
            'availableTahun'      => $availableTahun,
            'penangananYears'     => $availableTahun,
            'penangananSummary'   => $penangananSummary,
            'penangananTotalKm'   => round(((float)($penangananSummary['total_panjang'] ?? 0)) / 1000, 2),
            'penangananRencanaKm' => round(((float)($penangananSummary['total_rencana'] ?? 0)) / 1000, 2),
            'penangananProsesKm'  => round(((float)($penangananSummary['total_proses'] ?? 0)) / 1000, 2),
            'penangananSelesaiKm' => round(((float)($penangananSummary['total_selesai'] ?? 0)) / 1000, 2),
            'penangananAnggaran'  => (float)($penangananSummary['total_anggaran'] ?? 0),
            'penangananByKab'     => $penangananByKab,
        ];

        $data = array_merge($stats, $penangananStats, [
            'title'              => 'Dashboard',
            'selectedTahun'      => $selectedTahun,
            'availableTahun'     => $availableTahun,
            'totalRuas'          => count($ruasList),
            'ruasList'           => $ruasList,
            'kabupatenChartData' => $kabupatenChartData,
            'koridorChartData'   => $koridorChartData,
            'uptdChartData'      => $uptdChartData,
        ]);

        view('layouts.app', array_merge($data, ['content' => 'dashboard.index']));
    }

    public function detail(): void
    {
        $kondisiParam = strtolower(trim($_GET['kondisi'] ?? 'rusak_ringan'));
        $allowedKondisi = ['baik', 'sedang', 'rusak_ringan', 'rusak_berat', 'mantap', 'tidak_mantap'];
        if (!in_array($kondisiParam, $allowedKondisi, true)) {
            $kondisiParam = 'rusak_ringan';
        }

        $selectedTahun   = TahunHelper::fromRequest();
        $stripmapService = new StripmapService();
        $summaryPerRuas  = $stripmapService->getConditionSummaryPerRuas();
        $globalSummary   = $stripmapService->getGlobalSummary(null, $selectedTahun);

        $ruasService = new RuasService();
        $ruasList    = $ruasService->getAll();

        $kondisiMeta = [
            'rusak_ringan' => [
                'title'       => 'Detail Kondisi Rusak Ringan',
                'label'       => 'Rusak Ringan',
                'color'       => 'orange',
                'badge_bg'    => 'bg-orange-100',
                'badge_text'  => 'text-orange-800',
                'card_bg'     => '#fff7ed',
                'border'      => '#ffedd5',
                'accent'      => '#f97316',
            ],
            'rusak_berat' => [
                'title'       => 'Detail Kondisi Rusak Berat',
                'label'       => 'Rusak Berat',
                'color'       => 'red',
                'badge_bg'    => 'bg-red-100',
                'badge_text'  => 'text-red-800',
                'card_bg'     => '#fef2f2',
                'border'      => '#fee2e2',
                'accent'      => '#ef4444',
            ],
            'baik' => [
                'title'       => 'Detail Kondisi Baik',
                'label'       => 'Baik',
                'color'       => 'emerald',
                'badge_bg'    => 'bg-emerald-100',
                'badge_text'  => 'text-emerald-800',
                'card_bg'     => '#f0fdf4',
                'border'      => '#d1fae5',
                'accent'      => '#10b981',
            ],
            'sedang' => [
                'title'       => 'Detail Kondisi Sedang',
                'label'       => 'Sedang',
                'color'       => 'yellow',
                'badge_bg'    => 'bg-yellow-100',
                'badge_text'  => 'text-yellow-800',
                'card_bg'     => '#fefce8',
                'border'      => '#fef08a',
                'accent'      => '#facc15',
            ],
            'mantap' => [
                'title'       => 'Detail Jalan Mantap (Baik + Sedang)',
                'label'       => 'Mantap',
                'color'       => 'emerald',
                'badge_bg'    => 'bg-emerald-100',
                'badge_text'  => 'text-emerald-800',
                'card_bg'     => '#f0fdf4',
                'border'      => '#d1fae5',
                'accent'      => '#10b981',
            ],
            'tidak_mantap' => [
                'title'       => 'Detail Jalan Tidak Mantap (R. Ringan + R. Berat)',
                'label'       => 'Tidak Mantap',
                'color'       => 'rose',
                'badge_bg'    => 'bg-rose-100',
                'badge_text'  => 'text-rose-800',
                'card_bg'     => '#fff1f2',
                'border'      => '#ffe4e6',
                'accent'      => '#f43f5e',
            ],
        ];

        $data = [
            'title'           => $kondisiMeta[$kondisiParam]['title'],
            'selectedKondisi' => $kondisiParam,
            'kondisiMeta'     => $kondisiMeta,
            'summaryPerRuas'  => $summaryPerRuas,
            'globalSummary'   => $globalSummary,
            'totalRuas'       => count($ruasList),
        ];

        view('layouts.app', array_merge($data, ['content' => 'dashboard.detail']));
    }
}
