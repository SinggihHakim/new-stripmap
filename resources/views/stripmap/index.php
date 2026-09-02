<!-- ============================================================ -->
<!-- Halaman Daftar Strip Map & Perkerasan per Ruas               -->
<!-- ============================================================ -->

<div class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="<?= base_url('ruas') ?>"
               class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-white border border-gray-200 hover:bg-gray-50 transition-colors shadow-sm">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Strip Map & Perkerasan Ruas Jalan</h1>
                <p class="text-sm text-gray-500">Manajemen segmen kondisi dan jenis perkerasan jalan.</p>
            </div>
        </div>
        <div class="flex flex-wrap gap-2">
            <button onclick="window.dispatchEvent(new CustomEvent('open-foto-modal'))"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-purple-600 text-white text-sm font-medium rounded-xl hover:bg-purple-700 transition-colors shadow-sm cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 011.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Foto Lapangan (<?= count($fotoLapangans ?? []) ?>)
            </button>
            <?php if (!empty($stripmaps) || !empty($perkerasans)): ?>
            <a href="<?= base_url('stripmap/preview/' . $ruas['id']) ?>"
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-xl hover:bg-indigo-700 transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                Preview Mode
            </a>
            <a href="<?= base_url('stripmap/compare/' . $ruas['id']) ?>"
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-violet-600 text-white text-sm font-medium rounded-xl hover:bg-violet-700 transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Perbandingan Tahun
            </a>
            <?php endif; ?>
            <label class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 text-white text-sm font-medium rounded-xl hover:bg-emerald-700 transition-colors shadow-sm cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                </svg>
                Import KML / KMZ
                <input type="file" accept=".kml,.kmz" class="hidden" onchange="handleDirectKmlImport(event, <?= $ruas['id'] ?>)">
            </label>
            <a href="<?= base_url('stripmap/create/' . $ruas['id']) ?>"
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-xl hover:bg-blue-700 transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Segmen Data
            </a>
        </div>
    </div>

    <!-- Hidden Form untuk Import KML Langsung -->
    <form id="direct-kml-form" method="POST" action="<?= base_url('stripmap/import-kml/' . $ruas['id']) ?>" class="hidden">
        <input type="hidden" name="koordinat_json" id="kml_koordinat_json">
        <input type="hidden" name="lat_awal" id="kml_lat_awal">
        <input type="hidden" name="lng_awal" id="kml_lng_awal">
        <input type="hidden" name="lat_akhir" id="kml_lat_akhir">
        <input type="hidden" name="lng_akhir" id="kml_lng_akhir">
    </form>

    <!-- Data Umum Ruas Jalan Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-sm font-semibold text-gray-900">Data Umum Ruas Jalan</h2>
        </div>
        <div class="border-t border-gray-100">
            <table class="w-full text-sm text-left">
                <tbody class="divide-y divide-gray-100">
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-3 font-semibold text-gray-500 w-1/4">Nama Ruas</td>
                        <td class="px-6 py-3 text-gray-900 font-bold"><?= e($ruas['nama_ruas']) ?></td>
                    </tr>
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-3 font-semibold text-gray-500 w-1/4">Nomor Ruas</td>
                        <td class="px-6 py-3 text-gray-800 font-semibold font-mono"><?= e($ruas['kode_ruas']) ?></td>
                    </tr>
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-3 font-semibold text-gray-500 w-1/4">Panjang Ruas</td>
                        <td class="px-6 py-3 text-gray-900 font-bold"><?= format_number($ruas['panjang']) ?> m</td>
                    </tr>
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-3 font-semibold text-gray-500 w-1/4">Koridor</td>
                        <td class="px-6 py-3 text-gray-900 font-semibold"><?= e($ruas['koridor'] ?? '-') ?></td>
                    </tr>
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-3 font-semibold text-gray-500 w-1/4">Kabupaten / Kota</td>
                        <td class="px-6 py-3 text-gray-900 font-semibold"><?= e($ruas['kabupaten_kota'] ?? '-') ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Strip Map & Perkerasan Visual Preview Partial -->
    <?php if (!empty($stripmaps) || !empty($perkerasans) || !empty($penanganans)): ?>
        <?php view('stripmap._visual', [
            'stripmaps'         => $stripmaps,
            'summary'           => $summary,
            'ruas'              => $ruas,
            'perkerasans'       => $perkerasans ?? [],
            'summaryPerkerasan' => $summaryPerkerasan ?? [],
            'fotoLapangans'     => $fotoLapangans ?? [],
            'penanganans'       => $penanganans ?? [],
            'penangananSummary' => $penangananSummary ?? [],
            'penangananYears'   => $penangananYears ?? []
        ]); ?>
    <?php endif; ?>

    <!-- Peta Lokasi Ruas (tampil jika ada koordinat awal/akhir ATAU ada rute KML/KMZ) -->
    <?php
    $hasMapData = (!empty($ruas['lat_awal']) && !empty($ruas['lng_awal']))
               || (!empty($ruas['koordinat_json']) && $ruas['koordinat_json'] !== '[]' && $ruas['koordinat_json'] !== 'null');
    ?>
    <?php if ($hasMapData): ?>
    <div x-data="{ isOpen: true }" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between cursor-pointer select-none bg-gray-50/60" @click="isOpen = !isOpen">
            <h2 class="text-base font-bold text-gray-900 flex items-center gap-2">
                <span class="w-2 h-4 rounded bg-teal-600 inline-block"></span>
                Peta Lokasi Ruas Jalan
            </h2>
            <div class="flex items-center gap-3">
                <a href="https://www.google.com/maps?q=&layer=c&cbll=<?= e($ruas['lat_awal']) ?>,<?= e($ruas['lng_awal']) ?>"
                   target="_blank" rel="noopener"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-blue-700 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors"
                   @click.stop>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.069A1 1 0 0121 8.82V18a1 1 0 01-1.447.894L15 17M3 8a2 2 0 012-2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z"/></svg>
                    Street View
                </a>
                <?php if (!empty($ruas['lat_akhir']) && !empty($ruas['lng_akhir'])): ?>
                <a href="https://www.google.com/maps/dir/<?= e($ruas['lat_awal']) ?>,<?= e($ruas['lng_awal']) ?>/<?= e($ruas['lat_akhir']) ?>,<?= e($ruas['lng_akhir']) ?>"
                   target="_blank" rel="noopener"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-teal-700 bg-teal-50 rounded-lg hover:bg-teal-100 transition-colors"
                   @click.stop>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                    Lihat di Google Maps
                </a>
                <?php endif; ?>
                <button class="text-gray-500 hover:text-gray-700 focus:outline-none transition-transform duration-200" :class="isOpen ? 'rotate-90' : 'rotate-0'">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </button>
            </div>
        </div>
        <div x-show="isOpen" x-collapse>
            <?php
            // Hitung total dan urutkan segmen secara linier (sepanjang rute STA 0 s/d STA Akhir)
            $totBaik = $totSedang = $totRR = $totRB = 0.0;
            $linearPieces = [];

            // Sort stripmaps berdasarkan sta_awal ascending
            $sortedStripmaps = $stripmaps ?? [];
            usort($sortedStripmaps, fn($a, $b) => (float)$a['sta_awal'] <=> (float)$b['sta_awal']);

            foreach ($sortedStripmaps as $s) {
                $b  = (float) $s['baik'];
                $sd = (float) $s['sedang'];
                $rr = (float) $s['rusak_ringan'];
                $rb = (float) $s['rusak_berat'];

                $totBaik   += $b;
                $totSedang += $sd;
                $totRR     += $rr;
                $totRB     += $rb;

                $sa = (float)$s['sta_awal'];
                $curr = $sa;

                if ($b > 0) {
                    $linearPieces[] = ['sta_awal' => $curr, 'sta_akhir' => $curr + $b, 'panjang' => $b, 'lbl' => 'Baik', 'bg' => 'bg-emerald-500', 'hex' => '#10b981'];
                    $curr += $b;
                }
                if ($sd > 0) {
                    $linearPieces[] = ['sta_awal' => $curr, 'sta_akhir' => $curr + $sd, 'panjang' => $sd, 'lbl' => 'Sedang', 'bg' => 'bg-yellow-400', 'hex' => '#facc15'];
                    $curr += $sd;
                }
                if ($rr > 0) {
                    $linearPieces[] = ['sta_awal' => $curr, 'sta_akhir' => $curr + $rr, 'panjang' => $rr, 'lbl' => 'Rusak Ringan', 'bg' => 'bg-orange-500', 'hex' => '#f97316'];
                    $curr += $rr;
                }
                if ($rb > 0) {
                    $linearPieces[] = ['sta_awal' => $curr, 'sta_akhir' => $curr + $rb, 'panjang' => $rb, 'lbl' => 'Rusak Berat', 'bg' => 'bg-red-500', 'hex' => '#ef4444'];
                    $curr += $rb;
                }
            }

            $totKond = $totBaik + $totSedang + $totRR + $totRB;
            $pct = fn($v) => $totKond > 0 ? round($v / $totKond * 100, 1) : 0;
            $summaryRows = [
                ['Baik',         $totBaik,   '#10b981', 'bg-emerald-500'],
                ['Sedang',       $totSedang, '#facc15', 'bg-yellow-400'],
                ['Rusak Ringan', $totRR,     '#f97316', 'bg-orange-500'],
                ['Rusak Berat',  $totRB,     '#ef4444', 'bg-red-500'],
            ];

            $formatSta = function($m) {
                $km = floor($m / 1000);
                $r  = round($m % 1000);
                return 'STA ' . $km . '+' . str_pad((string)$r, 3, '0', STR_PAD_LEFT);
            };
            ?>
            <?php if ($totKond > 0): ?>
            <div class="px-6 pt-5 pb-4 border-b border-gray-100">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-semibold text-gray-600 uppercase tracking-wider flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                        Kondisi Linier Sepanjang Rute Jalan
                    </span>
                    <span class="text-xs text-gray-500 font-medium">Total: <?= format_number($totKond) ?> m</span>
                </div>
                <!-- Bar Linier Berurutan Sesuai Rute Asli (STA 0 s/d STA Akhir) -->
                <div class="flex w-full h-4 rounded-full overflow-hidden bg-gray-100 shadow-inner">
                    <?php foreach ($linearPieces as $piece): ?>
                        <?php $wPct = ($piece['panjang'] / $totKond) * 100; ?>
                        <div class="<?= $piece['bg'] ?> h-full transition-all hover:brightness-110 cursor-pointer"
                             style="width:<?= $wPct ?>%"
                             title="STA <?= format_number($piece['sta_awal']) ?> - <?= format_number($piece['sta_akhir']) ?> m (<?= $piece['lbl'] ?>: <?= format_number($piece['panjang']) ?> m)"></div>
                    <?php endforeach; ?>
                </div>
                <!-- Ringkasan Statistik Persentase -->
                <div class="flex flex-wrap gap-x-5 gap-y-1.5 mt-3">
                    <?php foreach ($summaryRows as [$lbl, $val, $hex, $bg]): ?>
                    <div class="flex items-center gap-1.5 text-xs">
                        <span class="w-3 h-3 rounded-sm inline-block" style="background:<?= $hex ?>"></span>
                        <span class="text-gray-600"><?= $lbl ?></span>
                        <span class="font-semibold text-gray-900"><?= $pct($val) ?>%</span>
                        <span class="text-gray-400">(<?= format_number($val) ?> m)</span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Interactive Map Controls & Filter Toolbar -->
            <div x-data="{
                    layerKondisi: true,
                    layerPenanganan: true,
                    layerPerkerasan: false,
                    layerFoto: true,
                    penangananYear: 'all',
                    penangananStatus: 'all',
                    legendOpen: (function() {
                        try {
                            let s = localStorage.getItem('map_legend_open');
                            return s !== null ? (s === '1') : true;
                        } catch(e) { return true; }
                    })(),
                    toggleLegend() {
                        this.legendOpen = !this.legendOpen;
                        try { localStorage.setItem('map_legend_open', this.legendOpen ? '1' : '0'); } catch(e) {}
                    },
                    init() {
                        this.$watch('layerKondisi', () => this.syncLayers());
                        this.$watch('layerPenanganan', () => this.syncLayers());
                        this.$watch('layerPerkerasan', () => this.syncLayers());
                        this.$watch('layerFoto', () => this.syncLayers());
                        this.$watch('penangananYear', () => this.syncLayers());
                        this.$watch('penangananStatus', () => this.syncLayers());
                    },
                    syncLayers() {
                        if (typeof window.applyMapFilters === 'function') {
                            window.applyMapFilters({
                                showKondisi: this.layerKondisi,
                                showPenanganan: this.layerPenanganan,
                                showPerkerasan: this.layerPerkerasan,
                                showFoto: this.layerFoto,
                                year: this.penangananYear,
                                status: this.penangananStatus
                            });
                        }
                    }
                 }" 
                 class="relative border-b border-gray-100 bg-white">
                
                <!-- Toolbar Header: Multi-Tombol Layering & Legenda Toggle -->
                <div class="px-5 py-3 bg-slate-50/80 flex flex-wrap items-center justify-between gap-3 border-b border-gray-200/70">
                    
                    <!-- Left: Layer Buttons -->
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="text-xs font-bold text-gray-500 uppercase tracking-wider mr-1 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                            Layer Peta:
                        </span>

                        <!-- Tombol Layer 1: Kondisi Jalan -->
                        <button type="button" 
                                @click="layerKondisi = !layerKondisi"
                                :class="layerKondisi ? 'bg-emerald-600 text-white shadow-sm border-emerald-700 font-semibold' : 'bg-white text-gray-600 hover:bg-gray-100 border-gray-300'"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs border transition-all">
                            <span class="w-2 h-2 rounded-full" :class="layerKondisi ? 'bg-white' : 'bg-emerald-500'"></span>
                            Kondisi Jalan
                        </button>

                        <!-- Tombol Layer 2: Penanganan Jalan -->
                        <button type="button" 
                                @click="layerPenanganan = !layerPenanganan"
                                :class="layerPenanganan ? 'bg-blue-600 text-white shadow-sm border-blue-700 font-semibold' : 'bg-white text-gray-600 hover:bg-gray-100 border-gray-300'"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs border transition-all">
                            <span class="w-2 h-2 rounded-full" :class="layerPenanganan ? 'bg-white' : 'bg-blue-500'"></span>
                            Penanganan Jalan
                            <?php if (!empty($penanganans)): ?>
                            <span class="px-1.5 py-0.2 text-[10px] rounded-full" :class="layerPenanganan ? 'bg-blue-800 text-blue-100' : 'bg-gray-100 text-gray-600'"><?= count($penanganans) ?></span>
                            <?php endif; ?>
                        </button>

                        <!-- Tombol Layer 3: Jenis Perkerasan -->
                        <button type="button" 
                                @click="layerPerkerasan = !layerPerkerasan"
                                :class="layerPerkerasan ? 'bg-amber-700 text-white shadow-sm border-amber-800 font-semibold' : 'bg-white text-gray-600 hover:bg-gray-100 border-gray-300'"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs border transition-all">
                            <span class="w-2 h-2 rounded-full" :class="layerPerkerasan ? 'bg-white' : 'bg-amber-600'"></span>
                            Jenis Perkerasan
                        </button>

                        <!-- Tombol Layer 4: Foto Lapangan -->
                        <button type="button" 
                                @click="layerFoto = !layerFoto"
                                :class="layerFoto ? 'bg-indigo-600 text-white shadow-sm border-indigo-700 font-semibold' : 'bg-white text-gray-600 hover:bg-gray-100 border-gray-300'"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs border transition-all">
                            <span class="w-2 h-2 rounded-full" :class="layerFoto ? 'bg-white' : 'bg-indigo-500'"></span>
                            Foto Real STA
                            <?php if (!empty($fotoLapangans)): ?>
                            <span class="px-1.5 py-0.2 text-[10px] rounded-full" :class="layerFoto ? 'bg-indigo-800 text-indigo-100' : 'bg-gray-100 text-gray-600'"><?= count($fotoLapangans) ?></span>
                            <?php endif; ?>
                        </button>
                    </div>

                    <!-- Right: Tombol Hide / Unhide Legenda Peta -->
                    <div class="flex items-center gap-2">
                        <button type="button" 
                                @click="toggleLegend()"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white hover:bg-gray-100 border border-gray-300 rounded-lg text-xs font-semibold text-gray-700 shadow-sm transition-colors">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span x-text="legendOpen ? 'Sembunyikan Legenda' : 'Tampilkan Legenda'"></span>
                            <span class="w-1.5 h-1.5 rounded-full" :class="legendOpen ? 'bg-emerald-500' : 'bg-gray-400'"></span>
                        </button>
                    </div>
                </div>

                <!-- Sub-Filter Baris ke-2: Filter Tahun & Status Penanganan (Tampil Dinamis saat Layer Penanganan Aktif) -->
                <div x-show="layerPenanganan" x-transition.opacity class="px-5 py-2.5 bg-blue-50/50 border-b border-blue-100/70 flex flex-wrap items-center justify-between gap-3 text-xs">
                    
                    <!-- Filter Tahun Buttons -->
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="font-bold text-blue-900 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            Pilih Tahun:
                        </span>
                        <div class="inline-flex rounded-lg shadow-sm bg-white p-0.5 border border-blue-200">
                            <button type="button" @click="penangananYear = 'all'" 
                                    :class="penangananYear === 'all' ? 'bg-blue-600 text-white font-bold' : 'text-gray-700 hover:bg-gray-50 font-medium'"
                                    class="px-2.5 py-1 rounded-md text-xs transition-colors">
                                Semua Tahun
                            </button>
                            <?php foreach ($penangananYears ?? [] as $yr): ?>
                            <button type="button" @click="penangananYear = '<?= $yr ?>'" 
                                    :class="penangananYear == '<?= $yr ?>' ? 'bg-blue-600 text-white font-bold' : 'text-gray-700 hover:bg-gray-50 font-medium'"
                                    class="px-2.5 py-1 rounded-md text-xs transition-colors">
                                <?= $yr ?>
                            </button>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Filter Status Buttons -->
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="font-bold text-blue-900">Status:</span>
                        <div class="inline-flex rounded-lg shadow-sm bg-white p-0.5 border border-blue-200">
                            <button type="button" @click="penangananStatus = 'all'" 
                                    :class="penangananStatus === 'all' ? 'bg-blue-600 text-white font-bold' : 'text-gray-700 hover:bg-gray-50 font-medium'"
                                    class="px-2.5 py-1 rounded-md text-xs transition-colors">
                                Semua
                            </button>
                            <button type="button" @click="penangananStatus = 'rencana'" 
                                    :class="penangananStatus === 'rencana' ? 'bg-sky-600 text-white font-bold' : 'text-gray-700 hover:bg-gray-50 font-medium'"
                                    class="px-2.5 py-1 rounded-md text-xs transition-colors flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-sky-400"></span> Rencana
                            </button>
                            <button type="button" @click="penangananStatus = 'proses'" 
                                    :class="penangananStatus === 'proses' ? 'bg-indigo-600 text-white font-bold' : 'text-gray-700 hover:bg-gray-50 font-medium'"
                                    class="px-2.5 py-1 rounded-md text-xs transition-colors flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-indigo-400"></span> Proses
                            </button>
                            <button type="button" @click="penangananStatus = 'selesai'" 
                                    :class="penangananStatus === 'selesai' ? 'bg-emerald-600 text-white font-bold' : 'text-gray-700 hover:bg-gray-50 font-medium'"
                                    class="px-2.5 py-1 rounded-md text-xs transition-colors flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> Selesai
                            </button>
                        </div>
                    </div>

                </div>

                <!-- Container Map Element & Floating Interactive Legend -->
                <div class="relative w-full">
                    <div id="ruas-detail-map" class="w-full" style="height:420px;"></div>

                    <!-- FLOATING LEGENDA PETA (Bisa di-Hide & di-Unhide) -->
                    <div x-show="legendOpen" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 translate-y-2"
                         class="absolute bottom-4 right-4 z-[400] max-w-xs w-64 bg-white/95 backdrop-blur-md rounded-xl shadow-xl border border-gray-200 overflow-hidden text-xs">
                        
                        <!-- Legend Header with Minimize Button -->
                        <div class="px-3 py-2 bg-slate-100 border-b border-gray-200 flex items-center justify-between">
                            <span class="font-bold text-gray-800 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Legenda Peta
                            </span>
                            <button type="button" @click="toggleLegend()" title="Sembunyikan Legenda" class="text-gray-400 hover:text-gray-700 p-0.5 rounded focus:outline-none">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                        </div>

                        <!-- Legend Content (Dinamis sesuai layer aktif) -->
                        <div class="p-3 space-y-3 max-h-60 overflow-y-auto">
                            
                            <!-- 1. Kondisi Jalan -->
                            <div x-show="layerKondisi" class="space-y-1">
                                <div class="font-bold text-gray-700 text-[11px]">Kondisi Jalan (Eksisting)</div>
                                <div class="grid grid-cols-2 gap-1 text-[11px] text-gray-600">
                                    <div class="flex items-center gap-1.5"><span class="w-3 h-2 rounded-sm bg-emerald-500 inline-block"></span> Baik</div>
                                    <div class="flex items-center gap-1.5"><span class="w-3 h-2 rounded-sm bg-yellow-400 inline-block"></span> Sedang</div>
                                    <div class="flex items-center gap-1.5"><span class="w-3 h-2 rounded-sm bg-orange-500 inline-block"></span> R. Ringan</div>
                                    <div class="flex items-center gap-1.5"><span class="w-3 h-2 rounded-sm bg-red-500 inline-block"></span> R. Berat</div>
                                </div>
                            </div>

                            <!-- 2. Penanganan Jalan -->
                            <div x-show="layerPenanganan" class="space-y-1 border-t border-gray-100 pt-2">
                                <div class="font-bold text-blue-900 text-[11px]">Penanganan Jalan</div>
                                <div class="space-y-1 text-[11px] text-gray-600">
                                    <div class="flex items-center gap-2">
                                        <span class="w-4 h-1 rounded border-t-2 border-dashed border-sky-600 inline-block"></span> 
                                        <span>Rencana / Usulan</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="w-4 h-1.5 rounded bg-indigo-600 inline-block"></span> 
                                        <span>Sedang Dikerjakan</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="w-4 h-1.5 rounded bg-emerald-600 inline-block"></span> 
                                        <span>Selesai Ditangani</span>
                                    </div>
                                </div>
                            </div>

                            <!-- 3. Jenis Perkerasan -->
                            <div x-show="layerPerkerasan" class="space-y-1 border-t border-gray-100 pt-2">
                                <div class="font-bold text-amber-900 text-[11px]">Jenis Perkerasan</div>
                                <div class="grid grid-cols-2 gap-1 text-[11px] text-gray-600">
                                    <div class="flex items-center gap-1.5"><span class="w-3 h-2 rounded-sm bg-slate-500 inline-block"></span> Rigid</div>
                                    <div class="flex items-center gap-1.5"><span class="w-3 h-2 rounded-sm bg-slate-900 inline-block"></span> Aspal</div>
                                    <div class="flex items-center gap-1.5"><span class="w-3 h-2 rounded-sm bg-amber-800 inline-block"></span> Agregat</div>
                                    <div class="flex items-center gap-1.5"><span class="w-3 h-2 rounded-sm bg-purple-600 inline-block"></span> B. Tembus</div>
                                </div>
                            </div>

                            <!-- 4. Foto Real -->
                            <div x-show="layerFoto" class="flex items-center gap-2 border-t border-gray-100 pt-2 text-[11px] text-gray-600">
                                <span class="w-4 h-4 rounded-full bg-indigo-600 text-white flex items-center justify-center">
                                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                </span>
                                <span>Titik Foto Lapangan Real</span>
                            </div>

                        </div>
                    </div>

                    <!-- Mini Unhide Button (Saat Legenda di-Hide) -->
                    <div x-show="!legendOpen" class="absolute bottom-4 right-4 z-[400]">
                        <button type="button" 
                                @click="toggleLegend()" 
                                title="Buka Legenda Peta" 
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white/95 backdrop-blur-sm hover:bg-white text-gray-800 rounded-lg shadow-lg border border-gray-300 font-bold text-xs transition-all">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Legenda
                        </button>
                    </div>

                </div>

            </div>
        </div>
    </div>

    <!-- Leaflet JS & CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
    (function () {
        const latAwal  = <?= !empty($ruas['lat_awal']) ? (float) $ruas['lat_awal'] : 'null' ?>;
        const lngAwal  = <?= !empty($ruas['lng_awal']) ? (float) $ruas['lng_awal'] : 'null' ?>;
        const latAkhir = <?= !empty($ruas['lat_akhir']) ? (float) $ruas['lat_akhir'] : 'null' ?>;
        const lngAkhir = <?= !empty($ruas['lng_akhir']) ? (float) $ruas['lng_akhir'] : 'null' ?>;
        const panjangRuas = <?= (float) $ruas['panjang'] ?>;

        // Polyline rute asli hasil impor KML/KMZ
        <?php
            $safeRouteJson = '[]';
            if (!empty($ruas['koordinat_json'])) {
                $decodedRoute = json_decode($ruas['koordinat_json'], true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decodedRoute)) {
                    $safeRouteJson = $ruas['koordinat_json'];
                }
            }
        ?>
        const rawRoute = <?= $safeRouteJson ?>;
        const fotoLapangans = <?= json_encode($fotoLapangans ?? []) ?>;

        const segments = <?= json_encode(array_map(function($s) {
            return [
                'sta_awal'    => (float) $s['sta_awal'],
                'sta_akhir'   => (float) $s['sta_akhir'],
                'panjang'     => (float) $s['panjang'],
                'baik'        => (float) $s['baik'],
                'sedang'      => (float) $s['sedang'],
                'rusak_ringan'=> (float) $s['rusak_ringan'],
                'rusak_berat' => (float) $s['rusak_berat'],
            ];
        }, $stripmaps ?? [])) ?>;

        const penanganans = <?= json_encode(array_map(function($pn) {
            return [
                'id'               => (int) $pn['id'],
                'tahun'            => (int) $pn['tahun'],
                'sta_awal'         => (float) $pn['sta_awal'],
                'sta_akhir'        => (float) $pn['sta_akhir'],
                'panjang'          => (float) $pn['panjang'],
                'jenis_penanganan' => $pn['jenis_penanganan'],
                'status'           => $pn['status'],
                'nama_paket'       => $pn['nama_paket'] ?? '',
                'anggaran'         => (float) ($pn['anggaran'] ?? 0),
                'sumber_dana'      => $pn['sumber_dana'] ?? '',
                'color'            => !empty($pn['warna']) ? $pn['warna'] : (PenangananService::STATUS_COLORS[$pn['status']] ?? '#6366f1'),
                'status_label'     => PenangananService::STATUS_LABELS[$pn['status']] ?? ucfirst($pn['status']),
            ];
        }, $penanganans ?? [])) ?>;

        const perkerasans = <?= json_encode(array_map(function($pk) {
            return [
                'sta_awal'      => (float) $pk['sta_awal'],
                'sta_akhir'     => (float) $pk['sta_akhir'],
                'panjang'       => (float) $pk['panjang'],
                'rigid'         => (float) $pk['rigid'],
                'aspal'         => (float) $pk['aspal'],
                'agregat_tanah' => (float) $pk['agregat_tanah'],
                'belum_tembus'  => (float) $pk['belum_tembus'],
            ];
        }, $perkerasans ?? [])) ?>;

        const condColors = { baik: '#10b981', sedang: '#facc15', rusak_ringan: '#f97316', rusak_berat: '#ef4444' };
        const condLabels = { baik: 'Baik', sedang: 'Sedang', rusak_ringan: 'Rusak Ringan', rusak_berat: 'Rusak Berat' };
        const condOrder  = ['baik', 'sedang', 'rusak_ringan', 'rusak_berat'];

        const paveColors = { rigid: '#6b7280', aspal: '#111827', agregat_tanah: '#7c461b', belum_tembus: '#7c3aed' };
        const paveLabels = { rigid: 'Rigid (Beton)', aspal: 'Aspal', agregat_tanah: 'Kerikil', belum_tembus: 'Belum Tembus' };

        const staFmt = m => {
            const km = Math.floor(m / 1000);
            const r  = Math.round(m % 1000);
            return km + '+' + String(r).padStart(3, '0');
        };

        function condPieces(seg) {
            const sum = seg.baik + seg.sedang + seg.rusak_ringan + seg.rusak_berat;
            const denom = sum > 0 ? sum : ((seg.sta_akhir - seg.sta_awal) || 1);
            const pieces = [];
            let acc = 0;
            condOrder.forEach(key => {
                const len = seg[key];
                if (len > 0) {
                    pieces.push({ key, color: condColors[key], len, t0: acc / denom, t1: (acc + len) / denom });
                    acc += len;
                }
            });
            return pieces;
        }

        function pavePieces(pk) {
            const sum = pk.rigid + pk.aspal + pk.agregat_tanah + pk.belum_tembus;
            const denom = sum > 0 ? sum : ((pk.sta_akhir - pk.sta_awal) || 1);
            const pieces = [];
            let acc = 0;
            ['rigid', 'aspal', 'agregat_tanah', 'belum_tembus'].forEach(key => {
                const len = pk[key];
                if (len > 0) {
                    pieces.push({ key, color: paveColors[key], label: paveLabels[key], len, t0: acc / denom, t1: (acc + len) / denom });
                    acc += len;
                }
            });
            return pieces;
        }

        function piecePopup(seg, piece, svLat, svLng) {
            return `<b>${condLabels[piece.key]}</b> — ${piece.len.toLocaleString('id-ID')} m<br>
                <span style="color:#6b7280">Segmen STA ${staFmt(seg.sta_awal)} – ${staFmt(seg.sta_akhir)}</span><br>
                Baik: ${seg.baik} m &nbsp; Sedang: ${seg.sedang} m<br>
                R.Ringan: ${seg.rusak_ringan} m &nbsp; R.Berat: ${seg.rusak_berat} m<br>
                <a href="https://www.google.com/maps?q=&layer=c&cbll=${svLat},${svLng}" target="_blank" rel="noopener"
                   style="color:#2563eb;text-decoration:underline">Buka Street View titik ini</a>`;
        }

        function penangananPopup(pn, svLat, svLng) {
            const statusBg = pn.status === 'selesai' ? '#ecfdf5;color:#065f46;border:1px solid #a7f3d0' : (pn.status === 'proses' ? '#eef2ff;color:#3730a3;border:1px solid #c7d2fe' : '#f0f9ff;color:#0369a1;border:1px solid #bae6fd');
            return `<div style="font-family:sans-serif;min-width:200px">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:4px">
                    <span style="font-weight:700;font-size:12px;color:#1e293b">Penanganan ${pn.tahun}</span>
                    <span style="font-size:10px;font-weight:700;padding:2px 6px;border-radius:4px;background:${statusBg}">${pn.status_label}</span>
                </div>
                <div style="font-size:12px;font-weight:600;color:#0f172a;margin-bottom:3px">${pn.jenis_penanganan}</div>
                ${pn.nama_paket ? `<div style="font-size:11px;color:#475569;margin-bottom:3px">${pn.nama_paket}</div>` : ''}
                <div style="font-size:11px;color:#64748b;margin-bottom:3px">STA ${staFmt(pn.sta_awal)} – ${staFmt(pn.sta_akhir)} (${pn.panjang.toLocaleString('id-ID')} m)</div>
                ${pn.anggaran > 0 ? `<div style="font-size:11px;font-weight:600;color:#16a34a;margin-bottom:4px">Rp ${pn.anggaran.toLocaleString('id-ID')} ${pn.sumber_dana ? '(' + pn.sumber_dana + ')' : ''}</div>` : ''}
                <a href="https://www.google.com/maps?q=&layer=c&cbll=${svLat},${svLng}" target="_blank" rel="noopener"
                   style="color:#2563eb;text-decoration:underline;font-size:11px">Buka Street View titik ini</a>
            </div>`;
        }

        function haversine(a, b) {
            const R = 6371000, toRad = d => d * Math.PI / 180;
            const dLat = toRad(b[0] - a[0]), dLng = toRad(b[1] - a[1]);
            const s = Math.sin(dLat/2)**2 + Math.cos(toRad(a[0]))*Math.cos(toRad(b[0]))*Math.sin(dLng/2)**2;
            return 2 * R * Math.asin(Math.sqrt(s));
        }

        function addStaMarkers(map, posAtFrac, totalSta) {
            if (!(totalSta > 0)) return;
            let step = 1000;
            const steps = [1000, 2000, 5000, 10000, 20000, 50000];
            for (const s of steps) { step = s; if (totalSta / s <= 15) break; }
            for (let m = 0; m <= totalSta + 1; m += step) {
                const pos = posAtFrac(Math.min(m / totalSta, 1));
                if (!pos) continue;
                L.marker(pos, {
                    icon: L.divIcon({
                        className: '',
                        html: `<div style="background:#1f2937;color:#fff;font:10px/1 sans-serif;padding:2px 4px;border-radius:4px;white-space:nowrap;box-shadow:0 1px 2px rgba(0,0,0,.4)">${staFmt(m)}</div>`,
                        iconSize: [0, 0], iconAnchor: [0, 18]
                    }),
                    interactive: false, keyboard: false
                }).addTo(map);
            }
        }

        function addFotoMarkers(layerGroup, posAtFrac, totalSta) {
            if (!fotoLapangans || !fotoLapangans.length || !(totalSta > 0)) return;

            fotoLapangans.forEach(foto => {
                const frac = Math.min(Math.max(foto.sta_meter / totalSta, 0), 1);
                const pos = posAtFrac(frac);
                if (!pos) return;

                const cameraIcon = L.divIcon({
                    className: 'custom-photo-marker',
                    html: `
                        <div class="group relative cursor-pointer flex items-center justify-center w-7 h-7 rounded-full bg-indigo-600 text-white border-2 border-white shadow-lg hover:scale-125 transition-transform" style="background:#4f46e5; border:2px solid #ffffff; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.3);">
                            <svg style="width:14px;height:14px" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                    `,
                    iconSize: [28, 28],
                    iconAnchor: [14, 14]
                });

                const popupHtml = `
                    <div style="font-family:sans-serif;padding:2px;max-width:240px">
                        <div style="position:relative;overflow:hidden;border-radius:8px;margin-bottom:6px;box-shadow:0 1px 3px rgba(0,0,0,.2)">
                            <img src="${foto.url}" alt="STA ${foto.sta_titik}" style="width:100%;height:130px;object-fit:cover;cursor:pointer;display:block" onclick="window.openLightbox('${foto.url}', 'Foto STA ${foto.sta_titik}')"/>
                        </div>
                        <div style="display:flex;align-items:center;justify-between:space-between;margin-bottom:4px">
                            <span style="background:#e0e7ff;color:#3730a3;font-weight:700;font-size:11px;padding:2px 6px;border-radius:4px">STA ${foto.sta_titik}</span>
                            <span style="font-size:10px;color:#6b7280">${(foto.file_size / 1024).toFixed(0)} KB</span>
                        </div>
                        ${foto.keterangan ? `<div style="font-size:11px;color:#4b5563;margin-bottom:4px">${foto.keterangan}</div>` : ''}
                        <button onclick="window.openLightbox('${foto.url}', 'Foto STA ${foto.sta_titik}')" style="background:#4f46e5;color:#fff;border:none;padding:4px 8px;border-radius:6px;font-size:11px;font-weight:600;cursor:pointer;width:100%">
                            Lihat Foto Lapangan Penuh
                        </button>
                    </div>
                `;

                L.marker(pos, { icon: cameraIcon, zIndexOffset: 500 }).addTo(layerGroup).bindPopup(popupHtml);
            });
        }

        let mapInstance = null;
        let mapKondisiLayer = null;
        let mapPenangananLayer = null;
        let mapPerkerasanLayer = null;
        let mapFotoLayer = null;
        let mapTotalSta = 0;
        let mapPosAtFrac = null;
        let mapSliceByDist = null;
        let mapTotal = 1;
        let mapStaSpan = 1;

        function initMap() {
            const container = document.getElementById('ruas-detail-map');
            if (!container) return;

            const route = Array.isArray(rawRoute) ? rawRoute.map(p => [p[1], p[0]]) : [];
            const hasRoute = route.length >= 2;
            const hasValidCoords = hasRoute || (latAwal !== null && lngAwal !== null && (latAwal !== 0 || lngAwal !== 0));

            if (!hasValidCoords) {
                container.style.height = 'auto';
                container.innerHTML = `
                    <div class="flex flex-col items-center justify-center py-8 px-4 bg-slate-50 rounded-xl border border-dashed border-slate-300 text-center">
                        <svg class="w-8 h-8 text-slate-400 mb-2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
                        <p class="text-xs font-semibold text-slate-700">Koordinat Peta Belum Tersedia</p>
                        <p class="text-[11px] text-slate-500 max-w-sm mt-0.5">Data ruas ini belum memiliki titik koordinat awal/akhir atau rute KML. Anda dapat mengedit ruas jalan untuk menambahkan koordinat peta.</p>
                    </div>
                `;
                return;
            }

            const googleHybrid  = L.tileLayer('https://mt1.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', { maxZoom: 20, attribution: '&copy; Google Maps' });
            const googleSat     = L.tileLayer('https://mt1.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', { maxZoom: 20, attribution: '&copy; Google Maps' });
            const googleStreets = L.tileLayer('https://mt1.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', { maxZoom: 20, attribution: '&copy; Google Maps' });
            const osm           = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '&copy; OpenStreetMap' });

            mapKondisiLayer    = L.layerGroup();
            mapPenangananLayer = L.layerGroup();
            mapPerkerasanLayer = L.layerGroup();
            mapFotoLayer       = L.layerGroup();

            mapInstance = L.map(container, {
                layers: [googleHybrid, mapKondisiLayer, mapPenangananLayer, mapFotoLayer]
            });

            L.control.layers(
                {
                    "Google Satelit Hybrid": googleHybrid,
                    "Google Satelit Murni": googleSat,
                    "Google Streets": googleStreets,
                    "OpenStreetMap": osm
                },
                null,
                { position: 'topright' }
            ).addTo(mapInstance);

            const makeIcon = (color) => L.divIcon({
                className: '',
                html: `<div style="width:14px;height:14px;border-radius:50%;background:${color};border:3px solid #fff;box-shadow:0 0 0 1px rgba(0,0,0,.3)"></div>`,
                iconSize: [14, 14], iconAnchor: [7, 7]
            });

            const startPt = hasRoute ? route[0] : [latAwal, lngAwal];
            const endPt   = hasRoute ? route[route.length - 1]
                          : (latAkhir !== null ? [latAkhir, lngAkhir] : null);

            L.marker(startPt, { icon: makeIcon('#10b981') }).addTo(mapInstance).bindPopup('<b>Titik Awal Ruas</b>');
            if (endPt) L.marker(endPt, { icon: makeIcon('#ef4444') }).addTo(mapInstance).bindPopup('<b>Titik Akhir Ruas</b>');

            mapTotalSta = panjangRuas > 0 ? panjangRuas
                           : (segments.length ? segments[segments.length - 1].sta_akhir : 0);

            if (hasRoute) {
                setupRouteGeometry(route, mapTotalSta);
                addStaMarkers(mapInstance, mapPosAtFrac, mapTotalSta);
                addFotoMarkers(mapFotoLayer, mapPosAtFrac, mapTotalSta);
                mapInstance.fitBounds(L.polyline(route).getBounds(), { padding: [30, 30] });
            } else if (endPt) {
                setupStraightGeometry(mapTotalSta);
                addStaMarkers(mapInstance, mapPosAtFrac, mapTotalSta);
                addFotoMarkers(mapFotoLayer, mapPosAtFrac, mapTotalSta);
                mapInstance.fitBounds([startPt, endPt], { padding: [40, 40] });
            } else {
                mapInstance.setView(startPt, 14);
            }

            renderAllLayers();
            setTimeout(() => mapInstance && mapInstance.invalidateSize(), 250);
        }

        function setupRouteGeometry(route, totalSta) {
            const cum = [0];
            for (let i = 1; i < route.length; i++) cum.push(cum[i-1] + haversine(route[i-1], route[i]));
            mapTotal = cum[cum.length - 1] || 1;
            mapStaSpan = totalSta > 0 ? totalSta : mapTotal;

            const pointAtDist = (d) => {
                d = Math.max(0, Math.min(d, mapTotal));
                let i = 1;
                while (i < cum.length && cum[i] < d) i++;
                if (i >= route.length) return route[route.length - 1];
                const segLen = cum[i] - cum[i-1] || 1;
                const t = (d - cum[i-1]) / segLen;
                return [
                    route[i-1][0] + t * (route[i][0] - route[i-1][0]),
                    route[i-1][1] + t * (route[i][1] - route[i-1][1])
                ];
            };

            mapSliceByDist = (d0, d1) => {
                const pts = [pointAtDist(d0)];
                for (let i = 0; i < route.length; i++) {
                    if (cum[i] > d0 && cum[i] < d1) pts.push(route[i]);
                }
                pts.push(pointAtDist(d1));
                return pts;
            };

            mapPosAtFrac = (frac) => pointAtDist(frac * mapTotal);
        }

        function setupStraightGeometry(totalSta) {
            const interpolate = (t) => [
                latAwal + t * (latAkhir - latAwal),
                lngAwal + t * (lngAkhir - lngAwal)
            ];
            mapStaSpan = totalSta > 0 ? totalSta : (segments.length ? segments[segments.length - 1].sta_akhir : 1);
            mapTotal = 1;

            mapSliceByDist = (d0, d1) => {
                return [interpolate(d0), interpolate(d1)];
            };

            mapPosAtFrac = (frac) => interpolate(frac);
        }

        function renderAllLayers(filters = {}) {
            if (!mapSliceByDist) return;

            const yearFilter   = filters.year || 'all';
            const statusFilter = filters.status || 'all';

            // 1. Render Kondisi
            mapKondisiLayer.clearLayers();
            if (segments.length > 0) {
                segments.forEach(seg => {
                    const segD0 = (seg.sta_awal  / mapStaSpan) * mapTotal;
                    const segD1 = (seg.sta_akhir / mapStaSpan) * mapTotal;
                    const span  = segD1 - segD0;
                    condPieces(seg).forEach(piece => {
                        const dd0 = segD0 + piece.t0 * span;
                        const dd1 = segD0 + piece.t1 * span;
                        const slice = mapSliceByDist(dd0, dd1);
                        const sv = slice[0];
                        L.polyline(slice, { color: piece.color, weight: 7, opacity: 0.9 })
                            .addTo(mapKondisiLayer).bindPopup(piecePopup(seg, piece, sv[0], sv[1]));
                    });
                });
            }

            // 2. Render Penanganan
            mapPenangananLayer.clearLayers();
            if (penanganans.length > 0) {
                penanganans.forEach(pn => {
                    const pnYear = parseInt(pn.tahun);
                    if (yearFilter !== 'all') {
                        const targetYr = parseInt(yearFilter);
                        // Filter Tahun:
                        // Tampilkan yang dikerjakan pada tahun tersebut (pnYear === targetYr)
                        // ATAU yang sudah selesai pada tahun tersebut/sebelumnya (pn.status === 'selesai' && pnYear <= targetYr)
                        const isThisYear = (pnYear === targetYr);
                        const isAlreadyFinished = (pn.status === 'selesai' && pnYear <= targetYr);
                        if (!isThisYear && !isAlreadyFinished) return;
                    }
                    if (statusFilter !== 'all' && pn.status !== statusFilter) return;

                    const pnD0 = (pn.sta_awal  / mapStaSpan) * mapTotal;
                    const pnD1 = (pn.sta_akhir / mapStaSpan) * mapTotal;
                    const slice = mapSliceByDist(pnD0, pnD1);
                    const sv = slice[0];
                    const isRencana = pn.status === 'rencana';
                    L.polyline(slice, {
                        color: pn.color,
                        weight: 8,
                        opacity: 0.95,
                        dashArray: isRencana ? '6, 6' : null
                    }).addTo(mapPenangananLayer).bindPopup(penangananPopup(pn, sv[0], sv[1]));
                });
            }

            // 3. Render Perkerasan
            mapPerkerasanLayer.clearLayers();
            if (perkerasans.length > 0) {
                perkerasans.forEach(pk => {
                    const pkD0 = (pk.sta_awal  / mapStaSpan) * mapTotal;
                    const pkD1 = (pk.sta_akhir / mapStaSpan) * mapTotal;
                    const span = pkD1 - pkD0;
                    pavePieces(pk).forEach(piece => {
                        const dd0 = pkD0 + piece.t0 * span;
                        const dd1 = pkD0 + piece.t1 * span;
                        const slice = mapSliceByDist(dd0, dd1);
                        const sv = slice[0];
                        L.polyline(slice, { color: piece.color, weight: 6, opacity: 0.85 })
                            .addTo(mapPerkerasanLayer).bindPopup(`<b>${piece.label}</b> (${piece.len} m)<br>Segmen STA ${staFmt(pk.sta_awal)} – ${staFmt(pk.sta_akhir)}`);
                    });
                });
            }
        }

        // Ekspos ke Alpine.js untuk live interactive filtering
        window.applyMapFilters = function(opts) {
            if (!mapInstance) return;

            // Toggle Layer Visibility
            if (opts.showKondisi) {
                if (!mapInstance.hasLayer(mapKondisiLayer)) mapInstance.addLayer(mapKondisiLayer);
            } else {
                if (mapInstance.hasLayer(mapKondisiLayer)) mapInstance.removeLayer(mapKondisiLayer);
            }

            if (opts.showPenanganan) {
                if (!mapInstance.hasLayer(mapPenangananLayer)) mapInstance.addLayer(mapPenangananLayer);
            } else {
                if (mapInstance.hasLayer(mapPenangananLayer)) mapInstance.removeLayer(mapPenangananLayer);
            }

            if (opts.showPerkerasan) {
                if (!mapInstance.hasLayer(mapPerkerasanLayer)) mapInstance.addLayer(mapPerkerasanLayer);
            } else {
                if (mapInstance.hasLayer(mapPerkerasanLayer)) mapInstance.removeLayer(mapPerkerasanLayer);
            }

            if (opts.showFoto) {
                if (!mapInstance.hasLayer(mapFotoLayer)) mapInstance.addLayer(mapFotoLayer);
            } else {
                if (mapInstance.hasLayer(mapFotoLayer)) mapInstance.removeLayer(mapFotoLayer);
            }

            // Re-render penanganan with year/status filters
            renderAllLayers({ year: opts.year, status: opts.status });
        };

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initMap);
        } else {
            setTimeout(initMap, 0);
        }
    })();
    </script>
    <?php endif; ?>

    <!-- Table 1: Kondisi Jalan (Strip Map) -->
    <?php if (!empty($stripmaps)): ?>
    <div x-data="{ isOpen: false }" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between cursor-pointer select-none bg-gray-50/60" @click="isOpen = !isOpen">
            <h2 class="text-base font-bold text-gray-900 flex items-center gap-2">
                <span class="w-2 h-4 rounded bg-blue-600 inline-block"></span>
                Data Segmen Kondisi Jalan (Strip Map)
            </h2>
            <button class="text-gray-500 hover:text-gray-700 focus:outline-none transition-transform duration-200" :class="isOpen ? 'rotate-90' : 'rotate-0'">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            </button>
        </div>
        <div x-show="isOpen" x-collapse class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">STA Awal</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">STA Akhir</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Panjang</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-emerald-700 uppercase tracking-wider">Baik</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-yellow-700 uppercase tracking-wider">Sedang</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-orange-700 uppercase tracking-wider">R. Ringan</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-red-700 uppercase tracking-wider">R. Berat</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($stripmaps as $i => $sm): ?>
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 text-sm text-gray-500"><?= $i + 1 ?></td>
                        <td class="px-4 py-3 text-sm text-gray-700 text-center font-mono"><?= meter_to_sta($sm['sta_awal']) ?></td>
                        <td class="px-4 py-3 text-sm text-gray-700 text-center font-mono"><?= meter_to_sta($sm['sta_akhir']) ?></td>
                        <td class="px-4 py-3 text-sm text-gray-700 text-center font-semibold"><?= format_number($sm['panjang']) ?></td>
                        <td class="px-4 py-3 text-sm text-center">
                            <span class="inline-flex px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 text-xs font-semibold"><?= format_number($sm['baik']) ?></span>
                        </td>
                        <td class="px-4 py-3 text-sm text-center">
                            <span class="inline-flex px-2 py-0.5 rounded-md bg-yellow-50 text-yellow-700 text-xs font-semibold"><?= format_number($sm['sedang']) ?></span>
                        </td>
                        <td class="px-4 py-3 text-sm text-center">
                            <span class="inline-flex px-2 py-0.5 rounded-md bg-orange-50 text-orange-700 text-xs font-semibold"><?= format_number($sm['rusak_ringan']) ?></span>
                        </td>
                        <td class="px-4 py-3 text-sm text-center">
                            <span class="inline-flex px-2 py-0.5 rounded-md bg-red-50 text-red-700 text-xs font-semibold"><?= format_number($sm['rusak_berat']) ?></span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center gap-2">
                                <a href="<?= base_url('stripmap/create/' . $ruas['id'] . '?insert_after=' . $sm['id']) ?>"
                                   class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium text-blue-700 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors"
                                   title="Sisipkan segmen baru setelah segmen ini">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                    Sisipkan
                                </a>
                                <a href="<?= base_url('stripmap/edit/' . $sm['id']) ?>"
                                   class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium text-amber-700 bg-amber-50 rounded-lg hover:bg-amber-100 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    Edit
                                </a>
                                <a href="<?= base_url('stripmap/delete/' . $sm['id']) ?>"
                                   onclick="confirmDelete(event, this.href, 'Yakin ingin menghapus segmen ini?')"
                                   class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium text-red-700 bg-red-50 rounded-lg hover:bg-red-100 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    Hapus
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

    <!-- Table 2: Jenis Perkerasan Jalan -->
    <?php if (!empty($perkerasans)): ?>
    <div x-data="{ isOpen: false }" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between cursor-pointer select-none bg-gray-50/60" @click="isOpen = !isOpen">
            <h2 class="text-base font-bold text-gray-900 flex items-center gap-2">
                <span class="w-2 h-4 rounded bg-amber-700 inline-block"></span>
                Data Segmen Jenis Perkerasan Jalan
            </h2>
            <button class="text-gray-500 hover:text-gray-700 focus:outline-none transition-transform duration-200" :class="isOpen ? 'rotate-90' : 'rotate-0'">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            </button>
        </div>
        <div x-show="isOpen" x-collapse class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">STA Awal</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">STA Akhir</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Panjang</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Rigid</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-slate-900 uppercase tracking-wider">Aspal</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-amber-800 uppercase tracking-wider">Kerikil</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-purple-700 uppercase tracking-wider">Belum Tembus</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($perkerasans as $i => $pk): ?>
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 text-sm text-gray-500"><?= $i + 1 ?></td>
                        <td class="px-4 py-3 text-sm text-gray-700 text-center font-mono"><?= meter_to_sta($pk['sta_awal']) ?></td>
                        <td class="px-4 py-3 text-sm text-gray-700 text-center font-mono"><?= meter_to_sta($pk['sta_akhir']) ?></td>
                        <td class="px-4 py-3 text-sm text-gray-700 text-center font-semibold"><?= format_number($pk['panjang']) ?></td>
                        <td class="px-4 py-3 text-sm text-center">
                            <span class="inline-flex px-2 py-0.5 rounded-md bg-gray-100 text-gray-700 text-xs font-semibold"><?= format_number($pk['rigid']) ?></span>
                        </td>
                        <td class="px-4 py-3 text-sm text-center">
                            <span class="inline-flex px-2 py-0.5 rounded-md bg-slate-900 text-white text-xs font-semibold"><?= format_number($pk['aspal']) ?></span>
                        </td>
                        <td class="px-4 py-3 text-sm text-center">
                            <span class="inline-flex px-2 py-0.5 rounded-md bg-amber-100 text-amber-900 text-xs font-semibold"><?= format_number($pk['agregat_tanah']) ?></span>
                        </td>
                        <td class="px-4 py-3 text-sm text-center">
                            <span class="inline-flex px-2 py-0.5 rounded-md bg-purple-100 text-purple-800 text-xs font-semibold"><?= format_number($pk['belum_tembus']) ?></span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center gap-2">
                                <a href="<?= base_url('perkerasan/edit/' . $pk['id']) ?>"
                                   class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium text-amber-700 bg-amber-50 rounded-lg hover:bg-amber-100 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    Edit
                                </a>
                                <a href="<?= base_url('perkerasan/delete/' . $pk['id']) ?>"
                                   onclick="confirmDelete(event, this.href, 'Yakin ingin menghapus data perkerasan ini?')"
                                   class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium text-red-700 bg-red-50 rounded-lg hover:bg-red-100 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    Hapus
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

    <!-- Table 3: Data Segmentasi Penanganan Jalan Berdasarkan Prioritas Kerusakan (Ter-rusak) -->
    <?php
    // Urutkan segmen kondisi jalan dari yang PALING RUSAK (Rusak Berat DESC, Rusak Ringan DESC, Sedang DESC, STA Awal ASC)
    $sortedByDamage = $stripmaps ?? [];
    usort($sortedByDamage, function($a, $b) {
        $rb_b = (float)($b['rusak_berat'] ?? 0);
        $rb_a = (float)($a['rusak_berat'] ?? 0);
        if ($rb_b !== $rb_a) return $rb_b <=> $rb_a;

        $rr_b = (float)($b['rusak_ringan'] ?? 0);
        $rr_a = (float)($a['rusak_ringan'] ?? 0);
        if ($rr_b !== $rr_a) return $rr_b <=> $rr_a;

        $sd_b = (float)($b['sedang'] ?? 0);
        $sd_a = (float)($a['sedang'] ?? 0);
        if ($sd_b !== $sd_a) return $sd_b <=> $sd_a;

        return (float)($a['sta_awal'] ?? 0) <=> (float)($b['sta_awal'] ?? 0);
    });

    // Helper untuk mencari seluruh data penanganan yang bersesuaian dengan segmen STA
    $findPenanganansForSeg = function($sm) use ($penanganans) {
        $sa = (float)$sm['sta_awal'];
        $sb = (float)$sm['sta_akhir'];
        $res = [];
        foreach ($penanganans ?? [] as $pn) {
            $pa = (float)$pn['sta_awal'];
            $pb = (float)$pn['sta_akhir'];
            if (($pa <= $sb && $pb >= $sa) || ($pa == $sa && $pb == $sb)) {
                $res[] = [
                    'id'               => (int) $pn['id'],
                    'tahun'            => (int) $pn['tahun'],
                    'sta_awal'         => (float) $pn['sta_awal'],
                    'sta_akhir'        => (float) $pn['sta_akhir'],
                    'panjang'          => (float) $pn['panjang'],
                    'jenis_penanganan' => $pn['jenis_penanganan'],
                    'status'           => $pn['status'],
                    'nama_paket'       => $pn['nama_paket'] ?? '',
                    'anggaran'         => (float) ($pn['anggaran'] ?? 0),
                    'sumber_dana'      => $pn['sumber_dana'] ?? '',
                    'warna'            => !empty($pn['warna']) ? $pn['warna'] : '#6366f1',
                    'keterangan'       => $pn['keterangan'] ?? ''
                ];
            }
        }
        return $res;
    };
    ?>

    <div x-data="{ 
            isOpen: false, 
            isAddModalOpen: false, 
            isEditModalOpen: false,
            filterTahun: 'all',
            filterStatusPenanganan: 'all',
            filterPrioritas: 'all',
            getPn(pnsList) {
                if (!pnsList || pnsList.length === 0) return null;
                if (this.filterTahun === 'all') return pnsList[0];
                let targetYr = parseInt(this.filterTahun);
                // 1. Cari yang dikerjakan di tahun target
                let thisYearPn = pnsList.find(p => parseInt(p.tahun) === targetYr);
                if (thisYearPn) return thisYearPn;
                // 2. Atau yang sudah selesai pada tahun target atau sebelumnya
                let completedPrior = pnsList.find(p => p.status === 'selesai' && parseInt(p.tahun) <= targetYr);
                if (completedPrior) return completedPrior;
                return null;
            },
            addData: {
                tahun: '<?= date('Y') ?>',
                sta_awal: '',
                sta_akhir: '',
                panjang: '',
                jenis_penanganan: 'Rekonstruksi Jalan',
                status: 'rencana',
                nama_paket: '',
                anggaran: '',
                sumber_dana: 'APBD Provinsi',
                warna: '#0284c7',
                keterangan: ''
            },
            editData: {
                id: '',
                tahun: '<?= date('Y') ?>',
                sta_awal: '',
                sta_akhir: '',
                panjang: '',
                jenis_penanganan: 'Rekonstruksi Jalan',
                status: 'rencana',
                nama_paket: '',
                anggaran: '',
                sumber_dana: '',
                warna: '#0284c7',
                keterangan: ''
            },
            openAddForSegment(sa, sb, rb, rr, sd) {
                let p = Math.max(0, parseFloat(sb) - parseFloat(sa));
                let defaultJenis = 'Rekonstruksi Jalan';
                if (parseFloat(rb) > 0) defaultJenis = 'Rekonstruksi Jalan';
                else if (parseFloat(rr) > 0) defaultJenis = 'Rehabilitasi Jalan';
                else if (parseFloat(sd) > 0) defaultJenis = 'Pemeliharaan Berkala';

                let defaultYear = this.filterTahun !== 'all' ? this.filterTahun : '<?= date('Y') ?>';

                this.addData = {
                    tahun: defaultYear,
                    sta_awal: sa,
                    sta_akhir: sb,
                    panjang: p,
                    jenis_penanganan: defaultJenis,
                    status: 'rencana',
                    nama_paket: 'Penanganan STA ' + Math.floor(sa/1000) + '+' + String(Math.round(sa%1000)).padStart(3,'0') + ' - ' + Math.floor(sb/1000) + '+' + String(Math.round(sb%1000)).padStart(3,'0'),
                    anggaran: '',
                    sumber_dana: 'APBD Provinsi',
                    warna: '#0284c7',
                    keterangan: 'Prioritas penanganan segmen rusak (Rusak Berat: ' + rb + ' m, Rusak Ringan: ' + rr + ' m, Sedang: ' + sd + ' m)'
                };
                this.isAddModalOpen = true;
            },
            openAddCustom() {
                let defaultYear = this.filterTahun !== 'all' ? this.filterTahun : '<?= date('Y') ?>';
                this.addData = {
                    tahun: defaultYear,
                    sta_awal: '0',
                    sta_akhir: '1000',
                    panjang: '1000',
                    jenis_penanganan: 'Rekonstruksi Jalan',
                    status: 'rencana',
                    nama_paket: '',
                    anggaran: '',
                    sumber_dana: 'APBD Provinsi',
                    warna: '#0284c7',
                    keterangan: ''
                };
                this.isAddModalOpen = true;
            },
            openEdit(pn) {
                this.editData = { ...pn };
                this.isEditModalOpen = true;
            }
         }" 
         class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        
        <div class="px-6 py-4 border-b border-gray-200 flex flex-wrap items-center justify-between gap-4 bg-gray-50/60 select-none">
            <div class="flex items-center gap-3 cursor-pointer" @click="isOpen = !isOpen">
                <h2 class="text-base font-bold text-gray-900 flex items-center gap-2">
                    <span class="w-2 h-4 rounded bg-rose-600 inline-block"></span>
                    Data Segmen Penanganan Jalan (Berdasarkan Prioritas Kerusakan)
                </h2>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-800">
                    <?= count($sortedByDamage) ?> Segmen (Urutan Ter-rusak)
                </span>
                <button class="text-gray-500 hover:text-gray-700 focus:outline-none transition-transform duration-200" :class="isOpen ? 'rotate-90' : 'rotate-0'">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </button>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <!-- Filter Tahun Table -->
                <div class="flex items-center gap-1.5 text-xs">
                    <span class="font-medium text-gray-600 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Tahun:
                    </span>
                    <select x-model="filterTahun" class="rounded-lg border border-gray-300 bg-white px-2.5 py-1.5 font-medium text-gray-700 hover:bg-gray-50 focus:border-blue-500 focus:outline-none text-xs cursor-pointer">
                        <option value="all">Semua Tahun</option>
                        <?php foreach ($penangananYears as $yr): ?>
                            <option value="<?= $yr ?>"><?= $yr ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Filter Status Penanganan -->
                <div class="flex items-center gap-1.5 text-xs">
                    <span class="font-medium text-gray-600">Status:</span>
                    <select x-model="filterStatusPenanganan" class="rounded-lg border border-gray-300 bg-white px-2.5 py-1.5 font-medium text-gray-700 hover:bg-gray-50 focus:border-blue-500 focus:outline-none text-xs cursor-pointer">
                        <option value="all">Semua Status</option>
                        <option value="belum">Belum Ada Penanganan</option>
                        <option value="rencana">Rencana</option>
                        <option value="proses">Sedang Dikerjakan</option>
                        <option value="selesai">Selesai Ditangani</option>
                    </select>
                </div>

                <!-- Filter Prioritas Kerusakan -->
                <div class="flex items-center gap-1.5 text-xs">
                    <span class="font-medium text-gray-600">Prioritas:</span>
                    <select x-model="filterPrioritas" class="rounded-lg border border-gray-300 bg-white px-2.5 py-1.5 font-medium text-gray-700 hover:bg-gray-50 focus:border-blue-500 focus:outline-none text-xs cursor-pointer">
                        <option value="all">Semua Kerusakan</option>
                        <option value="rusak_berat">Rusak Berat > 0</option>
                        <option value="rusak_ringan">Rusak Ringan > 0</option>
                        <option value="sedang">Sedang > 0</option>
                    </select>
                </div>

                <!-- Tombol Tambah Penanganan Kustom -->
                <button @click="openAddCustom()" type="button" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-semibold shadow-sm transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    + Penanganan Kustom
                </button>
            </div>
        </div>

        <div x-show="isOpen" x-collapse class="overflow-x-auto">
            <?php if (empty($sortedByDamage)): ?>
                <div class="p-8 text-center text-gray-500 text-sm">
                    <p>Belum ada data segmen kondisi jalan untuk ruas ini.</p>
                </div>
            <?php else: ?>
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="px-3 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider w-12">Prioritas</th>
                            <th class="px-3 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">STA Awal</th>
                            <th class="px-3 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">STA Akhir</th>
                            <th class="px-3 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Panjang</th>
                            <th class="px-3 py-3 text-center text-xs font-semibold text-emerald-700 uppercase tracking-wider">Baik</th>
                            <th class="px-3 py-3 text-center text-xs font-semibold text-yellow-700 uppercase tracking-wider">Sedang</th>
                            <th class="px-3 py-3 text-center text-xs font-semibold text-orange-700 uppercase tracking-wider">R. Ringan</th>
                            <th class="px-3 py-3 text-center text-xs font-semibold text-red-700 uppercase tracking-wider">R. Berat</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-blue-900 uppercase tracking-wider">Status & Info Penanganan</th>
                            <th class="px-3 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Opsi Penanganan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php foreach ($sortedByDamage as $i => $sm): ?>
                        <?php
                            $segPns = $findPenanganansForSeg($sm);
                            $rb = (float)$sm['rusak_berat'];
                            $rr = (float)$sm['rusak_ringan'];
                            $sd = (float)$sm['sedang'];
                            $bk = (float)$sm['baik'];
                            $rowBg = ($rb > 0) ? 'bg-red-50/30 hover:bg-red-50/60' : (($rr > 0) ? 'bg-orange-50/20 hover:bg-orange-50/50' : (($sd > 0) ? 'bg-yellow-50/20 hover:bg-yellow-50/50' : 'hover:bg-gray-50'));
                        ?>
                        <tr class="transition-colors <?= $rowBg ?>" 
                            x-data="{ 
                                pns: <?= htmlspecialchars(json_encode($segPns), ENT_QUOTES, 'UTF-8') ?>,
                                get activePn() { return getPn(this.pns); }
                            }"
                            x-show="(filterStatusPenanganan === 'all' || (activePn ? activePn.status === filterStatusPenanganan : filterStatusPenanganan === 'belum')) && 
                                    (filterPrioritas === 'all' || 
                                     (filterPrioritas === 'rusak_berat' && <?= $rb > 0 ? 'true' : 'false' ?>) || 
                                     (filterPrioritas === 'rusak_ringan' && <?= $rr > 0 ? 'true' : 'false' ?>) || 
                                     (filterPrioritas === 'sedang' && <?= $sd > 0 ? 'true' : 'false' ?>))">
                            
                            <!-- Prioritas No / Badge -->
                            <td class="px-3 py-3 text-center font-bold text-xs">
                                <?php if ($rb > 0): ?>
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-red-600 text-white font-mono text-[11px] shadow-sm" title="Prioritas Utama (Ada Rusak Berat)">#<?= $i + 1 ?></span>
                                <?php elseif ($rr > 0): ?>
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-orange-500 text-white font-mono text-[11px]" title="Prioritas Menengah (Ada Rusak Ringan)">#<?= $i + 1 ?></span>
                                <?php elseif ($sd > 0): ?>
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-yellow-500 text-white font-mono text-[11px]" title="Prioritas Pemeliharaan (Kondisi Sedang)">#<?= $i + 1 ?></span>
                                <?php else: ?>
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-emerald-100 text-emerald-800 font-mono text-[11px]">#<?= $i + 1 ?></span>
                                <?php endif; ?>
                            </td>

                            <!-- STA -->
                            <td class="px-3 py-3 text-xs text-gray-700 text-center font-mono font-semibold"><?= meter_to_sta($sm['sta_awal']) ?></td>
                            <td class="px-3 py-3 text-xs text-gray-700 text-center font-mono font-semibold"><?= meter_to_sta($sm['sta_akhir']) ?></td>
                            <td class="px-3 py-3 text-xs text-gray-700 text-center font-bold"><?= format_number($sm['panjang']) ?> m</td>

                            <!-- Kondisi Jalan (Baik, Sedang, RR, RB) -->
                            <td class="px-3 py-3 text-xs text-center">
                                <span class="inline-flex px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 font-semibold"><?= format_number($bk) ?></span>
                            </td>
                            <td class="px-3 py-3 text-xs text-center">
                                <span class="inline-flex px-2 py-0.5 rounded-md bg-yellow-50 text-yellow-700 font-semibold"><?= format_number($sd) ?></span>
                            </td>
                            <td class="px-3 py-3 text-xs text-center">
                                <span class="inline-flex px-2 py-0.5 rounded-md bg-orange-50 text-orange-700 font-semibold"><?= format_number($rr) ?></span>
                            </td>
                            <td class="px-3 py-3 text-xs text-center">
                                <span class="inline-flex px-2 py-0.5 rounded-md <?= $rb > 0 ? 'bg-red-600 text-white font-bold animate-pulse' : 'bg-red-50 text-red-700 font-semibold' ?>">
                                    <?= format_number($rb) ?>
                                </span>
                            </td>

                            <!-- Status & Info Penanganan -->
                            <td class="px-4 py-3 text-xs">
                                <template x-if="activePn">
                                    <div class="space-y-1">
                                        <div class="flex items-center gap-1.5">
                                            <template x-if="activePn.status === 'rencana'">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-sky-100 text-sky-800 border border-sky-200">
                                                    Rencana (<span x-text="activePn.tahun"></span>)
                                                </span>
                                            </template>
                                            <template x-if="activePn.status === 'proses'">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-indigo-100 text-indigo-800 border border-indigo-200">
                                                    Sedang Dikerjakan (<span x-text="activePn.tahun"></span>)
                                                </span>
                                            </template>
                                            <template x-if="activePn.status === 'selesai'">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                                    Selesai (<span x-text="activePn.tahun"></span>)
                                                </span>
                                            </template>
                                            <span class="font-bold text-gray-900" x-text="activePn.jenis_penanganan"></span>
                                        </div>
                                        <template x-if="activePn.nama_paket">
                                            <div class="text-[11px] text-gray-600 truncate max-w-xs" x-text="activePn.nama_paket"></div>
                                        </template>
                                        <template x-if="activePn.anggaran && activePn.anggaran > 0">
                                            <div class="text-[11px] font-bold text-emerald-700">
                                                Rp <span x-text="Number(activePn.anggaran).toLocaleString('id-ID')"></span>
                                                <span x-show="activePn.sumber_dana" x-text="'(' + activePn.sumber_dana + ')'"></span>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                                <template x-if="!activePn">
                                    <div>
                                        <?php if ($rb > 0 || $rr > 0 || $sd > 0): ?>
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-semibold bg-amber-50 text-amber-800 border border-amber-200">
                                                <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                                <span x-text="filterTahun === 'all' ? 'Belum Ada Penanganan' : 'Belum Ada Penanganan (' + filterTahun + ')'"></span>
                                            </span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium text-gray-500 bg-gray-100">
                                                Kondisi Mantap (Baik)
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </template>
                            </td>

                            <!-- Opsi Penanganan (Aksi Langsung) -->
                            <td class="px-3 py-3 text-center">
                                <template x-if="activePn">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <template x-if="activePn.status === 'selesai'">
                                            <form method="POST" :action="'<?= base_url('penanganan/apply-kondisi/') ?>' + activePn.id" onsubmit="return confirm('Terapkan status penanganan selesai ini ke Kondisi Jalan (Stripmap)? Segmen STA ini akan diperbarui menjadi kondisi BAIK.')">
                                                <button type="submit" title="Terapkan hasil penanganan ke kondisi stripmap jalan (Kondisi Baik)" class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-bold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 rounded-lg transition-colors border border-emerald-200 shadow-sm">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                                    Terapkan
                                                </button>
                                            </form>
                                        </template>

                                        <button type="button" 
                                                @click="openEdit(activePn)"
                                                title="Edit Penanganan"
                                                class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors border border-blue-200">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            Edit
                                        </button>

                                        <form method="POST" :action="'<?= base_url('penanganan/delete/') ?>' + activePn.id" onsubmit="return confirm('Yakin ingin menghapus data penanganan ini?')">
                                            <button type="submit" title="Hapus Penanganan" class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium text-red-700 bg-red-50 hover:bg-red-100 rounded-lg transition-colors border border-red-200">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </template>
                                <template x-if="!activePn">
                                    <button type="button" 
                                            @click="openAddForSegment(<?= (float)$sm['sta_awal'] ?>, <?= (float)$sm['sta_akhir'] ?>, <?= (float)$rb ?>, <?= (float)$rr ?>, <?= (float)$sd ?>)"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 rounded-lg shadow-sm transition-all transform hover:-translate-y-0.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                        Atur Penanganan
                                    </button>
                                </template>
                            </td>

                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <!-- MODAL TAMBAH PENANGANAN (Bisa Dipicu dari Tombol 'Atur Penanganan' pada Segmen Rusak) -->
        <div x-show="isAddModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="isAddModalOpen" x-transition.opacity class="fixed inset-0 bg-gray-900 bg-opacity-60 transition-opacity" @click="isAddModalOpen = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div x-show="isAddModalOpen" x-transition class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full">
                    <form method="POST" action="<?= base_url('penanganan/store/' . $ruas['id']) ?>">
                        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between bg-gradient-to-r from-blue-600 to-indigo-600 text-white">
                            <h3 class="text-base font-bold flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                Atur Segmentasi Penanganan Jalan
                            </h3>
                            <button type="button" @click="isAddModalOpen = false" class="text-white/80 hover:text-white focus:outline-none">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                        <div class="p-6 space-y-4 max-h-[75vh] overflow-y-auto">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Tahun Anggaran *</label>
                                    <input type="number" name="tahun" x-model="addData.tahun" required min="2000" max="2100" class="w-full text-sm rounded-xl border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none font-semibold">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Status Pekerjaan *</label>
                                    <select name="status" x-model="addData.status" required class="w-full text-sm rounded-xl border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none font-semibold">
                                        <option value="rencana">Rencana / Usulan</option>
                                        <option value="proses">Sedang Dikerjakan (Proses)</option>
                                        <option value="selesai">Selesai Ditangani</option>
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">STA Awal (m) *</label>
                                    <input type="number" step="any" name="sta_awal" x-model="addData.sta_awal" required placeholder="0" class="w-full text-sm rounded-xl border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none font-mono">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">STA Akhir (m) *</label>
                                    <input type="number" step="any" name="sta_akhir" x-model="addData.sta_akhir" required placeholder="1000" class="w-full text-sm rounded-xl border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none font-mono">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Panjang (m)</label>
                                    <input type="number" step="any" name="panjang" x-model="addData.panjang" placeholder="Otomatis" class="w-full text-sm rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 font-mono text-gray-500">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Jenis Penanganan *</label>
                                <select name="jenis_penanganan" x-model="addData.jenis_penanganan" required class="w-full text-sm rounded-xl border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none font-semibold">
                                    <option value="Rekonstruksi Jalan">Rekonstruksi Jalan (Untuk Rusak Berat)</option>
                                    <option value="Rehabilitasi Jalan">Rehabilitasi Jalan (Untuk Rusak Ringan)</option>
                                    <option value="Pemeliharaan Berkala">Pemeliharaan Berkala (Untuk Kondisi Sedang)</option>
                                    <option value="Pemeliharaan Rutin">Pemeliharaan Rutin</option>
                                    <option value="Peningkatan Struktur">Peningkatan Struktur</option>
                                    <option value="Penggantian Jembatan / Drainase">Penggantian Jembatan / Drainase</option>
                                    <option value="Pelebaran Jalan">Pelebaran Jalan</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-purple-700 uppercase tracking-wider mb-1">
                                    Jenis Pelaksana (Matriks Prediksi)
                                    <span class="ml-1 text-[10px] font-normal text-purple-400 normal-case">&mdash; untuk perhitungan prediksi kondisi</span>
                                </label>
                                <select name="jenis_pelaksana" x-model="addData.jenis_pelaksana"
                                    class="w-full text-sm rounded-xl border border-purple-300 bg-purple-50/30 px-3 py-2 focus:ring-2 focus:ring-purple-500 focus:outline-none font-semibold text-purple-900">
                                    <option value="">&mdash; Tidak Dipilih (tidak dihitung) &mdash;</option>
                                    <option value="pihak_ke3_rigid">Pihak Ke-3 (Rigid)</option>
                                    <option value="pihak_ke3_aspal">Pihak Ke-3 (Aspal)</option>
                                    <option value="rutin_uptd">Rutin UPTD</option>
                                    <option value="urc_overlay_tanpa_finisher">URC UPTD &mdash; Overlay Tanpa Finisher</option>
                                    <option value="urc_overlay_dengan_finisher">URC UPTD &mdash; Overlay Dengan Finisher</option>
                                    <option value="urc_rigid">URC UPTD &mdash; Rigid</option>
                                    <option value="urc_base">URC UPTD &mdash; Base</option>
                                </select>
                                <p class="text-[10px] text-purple-500 mt-1">Pilih pelaksana sesuai matriks strip map agar prediksi kondisi dihitung otomatis.</p>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Nama Paket Pekerjaan</label>
                                    <input type="text" name="nama_paket" x-model="addData.nama_paket" placeholder="Contoh: Rekonstruksi Ruas X" class="w-full text-sm rounded-xl border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Sumber Dana</label>
                                    <input type="text" name="sumber_dana" x-model="addData.sumber_dana" placeholder="APBD Provinsi / DAK / APBN" class="w-full text-sm rounded-xl border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Nilai Anggaran (Rp)</label>
                                    <input type="number" step="any" name="anggaran" x-model="addData.anggaran" placeholder="Contoh: 1500000000" class="w-full text-sm rounded-xl border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none font-mono">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Warna Penanda</label>
                                    <div class="flex items-center gap-2">
                                        <input type="color" name="warna" x-model="addData.warna" class="w-10 h-10 rounded-lg cursor-pointer border-0 p-0">
                                        <span class="text-xs text-gray-500">Pilih warna khusus atau gunakan warna status</span>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Keterangan Tambahan</label>
                                <textarea name="keterangan" x-model="addData.keterangan" rows="2" placeholder="Catatan teknis..." class="w-full text-sm rounded-xl border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"></textarea>
                            </div>
                        </div>
                        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-end gap-3">
                            <button type="button" @click="isAddModalOpen = false" class="px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-200 rounded-xl transition-colors">Batal</button>
                            <button type="submit" class="px-5 py-2 text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 rounded-xl shadow-md transition-colors">Simpan Penanganan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- MODAL EDIT PENANGANAN -->
        <div x-show="isEditModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="isEditModalOpen" x-transition.opacity class="fixed inset-0 bg-gray-900 bg-opacity-60 transition-opacity" @click="isEditModalOpen = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div x-show="isEditModalOpen" x-transition class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full">
                    <form method="POST" :action="'<?= base_url('penanganan/update/') ?>' + editData.id">
                        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between bg-gradient-to-r from-blue-700 to-indigo-700 text-white">
                            <h3 class="text-base font-bold flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                Edit Data Segmentasi Penanganan
                            </h3>
                            <button type="button" @click="isEditModalOpen = false" class="text-white/80 hover:text-white focus:outline-none">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                        <div class="p-6 space-y-4 max-h-[75vh] overflow-y-auto">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Tahun Anggaran *</label>
                                    <input type="number" name="tahun" x-model="editData.tahun" required min="2000" max="2100" class="w-full text-sm rounded-xl border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none font-semibold">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Status Pekerjaan *</label>
                                    <select name="status" x-model="editData.status" required class="w-full text-sm rounded-xl border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none font-semibold">
                                        <option value="rencana">Rencana / Usulan</option>
                                        <option value="proses">Sedang Dikerjakan (Proses)</option>
                                        <option value="selesai">Selesai Ditangani</option>
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">STA Awal (m) *</label>
                                    <input type="number" step="any" name="sta_awal" x-model="editData.sta_awal" required class="w-full text-sm rounded-xl border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none font-mono">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">STA Akhir (m) *</label>
                                    <input type="number" step="any" name="sta_akhir" x-model="editData.sta_akhir" required class="w-full text-sm rounded-xl border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none font-mono">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Panjang (m)</label>
                                    <input type="number" step="any" name="panjang" x-model="editData.panjang" class="w-full text-sm rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 font-mono text-gray-500">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Jenis Penanganan *</label>
                                <select name="jenis_penanganan" x-model="editData.jenis_penanganan" required class="w-full text-sm rounded-xl border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none font-semibold">
                                    <option value="Rekonstruksi Jalan">Rekonstruksi Jalan</option>
                                    <option value="Rehabilitasi Jalan">Rehabilitasi Jalan</option>
                                    <option value="Pemeliharaan Berkala">Pemeliharaan Berkala</option>
                                    <option value="Pemeliharaan Rutin">Pemeliharaan Rutin</option>
                                    <option value="Peningkatan Struktur">Peningkatan Struktur</option>
                                    <option value="Penggantian Jembatan / Drainase">Penggantian Jembatan / Drainase</option>
                                    <option value="Pelebaran Jalan">Pelebaran Jalan</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-purple-700 uppercase tracking-wider mb-1">
                                    Jenis Pelaksana (Matriks Prediksi)
                                    <span class="ml-1 text-[10px] font-normal text-purple-400 normal-case">&mdash; untuk perhitungan prediksi kondisi</span>
                                </label>
                                <select name="jenis_pelaksana" x-model="editData.jenis_pelaksana"
                                    class="w-full text-sm rounded-xl border border-purple-300 bg-purple-50/30 px-3 py-2 focus:ring-2 focus:ring-purple-500 focus:outline-none font-semibold text-purple-900">
                                    <option value="">&mdash; Tidak Dipilih (tidak dihitung) &mdash;</option>
                                    <option value="pihak_ke3_rigid">Pihak Ke-3 (Rigid)</option>
                                    <option value="pihak_ke3_aspal">Pihak Ke-3 (Aspal)</option>
                                    <option value="rutin_uptd">Rutin UPTD</option>
                                    <option value="urc_overlay_tanpa_finisher">URC UPTD &mdash; Overlay Tanpa Finisher</option>
                                    <option value="urc_overlay_dengan_finisher">URC UPTD &mdash; Overlay Dengan Finisher</option>
                                    <option value="urc_rigid">URC UPTD &mdash; Rigid</option>
                                    <option value="urc_base">URC UPTD &mdash; Base</option>
                                </select>
                                <p class="text-[10px] text-purple-500 mt-1">Pilih pelaksana sesuai matriks strip map agar prediksi kondisi dihitung otomatis.</p>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Nama Paket Pekerjaan</label>
                                    <input type="text" name="nama_paket" x-model="editData.nama_paket" class="w-full text-sm rounded-xl border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Sumber Dana</label>
                                    <input type="text" name="sumber_dana" x-model="editData.sumber_dana" class="w-full text-sm rounded-xl border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Nilai Anggaran (Rp)</label>
                                    <input type="number" step="any" name="anggaran" x-model="editData.anggaran" class="w-full text-sm rounded-xl border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none font-mono">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Warna Penanda</label>
                                    <div class="flex items-center gap-2">
                                        <input type="color" name="warna" x-model="editData.warna" class="w-10 h-10 rounded-lg cursor-pointer border-0 p-0">
                                        <span class="text-xs text-gray-500">Pilih warna khusus atau biarkan sesuai status</span>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Keterangan Tambahan</label>
                                <textarea name="keterangan" x-model="editData.keterangan" rows="2" class="w-full text-sm rounded-xl border border-gray-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"></textarea>
                            </div>
                        </div>
                        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-end gap-3">
                            <button type="button" @click="isEditModalOpen = false" class="px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-200 rounded-xl transition-colors">Batal</button>
                            <button type="submit" class="px-5 py-2 text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 rounded-xl shadow-md transition-colors">Perbarui Penanganan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>

    <?php if (empty($stripmaps) && empty($perkerasans) && empty($penanganans)): ?>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2z"/>
        </svg>
        <h3 class="text-lg font-semibold text-gray-600 mb-2">Belum ada data strip map, perkerasan & penanganan</h3>
        <p class="text-sm text-gray-500 mb-6">Tambahkan segmen pertama untuk ruas ini.</p>
        <a href="<?= base_url('stripmap/create/' . $ruas['id']) ?>"
           class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-xl hover:bg-blue-700 transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Segmen Data
        </a>
    </div>
    <?php endif; ?>

</div>

<!-- JSZip for KMZ extraction & Direct KML Import Handler -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script>
async function handleDirectKmlImport(e, ruasId) {
    const file = e.target.files[0];
    if (!file) return;
    const name = file.name.toLowerCase();
    try {
        let kmlText;
        if (name.endsWith('.kmz')) {
            if (typeof JSZip === 'undefined') {
                alert('Pustaka JSZip belum termuat.');
                return;
            }
            const zip = await JSZip.loadAsync(await file.arrayBuffer());
            const kmlEntry = Object.keys(zip.files).find(f => f.toLowerCase().endsWith('.kml'));
            if (!kmlEntry) { alert('File KMZ tidak berisi file .kml.'); return; }
            kmlText = await zip.files[kmlEntry].async('string');
        } else if (name.endsWith('.kml')) {
            kmlText = await file.text();
        } else {
            alert('Format file harus .kml atau .kmz');
            return;
        }

        const coords = parseKmlRouteText(kmlText); // [[lat, lng], ...]
        if (coords.length < 2) {
            alert('Tidak ditemukan garis rute (LineString) yang valid di dalam file KML/KMZ.');
            return;
        }

        const first = coords[0];
        const last = coords[coords.length - 1];

        document.getElementById('kml_koordinat_json').value = JSON.stringify(coords.map(p => [p[1], p[0]]));
        document.getElementById('kml_lat_awal').value = Math.round(first[0] * 1e7) / 1e7;
        document.getElementById('kml_lng_awal').value = Math.round(first[1] * 1e7) / 1e7;
        document.getElementById('kml_lat_akhir').value = Math.round(last[0] * 1e7) / 1e7;
        document.getElementById('kml_lng_akhir').value = Math.round(last[1] * 1e7) / 1e7;

        document.getElementById('direct-kml-form').submit();
    } catch (err) {
        console.error('Import KML error:', err);
        alert('Gagal membaca file. Pastikan file KML/KMZ valid.');
    }
}

function parseKmlRouteText(text) {
    const doc = new DOMParser().parseFromString(text, 'application/xml');
    const lines = [];

    const lineNodes = doc.querySelectorAll('LineString coordinates, linestring coordinates');
    lineNodes.forEach(node => {
        const pts = parseSingleCoordString(node.textContent);
        if (pts.length >= 2) lines.push(pts);
    });

    if (lines.length === 0) {
        const trackNodes = doc.querySelectorAll('Track, gx\\:Track');
        trackNodes.forEach(tnode => {
            const coordNodes = tnode.querySelectorAll('coord, gx\\:coord');
            let pts = [];
            coordNodes.forEach(c => {
                const parts = c.textContent.trim().split(/\s+/);
                const lng = parseFloat(parts[0]);
                const lat = parseFloat(parts[1]);
                if (!isNaN(lat) && !isNaN(lng)) pts.push([lat, lng]);
            });
            if (pts.length >= 2) lines.push(pts);
        });
    }

    if (lines.length === 0) {
        const allCoordNodes = doc.getElementsByTagName('coordinates');
        for (let i = 0; i < allCoordNodes.length; i++) {
            const pts = parseSingleCoordString(allCoordNodes[i].textContent);
            if (pts.length >= 2) lines.push(pts);
        }
    }

    if (lines.length === 0) return [];
    lines.sort((a, b) => b.length - a.length);
    return lines[0];
}

function parseSingleCoordString(raw) {
    if (!raw) return [];
    let points = [];
    raw.trim().split(/\s+/).forEach(tuple => {
        const parts = tuple.split(',');
        const lng = parseFloat(parts[0]);
        const lat = parseFloat(parts[1]);
        if (!isNaN(lat) && !isNaN(lng)) points.push([lat, lng]);
    });
    return points;
}
</script>

<?php require __DIR__ . '/_modal_foto.php'; ?>

