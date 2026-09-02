<!-- ============================================================ -->
<!-- Detail Ruas Jalan + Strip Map & Perkerasan Preview          -->
<!-- ============================================================ -->

<div class="space-y-6">

    <!-- Header -->
    <div class="flex items-center gap-4">
        <a href="<?= base_url('ruas') ?>"
           class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-white border border-gray-200 hover:bg-gray-50 transition-colors shadow-sm">
            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Detail Ruas Jalan</h1>
            <p class="text-sm text-gray-500">Visualisasi kondisi strip map, perkerasan, dan informasi data teknis.</p>
        </div>
    </div>

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

    <!-- Strip Map & Perkerasan Visual -->
    <?php if (!empty($stripmaps) || !empty($perkerasans) || !empty($penanganans)): ?>
        <?php view('stripmap._visual', [
            'stripmaps'         => $stripmaps,
            'summary'           => $summary,
            'ruas'              => $ruas,
            'perkerasans'       => $perkerasans ?? [],
            'summaryPerkerasan' => $summaryPerkerasan ?? [],
            'penanganans'       => $penanganans ?? [],
            'penangananSummary' => $penangananSummary ?? [],
            'penangananYears'   => $penangananYears ?? []
        ]); ?>
    <?php endif; ?>

    <!-- Aksi -->
    <div class="flex gap-3">
        <a href="<?= base_url('stripmap/' . $ruas['id']) ?>"
           class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-xl hover:bg-blue-700 transition-colors shadow-sm">
            Kelola Strip Map & Perkerasan
        </a>
        <a href="<?= base_url('ruas/edit/' . $ruas['id']) ?>"
           class="inline-flex items-center gap-2 px-5 py-2.5 bg-white text-gray-700 text-sm font-medium rounded-xl border border-gray-300 hover:bg-gray-50 transition-colors">
            Edit Ruas
        </a>
    </div>

</div>
