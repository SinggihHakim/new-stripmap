<?php

/**
 * ============================================================
 * Helper: TahunHelper
 * ============================================================
 * Single source of truth untuk daftar tahun yang digunakan
 * di seluruh aplikasi (filter, dropdown, prediksi, chart).
 *
 * Untuk mengubah range tahun, cukup ubah konstanta di bawah ini.
 * Tidak perlu mengubah file lain.
 */
class TahunHelper
{
    /** Tahun pertama dalam range aplikasi */
    const TAHUN_AWAL  = 2025;

    /** Tahun terakhir dalam range aplikasi */
    const TAHUN_AKHIR = 2029;

    /**
     * Kembalikan array daftar tahun [2025, 2026, ..., 2030]
     */
    public static function getList(): array
    {
        return range(self::TAHUN_AWAL, self::TAHUN_AKHIR);
    }

    /**
     * Kembalikan tahun awal
     */
    public static function awal(): int
    {
        return self::TAHUN_AWAL;
    }

    /**
     * Kembalikan tahun akhir
     */
    public static function akhir(): int
    {
        return self::TAHUN_AKHIR;
    }

    /**
     * Validasi apakah sebuah nilai tahun valid (ada dalam range)
     */
    public static function isValid(int $tahun): bool
    {
        return $tahun >= self::TAHUN_AWAL && $tahun <= self::TAHUN_AKHIR;
    }

    /**
     * Parse tahun dari GET parameter dengan validasi.
     * Kembalikan (int) jika valid, null jika tidak ada/tidak valid.
     *
     * @param string $key Nama parameter GET, default 'tahun'
     */
    public static function fromRequest(string $key = 'tahun'): ?int
    {
        if (!isset($_GET[$key]) || !is_numeric($_GET[$key])) {
            return null;
        }
        $tahun = (int)$_GET[$key];
        return $tahun > 0 ? $tahun : null;
    }

    /**
     * Warna per tahun untuk chart (konsisten di seluruh aplikasi)
     */
    public static function getWarna(): array
    {
        return [
            2025 => '#0284c7', // blue-600
            2026 => '#7c3aed', // violet-600
            2027 => '#dc2626', // red-600
            2028 => '#d97706', // amber-600
            2029 => '#059669', // emerald-600
        ];
    }

    /**
     * Kembalikan warna untuk tahun tertentu
     */
    public static function getWarnaByTahun(int $tahun): string
    {
        $map = self::getWarna();
        return $map[$tahun] ?? '#6b7280'; // gray-500 sebagai fallback
    }
}
