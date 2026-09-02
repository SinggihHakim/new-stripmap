<!-- ============================================================ -->
<!-- Dashboard -->
<!-- ============================================================ -->

<div id="dashboard-page" class="space-y-8">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
                <?php if (!empty($selectedTahun)): ?>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200 shadow-sm">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-600 animate-pulse"></span>
                        Tahun <?= (int)$selectedTahun ?>
                    </span>
                <?php endif; ?>
            </div>
            <p class="mt-1 text-sm text-gray-500">Ringkasan data ruas jalan, strip map, jenis perkerasan, dan segmentasi penanganan.</p>
        </div>
        
        <!-- Filter Tahun Dropdown -->
        <div class="flex items-center gap-2.5">
            <?php if (!empty($availableTahun)): ?>
            <form method="GET" action="<?= base_url('dashboard') ?>" class="flex items-center">
                <div class="relative">
                    <select name="tahun" onchange="this.form.submit()"
                            class="appearance-none pl-9 pr-9 py-2.5 text-xs font-semibold bg-white border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm transition-all cursor-pointer">
                        <option value="">🗓 Semua Tahun</option>
                        <?php foreach ($availableTahun as $thn): ?>
                            <option value="<?= (int)$thn ?>" <?= (!empty($selectedTahun) && (int)$selectedTahun === (int)$thn) ? 'selected' : '' ?>>
                                Tahun <?= (int)$thn ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 left-3 flex items-center">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div class="pointer-events-none absolute inset-y-0 right-2.5 flex items-center">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                </div>
            </form>
            <?php endif; ?>
        </div>
    </div>

    <!-- Load Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Capture Area -->
    <div id="capture-area" class="space-y-8 bg-transparent pb-4">
    <!-- Stats Cards & Pie Charts Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left Panel: Metric Cards (2 - 4 - 2 - 4 - 4 Grid Layout) -->
        <div class="lg:col-span-2 space-y-4">
            
            <!-- Row 1: 2 Grid (Total Ruas & Total Panjang) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Card 1: Total Ruas Jalan -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                        </div>
                        <span data-dashboard-export-center-text class="text-xs font-semibold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-md">General</span>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900"><?= $totalRuas ?? 0 ?> <span class="text-xs font-semibold text-gray-400">Ruas</span></h3>
                    <p class="text-[13px] font-semibold text-gray-500 mt-1">Total Ruas Jalan</p>
                </div>

                <!-- Card 2: Total Panjang Jalan -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-10 h-10 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                            </svg>
                        </div>
                        <span data-dashboard-export-center-text class="text-xs font-semibold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-md">Panjang</span>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900"><?= format_number($totalPanjang ?? 0.0, 2) ?> <span class="text-xs font-semibold text-gray-400">km</span></h3>
                    <p class="text-[13px] font-semibold text-gray-500 mt-1">Total Panjang Jalan</p>
                </div>
            </div>

            <!-- Row 2: 4 Grid (Detail Kondisi Segmen: Baik, Sedang, Rusak Ringan, Rusak Berat) -->
            <?php $thnQuery = !empty($selectedTahun) ? '&tahun=' . (int)$selectedTahun : ''; ?>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <!-- Card 3: Baik -->
                <a href="<?= base_url('dashboard/detail?kondisi=baik' . $thnQuery) ?>" 
                   title="Klik untuk melihat detail ruas jalan kondisi Baik"
                   class="p-4 rounded-xl border shadow-sm hover:shadow-md transition-all transform hover:-translate-y-0.5 group cursor-pointer block" 
                   style="background-color: #f0fdf4; border-color: #d1fae5;">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full" style="background-color: #10b981; display: inline-block; width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0;"></span>
                            <span data-dashboard-export-dot-label class="text-xs font-semibold text-emerald-800">Baik</span>
                        </div>
                        <span data-dashboard-export-percent-badge class="inline-flex items-center px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 text-[10px] font-bold">
                            <?= format_number($pctBaik ?? 0.0, 1) ?>%
                        </span>
                    </div>
                    <h3 class="text-xl font-bold text-emerald-700"><?= format_number($baikKm ?? 0.0, 2) ?> <span class="text-xs font-normal text-emerald-600">km</span></h3>
                    <p class="text-[11px] font-medium text-emerald-600 mt-0.5">
                        <span>Kondisi Baik</span>
                    </p>
                </a>

                <!-- Card 4: Sedang -->
                <a href="<?= base_url('dashboard/detail?kondisi=sedang' . $thnQuery) ?>" 
                   title="Klik untuk melihat detail ruas jalan kondisi Sedang"
                   class="p-4 rounded-xl border shadow-sm hover:shadow-md transition-all transform hover:-translate-y-0.5 group cursor-pointer block" 
                   style="background-color: #fefce8; border-color: #fef08a;">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full" style="background-color: #facc15; display: inline-block; width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0;"></span>
                            <span data-dashboard-export-dot-label class="text-xs font-semibold text-yellow-800">Sedang</span>
                        </div>
                        <span data-dashboard-export-percent-badge class="inline-flex items-center px-2 py-0.5 rounded bg-yellow-100 text-yellow-800 text-[10px] font-bold">
                            <?= format_number($pctSedang ?? 0.0, 1) ?>%
                        </span>
                    </div>
                    <h3 class="text-xl font-bold text-yellow-700"><?= format_number($sedangKm ?? 0.0, 2) ?> <span class="text-xs font-normal text-yellow-600">km</span></h3>
                    <p class="text-[11px] font-medium text-yellow-600 mt-0.5">
                        <span>Kondisi Sedang</span>
                    </p>
                </a>

                <!-- Card 5: Rusak Ringan -->
                <a href="<?= base_url('dashboard/detail?kondisi=rusak_ringan' . $thnQuery) ?>" 
                   title="Klik untuk melihat detail ruas jalan kondisi Rusak Ringan (Terpanjang ke Terpendek)"
                   class="p-4 rounded-xl border shadow-sm hover:shadow-md transition-all transform hover:-translate-y-0.5 group cursor-pointer block ring-2 ring-orange-400/40" 
                   style="background-color: #fff7ed; border-color: #ffedd5;">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full" style="background-color: #f97316; display: inline-block; width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0;"></span>
                            <span data-dashboard-export-dot-label class="text-xs font-bold text-orange-900">Rusak Ringan</span>
                        </div>
                        <span data-dashboard-export-percent-badge class="inline-flex items-center px-2 py-0.5 rounded bg-orange-100 text-orange-800 text-[10px] font-bold">
                            <?= format_number($pctRusakRingan ?? 0.0, 1) ?>%
                        </span>
                    </div>
                    <h3 class="text-xl font-bold text-orange-700"><?= format_number($rusakRinganKm ?? 0.0, 2) ?> <span class="text-xs font-normal text-orange-600">km</span></h3>
                    <p class="text-[11px] font-semibold text-orange-600 mt-0.5">
                        <span>Rusak Ringan</span>
                    </p>
                </a>

                <!-- Card 6: Rusak Berat -->
                <a href="<?= base_url('dashboard/detail?kondisi=rusak_berat' . $thnQuery) ?>" 
                   title="Klik untuk melihat detail ruas jalan kondisi Rusak Berat (Terpanjang ke Terpendek)"
                   class="p-4 rounded-xl border shadow-sm hover:shadow-md transition-all transform hover:-translate-y-0.5 group cursor-pointer block ring-2 ring-red-400/40" 
                   style="background-color: #fef2f2; border-color: #fee2e2;">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full" style="background-color: #ef4444; display: inline-block; width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0;"></span>
                            <span data-dashboard-export-dot-label class="text-xs font-bold text-red-900">Rusak Berat</span>
                        </div>
                        <span data-dashboard-export-percent-badge class="inline-flex items-center px-2 py-0.5 rounded bg-red-100 text-red-800 text-[10px] font-bold">
                            <?= format_number($pctRusakBerat ?? 0.0, 1) ?>%
                        </span>
                    </div>
                    <h3 class="text-xl font-bold text-red-700"><?= format_number($rusakBeratKm ?? 0.0, 2) ?> <span class="text-xs font-normal text-red-600">km</span></h3>
                    <p class="text-[11px] font-semibold text-red-600 mt-0.5">
                        <span>Rusak Berat</span>
                    </p>
                </a>
            </div>

            <!-- Row 3: 2 Grid (Kemantapan Jalan: Mantap vs Tidak Mantap) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Card 7: Mantap -->
                <a href="<?= base_url('dashboard/detail?kondisi=mantap' . $thnQuery) ?>" 
                   title="Klik untuk melihat detail ruas jalan Mantap"
                   class="p-4 rounded-xl border shadow-sm hover:shadow-md transition-all transform hover:-translate-y-0.5 group cursor-pointer block" 
                   style="background-color: #f0fdf4; border-color: #d1fae5;">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full" style="background-color: #10b981; display: inline-block; width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0;"></span>
                            <span data-dashboard-export-dot-label class="text-xs font-semibold text-emerald-800">Mantap <span class="font-normal text-emerald-600">(Baik + Sedang)</span></span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span data-dashboard-export-center-text class="text-xs font-bold text-emerald-700"><?= format_number($pctMantap ?? 0.0, 1) ?>%</span>
                        </div>
                    </div>
                    <h3 data-dashboard-export-stability-value class="text-2xl font-bold text-emerald-700"><?= format_number($mantapKm ?? 0.0, 2) ?> <span class="text-xs font-semibold text-emerald-600">km</span></h3>
                    <div class="mt-2.5 w-full rounded-full h-2" style="background-color: rgba(16, 185, 129, 0.2);">
                        <div class="h-2 rounded-full" style="width: <?= number_format($pctMantap ?? 0.0, 4, '.', '') ?>%; background-color: #10b981;"></div>
                    </div>
                </a>

                <!-- Card 8: Tidak Mantap -->
                <a href="<?= base_url('dashboard/detail?kondisi=tidak_mantap' . $thnQuery) ?>" 
                   title="Klik untuk melihat detail ruas jalan Tidak Mantap"
                   class="p-4 rounded-xl border shadow-sm hover:shadow-md transition-all transform hover:-translate-y-0.5 group cursor-pointer block" 
                   style="background-color: #fff1f2; border-color: #ffe4e6;">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full" style="background-color: #ef4444; display: inline-block; width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0;"></span>
                            <span data-dashboard-export-dot-label class="text-xs font-semibold text-rose-800">Tidak Mantap <span class="font-normal text-rose-600">(R. Ringan + R. Berat)</span></span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span data-dashboard-export-center-text class="text-xs font-bold text-rose-700"><?= format_number($pctTidakMantap ?? 0.0, 1) ?>%</span>
                        </div>
                    </div>
                    <h3 data-dashboard-export-stability-value class="text-2xl font-bold text-rose-700"><?= format_number($tidakMantapKm ?? 0.0, 2) ?> <span class="text-xs font-semibold text-rose-600">km</span></h3>
                    <div class="mt-2.5 w-full rounded-full h-2" style="background-color: rgba(239, 68, 68, 0.2);">
                        <div class="h-2 rounded-full" style="width: <?= number_format($pctTidakMantap ?? 0.0, 4, '.', '') ?>%; background-color: #ef4444;"></div>
                    </div>
                </a>
            </div>

            <!-- Row 4: 4 Grid (Detail Jenis Perkerasan Jalan: Rigid, Aspal, Kerikil, Belum Tembus) -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <!-- Card 9: Rigid -->
                <div class="p-4 rounded-xl border shadow-sm hover:shadow-md transition-all transform hover:-translate-y-0.5" style="background-color: #475569; border-color: #334155;">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full" style="background-color: #cbd5e1; display: inline-block; width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0;"></span>
                            <span data-dashboard-export-dot-label class="text-xs font-semibold text-white">Rigid</span>
                        </div>
                        <span data-dashboard-export-percent-badge class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold" style="background-color: rgba(0, 0, 0, 0.25); color: #ffffff;">
                            <?= format_number($pctRigid ?? 0.0, 1) ?>%
                        </span>
                    </div>
                    <h3 data-dashboard-export-pavement-value class="text-xl font-bold text-white"><?= format_number($rigidKm ?? 0.0, 2) ?> <span class="text-xs font-normal text-slate-200">km</span></h3>
                    <p class="text-[11px] font-medium text-slate-200 mt-0.5">Rigid / Beton</p>
                </div>

                <!-- Card 10: Aspal -->
                <div class="p-4 rounded-xl border shadow-sm hover:shadow-md transition-all transform hover:-translate-y-0.5" style="background-color: #0f172a; border-color: #020617;">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full" style="background-color: #38bdf8; display: inline-block; width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0;"></span>
                            <span data-dashboard-export-dot-label class="text-xs font-semibold text-white">Aspal</span>
                        </div>
                        <span data-dashboard-export-percent-badge class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold" style="background-color: rgba(255, 255, 255, 0.15); color: #ffffff;">
                            <?= format_number($pctAspal ?? 0.0, 1) ?>%
                        </span>
                    </div>
                    <h3 data-dashboard-export-pavement-value class="text-xl font-bold text-white"><?= format_number($aspalKm ?? 0.0, 2) ?> <span class="text-xs font-normal text-slate-300">km</span></h3>
                    <p class="text-[11px] font-medium text-slate-300 mt-0.5">Flexible / Aspal</p>
                </div>

                <!-- Card 11: Kerikil -->
                <div class="p-4 rounded-xl border shadow-sm hover:shadow-md transition-all transform hover:-translate-y-0.5" style="background-color: #7c461b; border-color: #5c3211;">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full" style="background-color: #fde047; display: inline-block; width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0;"></span>
                            <span data-dashboard-export-dot-label class="text-xs font-semibold text-white">Kerikil</span>
                        </div>
                        <span data-dashboard-export-percent-badge class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold" style="background-color: rgba(0, 0, 0, 0.25); color: #ffffff;">
                            <?= format_number($pctAgregatTanah ?? 0.0, 1) ?>%
                        </span>
                    </div>
                    <h3 data-dashboard-export-pavement-value class="text-xl font-bold text-white"><?= format_number($agregatTanahKm ?? 0.0, 2) ?> <span class="text-xs font-normal text-amber-100">km</span></h3>
                    <p class="text-[11px] font-medium text-amber-100 mt-0.5">Kerikil / Tanah</p>
                </div>

                <!-- Card 12: Belum Tembus -->
                <div class="p-4 rounded-xl border shadow-sm hover:shadow-md transition-all transform hover:-translate-y-0.5" style="background-color: #7e22ce; border-color: #6b21a8;">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full" style="background-color: #f0abfc; display: inline-block; width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0;"></span>
                            <span data-dashboard-export-dot-label class="text-xs font-semibold text-white">Belum Tembus</span>
                        </div>
                        <span data-dashboard-export-percent-badge class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold" style="background-color: rgba(0, 0, 0, 0.25); color: #ffffff;">
                            <?= format_number($pctBelumTembus ?? 0.0, 1) ?>%
                        </span>
                    </div>
                    <h3 data-dashboard-export-pavement-value class="text-xl font-bold text-white"><?= format_number($belumTembusKm ?? 0.0, 2) ?> <span class="text-xs font-normal text-purple-100">km</span></h3>
                    <p class="text-[11px] font-medium text-purple-100 mt-0.5">Belum Tembus</p>
                </div>
            </div>

            <!-- Row 5: 4 Grid (Detail Segmentasi Penanganan Jalan: Rencana, Proses, Selesai, Total Anggaran) -->
            <?php if (!empty($penangananStats) && $penangananStats['total_paket'] > 0): ?>
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                <!-- Rencana -->
                <div class="p-4 rounded-xl border shadow-sm hover:shadow-md transition-all transform hover:-translate-y-0.5" style="background-color: #f0f9ff; border-color: #bae6fd;">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full" style="background-color: #0284c7; display: inline-block; width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0;"></span>
                            <span class="text-xs font-semibold text-sky-800">Rencana</span>
                        </div>
                        <span class="inline-flex items-center px-2 py-0.5 rounded bg-sky-100 text-sky-800 text-[10px] font-bold">
                            <?= number_format($penangananStats['pct_rencana'] ?? 0.0, 1) ?>%
                        </span>
                    </div>
                    <h3 class="text-xl font-bold text-sky-700"><?= format_number($penangananStats['rencana_km'] ?? 0.0, 2) ?> <span class="text-xs font-normal text-sky-600">km</span></h3>
                    <p class="text-[11px] font-medium text-sky-600 mt-0.5">Usulan / Rencana</p>
                </div>

                <!-- Proses -->
                <div class="p-4 rounded-xl border shadow-sm hover:shadow-md transition-all transform hover:-translate-y-0.5" style="background-color: #eef2ff; border-color: #c7d2fe;">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full" style="background-color: #6366f1; display: inline-block; width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0;"></span>
                            <span class="text-xs font-semibold text-indigo-800">Sedang Dikerjakan</span>
                        </div>
                        <span class="inline-flex items-center px-2 py-0.5 rounded bg-indigo-100 text-indigo-800 text-[10px] font-bold">
                            <?= number_format($penangananStats['pct_proses'] ?? 0.0, 1) ?>%
                        </span>
                    </div>
                    <h3 class="text-xl font-bold text-indigo-700"><?= format_number($penangananStats['proses_km'] ?? 0.0, 2) ?> <span class="text-xs font-normal text-indigo-600">km</span></h3>
                    <p class="text-[11px] font-medium text-indigo-600 mt-0.5">Dalam Pengerjaan</p>
                </div>

                <!-- Selesai -->
                <div class="p-4 rounded-xl border shadow-sm hover:shadow-md transition-all transform hover:-translate-y-0.5" style="background-color: #ecfdf5; border-color: #a7f3d0;">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full" style="background-color: #10b981; display: inline-block; width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0;"></span>
                            <span class="text-xs font-semibold text-emerald-800">Selesai</span>
                        </div>
                        <span class="inline-flex items-center px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 text-[10px] font-bold">
                            <?= number_format($penangananStats['pct_selesai'] ?? 0.0, 1) ?>%
                        </span>
                    </div>
                    <h3 class="text-xl font-bold text-emerald-700"><?= format_number($penangananStats['selesai_km'] ?? 0.0, 2) ?> <span class="text-xs font-normal text-emerald-600">km</span></h3>
                    <p class="text-[11px] font-medium text-emerald-600 mt-0.5">Tuntas Ditangani</p>
                </div>

                <!-- Total Anggaran -->
                <div class="p-4 rounded-xl border shadow-sm hover:shadow-md transition-all transform hover:-translate-y-0.5 bg-gradient-to-br from-blue-50 to-indigo-50 border-blue-200">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full inline-block bg-blue-600"></span>
                            <span class="text-xs font-semibold text-blue-900">Total Anggaran</span>
                        </div>
                        <span class="inline-flex items-center px-2 py-0.5 rounded bg-blue-100 text-blue-800 text-[10px] font-bold">
                            <?= (int)($penangananStats['total_paket'] ?? 0) ?> Paket
                        </span>
                    </div>
                    <h3 class="text-lg font-bold text-blue-900 truncate" title="Rp <?= format_number($penangananStats['total_anggaran'] ?? 0) ?>">
                        Rp <?= ($penangananStats['total_anggaran'] ?? 0) >= 1000000000 ? format_number(($penangananStats['total_anggaran'] ?? 0) / 1000000000, 2) . ' M' : (($penangananStats['total_anggaran'] ?? 0) >= 1000000 ? format_number(($penangananStats['total_anggaran'] ?? 0) / 1000000, 2) . ' Jt' : format_number($penangananStats['total_anggaran'] ?? 0)) ?>
                    </h3>
                    <p class="text-[11px] font-medium text-blue-700 mt-0.5">Alokasi Dana Penanganan</p>
                </div>
            </div>
            <?php endif; ?>

        </div>

        <!-- Right Panel: 2 Pie Charts (Takes 1 Column on LG screens) -->
        <div class="space-y-6">
            
            <!-- Pie Chart 1: Kondisi Jalan -->
            <div class="flex flex-col items-center justify-center rounded-2xl p-5 border min-h-[220px]" style="background-color: rgba(249, 250, 251, 0.6); border-color: #e5e7eb;">
                <h4 class="text-[13px] font-semibold text-gray-500 uppercase tracking-wider mb-4">Kondisi Jalan</h4>
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
                    <canvas id="kondisiPieChart"></canvas>
                </div>
                <!-- Legend -->
                <div class="flex flex-wrap justify-center gap-x-3 gap-y-1.5 mt-5">
                    <?php
                        $legendItems = [
                            ['label' => 'Baik',         'color' => '#10b981', 'pct' => $pctBaik,   'val' => $baikKm],
                            ['label' => 'Sedang',       'color' => '#facc15', 'pct' => $pctSedang, 'val' => $sedangKm],
                            ['label' => 'Rusak Ringan', 'color' => '#f97316', 'pct' => $pctRusakRingan, 'val' => $rusakRinganKm],
                            ['label' => 'Rusak Berat',  'color' => '#ef4444', 'pct' => $pctRusakBerat,  'val' => $rusakBeratKm],
                        ];
                    ?>
                    <?php foreach ($legendItems as $li): ?>
                        <?php if ($li['val'] > 0): ?>
                        <div data-dashboard-export-legend-item class="flex items-center gap-1.5" style="display: flex; align-items: center; gap: 6px; line-height: 16px;">
                            <span class="w-2.5 h-2.5 rounded-full" style="background-color: <?= $li['color'] ?>; display: inline-block; width: 10px; height: 10px; min-width: 10px; min-height: 10px; border-radius: 50%; flex-shrink: 0; vertical-align: middle; align-self: center;"></span>
                            <span class="text-[11px] font-medium text-gray-600" style="display: inline-block; vertical-align: middle;"><?= $li['label'] ?></span>
                            <span class="text-[10px] text-gray-400" style="display: inline-block; vertical-align: middle;"><?= number_format($li['pct'], 1) ?>%</span>
                        </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Pie Chart 2: Proporsi Kemantapan Jalan -->
            <div class="flex flex-col items-center justify-center rounded-2xl p-5 border min-h-[220px]" style="background-color: rgba(249, 250, 251, 0.6); border-color: #e5e7eb;">
                <h4 class="text-[13px] font-semibold text-gray-500 uppercase tracking-wider mb-4">Kemantapan Jalan</h4>
                <div class="pie-chart-container w-full max-w-[180px] aspect-square relative">
                    <canvas id="kemantapanPieChart"></canvas>
                </div>
                <!-- Legend -->
                <div class="flex flex-wrap justify-center gap-x-4 gap-y-1.5 mt-5">
                    <div data-dashboard-export-legend-item class="flex items-center gap-1.5" style="display: flex; align-items: center; gap: 6px; line-height: 16px;">
                        <span class="w-2.5 h-2.5 rounded-full" style="background-color: #10b981; display: inline-block; width: 10px; height: 10px; min-width: 10px; min-height: 10px; border-radius: 50%; flex-shrink: 0; vertical-align: middle; align-self: center;"></span>
                        <span class="text-[11px] font-medium text-gray-600" style="display: inline-block; vertical-align: middle;">Mantap</span>
                        <span class="text-[10px] text-gray-400" style="display: inline-block; vertical-align: middle;"><?= number_format($pctMantap, 1) ?>%</span>
                    </div>
                    <div data-dashboard-export-legend-item class="flex items-center gap-1.5" style="display: flex; align-items: center; gap: 6px; line-height: 16px;">
                        <span class="w-2.5 h-2.5 rounded-full" style="background-color: #ef4444; display: inline-block; width: 10px; height: 10px; min-width: 10px; min-height: 10px; border-radius: 50%; flex-shrink: 0; vertical-align: middle; align-self: center;"></span>
                        <span class="text-[11px] font-medium text-gray-600" style="display: inline-block; vertical-align: middle;">Tidak Mantap</span>
                        <span class="text-[10px] text-gray-400" style="display: inline-block; vertical-align: middle;"><?= number_format($pctTidakMantap, 1) ?>%</span>
                    </div>
                </div>
            </div>

            <!-- Pie Chart 3: Proporsi Penanganan Jalan -->
            <?php if (!empty($penangananStats) && $penangananStats['total_paket'] > 0): ?>
            <div class="flex flex-col items-center justify-center rounded-2xl p-5 border min-h-[220px]" style="background-color: rgba(249, 250, 251, 0.6); border-color: #e5e7eb;">
                <h4 class="text-[13px] font-semibold text-blue-700 uppercase tracking-wider mb-4">Penanganan Jalan</h4>
                <div class="pie-chart-container w-full max-w-[180px] aspect-square relative">
                    <canvas id="penangananGlobalPieChart"></canvas>
                </div>
                <!-- Legend -->
                <div class="flex flex-wrap justify-center gap-x-3 gap-y-1.5 mt-5">
                    <div class="flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full inline-block bg-sky-600"></span>
                        <span class="text-[11px] font-medium text-gray-600">Rencana</span>
                        <span class="text-[10px] text-gray-400"><?= number_format($penangananStats['pct_rencana'] ?? 0.0, 1) ?>%</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full inline-block bg-indigo-600"></span>
                        <span class="text-[11px] font-medium text-gray-600">Proses</span>
                        <span class="text-[10px] text-gray-400"><?= number_format($penangananStats['pct_proses'] ?? 0.0, 1) ?>%</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full inline-block bg-emerald-600"></span>
                        <span class="text-[11px] font-medium text-gray-600">Selesai</span>
                        <span class="text-[10px] text-gray-400"><?= number_format($penangananStats['pct_selesai'] ?? 0.0, 1) ?>%</span>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
        </div>
        
    </div>

    <!-- Recent Ruas Table & Filters -->
    <?php if (!empty($ruasList)): ?>
    
    <?php
    // Siapkan data ruas untuk AlpineJS
    $ruasJsonData = [];
    foreach ($ruasList as $ruas) {
        $ruasJsonData[] = [
            'id'             => (int)$ruas['id'],
            'kode_ruas'      => $ruas['kode_ruas'],
            'nama_ruas'      => $ruas['nama_ruas'],
            'sta_awal'       => (float)$ruas['sta_awal'],
            'sta_akhir'      => (float)$ruas['sta_akhir'],
            'sta_awal_str'   => meter_to_sta($ruas['sta_awal']),
            'sta_akhir_str'  => meter_to_sta($ruas['sta_akhir']),
            'panjang'        => (float)$ruas['panjang'],
            'koridor'        => $ruas['koridor'] ?? '',
            'kabupaten_kota' => $ruas['kabupaten_kota'] ?? '',
            'url_stripmap'   => base_url('stripmap/' . $ruas['id']),
            'url_edit'       => base_url('ruas/edit/' . $ruas['id']),
            'url_delete'     => base_url('ruas/delete/' . $ruas['id']),
        ];
    }
    ?>

    <?php if (!empty($kabupatenChartData)): ?>
    <!-- ============================================================ -->
    <!-- 3 Grid Section: Kemantapan per Kab/Kota, Koridor, & UPTD    -->
    <!-- ============================================================ -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- 1. KIRI: Kemantapan per Kabupaten/Kota -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden flex flex-col">
            <div class="px-4 py-3.5 border-b border-gray-100 flex items-center justify-between gap-2">
                <div>
                    <h3 class="text-sm font-bold text-gray-900">Kemantapan per Kab/Kota</h3>
                    <p class="text-[11px] text-gray-500 mt-0.5">Wilayah Kabupaten / Kota</p>
                </div>
                <div class="flex items-center gap-1.5" x-data="{ sortMode: 'desc', chartMode: 'pct' }">
                    <!-- Sort -->
                    <div class="relative flex items-center bg-gray-100 rounded-lg p-0.5" style="width: 64px;">
                        <span class="absolute top-0.5 bottom-0.5 w-[30px] rounded-md bg-white shadow-sm transition-all duration-200 ease-in-out"
                              :style="sortMode === 'asc' ? 'left: 2px;' : 'left: 32px;'"></span>
                        <button type="button" @click="sortMode = 'asc'; window.chartKab && window.chartKab.sort('asc')" title="Terendah ke Tertinggi"
                                class="relative z-10 flex-1 py-1 flex items-center justify-center rounded-md text-xs transition-colors duration-200"
                                :class="sortMode === 'asc' ? 'text-gray-900 font-bold' : 'text-gray-400'">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7"/></svg>
                        </button>
                        <button type="button" @click="sortMode = 'desc'; window.chartKab && window.chartKab.sort('desc')" title="Tertinggi ke Terendah"
                                class="relative z-10 flex-1 py-1 flex items-center justify-center rounded-md text-xs transition-colors duration-200"
                                :class="sortMode === 'desc' ? 'text-gray-900 font-bold' : 'text-gray-400'">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                    </div>
                    <!-- km / % -->
                    <div class="relative flex items-center bg-gray-100 rounded-lg p-0.5" style="width: 72px;">
                        <span class="absolute top-0.5 bottom-0.5 w-[34px] rounded-md bg-white shadow-sm transition-all duration-200 ease-in-out"
                              :style="chartMode === 'km' ? 'left: 2px;' : 'left: 36px;'"></span>
                        <button data-dashboard-export-center-text type="button" @click="chartMode = 'km'; window.chartKab && window.chartKab.setMode('km')"
                                class="relative z-10 flex-1 py-1 text-[11px] font-semibold rounded-md transition-colors duration-200"
                                :class="chartMode === 'km' ? 'text-gray-900' : 'text-gray-400'">km</button>
                        <button data-dashboard-export-center-text type="button" @click="chartMode = 'pct'; window.chartKab && window.chartKab.setMode('pct')"
                                class="relative z-10 flex-1 py-1 text-[11px] font-semibold rounded-md transition-colors duration-200"
                                :class="chartMode === 'pct' ? 'text-gray-900' : 'text-gray-400'">%</button>
                    </div>
                </div>
            </div>
            <div class="p-3.5 flex-1 flex flex-col overflow-y-auto min-h-[380px] max-h-[420px]">
                <div class="flex items-center gap-4 mb-2 text-[11px]">
                    <div data-dashboard-export-bar-legend-item class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-sm" style="background-color: #10b981;"></span><span class="font-medium text-gray-600">Mantap</span></div>
                    <div data-dashboard-export-bar-legend-item class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-sm" style="background-color: #ef4444;"></span><span class="font-medium text-gray-600">Tidak Mantap</span></div>
                </div>
                <div class="relative w-full" style="height: <?= max(320, count($kabupatenChartData) * 24) ?>px;">
                    <canvas id="kabupatenBarChart"></canvas>
                </div>
            </div>
        </div>

        <!-- 2. TENGAH: Kemantapan per Koridor -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden flex flex-col">
            <div class="px-4 py-3.5 border-b border-gray-100 flex items-center justify-between gap-2">
                <div>
                    <h3 class="text-sm font-bold text-gray-900">Kemantapan per Koridor</h3>
                    <p class="text-[11px] text-gray-500 mt-0.5">Rute Koridor Utama</p>
                </div>
                <div class="flex items-center gap-1.5" x-data="{ sortMode: 'desc', chartMode: 'pct' }">
                    <!-- Sort -->
                    <div class="relative flex items-center bg-gray-100 rounded-lg p-0.5" style="width: 64px;">
                        <span class="absolute top-0.5 bottom-0.5 w-[30px] rounded-md bg-white shadow-sm transition-all duration-200 ease-in-out"
                              :style="sortMode === 'asc' ? 'left: 2px;' : 'left: 32px;'"></span>
                        <button type="button" @click="sortMode = 'asc'; window.chartKoridor && window.chartKoridor.sort('asc')" title="Terendah ke Tertinggi"
                                class="relative z-10 flex-1 py-1 flex items-center justify-center rounded-md text-xs transition-colors duration-200"
                                :class="sortMode === 'asc' ? 'text-gray-900 font-bold' : 'text-gray-400'">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7"/></svg>
                        </button>
                        <button type="button" @click="sortMode = 'desc'; window.chartKoridor && window.chartKoridor.sort('desc')" title="Tertinggi ke Terendah"
                                class="relative z-10 flex-1 py-1 flex items-center justify-center rounded-md text-xs transition-colors duration-200"
                                :class="sortMode === 'desc' ? 'text-gray-900 font-bold' : 'text-gray-400'">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                    </div>
                    <!-- km / % -->
                    <div class="relative flex items-center bg-gray-100 rounded-lg p-0.5" style="width: 72px;">
                        <span class="absolute top-0.5 bottom-0.5 w-[34px] rounded-md bg-white shadow-sm transition-all duration-200 ease-in-out"
                              :style="chartMode === 'km' ? 'left: 2px;' : 'left: 36px;'"></span>
                        <button data-dashboard-export-center-text type="button" @click="chartMode = 'km'; window.chartKoridor && window.chartKoridor.setMode('km')"
                                class="relative z-10 flex-1 py-1 text-[11px] font-semibold rounded-md transition-colors duration-200"
                                :class="chartMode === 'km' ? 'text-gray-900' : 'text-gray-400'">km</button>
                        <button data-dashboard-export-center-text type="button" @click="chartMode = 'pct'; window.chartKoridor && window.chartKoridor.setMode('pct')"
                                class="relative z-10 flex-1 py-1 text-[11px] font-semibold rounded-md transition-colors duration-200"
                                :class="chartMode === 'pct' ? 'text-gray-900' : 'text-gray-400'">%</button>
                    </div>
                </div>
            </div>
            <div class="p-3.5 flex-1 flex flex-col overflow-y-auto min-h-[380px] max-h-[420px]">
                <div class="flex items-center gap-4 mb-2 text-[11px]">
                    <div data-dashboard-export-bar-legend-item class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-sm" style="background-color: #10b981;"></span><span class="font-medium text-gray-600">Mantap</span></div>
                    <div data-dashboard-export-bar-legend-item class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-sm" style="background-color: #ef4444;"></span><span class="font-medium text-gray-600">Tidak Mantap</span></div>
                </div>
                <div class="relative w-full" style="height: <?= max(320, count($koridorChartData ?? []) * 24) ?>px;">
                    <canvas id="koridorBarChart"></canvas>
                </div>
            </div>
        </div>

        <!-- 3. KANAN: Kemantapan per UPTD -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden flex flex-col">
            <div class="px-4 py-3.5 border-b border-gray-100 flex items-center justify-between gap-2">
                <div>
                    <h3 class="text-sm font-bold text-gray-900">Kemantapan per UPTD</h3>
                    <p class="text-[11px] text-gray-500 mt-0.5">Wilayah UPTD Dinas BMBK</p>
                </div>
                <div class="flex items-center gap-1.5" x-data="{ sortMode: 'desc', chartMode: 'pct' }">
                    <!-- Sort -->
                    <div class="relative flex items-center bg-gray-100 rounded-lg p-0.5" style="width: 64px;">
                        <span class="absolute top-0.5 bottom-0.5 w-[30px] rounded-md bg-white shadow-sm transition-all duration-200 ease-in-out"
                              :style="sortMode === 'asc' ? 'left: 2px;' : 'left: 32px;'"></span>
                        <button type="button" @click="sortMode = 'asc'; window.chartUptd && window.chartUptd.sort('asc')" title="Terendah ke Tertinggi"
                                class="relative z-10 flex-1 py-1 flex items-center justify-center rounded-md text-xs transition-colors duration-200"
                                :class="sortMode === 'asc' ? 'text-gray-900 font-bold' : 'text-gray-400'">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7"/></svg>
                        </button>
                        <button type="button" @click="sortMode = 'desc'; window.chartUptd && window.chartUptd.sort('desc')" title="Tertinggi ke Terendah"
                                class="relative z-10 flex-1 py-1 flex items-center justify-center rounded-md text-xs transition-colors duration-200"
                                :class="sortMode === 'desc' ? 'text-gray-900 font-bold' : 'text-gray-400'">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                    </div>
                    <!-- km / % -->
                    <div class="relative flex items-center bg-gray-100 rounded-lg p-0.5" style="width: 72px;">
                        <span class="absolute top-0.5 bottom-0.5 w-[34px] rounded-md bg-white shadow-sm transition-all duration-200 ease-in-out"
                              :style="chartMode === 'km' ? 'left: 2px;' : 'left: 36px;'"></span>
                        <button data-dashboard-export-center-text type="button" @click="chartMode = 'km'; window.chartUptd && window.chartUptd.setMode('km')"
                                class="relative z-10 flex-1 py-1 text-[11px] font-semibold rounded-md transition-colors duration-200"
                                :class="chartMode === 'km' ? 'text-gray-900' : 'text-gray-400'">km</button>
                        <button data-dashboard-export-center-text type="button" @click="chartMode = 'pct'; window.chartUptd && window.chartUptd.setMode('pct')"
                                class="relative z-10 flex-1 py-1 text-[11px] font-semibold rounded-md transition-colors duration-200"
                                :class="chartMode === 'pct' ? 'text-gray-900' : 'text-gray-400'">%</button>
                    </div>
                </div>
            </div>
            <div class="p-3.5 flex-1 flex flex-col overflow-y-auto min-h-[380px] max-h-[420px]">
                <div class="flex items-center gap-4 mb-2 text-[11px]">
                    <div data-dashboard-export-bar-legend-item class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-sm" style="background-color: #10b981;"></span><span class="font-medium text-gray-600">Mantap</span></div>
                    <div data-dashboard-export-bar-legend-item class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-sm" style="background-color: #ef4444;"></span><span class="font-medium text-gray-600">Tidak Mantap</span></div>
                </div>
                <div class="relative w-full" style="height: <?= max(320, count($uptdChartData ?? []) * 24) ?>px;">
                    <canvas id="uptdBarChart"></canvas>
                </div>
            </div>
        </div>

    </div>

    <script>
    // ================================================================
    // Modular Builder untuk Stacked Bar Charts (Kab, Koridor, UPTD)
    // ================================================================
    function initStackedBarChart(canvasId, rawData, isVertical = false) {
        if (!rawData || !rawData.length) return null;

        let activeRaw       = [...rawData];
        let activeLabels    = activeRaw.map(d => d.short_label || d.label);
        let activeMantapKm  = activeRaw.map(d => d.mantap_km);
        let activeTidakKm   = activeRaw.map(d => d.tidak_mantap_km);
        let activeMantapPct = activeRaw.map(d => d.pct_mantap);
        let activeTidakPct  = activeRaw.map(d => d.pct_tidak_mantap);

        let currentMode = 'pct';
        let currentDir  = 'desc';
        const fmt = v => new Intl.NumberFormat('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(v);

        const canvas = document.getElementById(canvasId);
        if (!canvas) return null;

        const ctx = canvas.getContext('2d');
        const chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: activeLabels,
                datasets: [
                    {
                        label: 'Mantap',
                        data: activeMantapPct,
                        backgroundColor: '#16ad7aff',
                        hoverBackgroundColor: '#059669',
                        borderRadius: 3,
                        borderSkipped: false,
                        barPercentage: isVertical ? 0.7 : 0.85,
                        categoryPercentage: isVertical ? 0.8 : 0.92,
                    },
                    {
                        label: 'Tidak Mantap',
                        data: activeTidakPct,
                        backgroundColor: '#ff5151ff',
                        hoverBackgroundColor: '#dc2626',
                        borderRadius: 3,
                        borderSkipped: false,
                        barPercentage: isVertical ? 0.7 : 0.85,
                        categoryPercentage: isVertical ? 0.8 : 0.92,
                    }
                ]
            },
            options: {
                indexAxis: isVertical ? 'x' : 'y',
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', axis: isVertical ? 'x' : 'y', intersect: false },
                animation: { duration: 600, easing: 'easeOutQuart' },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(17,24,39,0.95)',
                        titleFont: { family: 'Inter, system-ui, sans-serif', size: 12, weight: '700' },
                        bodyFont:  { family: 'Inter, system-ui, sans-serif', size: 11 },
                        padding:   { top: 8, bottom: 8, left: 12, right: 12 },
                        cornerRadius: 8,
                        displayColors: true,
                        boxWidth: 8, boxHeight: 8, boxPadding: 4,
                        callbacks: {
                            title: function(items) {
                                const d = activeRaw[items[0].dataIndex];
                                return d ? d.label : items[0].label;
                            },
                            label: function(ctx) {
                                const d = activeRaw[ctx.dataIndex];
                                if (!d) return '';
                                if (currentMode === 'pct') {
                                    if (ctx.dataset.label === 'Mantap')
                                        return ` Mantap: ${d.pct_mantap}% (${fmt(d.mantap_km)} km)`;
                                    return ` Tidak Mantap: ${d.pct_tidak_mantap}% (${fmt(d.tidak_mantap_km)} km)`;
                                } else {
                                    if (ctx.dataset.label === 'Mantap')
                                        return ` Mantap: ${fmt(d.mantap_km)} km (${d.pct_mantap}%)`;
                                    return ` Tidak Mantap: ${fmt(d.tidak_mantap_km)} km (${d.pct_tidak_mantap}%)`;
                                }
                            }
                        }
                    }
                },
                scales: {
                    x: isVertical ? {
                        stacked: true,
                        grid: { display: false },
                        border: { display: false },
                        ticks: {
                            autoSkip: false,
                            maxRotation: 45,
                            minRotation: 0,
                            font: { size: 9, family: 'Inter, system-ui, sans-serif', weight: '600' },
                            color: '#374151',
                        }
                    } : {
                        stacked: true,
                        beginAtZero: true,
                        grid: { color: 'rgba(243,244,246,1)', lineWidth: 1 },
                        border: { display: false },
                        ticks: {
                            font: { size: 10, family: 'Inter, system-ui, sans-serif' },
                            color: '#9ca3af',
                            callback: function(v) { return currentMode === 'pct' ? v + '%' : v + ' km'; }
                        }
                    },
                    y: isVertical ? {
                        stacked: true,
                        beginAtZero: true,
                        grid: { color: 'rgba(243,244,246,1)', lineWidth: 1 },
                        border: { display: false },
                        ticks: {
                            font: { size: 10, family: 'Inter, system-ui, sans-serif' },
                            color: '#9ca3af',
                            callback: function(v) { return currentMode === 'pct' ? v + '%' : v + ' km'; }
                        }
                    } : {
                        stacked: true,
                        grid: { display: false },
                        border: { display: false },
                        ticks: {
                            autoSkip: false,
                            font: { size: 10, family: 'Inter, system-ui, sans-serif', weight: '600' },
                            color: '#374151',
                        }
                    }
                }
            }
        });

        function applyData() {
            chart.data.labels = activeLabels;
            if (currentMode === 'pct') {
                chart.data.datasets[0].data = activeMantapPct;
                chart.data.datasets[1].data = activeTidakPct;
                if (isVertical) {
                    chart.options.scales.y.ticks.callback = v => v + '%';
                } else {
                    chart.options.scales.x.ticks.callback = v => v + '%';
                }
            } else {
                chart.data.datasets[0].data = activeMantapKm;
                chart.data.datasets[1].data = activeTidakKm;
                if (isVertical) {
                    chart.options.scales.y.ticks.callback = v => v + ' km';
                } else {
                    chart.options.scales.x.ticks.callback = v => v + ' km';
                }
            }
            chart.update();
        }

        function doSort(dir) {
            currentDir = dir;
            const sortKey = currentMode === 'km' ? 'mantap_km' : 'pct_mantap';
            const sorted = [...rawData].sort((a, b) =>
                dir === 'asc'
                    ? a[sortKey] - b[sortKey]
                    : b[sortKey] - a[sortKey]
            );
            activeRaw       = sorted;
            activeLabels    = sorted.map(d => d.short_label || d.label);
            activeMantapKm  = sorted.map(d => d.mantap_km);
            activeTidakKm   = sorted.map(d => d.tidak_mantap_km);
            activeMantapPct = sorted.map(d => d.pct_mantap);
            activeTidakPct  = sorted.map(d => d.pct_tidak_mantap);
            applyData();
        }

        return {
            sort: function(dir) {
                doSort(dir);
            },
            setMode: function(mode) {
                currentMode = mode;
                doSort(currentDir); // auto re-sort sesuai mode baru
            }
        };
    }

    (function() {
        const rawKab = <?= json_encode($kabupatenChartData ?? [], JSON_UNESCAPED_UNICODE) ?>;
        const rawKor = <?= json_encode($koridorChartData ?? [], JSON_UNESCAPED_UNICODE) ?>;
        const rawUpt = <?= json_encode($uptdChartData ?? [], JSON_UNESCAPED_UNICODE) ?>;

        window.chartKab     = initStackedBarChart('kabupatenBarChart', rawKab, false);
        window.chartKoridor = initStackedBarChart('koridorBarChart', rawKor, false);
        window.chartUptd    = initStackedBarChart('uptdBarChart', rawUpt, false);

        if (window.chartKab) window.chartKab.sort('desc');
        if (window.chartKoridor) window.chartKoridor.sort('desc');
        if (window.chartUptd) window.chartUptd.sort('desc');
    })();
    </script>
    <?php endif; ?>

    <div data-dashboard-export-ignore
         x-data="dashboardRuasTable(<?= htmlspecialchars(json_encode($ruasJsonData), ENT_QUOTES, 'UTF-8') ?>)"
         class="space-y-6">

        
        <!-- Filters & Search Panel -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden no-export" x-data="{ isSearchOpen: false }">
            <div class="px-5 py-4 border-b border-gray-200 bg-gray-50/70 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 cursor-pointer select-none" @click="isSearchOpen = !isSearchOpen">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 whitespace-nowrap">Daftar Ruas Jalan</h2>
                    <p class="text-xs text-gray-500">Filter, cari, dan lihat data ruas jalan secara langsung.</p>
                </div>
                <button type="button"
                        class="inline-flex items-center justify-center p-2 rounded-xl border border-gray-200 bg-white text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition-all shadow-sm focus:outline-none"
                        :title="isSearchOpen ? 'Sembunyikan' : 'Tampilkan'">
                    <svg class="w-5 h-5 transition-transform duration-200" :class="isSearchOpen ? 'rotate-90' : 'rotate-0'" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            </div>
            
            <div x-show="isSearchOpen" x-collapse>
                <div class="p-5 border-b border-gray-200">
                    <div class="flex flex-col md:flex-row gap-4 items-stretch md:items-end">
                    
                    <!-- Pencarian -->
                    <div class="flex-1 min-w-0">
                        <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Pencarian</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </span>
                            <input type="text" 
                                   x-model="searchQuery" 
                                   placeholder="Cari nama atau kode ruas..." 
                                   class="w-full pl-9 pr-4 py-2 text-sm rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        </div>
                    </div>

                    <!-- Filter Koridor -->
                    <div class="w-full md:w-56">
                        <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Filter Koridor</label>
                        <select x-model="selectedKoridor" 
                                class="w-full px-3 py-2 text-sm rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors bg-white">
                            <option value="">Semua Koridor</option>
                            <template x-for="koridor in getUniqueKoridor()" :key="koridor">
                                <option :value="koridor" x-text="koridor"></option>
                            </template>
                        </select>
                    </div>

                    <!-- Filter Kabupaten / Kota -->
                    <div class="w-full md:w-56">
                        <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Filter Wilayah</label>
                        <select x-model="selectedKabupaten" 
                                class="w-full px-3 py-2 text-sm rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors bg-white">
                            <option value="">Semua Kabupaten/Kota</option>
                            <template x-for="kab in getUniqueKabupaten()" :key="kab">
                                <option :value="kab" x-text="kab"></option>
                            </template>
                        </select>
                    </div>

                    <!-- Reset Button -->
                    <div class="w-full md:w-auto">
                        <button type="button" 
                                @click="resetFilters()" 
                                title="Reset Filter"
                                class="w-full md:w-auto inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl border border-gray-200 bg-gray-50 text-gray-500 hover:bg-gray-100 hover:text-gray-700 transition-colors text-sm font-medium">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                            </svg>
                            Reset
                        </button>
                    </div>

                    </div>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200 text-xs font-bold text-gray-500 uppercase tracking-wider">
                                <th class="px-4 py-3.5 w-12 text-center">No</th>
                                <th class="px-5 py-3.5">
                                    <button type="button" @click="sortByCol('nama_ruas')" class="flex items-center gap-1.5 hover:text-gray-900 focus:outline-none">
                                        Ruas Jalan
                                        <template x-if="sortBy === 'nama_ruas' || sortBy === 'kode_ruas'">
                                            <svg x-show="sortOrder === 'asc'" class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7l4-4m0 0l4 4m-4-4v18"/></svg>
                                            <svg x-show="sortOrder === 'desc'" class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 17l-4 4m0 0l-4-4m4 4V3"/></svg>
                                        </template>
                                    </button>
                                </th>
                                <th class="px-5 py-3.5 hidden md:table-cell">
                                    <button type="button" @click="sortByCol('kabupaten_kota')" class="flex items-center gap-1.5 hover:text-gray-900 focus:outline-none">
                                        Lokasi & Koridor
                                    </button>
                                </th>
                                <th class="px-5 py-3.5 text-center">
                                    <button type="button" @click="sortByCol('panjang')" class="mx-auto flex items-center gap-1.5 hover:text-gray-900 focus:outline-none">
                                        Segmen (STA & Panjang)
                                    </button>
                                </th>
                                <th class="px-5 py-3.5 text-right w-48">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            <template x-for="(ruas, index) in paginatedRuas()" :key="ruas.id">
                                <tr class="hover:bg-blue-50/30 transition-colors">
                                    <!-- No -->
                                    <td class="px-4 py-3.5 text-xs font-semibold text-gray-400 text-center" x-text="(currentPage - 1) * perPage + index + 1"></td>
                                    
                                    <!-- Ruas Jalan (Kode + Nama stacked) -->
                                    <td class="px-5 py-3.5">
                                        <div class="flex flex-col gap-1">
                                            <div class="flex items-center gap-2">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded bg-blue-50 text-blue-700 text-[11px] font-bold border border-blue-100" x-text="ruas.kode_ruas"></span>
                                                <span class="text-sm font-bold text-gray-900" x-text="ruas.nama_ruas"></span>
                                            </div>
                                            <!-- Mobile only location subtitle -->
                                            <div class="text-xs text-gray-500 md:hidden flex items-center gap-1">
                                                <span x-text="ruas.kabupaten_kota || '-'"></span>
                                                <span>•</span>
                                                <span x-text="ruas.koridor || '-'"></span>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Lokasi & Koridor (Desktop) -->
                                    <td class="px-5 py-3.5 hidden md:table-cell">
                                        <div class="flex flex-col">
                                            <span class="text-sm font-medium text-gray-800" x-text="ruas.kabupaten_kota || '-'"></span>
                                            <span class="text-xs font-medium text-gray-400" x-text="ruas.koridor ? 'Koridor: ' + ruas.koridor : '-'"></span>
                                        </div>
                                    </td>

                                    <!-- STA & Panjang -->
                                    <td class="px-5 py-3.5 text-center">
                                        <div class="flex flex-col items-center gap-0.5">
                                            <span class="text-xs font-mono font-semibold text-gray-700 bg-gray-100 px-2 py-0.5 rounded" x-text="ruas.sta_awal_str + ' s/d ' + ruas.sta_akhir_str"></span>
                                            <span class="text-[11px] font-bold text-emerald-700" x-text="formatNumber(ruas.panjang) + ' m (' + formatNumber(ruas.panjang / 1000) + ' km)'"></span>
                                        </div>
                                    </td>

                                    <!-- Action Buttons -->
                                    <td class="px-5 py-3.5 text-right whitespace-nowrap">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <!-- Primary Action: Visual Stripmap -->
                                            <a :href="ruas.url_stripmap"
                                               class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 text-white text-xs font-semibold rounded-lg hover:bg-blue-700 transition-colors shadow-sm"
                                               title="Buka Visualisasi Strip Map">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2z"/>
                                                </svg>
                                                <span>Strip Map</span>
                                            </a>

                                            <!-- Edit Icon Button -->
                                            <a :href="ruas.url_edit"
                                               class="p-1.5 text-amber-700 bg-amber-50 hover:bg-amber-100 border border-amber-200/60 rounded-lg transition-colors"
                                               title="Edit Ruas Jalan">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </a>

                                            <!-- Delete Icon Button -->
                                            <a :href="ruas.url_delete"
                                               @click="confirmDelete($event, ruas.url_delete, 'Yakin ingin menghapus ruas ini? Semua data strip map terkait juga akan dihapus.')"
                                               class="p-1.5 text-red-700 bg-red-50 hover:bg-red-100 border border-red-200/60 rounded-lg transition-colors"
                                               title="Hapus Ruas Jalan">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                            <!-- Bila Filter Tidak Menemukan Apapun -->
                            <tr x-show="filteredRuas().length === 0">
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center text-gray-400">
                                        <svg class="w-12 h-12 mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                                        </svg>
                                        <p class="text-sm font-medium">Tidak ada data ruas jalan yang cocok dengan pencarian/filter.</p>
                                        <button type="button" @click="resetFilters()" class="mt-2 text-xs font-semibold text-blue-600 hover:text-blue-800 hover:underline">Reset Filter & Pencarian</button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Controls -->
                <div x-show="totalPages() > 1" 
                     class="bg-white border-t border-gray-200 px-5 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 no-export">
                    <!-- Info Text -->
                    <div class="text-xs text-gray-500 font-medium">
                        Menampilkan <span class="font-bold text-gray-900" x-text="(currentPage - 1) * perPage + 1"></span> sampai 
                        <span class="font-bold text-gray-900" x-text="Math.min(currentPage * perPage, filteredRuas().length)"></span> dari 
                        <span class="font-bold text-gray-900" x-text="filteredRuas().length"></span> ruas jalan
                    </div>
                    
                    <!-- Page buttons -->
                    <div class="flex items-center gap-1.5 self-center sm:self-auto">
                        <!-- Previous Button -->
                        <button type="button" 
                                @click="currentPage > 1 ? currentPage-- : null"
                                :disabled="currentPage === 1"
                                :class="currentPage === 1 ? 'opacity-40 cursor-not-allowed' : 'hover:bg-gray-50 hover:text-gray-900'"
                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-gray-200 text-gray-500 bg-white transition-colors text-sm font-semibold">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </button>

                        <!-- Page numbers -->
                        <template x-for="p in getPagesToShow()" :key="p">
                            <div class="flex items-center">
                                <template x-if="p === '...'">
                                    <span class="px-2 text-gray-400 text-xs font-semibold">...</span>
                                </template>
                                <template x-if="p !== '...'">
                                    <button type="button"
                                            @click="currentPage = p"
                                            :class="currentPage === p ? 'bg-blue-600 border-blue-600 text-white shadow-sm' : 'border-gray-200 text-gray-600 hover:bg-gray-50 hover:text-gray-900 bg-white'"
                                            class="inline-flex items-center justify-center min-w-8 h-8 px-2.5 rounded-lg border text-xs font-semibold transition-colors"
                                            x-text="p">
                                    </button>
                                </template>
                            </div>
                        </template>

                        <!-- Next Button -->
                        <button type="button" 
                                @click="currentPage < totalPages() ? currentPage++ : null"
                                :disabled="currentPage === totalPages()"
                                :class="currentPage === totalPages() ? 'opacity-40 cursor-not-allowed' : 'hover:bg-gray-50 hover:text-gray-900'"
                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-gray-200 text-gray-500 bg-white transition-colors text-sm font-semibold">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script>
    function dashboardRuasTable(initialRuasList) {
        return {
            ruasList: initialRuasList,
            currentPage: 1,
            perPage: 10,
            searchQuery: '',
            selectedKoridor: '',
            selectedKabupaten: '',
            sortBy: 'kode_ruas',
            sortOrder: 'asc',

            init() {
                this.$watch('searchQuery', () => this.currentPage = 1);
                this.$watch('selectedKoridor', () => this.currentPage = 1);
                this.$watch('selectedKabupaten', () => this.currentPage = 1);
            },

            totalPages() {
                return Math.ceil(this.filteredRuas().length / this.perPage);
            },

            paginatedRuas() {
                const start = (this.currentPage - 1) * this.perPage;
                const end = start + this.perPage;
                return this.filteredRuas().slice(start, end);
            },

            getPagesToShow() {
                const total = this.totalPages();
                const current = this.currentPage;
                if (total <= 5) {
                    return Array.from({ length: total }, (_, i) => i + 1);
                }
                const pages = [];
                pages.push(1);
                
                let start = Math.max(2, current - 1);
                let end = Math.min(total - 1, current + 1);
                
                if (current <= 2) {
                    end = 3;
                } else if (current >= total - 1) {
                    start = total - 2;
                }
                
                if (start > 2) {
                    pages.push('...');
                }
                
                for (let i = start; i <= end; i++) {
                    pages.push(i);
                }
                
                if (end < total - 1) {
                    pages.push('...');
                }
                
                pages.push(total);
                return pages;
            },

            getUniqueKoridor() {
                const list = this.ruasList.map(r => r.koridor).filter(val => val !== null && val !== '');
                return [...new Set(list)].sort();
            },

            getUniqueKabupaten() {
                const list = this.ruasList.map(r => r.kabupaten_kota).filter(val => val !== null && val !== '');
                return [...new Set(list)].sort();
            },

            sortByCol(col) {
                if (this.sortBy === col) {
                    this.sortOrder = this.sortOrder === 'asc' ? 'desc' : 'asc';
                } else {
                    this.sortBy = col;
                    this.sortOrder = 'asc';
                }
            },

            resetFilters() {
                this.searchQuery = '';
                this.selectedKoridor = '';
                this.selectedKabupaten = '';
                this.sortBy = 'kode_ruas';
                this.sortOrder = 'asc';
                this.currentPage = 1;
            },

            formatNumber(num) {
                return new Intl.NumberFormat('id-ID', { maximumFractionDigits: 2 }).format(num);
            },

            filteredRuas() {
                let result = [...this.ruasList];

                // 1. Search Query
                if (this.searchQuery.trim() !== '') {
                    const query = this.searchQuery.toLowerCase().trim();
                    result = result.filter(r => 
                        r.nama_ruas.toLowerCase().includes(query) || 
                        r.kode_ruas.toLowerCase().includes(query)
                    );
                }

                // 2. Filter Koridor
                if (this.selectedKoridor !== '') {
                    result = result.filter(r => r.koridor === this.selectedKoridor);
                }

                // 3. Filter Kabupaten/Kota
                if (this.selectedKabupaten !== '') {
                    result = result.filter(r => r.kabupaten_kota === this.selectedKabupaten);
                }

                // 4. Sorting
                result.sort((a, b) => {
                    let valA = a[this.sortBy] ? a[this.sortBy].toString().toLowerCase() : '';
                    let valB = b[this.sortBy] ? b[this.sortBy].toString().toLowerCase() : '';

                    if (this.sortBy === 'panjang' || this.sortBy === 'sta_awal' || this.sortBy === 'sta_akhir') {
                        valA = parseFloat(a[this.sortBy]) || 0;
                        valB = parseFloat(b[this.sortBy]) || 0;
                    }

                    if (valA < valB) return this.sortOrder === 'asc' ? -1 : 1;
                    if (valA > valB) return this.sortOrder === 'asc' ? 1 : -1;
                    return 0;
                });

                return result;
            }
        };
    }
    </script>
    <?php else: ?>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
        </svg>
        <h3 class="text-lg font-semibold text-gray-600 mb-2">Belum ada data ruas jalan</h3>
        <p class="text-sm text-gray-500 mb-6">Mulai dengan menambahkan ruas jalan pertama Anda.</p>
        <a href="<?= base_url('ruas/create') ?>"
           class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-xl hover:bg-blue-700 transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Ruas Jalan
        </a>
    </div>
    <?php endif; ?>
    </div> <!-- End of capture-area -->

    <!-- Inisialisasi Chart.js untuk Pie Charts -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // --------------------------------------------------------
        // Chart 1: Proporsi Kondisi Jalan (Baik, Sedang, RR, RB)
        // --------------------------------------------------------
        const ctx1 = document.getElementById('kondisiPieChart').getContext('2d');

        const chartColors1 = ['#10b981', '#facc15', '#f97316', '#ef4444'];
        const chartLabels1 = ['Baik', 'Sedang', 'Rusak Ringan', 'Rusak Berat'];
        const chartData1   = [
            <?= (float)$baikKm ?>,
            <?= (float)$sedangKm ?>,
            <?= (float)$rusakRinganKm ?>,
            <?= (float)$rusakBeratKm ?>
        ];

        // Filter out zero values
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
                    hoverBorderWidth: 3,
                    hoverBorderColor: '#ffffff',
                    hoverOffset: 12
                }]
            },
            plugins: [{
                id: 'modernLabels1',
                afterDraw: (chart) => {
                    const ctxDraw = chart.ctx;
                    const dataset = chart.data.datasets[0];
                    const meta = chart.getDatasetMeta(0);
                    const total = dataset.data.reduce((a, b) => a + b, 0);
                    if (total <= 0) return;

                    meta.data.forEach((element, index) => {
                        const dataVal = dataset.data[index];
                        if (dataVal <= 0) return;

                        const pct = ((dataVal / total) * 100).toFixed(1);
                        const midAngle = element.startAngle + (element.endAngle - element.startAngle) / 2;

                        const innerPt = {
                            x: element.x + Math.cos(midAngle) * (element.outerRadius + 4),
                            y: element.y + Math.sin(midAngle) * (element.outerRadius + 4)
                        };
                        const outerPt = {
                            x: element.x + Math.cos(midAngle) * (element.outerRadius + 16),
                            y: element.y + Math.sin(midAngle) * (element.outerRadius + 16)
                        };

                        ctxDraw.save();
                        ctxDraw.strokeStyle = 'rgba(156,163,175,0.4)';
                        ctxDraw.lineWidth = 0.8;
                        ctxDraw.beginPath();
                        ctxDraw.moveTo(innerPt.x, innerPt.y);
                        ctxDraw.lineTo(outerPt.x, outerPt.y);
                        ctxDraw.stroke();

                        const labelPt = {
                            x: element.x + Math.cos(midAngle) * (element.outerRadius + 24),
                            y: element.y + Math.sin(midAngle) * (element.outerRadius + 24)
                        };

                        ctxDraw.fillStyle = '#374151';
                        ctxDraw.font = '600 10px Inter, system-ui, sans-serif';
                        ctxDraw.textAlign = 'center';
                        ctxDraw.textBaseline = 'middle';
                        ctxDraw.fillText(pct + '%', labelPt.x, labelPt.y);

                        ctxDraw.restore();
                    });
                }
            }],
            options: {
                layout: { padding: 40 },
                responsive: true,
                maintainAspectRatio: true,
                animation: {
                    duration: 1000,
                    easing: 'easeOutQuart'
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(31,41,55,0.95)',
                        titleFont: { family: 'Inter, system-ui, sans-serif', size: 12, weight: '600' },
                        bodyFont: { family: 'Inter, system-ui, sans-serif', size: 11 },
                        padding: { top: 10, bottom: 10, left: 14, right: 14 },
                        cornerRadius: 10,
                        displayColors: true,
                        boxWidth: 10,
                        boxHeight: 10,
                        boxPadding: 4,
                        callbacks: {
                            label: function(context) {
                                const value = context.raw;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                return ` ${context.label}: ${new Intl.NumberFormat('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(value)} km (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });

        // --------------------------------------------------------
        // Chart 2: Kemantapan Jalan (Mantap vs Tidak Mantap)
        // --------------------------------------------------------
        const ctx2 = document.getElementById('kemantapanPieChart').getContext('2d');
        const chartData2 = [
            <?= (float)$mantapKm ?>,
            <?= (float)$tidakMantapKm ?>
        ];
        const chartLabels2 = ['Mantap', 'Tidak Mantap'];
        const chartColors2 = ['#10b981', '#ef4444']; // Hijau & Merah

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
                    hoverBorderWidth: 3,
                    hoverBorderColor: '#ffffff',
                    hoverOffset: 12
                }]
            },
            plugins: [{
                id: 'modernLabels2',
                afterDraw: (chart) => {
                    const ctxDraw = chart.ctx;
                    const dataset = chart.data.datasets[0];
                    const meta = chart.getDatasetMeta(0);
                    const total = dataset.data.reduce((a, b) => a + b, 0);
                    if (total <= 0) return;

                    meta.data.forEach((element, index) => {
                        const dataVal = dataset.data[index];
                        if (dataVal <= 0) return;

                        const pct = ((dataVal / total) * 100).toFixed(1);
                        const midAngle = element.startAngle + (element.endAngle - element.startAngle) / 2;

                        const innerPt = {
                            x: element.x + Math.cos(midAngle) * (element.outerRadius + 4),
                            y: element.y + Math.sin(midAngle) * (element.outerRadius + 4)
                        };
                        const outerPt = {
                            x: element.x + Math.cos(midAngle) * (element.outerRadius + 16),
                            y: element.y + Math.sin(midAngle) * (element.outerRadius + 16)
                        };

                        ctxDraw.save();
                        ctxDraw.strokeStyle = 'rgba(156,163,175,0.4)';
                        ctxDraw.lineWidth = 0.8;
                        ctxDraw.beginPath();
                        ctxDraw.moveTo(innerPt.x, innerPt.y);
                        ctxDraw.lineTo(outerPt.x, outerPt.y);
                        ctxDraw.stroke();

                        const labelPt = {
                            x: element.x + Math.cos(midAngle) * (element.outerRadius + 24),
                            y: element.y + Math.sin(midAngle) * (element.outerRadius + 24)
                        };

                        ctxDraw.fillStyle = '#374151';
                        ctxDraw.font = '600 10px Inter, system-ui, sans-serif';
                        ctxDraw.textAlign = 'center';
                        ctxDraw.textBaseline = 'middle';
                        ctxDraw.fillText(pct + '%', labelPt.x, labelPt.y);

                        ctxDraw.restore();
                    });
                }
            }],
            options: {
                layout: { padding: 40 },
                responsive: true,
                maintainAspectRatio: true,
                animation: {
                    duration: 1000,
                    easing: 'easeOutQuart'
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(31,41,55,0.95)',
                        titleFont: { family: 'Inter, system-ui, sans-serif', size: 12, weight: '600' },
                        bodyFont: { family: 'Inter, system-ui, sans-serif', size: 11 },
                        padding: { top: 10, bottom: 10, left: 14, right: 14 },
                        cornerRadius: 10,
                        displayColors: true,
                        boxWidth: 10,
                        boxHeight: 10,
                        boxPadding: 4,
                        callbacks: {
                            label: function(context) {
                                const value = context.raw;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                return ` ${context.label}: ${new Intl.NumberFormat('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(value)} km (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });

        // --------------------------------------------------------
        // Chart 3: Proporsi Penanganan Jalan (Rencana, Proses, Selesai)
        // --------------------------------------------------------
        const canvas3 = document.getElementById('penangananGlobalPieChart');
        if (canvas3) {
            const ctx3 = canvas3.getContext('2d');
            const chartData3 = [
                <?= (float)($penangananStats['rencana_km'] ?? 0.0) ?>,
                <?= (float)($penangananStats['proses_km'] ?? 0.0) ?>,
                <?= (float)($penangananStats['selesai_km'] ?? 0.0) ?>
            ];
            const chartLabels3 = ['Rencana', 'Proses', 'Selesai'];
            const chartColors3 = ['#0284c7', '#6366f1', '#10b981'];

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
                        hoverBorderWidth: 3,
                        hoverBorderColor: '#ffffff',
                        hoverOffset: 12
                    }]
                },
                options: {
                    layout: { padding: 30 },
                    responsive: true,
                    maintainAspectRatio: true,
                    animation: {
                        duration: 1000,
                        easing: 'easeOutQuart'
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'rgba(31,41,55,0.95)',
                            titleFont: { family: 'Inter, system-ui, sans-serif', size: 12, weight: '600' },
                            bodyFont: { family: 'Inter, system-ui, sans-serif', size: 11 },
                            padding: { top: 10, bottom: 10, left: 14, right: 14 },
                            cornerRadius: 10,
                            displayColors: true,
                            callbacks: {
                                label: function(context) {
                                    const value = context.raw;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                    return ` ${context.label}: ${new Intl.NumberFormat('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(value)} km (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });
        }
    });
    </script>

    <?php view('export.dashboard'); ?>
</div>
