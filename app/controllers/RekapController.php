<?php

/**
 * ============================================================
 * Controller: RekapController
 * ============================================================
 * Menyajikan laporan Rekapitulasi Eksekutif (Kemantapan & Perkerasan).
 */

class RekapController
{
    private RuasService $ruasService;
    private StripmapService $stripmapService;
    private PerkerasanService $perkerasanService;

    public function __construct()
    {
        $this->ruasService       = new RuasService();
        $this->stripmapService   = new StripmapService();
        $this->perkerasanService = new PerkerasanService();
    }

    /**
     * Halaman Rekapitulasi Kemantapan Jalan (Kondisi Jalan)
     */
    public function kemantapan(): void
    {
        // Filter tahun — sumber tunggal via TahunHelper
        $selectedTahun  = TahunHelper::fromRequest();
        $availableTahun = TahunHelper::getList();


        $ruasList          = $this->ruasService->getAll();
        $globalSummary     = $this->stripmapService->getGlobalSummary(null, $selectedTahun);
        $perkerasanSummary = $this->perkerasanService->getGlobalSummary(null, $selectedTahun);
        $stats             = build_road_summary_stats($ruasList, $globalSummary, $perkerasanSummary);

        // Data Ringkasan Per Ruas Jalan
        $perRuas = $this->stripmapService->getConditionSummaryPerRuas($selectedTahun);

        // 1. Rekap Per Kabupaten / Kota
        $summaryByKabupaten = $this->stripmapService->getSummaryByKabupaten($selectedTahun);
        $rekapKabupaten = [];
        foreach ($summaryByKabupaten as $row) {
            $totalP = (float)$row['total_panjang'];
            $baik   = (float)$row['total_baik'];
            $sedang = (float)$row['total_sedang'];
            $rr     = (float)$row['total_rusak_ringan'];
            $rb     = (float)$row['total_rusak_berat'];
            $mantap = $baik + $sedang;
            $tm     = $rr + $rb;

            $rekapKabupaten[] = [
                'nama'             => $row['kabupaten_kota'],
                'short_name'       => Uptd::getShortName($row['kabupaten_kota']),
                'uptd'             => Uptd::getUptdString($row['kabupaten_kota']),
                'total_panjang_km' => round($totalP / 1000, 2),
                'baik_km'          => round($baik / 1000, 2),
                'sedang_km'        => round($sedang / 1000, 2),
                'rusak_ringan_km'  => round($rr / 1000, 2),
                'rusak_berat_km'   => round($rb / 1000, 2),
                'mantap_km'        => round($mantap / 1000, 2),
                'tidak_mantap_km'  => round($tm / 1000, 2),
                'pct_mantap'       => $totalP > 0 ? round(($mantap / $totalP) * 100, 1) : 0,
                'pct_tidak_mantap' => $totalP > 0 ? round(($tm / $totalP) * 100, 1) : 0,
            ];
        }

        // 2. Rekap Per UPTD
        $uptdMaster = Uptd::all();
        $uptdStats  = [];
        foreach ($uptdMaster as $uptdKey => $kabList) {
            $uptdStats[$uptdKey] = [
                'total' => 0, 'baik' => 0, 'sedang' => 0, 'rr' => 0, 'rb' => 0
            ];
        }

        foreach ($summaryByKabupaten as $row) {
            $totalP       = (float)$row['total_panjang'];
            $baik         = (float)$row['total_baik'];
            $sedang       = (float)$row['total_sedang'];
            $rr           = (float)$row['total_rusak_ringan'];
            $rb           = (float)$row['total_rusak_berat'];
            $matchedUptds = Uptd::getUptdByKabupaten($row['kabupaten_kota']);

            foreach ($matchedUptds as $u) {
                if (isset($uptdStats[$u])) {
                    $uptdStats[$u]['total']  += $totalP;
                    $uptdStats[$u]['baik']   += $baik;
                    $uptdStats[$u]['sedang'] += $sedang;
                    $uptdStats[$u]['rr']     += $rr;
                    $uptdStats[$u]['rb']     += $rb;
                }
            }
        }

        $rekapUptd = [];
        foreach ($uptdStats as $uptdName => $s) {
            $tot    = (float)$s['total'];
            $b      = (float)$s['baik'];
            $sd     = (float)$s['sedang'];
            $rr     = (float)$s['rr'];
            $rb     = (float)$s['rb'];
            $mantap = $b + $sd;
            $tm     = $rr + $rb;

            $rekapUptd[] = [
                'nama'             => $uptdName,
                'kabupaten_list'   => implode(', ', Uptd::getKabupatenByUptd($uptdName)),
                'total_panjang_km' => round($tot / 1000, 2),
                'baik_km'          => round($b / 1000, 2),
                'sedang_km'        => round($sd / 1000, 2),
                'rusak_ringan_km'  => round($rr / 1000, 2),
                'rusak_berat_km'   => round($rb / 1000, 2),
                'mantap_km'        => round($mantap / 1000, 2),
                'tidak_mantap_km'  => round($tm / 1000, 2),
                'pct_mantap'       => $tot > 0 ? round(($mantap / $tot) * 100, 1) : 0,
                'pct_tidak_mantap' => $tot > 0 ? round(($tm / $tot) * 100, 1) : 0,
            ];
        }

        // 3. Rekap Per Koridor
        $summaryByKoridor = $this->stripmapService->getSummaryByKoridor($selectedTahun);
        $rekapKoridor = [];
        foreach ($summaryByKoridor as $row) {
            $totalP = (float)$row['total_panjang'];
            $mantap = (float)$row['total_mantap'];
            $tm     = (float)$row['total_tidak_mantap'];
            $label  = is_numeric($row['koridor']) ? 'Koridor ' . $row['koridor'] : $row['koridor'];

            $rekapKoridor[] = [
                'nama'             => $label,
                'total_panjang_km' => round($totalP / 1000, 2),
                'mantap_km'        => round($mantap / 1000, 2),
                'tidak_mantap_km'  => round($tm / 1000, 2),
                'pct_mantap'       => $totalP > 0 ? round(($mantap / $totalP) * 100, 1) : 0,
                'pct_tidak_mantap' => $totalP > 0 ? round(($tm / $totalP) * 100, 1) : 0,
            ];
        }

        $data = array_merge($stats, [
            'title'          => 'Rekapitulasi Kemantapan Jalan',
            'selectedTahun'  => $selectedTahun,
            'availableTahun' => $availableTahun,
            'rekapKabupaten' => $rekapKabupaten,
            'rekapUptd'      => $rekapUptd,
            'rekapKoridor'   => $rekapKoridor,
            'perRuas'        => $perRuas,
        ]);

        view('layouts.app', array_merge($data, ['content' => 'rekap.kemantapan']));
    }

    /**
     * Halaman Rekapitulasi Jenis Perkerasan Jalan
     */
    public function perkerasan(): void
    {
        // Filter tahun — sumber tunggal via TahunHelper
        $selectedTahun  = TahunHelper::fromRequest();
        $availableTahun = TahunHelper::getList();


        $ruasList          = $this->ruasService->getAll();
        $globalSummary     = $this->stripmapService->getGlobalSummary(null, null);
        $perkerasanSummary = $this->perkerasanService->getGlobalSummary(null, $selectedTahun);
        $stats             = build_road_summary_stats($ruasList, $globalSummary, $perkerasanSummary);

        // Agregasi Perkerasan Per Ruas (filtered by tahun)
        $ruasPerkerasanList = [];
        foreach ($ruasList as $r) {
            $pkSum = $this->perkerasanService->getSummary($r['id']);
            $tot   = (float)($pkSum['total_panjang'] ?? 0);
            $rigid = (float)($pkSum['total_rigid'] ?? 0);
            $aspal = (float)($pkSum['total_aspal'] ?? 0);
            $ag    = (float)($pkSum['total_agregat_tanah'] ?? 0);
            $bt    = (float)($pkSum['total_belum_tembus'] ?? 0);

            $ruasPerkerasanList[] = [
                'id'               => $r['id'],
                'kode_ruas'        => $r['kode_ruas'],
                'nama_ruas'        => $r['nama_ruas'],
                'kabupaten_kota'   => $r['kabupaten_kota'] ?? '',
                'koridor'          => $r['koridor'] ?? '',
                'uptd'             => Uptd::getUptdString($r['kabupaten_kota'] ?? ''),
                'total_panjang_km' => round($r['panjang'] / 1000, 2),
                'rigid_km'         => round($rigid / 1000, 2),
                'aspal_km'         => round($aspal / 1000, 2),
                'agregat_tanah_km' => round($ag / 1000, 2),
                'belum_tembus_km'  => round($bt / 1000, 2),
                'pct_rigid'        => $tot > 0 ? round(($rigid / $tot) * 100, 1) : 0,
                'pct_aspal'        => $tot > 0 ? round(($aspal / $tot) * 100, 1) : 0,
                'pct_agregat_tanah'=> $tot > 0 ? round(($ag / $tot) * 100, 1) : 0,
                'pct_belum_tembus' => $tot > 0 ? round(($bt / $tot) * 100, 1) : 0,
            ];
        }

        // Agregasi Per UPTD
        $uptdMaster = Uptd::all();
        $rekapUptd  = [];
        foreach ($uptdMaster as $uptdKey => $kabList) {
            $tot   = 0;
            $rigid = 0;
            $aspal = 0;
            $ag    = 0;
            $bt    = 0;

            foreach ($ruasPerkerasanList as $item) {
                $matchedUptds = Uptd::getUptdByKabupaten($item['kabupaten_kota']);
                if (in_array($uptdKey, $matchedUptds, true)) {
                    $tot   += $item['total_panjang_km'] * 1000;
                    $rigid += $item['rigid_km'] * 1000;
                    $aspal += $item['aspal_km'] * 1000;
                    $ag    += $item['agregat_tanah_km'] * 1000;
                    $bt    += $item['belum_tembus_km'] * 1000;
                }
            }

            $rekapUptd[] = [
                'nama'             => $uptdKey,
                'kabupaten_list'   => implode(', ', $kabList),
                'total_panjang_km' => round($tot / 1000, 2),
                'rigid_km'         => round($rigid / 1000, 2),
                'aspal_km'         => round($aspal / 1000, 2),
                'agregat_tanah_km' => round($ag / 1000, 2),
                'belum_tembus_km'  => round($bt / 1000, 2),
                'pct_rigid'        => $tot > 0 ? round(($rigid / $tot) * 100, 1) : 0,
                'pct_aspal'        => $tot > 0 ? round(($aspal / $tot) * 100, 1) : 0,
                'pct_agregat_tanah'=> $tot > 0 ? round(($ag / $tot) * 100, 1) : 0,
                'pct_belum_tembus' => $tot > 0 ? round(($bt / $tot) * 100, 1) : 0,
            ];
        }

        // Agregasi Per Kabupaten
        $kabMap = [];
        foreach ($ruasPerkerasanList as $item) {
            $kab = $item['kabupaten_kota'] ?: 'Lainnya';
            if (!isset($kabMap[$kab])) {
                $kabMap[$kab] = ['tot' => 0, 'rigid' => 0, 'aspal' => 0, 'ag' => 0, 'bt' => 0];
            }
            $kabMap[$kab]['tot']   += $item['total_panjang_km'] * 1000;
            $kabMap[$kab]['rigid'] += $item['rigid_km'] * 1000;
            $kabMap[$kab]['aspal'] += $item['aspal_km'] * 1000;
            $kabMap[$kab]['ag']    += $item['agregat_tanah_km'] * 1000;
            $kabMap[$kab]['bt']    += $item['belum_tembus_km'] * 1000;
        }

        $rekapKabupaten = [];
        foreach ($kabMap as $kabName => $m) {
            $tot   = $m['tot'];
            $rigid = $m['rigid'];
            $aspal = $m['aspal'];
            $ag    = $m['ag'];
            $bt    = $m['bt'];

            $rekapKabupaten[] = [
                'nama'             => $kabName,
                'uptd'             => Uptd::getUptdString($kabName),
                'total_panjang_km' => round($tot / 1000, 2),
                'rigid_km'         => round($rigid / 1000, 2),
                'aspal_km'         => round($aspal / 1000, 2),
                'agregat_tanah_km' => round($ag / 1000, 2),
                'belum_tembus_km'  => round($bt / 1000, 2),
                'pct_rigid'        => $tot > 0 ? round(($rigid / $tot) * 100, 1) : 0,
                'pct_aspal'        => $tot > 0 ? round(($aspal / $tot) * 100, 1) : 0,
                'pct_agregat_tanah'=> $tot > 0 ? round(($ag / $tot) * 100, 1) : 0,
                'pct_belum_tembus' => $tot > 0 ? round(($bt / $tot) * 100, 1) : 0,
            ];
        }

        $data = array_merge($stats, [
            'title'          => 'Rekapitulasi Jenis Perkerasan Jalan',
            'selectedTahun'  => $selectedTahun,
            'availableTahun' => $availableTahun,
            'rekapUptd'      => $rekapUptd,
            'rekapKabupaten' => $rekapKabupaten,
            'ruasPerkerasan' => $ruasPerkerasanList,
        ]);

        view('layouts.app', array_merge($data, ['content' => 'rekap.perkerasan']));
    }
}
