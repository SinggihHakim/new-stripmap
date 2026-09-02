<?php
/**
 * View: Rekapitulasi Jenis Perkerasan Jalan
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
                <span class="text-xs font-semibold text-blue-600">Rekapitulasi Perkerasan</span>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-3">
                <span>Rekapitulasi Jenis Perkerasan</span>
                <span class="text-xs px-2.5 py-1 rounded-full bg-indigo-100 text-indigo-700 font-semibold border border-indigo-200">Struktur Jalan</span>
            </h1>
            <p class="text-xs text-gray-500 mt-1">Laporan agregat struktur jenis konstruksi perkerasan jalan (Rigid, Aspal, Agregat, Belum Tembus).</p>
        </div>

        <div class="flex items-center gap-2 sm:gap-3">
            <?php if (!empty($availableTahun)): ?>
            <form method="GET" class="flex items-center">
                <div class="relative">
                    <select name="tahun" onchange="this.form.submit()"
                            class="appearance-none pl-8 pr-8 py-2.5 text-xs font-semibold bg-white border border-gray-300 rounded-xl text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all cursor-pointer shadow-sm">
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

    <!-- Ringkasan Stat Cards Struktur Perkerasan -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card Rigid (Beton) -->
        <div class="bg-white p-5 rounded-2xl border border-blue-100 shadow-sm relative overflow-hidden bg-gradient-to-br from-white to-blue-50/30">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold text-blue-900 uppercase tracking-wider">Rigid (Beton)</span>
                <div class="p-2 rounded-xl bg-blue-100 text-blue-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                </div>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-2xl font-black text-blue-900"><?= format_number($rigidKm, 2) ?></span>
                <span class="text-xs font-semibold text-blue-700">Km</span>
                <span class="text-xs font-extrabold text-blue-900 bg-blue-100 px-2 py-0.5 rounded-full ml-auto"><?= format_number($pctRigid, 1) ?>%</span>
            </div>
            <p class="text-[11px] text-blue-600 mt-2">Konstruksi Beton Semen</p>
        </div>

        <!-- Card Aspal -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm relative overflow-hidden bg-gradient-to-br from-white to-slate-100/50">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold text-slate-800 uppercase tracking-wider">Lapis Aspal</span>
                <div class="p-2 rounded-xl bg-slate-200 text-slate-800">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                </div>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-2xl font-black text-slate-900"><?= format_number($aspalKm, 2) ?></span>
                <span class="text-xs font-semibold text-slate-600">Km</span>
                <span class="text-xs font-extrabold text-slate-900 bg-slate-200 px-2 py-0.5 rounded-full ml-auto"><?= format_number($pctAspal, 1) ?>%</span>
            </div>
            <p class="text-[11px] text-slate-600 mt-2">Flex Pavement (AC-WC/AC-BC)</p>
        </div>

        <!-- Card Kerikil -->
        <div class="bg-white p-5 rounded-2xl border border-amber-100 shadow-sm relative overflow-hidden bg-gradient-to-br from-white to-amber-50/30">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold text-amber-900 uppercase tracking-wider">Kerikil</span>
                <div class="p-2 rounded-xl bg-amber-100 text-amber-800">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 7v10c0 2 1.5 3 3.5 3h9c2 0 3.5-1 3.5-3V7M4 7c0-2 1.5-3 3.5-3h9c2 0 3.5 1 3.5 3M4 7h16"/></svg>
                </div>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-2xl font-black text-amber-900"><?= format_number($agregatTanahKm, 2) ?></span>
                <span class="text-xs font-semibold text-amber-700">Km</span>
                <span class="text-xs font-extrabold text-amber-900 bg-amber-100 px-2 py-0.5 rounded-full ml-auto"><?= format_number($pctAgregatTanah, 1) ?>%</span>
            </div>
            <p class="text-[11px] text-amber-700 mt-2">Unpaved / Kerikil & Tanah</p>
        </div>

        <!-- Card Belum Tembus -->
        <div class="bg-white p-5 rounded-2xl border border-red-100 shadow-sm relative overflow-hidden bg-gradient-to-br from-white to-red-50/30">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold text-red-900 uppercase tracking-wider">Belum Tembus</span>
                <div class="p-2 rounded-xl bg-red-100 text-red-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                </div>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-2xl font-black text-red-900"><?= format_number($belumTembusKm, 2) ?></span>
                <span class="text-xs font-semibold text-red-700">Km</span>
                <span class="text-xs font-extrabold text-red-900 bg-red-100 px-2 py-0.5 rounded-full ml-auto"><?= format_number($pctBelumTembus, 1) ?>%</span>
            </div>
            <p class="text-[11px] text-red-600 mt-2">Trase Jalan Belum Terbuka</p>
        </div>
    </div>

    <!-- Navigasi Tab & Search -->
    <div class="bg-white p-4 rounded-2xl border border-gray-200/80 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
        <!-- Tabs -->
        <div class="flex items-center gap-1.5 p-1 bg-gray-100/80 rounded-xl w-full md:w-auto">
            <button type="button" @click="activeTab = 'uptd'" :class="activeTab === 'uptd' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-600 hover:text-gray-900'" class="px-4 py-2 rounded-lg text-xs font-bold transition-all flex-1 md:flex-none">
                Per UPTD Wilayah
            </button>
            <button type="button" @click="activeTab = 'kabupaten'" :class="activeTab === 'kabupaten' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-600 hover:text-gray-900'" class="px-4 py-2 rounded-lg text-xs font-bold transition-all flex-1 md:flex-none">
                Per Kabupaten / Kota
            </button>
            <button type="button" @click="activeTab = 'ruas'" :class="activeTab === 'ruas' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-600 hover:text-gray-900'" class="px-4 py-2 rounded-lg text-xs font-bold transition-all flex-1 md:flex-none">
                Per Ruas Jalan
            </button>
        </div>

        <!-- Search Box -->
        <div class="relative w-full md:w-72">
            <input type="text" x-model="searchQuery" placeholder="Cari wilayah / perkerasan..." class="w-full pl-9 pr-4 py-2 text-xs bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-blue-500 focus:outline-none transition-colors">
            <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        </div>
    </div>

    <!-- TAB 1: Rekap Per UPTD -->
    <div x-show="activeTab === 'uptd'" x-transition class="bg-white rounded-2xl border border-gray-200/80 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
            <h2 class="text-sm font-bold text-gray-900">Rekapitulasi Struktur Perkerasan Per UPTD</h2>
            <span class="text-xs font-medium text-gray-500">Total <?= count($rekapUptd) ?> Wilayah UPTD</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100/70 text-gray-600 text-[11px] font-extrabold uppercase tracking-wider border-b border-gray-200">
                        <th class="py-3.5 px-4">Wilayah UPTD</th>
                        <th class="py-3.5 px-4">Kabupaten / Kota</th>
                        <th class="py-3.5 px-4 text-right">Total Panjang (Km)</th>
                        <th class="py-3.5 px-4 text-right text-blue-800">Rigid / Beton (Km)</th>
                        <th class="py-3.5 px-4 text-right text-slate-800">Aspal (Km)</th>
                        <th class="py-3.5 px-4 text-right text-amber-800">Kerikil (Km)</th>
                        <th class="py-3.5 px-4 text-right text-red-800">Belum Tembus (Km)</th>
                        <th class="py-3.5 px-4 text-center">Komposisi Perkerasan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-xs">
                    <?php foreach ($rekapUptd as $row): ?>
                        <tr class="hover:bg-blue-50/40 transition-colors">
                            <td class="py-3.5 px-4 font-bold text-gray-900"><?= e($row['nama']) ?></td>
                            <td class="py-3.5 px-4 text-gray-600 max-w-xs truncate" title="<?= e($row['kabupaten_list']) ?>"><?= e($row['kabupaten_list']) ?></td>
                            <td class="py-3.5 px-4 text-right font-bold text-gray-900"><?= format_number($row['total_panjang_km'], 2) ?></td>
                            <td class="py-3.5 px-4 text-right font-semibold text-blue-800"><?= format_number($row['rigid_km'], 2) ?></td>
                            <td class="py-3.5 px-4 text-right font-semibold text-slate-800"><?= format_number($row['aspal_km'], 2) ?></td>
                            <td class="py-3.5 px-4 text-right font-semibold text-amber-800"><?= format_number($row['agregat_tanah_km'], 2) ?></td>
                            <td class="py-3.5 px-4 text-right font-semibold text-red-800"><?= format_number($row['belum_tembus_km'], 2) ?></td>
                            <td class="py-3.5 px-4">
                                <div class="w-32 bg-gray-200 rounded-full h-3 overflow-hidden flex ml-auto border border-gray-200">
                                    <div class="bg-blue-600 h-full" style="width: <?= $row['pct_rigid'] ?>%" title="Rigid: <?= $row['pct_rigid'] ?>%"></div>
                                    <div class="bg-slate-700 h-full" style="width: <?= $row['pct_aspal'] ?>%" title="Aspal: <?= $row['pct_aspal'] ?>%"></div>
                                    <div class="bg-amber-500 h-full" style="width: <?= $row['pct_agregat_tanah'] ?>%" title="Kerikil: <?= $row['pct_agregat_tanah'] ?>%"></div>
                                    <div class="bg-red-500 h-full" style="width: <?= $row['pct_belum_tembus'] ?>%" title="Belum Tembus: <?= $row['pct_belum_tembus'] ?>%"></div>
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
            <h2 class="text-sm font-bold text-gray-900">Rekapitulasi Struktur Perkerasan Per Kabupaten / Kota</h2>
            <span class="text-xs font-medium text-gray-500">Total <?= count($rekapKabupaten) ?> Wilayah</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100/70 text-gray-600 text-[11px] font-extrabold uppercase tracking-wider border-b border-gray-200">
                        <th class="py-3.5 px-4">Kabupaten / Kota</th>
                        <th class="py-3.5 px-4">UPTD</th>
                        <th class="py-3.5 px-4 text-right">Total Panjang (Km)</th>
                        <th class="py-3.5 px-4 text-right text-blue-800">Rigid / Beton (Km)</th>
                        <th class="py-3.5 px-4 text-right text-slate-800">Aspal (Km)</th>
                        <th class="py-3.5 px-4 text-right text-amber-800">Kerikil (Km)</th>
                        <th class="py-3.5 px-4 text-right text-red-800">Belum Tembus (Km)</th>
                        <th class="py-3.5 px-4 text-center">Komposisi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-xs">
                    <?php foreach ($rekapKabupaten as $row): ?>
                        <tr class="hover:bg-blue-50/40 transition-colors">
                            <td class="py-3.5 px-4 font-bold text-gray-900"><?= e($row['nama']) ?></td>
                            <td class="py-3.5 px-4 font-medium text-gray-500"><?= e($row['uptd']) ?></td>
                            <td class="py-3.5 px-4 text-right font-bold text-gray-900"><?= format_number($row['total_panjang_km'], 2) ?></td>
                            <td class="py-3.5 px-4 text-right font-semibold text-blue-800"><?= format_number($row['rigid_km'], 2) ?></td>
                            <td class="py-3.5 px-4 text-right font-semibold text-slate-800"><?= format_number($row['aspal_km'], 2) ?></td>
                            <td class="py-3.5 px-4 text-right font-semibold text-amber-800"><?= format_number($row['agregat_tanah_km'], 2) ?></td>
                            <td class="py-3.5 px-4 text-right font-semibold text-red-800"><?= format_number($row['belum_tembus_km'], 2) ?></td>
                            <td class="py-3.5 px-4">
                                <div class="w-32 bg-gray-200 rounded-full h-3 overflow-hidden flex ml-auto border border-gray-200">
                                    <div class="bg-blue-600 h-full" style="width: <?= $row['pct_rigid'] ?>%"></div>
                                    <div class="bg-slate-700 h-full" style="width: <?= $row['pct_aspal'] ?>%"></div>
                                    <div class="bg-amber-500 h-full" style="width: <?= $row['pct_agregat_tanah'] ?>%"></div>
                                    <div class="bg-red-500 h-full" style="width: <?= $row['pct_belum_tembus'] ?>%"></div>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- TAB 3: Per Ruas Jalan -->
    <div x-show="activeTab === 'ruas'" x-transition class="bg-white rounded-2xl border border-gray-200/80 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
            <h2 class="text-sm font-bold text-gray-900">Perkerasan Detail Per Ruas Jalan</h2>
            <span class="text-xs font-medium text-gray-500">Total <?= count($ruasPerkerasan) ?> Ruas Jalan</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100/70 text-gray-600 text-[11px] font-extrabold uppercase tracking-wider border-b border-gray-200">
                        <th class="py-3.5 px-4">Kode / Nama Ruas</th>
                        <th class="py-3.5 px-4">Kabupaten / UPTD</th>
                        <th class="py-3.5 px-4 text-right">Panjang (Km)</th>
                        <th class="py-3.5 px-4 text-right text-blue-800">Rigid (Km)</th>
                        <th class="py-3.5 px-4 text-right text-slate-800">Aspal (Km)</th>
                        <th class="py-3.5 px-4 text-right text-amber-800">Agregat (Km)</th>
                        <th class="py-3.5 px-4 text-right text-red-800">Belum Tembus (Km)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-xs">
                    <?php foreach ($ruasPerkerasan as $row): ?>
                        <tr class="hover:bg-blue-50/40 transition-colors">
                            <td class="py-3.5 px-4 font-bold text-gray-900">
                                <span class="text-blue-600 text-[11px] block font-mono"><?= e($row['kode_ruas']) ?></span>
                                <?= e($row['nama_ruas']) ?>
                            </td>
                            <td class="py-3.5 px-4 text-gray-600">
                                <?= e($row['kabupaten_kota']) ?>
                                <span class="text-[10px] text-gray-400 block font-medium"><?= e($row['uptd']) ?></span>
                            </td>
                            <td class="py-3.5 px-4 text-right font-bold text-gray-900"><?= format_number($row['total_panjang_km'], 2) ?></td>
                            <td class="py-3.5 px-4 text-right font-semibold text-blue-800"><?= format_number($row['rigid_km'], 2) ?></td>
                            <td class="py-3.5 px-4 text-right font-semibold text-slate-800"><?= format_number($row['aspal_km'], 2) ?></td>
                            <td class="py-3.5 px-4 text-right font-semibold text-amber-800"><?= format_number($row['agregat_tanah_km'], 2) ?></td>
                            <td class="py-3.5 px-4 text-right font-semibold text-red-800"><?= format_number($row['belum_tembus_km'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>
