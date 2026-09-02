<?php
/**
 * View: Perbandingan Kondisi Jalan Antar Tahun (Stripmap Berlapis)
 * Route: /stripmap/compare/{ruas_id}
 *
 * Menampilkan visual garis warna kondisi (Baseline vs Prediksi per tahun)
 * dan chart kemantapan untuk satu ruas jalan.
 */

$fkm  = fn($v) => number_format((float)$v / 1000, 2, ',', '.');
$fsta = fn($m) => meter_to_sta($m);

$kondisiColors = $kondisiColors ?? [
    'baik'         => '#22c55e',
    'sedang'       => '#eab308',
    'rusak_ringan' => '#f97316',
    'rusak_berat'  => '#ef4444',
];
$kondisiLabels = $kondisiLabels ?? [
    'baik'         => 'Baik',
    'sedang'       => 'Sedang',
    'rusak_ringan' => 'Rusak Ringan',
    'rusak_berat'  => 'Rusak Berat',
];
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0/dist/chartjs-plugin-datalabels.min.js"></script>

<div x-data="compareStripmap()" x-init="init()" class="space-y-6">

    <!-- ============ HEADER ============ -->
    <div class="bg-white p-5 rounded-2xl border border-gray-200/80 shadow-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="<?= base_url() ?>" class="text-xs font-semibold text-gray-500 hover:text-blue-600">Dashboard</a>
                <span class="text-xs text-gray-400">/</span>
                <a href="<?= base_url('stripmap/' . $ruas['id']) ?>" class="text-xs font-semibold text-gray-500 hover:text-blue-600">Strip Map</a>
                <span class="text-xs text-gray-400">/</span>
                <span class="text-xs font-semibold text-blue-600">Perbandingan Tahun</span>
            </div>
            <h1 class="text-xl font-black text-gray-900 flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                <?= htmlspecialchars($ruas['nama_ruas']) ?>
                <span class="text-sm font-semibold text-gray-400">— Kode <?= htmlspecialchars($ruas['kode_ruas']) ?></span>
            </h1>
            <p class="text-xs text-gray-500 mt-0.5">Perbandingan garis warna kondisi jalan eksisting (Tahun <?= TahunHelper::awal() ?> sebagai baseline) terhadap hasil simulasi prediksi penanganan tahun-tahun berikutnya.</p>
        </div>
        <!-- Ganti ruas dropdown -->
        <div class="flex items-center gap-2 shrink-0">
            <label class="text-xs font-semibold text-gray-600 whitespace-nowrap">Pilih Ruas:</label>
            <select onchange="window.location.href='<?= base_url('stripmap/compare/') ?>' + this.value"
                    class="text-sm font-semibold border border-gray-300 rounded-xl px-3 py-2 bg-white text-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 max-w-xs shadow-xs cursor-pointer">
                <?php foreach ($ruasList as $r): ?>
                    <option value="<?= $r['id'] ?>" <?= $r['id'] == $ruas['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($r['kode_ruas'] . ' — ' . $r['nama_ruas']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <!-- ============ KPI + CHART ============ -->
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-4">
        <!-- KPI Cards per tahun -->
        <div class="lg:col-span-2 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-2 gap-3 content-start">
            <?php foreach ($yearlyData as $key => $d): ?>
            <?php
            $isBase  = $d['tipe'] === 'baseline';
            $delta   = $d['delta_pct'];
            $bgClass = $isBase
                ? 'bg-gradient-to-br from-blue-50 to-sky-50 border-blue-200'
                : ($delta > 0 ? 'bg-gradient-to-br from-emerald-50 to-green-50 border-emerald-200' : ($delta == 0 ? 'bg-gray-50 border-gray-200' : 'bg-rose-50 border-rose-200'));
            $textClass = $isBase ? 'text-blue-900' : ($delta > 0 ? 'text-emerald-700' : ($delta == 0 ? 'text-gray-600' : 'text-rose-700'));
            ?>
            <div class="p-3.5 rounded-xl border <?= $bgClass ?> relative overflow-hidden">
                <div class="flex items-center justify-between gap-1 mb-1">
                    <div class="flex items-center gap-1.5">
                        <div class="w-2.5 h-2.5 rounded-full shrink-0" style="background: <?= $d['warna_label'] ?>"></div>
                        <span class="text-[11px] font-bold text-gray-800 uppercase tracking-wider">Tahun <?= $d['tahun'] ?></span>
                    </div>
                    <?php if ($isBase): ?>
                        <span class="text-[9px] font-extrabold px-1.5 py-0.5 rounded bg-blue-100 text-blue-800 border border-blue-200 uppercase">Baseline</span>
                    <?php else: ?>
                        <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-purple-100 text-purple-700 uppercase">Prediksi</span>
                    <?php endif; ?>
                </div>
                <div class="flex items-baseline gap-0.5">
                    <span class="text-2xl font-black <?= $textClass ?>"><?= $d['pct_mantap'] ?></span>
                    <span class="text-sm font-semibold text-gray-400">%</span>
                </div>
                <div class="text-[11px] <?= $textClass ?> font-semibold mt-0.5">
                    <?php if ($isBase): ?>
                        <span>Kondisi Awal (Baseline)</span>
                    <?php elseif ($delta > 0): ?>
                        <span>↑ +<?= $delta ?> poin naik</span>
                    <?php elseif ($delta < 0): ?>
                        <span>↓ <?= $delta ?> poin turun</span>
                    <?php else: ?>
                        <span>→ Belum ada perubahan</span>
                    <?php endif; ?>
                </div>
                <?php if (!$isBase && !empty($d['penanganan'])): ?>
                <div class="text-[10px] text-gray-500 mt-0.5 font-medium">
                    <?= count(array_filter($d['penanganan'], fn($p) => (int)$p['tahun'] == (int)$d['tahun'])) ?> paket penanganan
                </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Chart kemantapan per tahun -->
        <div class="lg:col-span-3 bg-white p-5 rounded-2xl border border-gray-200/80 shadow-sm flex flex-col">
            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Tren Kemantapan per Tahun</p>
            <div class="relative flex-1" style="min-height: 200px">
                <canvas id="chartKemantapanTahunan"></canvas>
            </div>
        </div>
    </div>

    <!-- ============ VISUAL STRIPMAP BERLAPIS ============ -->
    <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="text-sm font-black text-gray-800">Visualisasi Garis Warna Kondisi — Antar Tahun</h2>
                <p class="text-xs text-gray-500 mt-0.5">Setiap baris merepresentasikan kondisi jalan per segmen STA untuk satu tahun. Baca dari atas (baseline) ke bawah (prediksi).</p>
            </div>
            <!-- Legenda kondisi -->
            <div class="flex flex-wrap items-center gap-3 shrink-0">
                <?php foreach ($kondisiColors as $k => $c): ?>
                <div class="flex items-center gap-1.5">
                    <div class="w-4 h-3 rounded-sm shrink-0" style="background: <?= $c ?>"></div>
                    <span class="text-[11px] font-semibold text-gray-600"><?= $kondisiLabels[$k] ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="p-4 overflow-x-auto">
            <!-- Container stripmap -->
            <div class="min-w-[600px]">
                <!-- Baris stripmap per tahun -->
                <div class="space-y-1.5">
                    <?php foreach ($yearlyData as $key => $d): ?>
                    <?php $isBase = $d['tipe'] === 'baseline'; ?>
                    <div class="flex items-center gap-2 group">
                        <!-- Label tahun di kiri -->
                        <div class="w-32 shrink-0 text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                <div class="w-2.5 h-2.5 rounded-full" style="background: <?= $d['warna_label'] ?>"></div>
                                <span class="text-xs font-black <?= $isBase ? 'text-blue-950' : 'text-gray-800' ?> uppercase tracking-wider">
                                    Tahun <?= $d['tahun'] ?>
                                </span>
                            </div>
                            <div class="text-[10px] text-gray-400 font-semibold text-right mt-0.5 pr-3.5">
                                <?= $isBase ? 'Kondisi Awal' : 'Prediksi' ?> • <?= $d['pct_mantap'] ?>%
                            </div>
                        </div>

                        <!-- Strip bar visual -->
                        <div class="flex-1 relative">
                            <!-- Track bg -->
                            <div class="relative h-8 bg-gray-100 rounded-lg overflow-hidden">
                                <!-- Runs kondisi -->
                                <?php foreach ($d['runs'] as $run): ?>
                                <div class="absolute top-0 h-full transition-all duration-300"
                                     style="left: <?= $run['pct_from'] ?>%; width: <?= $run['pct_width'] ?>%; background: <?= $kondisiColors[$run['kondisi']] ?? '#94a3b8' ?>;"
                                     title="<?= $kondisiLabels[$run['kondisi']] ?> | <?= $fsta($run['sta_awal']) ?> – <?= $fsta($run['sta_akhir']) ?> | <?= round(($run['sta_akhir'] - $run['sta_awal']) / 1000, 2) ?> km">
                                </div>
                                <?php endforeach; ?>
                                <!-- Overlay penanganan: tanda kotak putih semi-transparan -->
                                <?php if (!$isBase): ?>
                                    <?php foreach ($d['penanganan'] as $p): ?>
                                    <?php if ((int)$p['tahun'] === (int)$d['tahun']): // hanya tandai paket tahun ini ?>
                                    <div class="absolute top-0 h-full border-2 border-white/80 rounded-sm pointer-events-none opacity-0 group-hover:opacity-100 transition-opacity"
                                         style="left: <?= $p['pct_from'] ?>%; width: max(<?= $p['pct_width'] ?>%, 2px);"
                                         title="<?= htmlspecialchars($p['jenis_penanganan']) ?><?= !empty($p['nama_paket']) ? ' — ' . htmlspecialchars($p['nama_paket']) : '' ?> | <?= $fsta($p['sta_awal']) ?> – <?= $fsta($p['sta_akhir']) ?>">
                                    </div>
                                    <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Pct mantap di kanan -->
                        <div class="w-16 shrink-0 text-left">
                            <?php if (!$isBase): ?>
                            <?php $delta = $d['delta_pct']; ?>
                            <span class="text-[11px] font-bold <?= $delta > 0 ? 'text-emerald-600' : ($delta < 0 ? 'text-rose-600' : 'text-gray-400') ?>">
                                <?= $delta >= 0 ? '+' : '' ?><?= $delta ?>%
                            </span>
                            <?php else: ?>
                            <span class="text-[11px] font-semibold text-gray-400">—</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Sumbu X: label STA -->
                <div class="relative mt-3 h-5 ml-[136px] mr-[72px]">
                    <?php foreach ($ticks as $tick): ?>
                    <div class="absolute text-[10px] text-gray-400 font-mono font-semibold transform -translate-x-1/2"
                         style="left: <?= $tick['pct'] ?>%">
                        <?= $tick['label'] ?>
                    </div>
                    <?php endforeach; ?>
                </div>
                <!-- Garis penanda STA -->
                <div class="relative h-1 ml-[136px] mr-[72px] mb-1">
                    <?php foreach ($ticks as $tick): ?>
                    <div class="absolute w-px h-full bg-gray-300"
                         style="left: <?= $tick['pct'] ?>%"></div>
                    <?php endforeach; ?>
                </div>

                <div class="text-center text-[10px] text-gray-400 mt-1 font-semibold">
                    Sumbu Horizontal: Jarak dari <?= $fsta($staBase) ?> hingga <?= $fsta($staEnd) ?>
                    (Total <?= $fkm($rentangM * 1000) ?> km)
                </div>
            </div>
        </div>
    </div>

    <!-- ============ TABEL PERBANDINGAN ============ -->
    <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-gray-100">
            <h2 class="text-sm font-black text-gray-800">Tabel Ringkasan Perbandingan Kondisi</h2>
            <p class="text-xs text-gray-500 mt-0.5">Distribusi kondisi jalan (km) dari data awal (Tahun <?= TahunHelper::awal() ?>) hingga prediksi hasil penanganan tiap tahun.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left font-bold text-gray-600 uppercase tracking-wider">Tahun</th>
                        <th class="px-4 py-3 text-center font-bold text-emerald-700 bg-green-50/60">Baik (km)</th>
                        <th class="px-4 py-3 text-center font-bold text-yellow-700 bg-yellow-50/60">Sedang (km)</th>
                        <th class="px-4 py-3 text-center font-bold text-orange-700 bg-orange-50/60">Rusak Ringan (km)</th>
                        <th class="px-4 py-3 text-center font-bold text-red-700 bg-red-50/60">Rusak Berat (km)</th>
                        <th class="px-4 py-3 text-center font-bold text-indigo-700 bg-indigo-50/60">Mantap (%)</th>
                        <th class="px-4 py-3 text-center font-bold text-gray-600">Delta vs Baseline</th>
                        <th class="px-4 py-3 text-center font-bold text-gray-600">Paket (tahun ini)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($yearlyData as $key => $d): ?>
                    <?php
                    // Hitung km per kondisi dari runs
                    $kmKondisi = ['baik' => 0.0, 'sedang' => 0.0, 'rusak_ringan' => 0.0, 'rusak_berat' => 0.0];
                    foreach ($d['runs'] as $run) {
                        $len = $run['sta_akhir'] - $run['sta_awal'];
                        $kmKondisi[$run['kondisi']] = ($kmKondisi[$run['kondisi']] ?? 0.0) + $len;
                    }
                    $isBase   = $d['tipe'] === 'baseline';
                    $delta    = $d['delta_pct'];
                    $pakBaru  = $isBase ? 0 : count(array_filter($d['penanganan'] ?? [], fn($p) => (int)$p['tahun'] == (int)$d['tahun']));
                    ?>
                    <tr class="hover:bg-gray-50/80 <?= $isBase ? 'bg-blue-50/40 font-semibold' : '' ?>">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <div class="w-2.5 h-2.5 rounded-full shrink-0" style="background: <?= $d['warna_label'] ?>"></div>
                                <span class="font-bold text-gray-900">Tahun <?= $d['tahun'] ?></span>
                                <?php if ($isBase): ?>
                                <span class="text-[10px] px-2 py-0.5 bg-blue-100 text-blue-800 border border-blue-200 rounded-full font-bold">Kondisi Awal (Baseline)</span>
                                <?php else: ?>
                                <span class="text-[10px] px-2 py-0.5 bg-purple-100 text-purple-700 border border-purple-200 rounded-full font-bold">Prediksi Penanganan</span>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-center font-semibold text-emerald-700 bg-green-50/30">
                            <?= number_format($kmKondisi['baik'] / 1000, 2, ',', '.') ?>
                        </td>
                        <td class="px-4 py-3 text-center font-semibold text-yellow-700 bg-yellow-50/30">
                            <?= number_format($kmKondisi['sedang'] / 1000, 2, ',', '.') ?>
                        </td>
                        <td class="px-4 py-3 text-center font-semibold text-orange-700 bg-orange-50/30">
                            <?= number_format($kmKondisi['rusak_ringan'] / 1000, 2, ',', '.') ?>
                        </td>
                        <td class="px-4 py-3 text-center font-semibold text-red-700 bg-red-50/30">
                            <?= number_format($kmKondisi['rusak_berat'] / 1000, 2, ',', '.') ?>
                        </td>
                        <td class="px-4 py-3 text-center bg-indigo-50/30">
                            <span class="font-black text-indigo-800 text-sm"><?= $d['pct_mantap'] ?>%</span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <?php if ($isBase): ?>
                            <span class="text-gray-400 font-semibold">—</span>
                            <?php elseif ($delta > 0): ?>
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                                ↑ +<?= $delta ?>%
                            </span>
                            <?php elseif ($delta < 0): ?>
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-800">
                                ↓ <?= $delta ?>%
                            </span>
                            <?php else: ?>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium text-gray-500 bg-gray-100">
                                0.0%
                            </span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <?php if ($pakBaru > 0): ?>
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-800">
                                <?= $pakBaru ?> paket
                            </span>
                            <?php else: ?>
                            <span class="text-gray-300 font-semibold">—</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('compareStripmap', () => ({
        init() {
            this.$nextTick(() => this.renderChart());
        },
        renderChart() {
            if (typeof Chart === 'undefined') {
                setTimeout(() => this.renderChart(), 150);
                return;
            }
            if (typeof ChartDataLabels !== 'undefined') {
                Chart.register(ChartDataLabels);
            }

            const ctx = document.getElementById('chartKemantapanTahunan');
            if (!ctx) return;

            const yearlyData = <?= json_encode(array_values(array_map(fn($d) => [
                'label'       => $d['label'],
                'tahun'       => $d['tahun'],
                'pct_mantap'  => $d['pct_mantap'],
                'delta_pct'   => $d['delta_pct'],
                'tipe'        => $d['tipe'],
                'warna_label' => $d['warna_label'],
            ], $yearlyData))) ?>;

            const labels = yearlyData.map(d => d.tipe === 'baseline' ? `Tahun ${d.label} (Base)` : `Prediksi ${d.label}`);
            const values = yearlyData.map(d => d.pct_mantap);
            const bgColors = yearlyData.map(d => d.warna_label + 'cc');
            const borderColors = yearlyData.map(d => d.warna_label);

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels,
                    datasets: [{
                        label: 'Kemantapan (%)',
                        data: values,
                        backgroundColor: bgColors,
                        borderColor: borderColors,
                        borderWidth: 2,
                        borderRadius: 8,
                        borderSkipped: false,
                        barPercentage: 0.65,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    layout: { padding: { top: 26 } },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: (ctx) => {
                                    const d = yearlyData[ctx.dataIndex];
                                    const delta = d.delta_pct;
                                    const sign  = delta >= 0 ? '+' : '';
                                    return [
                                        `  Kemantapan: ${ctx.parsed.y.toFixed(1)}%`,
                                        d.tipe === 'baseline' ? '  Kondisi Awal (Baseline)' : `  Delta vs Base: ${sign}${delta} poin`
                                    ];
                                }
                            }
                        },
                        datalabels: {
                            anchor: 'end',
                            align: 'end',
                            offset: 2,
                            formatter: (v) => v.toFixed(1) + '%',
                            font: { size: 10, weight: '700' },
                            color: (ctx) => yearlyData[ctx.dataIndex].warna_label,
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { font: { size: 10, weight: '700' }, maxRotation: 25 }
                        },
                        y: {
                            min: 0,
                            max: 100,
                            grid: { color: 'rgba(0,0,0,0.04)' },
                            ticks: { callback: v => v + '%', font: { size: 10 } },
                            title: { display: true, text: 'Kemantapan (%)', font: { size: 10 }, color: '#9ca3af' }
                        }
                    }
                }
            });
        }
    }));
});
</script>
