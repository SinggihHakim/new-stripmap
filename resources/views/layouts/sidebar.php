<?php
$currentUrl = $_GET['url'] ?? null;
if ($currentUrl === null) {
    $uri = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?? '';
    $scriptDir = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/\\');
    if ($scriptDir !== '' && $scriptDir !== '/' && $scriptDir !== '\\' && strpos($uri, $scriptDir) === 0) {
        $uri = substr($uri, strlen($scriptDir));
    }
    $currentUrl = trim($uri, '/');
} else {
    $currentUrl = trim($currentUrl, '/');
}

// Helper to check if the current URL matches a pattern (supports * wildcard)
$urlMatches = function (string $pattern, string $url): bool {
    if ($pattern === '') {
        return $url === '';
    }
    if (strpos($pattern, '*') !== false) {
        $regex = '#^' . str_replace('\*', '.*', preg_quote($pattern, '#')) . '$#';
        return preg_match($regex, $url) === 1;
    }
    return $url === $pattern;
};

// Menu definition with submenus
$menuGroups = [
    [
        'title' => 'UTAMA',
        'items' => [
            [
                'label' => 'Dashboard',
                'icon'  => '<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 00-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 00-1 1m-6 0h6"/></svg>',
                'url'   => '',
                'match' => ['', 'dashboard', 'dashboard/detail', 'dashboard/detail/*'],
                'sub'   => []
            ],
        ]
    ],
    [
        'title' => 'MANAJEMEN PAVEMENT',
        'items' => [
            [
                'label' => 'Ruas Jalan',
                'icon'  => '<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>',
                'match' => ['ruas', 'ruas/*'],
                'sub'   => [
                    ['label' => 'Daftar Ruas Jalan', 'url' => 'ruas', 'match' => ['ruas', 'ruas/index', 'ruas/edit/*', 'ruas/show/*']],
                    ['label' => 'Tambah Ruas Baru',  'url' => 'ruas/create', 'match' => ['ruas/create']],
                ]
            ],
            [
                'label' => 'Strip Map & Visual',
                'icon'  => '<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/></svg>',
                'match' => ['stripmap', 'stripmap/*'],
                'sub'   => [
                    ['label' => 'Visualisasi Strip Map', 'url' => 'ruas',             'match' => ['stripmap', 'stripmap/*']],
                    ['label' => 'Perbandingan Tahun',    'url' => 'stripmap/compare', 'match' => ['stripmap/compare', 'stripmap/compare/*'], 'badge' => 'Baru'],
                ]
            ]
        ]
    ],
    [
        'title' => 'LAPORAN & ANALISIS',
        'items' => [
            [
                'label' => 'Rekapitulasi',
                'icon'  => '<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>',
                'match' => ['rekap/*'],
                'sub'   => [
                    ['label' => 'Rekap Kemantapan',    'url' => 'rekap/kemantapan', 'match' => ['rekap/kemantapan']],
                    ['label' => 'Rekap Perkerasan',    'url' => 'rekap/perkerasan', 'match' => ['rekap/perkerasan']],
                    ['label' => 'Prediksi Kondisi',    'url' => 'rekap/prediksi',   'match' => ['rekap/prediksi', 'rekap/prediksi/*'], 'badge' => 'Baru'],
                ]
            ]
        ]
    ],
    [
        'title' => 'INTEGRASI DATA',
        'items' => [
            [
                'label' => 'Import Excel',
                'icon'  => '<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>',
                'url'   => 'ruas/import',
                'match' => ['ruas/import'],
                'sub'   => []
            ],
            [
                'label' => 'Export & Cetak',
                'icon'  => '<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>',
                'url'   => 'export',
                'match' => ['export'],
                'sub'   => []
            ]
        ]
    ]
];
?>

<!-- Mobile Sidebar Backdrop -->
<div x-show="sidebarOpen" 
     x-transition:enter="transition-opacity ease-linear duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition-opacity ease-linear duration-300"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     @click="sidebarOpen = false" 
     class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm z-40 lg:hidden">
</div>

<!-- Left Sidebar Component (Bright & Eye-Catching Light Theme) -->
<aside class="fixed top-0 left-0 bottom-0 w-64 bg-white text-gray-700 flex flex-col z-50 transition-transform duration-300 ease-in-out border-r border-gray-200 shadow-xl shadow-gray-200/50"
       :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">

    <!-- Sidebar Header / Logo -->
    <div class="h-16 px-5 flex items-center justify-between border-b border-gray-200/80 bg-white">
        <a href="<?= base_url() ?>" class="flex items-center gap-3 group">
            <img src="<?= base_url('assets/img/logo.png') ?>" alt="Logo" class="h-9 w-auto object-contain transition-transform group-hover:scale-105">
            <div class="flex flex-col">
                <span class="text-sm font-bold text-gray-900 tracking-tight leading-tight group-hover:text-blue-600 transition-colors">Stripmap BMBK</span>
                <span class="text-[10px] font-medium text-gray-500">Provinsi Lampung</span>
            </div>
        </a>
        <button type="button" @click="sidebarOpen = false" class="lg:hidden text-gray-400 hover:text-gray-700 p-1.5 rounded-lg hover:bg-gray-100 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>

    <!-- Sidebar Menu List -->
    <div class="flex-1 overflow-y-auto py-5 px-3.5 space-y-6 custom-scrollbar">
        <?php foreach ($menuGroups as $group): ?>
            <div>
                <h3 class="px-3 text-[10px] font-extrabold text-blue-900/50 uppercase tracking-widest mb-2.5">
                    <?= $group['title'] ?>
                </h3>
                <div class="space-y-1">
                    <?php foreach ($group['items'] as $item): ?>
                        <?php
                        $hasSub = !empty($item['sub']);
                        $isItemActive = false;
                        if (!$hasSub) {
                            foreach ($item['match'] as $m) {
                                if ($urlMatches($m, $currentUrl)) {
                                    $isItemActive = true;
                                    break;
                                }
                            }
                        } else {
                            foreach ($item['sub'] as $subItem) {
                                foreach ($subItem['match'] as $sm) {
                                    if ($urlMatches($sm, $currentUrl)) {
                                        $isItemActive = true;
                                        break 2;
                                    }
                                }
                            }
                        }
                        ?>

                        <?php if (!$hasSub): ?>
                            <!-- Single Link Menu -->
                            <a href="<?= base_url($item['url']) ?>" 
                               class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 <?= $isItemActive ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-md shadow-blue-500/25' : 'text-gray-700 hover:bg-blue-50/80 hover:text-blue-700' ?>">
                                <?= $item['icon'] ?>
                                <span><?= $item['label'] ?></span>
                            </a>
                        <?php else: ?>
                            <!-- Dropdown / Accordion Menu -->
                            <div x-data="{ open: <?= $isItemActive ? 'true' : 'false' ?> }" class="space-y-1">
                                <button type="button" 
                                        @click="open = !open" 
                                        class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 <?= $isItemActive ? 'bg-blue-50/80 text-blue-900 border border-blue-100' : 'text-gray-700 hover:bg-blue-50/80 hover:text-blue-700' ?>">
                                    <div class="flex items-center gap-3">
                                        <?= $item['icon'] ?>
                                        <span><?= $item['label'] ?></span>
                                    </div>
                                    <svg class="w-4 h-4 transition-transform duration-200" :class="open ? 'rotate-90 text-blue-600' : 'rotate-0 text-gray-400'" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </button>

                                <!-- Submenu list -->
                                <div x-show="open" x-collapse class="pl-4 pr-1 space-y-1 pt-1">
                                    <?php foreach ($item['sub'] as $sub): ?>
                                        <?php
                                        $isSubActive = false;
                                        if ($sub['url'] === 'ruas' && str_starts_with($currentUrl, 'stripmap/compare')) {
                                            $isSubActive = false;
                                        } else {
                                            foreach ($sub['match'] as $sm) {
                                                if ($urlMatches($sm, $currentUrl)) {
                                                    $isSubActive = true;
                                                    break;
                                                }
                                            }
                                        }
                                        ?>
                                        <a href="<?= str_starts_with($sub['url'], '?') ? base_url() . $sub['url'] : base_url($sub['url']) ?>" 
                                           <?php if (!empty($sub['onclick'])): ?>onclick="<?= $sub['onclick'] ?>"<?php endif; ?>
                                           class="flex items-center justify-between gap-2.5 px-3.5 py-2 rounded-lg text-xs font-semibold transition-all duration-200 <?= $isSubActive ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/20' : 'text-gray-600 hover:text-blue-700 hover:bg-blue-50/60' ?>">
                                            <div class="flex items-center gap-2.5">
                                                <span class="w-1.5 h-1.5 rounded-full <?= $isSubActive ? 'bg-white' : 'bg-blue-400' ?>"></span>
                                                <span><?= $sub['label'] ?></span>
                                            </div>
                                            <?php if (!empty($sub['badge'])): ?>
                                                <span class="text-[9px] font-extrabold px-1.5 py-0.5 rounded bg-amber-100 text-amber-800 border border-amber-200/80 tracking-wide uppercase"><?= $sub['badge'] ?></span>
                                            <?php endif; ?>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Sidebar Footer / Profile Info -->
    <div class="p-4 border-t border-gray-200/80 bg-gray-50/70 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-full bg-blue-600 text-white font-bold text-xs flex items-center justify-center shadow-sm shadow-blue-500/30">
                BM
            </div>
            <div class="flex flex-col">
                <span class="text-xs font-bold text-gray-900">Admin BMBK</span>
                <span class="text-[10px] font-medium text-gray-500">Dinas BMBK Lampung</span>
            </div>
        </div>
    </div>
</aside>
