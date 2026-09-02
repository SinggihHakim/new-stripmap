<!-- ============================================================ -->
<!-- Form Tambah / Edit Ruas Jalan (Beserta Strip Map & Perkerasan)-->
<!-- ============================================================ -->

<?php
    $isEdit  = isset($ruas);
    $action  = $isEdit ? base_url('ruas/update/' . $ruas['id']) : base_url('ruas/store');
    $heading = $isEdit ? 'Edit Ruas Jalan' : 'Tambah Ruas Jalan';

    $oldRows           = $_SESSION['old_rows'] ?? null;
    $oldPerkerasanRows = $_SESSION['old_perkerasan_rows'] ?? null;
    if ($oldRows) unset($_SESSION['old_rows']);
    if ($oldPerkerasanRows) unset($_SESSION['old_perkerasan_rows']);
?>

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
                <h1 class="text-2xl font-bold text-gray-900"><?= $heading ?></h1>
                <p class="mt-1 text-sm text-gray-500">
                    <?= $isEdit ? 'Perbarui data ruas jalan, strip map, dan jenis perkerasan.' : 'Isi form berikut untuk menambahkan ruas jalan baru beserta kondisi strip map dan jenis perkerasan.' ?>
                </p>
            </div>
        </div>
        <?php if (!$isEdit): ?>
            <div>
                <a href="<?= base_url('ruas/import') ?>"
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 text-white text-sm font-semibold rounded-xl hover:bg-emerald-700 transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                    Import Excel
                </a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Form Card -->
    <div x-data="ruasDanStripmapForm()">
        <form action="<?= $action ?>" method="POST" class="space-y-6" @submit="validateForm($event)">

            <!-- SECTION 1: DATA UTAMA RUAS JALAN -->
            <div x-data="{ isOpen: true }" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50/50 flex justify-between items-center cursor-pointer select-none" @click="isOpen = !isOpen">
                    <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <span class="w-7 h-7 rounded-lg bg-gray-200 text-gray-700 flex items-center justify-center text-xs font-bold">1</span>
                        Data Utama Ruas Jalan
                    </h2>
                    <button type="button" class="text-gray-500 hover:text-gray-700 transition-transform duration-200" :class="isOpen ? 'rotate-90' : 'rotate-0'">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
                <div x-show="isOpen" x-collapse class="p-6 space-y-6">
                    <!-- Kode Ruas -->
                    <div>
                        <label for="kode_ruas" class="block text-sm font-medium text-gray-700 mb-1.5">Kode Ruas</label>
                        <input type="text" id="kode_ruas" name="kode_ruas"
                               value="<?= e($isEdit ? $ruas['kode_ruas'] : old('kode_ruas')) ?>"
                               placeholder="Contoh: RJ-001"
                               class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                               required>
                    </div>

                    <!-- Nama Ruas -->
                    <div>
                        <label for="nama_ruas" class="block text-sm font-medium text-gray-700 mb-1.5">Nama Ruas</label>
                        <input type="text" id="nama_ruas" name="nama_ruas"
                               value="<?= e($isEdit ? $ruas['nama_ruas'] : old('nama_ruas')) ?>"
                               placeholder="Contoh: Jl. Ahmad Yani"
                               class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                               required>
                    </div>

                    <!-- Koridor -->
                    <div>
                        <label for="koridor" class="block text-sm font-medium text-gray-700 mb-1.5">Koridor</label>
                        <input type="text" id="koridor" name="koridor"
                               value="<?= e($isEdit ? $ruas['koridor'] : old('koridor')) ?>"
                               placeholder="Contoh: Koridor I"
                               class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                    </div>

                    <!-- Kabupaten / Kota -->
                    <div>
                        <label for="kabupaten_kota" class="block text-sm font-medium text-gray-700 mb-1.5">Kabupaten / Kota</label>
                        <input type="text" id="kabupaten_kota" name="kabupaten_kota"
                               value="<?= e($isEdit ? $ruas['kabupaten_kota'] : old('kabupaten_kota')) ?>"
                               placeholder="Contoh: Lampung Selatan"
                               class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                    </div>

                    <!-- Koordinat Lokasi Peta -->
                    <div x-data="ruasMapPicker()" class="pt-2">
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-sm font-medium text-gray-700">Koordinat Lokasi (Titik Awal &amp; Akhir)</label>
                            <span class="text-xs text-gray-400">Opsional &mdash; untuk peta &amp; street view</span>
                        </div>
                        <p class="text-xs text-gray-500 mb-3">
                            Klik peta untuk menaruh <span class="font-semibold text-emerald-600">Titik Awal</span> (klik pertama), lalu
                            <span class="font-semibold text-red-600">Titik Akhir</span> (klik kedua). Marker bisa digeser. Atau isi manual di bawah.
                        </p>

                        <div id="ruas-map-picker" class="w-full h-72 rounded-xl border border-gray-300 z-0 mb-3"></div>

                        <!-- Polyline hasil impor KML/KMZ (JSON) -->
                        <input type="hidden" name="koordinat_json" x-model="koordinatJson">
                        <p x-show="pointCount > 0" x-cloak class="mb-3 text-xs font-medium text-purple-700 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            <span>Rute berhasil dimuat: <span x-text="pointCount"></span> titik koordinat dari file.</span>
                        </p>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="space-y-2 p-3 rounded-xl bg-emerald-50/60 border border-emerald-100">
                                <p class="text-xs font-semibold text-emerald-700 flex items-center gap-1.5">
                                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 inline-block"></span> Titik Awal
                                </p>
                                <div class="grid grid-cols-2 gap-2">
                                    <input type="text" name="lat_awal" x-model="latAwal" @input="syncFromInputs()" placeholder="Latitude"
                                           class="w-full px-3 py-2 rounded-lg border border-gray-300 font-mono text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                                    <input type="text" name="lng_awal" x-model="lngAwal" @input="syncFromInputs()" placeholder="Longitude"
                                           class="w-full px-3 py-2 rounded-lg border border-gray-300 font-mono text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                                </div>
                            </div>
                            <div class="space-y-2 p-3 rounded-xl bg-red-50/60 border border-red-100">
                                <p class="text-xs font-semibold text-red-700 flex items-center gap-1.5">
                                    <span class="w-2.5 h-2.5 rounded-full bg-red-500 inline-block"></span> Titik Akhir
                                </p>
                                <div class="grid grid-cols-2 gap-2">
                                    <input type="text" name="lat_akhir" x-model="latAkhir" @input="syncFromInputs()" placeholder="Latitude"
                                           class="w-full px-3 py-2 rounded-lg border border-gray-300 font-mono text-xs focus:ring-2 focus:ring-red-500 focus:border-red-500">
                                    <input type="text" name="lng_akhir" x-model="lngAkhir" @input="syncFromInputs()" placeholder="Longitude"
                                           class="w-full px-3 py-2 rounded-lg border border-gray-300 font-mono text-xs focus:ring-2 focus:ring-red-500 focus:border-red-500">
                                </div>
                            </div>
                        </div>

                        <div class="mt-3 flex flex-wrap gap-2">
                            <label class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-purple-700 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors cursor-pointer">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                Import KML / KMZ
                                <input type="file" accept=".kml,.kmz" class="hidden" @change="importKml($event)">
                            </label>
                            <button type="button" @click="useMyLocation()"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-blue-700 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                Pakai Lokasi Saya (Titik Awal)
                            </button>
                            <button type="button" @click="clearPoints()"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                Reset Titik
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION 2: DATA STRIP MAP (Kondisi Jalan) -->
            <div x-data="{ isOpen: <?= $isEdit ? 'false' : 'true' ?> }" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50/50 flex items-center justify-between cursor-pointer select-none" @click="isOpen = !isOpen">
                    <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <span class="w-7 h-7 rounded-lg bg-gray-200 text-gray-700 flex items-center justify-center text-xs font-bold">2</span>
                        Form Input Kondisi Jalan (Strip Map)
                    </h2>
                    <button type="button" class="text-gray-500 hover:text-gray-700 transition-transform duration-200" :class="isOpen ? 'rotate-90' : 'rotate-0'">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>

                <div x-show="isOpen" x-collapse class="p-6">
                    <div class="overflow-x-auto border rounded-lg mb-4">
                        <table class="w-full text-sm text-left">
                            <thead class="text-xs text-gray-700 bg-gray-50 border-b">
                                <tr>
                                    <th class="px-3 py-3 w-32">STA Awal</th>
                                    <th class="px-3 py-3 w-32">STA Akhir</th>
                                    <th class="px-3 py-3 w-24">Panjang</th>
                                    <th class="px-3 py-3 text-emerald-800">Baik (m)</th>
                                    <th class="px-3 py-3 text-yellow-800">Sedang (m)</th>
                                    <th class="px-3 py-3 text-orange-800">R. Ringan (m)</th>
                                    <th class="px-3 py-3 text-red-800">R. Berat (m)</th>
                                    <th class="px-3 py-3 w-16 text-center">Status</th>
                                    <th class="px-3 py-3 w-16 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(row, index) in rows" :key="row.id">
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="p-2">
                                            <input type="text" :name="`rows[${index}][sta_awal]`" x-model="row.staAwal" @blur="row.staAwal = formatStaValue(row.staAwal); calculateRow(row)" @input="onStaInput($event, row, 'awal')" placeholder="0+000" class="w-full px-2 py-1.5 rounded border border-gray-300 font-mono text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        </td>
                                        <td class="p-2">
                                            <input type="text" :name="`rows[${index}][sta_akhir]`" x-model="row.staAkhir" @blur="row.staAkhir = formatStaValue(row.staAkhir); calculateRow(row)" @input="onStaInput($event, row, 'akhir')" placeholder="1+000" class="w-full px-2 py-1.5 rounded border border-gray-300 font-mono text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        </td>
                                        <td class="p-2">
                                            <div class="font-mono font-semibold px-2" :class="row.error ? 'text-red-600' : 'text-gray-700'" x-text="row.panjang > 0 ? formatNumber(row.panjang) : '-'"></div>
                                        </td>
                                        <td class="p-2">
                                            <input type="number" :name="`rows[${index}][baik]`" x-model.number="row.baik" @input="calculateRow(row)" min="0" step="0.01" class="w-full px-2 py-1.5 rounded border border-gray-300 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                                        </td>
                                        <td class="p-2">
                                            <input type="number" :name="`rows[${index}][sedang]`" x-model.number="row.sedang" @input="calculateRow(row)" min="0" step="0.01" class="w-full px-2 py-1.5 rounded border border-gray-300 text-sm focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500">
                                        </td>
                                        <td class="p-2">
                                            <input type="number" :name="`rows[${index}][rusak_ringan]`" x-model.number="row.rusakRingan" @input="calculateRow(row)" min="0" step="0.01" class="w-full px-2 py-1.5 rounded border border-gray-300 text-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                                        </td>
                                        <td class="p-2">
                                            <input type="number" :name="`rows[${index}][rusak_berat]`" x-model.number="row.rusakBerat" @input="calculateRow(row)" min="0" step="0.01" class="w-full px-2 py-1.5 rounded border border-gray-300 text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500">
                                        </td>
                                        <td class="p-2 text-center">
                                            <div class="flex justify-center" :title="row.error || 'Valid'">
                                                <svg x-show="row.isValid" class="w-5 h-5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                                <svg x-show="!row.isValid && !isRowEmpty(row)" class="w-5 h-5 text-red-500 cursor-help" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                                </svg>
                                            </div>
                                        </td>
                                        <td class="p-2 text-center">
                                            <button type="button" @click="removeRow(index)" x-show="rows.length > 1" class="text-red-500 hover:text-red-700 p-1 rounded hover:bg-red-50 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>

                    <div class="flex justify-start">
                        <button type="button" @click="addRow()" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-blue-700 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                            </svg>
                            Tambah Baris Kondisi
                        </button>
                    </div>

                    <!-- Error Messages Summary (Strip Map) -->
                    <div x-show="formErrors.length > 0" class="mt-4 p-4 rounded-xl bg-red-50 border border-red-200">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-red-500 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            <div>
                                <h4 class="text-sm font-medium text-red-800">Terdapat error pada input Strip Map:</h4>
                                <ul class="mt-1 text-sm text-red-700 list-disc list-inside">
                                    <template x-for="err in formErrors" :key="err">
                                        <li x-text="err"></li>
                                    </template>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION 3: DATA JENIS PERKERASAN JALAN -->
            <div x-data="{ isOpen: <?= $isEdit ? 'false' : 'true' ?> }" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50/50 flex items-center justify-between cursor-pointer select-none" @click="isOpen = !isOpen">
                    <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <span class="w-7 h-7 rounded-lg bg-gray-200 text-gray-700 flex items-center justify-center text-xs font-bold">3</span>
                        Form Input Jenis Perkerasan Jalan
                    </h2>
                    <button type="button" class="text-gray-500 hover:text-gray-700 transition-transform duration-200" :class="isOpen ? 'rotate-90' : 'rotate-0'">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>

                <div x-show="isOpen" x-collapse class="p-6">
                    <div class="overflow-x-auto border rounded-lg mb-4">
                        <table class="w-full text-sm text-left">
                            <thead class="text-xs text-gray-700 bg-gray-50 border-b">
                                <tr>
                                    <th class="px-3 py-3 w-32">STA Awal</th>
                                    <th class="px-3 py-3 w-32">STA Akhir</th>
                                    <th class="px-3 py-3 w-24">Panjang</th>
                                    <th class="px-3 py-3 text-gray-700">Rigid (m)</th>
                                    <th class="px-3 py-3 text-slate-900">Aspal (m)</th>
                                    <th class="px-3 py-3 text-amber-800">Kerikil (m)</th>
                                    <th class="px-3 py-3 text-purple-700">Belum Tembus (m)</th>
                                    <th class="px-3 py-3 w-16 text-center">Status</th>
                                    <th class="px-3 py-3 w-16 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(row, index) in pkRows" :key="row.id">
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="p-2">
                                            <input type="text" :name="`perkerasan_rows[${index}][sta_awal]`" x-model="row.staAwal" @blur="row.staAwal = formatStaValue(row.staAwal); calculatePkRow(row)" @input="onStaInput($event, row, 'awal')" placeholder="0+000" class="w-full px-2 py-1.5 rounded border border-gray-300 font-mono text-sm focus:ring-2 focus:ring-amber-500">
                                        </td>
                                        <td class="p-2">
                                            <input type="text" :name="`perkerasan_rows[${index}][sta_akhir]`" x-model="row.staAkhir" @blur="row.staAkhir = formatStaValue(row.staAkhir); calculatePkRow(row)" @input="onStaInput($event, row, 'akhir')" placeholder="1+000" class="w-full px-2 py-1.5 rounded border border-gray-300 font-mono text-sm focus:ring-2 focus:ring-amber-500">
                                        </td>
                                        <td class="p-2">
                                            <div class="font-mono font-semibold px-2" :class="row.error ? 'text-red-600' : 'text-gray-700'" x-text="row.panjang > 0 ? formatNumber(row.panjang) : '-'"></div>
                                        </td>
                                        <td class="p-2">
                                            <input type="number" :name="`perkerasan_rows[${index}][rigid]`" x-model.number="row.rigid" @input="calculatePkRow(row)" min="0" step="0.01" class="w-full px-2 py-1.5 rounded border border-gray-300 text-sm focus:ring-2 focus:ring-gray-500">
                                        </td>
                                        <td class="p-2">
                                            <input type="number" :name="`perkerasan_rows[${index}][aspal]`" x-model.number="row.aspal" @input="calculatePkRow(row)" min="0" step="0.01" class="w-full px-2 py-1.5 rounded border border-gray-300 text-sm focus:ring-2 focus:ring-slate-900">
                                        </td>
                                        <td class="p-2">
                                            <input type="number" :name="`perkerasan_rows[${index}][agregat_tanah]`" x-model.number="row.agregatTanah" @input="calculatePkRow(row)" min="0" step="0.01" class="w-full px-2 py-1.5 rounded border border-gray-300 text-sm focus:ring-2 focus:ring-amber-700">
                                        </td>
                                        <td class="p-2">
                                            <input type="number" :name="`perkerasan_rows[${index}][belum_tembus]`" x-model.number="row.belumTembus" @input="calculatePkRow(row)" min="0" step="0.01" class="w-full px-2 py-1.5 rounded border border-gray-300 text-sm focus:ring-2 focus:ring-purple-600">
                                        </td>
                                        <td class="p-2 text-center">
                                            <div class="flex justify-center" :title="row.error || 'Valid'">
                                                <svg x-show="row.isValid" class="w-5 h-5 text-amber-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                                <svg x-show="!row.isValid && !isPkRowEmpty(row)" class="w-5 h-5 text-red-500 cursor-help" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                            </div>
                                        </td>
                                        <td class="p-2 text-center">
                                            <button type="button" @click="removePkRow(index)" x-show="pkRows.length > 1" class="text-red-500 hover:text-red-700 p-1 rounded hover:bg-red-50 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>

                    <div class="flex justify-start">
                        <button type="button" @click="addPkRow()" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-amber-800 bg-amber-50 rounded-lg hover:bg-amber-100 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                            </svg>
                            Tambah Baris Perkerasan
                        </button>
                    </div>

                    <!-- Error Messages Summary (Perkerasan) -->
                    <div x-show="pkFormErrors.length > 0" class="mt-4 p-4 rounded-xl bg-red-50 border border-red-200">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-red-500 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            <div>
                                <h4 class="text-sm font-medium text-red-800">Terdapat error pada input Perkerasan:</h4>
                                <ul class="mt-1 text-sm text-red-700 list-disc list-inside">
                                    <template x-for="err in pkFormErrors" :key="err">
                                        <li x-text="err"></li>
                                    </template>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200 flex items-center justify-end gap-3 sticky bottom-4 z-10">
                <a href="<?= base_url('ruas') ?>"
                   class="inline-flex items-center gap-2 px-6 py-2.5 bg-gray-100 text-gray-700 text-sm font-medium rounded-xl hover:bg-gray-200 transition-colors">
                    Batal
                </a>
                <button type="submit"
                        :disabled="!isReadyToSubmit"
                        :class="isReadyToSubmit ? 'bg-blue-600 hover:bg-blue-700' : 'bg-gray-300 cursor-not-allowed'"
                        class="inline-flex items-center gap-2 px-6 py-2.5 text-white text-sm font-medium rounded-xl transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    <?= $isEdit ? 'Perbarui Data Ruas' : 'Simpan Semua Data' ?>
                </button>
            </div>

        </form>
    </div>

</div>

<!-- Leaflet (peta pemilih koordinat) -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<!-- JSZip (untuk baca file KMZ) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

<script>
function ruasMapPicker() {
    const initial = <?= json_encode([
        'lat_awal'  => $isEdit ? ($ruas['lat_awal']  ?? null) : (old('lat_awal')  ?: null),
        'lng_awal'  => $isEdit ? ($ruas['lng_awal']  ?? null) : (old('lng_awal')  ?: null),
        'lat_akhir' => $isEdit ? ($ruas['lat_akhir'] ?? null) : (old('lat_akhir') ?: null),
        'lng_akhir' => $isEdit ? ($ruas['lng_akhir'] ?? null) : (old('lng_akhir') ?: null),
        'koordinat_json' => $isEdit ? ($ruas['koordinat_json'] ?? '') : (old('koordinat_json') ?: ''),
    ]) ?>;

    return {
        latAwal:  initial.lat_awal  ?? '',
        lngAwal:  initial.lng_awal  ?? '',
        latAkhir: initial.lat_akhir ?? '',
        lngAkhir: initial.lng_akhir ?? '',
        koordinatJson: initial.koordinat_json ?? '',
        pointCount: 0,
        map: null,
        markerAwal: null,
        markerAkhir: null,
        line: null,
        routeLine: null,

        init() {
            const googleHybrid = L.tileLayer('https://mt1.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
                maxZoom: 20, attribution: '&copy; Google Maps'
            });
            const googleSat = L.tileLayer('https://mt1.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', {
                maxZoom: 20, attribution: '&copy; Google Maps'
            });
            const googleStreets = L.tileLayer('https://mt1.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
                maxZoom: 20, attribution: '&copy; Google Maps'
            });
            const osm = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19, attribution: '&copy; OpenStreetMap'
            });

            const defaultCenter = [-5.45, 105.26];
            this.map = L.map('ruas-map-picker', {
                center: defaultCenter,
                zoom: 9,
                layers: [googleHybrid]
            });

            L.control.layers({
                "🛰️ Google Satelit Hybrid": googleHybrid,
                "📷 Google Satelit Murni": googleSat,
                "🗺️ Google Streets": googleStreets,
                "🌐 OpenStreetMap": osm
            }, null, { position: 'topright' }).addTo(this.map);

            // Klik peta: isi titik awal dulu, lalu titik akhir
            this.map.on('click', (e) => {
                if (!this.latAwal || !this.lngAwal) {
                    this.setAwal(e.latlng.lat, e.latlng.lng);
                } else {
                    this.setAkhir(e.latlng.lat, e.latlng.lng);
                }
            });

            this.syncFromInputs();
            this.drawRoute(); // gambar polyline jika sudah ada koordinat_json (mode edit)
            // Perbaiki ukuran peta setelah section render
            setTimeout(() => this.map.invalidateSize(), 200);
        },

        round(v) { return Math.round(v * 1e7) / 1e7; },

        setAwal(lat, lng) {
            this.latAwal = this.round(lat);
            this.lngAwal = this.round(lng);
            this.renderMarkers();
        },
        setAkhir(lat, lng) {
            this.latAkhir = this.round(lat);
            this.lngAkhir = this.round(lng);
            this.renderMarkers();
        },

        // Dipanggil saat input teks diubah manual
        syncFromInputs() {
            this.renderMarkers();
        },

        makeIcon(color) {
            return L.divIcon({
                className: '',
                html: `<div style="width:16px;height:16px;border-radius:50%;background:${color};border:3px solid #fff;box-shadow:0 0 0 1px rgba(0,0,0,.3)"></div>`,
                iconSize: [16, 16],
                iconAnchor: [8, 8],
            });
        },

        renderMarkers() {
            if (!this.map) return;
            const a = this.parsePoint(this.latAwal, this.lngAwal);
            const b = this.parsePoint(this.latAkhir, this.lngAkhir);

            // Marker Awal
            if (a) {
                if (!this.markerAwal) {
                    this.markerAwal = L.marker(a, { draggable: true, icon: this.makeIcon('#10b981') }).addTo(this.map);
                    this.markerAwal.on('dragend', (e) => { const p = e.target.getLatLng(); this.setAwal(p.lat, p.lng); });
                    this.markerAwal.bindPopup('Titik Awal');
                } else {
                    this.markerAwal.setLatLng(a);
                }
            } else if (this.markerAwal) {
                this.map.removeLayer(this.markerAwal); this.markerAwal = null;
            }

            // Marker Akhir
            if (b) {
                if (!this.markerAkhir) {
                    this.markerAkhir = L.marker(b, { draggable: true, icon: this.makeIcon('#ef4444') }).addTo(this.map);
                    this.markerAkhir.on('dragend', (e) => { const p = e.target.getLatLng(); this.setAkhir(p.lat, p.lng); });
                    this.markerAkhir.bindPopup('Titik Akhir');
                } else {
                    this.markerAkhir.setLatLng(b);
                }
            } else if (this.markerAkhir) {
                this.map.removeLayer(this.markerAkhir); this.markerAkhir = null;
            }

            // Garis penghubung lurus (disembunyikan bila ada rute polyline dari KML)
            if (this.line) { this.map.removeLayer(this.line); this.line = null; }
            if (a && b && !this.koordinatJson) {
                this.line = L.polyline([a, b], { color: '#3b82f6', weight: 4, opacity: 0.7 }).addTo(this.map);
                this.map.fitBounds(this.line.getBounds(), { padding: [40, 40] });
            } else if (a && !b) {
                this.map.setView(a, 14);
            } else if (b && !a) {
                this.map.setView(b, 14);
            }
        },

        parsePoint(lat, lng) {
            const la = parseFloat(String(lat).replace(',', '.'));
            const ln = parseFloat(String(lng).replace(',', '.'));
            if (isNaN(la) || isNaN(ln)) return null;
            return [la, ln];
        },

        useMyLocation() {
            if (!navigator.geolocation) {
                showAlert('Browser tidak mendukung Geolocation.', 'warning');
                return;
            }
            navigator.geolocation.getCurrentPosition(
                (pos) => this.setAwal(pos.coords.latitude, pos.coords.longitude),
                ()    => showAlert('Gagal mengambil lokasi. Pastikan izin lokasi aktif.', 'error')
            );
        },

        clearPoints() {
            this.latAwal = this.lngAwal = this.latAkhir = this.lngAkhir = '';
            this.koordinatJson = '';
            this.pointCount = 0;
            if (this.routeLine) { this.map.removeLayer(this.routeLine); this.routeLine = null; }
            this.renderMarkers();
        },

        // --- Impor KML / KMZ ---
        async importKml(e) {
            const file = e.target.files[0];
            if (!file) return;
            const name = file.name.toLowerCase();
            try {
                let kmlText;
                if (name.endsWith('.kmz')) {
                    if (typeof JSZip === 'undefined') {
                        showAlert('Pustaka JSZip belum termuat. Coba muat ulang halaman.', 'error');
                        return;
                    }
                    const zip = await JSZip.loadAsync(await file.arrayBuffer());
                    // Cari file .kml pertama di dalam arsip
                    const kmlEntry = Object.keys(zip.files).find(f => f.toLowerCase().endsWith('.kml'));
                    if (!kmlEntry) { showAlert('File KMZ tidak berisi .kml di dalamnya.', 'error'); return; }
                    kmlText = await zip.files[kmlEntry].async('string');
                } else if (name.endsWith('.kml')) {
                    kmlText = await file.text();
                } else {
                    showAlert('Format file harus .kml atau .kmz', 'warning');
                    return;
                }

                const coords = this.parseKmlText(kmlText); // array [[lat,lng], ...]
                if (coords.length < 2) {
                    showAlert('Tidak ditemukan garis rute (LineString) yang valid di dalam file.', 'error');
                    return;
                }

                // Simpan sebagai JSON [lng,lat] (konsisten dgn urutan KML) & isi titik awal/akhir
                this.koordinatJson = JSON.stringify(coords.map(p => [p[1], p[0]]));
                this.pointCount = coords.length;
                const first = coords[0], last = coords[coords.length - 1];
                this.latAwal  = this.round(first[0]); this.lngAwal  = this.round(first[1]);
                this.latAkhir = this.round(last[0]);  this.lngAkhir = this.round(last[1]);

                this.renderMarkers();
                this.drawRoute();
                showAlert(`Rute berhasil diimpor: ${coords.length} titik koordinat.`, 'success', 'Impor Berhasil');
            } catch (err) {
                console.error('Import KML error:', err);
                showAlert('Gagal membaca file. Pastikan file KML/KMZ valid.', 'error');
            } finally {
                e.target.value = ''; // reset agar file yang sama bisa dipilih lagi
            }
        },

        // Ekstrak koordinat dari KML text (hanya ambil rute LineString/Track utama, abaikan Point marker)
        parseKmlText(text) {
            const doc = new DOMParser().parseFromString(text, 'application/xml');
            const lines = [];

            // 1. Cari elemen LineString coordinates
            const lineNodes = doc.querySelectorAll('LineString coordinates, linestring coordinates');
            lineNodes.forEach(node => {
                const pts = this.parseCoordString(node.textContent);
                if (pts.length >= 2) lines.push(pts);
            });

            // 2. Cari elemen gx:Track / Track jika tidak ada LineString
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

            // 3. Fallback: cari tag coordinates mana saja yang memiliki minimal 2 titik (bukan 1 titik Point)
            if (lines.length === 0) {
                const allCoordNodes = doc.getElementsByTagName('coordinates');
                for (let i = 0; i < allCoordNodes.length; i++) {
                    const pts = this.parseCoordString(allCoordNodes[i].textContent);
                    if (pts.length >= 2) lines.push(pts);
                }
            }

            if (lines.length === 0) return [];

            // Pilih garis rute dengan jumlah titik terbanyak (rute utama)
            lines.sort((a, b) => b.length - a.length);
            return lines[0];
        },

        parseCoordString(raw) {
            if (!raw) return [];
            let points = [];
            raw.trim().split(/\s+/).forEach(tuple => {
                const parts = tuple.split(',');
                const lng = parseFloat(parts[0]);
                const lat = parseFloat(parts[1]);
                if (!isNaN(lat) && !isNaN(lng)) points.push([lat, lng]);
            });
            return points;
        },

        // Gambar polyline rute dari koordinatJson
        drawRoute() {
            if (!this.map) return;
            if (this.routeLine) { this.map.removeLayer(this.routeLine); this.routeLine = null; }
            if (!this.koordinatJson) return;
            try {
                const arr = JSON.parse(this.koordinatJson); // [[lng,lat], ...]
                const latlngs = arr.map(p => [p[1], p[0]]);
                if (latlngs.length < 2) return;
                this.pointCount = latlngs.length;
                this.routeLine = L.polyline(latlngs, { color: '#7c3aed', weight: 5, opacity: 0.85 }).addTo(this.map);
                this.map.fitBounds(this.routeLine.getBounds(), { padding: [30, 30] });
            } catch (err) {
                console.error('drawRoute parse error:', err);
            }
        }
    };
}
</script>

<script>
function ruasDanStripmapForm() {
    const isEdit = <?= $isEdit ? 'true' : 'false' ?>;

    // Inisialisasi Data Strip Map
    let initialRows = [];
    <?php if ($oldRows): ?>
        initialRows = <?= json_encode(array_values($oldRows)) ?>.map((r, idx) => ({
            id: Date.now() + idx, staAwal: r.sta_awal, staAkhir: r.sta_akhir, panjang: 0,
            baik: parseFloat(r.baik) || 0, sedang: parseFloat(r.sedang) || 0,
            rusakRingan: parseFloat(r.rusak_ringan) || 0, rusakBerat: parseFloat(r.rusak_berat) || 0,
            error: '', isValid: false
        }));
    <?php elseif ($isEdit && !empty($stripmaps)): ?>
        initialRows = <?= json_encode(array_map(fn($sm) => [
            'id' => $sm['id'],
            'staAwal' => meter_to_sta($sm['sta_awal']),
            'staAkhir' => meter_to_sta($sm['sta_akhir']),
            'panjang' => (float)$sm['panjang'],
            'baik' => (float)$sm['baik'],
            'sedang' => (float)$sm['sedang'],
            'rusakRingan' => (float)$sm['rusak_ringan'],
            'rusakBerat' => (float)$sm['rusak_berat'],
            'error' => '',
            'isValid' => true
        ], $stripmaps)) ?>;
    <?php else: ?>
        for(let i=0; i<3; i++) {
            initialRows.push({ id: Date.now() + i, staAwal: '', staAkhir: '', panjang: 0, baik: '', sedang: '', rusakRingan: '', rusakBerat: '', error: '', isValid: false });
        }
    <?php endif; ?>

    // Inisialisasi Data Perkerasan
    let initialPkRows = [];
    <?php if ($oldPerkerasanRows): ?>
        initialPkRows = <?= json_encode(array_values($oldPerkerasanRows)) ?>.map((r, idx) => ({
            id: Date.now() + 100 + idx, staAwal: r.sta_awal, staAkhir: r.sta_akhir, panjang: 0,
            rigid: parseFloat(r.rigid) || 0, aspal: parseFloat(r.aspal) || 0,
            agregatTanah: parseFloat(r.agregat_tanah) || 0, belumTembus: parseFloat(r.belum_tembus) || 0,
            error: '', isValid: false
        }));
    <?php elseif ($isEdit && !empty($perkerasans)): ?>
        initialPkRows = <?= json_encode(array_map(fn($pk) => [
            'id' => $pk['id'],
            'staAwal' => meter_to_sta($pk['sta_awal']),
            'staAkhir' => meter_to_sta($pk['sta_akhir']),
            'panjang' => (float)$pk['panjang'],
            'rigid' => (float)$pk['rigid'],
            'aspal' => (float)$pk['aspal'],
            'agregatTanah' => (float)$pk['agregat_tanah'],
            'belumTembus' => (float)$pk['belum_tembus'],
            'error' => '',
            'isValid' => true
        ], $perkerasans)) ?>;
    <?php else: ?>
        for(let i=0; i<3; i++) {
            initialPkRows.push({ id: Date.now() + 100 + i, staAwal: '', staAkhir: '', panjang: 0, rigid: '', aspal: '', agregatTanah: '', belumTembus: '', error: '', isValid: false });
        }
    <?php endif; ?>

    return {
        rows: initialRows,
        pkRows: initialPkRows,

        init() {
            this.rows.forEach(r => this.calculateRow(r));
            this.pkRows.forEach(r => this.calculatePkRow(r));
        },

        // --- Methods Strip Map ---
        addRow() {
            let lastSta = this.rows.length > 0 ? this.rows[this.rows.length - 1].staAkhir : '';
            this.rows.push({ id: Date.now(), staAwal: lastSta, staAkhir: '', panjang: 0, baik: '', sedang: '', rusakRingan: '', rusakBerat: '', error: '', isValid: false });
        },
        removeRow(idx) { this.rows.splice(idx, 1); },
        isRowEmpty(row) { return !row.staAwal && !row.staAkhir && row.baik==='' && row.sedang==='' && row.rusakRingan==='' && row.rusakBerat===''; },
        calculateRow(row) {
            row.error = ''; row.isValid = false;
            if (this.isRowEmpty(row)) { row.panjang = 0; return; }
            const total = (parseFloat(row.baik)||0) + (parseFloat(row.sedang)||0) + (parseFloat(row.rusakRingan)||0) + (parseFloat(row.rusakBerat)||0);
            if (row.staAwal && row.staAkhir) {
                const awal = this.staToMeter(row.staAwal);
                const akhir = this.staToMeter(row.staAkhir);
                if (akhir <= awal) { row.error = 'STA Akhir harus > STA Awal.'; row.panjang = 0; }
                else {
                    row.panjang = akhir - awal;
                    if (Math.abs(total - row.panjang) > 0.01) row.error = `Selisih kondisi (${total}m) vs segmen (${row.panjang}m).`;
                    else row.isValid = true;
                }
            } else { row.error = 'STA harus diisi lengkap.'; }
        },

        // --- Methods Perkerasan ---
        addPkRow() {
            let lastSta = this.pkRows.length > 0 ? this.pkRows[this.pkRows.length - 1].staAkhir : '';
            this.pkRows.push({ id: Date.now() + 100, staAwal: lastSta, staAkhir: '', panjang: 0, rigid: '', aspal: '', agregatTanah: '', belumTembus: '', error: '', isValid: false });
        },
        removePkRow(idx) { this.pkRows.splice(idx, 1); },
        isPkRowEmpty(row) { return !row.staAwal && !row.staAkhir && row.rigid==='' && row.aspal==='' && row.agregatTanah==='' && row.belumTembus===''; },
        calculatePkRow(row) {
            row.error = ''; row.isValid = false;
            if (this.isPkRowEmpty(row)) { row.panjang = 0; return; }
            const total = (parseFloat(row.rigid)||0) + (parseFloat(row.aspal)||0) + (parseFloat(row.agregatTanah)||0) + (parseFloat(row.belumTembus)||0);
            if (row.staAwal && row.staAkhir) {
                const awal = this.staToMeter(row.staAwal);
                const akhir = this.staToMeter(row.staAkhir);
                if (akhir <= awal) { row.error = 'STA Akhir harus > STA Awal.'; row.panjang = 0; }
                else {
                    row.panjang = akhir - awal;
                    if (Math.abs(total - row.panjang) > 0.01) row.error = `Selisih perkerasan (${total}m) vs segmen (${row.panjang}m).`;
                    else row.isValid = true;
                }
            } else { row.error = 'STA harus diisi lengkap.'; }
        },

        // --- Common Helpers ---
        staToMeter(sta) {
            if(!sta) return 0;
            sta = sta.toString().trim();
            if (sta.includes('+')) {
                const parts = sta.split('+');
                return parseFloat(parts[0]) * 1000 + parseFloat(parts[1] || 0);
            }
            return parseFloat(sta) || 0;
        },
        formatStaValue(val) {
            if (!val) return '';
            val = val.toString().trim().replace(',', '.');
            let totalMeters = 0;
            if (val.includes('+')) {
                const parts = val.split('+');
                const km = parseFloat(parts[0]) || 0;
                const m = parseFloat(parts[1]) || 0;
                totalMeters = km * 1000 + m;
            } else {
                const num = parseFloat(val);
                if (isNaN(num)) return val;
                totalMeters = num < 10 || val.includes('.') ? num * 1000 : num;
            }
            const km = Math.floor(totalMeters / 1000);
            const m = Math.round(totalMeters % 1000);
            return `${km}+${String(m).padStart(3, '0')}`;
        },
        onStaInput(e, row, field) {
            let val = e.target.value.replace(/[^0-9+]/g, '');
            if (val.length === 1 && /^\d$/.test(val)) val += '+';
            if (field === 'awal') row.staAwal = val; else row.staAkhir = val;
            if ('baik' in row) this.calculateRow(row);
            else this.calculatePkRow(row);
        },
        formatNumber(num) {
            return new Intl.NumberFormat('id-ID', { maximumFractionDigits: 2 }).format(num);
        },

        // --- Errors & Submit Readiness ---
        get formErrors() {
            let errs = [];
            this.rows.forEach((r, idx) => {
                if (!this.isRowEmpty(r)) {
                    if (r.error) errs.push(`Baris ${idx+1}: ${r.error}`);
                    else if (!r.isValid) errs.push(`Baris ${idx+1}: Input belum valid.`);
                }
            });
            return errs;
        },
        get pkFormErrors() {
            let errs = [];
            this.pkRows.forEach((r, idx) => {
                if (!this.isPkRowEmpty(r)) {
                    if (r.error) errs.push(`Baris ${idx+1}: ${r.error}`);
                    else if (!r.isValid) errs.push(`Baris ${idx+1}: Input belum valid.`);
                }
            });
            return errs;
        },
        get isReadyToSubmit() {
            if (this.formErrors.length > 0 || this.pkFormErrors.length > 0) return false;
            const activeSm = this.rows.filter(r => !this.isRowEmpty(r));
            const activePk = this.pkRows.filter(r => !this.isPkRowEmpty(r));
            return (activeSm.length === 0 || activeSm.every(r => r.isValid)) &&
                   (activePk.length === 0 || activePk.every(r => r.isValid));
        },
        validateForm(e) {
            if (!this.isReadyToSubmit) {
                e.preventDefault();
                showAlert('Terdapat input data yang belum valid!', 'warning', 'Validasi Gagal');
            }
        }
    };
}
</script>
