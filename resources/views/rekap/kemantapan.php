<?php
/**
 * View: Rekapitulasi Kemantapan Jalan
 */
?>

<div x-data="{ 
    activeTab: 'uptd', 
    searchQuery: '',
    filterTable(items) {
        if (!this.searchQuery) return items;
        const q = this.searchQuery.toLowerCase();
        return items.filter(item => {
            const name = (item.nama || item.nama_ruas || '').toLowerCase();
            const uptd = (item.uptd || item.kabupaten_list || '').toLowerCase();
            return name.includes(q) || uptd.includes(q);
        });
    }
}" class="space-y-6">

    <!-- Header & Action Row -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-2xl border border-gray-200/80 shadow-sm">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="<?= base_url() ?>" class="text-xs font-semibold text-gray-500 hover:text-blue-600 transition-colors">Dashboard</a>
                <span class="text-xs text-gray-400">/</span>
                <span class="text-xs font-semibold text-blue-600">Rekapitulasi Kemantapan</span>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-3">
                <span>Rekapitulasi Kemantapan Jalan</span>
                <span class="text-xs px-2.5 py-1 rounded-full bg-blue-100 text-blue-700 font-semibold border border-blue-200">Provinsi Lampung</span>
            </h1>
            <p class="text-xs text-gray-500 mt-1">Laporan agregat kondisi fisik & tingkat kemantapan jalan per wilayah kewenangan.</p>
        </div>

        <div class="flex items-center gap-2 sm:gap-3">
            <?php if (!empty($availableTahun)): ?>
            <form method="GET" class="flex items-center">
                <div class="relative">
                    <select name="tahun" onchange="this.form.submit()"
                            class="appearance-none pl-8 pr-8 py-2.5 text-xs font-semibold bg-white border border-gray-300 rounded-xl text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all cursor-pointer shadow-sm">
                        <option value="">Semua Tahun</option>
                        <?php foreach ($availableTahun as $thn): ?>
                        <option value="<?= (int)$thn ?>" <?= $selectedTahun === (int)$thn ? 'selected' : '' ?>>
                            <?= (int)$thn ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 left-2.5 flex items-center">
                        <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div class="pointer-events-none absolute inset-y-0 right-2 flex items-center">
                        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                </div>
            </form>
            <?php endif; ?>
            <button type="button" onclick="window.print()" class="inline-flex items-center gap-2 px-3.5 py-2.5 bg-gray-100 text-gray-700 hover:bg-gray-200 rounded-xl text-xs font-semibold border border-gray-300/80 transition-all">
                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Cetak Halaman Ini</span>
            </button>
            <a href="<?= base_url('export') ?>" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white rounded-xl text-xs font-semibold hover:bg-blue-700 shadow-md shadow-blue-500/20 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                <span>Pusat Export</span>
            </a>
        </div>
    </div>

    <!-- Ringkasan Stat Cards KPI -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card Total Panjang -->
        <div class="bg-white p-5 rounded-2xl border border-gray-200/80 shadow-sm relative overflow-hidden">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Total Panjang Jalan</span>
                <div class="p-2 rounded-xl bg-blue-50 text-blue-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                </div>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-2xl font-black text-gray-900"><?= format_number($totalPanjang, 2) ?></span>
                <span class="text-xs font-semibold text-gray-500">Km</span>
            </div>
            <p class="text-[11px] text-gray-400 mt-2">Seluruh Ruas Jalan Provinsi</p>
        </div>

        <!-- Card Jalan Mantap -->
        <div class="bg-white p-5 rounded-2xl border border-emerald-100 shadow-sm relative overflow-hidden bg-gradient-to-br from-white to-emerald-50/30">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold text-emerald-800 uppercase tracking-wider">Jalan Mantap</span>
                <div class="p-2 rounded-xl bg-emerald-100 text-emerald-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-2xl font-black text-emerald-700"><?= format_number($mantapKm, 2) ?></span>
                <span class="text-xs font-semibold text-emerald-600">Km</span>
                <span class="text-xs font-extrabold text-emerald-800 bg-emerald-100 px-2 py-0.5 rounded-full ml-auto"><?= format_number($pctMantap, 1) ?>%</span>
            </div>
            <p class="text-[11px] text-emerald-600 mt-2">Baik (<?= format_number($baikKm, 1) ?> km) + Sedang (<?= format_number($sedangKm, 1) ?> km)</p>
        </div>

        <!-- Card Jalan Tidak Mantap -->
        <div class="bg-white p-5 rounded-2xl border border-rose-100 shadow-sm relative overflow-hidden bg-gradient-to-br from-white to-rose-50/30">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold text-rose-800 uppercase tracking-wider">Tidak Mantap</span>
                <div class="p-2 rounded-xl bg-rose-100 text-rose-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-2xl font-black text-rose-700"><?= format_number($tidakMantapKm, 2) ?></span>
                <span class="text-xs font-semibold text-rose-600">Km</span>
                <span class="text-xs font-extrabold text-rose-800 bg-rose-100 px-2 py-0.5 rounded-full ml-auto"><?= format_number($pctTidakMantap, 1) ?>%</span>
            </div>
            <p class="text-[11px] text-rose-600 mt-2">Rusak Ringan (<?= format_number($rusakRinganKm, 1) ?> km) + Berat (<?= format_number($rusakBeratKm, 1) ?> km)</p>
        </div>

        <!-- Card Visual Bar Kemantapan -->
        <div class="bg-white p-5 rounded-2xl border border-gray-200/80 shadow-sm flex flex-col justify-between">
            <div>
                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Indikator Ratio</span>
                <div class="mt-2 flex items-center justify-between text-xs font-bold">
                    <span class="text-emerald-700">Mantap: <?= format_number($pctMantap, 1) ?>%</span>
                    <span class="text-rose-600">Tidak Mantap: <?= format_number($pctTidakMantap, 1) ?>%</span>
                </div>
            </div>
            <div class="w-full bg-rose-200 rounded-full h-3.5 overflow-hidden flex my-2 border border-gray-200/50">
                <div class="bg-emerald-500 h-full transition-all duration-500" style="width: <?= $pctMantap ?>%"></div>
                <div class="bg-rose-500 h-full transition-all duration-500" style="width: <?= $pctTidakMantap ?>%"></div>
            </div>
            <span class="text-[10px] text-gray-400">Target Mantap Provinsi: ≥ 85.0%</span>
        </div>
    </div>

    <!-- Navigasi Tab & Pencarian -->
    <div class="bg-white p-4 rounded-2xl border border-gray-200/80 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
        <!-- Tabs -->
        <div class="flex items-center gap-1.5 p-1 bg-gray-100/80 rounded-xl w-full md:w-auto">
            <button type="button" @click="activeTab = 'uptd'" :class="activeTab === 'uptd' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-600 hover:text-gray-900'" class="px-4 py-2 rounded-lg text-xs font-bold transition-all flex-1 md:flex-none">
                Per UPTD Wilayah
            </button>
            <button type="button" @click="activeTab = 'kabupaten'" :class="activeTab === 'kabupaten' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-600 hover:text-gray-900'" class="px-4 py-2 rounded-lg text-xs font-bold transition-all flex-1 md:flex-none">
                Per Kabupaten / Kota
            </button>
            <button type="button" @click="activeTab = 'koridor'" :class="activeTab === 'koridor' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-600 hover:text-gray-900'" class="px-4 py-2 rounded-lg text-xs font-bold transition-all flex-1 md:flex-none">
                Per Koridor
            </button>
        </div>

        <!-- Search Box -->
        <div class="relative w-full md:w-72">
            <input type="text" x-model="searchQuery" placeholder="Cari wilayah / UPTD..." class="w-full pl-9 pr-4 py-2 text-xs bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-blue-500 focus:outline-none transition-colors">
            <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        </div>
    </div>

    <!-- TAB 1: Rekap Per UPTD -->
    <div x-show="activeTab === 'uptd'" x-transition class="bg-white rounded-2xl border border-gray-200/80 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
            <h2 class="text-sm font-bold text-gray-900">Rekapitulasi Kemantapan Per Wilayah UPTD</h2>
            <span class="text-xs font-medium text-gray-500">Total <?= count($rekapUptd) ?> Wilayah UPTD</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100/70 text-gray-600 text-[11px] font-extrabold uppercase tracking-wider border-b border-gray-200">
                        <th class="py-3.5 px-4">Wilayah UPTD</th>
                        <th class="py-3.5 px-4">Kabupaten / Kota</th>
                        <th class="py-3.5 px-4 text-right">Total (Km)</th>
                        <th class="py-3.5 px-4 text-right text-emerald-700">Baik (Km)</th>
                        <th class="py-3.5 px-4 text-right text-blue-700">Sedang (Km)</th>
                        <th class="py-3.5 px-4 text-right text-amber-700">R. Ringan (Km)</th>
                        <th class="py-3.5 px-4 text-right text-rose-700">R. Berat (Km)</th>
                        <th class="py-3.5 px-4 text-right bg-emerald-50/80 text-emerald-900">Mantap (Km)</th>
                        <th class="py-3.5 px-4 text-center">% Mantap</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-xs">
                    <?php foreach ($rekapUptd as $row): ?>
                        <tr class="hover:bg-blue-50/40 transition-colors">
                            <td class="py-3.5 px-4 font-bold text-gray-900"><?= e($row['nama']) ?></td>
                            <td class="py-3.5 px-4 text-gray-600 max-w-xs truncate" title="<?= e($row['kabupaten_list']) ?>"><?= e($row['kabupaten_list']) ?></td>
                            <td class="py-3.5 px-4 text-right font-bold text-gray-900"><?= format_number($row['total_panjang_km'], 2) ?></td>
                            <td class="py-3.5 px-4 text-right font-semibold text-emerald-700"><?= format_number($row['baik_km'], 2) ?></td>
                            <td class="py-3.5 px-4 text-right font-semibold text-blue-700"><?= format_number($row['sedang_km'], 2) ?></td>
                            <td class="py-3.5 px-4 text-right font-semibold text-amber-700"><?= format_number($row['rusak_ringan_km'], 2) ?></td>
                            <td class="py-3.5 px-4 text-right font-semibold text-rose-700"><?= format_number($row['rusak_berat_km'], 2) ?></td>
                            <td class="py-3.5 px-4 text-right font-black bg-emerald-50/50 text-emerald-900"><?= format_number($row['mantap_km'], 2) ?></td>
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-2 justify-end">
                                    <div class="w-16 bg-gray-200 rounded-full h-2 overflow-hidden">
                                        <div class="bg-emerald-500 h-full" style="width: <?= $row['pct_mantap'] ?>%"></div>
                                    </div>
                                    <span class="font-extrabold text-xs <?= $row['pct_mantap'] >= 80 ? 'text-emerald-700' : 'text-amber-700' ?>"><?= format_number($row['pct_mantap'], 1) ?>%</span>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- TAB 2: Rekap Per Kabupaten -->
    <div x-show="activeTab === 'kabupaten'" x-transition class="bg-white rounded-2xl border border-gray-200/80 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
            <h2 class="text-sm font-bold text-gray-900">Rekapitulasi Kemantapan Per Kabupaten / Kota</h2>
            <span class="text-xs font-medium text-gray-500">Total <?= count($rekapKabupaten) ?> Wilayah</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100/70 text-gray-600 text-[11px] font-extrabold uppercase tracking-wider border-b border-gray-200">
                        <th class="py-3.5 px-4">Kabupaten / Kota</th>
                        <th class="py-3.5 px-4">UPTD</th>
                        <th class="py-3.5 px-4 text-right">Total (Km)</th>
                        <th class="py-3.5 px-4 text-right text-emerald-700">Baik (Km)</th>
                        <th class="py-3.5 px-4 text-right text-blue-700">Sedang (Km)</th>
                        <th class="py-3.5 px-4 text-right text-amber-700">R. Ringan (Km)</th>
                        <th class="py-3.5 px-4 text-right text-rose-700">R. Berat (Km)</th>
                        <th class="py-3.5 px-4 text-right bg-emerald-50/80 text-emerald-900">Mantap (Km)</th>
                        <th class="py-3.5 px-4 text-center">% Mantap</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-xs">
                    <?php foreach ($rekapKabupaten as $row): ?>
                        <tr class="hover:bg-blue-50/40 transition-colors">
                            <td class="py-3.5 px-4 font-bold text-gray-900"><?= e($row['nama']) ?></td>
                            <td class="py-3.5 px-4 font-medium text-gray-500"><?= e($row['uptd']) ?></td>
                            <td class="py-3.5 px-4 text-right font-bold text-gray-900"><?= format_number($row['total_panjang_km'], 2) ?></td>
                            <td class="py-3.5 px-4 text-right font-semibold text-emerald-700"><?= format_number($row['baik_km'], 2) ?></td>
                            <td class="py-3.5 px-4 text-right font-semibold text-blue-700"><?= format_number($row['sedang_km'], 2) ?></td>
                            <td class="py-3.5 px-4 text-right font-semibold text-amber-700"><?= format_number($row['rusak_ringan_km'], 2) ?></td>
                            <td class="py-3.5 px-4 text-right font-semibold text-rose-700"><?= format_number($row['rusak_berat_km'], 2) ?></td>
                            <td class="py-3.5 px-4 text-right font-black bg-emerald-50/50 text-emerald-900"><?= format_number($row['mantap_km'], 2) ?></td>
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-2 justify-end">
                                    <div class="w-16 bg-gray-200 rounded-full h-2 overflow-hidden">
                                        <div class="bg-emerald-500 h-full" style="width: <?= $row['pct_mantap'] ?>%"></div>
                                    </div>
                                    <span class="font-extrabold text-xs <?= $row['pct_mantap'] >= 80 ? 'text-emerald-700' : 'text-amber-700' ?>"><?= format_number($row['pct_mantap'], 1) ?>%</span>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- TAB 3: Rekap Per Koridor -->
    <div x-show="activeTab === 'koridor'" x-transition class="bg-white rounded-2xl border border-gray-200/80 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
            <h2 class="text-sm font-bold text-gray-900">Rekapitulasi Kemantapan Per Koridor</h2>
            <span class="text-xs font-medium text-gray-500">Total <?= count($rekapKoridor) ?> Koridor</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100/70 text-gray-600 text-[11px] font-extrabold uppercase tracking-wider border-b border-gray-200">
                        <th class="py-3.5 px-4">Koridor</th>
                        <th class="py-3.5 px-4 text-right">Total Panjang (Km)</th>
                        <th class="py-3.5 px-4 text-right text-emerald-700">Mantap (Km)</th>
                        <th class="py-3.5 px-4 text-right text-rose-700">Tidak Mantap (Km)</th>
                        <th class="py-3.5 px-4 text-center">% Mantap</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-xs">
                    <?php foreach ($rekapKoridor as $row): ?>
                        <tr class="hover:bg-blue-50/40 transition-colors">
                            <td class="py-3.5 px-4 font-bold text-gray-900"><?= e($row['nama']) ?></td>
                            <td class="py-3.5 px-4 text-right font-bold text-gray-900"><?= format_number($row['total_panjang_km'], 2) ?></td>
                            <td class="py-3.5 px-4 text-right font-semibold text-emerald-700"><?= format_number($row['mantap_km'], 2) ?></td>
                            <td class="py-3.5 px-4 text-right font-semibold text-rose-700"><?= format_number($row['tidak_mantap_km'], 2) ?></td>
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-2 justify-end">
                                    <div class="w-24 bg-gray-200 rounded-full h-2.5 overflow-hidden">
                                        <div class="bg-emerald-500 h-full" style="width: <?= $row['pct_mantap'] ?>%"></div>
                                    </div>
                                    <span class="font-extrabold text-xs text-emerald-700"><?= format_number($row['pct_mantap'], 1) ?>%</span>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>
