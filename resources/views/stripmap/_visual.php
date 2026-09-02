<!-- ============================================================ -->
<!-- Komponen Strip Map Visual, Perkerasan & Penanganan Multi-Layer -->
<!-- Digunakan di: stripmap/index, export/ruas_jalan, ruas/show      -->
<!-- ============================================================ -->

<?php
    $stripmaps = $stripmaps ?? [];
    $summary   = $summary ?? [];

    $totalPanjang = (float)($summary['total_panjang'] ?? 0);
    $totalBaik    = (float)($summary['total_baik'] ?? 0);
    $totalSedang  = (float)($summary['total_sedang'] ?? 0);
    $totalRR      = (float)($summary['total_rusak_ringan'] ?? 0);
    $totalRB      = (float)($summary['total_rusak_berat'] ?? 0);

    // Fallback jika summary bernilai 0 tapi array stripmaps memiliki data
    if ($totalPanjang <= 0 && !empty($stripmaps)) {
        foreach ($stripmaps as $sm) {
            $totalPanjang += (float)($sm['panjang'] ?? 0);
            $totalBaik    += (float)($sm['baik'] ?? 0);
            $totalSedang  += (float)($sm['sedang'] ?? 0);
            $totalRR      += (float)($sm['rusak_ringan'] ?? 0);
            $totalRB      += (float)($sm['rusak_berat'] ?? 0);
        }
    }

    $pctBaik   = $totalPanjang > 0 ? ($totalBaik / $totalPanjang) * 100 : 0;
    $pctSedang = $totalPanjang > 0 ? ($totalSedang / $totalPanjang) * 100 : 0;
    $pctRR     = $totalPanjang > 0 ? ($totalRR / $totalPanjang) * 100 : 0;
    $pctRB     = $totalPanjang > 0 ? ($totalRB / $totalPanjang) * 100 : 0;

    $totalMantap      = $totalBaik + $totalSedang;
    $totalTidakMantap = $totalRR + $totalRB;

    $pctMantap      = $totalPanjang > 0 ? ($totalMantap / $totalPanjang) * 100 : 0;
    $pctTidakMantap = $totalPanjang > 0 ? ($totalTidakMantap / $totalPanjang) * 100 : 0;

    // Data Perkerasan
    $perkerasans            = $perkerasans ?? [];
    $summaryPerkerasan      = $summaryPerkerasan ?? [];
    $totalRigid             = (float)($summaryPerkerasan['total_rigid'] ?? 0);
    $totalAspal             = (float)($summaryPerkerasan['total_aspal'] ?? 0);
    $totalAgregatTanah      = (float)($summaryPerkerasan['total_agregat_tanah'] ?? 0);
    $totalBelumTembus       = (float)($summaryPerkerasan['total_belum_tembus'] ?? 0);
    $totalPanjangPerkerasan = (float)($summaryPerkerasan['total_panjang'] ?? 0);

    // Fallback jika summary perkerasan bernilai 0 tapi array perkerasans memiliki data
    if ($totalPanjangPerkerasan <= 0 && !empty($perkerasans)) {
        foreach ($perkerasans as $pk) {
            $totalPanjangPerkerasan += (float)($pk['panjang'] ?? 0);
            $totalRigid             += (float)($pk['rigid'] ?? 0);
            $totalAspal             += (float)($pk['aspal'] ?? 0);
            $totalAgregatTanah      += (float)($pk['agregat_tanah'] ?? 0);
            $totalBelumTembus       += (float)($pk['belum_tembus'] ?? 0);
        }
    }

    $pctRigid        = $totalPanjangPerkerasan > 0 ? ($totalRigid / $totalPanjangPerkerasan) * 100 : 0;
    $pctAspal        = $totalPanjangPerkerasan > 0 ? ($totalAspal / $totalPanjangPerkerasan) * 100 : 0;
    $pctAgregatTanah = $totalPanjangPerkerasan > 0 ? ($totalAgregatTanah / $totalPanjangPerkerasan) * 100 : 0;
    $pctBelumTembus  = $totalPanjangPerkerasan > 0 ? ($totalBelumTembus / $totalPanjangPerkerasan) * 100 : 0;

    // Data Penanganan Jalan
    $penanganans            = $penanganans ?? [];
    $penangananSummary      = $penangananSummary ?? [];
    $penangananYears        = $penangananYears ?? [];
    $totalPanjangPenanganan = (float)($penangananSummary['total_panjang'] ?? 0);
    $totalRencana           = (float)($penangananSummary['total_rencana'] ?? 0);
    $totalProses            = (float)($penangananSummary['total_proses'] ?? 0);
    $totalSelesai           = (float)($penangananSummary['total_selesai'] ?? 0);
    $totalAnggaran          = (float)($penangananSummary['total_anggaran'] ?? 0);

    if ($totalPanjangPenanganan <= 0 && !empty($penanganans)) {
        foreach ($penanganans as $pn) {
            $pLen = (float)($pn['panjang'] ?? 0);
            $totalPanjangPenanganan += $pLen;
            $st = $pn['status'] ?? 'rencana';
            if ($st === 'rencana') $totalRencana += $pLen;
            elseif ($st === 'proses') $totalProses += $pLen;
            elseif ($st === 'selesai') $totalSelesai += $pLen;
            $totalAnggaran += (float)($pn['anggaran'] ?? 0);
        }
    }

    $pctRencana = $totalPanjangPenanganan > 0 ? ($totalRencana / $totalPanjangPenanganan) * 100 : 0;
    $pctProses  = $totalPanjangPenanganan > 0 ? ($totalProses / $totalPanjangPenanganan) * 100 : 0;
    $pctSelesai = $totalPanjangPenanganan > 0 ? ($totalSelesai / $totalPanjangPenanganan) * 100 : 0;

    // Agregasi Rencana Target Kondisi & Perkerasan dari Paket Penanganan
    $totalPnTargetBaik   = 0.0;
    $totalPnTargetSedang = 0.0;
    $totalPnTargetRR     = 0.0;
    $totalPnTargetRB     = 0.0;

    $totalPnRigid        = 0.0;
    $totalPnAspal        = 0.0;
    $totalPnAgregat      = 0.0;
    $totalPnBelumTembus  = 0.0;

    foreach ($penanganans as $pn) {
        $pLen = (float)($pn['panjang'] ?? 0);
        if ($pLen <= 0) continue;

        // Target Kondisi
        $cond = strtolower($pn['kondisi_prediksi'] ?? 'baik');
        if ($cond === 'baik') $totalPnTargetBaik += $pLen;
        elseif ($cond === 'sedang') $totalPnTargetSedang += $pLen;
        elseif ($cond === 'rusak_ringan') $totalPnTargetRR += $pLen;
        elseif ($cond === 'rusak_berat') $totalPnTargetRB += $pLen;
        else $totalPnTargetBaik += $pLen;

        // Target Perkerasan
        $pave = strtolower($pn['perkerasan_hasil'] ?? '');
        if (!$pave && !empty($pn['jenis_pelaksana'])) {
            $jp = strtolower($pn['jenis_pelaksana']);
            if (str_contains($jp, 'rigid')) $pave = 'rigid';
            elseif (str_contains($jp, 'aspal') || str_contains($jp, 'overlay')) $pave = 'aspal';
            elseif (str_contains($jp, 'base') || str_contains($jp, 'agregat')) $pave = 'agregat_tanah';
        }
        if ($pave === 'rigid') $totalPnRigid += $pLen;
        elseif ($pave === 'agregat_tanah' || $pave === 'agregat' || $pave === 'tanah') $totalPnAgregat += $pLen;
        elseif ($pave === 'belum_tembus') $totalPnBelumTembus += $pLen;
        else $totalPnAspal += $pLen;
    }

    $pctPnTargetBaik   = $totalPanjangPenanganan > 0 ? ($totalPnTargetBaik / $totalPanjangPenanganan) * 100 : 0;
    $pctPnTargetSedang = $totalPanjangPenanganan > 0 ? ($totalPnTargetSedang / $totalPanjangPenanganan) * 100 : 0;
    $pctPnTargetRR     = $totalPanjangPenanganan > 0 ? ($totalPnTargetRR / $totalPanjangPenanganan) * 100 : 0;
    $pctPnTargetRB     = $totalPanjangPenanganan > 0 ? ($totalPnTargetRB / $totalPanjangPenanganan) * 100 : 0;

    $pctPnRigid        = $totalPanjangPenanganan > 0 ? ($totalPnRigid / $totalPanjangPenanganan) * 100 : 0;
    $pctPnAspal        = $totalPanjangPenanganan > 0 ? ($totalPnAspal / $totalPanjangPenanganan) * 100 : 0;
    $pctPnAgregat      = $totalPanjangPenanganan > 0 ? ($totalPnAgregat / $totalPanjangPenanganan) * 100 : 0;
    $pctPnBelumTembus  = $totalPanjangPenanganan > 0 ? ($totalPnBelumTembus / $totalPanjangPenanganan) * 100 : 0;

    // -----------------------------------------------------------------
    // LOGIKA SLICING CHUNKS MAKSIMAL 5KM (5000 METER)
    // -----------------------------------------------------------------
    $staBase = (float)($ruas['sta_awal'] ?? 0);
    $staEnd  = (float)($ruas['sta_akhir'] ?? 0);
    if ((float)($ruas['panjang'] ?? 0) > 0) {
        $staEnd = max($staEnd, $staBase + (float)$ruas['panjang']);
    }
    foreach ($stripmaps as $sm) {
        if ((float)$sm['sta_akhir'] > $staEnd) {
            $staEnd = (float)$sm['sta_akhir'];
        }
    }
    foreach ($perkerasans as $pk) {
        if ((float)$pk['sta_akhir'] > $staEnd) {
            $staEnd = (float)$pk['sta_akhir'];
        }
    }
    foreach ($penanganans as $pn) {
        if ((float)$pn['sta_akhir'] > $staEnd) {
            $staEnd = (float)$pn['sta_akhir'];
        }
    }
    foreach (($fotoLapangans ?? []) as $fl) {
        if ((float)$fl['sta_meter'] > $staEnd) {
            $staEnd = (float)$fl['sta_meter'];
        }
    }
    if ($staEnd <= $staBase) {
        $staEnd = $staBase + max($totalPanjang, $totalPanjangPerkerasan, $totalPanjangPenanganan, (float)($ruas['panjang'] ?? 0), 1000.0);
    }
    
    // 1. Ekstrak data stripmap (Kondisi 2025)
    $smRuns = [];
    foreach ($stripmaps as $sm) {
        $currentMeter = (float)$sm['sta_awal'];
        $conditions = [
            'baik'         => (float)$sm['baik'],
            'sedang'       => (float)$sm['sedang'],
            'rusak_ringan' => (float)$sm['rusak_ringan'],
            'rusak_berat'  => (float)$sm['rusak_berat']
        ];
        foreach ($conditions as $condKey => $value) {
            if ($value > 0) {
                $smRuns[] = [
                    'sta_awal'     => $currentMeter,
                    'sta_akhir'    => $currentMeter + $value,
                    'panjang'      => $value,
                    'baik'         => $condKey === 'baik' ? $value : 0.0,
                    'sedang'       => $condKey === 'sedang' ? $value : 0.0,
                    'rusak_ringan' => $condKey === 'rusak_ringan' ? $value : 0.0,
                    'rusak_berat'  => $condKey === 'rusak_berat' ? $value : 0.0,
                ];
                $currentMeter += $value;
            }
        }
    }

    // 2. Ekstrak data perkerasan (Perkerasan 2025)
    $pkRuns = [];
    foreach ($perkerasans as $pk) {
        $currentMeter = (float)$pk['sta_awal'];
        $pavements = [
            'rigid'         => (float)$pk['rigid'],
            'aspal'         => (float)$pk['aspal'],
            'agregat_tanah' => (float)$pk['agregat_tanah'],
            'belum_tembus'  => (float)$pk['belum_tembus']
        ];
        foreach ($pavements as $paveKey => $value) {
            if ($value > 0) {
                $pkRuns[] = [
                    'sta_awal'      => $currentMeter,
                    'sta_akhir'     => $currentMeter + $value,
                    'panjang'       => $value,
                    'rigid'         => $paveKey === 'rigid' ? $value : 0.0,
                    'aspal'         => $paveKey === 'aspal' ? $value : 0.0,
                    'agregat_tanah' => $paveKey === 'agregat_tanah' ? $value : 0.0,
                    'belum_tembus'  => $paveKey === 'belum_tembus' ? $value : 0.0,
                ];
                $currentMeter += $value;
            }
        }
    }

    // 3. Ekstrak data penanganan
    $pnRuns = [];
    foreach ($penanganans as $pn) {
        // Tentukan warna dan label target perkerasan/kondisi
        $targetPave = $pn['perkerasan_hasil'] ?? null;
        if (!$targetPave && !empty($pn['jenis_pelaksana'])) {
            $jp = strtolower($pn['jenis_pelaksana']);
            if (str_contains($jp, 'rigid')) $targetPave = 'rigid';
            elseif (str_contains($jp, 'aspal') || str_contains($jp, 'overlay')) $targetPave = 'aspal';
            elseif (str_contains($jp, 'base') || str_contains($jp, 'agregat')) $targetPave = 'agregat_tanah';
        }

        $targetCond = strtolower($pn['kondisi_prediksi'] ?? 'baik');
        $warnaKondisiMap = [
            'baik'         => '#8b5cf6', // Ungu (Purple)
            'sedang'       => '#facc15', // Kuning Sedang
            'rusak_ringan' => '#f97316', // Oranye
            'rusak_berat'  => '#ef4444', // Merah
        ];
        $warnaKondisi = $warnaKondisiMap[$targetCond] ?? '#8b5cf6';

        $targetPaveNorm = strtolower($targetPave ?? 'aspal');
        $warnaPaveMap = [
            'rigid'         => '#475569', // Abu Beton / Rigid
            'aspal'         => '#0f172a', // Hitam Aspal
            'agregat_tanah' => '#7c461b', // Cokelat Kerikil
            'agregat'       => '#7c461b',
            'tanah'         => '#7c461b',
            'belum_tembus'  => '#7e22ce', // Ungu Belum Tembus
        ];
        $warnaPerkerasan = $warnaPaveMap[$targetPaveNorm] ?? '#0f172a';

        $pnRuns[] = [
            'id'               => (int) $pn['id'],
            'tahun'            => (int) $pn['tahun'],
            'sta_awal'         => (float) $pn['sta_awal'],
            'sta_akhir'        => (float) $pn['sta_akhir'],
            'panjang'          => (float) $pn['panjang'],
            'jenis_penanganan' => $pn['jenis_penanganan'] ?? 'Penanganan Jalan',
            'jenis_pelaksana'  => $pn['jenis_pelaksana'] ?? '',
            'status'           => $pn['status'] ?? 'rencana',
            'nama_paket'       => $pn['nama_paket'] ?? '',
            'anggaran'         => (float) ($pn['anggaran'] ?? 0),
            'sumber_dana'      => $pn['sumber_dana'] ?? '',
            'warna'            => !empty($pn['warna']) ? $pn['warna'] : (PenangananService::STATUS_COLORS[$pn['status'] ?? 'rencana'] ?? '#6366f1'),
            'warna_kondisi'    => $warnaKondisi,
            'warna_perkerasan' => $warnaPerkerasan,
            'status_label'     => PenangananService::STATUS_LABELS[$pn['status'] ?? 'rencana'] ?? ucfirst($pn['status'] ?? 'Rencana'),
            'kondisi_prediksi' => $pn['kondisi_prediksi'] ?? 'baik',
            'perkerasan_hasil' => $targetPave ?? 'aspal',
        ];
    }

    // 4. Hitung Simulasi Asumsi Kondisi & Asumsi Perkerasan Pasca Penanganan
    $prediksiService = new PrediksiService();
    $breakPoints = [$staBase, $staEnd];
    foreach ($stripmaps as $sm) {
        $breakPoints[] = (float)$sm['sta_awal'];
        $breakPoints[] = (float)$sm['sta_akhir'];
    }
    foreach ($perkerasans as $pk) {
        $breakPoints[] = (float)$pk['sta_awal'];
        $breakPoints[] = (float)$pk['sta_akhir'];
    }
    foreach ($pnRuns as $pn) {
        $breakPoints[] = (float)$pn['sta_awal'];
        $breakPoints[] = (float)$pn['sta_akhir'];
    }
    $chunkSize = 5000.0;
    for ($cur = $staBase; $cur < $staEnd; $cur += $chunkSize) {
        $breakPoints[] = $cur;
        $breakPoints[] = min($staEnd, $cur + $chunkSize);
    }
    $breakPoints = array_values(array_unique(array_filter($breakPoints, fn($p) => $p >= $staBase && $p <= $staEnd)));
    sort($breakPoints);

    $asumsiSmRuns = [];
    $asumsiPkRuns = [];

    for ($i = 0; $i < count($breakPoints) - 1; $i++) {
        $p1 = $breakPoints[$i];
        $p2 = $breakPoints[$i + 1];
        $len = $p2 - $p1;
        if ($len <= 0.001) continue;

        // Cari base kondisi
        $baseKondisi = 'baik';
        foreach ($stripmaps as $sm) {
            if ((float)$sm['sta_awal'] <= $p1 && (float)$sm['sta_akhir'] >= $p2) {
                $kArr = [
                    'baik'         => (float)$sm['baik'],
                    'sedang'       => (float)$sm['sedang'],
                    'rusak_ringan' => (float)$sm['rusak_ringan'],
                    'rusak_berat'  => (float)$sm['rusak_berat'],
                ];
                arsort($kArr);
                $baseKondisi = array_key_first($kArr) ?? 'baik';
                break;
            }
        }

        // Cari base perkerasan
        $basePave = 'aspal';
        foreach ($perkerasans as $pk) {
            if ((float)$pk['sta_awal'] <= $p1 && (float)$pk['sta_akhir'] >= $p2) {
                $pArr = [
                    'rigid'         => (float)$pk['rigid'],
                    'aspal'         => (float)$pk['aspal'],
                    'agregat_tanah' => (float)$pk['agregat_tanah'],
                    'belum_tembus'  => (float)$pk['belum_tembus'],
                ];
                arsort($pArr);
                $basePave = array_key_first($pArr) ?? 'aspal';
                break;
            }
        }

        // Cari penanganan yang tumpang tindih
        $activePn = null;
        foreach ($pnRuns as $pn) {
            if ($pn['sta_awal'] <= $p1 && $pn['sta_akhir'] >= $p2) {
                $activePn = $pn;
                break;
            }
        }

        $asumsiKondisi = $baseKondisi;
        $asumsiPave = $basePave;
        if ($activePn) {
            $predResult = $prediksiService->hitung($baseKondisi, $basePave, $activePn['jenis_pelaksana']);
            $asumsiKondisi = $activePn['kondisi_prediksi'] ?? $predResult['kondisi_prediksi'] ?? 'baik';
            $asumsiPave = $activePn['perkerasan_hasil'] ?? $predResult['perkerasan_hasil'] ?? $basePave;
        }

        $asumsiSmRuns[] = [
            'sta_awal'     => $p1,
            'sta_akhir'    => $p2,
            'panjang'      => $len,
            'kondisi'      => $asumsiKondisi,
            'baik'         => $asumsiKondisi === 'baik' ? $len : 0.0,
            'sedang'       => $asumsiKondisi === 'sedang' ? $len : 0.0,
            'rusak_ringan' => $asumsiKondisi === 'rusak_ringan' ? $len : 0.0,
            'rusak_berat'  => $asumsiKondisi === 'rusak_berat' ? $len : 0.0,
            'is_asumsi'    => ($activePn !== null),
            'tahun'        => $activePn['tahun'] ?? null,
            'pn_info'      => $activePn ? ($activePn['jenis_penanganan'] . ' (Tahun ' . $activePn['tahun'] . ')') : null,
        ];

        $asumsiPkRuns[] = [
            'sta_awal'      => $p1,
            'sta_akhir'     => $p2,
            'panjang'       => $len,
            'pave'          => $asumsiPave,
            'rigid'         => $asumsiPave === 'rigid' ? $len : 0.0,
            'aspal'         => $asumsiPave === 'aspal' ? $len : 0.0,
            'agregat_tanah' => $asumsiPave === 'agregat_tanah' ? $len : 0.0,
            'belum_tembus'  => $asumsiPave === 'belum_tembus' ? $len : 0.0,
            'is_asumsi'     => ($activePn !== null && $asumsiPave !== $basePave),
            'tahun'         => $activePn['tahun'] ?? null,
            'pn_info'       => $activePn ? ($activePn['jenis_penanganan'] . ' (Tahun ' . $activePn['tahun'] . ')') : null,
        ];
    }

    // Helper untuk mengisi gap antar segmen dalam chunk
    $fillChunkGaps = function(array $runs, float $cStart, float $cEnd): array {
        $result = [];
        $currentPos = $cStart;
        foreach ($runs as $r) {
            if ($r['sta_awal'] > $currentPos) {
                $gapLen = $r['sta_awal'] - $currentPos;
                $result[] = [
                    'sta_awal'  => $currentPos,
                    'sta_akhir' => $r['sta_awal'],
                    'panjang'   => $gapLen,
                    'is_gap'    => true
                ];
            }
            $result[] = $r;
            $currentPos = $r['sta_akhir'];
        }
        if ($currentPos < $cEnd) {
            $result[] = [
                'sta_awal'  => $currentPos,
                'sta_akhir' => $cEnd,
                'panjang'   => $cEnd - $currentPos,
                'is_gap'    => true
            ];
        }
        return $result;
    };

    // 5. Distribusikan ke Chunk (maks 5000 meter)
    $chunks = [];
    $current = $staBase;
    
    while ($current < $staEnd) {
        $chunkEnd = $current + $chunkSize;
        
        // A. Kondisi 2025
        $overlappingSmRuns = [];
        foreach ($smRuns as $run) {
            $overlapStart = max($run['sta_awal'], $current);
            $overlapEnd   = min($run['sta_akhir'], $chunkEnd);
            if ($overlapStart < $overlapEnd) {
                $overlapLen = $overlapEnd - $overlapStart;
                $overlappingSmRuns[] = [
                    'sta_awal'     => $overlapStart,
                    'sta_akhir'    => $overlapEnd,
                    'panjang'      => $overlapLen,
                    'baik'         => $run['baik'] > 0 ? $overlapLen : 0.0,
                    'sedang'       => $run['sedang'] > 0 ? $overlapLen : 0.0,
                    'rusak_ringan' => $run['rusak_ringan'] > 0 ? $overlapLen : 0.0,
                    'rusak_berat'  => $run['rusak_berat'] > 0 ? $overlapLen : 0.0,
                ];
            }
        }
        usort($overlappingSmRuns, fn($a, $b) => $a['sta_awal'] <=> $b['sta_awal']);
        $chunkStripmaps = $fillChunkGaps($overlappingSmRuns, $current, $chunkEnd);

        // B. Asumsi Kondisi Pasca Penanganan
        $overlappingAsumsiSm = [];
        foreach ($asumsiSmRuns as $run) {
            $overlapStart = max($run['sta_awal'], $current);
            $overlapEnd   = min($run['sta_akhir'], $chunkEnd);
            if ($overlapStart < $overlapEnd) {
                $overlapLen = $overlapEnd - $overlapStart;
                $overlappingAsumsiSm[] = [
                    'sta_awal'     => $overlapStart,
                    'sta_akhir'    => $overlapEnd,
                    'panjang'      => $overlapLen,
                    'kondisi'      => $run['kondisi'],
                    'baik'         => $run['baik'] > 0 ? $overlapLen : 0.0,
                    'sedang'       => $run['sedang'] > 0 ? $overlapLen : 0.0,
                    'rusak_ringan' => $run['rusak_ringan'] > 0 ? $overlapLen : 0.0,
                    'rusak_berat'  => $run['rusak_berat'] > 0 ? $overlapLen : 0.0,
                    'is_asumsi'    => $run['is_asumsi'],
                    'tahun'        => $run['tahun'],
                    'pn_info'      => $run['pn_info'],
                ];
            }
        }
        usort($overlappingAsumsiSm, fn($a, $b) => $a['sta_awal'] <=> $b['sta_awal']);
        $chunkAsumsiStripmaps = $fillChunkGaps($overlappingAsumsiSm, $current, $chunkEnd);

        // C. Perkerasan 2025
        $overlappingPkRuns = [];
        foreach ($pkRuns as $run) {
            $overlapStart = max($run['sta_awal'], $current);
            $overlapEnd   = min($run['sta_akhir'], $chunkEnd);
            if ($overlapStart < $overlapEnd) {
                $overlapLen = $overlapEnd - $overlapStart;
                $overlappingPkRuns[] = [
                    'sta_awal'      => $overlapStart,
                    'sta_akhir'     => $overlapEnd,
                    'panjang'       => $overlapLen,
                    'rigid'         => $run['rigid'] > 0 ? $overlapLen : 0.0,
                    'aspal'         => $run['aspal'] > 0 ? $overlapLen : 0.0,
                    'agregat_tanah' => $run['agregat_tanah'] > 0 ? $overlapLen : 0.0,
                    'belum_tembus'  => $run['belum_tembus'] > 0 ? $overlapLen : 0.0,
                ];
            }
        }
        usort($overlappingPkRuns, fn($a, $b) => $a['sta_awal'] <=> $b['sta_awal']);
        $chunkPerkerasans = $fillChunkGaps($overlappingPkRuns, $current, $chunkEnd);

        // D. Asumsi Perkerasan Pasca Penanganan
        $overlappingAsumsiPk = [];
        foreach ($asumsiPkRuns as $run) {
            $overlapStart = max($run['sta_awal'], $current);
            $overlapEnd   = min($run['sta_akhir'], $chunkEnd);
            if ($overlapStart < $overlapEnd) {
                $overlapLen = $overlapEnd - $overlapStart;
                $overlappingAsumsiPk[] = [
                    'sta_awal'      => $overlapStart,
                    'sta_akhir'     => $overlapEnd,
                    'panjang'       => $overlapLen,
                    'pave'          => $run['pave'],
                    'rigid'         => $run['rigid'] > 0 ? $overlapLen : 0.0,
                    'aspal'         => $run['aspal'] > 0 ? $overlapLen : 0.0,
                    'agregat_tanah' => $run['agregat_tanah'] > 0 ? $overlapLen : 0.0,
                    'belum_tembus'  => $run['belum_tembus'] > 0 ? $overlapLen : 0.0,
                    'is_asumsi'     => $run['is_asumsi'],
                    'tahun'         => $run['tahun'],
                    'pn_info'       => $run['pn_info'],
                ];
            }
        }
        usort($overlappingAsumsiPk, fn($a, $b) => $a['sta_awal'] <=> $b['sta_awal']);
        $chunkAsumsiPerkerasans = $fillChunkGaps($overlappingAsumsiPk, $current, $chunkEnd);

        // E. Penanganan Runs
        $overlappingPnRuns = [];
        foreach ($pnRuns as $run) {
            $overlapStart = max($run['sta_awal'], $current);
            $overlapEnd   = min($run['sta_akhir'], $chunkEnd);
            if ($overlapStart < $overlapEnd) {
                $overlapLen = $overlapEnd - $overlapStart;
                $overlappingPnRuns[] = array_merge($run, [
                    'sta_awal'  => $overlapStart,
                    'sta_akhir' => $overlapEnd,
                    'panjang'   => $overlapLen,
                ]);
            }
        }
        usort($overlappingPnRuns, fn($a, $b) => $a['sta_awal'] <=> $b['sta_awal']);
        $chunkPenanganans = $fillChunkGaps($overlappingPnRuns, $current, $chunkEnd);

        $chunks[] = [
            'start'              => $current,
            'end'                => $chunkEnd,
            'stripmaps'          => $chunkStripmaps,
            'asumsi_stripmaps'   => $chunkAsumsiStripmaps,
            'perkerasans'        => $chunkPerkerasans,
            'asumsi_perkerasans' => $chunkAsumsiPerkerasans,
            'penanganans'        => $chunkPenanganans,
        ];
        
        $current = $chunkEnd;
    }
?>

<!-- Load Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<?php if (!empty($stripmaps) || !empty($perkerasans) || !empty($penanganans) || $totalPanjang > 0 || $totalPanjangPerkerasan > 0): ?>
<div class="bg-white rounded-2xl shadow-sm border border-gray-200" style="background-color: #ffffff; border-color: #e5e7eb;"
     x-data="{ 
        activeLabel: null, 
        activePct: 0, 
        activeChunk: null,
        
        // 3 Layer Pengaktifan Tampilan Kondisi
        showKondisi2025: (function() {
            try {
                let saved = localStorage.getItem('sm_show_kondisi_2025');
                if (saved !== null) return saved === '1';
            } catch(e) {}
            return true;
        })(),
        showPenangananKondisi: (function() {
            try {
                let saved = localStorage.getItem('sm_show_penanganan_kondisi');
                if (saved !== null) return saved === '1';
            } catch(e) {}
            return true;
        })(),
        showAsumsiKondisi: (function() {
            try {
                let saved = localStorage.getItem('sm_show_asumsi_kondisi');
                if (saved !== null) return saved === '1';
            } catch(e) {}
            return true;
        })(),

        // 3 Layer Pengaktifan Tampilan Perkerasan
        showPerkerasan2025: (function() {
            try {
                let saved = localStorage.getItem('sm_show_perkerasan_2025');
                if (saved !== null) return saved === '1';
            } catch(e) {}
            return true;
        })(),
        showPenangananPerkerasan: (function() {
            try {
                let saved = localStorage.getItem('sm_show_penanganan_perkerasan');
                if (saved !== null) return saved === '1';
            } catch(e) {}
            return true;
        })(),
        showAsumsiPerkerasan: (function() {
            try {
                let saved = localStorage.getItem('sm_show_asumsi_perkerasan');
                if (saved !== null) return saved === '1';
            } catch(e) {}
            return true;
        })(),

        penangananYearFilter: 'all',

        tickInterval: (function() {
            try {
                let saved = localStorage.getItem('sta_tick_interval');
                if (saved) return parseInt(saved);
            } catch (e) {}
            return <?= max($totalPanjang, $totalPanjangPerkerasan, $totalPanjangPenanganan) < 1500 ? 100 : (max($totalPanjang, $totalPanjangPerkerasan, $totalPanjangPenanganan) < 4000 ? 250 : (max($totalPanjang, $totalPanjangPerkerasan, $totalPanjangPenanganan) < 10000 ? 500 : 1000)) ?>;
        })(),

        init() {
            this.$watch('tickInterval', value => {
                try { localStorage.setItem('sta_tick_interval', value); } catch (e) {}
            });
            this.$watch('showKondisi2025', value => {
                try { localStorage.setItem('sm_show_kondisi_2025', value ? '1' : '0'); } catch (e) {}
            });
            this.$watch('showPenangananKondisi', value => {
                try { localStorage.setItem('sm_show_penanganan_kondisi', value ? '1' : '0'); } catch (e) {}
            });
            this.$watch('showAsumsiKondisi', value => {
                try { localStorage.setItem('sm_show_asumsi_kondisi', value ? '1' : '0'); } catch (e) {}
            });
            this.$watch('showPerkerasan2025', value => {
                try { localStorage.setItem('sm_show_perkerasan_2025', value ? '1' : '0'); } catch (e) {}
            });
            this.$watch('showPenangananPerkerasan', value => {
                try { localStorage.setItem('sm_show_penanganan_perkerasan', value ? '1' : '0'); } catch (e) {}
            });
            this.$watch('showAsumsiPerkerasan', value => {
                try { localStorage.setItem('sm_show_asumsi_perkerasan', value ? '1' : '0'); } catch (e) {}
            });
        },
        meterToSta(meter) {
            let km = Math.floor(meter / 1000);
            let m = Math.round(meter - (km * 1000));
            return km + '+' + String(m).padStart(3, '0');
        },
        getTicks(start, end) {
            let ticks = [];
            ticks.push({ meter: start, pct: 0 });
            
            let interval = this.tickInterval;
            let firstMultiple = Math.ceil(start / interval) * interval;
            if (firstMultiple === start) {
                firstMultiple += interval;
            }
            
            let chunkTotal = end - start;
            if (chunkTotal <= 0) return ticks;

            for (let m = firstMultiple; m < end; m += interval) {
                let pct = ((m - start) / chunkTotal) * 100;
                let minTolerance = Math.min(50, interval * 0.2);
                if ((m - start) >= minTolerance && (end - m) >= minTolerance) {
                    ticks.push({
                        meter: m,
                        pct: pct
                    });
                }
            }
            
            if (end > start) {
                ticks.push({ meter: end, pct: 100 });
            }
            return ticks;
        }
     }"
     @click.outside="activeLabel = null; activeChunk = null">
     
    <!-- Header Controls -->
    <div class="px-6 py-4 border-b border-gray-200 flex flex-wrap items-center justify-between gap-4 bg-gray-50/70 rounded-t-2xl relative z-30">
        <div>
            <div class="flex items-center gap-2">
                <h3 class="text-base sm:text-lg font-bold text-gray-900">Visualisasi Strip Map, Perkerasan & Penanganan</h3>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-blue-100 text-blue-800">
                    Multi-Layer Mode
                </span>
            </div>
            <p class="text-xs text-gray-500 mt-0.5">
                Panjang ruas: <strong class="text-gray-700 font-semibold"><?= format_number(max($totalPanjang, $totalPanjangPerkerasan, $totalPanjangPenanganan)) ?> m</strong> — Klik atau hover segmen untuk melihat detail data teknis.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5 print:hidden no-export">
            
            <!-- 1. Filter Tahun Penanganan -->
            <div class="flex items-center gap-1.5">
                <span class="text-xs font-semibold text-gray-600">Tahun:</span>
                <select x-model="penangananYearFilter" class="text-xs rounded-xl border border-gray-300 bg-white px-2.5 py-1.5 font-semibold text-gray-800 hover:bg-gray-50 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 focus:outline-none shadow-sm transition-colors cursor-pointer">
                    <option value="all">🗓️ Semua Tahun</option>
                    <?php foreach (TahunHelper::getList() as $yr): ?>
                        <option value="<?= $yr ?>">Tahun <?= $yr ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- 2. Skala STA Label -->
            <div class="flex items-center gap-1.5">
                <span class="text-xs font-semibold text-gray-500">Skala:</span>
                <select x-model.number="tickInterval" class="text-xs rounded-xl border border-gray-300 bg-white px-2 py-1.5 font-semibold text-gray-800 hover:bg-gray-50 focus:border-blue-500 focus:outline-none shadow-sm transition-colors cursor-pointer">
                    <option value="100">100 m</option>
                    <option value="200">200 m</option>
                    <option value="250">250 m</option>
                    <option value="500">500 m</option>
                    <option value="1000">1 km</option>
                </select>
            </div>

        </div>
    </div>

    <!-- Quick Direct Layer Toggle Bar (Memudahkan Aktivasi/Non-Aktivasi Langsung 1-Klik) -->
    <div class="px-6 py-2.5 bg-gray-100/70 border-b border-gray-200 flex flex-wrap items-center justify-between gap-3 text-xs">
        <!-- Kondisi Group -->
        <div class="flex flex-wrap items-center gap-1.5">
            <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wider mr-1">Kondisi:</span>
            
            <button type="button" @click="showKondisi2025 = !showKondisi2025"
                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold border transition-all cursor-pointer select-none"
                    :class="showKondisi2025 ? 'bg-emerald-50 text-emerald-800 border-emerald-300 shadow-sm' : 'bg-white text-gray-400 border-gray-200 opacity-60 hover:opacity-100'">
                <span class="w-2 h-2 rounded-full" :class="showKondisi2025 ? 'bg-emerald-500' : 'bg-gray-300'"></span>
                <span>1. Kondisi 2025</span>
                <span x-show="showKondisi2025" class="text-[10px] text-emerald-600 font-bold">✓</span>
            </button>

            <button type="button" @click="showPenangananKondisi = !showPenangananKondisi"
                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold border transition-all cursor-pointer select-none"
                    :class="showPenangananKondisi ? 'bg-blue-50 text-blue-800 border-blue-300 shadow-sm' : 'bg-white text-gray-400 border-gray-200 opacity-60 hover:opacity-100'">
                <span class="w-2 h-2 rounded-full" :class="showPenangananKondisi ? 'bg-blue-500' : 'bg-gray-300'"></span>
                <span>2. Penanganan</span>
                <span x-show="showPenangananKondisi" class="text-[10px] text-blue-600 font-bold">✓</span>
            </button>

            <button type="button" @click="showAsumsiKondisi = !showAsumsiKondisi"
                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold border transition-all cursor-pointer select-none"
                    :class="showAsumsiKondisi ? 'bg-purple-50 text-purple-800 border-purple-300 shadow-sm' : 'bg-white text-gray-400 border-gray-200 opacity-60 hover:opacity-100'">
                <span class="w-2 h-2 rounded-full" :class="showAsumsiKondisi ? 'bg-purple-500' : 'bg-gray-300'"></span>
                <span>3. Asumsi</span>
                <span x-show="showAsumsiKondisi" class="text-[10px] text-purple-600 font-bold">✓</span>
            </button>
        </div>

        <!-- Perkerasan Group -->
        <div class="flex flex-wrap items-center gap-1.5">
            <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wider mr-1">Perkerasan:</span>
            
            <button type="button" @click="showPerkerasan2025 = !showPerkerasan2025"
                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold border transition-all cursor-pointer select-none"
                    :class="showPerkerasan2025 ? 'bg-slate-200 text-slate-900 border-slate-400 shadow-sm' : 'bg-white text-gray-400 border-gray-200 opacity-60 hover:opacity-100'">
                <span class="w-2 h-2 rounded-full" :class="showPerkerasan2025 ? 'bg-slate-700' : 'bg-gray-300'"></span>
                <span>1. Perkerasan 2025</span>
                <span x-show="showPerkerasan2025" class="text-[10px] text-slate-800 font-bold">✓</span>
            </button>

            <button type="button" @click="showPenangananPerkerasan = !showPenangananPerkerasan"
                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold border transition-all cursor-pointer select-none"
                    :class="showPenangananPerkerasan ? 'bg-amber-50 text-amber-900 border-amber-300 shadow-sm' : 'bg-white text-gray-400 border-gray-200 opacity-60 hover:opacity-100'">
                <span class="w-2 h-2 rounded-full" :class="showPenangananPerkerasan ? 'bg-amber-600' : 'bg-gray-300'"></span>
                <span>2. Penanganan</span>
                <span x-show="showPenangananPerkerasan" class="text-[10px] text-amber-700 font-bold">✓</span>
            </button>

            <button type="button" @click="showAsumsiPerkerasan = !showAsumsiPerkerasan"
                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold border transition-all cursor-pointer select-none"
                    :class="showAsumsiPerkerasan ? 'bg-indigo-50 text-indigo-900 border-indigo-300 shadow-sm' : 'bg-white text-gray-400 border-gray-200 opacity-60 hover:opacity-100'">
                <span class="w-2 h-2 rounded-full" :class="showAsumsiPerkerasan ? 'bg-indigo-600' : 'bg-gray-300'"></span>
                <span>3. Asumsi</span>
                <span x-show="showAsumsiPerkerasan" class="text-[10px] text-indigo-700 font-bold">✓</span>
            </button>
        </div>
    </div>

    <div class="p-6">
        <!-- Main Layout Grid: Kiri Pie Charts, Kanan Line Chart & Stats -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            <!-- Kiri: Pie Charts (lg:col-span-4) -->
            <div class="lg:col-span-4 flex flex-col gap-6">
                <!-- Pie Chart 1: Kondisi Jalan -->
                <div class="flex flex-col items-center justify-center rounded-2xl p-5 border min-h-[220px]" style="background-color: rgba(249, 250, 251, 0.6); border-color: #e5e7eb;">
                    <h4 class="text-[13px] font-semibold text-gray-500 uppercase tracking-wider mb-4">Kondisi Jalan 2025</h4>
                    <style>
                        @keyframes pie-spin-in {
                            from { transform: scale(0) rotate(-90deg); opacity: 0; }
                            to   { transform: scale(1) rotate(0deg);   opacity: 1; }
                        }
                        .pie-chart-container canvas {
                            animation: pie-spin-in 0.8s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
                        }
                        .pie-chart-container {
                            filter: drop-shadow(0 2px 8px rgba(0,0,0,0.06));
                        }
                    </style>
                    <div class="pie-chart-container w-full max-w-[180px] aspect-square relative">
                        <canvas id="conditionPieChart"></canvas>
                    </div>
                    <!-- Legend -->
                    <div class="flex flex-wrap justify-center gap-x-3 gap-y-1.5 mt-5">
                        <?php
                            $legendItems = [
                                ['label' => 'Baik',         'color' => '#10b981', 'pct' => $pctBaik,   'val' => $totalBaik],
                                ['label' => 'Sedang',       'color' => '#facc15', 'pct' => $pctSedang, 'val' => $totalSedang],
                                ['label' => 'Rusak Ringan', 'color' => '#f97316', 'pct' => $pctRR,     'val' => $totalRR],
                                ['label' => 'Rusak Berat',  'color' => '#ef4444', 'pct' => $pctRB,     'val' => $totalRB],
                            ];
                        ?>
                        <?php foreach ($legendItems as $li): ?>
                            <?php if ($li['val'] > 0): ?>
                            <div class="flex items-center gap-1.5">
                                <span class="w-2.5 h-2.5 rounded-full inline-block" style="background-color: <?= $li['color'] ?>;"></span>
                                <span class="text-[11px] font-medium text-gray-600"><?= $li['label'] ?></span>
                                <span class="text-[10px] text-gray-400"><?= number_format($li['pct'], 1) ?>%</span>
                            </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Pie Chart 2: Kemantapan Jalan -->
                <div class="flex flex-col items-center justify-center rounded-2xl p-5 border min-h-[220px]" style="background-color: rgba(249, 250, 251, 0.6); border-color: #e5e7eb;">
                    <h4 class="text-[13px] font-semibold text-gray-500 uppercase tracking-wider mb-4">Kemantapan Jalan</h4>
                    <div class="pie-chart-container w-full max-w-[180px] aspect-square relative">
                        <canvas id="stabilityPieChart"></canvas>
                    </div>
                    <!-- Legend -->
                    <div class="flex flex-wrap justify-center gap-x-4 gap-y-1.5 mt-5">
                        <div class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 inline-block"></span>
                            <span class="text-[11px] font-medium text-gray-600">Mantap</span>
                            <span class="text-[10px] text-gray-400"><?= number_format($pctMantap, 1) ?>%</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-red-500 inline-block"></span>
                            <span class="text-[11px] font-medium text-gray-600">Tidak Mantap</span>
                            <span class="text-[10px] text-gray-400"><?= number_format($pctTidakMantap, 1) ?>%</span>
                        </div>
                    </div>
                </div>

                <!-- Pie Chart 3: Proporsi Jenis Perkerasan -->
                <?php if ($totalPanjangPerkerasan > 0): ?>
                <div x-show="showPerkerasan2025 || showPenangananPerkerasan || showAsumsiPerkerasan" class="flex flex-col items-center justify-center rounded-2xl p-5 border min-h-[220px]" style="background-color: rgba(249, 250, 251, 0.6); border-color: #e5e7eb;">
                    <h4 class="text-[13px] font-semibold text-gray-500 uppercase tracking-wider mb-4">Jenis Perkerasan 2025</h4>
                    <div class="pie-chart-container w-full max-w-[180px] aspect-square relative">
                        <canvas id="pavementPieChart"></canvas>
                    </div>
                    <!-- Legend -->
                    <div class="flex flex-wrap justify-center gap-x-3 gap-y-1.5 mt-5">
                        <?php
                            $pkLegendItems = [
                                ['label' => 'Rigid',          'color' => '#6b7280', 'pct' => $pctRigid,        'val' => $totalRigid],
                                ['label' => 'Aspal',          'color' => '#1f2937', 'pct' => $pctAspal,        'val' => $totalAspal],
                                ['label' => 'Kerikil','color' => '#7c461b', 'pct' => $pctAgregatTanah, 'val' => $totalAgregatTanah],
                                ['label' => 'Belum Tembus',   'color' => '#7c3aed', 'pct' => $pctBelumTembus,  'val' => $totalBelumTembus],
                            ];
                        ?>
                        <?php foreach ($pkLegendItems as $li): ?>
                            <?php if ($li['val'] > 0): ?>
                            <div class="flex items-center gap-1.5">
                                <span class="w-2.5 h-2.5 rounded-full inline-block" style="background-color: <?= $li['color'] ?>;"></span>
                                <span class="text-[11px] font-medium text-gray-600"><?= $li['label'] ?></span>
                                <span class="text-[10px] text-gray-400"><?= number_format($li['pct'], 1) ?>%</span>
                            </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Pie Chart 4: Rencana Target Kondisi Penanganan -->
                <?php if ($totalPanjangPenanganan > 0): ?>
                <div x-show="showPenangananKondisi || showAsumsiKondisi" class="flex flex-col items-center justify-center rounded-2xl p-5 border min-h-[220px]" style="background-color: rgba(249, 250, 251, 0.6); border-color: #e5e7eb;">
                    <div class="flex items-center gap-1.5 mb-3">
                        <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                        <h4 class="text-[12px] font-bold text-blue-800 uppercase tracking-wider">Rencana Target Kondisi</h4>
                    </div>
                    <div class="pie-chart-container w-full max-w-[180px] aspect-square relative">
                        <canvas id="pnConditionPieChart"></canvas>
                    </div>
                    <!-- Legend -->
                    <div class="flex flex-wrap justify-center gap-x-3 gap-y-1.5 mt-5">
                        <?php
                            $pnCondLegendItems = [
                                ['label' => 'Target Baik',         'color' => '#8b5cf6', 'pct' => $pctPnTargetBaik,   'val' => $totalPnTargetBaik],
                                ['label' => 'Target Sedang',       'color' => '#facc15', 'pct' => $pctPnTargetSedang, 'val' => $totalPnTargetSedang],
                                ['label' => 'Target Rusak Ringan', 'color' => '#f97316', 'pct' => $pctPnTargetRR,     'val' => $totalPnTargetRR],
                                ['label' => 'Target Rusak Berat',  'color' => '#ef4444', 'pct' => $pctPnTargetRB,     'val' => $totalPnTargetRB],
                            ];
                        ?>
                        <?php foreach ($pnCondLegendItems as $li): ?>
                            <?php if ($li['val'] > 0): ?>
                            <div class="flex items-center gap-1.5">
                                <span class="w-2.5 h-2.5 rounded-full inline-block shrink-0" style="background-color: <?= $li['color'] ?>;"></span>
                                <span class="text-[11px] font-medium text-gray-700"><?= $li['label'] ?></span>
                                <span class="text-[10px] text-gray-400"><?= number_format($li['pct'], 1) ?>%</span>
                            </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Pie Chart 5: Rencana Jenis Perkerasan Baru -->
                <div x-show="showPenangananPerkerasan || showAsumsiPerkerasan" class="flex flex-col items-center justify-center rounded-2xl p-5 border min-h-[220px]" style="background-color: rgba(249, 250, 251, 0.6); border-color: #e5e7eb;">
                    <div class="flex items-center gap-1.5 mb-3">
                        <span class="w-2 h-2 rounded-full bg-amber-600"></span>
                        <h4 class="text-[12px] font-bold text-amber-800 uppercase tracking-wider">Rencana Jenis Perkerasan</h4>
                    </div>
                    <div class="pie-chart-container w-full max-w-[180px] aspect-square relative">
                        <canvas id="pnPavementPieChart"></canvas>
                    </div>
                    <!-- Legend -->
                    <div class="flex flex-wrap justify-center gap-x-3 gap-y-1.5 mt-5">
                        <?php
                            $pnPaveLegendItems = [
                                ['label' => 'Rigid / Beton',   'color' => '#475569', 'pct' => $pctPnRigid,       'val' => $totalPnRigid],
                                ['label' => 'Aspal',           'color' => '#0f172a', 'pct' => $pctPnAspal,       'val' => $totalPnAspal],
                                ['label' => 'Agregat / LPB',   'color' => '#7c461b', 'pct' => $pctPnAgregat,     'val' => $totalPnAgregat],
                                ['label' => 'Belum Tembus',    'color' => '#7e22ce', 'pct' => $pctPnBelumTembus, 'val' => $totalPnBelumTembus],
                            ];
                        ?>
                        <?php foreach ($pnPaveLegendItems as $li): ?>
                            <?php if ($li['val'] > 0): ?>
                            <div class="flex items-center gap-1.5">
                                <span class="w-2.5 h-2.5 rounded-full inline-block shrink-0" style="background-color: <?= $li['color'] ?>;"></span>
                                <span class="text-[11px] font-medium text-gray-700"><?= $li['label'] ?></span>
                                <span class="text-[10px] text-gray-400"><?= number_format($li['pct'], 1) ?>%</span>
                            </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

            </div>

            <!-- Kanan: Line Chart & Stats (lg:col-span-8) -->
            <div class="lg:col-span-8 flex flex-col space-y-6">
                <!-- Stats Grid -->
                <div class="space-y-6">
                    
                    <!-- Row 1: 4 Detail Kondisi Jalan (Baik, Sedang, Rusak Ringan, Rusak Berat) -->
                    <div x-show="showKondisi2025 || showAsumsiKondisi">
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                            <!-- Baik -->
                            <div class="p-4 rounded-xl border shadow-sm hover:shadow-md transition-shadow" style="background-color: #f0fdf4; border-color: #d1fae5;">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-2.5 h-2.5 rounded-full inline-block" style="background-color: #10b981;"></span>
                                        <span class="text-xs font-semibold text-emerald-800">Baik</span>
                                    </div>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 text-[10px] font-bold">
                                        <?= number_format($pctBaik, 1) ?>%
                                    </span>
                                </div>
                                <h3 class="text-xl font-bold text-emerald-700"><?= format_number($totalBaik) ?> <span class="text-xs font-normal text-emerald-600">m</span></h3>
                                <p class="text-[11px] font-medium text-emerald-600 mt-0.5">Kondisi Baik</p>
                            </div>

                            <!-- Sedang -->
                            <div class="p-4 rounded-xl border shadow-sm hover:shadow-md transition-shadow" style="background-color: #fefce8; border-color: #fef08a;">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-2.5 h-2.5 rounded-full inline-block" style="background-color: #facc15;"></span>
                                        <span class="text-xs font-semibold text-yellow-800">Sedang</span>
                                    </div>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded bg-yellow-100 text-yellow-800 text-[10px] font-bold">
                                        <?= number_format($pctSedang, 1) ?>%
                                    </span>
                                </div>
                                <h3 class="text-xl font-bold text-yellow-700"><?= format_number($totalSedang) ?> <span class="text-xs font-normal text-yellow-600">m</span></h3>
                                <p class="text-[11px] font-medium text-yellow-600 mt-0.5">Kondisi Sedang</p>
                            </div>

                            <!-- Rusak Ringan -->
                            <div class="p-4 rounded-xl border shadow-sm hover:shadow-md transition-shadow" style="background-color: #fff7ed; border-color: #ffedd5;">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-2.5 h-2.5 rounded-full inline-block" style="background-color: #f97316;"></span>
                                        <span class="text-xs font-semibold text-orange-800">Rusak Ringan</span>
                                    </div>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded bg-orange-100 text-orange-800 text-[10px] font-bold">
                                        <?= number_format($pctRR, 1) ?>%
                                    </span>
                                </div>
                                <h3 class="text-xl font-bold text-orange-700"><?= format_number($totalRR) ?> <span class="text-xs font-normal text-orange-600">m</span></h3>
                                <p class="text-[11px] font-medium text-orange-600 mt-0.5">Rusak Ringan</p>
                            </div>

                            <!-- Rusak Berat -->
                            <div class="p-4 rounded-xl border shadow-sm hover:shadow-md transition-shadow" style="background-color: #fef2f2; border-color: #fee2e2;">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-2.5 h-2.5 rounded-full inline-block" style="background-color: #ef4444;"></span>
                                        <span class="text-xs font-semibold text-red-800">Rusak Berat</span>
                                    </div>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded bg-red-100 text-red-800 text-[10px] font-bold">
                                        <?= number_format($pctRB, 1) ?>%
                                    </span>
                                </div>
                                <h3 class="text-xl font-bold text-red-700"><?= format_number($totalRB) ?> <span class="text-xs font-normal text-red-600">m</span></h3>
                                <p class="text-[11px] font-medium text-red-600 mt-0.5">Rusak Berat</p>
                            </div>
                        </div>
                    </div>

                    <!-- Row 2: 2 Kemantapan Jalan (Mantap vs Tidak Mantap) -->
                    <div x-show="showKondisi2025 || showAsumsiKondisi">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Card Mantap -->
                            <div class="p-4 rounded-xl border shadow-sm hover:shadow-md transition-shadow" style="background-color: #f0fdf4; border-color: #d1fae5;">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2.5 h-2.5 rounded-full inline-block" style="background-color: #10b981;"></span>
                                        <span class="text-xs font-semibold text-emerald-800">Mantap <span class="font-normal text-emerald-600">(Baik + Sedang)</span></span>
                                    </div>
                                    <span class="text-xs font-bold text-emerald-700"><?= number_format($pctMantap, 1) ?>%</span>
                                </div>
                                <h3 class="text-2xl font-bold text-emerald-700"><?= format_number($totalMantap) ?> <span class="text-xs font-semibold text-emerald-600">m</span></h3>
                                <div class="mt-2.5 w-full rounded-full h-2" style="background-color: rgba(16, 185, 129, 0.2);">
                                    <div class="h-2 rounded-full" style="width: <?= number_format($pctMantap, 4, '.', '') ?>%; background-color: #10b981;"></div>
                                </div>
                            </div>

                            <!-- Card Tidak Mantap -->
                            <div class="p-4 rounded-xl border shadow-sm hover:shadow-md transition-shadow" style="background-color: #fff1f2; border-color: #ffe4e6;">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2.5 h-2.5 rounded-full inline-block" style="background-color: #ef4444;"></span>
                                        <span class="text-xs font-semibold text-rose-800">Tidak Mantap <span class="font-normal text-rose-600">(R. Ringan + R. Berat)</span></span>
                                    </div>
                                    <span class="text-xs font-bold text-rose-700"><?= number_format($pctTidakMantap, 1) ?>%</span>
                                </div>
                                <h3 class="text-2xl font-bold text-rose-700"><?= format_number($totalTidakMantap) ?> <span class="text-xs font-semibold text-rose-600">m</span></h3>
                                <div class="mt-2.5 w-full rounded-full h-2" style="background-color: rgba(239, 68, 68, 0.2);">
                                    <div class="h-2 rounded-full" style="width: <?= number_format($pctTidakMantap, 4, '.', '') ?>%; background-color: #ef4444;"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Row 3: 4 Detail Jenis Perkerasan Jalan -->
                    <div x-show="showPerkerasan2025 || showAsumsiPerkerasan">
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                            <!-- Rigid -->
                            <div class="p-4 rounded-xl border shadow-sm hover:shadow-md transition-shadow" style="background-color: #475569; border-color: #334155;">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-2.5 h-2.5 rounded-full inline-block" style="background-color: #cbd5e1;"></span>
                                        <span class="text-xs font-semibold text-white">Rigid</span>
                                    </div>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold" style="background-color: rgba(0, 0, 0, 0.25); color: #ffffff;">
                                        <?= number_format($pctRigid, 1) ?>%
                                    </span>
                                </div>
                                <h3 class="text-xl font-bold text-white"><?= format_number($totalRigid) ?> <span class="text-xs font-normal text-slate-200">m</span></h3>
                                <p class="text-[11px] font-medium text-slate-200 mt-0.5">Beton / Rigid</p>
                            </div>

                            <!-- Aspal -->
                            <div class="p-4 rounded-xl border shadow-sm hover:shadow-md transition-shadow" style="background-color: #0f172a; border-color: #020617;">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-2.5 h-2.5 rounded-full inline-block" style="background-color: #38bdf8;"></span>
                                        <span class="text-xs font-semibold text-white">Aspal</span>
                                    </div>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold" style="background-color: rgba(255, 255, 255, 0.15); color: #ffffff;">
                                        <?= number_format($pctAspal, 1) ?>%
                                    </span>
                                </div>
                                <h3 class="text-xl font-bold text-white"><?= format_number($totalAspal) ?> <span class="text-xs font-normal text-slate-300">m</span></h3>
                                <p class="text-[11px] font-medium text-slate-300 mt-0.5">Flexible / Aspal</p>
                            </div>

                             <!-- Kerikil -->
                             <div class="p-4 rounded-xl border shadow-sm hover:shadow-md transition-shadow" style="background-color: #7c461b; border-color: #5c3211;">
                                 <div class="flex items-center justify-between mb-2">
                                     <div class="flex items-center gap-1.5">
                                         <span class="w-2.5 h-2.5 rounded-full inline-block" style="background-color: #fde047;"></span>
                                         <span class="text-xs font-semibold text-white">Kerikil</span>
                                     </div>
                                     <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold" style="background-color: rgba(0, 0, 0, 0.25); color: #ffffff;">
                                         <?= number_format($pctAgregatTanah, 1) ?>%
                                     </span>
                                 </div>
                                 <h3 class="text-xl font-bold text-white"><?= format_number($totalAgregatTanah) ?> <span class="text-xs font-normal text-amber-100">m</span></h3>
                                 <p class="text-[11px] font-medium text-amber-100 mt-0.5">Kerikil / Tanah</p>
                             </div>

                            <!-- Belum Tembus -->
                            <div class="p-4 rounded-xl border shadow-sm hover:shadow-md transition-shadow" style="background-color: #7e22ce; border-color: #6b21a8;">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-2.5 h-2.5 rounded-full inline-block" style="background-color: #f0abfc;"></span>
                                        <span class="text-xs font-semibold text-white">Belum Tembus</span>
                                    </div>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold" style="background-color: rgba(0, 0, 0, 0.25); color: #ffffff;">
                                        <?= number_format($pctBelumTembus, 1) ?>%
                                    </span>
                                </div>
                                <h3 class="text-xl font-bold text-white"><?= format_number($totalBelumTembus) ?> <span class="text-xs font-normal text-purple-100">m</span></h3>
                                <p class="text-[11px] font-medium text-purple-100 mt-0.5">Belum Tembus</p>
                            </div>
                        </div>
                    </div>

                    <!-- Row 4: 4 Detail Segmentasi Penanganan Jalan -->
                    <?php if ($totalPanjangPenanganan > 0): ?>
                    <div x-show="showPenangananKondisi || showPenangananPerkerasan">
                        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                            <!-- Rencana -->
                            <div class="p-4 rounded-xl border shadow-sm hover:shadow-md transition-shadow" style="background-color: #f0f9ff; border-color: #bae6fd;">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-2.5 h-2.5 rounded-full inline-block" style="background-color: #0284c7;"></span>
                                        <span class="text-xs font-semibold text-sky-800">Rencana</span>
                                    </div>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded bg-sky-100 text-sky-800 text-[10px] font-bold">
                                        <?= number_format($pctRencana, 1) ?>%
                                    </span>
                                </div>
                                <h3 class="text-xl font-bold text-sky-700"><?= format_number($totalRencana) ?> <span class="text-xs font-normal text-sky-600">m</span></h3>
                                <p class="text-[11px] font-medium text-sky-600 mt-0.5">Usulan / Rencana</p>
                            </div>

                            <!-- Proses -->
                            <div class="p-4 rounded-xl border shadow-sm hover:shadow-md transition-shadow" style="background-color: #eef2ff; border-color: #c7d2fe;">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-2.5 h-2.5 rounded-full inline-block" style="background-color: #6366f1;"></span>
                                        <span class="text-xs font-semibold text-indigo-800">Sedang Dikerjakan</span>
                                    </div>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded bg-indigo-100 text-indigo-800 text-[10px] font-bold">
                                        <?= number_format($pctProses, 1) ?>%
                                    </span>
                                </div>
                                <h3 class="text-xl font-bold text-indigo-700"><?= format_number($totalProses) ?> <span class="text-xs font-normal text-indigo-600">m</span></h3>
                                <p class="text-[11px] font-medium text-indigo-600 mt-0.5">Dalam Pengerjaan</p>
                            </div>

                            <!-- Selesai -->
                            <div class="p-4 rounded-xl border shadow-sm hover:shadow-md transition-shadow" style="background-color: #ecfdf5; border-color: #a7f3d0;">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-2.5 h-2.5 rounded-full inline-block" style="background-color: #10b981;"></span>
                                        <span class="text-xs font-semibold text-emerald-800">Selesai</span>
                                    </div>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 text-[10px] font-bold">
                                        <?= number_format($pctSelesai, 1) ?>%
                                    </span>
                                </div>
                                <h3 class="text-xl font-bold text-emerald-700"><?= format_number($totalSelesai) ?> <span class="text-xs font-normal text-emerald-600">m</span></h3>
                                <p class="text-[11px] font-medium text-emerald-600 mt-0.5">Tuntas Ditangani</p>
                            </div>

                            <!-- Total Anggaran -->
                            <div class="p-4 rounded-xl border shadow-sm hover:shadow-md transition-shadow bg-gradient-to-br from-blue-50 to-indigo-50 border-blue-200">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-2.5 h-2.5 rounded-full inline-block bg-blue-600"></span>
                                        <span class="text-xs font-semibold text-blue-900">Total Anggaran</span>
                                    </div>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded bg-blue-100 text-blue-800 text-[10px] font-bold">
                                        <?= count($penanganans) ?> Paket
                                    </span>
                                </div>
                                <h3 class="text-lg font-bold text-blue-900 truncate" title="Rp <?= format_number($totalAnggaran) ?>">
                                    Rp <?= $totalAnggaran >= 1000000000 ? format_number($totalAnggaran / 1000000000, 2) . ' M' : ($totalAnggaran >= 1000000 ? format_number($totalAnggaran / 1000000, 2) . ' Jt' : format_number($totalAnggaran)) ?>
                                </h3>
                                <p class="text-[11px] font-medium text-blue-700 mt-0.5">Alokasi Dana Penanganan</p>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                </div>

                <!-- Linear Multi-Track Strip Map Chunks (Tiap 5000 STA) -->
                <div class="pt-4 space-y-4">
                    <?php foreach ($chunks as $chunkIdx => $chunk): ?>
                        <?php 
                            $chunkTotalPanjang = $chunk['end'] - $chunk['start']; 
                            if ($chunkTotalPanjang <= 0) continue;
                        ?>
                        <div class="space-y-2 border border-gray-100 rounded-2xl p-4 sm:p-5 bg-white shadow-sm">

                            <!-- Chunk Header: STA Range & Panjang Segmen -->
                            <div class="flex items-center justify-between pb-1.5 border-b border-gray-50 text-xs">
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-gray-50 text-gray-700 font-mono">
                                        STA <?= meter_to_sta($chunk['start']) ?> — <?= meter_to_sta($chunk['end']) ?>
                                    </span>
                                    <span class="text-gray-300 text-[11px]">•</span>
                                    <span class="text-[11px] text-gray-500 font-medium">Panjang Segmen: <strong class="text-gray-700 font-semibold"><?= format_number($chunkTotalPanjang) ?> m</strong></span>
                                </div>
                                <span class="text-[10px] text-gray-400 font-medium hidden sm:inline-block">Hover bar untuk detail layer</span>
                            </div>

                            <!-- ============================================================== -->
                            <!-- GROUP A: KONDISI JALAN (1. Kondisi 2025, 2. Penanganan, 3. Asumsi) -->
                            <!-- ============================================================== -->

                            <!-- 1. Track Kondisi 2025 (Eksisting Baseline) -->
                            <div x-show="showKondisi2025" x-transition.opacity class="relative pt-1">
                                <div class="flex rounded-lg overflow-hidden shadow-xs bg-gray-200 border border-gray-200" style="height: 22px;">
                                    <?php $cumulativePct = 0; ?>
                                    <?php foreach ($chunk['stripmaps'] as $sm): ?>
                                        <?php
                                            $smTotal = $sm['panjang'] > 0 ? $sm['panjang'] : 1;
                                            $smPct   = $chunkTotalPanjang > 0 ? ($sm['panjang'] / $chunkTotalPanjang) * 100 : 0;
                                        ?>
                                        <div class="flex h-full flex-shrink-0" style="width: <?= number_format($smPct, 4, '.', '') ?>%">
                                            <?php if (!empty($sm['is_gap'])): ?>
                                                <?php
                                                    $subWidthGlobal = ($sm['panjang'] / $chunkTotalPanjang) * 100;
                                                    $subMidPct = $cumulativePct + ($subWidthGlobal / 2);
                                                    $cumulativePct += $subWidthGlobal;
                                                    $staLabelStr = meter_to_sta($sm['sta_awal']) . ' — ' . meter_to_sta($sm['sta_akhir']);
                                                ?>
                                                <div class="h-full w-full flex-shrink-0 relative group transition-all duration-300 cursor-pointer bg-gray-200"
                                                     @mouseenter="if (window.matchMedia('(hover: hover)').matches) { activeLabel = { panjang: '<?= format_number($sm['panjang']) ?>', kondisi: 'Belum Ada Data Kondisi 2025', sta: '<?= $staLabelStr ?>', color: '#6b7280', subinfo: 'Kondisi Eksisting 2025' }; activePct = <?= round($subMidPct, 2) ?>; activeChunk = 'sm_<?= $chunkIdx ?>' }"
                                                     @mouseleave="if (window.matchMedia('(hover: hover)').matches) { activeLabel = null; activeChunk = null }"
                                                     @click.stop="activeLabel = (activeLabel && activeLabel.sta === '<?= $staLabelStr ?>' && activeLabel.kondisi === 'Belum Ada Data Kondisi 2025') ? null : { panjang: '<?= format_number($sm['panjang']) ?>', kondisi: 'Belum Ada Data Kondisi 2025', sta: '<?= $staLabelStr ?>', color: '#6b7280', subinfo: 'Kondisi Eksisting 2025' }; activePct = <?= round($subMidPct, 2) ?>; activeChunk = 'sm_<?= $chunkIdx ?>'">
                                                    <div class="absolute inset-0 ring-1 ring-black/5 ring-inset opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                                </div>
                                            <?php else: ?>
                                                <?php
                                                    $segConditions = [
                                                        ['label' => 'Baik',         'value' => $sm['baik'],         'color' => '#10b981'],
                                                        ['label' => 'Sedang',       'value' => $sm['sedang'],       'color' => '#facc15'],
                                                        ['label' => 'Rusak Ringan', 'value' => $sm['rusak_ringan'], 'color' => '#f97316'],
                                                        ['label' => 'Rusak Berat',  'value' => $sm['rusak_berat'],  'color' => '#ef4444'],
                                                    ];
                                                    $activeSeg = array_values(array_filter($segConditions, fn($c) => $c['value'] > 0));
                                                ?>
                                                <?php foreach ($activeSeg as $sc): ?>
                                                    <?php
                                                        $scPct = ($sc['value'] / $smTotal) * 100;
                                                        $subWidthGlobal = ($sc['value'] / $chunkTotalPanjang) * 100;
                                                        $subMidPct = $cumulativePct + ($subWidthGlobal / 2);
                                                        $cumulativePct += $subWidthGlobal;
                                                        $staLabelStr = meter_to_sta($sm['sta_awal']) . ' — ' . meter_to_sta($sm['sta_akhir']);
                                                    ?>
                                                    <div class="h-full flex-shrink-0 relative group transition-all duration-300 cursor-pointer"
                                                         style="width: <?= number_format($scPct, 4, '.', '') ?>%; background-color: <?= $sc['color'] ?>;"
                                                         @mouseenter="if (window.matchMedia('(hover: hover)').matches) { activeLabel = { panjang: '<?= format_number($sc['value']) ?>', kondisi: '<?= $sc['label'] ?>', sta: '<?= $staLabelStr ?>', color: '<?= $sc['color'] ?>', subinfo: 'Kondisi Eksisting 2025' }; activePct = <?= round($subMidPct, 2) ?>; activeChunk = 'sm_<?= $chunkIdx ?>' }"
                                                         @mouseleave="if (window.matchMedia('(hover: hover)').matches) { activeLabel = null; activeChunk = null }"
                                                         @click.stop="activeLabel = (activeLabel && activeLabel.sta === '<?= $staLabelStr ?>' && activeLabel.kondisi === '<?= $sc['label'] ?>') ? null : { panjang: '<?= format_number($sc['value']) ?>', kondisi: '<?= $sc['label'] ?>', sta: '<?= $staLabelStr ?>', color: '<?= $sc['color'] ?>', subinfo: 'Kondisi Eksisting 2025' }; activePct = <?= round($subMidPct, 2) ?>; activeChunk = 'sm_<?= $chunkIdx ?>'">
                                                        <div class="absolute inset-0 ring-1 ring-white/20 ring-inset opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                                    </div>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>

                                <!-- Tooltip Hover Strip Map 2025 -->
                                <div class="relative w-full h-0 z-20">
                                    <template x-if="activeLabel && activeChunk === 'sm_<?= $chunkIdx ?>'">
                                        <div class="absolute top-1.5 flex flex-col items-center -translate-x-1/2 transition-all duration-150 ease-out"
                                             :style="'left:' + activePct + '%'">
                                            <div class="w-px h-3" :style="'background-color:' + activeLabel.color"></div>
                                            <div class="mt-0.5 px-2.5 py-1.5 rounded-lg border shadow-md text-center whitespace-nowrap bg-white"
                                                 :style="'border-color:' + activeLabel.color">
                                                <p class="text-xs font-bold" :style="'color:' + activeLabel.color" x-text="activeLabel.panjang + ' m'"></p>
                                                <p class="text-[10px] font-semibold text-gray-700" x-text="activeLabel.kondisi"></p>
                                                <p class="text-[9px] font-mono text-gray-500" x-text="activeLabel.sta"></p>
                                                <template x-if="activeLabel.subinfo">
                                                    <p class="text-[9px] font-medium text-emerald-700 border-t border-gray-100 pt-0.5 mt-0.5" x-text="activeLabel.subinfo"></p>
                                                </template>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <!-- 2. Track Penanganan Kondisi (Segmen Pekerjaan Penanganan) -->
                            <div x-show="showPenangananKondisi" x-transition.opacity class="relative">
                                <div class="flex rounded-lg overflow-hidden shadow-xs bg-gray-200 border border-gray-200" style="height: 22px;">
                                    <?php $cumulativePnPct = 0; ?>
                                    <?php foreach ($chunk['penanganans'] as $pn): ?>
                                        <?php
                                            $pnTotal = $pn['panjang'] > 0 ? $pn['panjang'] : 1;
                                            $pnPct   = $chunkTotalPanjang > 0 ? ($pn['panjang'] / $chunkTotalPanjang) * 100 : 0;
                                        ?>
                                        <div class="flex h-full flex-shrink-0" style="width: <?= number_format($pnPct, 4, '.', '') ?>%">
                                            <?php if (!empty($pn['is_gap'])): ?>
                                                <?php
                                                    $subWidthGlobal = ($pn['panjang'] / $chunkTotalPanjang) * 100;
                                                    $subMidPct = $cumulativePnPct + ($subWidthGlobal / 2);
                                                    $cumulativePnPct += $subWidthGlobal;
                                                    $staLabelStr = meter_to_sta($pn['sta_awal']) . ' — ' . meter_to_sta($pn['sta_akhir']);
                                                ?>
                                                <div class="h-full w-full flex-shrink-0 relative group transition-all duration-300 cursor-pointer bg-gray-200"
                                                     @mouseenter="if (window.matchMedia('(hover: hover)').matches) { activeLabel = { panjang: '<?= format_number($pn['panjang']) ?>', kondisi: 'Belum Ada Penanganan', sta: '<?= $staLabelStr ?>', color: '#6b7280', subinfo: 'Paket Penanganan' }; activePct = <?= round($subMidPct, 2) ?>; activeChunk = 'pn_k_<?= $chunkIdx ?>' }"
                                                     @mouseleave="if (window.matchMedia('(hover: hover)').matches) { activeLabel = null; activeChunk = null }"
                                                     @click.stop="activeLabel = (activeLabel && activeLabel.sta === '<?= $staLabelStr ?>' && activeLabel.kondisi === 'Belum Ada Penanganan') ? null : { panjang: '<?= format_number($pn['panjang']) ?>', kondisi: 'Belum Ada Penanganan', sta: '<?= $staLabelStr ?>', color: '#6b7280', subinfo: 'Paket Penanganan' }; activePct = <?= round($subMidPct, 2) ?>; activeChunk = 'pn_k_<?= $chunkIdx ?>'">
                                                    <div class="absolute inset-0 ring-1 ring-black/5 ring-inset opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                                </div>
                                            <?php else: ?>
                                                <?php
                                                    $subWidthGlobal = ($pn['panjang'] / $chunkTotalPanjang) * 100;
                                                    $subMidPct = $cumulativePnPct + ($subWidthGlobal / 2);
                                                    $cumulativePnPct += $subWidthGlobal;
                                                    $staLabelStr = meter_to_sta($pn['sta_awal']) . ' — ' . meter_to_sta($pn['sta_akhir']);
                                                    $targetCondLabel = ucfirst($pn['kondisi_prediksi'] ?? 'Baik');
                                                    $subInfoStr = 'Target Kondisi: ' . $targetCondLabel . ' • ' . $pn['status_label'] . ' (Tahun ' . $pn['tahun'] . ')' . ($pn['anggaran'] > 0 ? ' • Rp ' . format_number($pn['anggaran']) : '');
                                                ?>
                                                <div class="h-full flex-shrink-0 relative group transition-all duration-300 cursor-pointer"
                                                     x-show="penangananYearFilter === 'all' || parseInt(penangananYearFilter) === <?= (int)$pn['tahun'] ?> || ('<?= $pn['status'] ?>' === 'selesai' && <?= (int)$pn['tahun'] ?> <= parseInt(penangananYearFilter))"
                                                     style="width: 100%; background-color: <?= $pn['warna_kondisi'] ?>;"
                                                     @mouseenter="if (window.matchMedia('(hover: hover)').matches) { activeLabel = { panjang: '<?= format_number($pn['panjang']) ?>', kondisi: '<?= e($pn['jenis_penanganan']) ?> (Target: <?= $targetCondLabel ?>)', sta: '<?= $staLabelStr ?>', color: '<?= $pn['warna_kondisi'] ?>', subinfo: '<?= e($subInfoStr) ?>' }; activePct = <?= round($subMidPct, 2) ?>; activeChunk = 'pn_k_<?= $chunkIdx ?>' }"
                                                     @mouseleave="if (window.matchMedia('(hover: hover)').matches) { activeLabel = null; activeChunk = null }"
                                                     @click.stop="activeLabel = (activeLabel && activeLabel.sta === '<?= $staLabelStr ?>' && activeLabel.kondisi.startsWith('<?= e($pn['jenis_penanganan']) ?>')) ? null : { panjang: '<?= format_number($pn['panjang']) ?>', kondisi: '<?= e($pn['jenis_penanganan']) ?> (Target: <?= $targetCondLabel ?>)', sta: '<?= $staLabelStr ?>', color: '<?= $pn['warna_kondisi'] ?>', subinfo: '<?= e($subInfoStr) ?>' }; activePct = <?= round($subMidPct, 2) ?>; activeChunk = 'pn_k_<?= $chunkIdx ?>'">
                                                    <div class="absolute inset-0 ring-1 ring-white/20 ring-inset opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>

                                <!-- Tooltip Hover Penanganan Kondisi -->
                                <div class="relative w-full h-0 z-20">
                                    <template x-if="activeLabel && activeChunk === 'pn_k_<?= $chunkIdx ?>'">
                                        <div class="absolute top-1.5 flex flex-col items-center -translate-x-1/2 transition-all duration-150 ease-out"
                                             :style="'left:' + activePct + '%'">
                                            <div class="w-px h-3" :style="'background-color:' + activeLabel.color"></div>
                                            <div class="mt-0.5 px-2.5 py-1.5 rounded-lg border shadow-md text-center whitespace-nowrap bg-white"
                                                 :style="'border-color:' + activeLabel.color">
                                                <p class="text-xs font-bold" :style="'color:' + activeLabel.color" x-text="activeLabel.panjang + ' m'"></p>
                                                <p class="text-[10px] font-semibold text-gray-700" x-text="activeLabel.kondisi"></p>
                                                <p class="text-[9px] font-mono text-gray-500" x-text="activeLabel.sta"></p>
                                                <template x-if="activeLabel.subinfo">
                                                    <p class="text-[9px] font-medium text-purple-700 border-t border-gray-100 pt-0.5 mt-0.5" x-text="activeLabel.subinfo"></p>
                                                </template>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <!-- 3. Track Asumsi Kondisi Pasca Penanganan -->
                            <div x-show="showAsumsiKondisi" x-transition.opacity class="relative">
                                <div class="flex rounded-lg overflow-hidden shadow-xs bg-gray-200 border border-gray-200" style="height: 22px;">
                                    <?php $cumulativeAsmSmPct = 0; ?>
                                    <?php foreach ($chunk['asumsi_stripmaps'] as $asmSm): ?>
                                        <?php
                                            $asmSmTotal = $asmSm['panjang'] > 0 ? $asmSm['panjang'] : 1;
                                            $asmSmPct   = $chunkTotalPanjang > 0 ? ($asmSm['panjang'] / $chunkTotalPanjang) * 100 : 0;
                                        ?>
                                        <div class="flex h-full flex-shrink-0" style="width: <?= number_format($asmSmPct, 4, '.', '') ?>%">
                                            <?php if (!empty($asmSm['is_gap'])): ?>
                                                <?php
                                                    $subWidthGlobal = ($asmSm['panjang'] / $chunkTotalPanjang) * 100;
                                                    $subMidPct = $cumulativeAsmSmPct + ($subWidthGlobal / 2);
                                                    $cumulativeAsmSmPct += $subWidthGlobal;
                                                    $staLabelStr = meter_to_sta($asmSm['sta_awal']) . ' — ' . meter_to_sta($asmSm['sta_akhir']);
                                                ?>
                                                <div class="h-full w-full flex-shrink-0 relative group transition-all duration-300 cursor-pointer bg-gray-200"
                                                     @mouseenter="if (window.matchMedia('(hover: hover)').matches) { activeLabel = { panjang: '<?= format_number($asmSm['panjang']) ?>', kondisi: 'Belum Ada Data Asumsi', sta: '<?= $staLabelStr ?>', color: '#6b7280', subinfo: 'Asumsi Kondisi Pasca Penanganan' }; activePct = <?= round($subMidPct, 2) ?>; activeChunk = 'asm_sm_<?= $chunkIdx ?>' }"
                                                     @mouseleave="if (window.matchMedia('(hover: hover)').matches) { activeLabel = null; activeChunk = null }"
                                                     @click.stop="activeLabel = (activeLabel && activeLabel.sta === '<?= $staLabelStr ?>') ? null : { panjang: '<?= format_number($asmSm['panjang']) ?>', kondisi: 'Belum Ada Data Asumsi', sta: '<?= $staLabelStr ?>', color: '#6b7280', subinfo: 'Asumsi Kondisi Pasca Penanganan' }; activePct = <?= round($subMidPct, 2) ?>; activeChunk = 'asm_sm_<?= $chunkIdx ?>'">
                                                    <div class="absolute inset-0 ring-1 ring-black/5 ring-inset opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                                </div>
                                            <?php else: ?>
                                                <?php
                                                    $segConditions = [
                                                        ['label' => 'Baik',         'value' => $asmSm['baik'],         'color' => '#10b981'],
                                                        ['label' => 'Sedang',       'value' => $asmSm['sedang'],       'color' => '#facc15'],
                                                        ['label' => 'Rusak Ringan', 'value' => $asmSm['rusak_ringan'], 'color' => '#f97316'],
                                                        ['label' => 'Rusak Berat',  'value' => $asmSm['rusak_berat'],  'color' => '#ef4444'],
                                                    ];
                                                    $activeSeg = array_values(array_filter($segConditions, fn($c) => $c['value'] > 0));
                                                ?>
                                                <?php foreach ($activeSeg as $sc): ?>
                                                    <?php
                                                        $scPct = ($sc['value'] / $asmSmTotal) * 100;
                                                        $subWidthGlobal = ($sc['value'] / $chunkTotalPanjang) * 100;
                                                        $subMidPct = $cumulativeAsmSmPct + ($subWidthGlobal / 2);
                                                        $cumulativeAsmSmPct += $subWidthGlobal;
                                                        $staLabelStr = meter_to_sta($asmSm['sta_awal']) . ' — ' . meter_to_sta($asmSm['sta_akhir']);
                                                        $isImproved = !empty($asmSm['is_asumsi']);
                                                        $subInfoStr = $isImproved ? ('Asumsi Pasca Penanganan: ' . ($asmSm['pn_info'] ?? 'Penanganan Jalan')) : 'Kondisi belum/tidak ditangani';
                                                    ?>
                                                    <div class="h-full flex-shrink-0 relative group transition-all duration-300 cursor-pointer"
                                                         style="width: <?= number_format($scPct, 4, '.', '') ?>%; background-color: <?= $sc['color'] ?>;"
                                                         @mouseenter="if (window.matchMedia('(hover: hover)').matches) { activeLabel = { panjang: '<?= format_number($sc['value']) ?>', kondisi: '<?= $sc['label'] ?>', sta: '<?= $staLabelStr ?>', color: '<?= $sc['color'] ?>', subinfo: '<?= e($subInfoStr) ?>' }; activePct = <?= round($subMidPct, 2) ?>; activeChunk = 'asm_sm_<?= $chunkIdx ?>' }"
                                                         @mouseleave="if (window.matchMedia('(hover: hover)').matches) { activeLabel = null; activeChunk = null }"
                                                         @click.stop="activeLabel = (activeLabel && activeLabel.sta === '<?= $staLabelStr ?>' && activeLabel.kondisi === '<?= $sc['label'] ?>') ? null : { panjang: '<?= format_number($sc['value']) ?>', kondisi: '<?= $sc['label'] ?>', sta: '<?= $staLabelStr ?>', color: '<?= $sc['color'] ?>', subinfo: '<?= e($subInfoStr) ?>' }; activePct = <?= round($subMidPct, 2) ?>; activeChunk = 'asm_sm_<?= $chunkIdx ?>'">
                                                        <div class="absolute inset-0 ring-1 ring-white/20 ring-inset opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                                    </div>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>

                                <!-- Tooltip Hover Asumsi Kondisi -->
                                <div class="relative w-full h-0 z-20">
                                    <template x-if="activeLabel && activeChunk === 'asm_sm_<?= $chunkIdx ?>'">
                                        <div class="absolute top-1.5 flex flex-col items-center -translate-x-1/2 transition-all duration-150 ease-out"
                                             :style="'left:' + activePct + '%'">
                                            <div class="w-px h-3" :style="'background-color:' + activeLabel.color"></div>
                                            <div class="mt-0.5 px-2.5 py-1.5 rounded-lg border shadow-md text-center whitespace-nowrap bg-white"
                                                 :style="'border-color:' + activeLabel.color">
                                                <p class="text-xs font-bold" :style="'color:' + activeLabel.color" x-text="activeLabel.panjang + ' m'"></p>
                                                <p class="text-[10px] font-semibold text-gray-700" x-text="activeLabel.kondisi"></p>
                                                <p class="text-[9px] font-mono text-gray-500" x-text="activeLabel.sta"></p>
                                                <template x-if="activeLabel.subinfo">
                                                    <p class="text-[9px] font-medium text-purple-700 border-t border-gray-100 pt-0.5 mt-0.5" x-text="activeLabel.subinfo"></p>
                                                </template>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <!-- =================================================================== -->
                            <!-- GROUP B: PERKERASAN JALAN (1. Perkerasan 2025, 2. Penanganan, 3. Asumsi) -->
                            <!-- =================================================================== -->

                            <!-- 4. Track Perkerasan 2025 (Eksisting Baseline) -->
                            <div x-show="showPerkerasan2025" x-transition.opacity class="relative">
                                <div class="flex rounded-lg overflow-hidden shadow-xs bg-gray-200 border border-gray-200" style="height: 22px;">
                                    <?php $cumulativePkPct = 0; ?>
                                    <?php foreach ($chunk['perkerasans'] as $pk): ?>
                                        <?php
                                            $pkTotal = $pk['panjang'] > 0 ? $pk['panjang'] : 1;
                                            $pkPct   = $chunkTotalPanjang > 0 ? ($pk['panjang'] / $chunkTotalPanjang) * 100 : 0;
                                        ?>
                                        <div class="flex h-full flex-shrink-0" style="width: <?= number_format($pkPct, 4, '.', '') ?>%">
                                            <?php if (!empty($pk['is_gap'])): ?>
                                                <?php
                                                    $subWidthGlobal = ($pk['panjang'] / $chunkTotalPanjang) * 100;
                                                    $subMidPct = $cumulativePkPct + ($subWidthGlobal / 2);
                                                    $cumulativePkPct += $subWidthGlobal;
                                                    $staLabelStr = meter_to_sta($pk['sta_awal']) . ' — ' . meter_to_sta($pk['sta_akhir']);
                                                ?>
                                                <div class="h-full w-full flex-shrink-0 relative group transition-all duration-300 cursor-pointer bg-gray-200"
                                                     @mouseenter="if (window.matchMedia('(hover: hover)').matches) { activeLabel = { panjang: '<?= format_number($pk['panjang']) ?>', kondisi: 'Belum Ada Data Perkerasan 2025', sta: '<?= $staLabelStr ?>', color: '#6b7280', subinfo: 'Perkerasan Eksisting 2025' }; activePct = <?= round($subMidPct, 2) ?>; activeChunk = 'pk_<?= $chunkIdx ?>' }"
                                                     @mouseleave="if (window.matchMedia('(hover: hover)').matches) { activeLabel = null; activeChunk = null }"
                                                     @click.stop="activeLabel = (activeLabel && activeLabel.sta === '<?= $staLabelStr ?>') ? null : { panjang: '<?= format_number($pk['panjang']) ?>', kondisi: 'Belum Ada Data Perkerasan 2025', sta: '<?= $staLabelStr ?>', color: '#6b7280', subinfo: 'Perkerasan Eksisting 2025' }; activePct = <?= round($subMidPct, 2) ?>; activeChunk = 'pk_<?= $chunkIdx ?>'">
                                                    <div class="absolute inset-0 ring-1 ring-black/5 ring-inset opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                                </div>
                                            <?php else: ?>
                                                <?php
                                                    $paveTypes = [
                                                        ['label' => 'Rigid',           'value' => $pk['rigid'],         'color' => '#475569'],
                                                        ['label' => 'Aspal',           'value' => $pk['aspal'],         'color' => '#0f172a'],
                                                        ['label' => 'Kerikil', 'value' => $pk['agregat_tanah'], 'color' => '#7c461b'],
                                                        ['label' => 'Belum Tembus',    'value' => $pk['belum_tembus'],  'color' => '#7e22ce'],
                                                    ];
                                                    $activePave = array_values(array_filter($paveTypes, fn($c) => $c['value'] > 0));
                                                ?>
                                                <?php foreach ($activePave as $pt): ?>
                                                    <?php
                                                        $ptPct = ($pt['value'] / $pkTotal) * 100;
                                                        $subWidthGlobal = ($pt['value'] / $chunkTotalPanjang) * 100;
                                                        $subMidPct = $cumulativePkPct + ($subWidthGlobal / 2);
                                                        $cumulativePkPct += $subWidthGlobal;
                                                        $staLabelStr = meter_to_sta($pk['sta_awal']) . ' — ' . meter_to_sta($pk['sta_akhir']);
                                                    ?>
                                                    <div class="h-full flex-shrink-0 relative group transition-all duration-300 cursor-pointer"
                                                         style="width: <?= number_format($ptPct, 4, '.', '') ?>%; background-color: <?= $pt['color'] ?>;"
                                                         @mouseenter="if (window.matchMedia('(hover: hover)').matches) { activeLabel = { panjang: '<?= format_number($pt['value']) ?>', kondisi: '<?= $pt['label'] ?>', sta: '<?= $staLabelStr ?>', color: '<?= $pt['color'] ?>', subinfo: 'Perkerasan Eksisting 2025' }; activePct = <?= round($subMidPct, 2) ?>; activeChunk = 'pk_<?= $chunkIdx ?>' }"
                                                         @mouseleave="if (window.matchMedia('(hover: hover)').matches) { activeLabel = null; activeChunk = null }"
                                                         @click.stop="activeLabel = (activeLabel && activeLabel.sta === '<?= $staLabelStr ?>' && activeLabel.kondisi === '<?= $pt['label'] ?>') ? null : { panjang: '<?= format_number($pt['value']) ?>', kondisi: '<?= $pt['label'] ?>', sta: '<?= $staLabelStr ?>', color: '<?= $pt['color'] ?>', subinfo: 'Perkerasan Eksisting 2025' }; activePct = <?= round($subMidPct, 2) ?>; activeChunk = 'pk_<?= $chunkIdx ?>'">
                                                        <div class="absolute inset-0 ring-1 ring-white/20 ring-inset opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                                    </div>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>

                                <!-- Tooltip Hover Perkerasan 2025 -->
                                <div class="relative w-full h-0 z-20">
                                    <template x-if="activeLabel && activeChunk === 'pk_<?= $chunkIdx ?>'">
                                        <div class="absolute top-1.5 flex flex-col items-center -translate-x-1/2 transition-all duration-150 ease-out"
                                             :style="'left:' + activePct + '%'">
                                            <div class="w-px h-3" :style="'background-color:' + activeLabel.color"></div>
                                            <div class="mt-0.5 px-2.5 py-1.5 rounded-lg border shadow-md text-center whitespace-nowrap bg-white"
                                                 :style="'border-color:' + activeLabel.color">
                                                <p class="text-xs font-bold" :style="'color:' + activeLabel.color" x-text="activeLabel.panjang + ' m'"></p>
                                                <p class="text-[10px] font-semibold text-gray-700" x-text="activeLabel.kondisi"></p>
                                                <p class="text-[9px] font-mono text-gray-500" x-text="activeLabel.sta"></p>
                                                <template x-if="activeLabel.subinfo">
                                                    <p class="text-[9px] font-medium text-slate-700 border-t border-gray-100 pt-0.5 mt-0.5" x-text="activeLabel.subinfo"></p>
                                                </template>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <!-- 5. Track Penanganan Perkerasan (Pekerjaan Struktur Jenis Perkerasan) -->
                            <div x-show="showPenangananPerkerasan" x-transition.opacity class="relative">
                                <div class="flex rounded-lg overflow-hidden shadow-xs bg-gray-200 border border-gray-200" style="height: 22px;">
                                    <?php $cumulativePnPavePct = 0; ?>
                                    <?php foreach ($chunk['penanganans'] as $pn): ?>
                                        <?php
                                            $pnTotal = $pn['panjang'] > 0 ? $pn['panjang'] : 1;
                                            $pnPct   = $chunkTotalPanjang > 0 ? ($pn['panjang'] / $chunkTotalPanjang) * 100 : 0;
                                        ?>
                                        <div class="flex h-full flex-shrink-0" style="width: <?= number_format($pnPct, 4, '.', '') ?>%">
                                            <?php if (!empty($pn['is_gap'])): ?>
                                                <?php
                                                    $subWidthGlobal = ($pn['panjang'] / $chunkTotalPanjang) * 100;
                                                    $subMidPct = $cumulativePnPavePct + ($subWidthGlobal / 2);
                                                    $cumulativePnPavePct += $subWidthGlobal;
                                                    $staLabelStr = meter_to_sta($pn['sta_awal']) . ' — ' . meter_to_sta($pn['sta_akhir']);
                                                ?>
                                                <div class="h-full w-full flex-shrink-0 relative group transition-all duration-300 cursor-pointer bg-gray-200"
                                                     @mouseenter="if (window.matchMedia('(hover: hover)').matches) { activeLabel = { panjang: '<?= format_number($pn['panjang']) ?>', kondisi: 'Belum Ada Pekerjaan Perkerasan', sta: '<?= $staLabelStr ?>', color: '#6b7280', subinfo: 'Penanganan Perkerasan' }; activePct = <?= round($subMidPct, 2) ?>; activeChunk = 'pn_p_<?= $chunkIdx ?>' }"
                                                     @mouseleave="if (window.matchMedia('(hover: hover)').matches) { activeLabel = null; activeChunk = null }"
                                                     @click.stop="activeLabel = (activeLabel && activeLabel.sta === '<?= $staLabelStr ?>') ? null : { panjang: '<?= format_number($pn['panjang']) ?>', kondisi: 'Belum Ada Pekerjaan Perkerasan', sta: '<?= $staLabelStr ?>', color: '#6b7280', subinfo: 'Penanganan Perkerasan' }; activePct = <?= round($subMidPct, 2) ?>; activeChunk = 'pn_p_<?= $chunkIdx ?>'">
                                                    <div class="absolute inset-0 ring-1 ring-black/5 ring-inset opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                                </div>
                                            <?php else: ?>
                                                <?php
                                                    $subWidthGlobal = ($pn['panjang'] / $chunkTotalPanjang) * 100;
                                                    $subMidPct = $cumulativePnPavePct + ($subWidthGlobal / 2);
                                                    $cumulativePnPavePct += $subWidthGlobal;
                                                    $staLabelStr = meter_to_sta($pn['sta_awal']) . ' — ' . meter_to_sta($pn['sta_akhir']);
                                                    $targetPaveLabel = ucfirst($pn['perkerasan_hasil'] ?? 'Aspal');
                                                    $subInfoStr = 'Rencana Perkerasan: ' . $targetPaveLabel . ' • ' . $pn['jenis_penanganan'] . ' (Tahun ' . $pn['tahun'] . ')';
                                                ?>
                                                <div class="h-full flex-shrink-0 relative group transition-all duration-300 cursor-pointer"
                                                     x-show="penangananYearFilter === 'all' || parseInt(penangananYearFilter) === <?= (int)$pn['tahun'] ?> || ('<?= $pn['status'] ?>' === 'selesai' && <?= (int)$pn['tahun'] ?> <= parseInt(penangananYearFilter))"
                                                     style="width: 100%; background-color: <?= $pn['warna_perkerasan'] ?>;"
                                                     @mouseenter="if (window.matchMedia('(hover: hover)').matches) { activeLabel = { panjang: '<?= format_number($pn['panjang']) ?>', kondisi: 'Rencana: <?= e($targetPaveLabel) ?> • <?= e($pn['jenis_penanganan']) ?>', sta: '<?= $staLabelStr ?>', color: '<?= $pn['warna_perkerasan'] ?>', subinfo: '<?= e($subInfoStr) ?>' }; activePct = <?= round($subMidPct, 2) ?>; activeChunk = 'pn_p_<?= $chunkIdx ?>' }"
                                                     @mouseleave="if (window.matchMedia('(hover: hover)').matches) { activeLabel = null; activeChunk = null }"
                                                     @click.stop="activeLabel = (activeLabel && activeLabel.sta === '<?= $staLabelStr ?>' && activeLabel.kondisi.startsWith('Rencana: <?= e($targetPaveLabel) ?>')) ? null : { panjang: '<?= format_number($pn['panjang']) ?>', kondisi: 'Rencana: <?= e($targetPaveLabel) ?> • <?= e($pn['jenis_penanganan']) ?>', sta: '<?= $staLabelStr ?>', color: '<?= $pn['warna_perkerasan'] ?>', subinfo: '<?= e($subInfoStr) ?>' }; activePct = <?= round($subMidPct, 2) ?>; activeChunk = 'pn_p_<?= $chunkIdx ?>'">
                                                    <div class="absolute inset-0 ring-1 ring-white/20 ring-inset opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>

                                <!-- Tooltip Hover Penanganan Perkerasan -->
                                <div class="relative w-full h-0 z-20">
                                    <template x-if="activeLabel && activeChunk === 'pn_p_<?= $chunkIdx ?>'">
                                        <div class="absolute top-1.5 flex flex-col items-center -translate-x-1/2 transition-all duration-150 ease-out"
                                             :style="'left:' + activePct + '%'">
                                            <div class="w-px h-3" :style="'background-color:' + activeLabel.color"></div>
                                            <div class="mt-0.5 px-2.5 py-1.5 rounded-lg border shadow-md text-center whitespace-nowrap bg-white"
                                                 :style="'border-color:' + activeLabel.color">
                                                <p class="text-xs font-bold" :style="'color:' + activeLabel.color" x-text="activeLabel.panjang + ' m'"></p>
                                                <p class="text-[10px] font-semibold text-gray-700" x-text="activeLabel.kondisi"></p>
                                                <p class="text-[9px] font-mono text-gray-500" x-text="activeLabel.sta"></p>
                                                <template x-if="activeLabel.subinfo">
                                                    <p class="text-[9px] font-medium text-amber-700 border-t border-gray-100 pt-0.5 mt-0.5" x-text="activeLabel.subinfo"></p>
                                                </template>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <!-- 6. Track Asumsi Perkerasan Pasca Penanganan -->
                            <div x-show="showAsumsiPerkerasan" x-transition.opacity class="relative">
                                <div class="flex rounded-lg overflow-hidden shadow-xs bg-gray-200 border border-gray-200" style="height: 22px;">
                                    <?php $cumulativeAsmPkPct = 0; ?>
                                    <?php foreach ($chunk['asumsi_perkerasans'] as $asmPk): ?>
                                        <?php
                                            $asmPkTotal = $asmPk['panjang'] > 0 ? $asmPk['panjang'] : 1;
                                            $asmPkPct   = $chunkTotalPanjang > 0 ? ($asmPk['panjang'] / $chunkTotalPanjang) * 100 : 0;
                                        ?>
                                        <div class="flex h-full flex-shrink-0" style="width: <?= number_format($asmPkPct, 4, '.', '') ?>%">
                                            <?php if (!empty($asmPk['is_gap'])): ?>
                                                <?php
                                                    $subWidthGlobal = ($asmPk['panjang'] / $chunkTotalPanjang) * 100;
                                                    $subMidPct = $cumulativeAsmPkPct + ($subWidthGlobal / 2);
                                                    $cumulativeAsmPkPct += $subWidthGlobal;
                                                    $staLabelStr = meter_to_sta($asmPk['sta_awal']) . ' — ' . meter_to_sta($asmPk['sta_akhir']);
                                                ?>
                                                <div class="h-full w-full flex-shrink-0 relative group transition-all duration-300 cursor-pointer bg-gray-200"
                                                     @mouseenter="if (window.matchMedia('(hover: hover)').matches) { activeLabel = { panjang: '<?= format_number($asmPk['panjang']) ?>', kondisi: 'Belum Ada Data Asumsi Perkerasan', sta: '<?= $staLabelStr ?>', color: '#6b7280', subinfo: 'Asumsi Perkerasan' }; activePct = <?= round($subMidPct, 2) ?>; activeChunk = 'asm_pk_<?= $chunkIdx ?>' }"
                                                     @mouseleave="if (window.matchMedia('(hover: hover)').matches) { activeLabel = null; activeChunk = null }"
                                                     @click.stop="activeLabel = (activeLabel && activeLabel.sta === '<?= $staLabelStr ?>') ? null : { panjang: '<?= format_number($asmPk['panjang']) ?>', kondisi: 'Belum Ada Data Asumsi Perkerasan', sta: '<?= $staLabelStr ?>', color: '#6b7280', subinfo: 'Asumsi Perkerasan' }; activePct = <?= round($subMidPct, 2) ?>; activeChunk = 'asm_pk_<?= $chunkIdx ?>'">
                                                    <div class="absolute inset-0 ring-1 ring-black/5 ring-inset opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                                </div>
                                            <?php else: ?>
                                                <?php
                                                    $paveTypes = [
                                                        ['label' => 'Rigid',           'value' => $asmPk['rigid'],         'color' => '#475569'],
                                                        ['label' => 'Aspal',           'value' => $asmPk['aspal'],         'color' => '#0f172a'],
                                                        ['label' => 'Kerikil', 'value' => $asmPk['agregat_tanah'], 'color' => '#7c461b'],
                                                        ['label' => 'Belum Tembus',    'value' => $asmPk['belum_tembus'],  'color' => '#7e22ce'],
                                                    ];
                                                    $activePave = array_values(array_filter($paveTypes, fn($c) => $c['value'] > 0));
                                                ?>
                                                <?php foreach ($activePave as $pt): ?>
                                                    <?php
                                                        $ptPct = ($pt['value'] / $asmPkTotal) * 100;
                                                        $subWidthGlobal = ($pt['value'] / $chunkTotalPanjang) * 100;
                                                        $subMidPct = $cumulativeAsmPkPct + ($subWidthGlobal / 2);
                                                        $cumulativeAsmPkPct += $subWidthGlobal;
                                                        $staLabelStr = meter_to_sta($asmPk['sta_awal']) . ' — ' . meter_to_sta($asmPk['sta_akhir']);
                                                        $isUpgraded = !empty($asmPk['is_asumsi']);
                                                        $subInfoStr = $isUpgraded ? ('Asumsi Perkerasan Baru: ' . ($asmPk['pn_info'] ?? 'Penanganan')) : 'Struktur perkerasan eksisting';
                                                    ?>
                                                    <div class="h-full flex-shrink-0 relative group transition-all duration-300 cursor-pointer"
                                                         style="width: <?= number_format($ptPct, 4, '.', '') ?>%; background-color: <?= $pt['color'] ?>;"
                                                         @mouseenter="if (window.matchMedia('(hover: hover)').matches) { activeLabel = { panjang: '<?= format_number($pt['value']) ?>', kondisi: '<?= $pt['label'] ?>', sta: '<?= $staLabelStr ?>', color: '<?= $pt['color'] ?>', subinfo: '<?= e($subInfoStr) ?>' }; activePct = <?= round($subMidPct, 2) ?>; activeChunk = 'asm_pk_<?= $chunkIdx ?>' }"
                                                         @mouseleave="if (window.matchMedia('(hover: hover)').matches) { activeLabel = null; activeChunk = null }"
                                                         @click.stop="activeLabel = (activeLabel && activeLabel.sta === '<?= $staLabelStr ?>' && activeLabel.kondisi === '<?= $pt['label'] ?>') ? null : { panjang: '<?= format_number($pt['value']) ?>', kondisi: '<?= $pt['label'] ?>', sta: '<?= $staLabelStr ?>', color: '<?= $pt['color'] ?>', subinfo: '<?= e($subInfoStr) ?>' }; activePct = <?= round($subMidPct, 2) ?>; activeChunk = 'asm_pk_<?= $chunkIdx ?>'">
                                                        <div class="absolute inset-0 ring-1 ring-white/20 ring-inset opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                                    </div>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>

                                <!-- Tooltip Hover Asumsi Perkerasan -->
                                <div class="relative w-full h-0 z-20">
                                    <template x-if="activeLabel && activeChunk === 'asm_pk_<?= $chunkIdx ?>'">
                                        <div class="absolute top-1.5 flex flex-col items-center -translate-x-1/2 transition-all duration-150 ease-out"
                                             :style="'left:' + activePct + '%'">
                                            <div class="w-px h-3" :style="'background-color:' + activeLabel.color"></div>
                                            <div class="mt-0.5 px-2.5 py-1.5 rounded-lg border shadow-md text-center whitespace-nowrap bg-white"
                                                 :style="'border-color:' + activeLabel.color">
                                                <p class="text-xs font-bold" :style="'color:' + activeLabel.color" x-text="activeLabel.panjang + ' m'"></p>
                                                <p class="text-[10px] font-semibold text-gray-700" x-text="activeLabel.kondisi"></p>
                                                <p class="text-[9px] font-mono text-gray-500" x-text="activeLabel.sta"></p>
                                                <template x-if="activeLabel.subinfo">
                                                    <p class="text-[9px] font-medium text-indigo-700 border-t border-gray-100 pt-0.5 mt-0.5" x-text="activeLabel.subinfo"></p>
                                                </template>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <!-- Skala Penggaris STA Dinamis -->
                            <div class="relative w-full h-6 pt-1.5">
                                <div class="absolute top-1.5 left-0 right-0 h-px bg-gray-300"></div>
                                <template x-for="tick in getTicks(<?= $chunk['start'] ?>, <?= $chunk['end'] ?>)" :key="tick.meter">
                                    <div class="absolute top-1.5 -translate-x-1/2 flex flex-col items-center" :style="'left: ' + tick.pct + '%'">
                                        <div class="w-px h-2.5 bg-gray-400"></div>
                                        <span class="text-[10px] font-mono font-medium text-gray-500 mt-0.5" x-text="meterToSta(tick.meter)"></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

            </div>

        </div>

    </div>
</div>

<script>
(function initVisualCharts() {
    function runChartInit() {
        if (typeof Chart === 'undefined') {
            setTimeout(runChartInit, 100);
            return;
        }

        // 1. Chart Kondisi Jalan 2025
        const ctx1 = document.getElementById('conditionPieChart')?.getContext('2d');
        if (ctx1) {
            const chartColors1 = ['#10b981', '#facc15', '#f97316', '#ef4444'];
            const chartLabels1 = ['Baik', 'Sedang', 'Rusak Ringan', 'Rusak Berat'];
            const chartData1   = [<?= (float)$totalBaik ?>, <?= (float)$totalSedang ?>, <?= (float)$totalRR ?>, <?= (float)$totalRB ?>];

            const filtered1 = chartLabels1.reduce((acc, label, i) => {
                if (chartData1[i] > 0) {
                    acc.labels.push(label);
                    acc.data.push(chartData1[i]);
                    acc.colors.push(chartColors1[i]);
                }
                return acc;
            }, { labels: [], data: [], colors: [] });

            new Chart(ctx1, {
                type: 'pie',
                data: {
                    labels: filtered1.labels,
                    datasets: [{
                        data: filtered1.data,
                        backgroundColor: filtered1.colors,
                        borderWidth: 2.5,
                        borderColor: '#ffffff',
                        hoverOffset: 12
                    }]
                },
                options: {
                    layout: { padding: 25 },
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: { legend: { display: false } }
                }
            });
        }

        // 2. Chart Kemantapan Jalan
        const ctx2 = document.getElementById('stabilityPieChart')?.getContext('2d');
        if (ctx2) {
            const chartColors2 = ['#10b981', '#ef4444'];
            const chartLabels2 = ['Mantap', 'Tidak Mantap'];
            const chartData2   = [<?= (float)$totalMantap ?>, <?= (float)$totalTidakMantap ?>];

            const filtered2 = chartLabels2.reduce((acc, label, i) => {
                if (chartData2[i] > 0) {
                    acc.labels.push(label);
                    acc.data.push(chartData2[i]);
                    acc.colors.push(chartColors2[i]);
                }
                return acc;
            }, { labels: [], data: [], colors: [] });

            new Chart(ctx2, {
                type: 'pie',
                data: {
                    labels: filtered2.labels,
                    datasets: [{
                        data: filtered2.data,
                        backgroundColor: filtered2.colors,
                        borderWidth: 2.5,
                        borderColor: '#ffffff',
                        hoverOffset: 12
                    }]
                },
                options: {
                    layout: { padding: 25 },
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: { legend: { display: false } }
                }
            });
        }

        // 3. Chart Jenis Perkerasan
        const ctx3 = document.getElementById('pavementPieChart')?.getContext('2d');
        if (ctx3) {
            const chartColors3 = ['#475569', '#0f172a', '#7c461b', '#7e22ce'];
            const chartLabels3 = ['Rigid', 'Aspal', 'Kerikil', 'Belum Tembus'];
            const chartData3   = [<?= (float)$totalRigid ?>, <?= (float)$totalAspal ?>, <?= (float)$totalAgregatTanah ?>, <?= (float)$totalBelumTembus ?>];

            const filtered3 = chartLabels3.reduce((acc, label, i) => {
                if (chartData3[i] > 0) {
                    acc.labels.push(label);
                    acc.data.push(chartData3[i]);
                    acc.colors.push(chartColors3[i]);
                }
                return acc;
            }, { labels: [], data: [], colors: [] });

            new Chart(ctx3, {
                type: 'pie',
                data: {
                    labels: filtered3.labels,
                    datasets: [{
                        data: filtered3.data,
                        backgroundColor: filtered3.colors,
                        borderWidth: 2.5,
                        borderColor: '#ffffff',
                        hoverOffset: 12
                    }]
                },
                options: {
                    layout: { padding: 25 },
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: { legend: { display: false } }
                }
            });
        }

        // 4. Chart Rencana Target Kondisi Penanganan
        const ctx4 = document.getElementById('pnConditionPieChart')?.getContext('2d');
        if (ctx4) {
            const chartColors4 = ['#8b5cf6', '#facc15', '#f97316', '#ef4444'];
            const chartLabels4 = ['Target Baik', 'Target Sedang', 'Target Rusak Ringan', 'Target Rusak Berat'];
            const chartData4   = [<?= (float)$totalPnTargetBaik ?>, <?= (float)$totalPnTargetSedang ?>, <?= (float)$totalPnTargetRR ?>, <?= (float)$totalPnTargetRB ?>];

            const filtered4 = chartLabels4.reduce((acc, label, i) => {
                if (chartData4[i] > 0) {
                    acc.labels.push(label);
                    acc.data.push(chartData4[i]);
                    acc.colors.push(chartColors4[i]);
                }
                return acc;
            }, { labels: [], data: [], colors: [] });

            new Chart(ctx4, {
                type: 'pie',
                data: {
                    labels: filtered4.labels,
                    datasets: [{
                        data: filtered4.data,
                        backgroundColor: filtered4.colors,
                        borderWidth: 2.5,
                        borderColor: '#ffffff',
                        hoverOffset: 12
                    }]
                },
                options: {
                    layout: { padding: 25 },
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: { legend: { display: false } }
                }
            });
        }

        // 5. Chart Rencana Jenis Perkerasan Baru
        const ctx5 = document.getElementById('pnPavementPieChart')?.getContext('2d');
        if (ctx5) {
            const chartColors5 = ['#475569', '#0f172a', '#7c461b', '#7e22ce'];
            const chartLabels5 = ['Rigid / Beton', 'Aspal', 'Agregat / LPB', 'Belum Tembus'];
            const chartData5   = [<?= (float)$totalPnRigid ?>, <?= (float)$totalPnAspal ?>, <?= (float)$totalPnAgregat ?>, <?= (float)$totalPnBelumTembus ?>];

            const filtered5 = chartLabels5.reduce((acc, label, i) => {
                if (chartData5[i] > 0) {
                    acc.labels.push(label);
                    acc.data.push(chartData5[i]);
                    acc.colors.push(chartColors5[i]);
                }
                return acc;
            }, { labels: [], data: [], colors: [] });

            new Chart(ctx5, {
                type: 'pie',
                data: {
                    labels: filtered5.labels,
                    datasets: [{
                        data: filtered5.data,
                        backgroundColor: filtered5.colors,
                        borderWidth: 2.5,
                        borderColor: '#ffffff',
                        hoverOffset: 12
                    }]
                },
                options: {
                    layout: { padding: 25 },
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: { legend: { display: false } }
                }
            });
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', runChartInit);
    } else {
        setTimeout(runChartInit, 0);
    }
})();
</script>
<?php endif; ?>
