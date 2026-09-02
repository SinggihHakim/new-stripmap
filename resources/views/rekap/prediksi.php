<?php
/**
 * View: Prediksi Kondisi Jalan Setelah Penanganan
 * Menampilkan perbandingan kondisi jalan SEBELUM vs PREDIKSI SESUDAH penanganan
 * berdasarkan matriks logika Ide Strip Map.
 */

// Helper: format km
$fkm = fn($v) => number_format((float)$v, 2, ',', '.');

$mantapDelta = round($totalSesudah['pct_mantap'] - $totalSebelum['pct_mantap'], 1);
$deltaPositif = $mantapDelta >= 0;

$mantapDeltaKm = round($totalSesudah['mantap_km'] - $totalSebelum['mantap_km'], 2);
$deltaKmPositif = $mantapDeltaKm >= 0;

$tidakMantapDeltaPct = round($totalSesudah['pct_tidak_mantap'] - $totalSebelum['pct_tidak_mantap'], 1);
$tidakMantapDeltaKm  = round($totalSesudah['tidak_mantap_km'] - $totalSebelum['tidak_mantap_km'], 2);

?>

<!-- Load Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0/dist/chartjs-plugin-datalabels.min.js"></script>

<script>
window.changePrediksiParam = function(key, val) {
    var searchParams = new window.URLSearchParams(window.location.search);
    searchParams.delete('tahun'); // Hapus legacy parameter agar tidak tumpang tindih
    searchParams.set(key, val);
    window.location.search = searchParams.toString();
};

document.addEventListener('alpine:init', () => {
    Alpine.data('prediksiChart', () => ({
        chartRendered: false,
        initChart() {
            if (this.chartRendered) return;
            if (typeof Chart === 'undefined' || typeof ChartDataLabels === 'undefined') {
                setTimeout(() => this.initChart(), 100);
                return;
            }
            // Register plugin datalabels secara eksplisit
            Chart.register(ChartDataLabels);
            this.chartRendered = true;
            this.$nextTick(() => {
                this.renderDistribusiBar();
                this.renderKemantapanBar();
                this.renderMultiTahunBar();
            });
        },
        renderMultiTahunBar() {
            const ctx = document.getElementById('chartMultiTahun');
            if (!ctx) return;
            // Data disiapkan dari PHP (mode semua)
            const allYears   = <?= json_encode(array_keys($allYearsData ?? [])) ?>;
            const pctSebelum = <?= json_encode(array_values(array_map(fn($v) => $v['pct_sebelum'], $allYearsData ?? []))) ?>;
            const pctSesudah = <?= json_encode(array_values(array_map(fn($v) => $v['pct_sesudah'], $allYearsData ?? []))) ?>;
            if (!allYears.length) return;
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: allYears.map(y => String(y)),
                    datasets: [
                        {
                            label: 'Saat Ini / Target (n-1)',
                            data: pctSebelum,
                            backgroundColor: 'rgba(99,102,241,0.82)',
                            borderColor: '#4f46e5',
                            borderWidth: 2,
                            borderRadius: 7,
                            borderSkipped: false,
                        },
                        {
                            label: 'Prediksi Setelah Penanganan (n)',
                            data: pctSesudah,
                            backgroundColor: 'rgba(139,92,246,0.38)',
                            borderColor: '#7c3aed',
                            borderWidth: 2,
                            borderRadius: 7,
                            borderSkipped: false,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    layout: { padding: { top: 22 } },
                    plugins: {
                        legend: {
                            position: 'top',
                            align: 'end',
                            labels: { font: { size: 11, weight: '600' }, padding: 16, boxWidth: 14, boxHeight: 14, usePointStyle: true, pointStyle: 'rectRounded' }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(ctx) { return '  ' + ctx.dataset.label + ': ' + ctx.parsed.y.toFixed(1) + '%'; }
                            }
                        },
                        datalabels: {
                            anchor: 'end',
                            align: 'end',
                            offset: 2,
                            formatter: function(value) { return value.toFixed(1) + '%'; },
                            font: { size: 10, weight: '700' },
                            color: function(ctx) { return ctx.datasetIndex === 0 ? '#4f46e5' : '#7c3aed'; },
                            clip: false
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { font: { size: 12, weight: '700' } }
                        },
                        y: {
                            min: 0,
                            max: 100,
                            grid: { color: 'rgba(0,0,0,0.05)' },
                            ticks: { font: { size: 11 }, callback: function(v) { return v + '%'; } },
                            title: { display: true, text: 'Kemantapan (%)', font: { size: 11 }, color: '#6b7280' }
                        }
                    }
                }
            });
        },
        renderDistribusiBar() {
            const ctx = document.getElementById('chartDistribusiBar');
            if (!ctx) return;
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Baik', 'Sedang', 'Rusak Ringan', 'Rusak Berat'],
                    datasets: [
                        {
                            label: 'Saat Ini (n-1)',
                            data: [<?= $totalSebelum['baik_km'] ?>, <?= $totalSebelum['sedang_km'] ?>, <?= $totalSebelum['rusak_ringan_km'] ?>, <?= $totalSebelum['rusak_berat_km'] ?>],
                            backgroundColor: ['rgba(34,197,94,0.85)', 'rgba(234,179,8,0.85)', 'rgba(249,115,22,0.85)', 'rgba(239,68,68,0.85)'],
                            borderColor:     ['#16a34a', '#ca8a04', '#ea580c', '#dc2626'],
                            borderWidth: 1.5,
                            borderRadius: 5,
                            borderSkipped: false,
                        },
                        {
                            label: 'Prediksi (n)',
                            data: [<?= $totalSesudah['baik_km'] ?>, <?= $totalSesudah['sedang_km'] ?>, <?= $totalSesudah['rusak_ringan_km'] ?>, <?= $totalSesudah['rusak_berat_km'] ?>],
                            backgroundColor: ['rgba(34,197,94,0.28)', 'rgba(234,179,8,0.28)', 'rgba(249,115,22,0.28)', 'rgba(239,68,68,0.28)'],
                            borderColor:     ['#16a34a', '#ca8a04', '#ea580c', '#dc2626'],
                            borderWidth: 2,
                            borderRadius: 5,
                            borderSkipped: false,
                            borderDash: [4, 3],
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            align: 'end',
                            labels: { font: { size: 11, weight: '600' }, padding: 14, boxWidth: 14, boxHeight: 14, usePointStyle: true, pointStyle: 'rectRounded' }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(ctx) { return '  ' + ctx.dataset.label + ': ' + ctx.parsed.y.toFixed(2) + ' km'; }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { font: { size: 11, weight: '600' } }
                        },
                        y: {
                            grid: { color: 'rgba(0,0,0,0.05)' },
                            ticks: { font: { size: 10 }, callback: function(v) { return v.toFixed(0) + ' km'; } },
                            title: { display: true, text: 'Panjang (km)', font: { size: 11 }, color: '#6b7280' }
                        }
                    }
                }
            });
        },
        renderKemantapanBar() {
            const ctx = document.getElementById('chartKemantapanBar');
            if (!ctx) return;
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Saat Ini (n-1)', 'Prediksi (n) (<?= $tahunSummaryRaw === 'semua' ? 'Semua Tahun' : 'Tahun ' . (int)$tahunSummaryRaw ?>)'],
                    datasets: [
                        {
                            label: 'Kemantapan (%)',
                            data: [<?= $totalSebelum['pct_mantap'] ?>, <?= $totalSesudah['pct_mantap'] ?>],
                            backgroundColor: [
                                'rgba(99,102,241,0.80)',
                                'rgba(139,92,246,0.45)'
                            ],
                            borderColor: ['#4f46e5', '#7c3aed'],
                            borderWidth: 2,
                            borderRadius: 8,
                            borderSkipped: false,
                            barPercentage: 0.45,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(ctx) { return '  Kemantapan: ' + ctx.parsed.y.toFixed(1) + '%'; }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { font: { size: 11, weight: '700' } }
                        },
                        y: {
                            min: 0,
                            max: 100,
                            grid: { color: 'rgba(0,0,0,0.05)' },
                            ticks: { font: { size: 10 }, callback: function(v) { return v + '%'; } },
                            title: { display: true, text: 'Kemantapan (%)', font: { size: 11 }, color: '#6b7280' }
                        }
                    }
                }
            });
        }
    }));
});
</script>

<div x-data="prediksiChart()" x-init="initChart()" class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-2xl border border-gray-200/80 shadow-sm">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="<?= base_url() ?>" class="text-xs font-semibold text-gray-500 hover:text-blue-600 transition-colors">Dashboard</a>
                <span class="text-xs text-gray-400">/</span>
                <span class="text-xs font-semibold text-blue-600">Prediksi Kondisi Jalan</span>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-3">
                <span>Prediksi Kondisi Jalan Setelah Penanganan</span>
                <span class="text-xs px-2.5 py-1 rounded-full bg-purple-100 text-purple-700 font-semibold border border-purple-200"><?= $tahunSummaryRaw === 'semua' ? 'Semua Tahun (' . TahunHelper::awal() . '–' . TahunHelper::akhir() . ')' : 'Tahun ' . $tahunSummaryRaw ?></span>
            </h1>
            <p class="text-xs text-gray-500 mt-1">Prediksi kondisi jalan berdasarkan matriks penanganan (Ide Strip Map). Warna solid = kondisi saat ini (n-1), transparan = prediksi setelah penanganan (n).</p>
        </div>

        <!-- Filter Tahun Penanganan (Grid Atas) -->
        <div class="flex items-center gap-2">
            <label class="text-xs font-semibold text-gray-600">Tahun Ringkasan:</label>
            <select onchange="window.changePrediksiParam('tahun_summary', this.value)"
                class="text-sm font-semibold border border-gray-300 rounded-xl px-3 py-2 bg-white text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-xs cursor-pointer">
                <option value="semua" <?= $tahunSummaryRaw === 'semua' ? 'selected' : '' ?>>🗓 Semua Tahun</option>
                <?php foreach (TahunHelper::getList() as $y): ?>
                    <option value="<?= $y ?>" <?= $y == $tahunSummaryRaw ? 'selected' : '' ?>><?= $y ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <?php if ($modeSemua): ?>
    <!-- ===== SECTION: Chart Multi-Tahun (Mode Semua) ===== -->
    <div class="bg-white p-6 rounded-2xl border border-indigo-200 shadow-sm">
        <div class="flex items-start justify-between mb-5">
            <div>
                <h3 class="text-sm font-bold text-gray-800">Kemantapan Jaringan Jalan — Semua Tahun Penanganan</h3>
                <p class="text-xs text-gray-500 mt-0.5">Perbandingan kemantapan <strong>saat ini / target (n-1)</strong> vs <strong>prediksi setelah penanganan (n)</strong> untuk setiap tahun (<?= TahunHelper::awal() ?>–<?= TahunHelper::akhir() ?>).</p>
            </div>
            <span class="shrink-0 text-xs px-2.5 py-1 rounded-full bg-indigo-100 text-indigo-700 font-semibold border border-indigo-200"><?= TahunHelper::awal() ?> – <?= TahunHelper::akhir() ?></span>
        </div>
        <div class="relative" style="height: 320px">
            <canvas id="chartMultiTahun"></canvas>
        </div>
        <!-- Mini tabel nilai per tahun -->
        <div class="mt-5 overflow-x-auto">
            <table class="w-full text-xs">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="px-3 py-2 text-left font-bold text-gray-600">Tahun</th>
                        <th class="px-3 py-2 text-center font-bold text-indigo-700 bg-indigo-50">Saat Ini / Target (%)</th>
                        <th class="px-3 py-2 text-center font-bold text-purple-700 bg-purple-50">Prediksi Setelah Penanganan (%)</th>
                        <th class="px-3 py-2 text-center font-bold text-gray-600">Delta (%)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($allYearsData as $thn => $d): ?>
                    <?php $delta = round($d['pct_sesudah'] - $d['pct_sebelum'], 1); ?>
                    <tr class="hover:bg-gray-50/80">
                        <td class="px-3 py-2.5 font-black text-gray-800"><?= $thn ?></td>
                        <td class="px-3 py-2.5 text-center bg-indigo-50/50">
                            <span class="font-bold text-indigo-700"><?= $d['pct_sebelum'] ?>%</span>
                        </td>
                        <td class="px-3 py-2.5 text-center bg-purple-50/50">
                            <span class="font-bold text-purple-700"><?= $d['pct_sesudah'] ?>%</span>
                        </td>
                        <td class="px-3 py-2.5 text-center">
                            <span class="font-bold <?= $delta > 0 ? 'text-green-600' : ($delta < 0 ? 'text-red-600' : 'text-gray-400') ?>">
                                <?= $delta >= 0 ? '+' : '' ?><?= $delta ?> poin
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

    <!-- KPI Summary Cards -->
    <?php
    $mantapSebelumKm = $totalSebelum['baik_km'] + $totalSebelum['sedang_km'];
    $mantapSesudahKm = $totalSesudah['baik_km'] + $totalSesudah['sedang_km'];
    $deltaKm = round($mantapSesudahKm - $mantapSebelumKm, 3);
    $deltaM  = round($deltaKm * 1000);
    ?>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <!-- Bar Chart: Kemantapan Saat Ini vs Prediksi -->
        <div class="bg-white p-5 rounded-2xl border border-gray-200/80 shadow-sm flex flex-col">
            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Kemantapan: Saat Ini vs Prediksi</p>
            <div class="relative flex-1" style="min-height:160px">
                <canvas id="chartKemantapanBar"></canvas>
            </div>
        </div>
        <!-- KPI: Kemantapan Saat Ini -->
        <div class="bg-white p-5 rounded-2xl border border-gray-200/80 shadow-sm">
            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Kemantapan Saat Ini</p>
            <div class="flex items-baseline gap-1.5">
                <span class="text-3xl font-black text-gray-900"><?= $totalSebelum['pct_mantap'] ?></span>
                <span class="text-sm font-semibold text-gray-500">%</span>
            </div>
            <p class="text-xs text-gray-500 mt-1"><?= $fkm($mantapSebelumKm) ?> km mantap dari <?= $fkm($totalPanjangKm) ?> km</p>
            <div class="mt-3 space-y-1">
                <div class="flex items-center justify-between text-xs">
                    <span class="text-green-600 font-semibold">Baik</span>
                    <span class="font-mono text-gray-700"><?= $fkm($totalSebelum['baik_km']) ?> km</span>
                </div>
                <div class="flex items-center justify-between text-xs">
                    <span class="text-yellow-600 font-semibold">Sedang</span>
                    <span class="font-mono text-gray-700"><?= $fkm($totalSebelum['sedang_km']) ?> km</span>
                </div>
                <div class="flex items-center justify-between text-xs">
                    <span class="text-orange-500 font-semibold">Rusak Ringan</span>
                    <span class="font-mono text-gray-700"><?= $fkm($totalSebelum['rusak_ringan_km']) ?> km</span>
                </div>
                <div class="flex items-center justify-between text-xs">
                    <span class="text-red-600 font-semibold">Rusak Berat</span>
                    <span class="font-mono text-gray-700"><?= $fkm($totalSebelum['rusak_berat_km']) ?> km</span>
                </div>
            </div>
        </div>
        <!-- KPI: Prediksi Kemantapan -->
        <div class="bg-gradient-to-br from-purple-600 to-indigo-600 p-5 rounded-2xl shadow-lg shadow-purple-500/25">
            <p class="text-xs font-bold text-white/70 uppercase tracking-wider mb-2">Prediksi Kemantapan</p>
            <div class="flex items-baseline gap-1.5">
                <span class="text-3xl font-black text-white"><?= $totalSesudah['pct_mantap'] ?></span>
                <span class="text-sm font-semibold text-white/80">%</span>
            </div>
            <div class="flex flex-col gap-0.5 mt-1 mb-3">
                <span class="text-xs font-bold text-white/90">
                    <?= $deltaPositif ? '↑' : ($mantapDelta == 0 ? '→' : '↓') ?>
                    <?= abs($mantapDelta) ?> poin <?= $deltaPositif ? 'naik' : ($mantapDelta == 0 ? '(belum berubah)' : 'turun') ?>
                </span>
                <span class="text-[11px] font-semibold text-white/70">
                    Delta: <?= $deltaKm >= 0 ? '+' : '' ?><?= number_format($deltaKm, 3, ',', '.') ?> km
                    (<?= $deltaM >= 0 ? '+' : '' ?><?= number_format($deltaM, 0, ',', '.') ?> m)
                </span>
            </div>
            <div class="mt-2 space-y-1">
                <div class="flex items-center justify-between text-xs">
                    <span class="text-green-300 font-semibold">Baik</span>
                    <span class="font-mono text-white/80"><?= $fkm($totalSesudah['baik_km']) ?> km</span>
                </div>
                <div class="flex items-center justify-between text-xs">
                    <span class="text-yellow-300 font-semibold">Sedang</span>
                    <span class="font-mono text-white/80"><?= $fkm($totalSesudah['sedang_km']) ?> km</span>
                </div>
                <div class="flex items-center justify-between text-xs">
                    <span class="text-orange-300 font-semibold">Rusak Ringan</span>
                    <span class="font-mono text-white/80"><?= $fkm($totalSesudah['rusak_ringan_km']) ?> km</span>
                </div>
                <div class="flex items-center justify-between text-xs">
                    <span class="text-red-300 font-semibold">Rusak Berat</span>
                    <span class="font-mono text-white/80"><?= $fkm($totalSesudah['rusak_berat_km']) ?> km</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Bar Chart Distribusi Kondisi: Saat Ini vs Prediksi -->
    <div class="bg-white p-6 rounded-2xl border border-gray-200/80 shadow-sm">
        <div class="flex items-start justify-between mb-4">
            <div>
                <h3 class="text-sm font-bold text-gray-800">Distribusi Kondisi Jalan: Saat Ini vs Prediksi</h3>
                <p class="text-xs text-gray-500 mt-0.5">Perbandingan panjang jalan per kondisi sebelum dan setelah penanganan <?= $tahunSummaryRaw === 'semua' ? 'seluruh tahun' : 'tahun ' . $tahunSummaryRaw ?>.</p>
            </div>
            <div class="flex items-center gap-3 text-xs">
                <div class="flex items-center gap-1.5">
                    <div class="w-3 h-3 rounded bg-green-500 opacity-90"></div>
                    <span class="text-gray-600 font-medium">Solid = Saat Ini (n-1)</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <div class="w-3 h-3 rounded border-2 border-green-500 bg-green-200/30"></div>
                    <span class="text-gray-600 font-medium">Transparan = Prediksi (n)</span>
                </div>
            </div>
        </div>
        <div class="relative h-72">
            <canvas id="chartDistribusiBar"></canvas>
        </div>
    </div>

    <!-- Tabel Detail Per Ruas -->
    <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm overflow-hidden"
         x-data="{ tableUnit: 'pct', selectedRuas: '' }">
        <div class="p-5 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h3 class="text-sm font-bold text-gray-800">Detail Per Ruas Jalan</h3>
                <p class="text-xs text-gray-500 mt-0.5">Perbandingan kemantapan kondisi baseline dan prediksi sesudah penanganan per ruas jalan.</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <!-- Dropdown Filter Ruas Jalan -->
                <div class="flex items-center gap-1.5">
                    <label class="text-xs font-semibold text-gray-500 whitespace-nowrap">Pilih Ruas:</label>
                    <select x-model="selectedRuas"
                            class="text-xs font-semibold border border-gray-300 rounded-xl px-3 py-1.5 bg-white text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-2xs max-w-xs">
                        <option value="">Semua Ruas Jalan (<?= count($perRuas) ?> Ruas)</option>
                        <?php foreach ($perRuas as $r): ?>
                            <option value="<?= $r['id'] ?>"><?= htmlspecialchars($r['kode_ruas']) ?> - <?= htmlspecialchars($r['nama_ruas']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Toggle Slider km / % -->
                <div class="relative flex items-center bg-gray-100 rounded-lg p-0.5" style="width: 72px;">
                    <span class="absolute top-0.5 bottom-0.5 w-[34px] rounded-md bg-white shadow-sm transition-all duration-200 ease-in-out"
                          :style="tableUnit === 'km' ? 'left: 2px;' : 'left: 36px;'"></span>
                    <button type="button" @click="tableUnit = 'km'" title="Tampilkan dalam Kilometer (km)"
                            class="relative z-10 flex-1 py-1 text-[11px] font-semibold rounded-md transition-colors duration-200"
                            :class="tableUnit === 'km' ? 'text-gray-900 font-bold' : 'text-gray-400'">km</button>
                    <button type="button" @click="tableUnit = 'pct'" title="Tampilkan dalam Persentase (%)"
                            class="relative z-10 flex-1 py-1 text-[11px] font-semibold rounded-md transition-colors duration-200"
                            :class="tableUnit === 'pct' ? 'text-gray-900 font-bold' : 'text-gray-400'">%</button>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left font-bold text-gray-600 uppercase tracking-wider">Kode / Nama Ruas</th>
                        <th class="px-4 py-3 text-center font-bold text-gray-600 uppercase tracking-wider">Panjang</th>
                        
                        <!-- Dropdown Tahun Kondisi Baseline (n-1) -->
                        <th class="px-4 py-2.5 text-center bg-amber-50/90 border-l border-amber-100" colspan="2">
                            <div class="inline-flex items-center justify-center gap-1.5 py-0.5">
                                <span class="font-bold text-amber-900 uppercase tracking-wider text-[11px]">Kondisi:</span>
                                <select onchange="window.changePrediksiParam('tahun_baseline', this.value)"
                                        class="text-xs font-bold border border-amber-300 rounded-lg px-2 py-0.5 bg-white text-amber-900 focus:outline-none focus:ring-2 focus:ring-amber-500 shadow-2xs cursor-pointer">
                                    <?php foreach (TahunHelper::getList() as $y): ?>
                                        <option value="<?= $y ?>" <?= $y == ($tahunBaseline ?? 2025) ? 'selected' : '' ?>>Tahun <?= $y ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </th>
                        
                        <!-- Dropdown Tahun Prediksi Sesudah (n) -->
                        <th class="px-4 py-2.5 text-center bg-purple-50/90 border-l border-purple-100" colspan="2">
                            <div class="inline-flex items-center justify-center gap-1.5 py-0.5">
                                <span class="font-bold text-purple-900 uppercase tracking-wider text-[11px]">Prediksi:</span>
                                <select onchange="window.changePrediksiParam('tahun_prediksi', this.value)"
                                        class="text-xs font-bold border border-purple-300 rounded-lg px-2 py-0.5 bg-white text-purple-900 focus:outline-none focus:ring-2 focus:ring-purple-500 shadow-2xs cursor-pointer">
                                    <option value="semua" <?= ($tahunPrediksiRaw ?? '') === 'semua' ? 'selected' : '' ?>>🗓 Semua Tahun</option>
                                    <?php foreach (TahunHelper::getList() as $y): ?>
                                        <option value="<?= $y ?>" <?= $y == ($tahunPrediksiRaw ?? '') ? 'selected' : '' ?>>Tahun <?= $y ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </th>
                        
                        <!-- Kolom Selisih / Gap -->
                        <th class="px-4 py-2.5 text-center font-bold text-indigo-900 uppercase tracking-wider bg-indigo-50/90 border-l border-indigo-100">
                            <div>Selisih / Gap</div>
                            <div class="text-[10px] text-indigo-600 font-semibold lowercase tracking-normal mt-0.5">(prediksi − target)</div>
                        </th>
                    </tr>
                    <tr class="text-[10px] text-gray-500 font-semibold">
                        <th class="px-4 py-2 text-left"></th>
                        <th class="px-4 py-2 text-center text-gray-600">km</th>
                        <!-- Sebelum -->
                        <th class="px-4 py-2 text-center bg-amber-50/60 border-l border-amber-100 text-emerald-700">
                            <span x-show="tableUnit === 'pct'">Mantap (%)</span>
                            <span x-show="tableUnit === 'km'" x-cloak>Mantap (km)</span>
                        </th>
                        <th class="px-4 py-2 text-center bg-amber-50/60 text-rose-700">
                            <span x-show="tableUnit === 'pct'">Tidak Mantap (%)</span>
                            <span x-show="tableUnit === 'km'" x-cloak>Tidak Mantap (km)</span>
                        </th>
                        <!-- Sesudah -->
                        <th class="px-4 py-2 text-center bg-purple-50/60 border-l border-purple-100 text-emerald-700">
                            <span x-show="tableUnit === 'pct'">Mantap (%)</span>
                            <span x-show="tableUnit === 'km'" x-cloak>Mantap (km)</span>
                        </th>
                        <th class="px-4 py-2 text-center bg-purple-50/60 text-rose-700">
                            <span x-show="tableUnit === 'pct'">Tidak Mantap (%)</span>
                            <span x-show="tableUnit === 'km'" x-cloak>Tidak Mantap (km)</span>
                        </th>
                        <th class="px-4 py-2 text-center bg-indigo-50/60 border-l border-indigo-100 text-indigo-700 font-bold">
                            Prediksi − Target (Δ)
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($perRuas as $r): ?>
                    <?php
                    // Sebelum
                    $pctM   = $r['sebelum']['pct_mantap'];
                    $pctTM  = $r['sebelum']['pct_tidak_mantap'];
                    $kmM    = $r['sebelum']['mantap_km'];
                    $kmTM   = $r['sebelum']['tidak_mantap_km'];
                    $colorM = $pctM >= 80 ? 'text-emerald-700' : ($pctM >= 60 ? 'text-amber-700' : 'text-rose-700');

                    // Sesudah
                    $pctMS   = $r['sesudah']['pct_mantap'];
                    $pctTMS  = $r['sesudah']['pct_tidak_mantap'];
                    $kmMS    = $r['sesudah']['mantap_km'];
                    $kmTMS   = $r['sesudah']['tidak_mantap_km'];
                    $colorMS = $pctMS >= 80 ? 'text-emerald-700' : ($pctMS >= 60 ? 'text-amber-700' : 'text-rose-700');

                    // Deltas (Prediksi dikurangi Target/Baseline)
                    $deltaPctM  = round($pctMS - $pctM, 1);
                    $deltaKmM   = round($kmMS - $kmM, 2);
                    $deltaPctTM = round($pctTMS - $pctTM, 1);
                    $deltaKmTM  = round($kmTMS - $kmTM, 2);
                    ?>
                    <tr x-show="!selectedRuas || selectedRuas == '<?= $r['id'] ?>'"
                        class="hover:bg-gray-50/80 transition-colors <?= !$r['ada_penanganan'] ? 'opacity-50' : '' ?>">
                        <td class="px-4 py-3">
                            <a href="<?= base_url('rekap/prediksi/' . $r['id'] . '?tahun=' . $tahunPrediksiRaw) ?>"
                               class="font-bold text-blue-600 hover:underline"><?= htmlspecialchars($r['kode_ruas']) ?></a>
                            <p class="text-gray-500 mt-0.5 line-clamp-1"><?= htmlspecialchars($r['nama_ruas']) ?></p>
                        </td>
                        <td class="px-4 py-3 text-center font-semibold text-gray-700"><?= $fkm($r['panjang_km']) ?></td>
                        
                        <!-- Sebelum: Mantap -->
                        <td class="px-4 py-3 text-center bg-amber-50/30 border-l border-amber-100">
                            <span x-show="tableUnit === 'pct'" class="font-bold <?= $colorM ?>"><?= $pctM ?>%</span>
                            <span x-show="tableUnit === 'km'" x-cloak class="font-bold <?= $colorM ?>"><?= $fkm($kmM) ?></span>
                        </td>
                        
                        <!-- Sebelum: Tidak Mantap -->
                        <td class="px-4 py-3 text-center bg-amber-50/30">
                            <span x-show="tableUnit === 'pct'" class="font-semibold text-rose-700"><?= $pctTM ?>%</span>
                            <span x-show="tableUnit === 'km'" x-cloak class="font-semibold text-rose-700"><?= $fkm($kmTM) ?></span>
                        </td>
                        
                        <!-- Sesudah: Mantap -->
                        <td class="px-4 py-3 text-center bg-purple-50/30 border-l border-purple-100">
                            <div x-show="tableUnit === 'pct'" class="flex items-center justify-center gap-1">
                                <span class="font-bold <?= $colorMS ?>"><?= $pctMS ?>%</span>
                                <?php if ($r['ada_penanganan']): ?>
                                    <span class="text-[10px] font-bold <?= $deltaPctM >= 0 ? 'text-emerald-600' : 'text-rose-600' ?>">
                                        <?= $deltaPctM >= 0 ? '↑' : '↓' ?><?= abs($deltaPctM) ?>%
                                    </span>
                                <?php endif; ?>
                            </div>
                            <div x-show="tableUnit === 'km'" x-cloak class="flex items-center justify-center gap-1">
                                <span class="font-bold <?= $colorMS ?>"><?= $fkm($kmMS) ?></span>
                                <?php if ($r['ada_penanganan']): ?>
                                    <span class="text-[10px] font-bold <?= $deltaKmM >= 0 ? 'text-emerald-600' : 'text-rose-600' ?>">
                                        <?= $deltaKmM >= 0 ? '↑' : '↓' ?><?= abs($deltaKmM) ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </td>
                        
                        <!-- Sesudah: Tidak Mantap -->
                        <td class="px-4 py-3 text-center bg-purple-50/30">
                            <div x-show="tableUnit === 'pct'" class="flex items-center justify-center gap-1">
                                <span class="font-semibold text-rose-700"><?= $pctTMS ?>%</span>
                                <?php if ($r['ada_penanganan']): ?>
                                    <span class="text-[10px] font-bold <?= $deltaPctTM <= 0 ? 'text-emerald-600' : 'text-rose-600' ?>">
                                        <?= $deltaPctTM <= 0 ? '↓' : '↑' ?><?= abs($deltaPctTM) ?>%
                                    </span>
                                <?php endif; ?>
                            </div>
                            <div x-show="tableUnit === 'km'" x-cloak class="flex items-center justify-center gap-1">
                                <span class="font-semibold text-rose-700"><?= $fkm($kmTMS) ?></span>
                                <?php if ($r['ada_penanganan']): ?>
                                    <span class="text-[10px] font-bold <?= $deltaKmTM <= 0 ? 'text-emerald-600' : 'text-rose-600' ?>">
                                        <?= $deltaKmTM <= 0 ? '↓' : '↑' ?><?= abs($deltaKmTM) ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </td>
                        
                        <!-- Selisih / Gap (Prediksi dikurangi Target/Baseline) -->
                        <td class="px-4 py-3 text-center bg-indigo-50/30 border-l border-indigo-100">
                            <div x-show="tableUnit === 'pct'" class="flex flex-col items-center justify-center">
                                <?php if ($deltaPctM > 0): ?>
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 shadow-2xs">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                                        +<?= number_format($deltaPctM, 1) ?>%
                                    </span>
                                <?php elseif ($deltaPctM < 0): ?>
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-800 shadow-2xs">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                                        <?= number_format($deltaPctM, 1) ?>%
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium text-gray-500 bg-gray-100">
                                        0.0%
                                    </span>
                                <?php endif; ?>
                                <?php if ($r['ada_penanganan']): ?>
                                    <a href="<?= base_url('rekap/prediksi/' . $r['id'] . '?tahun=' . $tahunPrediksiRaw) ?>"
                                       class="text-[10px] text-purple-600 hover:text-purple-800 hover:underline mt-0.5 inline-block">
                                        <?= $r['total_penanganan'] ?> paket
                                    </a>
                                <?php endif; ?>
                            </div>
                            <div x-show="tableUnit === 'km'" x-cloak class="flex flex-col items-center justify-center">
                                <?php if ($deltaKmM > 0): ?>
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 shadow-2xs">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                                        +<?= $fkm($deltaKmM) ?> km
                                    </span>
                                <?php elseif ($deltaKmM < 0): ?>
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-800 shadow-2xs">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                                        <?= $fkm($deltaKmM) ?> km
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium text-gray-500 bg-gray-100">
                                        0.00 km
                                    </span>
                                <?php endif; ?>
                                <?php if ($r['ada_penanganan']): ?>
                                    <a href="<?= base_url('rekap/prediksi/' . $r['id'] . '?tahun=' . $tahunPrediksiRaw) ?>"
                                       class="text-[10px] text-purple-600 hover:text-purple-800 hover:underline mt-0.5 inline-block">
                                        <?= $r['total_penanganan'] ?> paket
                                    </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

