<?php
/**
 * View: Database Connection Error
 * Ditampilkan ketika koneksi ke database gagal.
 * $errorMessage tersedia dalam mode debug.
 */
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Tidak Tersedia</title>
    <link rel="shortcut icon" href="<?= rtrim($_ENV['APP_URL'] ?? '', '/') ?>/assets/img/favicon.png" type="image/png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 max-w-md w-full p-8 text-center">
        <div class="text-5xl mb-4">🗄️</div>
        <h1 class="text-xl font-bold text-gray-900 mb-2">Database Belum Tersedia</h1>

        <?php if (!empty($errorMessage)): ?>
        <p class="text-sm text-red-600 bg-red-50 rounded-lg p-3 mb-4 font-mono text-left break-all">
            <?= htmlspecialchars($errorMessage) ?>
        </p>
        <?php endif; ?>

        <p class="text-sm text-gray-500 mb-6">
            Sistem tidak dapat terhubung ke server database. Silakan pastikan server database aktif dan periksa kredensial di file <code class="px-1.5 py-0.5 bg-gray-100 rounded text-gray-700 font-mono text-xs">.env</code>.
        </p>
        <button onclick="window.location.reload()"
           class="inline-flex items-center gap-2 px-6 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-xl hover:bg-blue-700 transition-colors shadow-sm cursor-pointer">
            Muat Ulang Halaman
        </button>
    </div>
</body>
</html>
