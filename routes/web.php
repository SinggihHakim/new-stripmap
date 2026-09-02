<?php

/**
 * ============================================================
 * Web Routes
 * ============================================================
 * Definisikan seluruh route aplikasi di sini.
 */

$router = new Router();

// ──────────────────────────────────────────────
// Halaman Utama (Dashboard) & Detail
// ──────────────────────────────────────────────
$router->get('',                 'DashboardController', 'index');
$router->get('dashboard',        'DashboardController', 'index');
$router->get('dashboard/detail', 'DashboardController', 'detail');

// ──────────────────────────────────────────────
// CRUD Ruas Jalan
// ──────────────────────────────────────────────
$router->get('ruas',              'RuasController', 'index');
$router->get('ruas/create',      'RuasController', 'create');
$router->post('ruas/store',      'RuasController', 'store');
$router->get('ruas/import',      'RuasController', 'importForm');
$router->post('ruas/import',     'RuasController', 'importProcess');
$router->get('ruas/edit/{id}',   'RuasController', 'edit');
$router->post('ruas/update/{id}','RuasController', 'update');
$router->post('ruas/delete/{id}', 'RuasController', 'delete');
$router->get('ruas/show/{id}',   'RuasController', 'show');

// ──────────────────────────────────────────────
// CRUD Strip Map
// ──────────────────────────────────────────────
$router->get('stripmap/{id}',              'StripmapController', 'index');
$router->get('stripmap/create/{id}',      'StripmapController', 'create');
$router->post('stripmap/store/{id}',      'StripmapController', 'store');
$router->post('stripmap/batch/{id}',      'StripmapController', 'batch');
$router->get('stripmap/edit/{id}',        'StripmapController', 'edit');
$router->post('stripmap/update/{id}',     'StripmapController', 'update');
$router->post('stripmap/delete/{id}',  'StripmapController', 'delete');
$router->get('stripmap/preview/{id}',     'StripmapController', 'preview');
$router->get('stripmap/compare',          'StripmapController', 'compare');
$router->get('stripmap/compare/{id}',     'StripmapController', 'compare');
$router->post('stripmap/import-kml/{id}', 'StripmapController', 'importKml');

// ──────────────────────────────────────────────
// CRUD Perkerasan Jalan
// ──────────────────────────────────────────────
$router->post('perkerasan/store/{id}',    'StripmapController', 'perkerasanStore');
$router->get('perkerasan/edit/{id}',      'StripmapController', 'perkerasanEdit');
$router->post('perkerasan/update/{id}',   'StripmapController', 'perkerasanUpdate');
$router->post('perkerasan/delete/{id}',    'StripmapController', 'perkerasanDelete');

// ──────────────────────────────────────────────
// Foto Lapangan Real STA
// ──────────────────────────────────────────────
$router->post('foto-lapangan/upload/{id}', 'StripmapController', 'uploadFoto');
$router->post('foto-lapangan/delete/{id}', 'StripmapController', 'deleteFoto');

// ──────────────────────────────────────────────
// CRUD Segmentasi Penanganan Jalan
// ──────────────────────────────────────────────
$router->post('penanganan/store/{id}',         'PenangananController', 'store');
$router->post('penanganan/update/{id}',        'PenangananController', 'update');
$router->post('penanganan/delete/{id}',        'PenangananController', 'delete');
$router->post('penanganan/apply-kondisi/{id}', 'PenangananController', 'applyKondisi');


// ──────────────────────────────────────────────
// Rekapitulasi Eksekutif
// ──────────────────────────────────────────────
$router->get('rekap/kemantapan', 'RekapController', 'kemantapan');
$router->get('rekap/perkerasan', 'RekapController', 'perkerasan');
$router->get('rekap/prediksi',   'PrediksiController', 'index');
$router->get('rekap/prediksi/{id}', 'PrediksiController', 'detail');

// ──────────────────────────────────────────────
// Pusat Export & Cetak
// ──────────────────────────────────────────────
$router->get('export', 'ExportController', 'index');

// Jalankan router
$router->dispatch();
