<!-- ============================================================ -->
<!-- Form Tambah / Edit Segmen Strip Map & Perkerasan              -->
<!-- ============================================================ -->

<?php
    $isEditStripmap  = isset($stripmap);
    $isEditPerkerasan= isset($perkerasan);

    if ($isEditStripmap) {
        $heading = 'Edit Segmen Strip Map (Kondisi Jalan)';
        $action  = base_url('stripmap/update/' . $stripmap['id']);
    } elseif ($isEditPerkerasan) {
        $heading = 'Edit Segmen Perkerasan Jalan';
        $action  = base_url('perkerasan/update/' . $perkerasan['id']);
    } else {
        $heading = 'Input Segmen Jalan (Strip Map & Perkerasan)';
        $actionStripmap  = base_url('stripmap/store/' . $ruas['id']);
        $actionPerkerasan = base_url('perkerasan/store/' . $ruas['id']);
    }

    $oldInput           = $_SESSION['old_input'] ?? null;
    $oldPerkerasanInput = $_SESSION['old_perkerasan_input'] ?? null;
    if ($oldInput) unset($_SESSION['old_input']);
    if ($oldPerkerasanInput) unset($_SESSION['old_perkerasan_input']);
?>

<div class="space-y-6">

    <!-- Header -->
    <div class="flex items-center gap-4">
        <a href="<?= base_url('stripmap/' . $ruas['id']) ?>"
           class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-white border border-gray-200 hover:bg-gray-50 transition-colors shadow-sm">
            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900"><?= $heading ?></h1>
            <p class="mt-1 text-sm text-gray-500">
                Ruas: <span class="font-semibold text-gray-800"><?= e($ruas['nama_ruas']) ?></span>
                (<span class="font-mono text-gray-600"><?= e($ruas['kode_ruas']) ?></span>)
            </p>
        </div>
    </div>

    <!-- Mode 1: Edit Single Stripmap -->
    <?php if ($isEditStripmap): ?>
        <div x-data="singleStripmapForm()" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden p-6">
            <form action="<?= $action ?>" method="POST" class="space-y-6" @submit="validateForm($event)">
                <h2 class="text-lg font-bold text-gray-800 border-b pb-3">Form Edit Kondisi Strip Map</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">STA Awal</label>
                        <input type="text" name="rows[0][sta_awal]" x-model="row.staAwal" @blur="row.staAwal = formatStaValue(row.staAwal); calculateRow()" @input="onStaInput($event, 'awal')" class="w-full px-3 py-2 rounded-lg border border-gray-300 font-mono text-sm focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">STA Akhir</label>
                        <input type="text" name="rows[0][sta_akhir]" x-model="row.staAkhir" @blur="row.staAkhir = formatStaValue(row.staAkhir); calculateRow()" @input="onStaInput($event, 'akhir')" class="w-full px-3 py-2 rounded-lg border border-gray-300 font-mono text-sm focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Panjang Segmen</label>
                        <div class="px-3 py-2 rounded-lg bg-gray-50 border border-gray-200 font-mono font-bold text-gray-700 text-sm" x-text="row.panjang > 0 ? formatNumber(row.panjang) + ' m' : '-'"></div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-emerald-700 mb-1">Kondisi Baik (m)</label>
                        <input type="number" name="rows[0][baik]" x-model.number="row.baik" @input="calculateRow()" min="0" step="0.01" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-emerald-500" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-yellow-700 mb-1">Kondisi Sedang (m)</label>
                        <input type="number" name="rows[0][sedang]" x-model.number="row.sedang" @input="calculateRow()" min="0" step="0.01" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-yellow-500" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-orange-700 mb-1">Rusak Ringan (m)</label>
                        <input type="number" name="rows[0][rusak_ringan]" x-model.number="row.rusakRingan" @input="calculateRow()" min="0" step="0.01" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-orange-500" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-red-700 mb-1">Rusak Berat (m)</label>
                        <input type="number" name="rows[0][rusak_berat]" x-model.number="row.rusakBerat" @input="calculateRow()" min="0" step="0.01" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-red-500" required>
                    </div>
                </div>

                <div x-show="row.error" class="p-3 rounded-lg bg-red-50 border border-red-200 text-xs text-red-700 font-medium" x-text="row.error"></div>

                <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
                    <button type="submit" :disabled="!row.isValid" :class="row.isValid ? 'bg-blue-600 hover:bg-blue-700' : 'bg-gray-300 cursor-not-allowed'" class="px-6 py-2.5 text-white text-sm font-medium rounded-xl transition-colors shadow-sm">
                        Perbarui Kondisi
                    </button>
                    <a href="<?= base_url('stripmap/' . $ruas['id']) ?>" class="px-6 py-2.5 bg-white text-gray-700 text-sm font-medium rounded-xl border border-gray-300 hover:bg-gray-50 transition-colors">Batal</a>
                </div>
            </form>
        </div>

    <!-- Mode 2: Edit Single Perkerasan -->
    <?php elseif ($isEditPerkerasan): ?>
        <div x-data="singlePerkerasanForm()" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden p-6">
            <form action="<?= $action ?>" method="POST" class="space-y-6" @submit="validateForm($event)">
                <h2 class="text-lg font-bold text-gray-800 border-b pb-3">Form Edit Jenis Perkerasan</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">STA Awal</label>
                        <input type="text" name="perkerasan_rows[0][sta_awal]" x-model="row.staAwal" @blur="row.staAwal = formatStaValue(row.staAwal); calculateRow()" @input="onStaInput($event, 'awal')" class="w-full px-3 py-2 rounded-lg border border-gray-300 font-mono text-sm focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">STA Akhir</label>
                        <input type="text" name="perkerasan_rows[0][sta_akhir]" x-model="row.staAkhir" @blur="row.staAkhir = formatStaValue(row.staAkhir); calculateRow()" @input="onStaInput($event, 'akhir')" class="w-full px-3 py-2 rounded-lg border border-gray-300 font-mono text-sm focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Panjang Segmen</label>
                        <div class="px-3 py-2 rounded-lg bg-gray-50 border border-gray-200 font-mono font-bold text-gray-700 text-sm" x-text="row.panjang > 0 ? formatNumber(row.panjang) + ' m' : '-'"></div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1 flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full inline-block bg-gray-500"></span> Rigid (m)</label>
                        <input type="number" name="perkerasan_rows[0][rigid]" x-model.number="row.rigid" @input="calculateRow()" min="0" step="0.01" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-gray-500" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-900 mb-1 flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full inline-block bg-slate-900"></span> Aspal (m)</label>
                        <input type="number" name="perkerasan_rows[0][aspal]" x-model.number="row.aspal" @input="calculateRow()" min="0" step="0.01" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-slate-900" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-amber-800 mb-1 flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full inline-block" style="background:#92400e"></span> Kerikil (m)</label>
                        <input type="number" name="perkerasan_rows[0][agregat_tanah]" x-model.number="row.agregatTanah" @input="calculateRow()" min="0" step="0.01" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-amber-700" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-purple-700 mb-1 flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full inline-block bg-purple-600"></span> Belum Tembus (m)</label>
                        <input type="number" name="perkerasan_rows[0][belum_tembus]" x-model.number="row.belumTembus" @input="calculateRow()" min="0" step="0.01" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-purple-600" required>
                    </div>
                </div>

                <div x-show="row.error" class="p-3 rounded-lg bg-red-50 border border-red-200 text-xs text-red-700 font-medium" x-text="row.error"></div>

                <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
                    <button type="submit" :disabled="!row.isValid" :class="row.isValid ? 'bg-blue-600 hover:bg-blue-700' : 'bg-gray-300 cursor-not-allowed'" class="px-6 py-2.5 text-white text-sm font-medium rounded-xl transition-colors shadow-sm">
                        Perbarui Perkerasan
                    </button>
                    <a href="<?= base_url('stripmap/' . $ruas['id']) ?>" class="px-6 py-2.5 bg-white text-gray-700 text-sm font-medium rounded-xl border border-gray-300 hover:bg-gray-50 transition-colors">Batal</a>
                </div>
            </form>
        </div>

    <!-- Mode 3: Add New Segments (Form Kondisi + Form Perkerasan dalam 1 Form) -->
    <?php else: ?>
        <div x-data="batchCombinedForm()">
            <form action="<?= base_url('stripmap/batch/' . $ruas['id']) ?>" method="POST" class="space-y-8" @submit="validateForm($event)">

                <!-- SECTION 1: FORM STRIP MAP (Kondisi Jalan) -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50/50 flex items-center justify-between cursor-pointer select-none" @click="isOpenSm = !isOpenSm">
                        <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                            <span class="w-7 h-7 rounded-lg bg-gray-200 text-gray-700 flex items-center justify-center text-xs font-bold">1</span>
                            Form Input Kondisi Jalan (Strip Map)
                        </h2>
                        <button type="button" class="text-gray-500 hover:text-gray-700 transition-transform duration-200" :class="isOpenSm ? 'rotate-90' : 'rotate-0'">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    </div>

                    <div x-show="isOpenSm" x-collapse class="p-6 space-y-6">
                        <div class="overflow-x-auto">
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
                                                <input type="text" :name="`rows[${index}][sta_awal]`" x-model="row.staAwal" @blur="row.staAwal = formatStaValue(row.staAwal); calculateRow(row)" @input="onStaInput($event, row, 'awal')" placeholder="0+000" class="w-full px-2 py-1.5 rounded border border-gray-300 font-mono text-sm focus:ring-2 focus:ring-blue-500">
                                            </td>
                                            <td class="p-2">
                                                <input type="text" :name="`rows[${index}][sta_akhir]`" x-model="row.staAkhir" @blur="row.staAkhir = formatStaValue(row.staAkhir); calculateRow(row)" @input="onStaInput($event, row, 'akhir')" placeholder="1+000" class="w-full px-2 py-1.5 rounded border border-gray-300 font-mono text-sm focus:ring-2 focus:ring-blue-500">
                                            </td>
                                            <td class="p-2">
                                                <div class="font-mono font-semibold px-2 text-gray-700" x-text="row.panjang > 0 ? formatNumber(row.panjang) : '-'"></div>
                                            </td>
                                            <td class="p-2">
                                                <input type="number" :name="`rows[${index}][baik]`" x-model.number="row.baik" @input="calculateRow(row)" min="0" step="0.01" class="w-full px-2 py-1.5 rounded border border-gray-300 text-sm focus:ring-2 focus:ring-emerald-500">
                                            </td>
                                            <td class="p-2">
                                                <input type="number" :name="`rows[${index}][sedang]`" x-model.number="row.sedang" @input="calculateRow(row)" min="0" step="0.01" class="w-full px-2 py-1.5 rounded border border-gray-300 text-sm focus:ring-2 focus:ring-yellow-500">
                                            </td>
                                            <td class="p-2">
                                                <input type="number" :name="`rows[${index}][rusak_ringan]`" x-model.number="row.rusakRingan" @input="calculateRow(row)" min="0" step="0.01" class="w-full px-2 py-1.5 rounded border border-gray-300 text-sm focus:ring-2 focus:ring-orange-500">
                                            </td>
                                            <td class="p-2">
                                                <input type="number" :name="`rows[${index}][rusak_berat]`" x-model.number="row.rusakBerat" @input="calculateRow(row)" min="0" step="0.01" class="w-full px-2 py-1.5 rounded border border-gray-300 text-sm focus:ring-2 focus:ring-red-500">
                                            </td>
                                            <td class="p-2 text-center">
                                                <div class="flex justify-center" :title="row.error || 'Valid'">
                                                    <svg x-show="row.isValid" class="w-5 h-5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                                    <svg x-show="!row.isValid && !isRowEmpty(row)" class="w-5 h-5 text-red-500 cursor-help" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
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
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                Tambah Baris Kondisi
                            </button>
                        </div>

                        <!-- Error List (Strip Map) -->
                        <div x-show="formErrors.length > 0" class="p-4 rounded-xl bg-red-50 border border-red-200">
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-red-500 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                <div>
                                    <h4 class="text-sm font-medium text-red-800">Terdapat error pada input Strip Map:</h4>
                                    <ul class="mt-1 text-sm text-red-700 list-disc list-inside">
                                        <template x-for="err in formErrors" :key="err"><li x-text="err"></li></template>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SECTION 2: FORM PERKERASAN JALAN -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50/50 flex items-center justify-between cursor-pointer select-none" @click="isOpenPk = !isOpenPk">
                        <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                            <span class="w-7 h-7 rounded-lg bg-gray-200 text-gray-700 flex items-center justify-center text-xs font-bold">2</span>
                            Form Input Jenis Perkerasan Jalan
                        </h2>
                        <button type="button" class="text-gray-500 hover:text-gray-700 transition-transform duration-200" :class="isOpenPk ? 'rotate-90' : 'rotate-0'">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    </div>

                    <div x-show="isOpenPk" x-collapse class="p-6 space-y-6">
                        <div class="overflow-x-auto">
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
                                                <div class="font-mono font-semibold px-2 text-gray-700" x-text="row.panjang > 0 ? formatNumber(row.panjang) : '-'"></div>
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
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                Tambah Baris Perkerasan
                            </button>
                        </div>

                        <!-- Error List (Perkerasan) -->
                        <div x-show="pkFormErrors.length > 0" class="p-4 rounded-xl bg-red-50 border border-red-200">
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-red-500 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                <div>
                                    <h4 class="text-sm font-medium text-red-800">Terdapat error pada input Perkerasan:</h4>
                                    <ul class="mt-1 text-sm text-red-700 list-disc list-inside">
                                        <template x-for="err in pkFormErrors" :key="err"><li x-text="err"></li></template>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SINGLE SUBMIT BUTTON BAR -->
                <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200 flex items-center justify-end gap-3 sticky bottom-4 z-10">
                    <a href="<?= base_url('stripmap/' . $ruas['id']) ?>"
                       class="inline-flex items-center gap-2 px-6 py-2.5 bg-gray-100 text-gray-700 text-sm font-medium rounded-xl hover:bg-gray-200 transition-colors">
                        Batal
                    </a>
                    <button type="submit"
                            :disabled="!isFormValid"
                            :class="isFormValid ? 'bg-blue-600 hover:bg-blue-700' : 'bg-gray-300 cursor-not-allowed'"
                            class="inline-flex items-center gap-2 px-6 py-2.5 text-white text-sm font-medium rounded-xl transition-colors shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                        Simpan Semua Data Segmen
                    </button>
                </div>

            </form>
        </div>
    <?php endif; ?>

</div>

<script>
// --- Helper Functions ---
function staToMeter(sta) {
    if(!sta) return 0;
    sta = sta.toString().trim();
    if (sta.includes('+')) {
        const parts = sta.split('+');
        return parseFloat(parts[0]) * 1000 + parseFloat(parts[1] || 0);
    }
    return parseFloat(sta) || 0;
}

function formatStaValue(val) {
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
}

function formatNumber(num) {
    return new Intl.NumberFormat('id-ID', { maximumFractionDigits: 2 }).format(num);
}

// -------------------------------------------------------------
// 1. Single Edit Stripmap Form Component
// -------------------------------------------------------------
function singleStripmapForm() {
    return {
        row: {
            staAwal: '<?= isset($stripmap) ? meter_to_sta($stripmap['sta_awal']) : '' ?>',
            staAkhir: '<?= isset($stripmap) ? meter_to_sta($stripmap['sta_akhir']) : '' ?>',
            panjang: <?= isset($stripmap) ? $stripmap['panjang'] : 0 ?>,
            baik: <?= isset($stripmap) ? $stripmap['baik'] : 0 ?>,
            sedang: <?= isset($stripmap) ? $stripmap['sedang'] : 0 ?>,
            rusakRingan: <?= isset($stripmap) ? $stripmap['rusak_ringan'] : 0 ?>,
            rusakBerat: <?= isset($stripmap) ? $stripmap['rusak_berat'] : 0 ?>,
            error: '',
            isValid: true
        },
        calculateRow() {
            this.row.error = '';
            this.row.isValid = false;
            const baik = parseFloat(this.row.baik) || 0;
            const sedang = parseFloat(this.row.sedang) || 0;
            const rRingan = parseFloat(this.row.rusakRingan) || 0;
            const rBerat = parseFloat(this.row.rusakBerat) || 0;
            const total = baik + sedang + rRingan + rBerat;

            if (this.row.staAwal && this.row.staAkhir) {
                const awal = staToMeter(this.row.staAwal);
                const akhir = staToMeter(this.row.staAkhir);
                if (akhir <= awal) {
                    this.row.error = 'STA Akhir harus > STA Awal.';
                    this.row.panjang = 0;
                } else {
                    this.row.panjang = akhir - awal;
                    if (Math.abs(total - this.row.panjang) > 0.01) {
                        this.row.error = `Selisih kondisi (${total}m) dengan panjang segmen (${this.row.panjang}m).`;
                    } else {
                        this.row.isValid = true;
                    }
                }
            }
        },
        onStaInput(e, field) {
            let val = e.target.value.replace(/[^0-9+]/g, '');
            if (val.length === 1 && /^\d$/.test(val)) val += '+';
            if (field === 'awal') this.row.staAwal = val;
            else this.row.staAkhir = val;
            this.calculateRow();
        },
        validateForm(e) {
            if (!this.row.isValid) e.preventDefault();
        }
    };
}

// -------------------------------------------------------------
// 2. Single Edit Perkerasan Form Component
// -------------------------------------------------------------
function singlePerkerasanForm() {
    return {
        row: {
            staAwal: '<?= isset($perkerasan) ? meter_to_sta($perkerasan['sta_awal']) : '' ?>',
            staAkhir: '<?= isset($perkerasan) ? meter_to_sta($perkerasan['sta_akhir']) : '' ?>',
            panjang: <?= isset($perkerasan) ? $perkerasan['panjang'] : 0 ?>,
            rigid: <?= isset($perkerasan) ? $perkerasan['rigid'] : 0 ?>,
            aspal: <?= isset($perkerasan) ? $perkerasan['aspal'] : 0 ?>,
            agregatTanah: <?= isset($perkerasan) ? $perkerasan['agregat_tanah'] : 0 ?>,
            belumTembus: <?= isset($perkerasan) ? $perkerasan['belum_tembus'] : 0 ?>,
            error: '',
            isValid: true
        },
        calculateRow() {
            this.row.error = '';
            this.row.isValid = false;
            const rigid = parseFloat(this.row.rigid) || 0;
            const aspal = parseFloat(this.row.aspal) || 0;
            const agregat = parseFloat(this.row.agregatTanah) || 0;
            const belum = parseFloat(this.row.belumTembus) || 0;
            const total = rigid + aspal + agregat + belum;

            if (this.row.staAwal && this.row.staAkhir) {
                const awal = staToMeter(this.row.staAwal);
                const akhir = staToMeter(this.row.staAkhir);
                if (akhir <= awal) {
                    this.row.error = 'STA Akhir harus > STA Awal.';
                    this.row.panjang = 0;
                } else {
                    this.row.panjang = akhir - awal;
                    if (Math.abs(total - this.row.panjang) > 0.01) {
                        this.row.error = `Selisih perkerasan (${total}m) dengan panjang segmen (${this.row.panjang}m).`;
                    } else {
                        this.row.isValid = true;
                    }
                }
            }
        },
        onStaInput(e, field) {
            let val = e.target.value.replace(/[^0-9+]/g, '');
            if (val.length === 1 && /^\d$/.test(val)) val += '+';
            if (field === 'awal') this.row.staAwal = val;
            else this.row.staAkhir = val;
            this.calculateRow();
        },
        validateForm(e) {
            if (!this.row.isValid) e.preventDefault();
        }
    };
}

// -------------------------------------------------------------
// 3. Combined Batch Form Component (Kondisi + Perkerasan)
// -------------------------------------------------------------
function batchCombinedForm() {
    let initialRows = [];
    <?php if (isset($prefillData)): ?>
        initialRows = [{ id: Date.now(), staAwal: '<?= $prefillData['sta_awal'] ?>', staAkhir: '<?= $prefillData['sta_akhir'] ?>', panjang: 0, baik: '', sedang: '', rusakRingan: '', rusakBerat: '', error: '', isValid: false }];
    <?php else: ?>
        for(let i=0; i<3; i++) {
            initialRows.push({ id: Date.now() + i, staAwal: '', staAkhir: '', panjang: 0, baik: '', sedang: '', rusakRingan: '', rusakBerat: '', error: '', isValid: false });
        }
    <?php endif; ?>

    let initialPkRows = [];
    <?php if (isset($prefillPerkerasanData)): ?>
        initialPkRows = [{ id: Date.now() + 100, staAwal: '<?= $prefillPerkerasanData['sta_awal'] ?>', staAkhir: '<?= $prefillPerkerasanData['sta_akhir'] ?>', panjang: 0, rigid: '', aspal: '', agregatTanah: '', belumTembus: '', error: '', isValid: false }];
    <?php else: ?>
        for(let i=0; i<3; i++) {
            initialPkRows.push({ id: Date.now() + 100 + i, staAwal: '', staAkhir: '', panjang: 0, rigid: '', aspal: '', agregatTanah: '', belumTembus: '', error: '', isValid: false });
        }
    <?php endif; ?>

    return {
        isOpenSm: true,
        isOpenPk: true,
        rows: initialRows,
        pkRows: initialPkRows,

        init() {
            this.rows.forEach(r => this.calculateRow(r));
            this.pkRows.forEach(r => this.calculatePkRow(r));
        },

        // --- Strip Map Methods ---
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
                const awal = staToMeter(row.staAwal);
                const akhir = staToMeter(row.staAkhir);
                if (akhir <= awal) { row.error = 'STA Akhir harus > STA Awal.'; row.panjang = 0; }
                else {
                    row.panjang = akhir - awal;
                    if (Math.abs(total - row.panjang) > 0.01) row.error = `Selisih kondisi (${total}m) vs segmen (${row.panjang}m).`;
                    else row.isValid = true;
                }
            } else { row.error = 'STA harus diisi lengkap.'; }
        },

        // --- Perkerasan Methods ---
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
                const awal = staToMeter(row.staAwal);
                const akhir = staToMeter(row.staAkhir);
                if (akhir <= awal) { row.error = 'STA Akhir harus > STA Awal.'; row.panjang = 0; }
                else {
                    row.panjang = akhir - awal;
                    if (Math.abs(total - row.panjang) > 0.01) row.error = `Selisih perkerasan (${total}m) vs segmen (${row.panjang}m).`;
                    else row.isValid = true;
                }
            } else { row.error = 'STA harus diisi lengkap.'; }
        },

        // --- Helpers ---
        onStaInput(e, row, field) {
            let val = e.target.value.replace(/[^0-9+]/g, '');
            if (val.length === 1 && /^\d$/.test(val)) val += '+';
            if (field === 'awal') row.staAwal = val; else row.staAkhir = val;
            if ('baik' in row) this.calculateRow(row);
            else this.calculatePkRow(row);
        },

        // --- Validation & Readiness ---
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
        get isFormValid() {
            if (this.formErrors.length > 0 || this.pkFormErrors.length > 0) return false;
            const activeSm = this.rows.filter(r => !this.isRowEmpty(r));
            const activePk = this.pkRows.filter(r => !this.isPkRowEmpty(r));
            if (activeSm.length === 0 && activePk.length === 0) return false;
            return (activeSm.length === 0 || activeSm.every(r => r.isValid)) &&
                   (activePk.length === 0 || activePk.every(r => r.isValid));
        },
        validateForm(e) {
            if (!this.isFormValid) {
                e.preventDefault();
                showAlert('Terdapat input segmen yang belum valid!', 'warning', 'Validasi Gagal');
            }
        }
    };
}
</script>
