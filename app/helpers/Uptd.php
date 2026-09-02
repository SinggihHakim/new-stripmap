<?php

/**
 * ============================================================
 * UPTD Helper
 * ============================================================
 * Pengelompokan Kabupaten / Kota berdasarkan wilayah UPTD Dinas BMBK.
 */
class Uptd
{
    /**
     * Mapping Master UPTD ke Kabupaten / Kota
     */
    public const MAP = [
        'UPTD I'   => ['Bandar Lampung', 'Pesawaran', 'Pringsewu'],
        'UPTD II'  => ['Lampung Selatan', 'Lampung Timur'],
        'UPTD III' => ['Metro', 'Lampung Tengah'],
        'UPTD IV'  => ['Lampung Utara', 'Way Kanan'],
        'UPTD V'   => ['Tanggamus', 'Lampung Barat', 'Pesisir Barat'],
        'UPTD VI'  => ['Tulang Bawang', 'Tulang Bawang Barat', 'Mesuji'],
    ];

    /**
     * Daftar lengkap Kabupaten / Kota Provinsi Lampung
     */
    public const KABUPATEN_LIST = [
        'Lampung Selatan',
        'Lampung Tengah',
        'Lampung Utara',
        'Lampung Barat',
        'Tulang Bawang',
        'Tanggamus',
        'Way Kanan',
        'Pesawaran',
        'Pringsewu',
        'Mesuji',
        'Tulang Bawang Barat',
        'Pesisir Barat',
        'Lampung Timur',
        'Bandar Lampung',
        'Metro',
    ];

    /**
     * Dapatkan semua daftar UPTD beserta kabupaten/kotanya
     */
    public static function all(): array
    {
        return self::MAP;
    }

    /**
     * Singkatan Pintar (Smart Abbreviation) untuk label Y-axis chart
     */
    public static function getShortName(string $name): string
    {
        $clean = trim(preg_replace('/^(kabupaten|kab\.|kota)\s+/i', '', $name));
        $map = [
            'Tulang Bawang Barat' => 'Tuba Bar',
            'Tulang Bawang'       => 'Tuba',
            'Pesisir Barat'       => 'Pesibar',
            'Lampung Selatan'     => 'Lamsel',
            'Lampung Timur'       => 'Lamtim',
            'Lampung Tengah'      => 'Lamteng',
            'Lampung Utara'       => 'Lamut',
            'Lampung Barat'       => 'Lambar',
            'Bandar Lampung'      => 'B. Lampung',
            'Way Kanan'           => 'Way Kanan',
            'Pesawaran'           => 'Pesawaran',
            'Pringsewu'           => 'Pringsewu',
            'Tanggamus'           => 'Tanggamus',
            'Mesuji'              => 'Mesuji',
            'Metro'               => 'Metro',
        ];
        return $map[$clean] ?? $clean;
    }

    /**
     * Normalisasi nama kabupaten/kota untuk matching fleksibel
     */
    public static function normalizeName(string $name): string
    {
        $name = mb_strtolower(trim($name));
        $name = preg_replace('/^(kabupaten|kab\.|kota)\s+/i', '', $name);
        $name = str_replace([' ', '-'], '', $name);
        return $name;
    }

    /**
     * Dapatkan daftar UPTD tempat kabupaten/kota berada.
     * Mengembalikan array nama UPTD (contoh: ['UPTD 1', 'UPTD 3'] untuk Metro, atau ['UPTD 1'] untuk Pesawaran)
     *
     * @param string $kabupaten Nama kabupaten / kota
     * @return array
     */
    public static function getUptdByKabupaten(string $kabupaten): array
    {
        $norm = self::normalizeName($kabupaten);
        if (empty($norm)) {
            return [];
        }

        $matched = [];
        foreach (self::MAP as $uptd => $list) {
            foreach ($list as $item) {
                if (self::normalizeName($item) === $norm) {
                    $matched[] = $uptd;
                    break;
                }
            }
        }

        return $matched;
    }

    /**
     * Dapatkan nama UPTD sebagai string terformat (misal: "UPTD 1" atau "UPTD 1, UPTD 3")
     *
     * @param string $kabupaten
     * @param string $default
     * @return string
     */
    public static function getUptdString(string $kabupaten, string $default = '-'): string
    {
        $list = self::getUptdByKabupaten($kabupaten);
        return !empty($list) ? implode(', ', $list) : $default;
    }

    /**
     * Dapatkan daftar kabupaten/kota yang berada di bawah UPTD tertentu
     *
     * @param string $uptd (misal 'UPTD 1')
     * @return array
     */
    public static function getKabupatenByUptd(string $uptd): array
    {
        $key = strtoupper(trim($uptd));
        foreach (self::MAP as $k => $list) {
            if (strtoupper($k) === $key) {
                return $list;
            }
        }
        return [];
    }

    /**
     * Kelompokkan kumpulan data (array of array / array of object) berdasarkan UPTD
     *
     * @param array $items
     * @param string $kabupatenKey Key dari field kabupaten_kota
     * @return array
     */
    public static function groupByUptd(array $items, string $kabupatenKey = 'kabupaten_kota'): array
    {
        $grouped = [];
        foreach (self::MAP as $uptd => $list) {
            $grouped[$uptd] = [];
        }
        $grouped['Lainnya'] = [];

        foreach ($items as $item) {
            $kab = is_array($item) ? ($item[$kabupatenKey] ?? '') : ($item->$kabupatenKey ?? '');
            $uptds = self::getUptdByKabupaten((string)$kab);

            if (empty($uptds)) {
                $grouped['Lainnya'][] = $item;
            } else {
                foreach ($uptds as $u) {
                    $grouped[$u][] = $item;
                }
            }
        }

        return array_filter($grouped);
    }
}
