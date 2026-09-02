<?php

/**
 * ============================================================
 * Service: PrediksiService
 * ============================================================
 * Mengimplementasikan matriks logika prediksi kondisi jalan
 * berdasarkan dokumen "Ide Strip Map.xlsx".
 *
 * Input  : kondisi awal (B/S/RR/RB) + perkerasan awal + jenis_pelaksana
 * Output : kondisi_prediksi + perkerasan_hasil + perlu_verifikasi + pesan
 */

class PrediksiService
{
    // -------------------------------------------------------
    // Konstanta Label untuk Jenis Pelaksana
    // -------------------------------------------------------
    public const PELAKSANA_LABELS = [
        'pihak_ke3_rigid'               => 'Pihak Ke-3 (Rigid)',
        'pihak_ke3_aspal'               => 'Pihak Ke-3 (Aspal)',
        'rutin_uptd'                    => 'Rutin UPTD',
        'urc_overlay_tanpa_finisher'    => 'URC UPTD (Overlay Tanpa Finisher)',
        'urc_overlay_dengan_finisher'   => 'URC UPTD (Overlay Dengan Finisher)',
        'urc_rigid'                     => 'URC UPTD (Rigid)',
        'urc_base'                      => 'URC UPTD (Base)',
    ];

    // -------------------------------------------------------
    // Konstanta Label untuk Kondisi
    // -------------------------------------------------------
    public const KONDISI_LABELS = [
        'baik'         => 'Baik',
        'sedang'       => 'Sedang',
        'rusak_ringan' => 'Rusak Ringan',
        'rusak_berat'  => 'Rusak Berat',
    ];

    public const KONDISI_COLORS = [
        'baik'         => '#22c55e',  // green-500
        'sedang'       => '#eab308',  // yellow-500
        'rusak_ringan' => '#f97316',  // orange-500
        'rusak_berat'  => '#ef4444',  // red-500
    ];

    // -------------------------------------------------------
    // Konstanta Label untuk Perkerasan
    // -------------------------------------------------------
    public const PERKERASAN_LABELS = [
        'rigid'         => 'Rigid',
        'aspal'         => 'Aspal',
        'agregat_tanah' => 'Kerikil',
        'belum_tembus'  => 'Belum Tembus',
    ];

    /**
     * Hitung prediksi kondisi jalan berdasarkan matriks dari Ide Strip Map.xlsx
     *
     * @param string $kondisiAwal     Kondisi awal: 'baik'|'sedang'|'rusak_ringan'|'rusak_berat'
     * @param string $perkerasanAwal  Perkerasan awal: 'rigid'|'aspal'|'agregat_tanah'|'belum_tembus'
     * @param string $jenisPelaksana  Jenis pelaksana dari const PELAKSANA_LABELS
     * @return array {
     *   kondisi_prediksi: string,
     *   perkerasan_hasil: string,
     *   perlu_verifikasi: bool,
     *   bisa_dilaksanakan: bool,
     *   pesan: string
     * }
     */
    public function hitung(string $kondisiAwal, string $perkerasanAwal, string $jenisPelaksana): array
    {
        // Normalisasi input
        $kondisi    = strtolower(trim($kondisiAwal));
        $perkerasan = strtolower(trim($perkerasanAwal));
        $pelaksana  = strtolower(trim($jenisPelaksana));

        return match ($pelaksana) {
            'pihak_ke3_rigid'             => $this->hitungPihakKe3Rigid($kondisi, $perkerasan),
            'pihak_ke3_aspal'             => $this->hitungPihakKe3Aspal($kondisi, $perkerasan),
            'rutin_uptd'                  => $this->hitungRutinUptd($kondisi, $perkerasan),
            'urc_overlay_tanpa_finisher'  => $this->hitungUrcOverlayTanpaFinisher($kondisi, $perkerasan),
            'urc_overlay_dengan_finisher' => $this->hitungUrcOverlayDenganFinisher($kondisi, $perkerasan),
            'urc_rigid'                   => $this->hitungUrcRigid($kondisi, $perkerasan),
            'urc_base'                    => $this->hitungUrcBase($kondisi, $perkerasan),
            default                       => $this->tidakDiketahui(),
        };
    }

    /**
     * Hitung prediksi untuk seluruh segmen penanganan dalam satu ruas.
     * Mengembalikan summary: total panjang per kondisi sebelum dan sesudah.
     *
     * @param array $penangananList  Data dari PenangananService::getByRuasId()
     * @param array $stripmapList    Data dari StripmapService::getByRuasId()
     * @return array {
     *   sebelum: [baik, sedang, rusak_ringan, rusak_berat],
     *   sesudah: [baik, sedang, rusak_ringan, rusak_berat],
     *   detail: [...per segmen...]
     * }
     */
    public function hitungSummary(array $penangananList, array $stripmapList): array
    {
        // Hitung kondisi sebelum dari stripmap (dalam meter)
        $sebelum = ['baik' => 0.0, 'sedang' => 0.0, 'rusak_ringan' => 0.0, 'rusak_berat' => 0.0];
        foreach ($stripmapList as $sm) {
            $sebelum['baik']         += (float)($sm['baik'] ?? 0);
            $sebelum['sedang']       += (float)($sm['sedang'] ?? 0);
            $sebelum['rusak_ringan'] += (float)($sm['rusak_ringan'] ?? 0);
            $sebelum['rusak_berat']  += (float)($sm['rusak_berat'] ?? 0);
        }

        // Buat salinan kondisi sesudah yang akan dimodifikasi berdasarkan penanganan
        $sesudah = $sebelum;
        $detail  = [];

        foreach ($penangananList as $p) {
            $pAwal  = (float)$p['sta_awal'];
            $pAkhir = (float)$p['sta_akhir'];
            $panjangPenanganan = (float)$p['panjang'];

            // Tentukan kondisi awal dominan pada segmen ini dari stripmap
            [$kondisiDominan, $perkerasanDominan] = $this->getKondisiDominan($stripmapList, $pAwal, $pAkhir);

            if (empty($p['jenis_pelaksana'])) {
                // Penanganan tanpa jenis_pelaksana: tampilkan di detail tapi tidak ada prediksi
                $detail[] = [
                    'id'               => $p['id'],
                    'sta_awal'         => $p['sta_awal'],
                    'sta_akhir'        => $p['sta_akhir'],
                    'panjang'          => $panjangPenanganan,
                    'jenis_pelaksana'  => null,
                    'label_pelaksana'  => '— Belum dikonfigurasi —',
                    'kondisi_sebelum'  => $kondisiDominan,
                    'perkerasan_awal'  => $perkerasanDominan,
                    'kondisi_sesudah'  => null,
                    'perkerasan_hasil' => null,
                    'perlu_verifikasi' => false,
                    'bisa_dilaksanakan'=> false,
                    'pesan'            => 'Pilih Jenis Pelaksana di form penanganan agar prediksi dapat dihitung.',
                    'status'           => $p['status'],
                ];
                continue;
            }

            // Hitung prediksi
            $prediksi = $this->hitung($kondisiDominan, $perkerasanDominan, $p['jenis_pelaksana']);

            // Simpan ke kolom prediksi di data penanganan (untuk detail)
            $detail[] = [
                'id'               => $p['id'],
                'sta_awal'         => $p['sta_awal'],
                'sta_akhir'        => $p['sta_akhir'],
                'panjang'          => $panjangPenanganan,
                'jenis_pelaksana'  => $p['jenis_pelaksana'],
                'label_pelaksana'  => self::PELAKSANA_LABELS[$p['jenis_pelaksana']] ?? $p['jenis_pelaksana'],
                'kondisi_sebelum'  => $kondisiDominan,
                'perkerasan_awal'  => $perkerasanDominan,
                'kondisi_sesudah'  => $prediksi['kondisi_prediksi'],
                'perkerasan_hasil' => $prediksi['perkerasan_hasil'],
                'perlu_verifikasi' => $prediksi['perlu_verifikasi'],
                'bisa_dilaksanakan'=> $prediksi['bisa_dilaksanakan'],
                'pesan'            => $prediksi['pesan'],
                'status'           => $p['status'],
            ];

            // Update sesudah: hitung per segmen stripmap yang di-overlap
            // Ini lebih akurat daripada menggunakan satu kondisi dominan
            if ($prediksi['bisa_dilaksanakan'] && !$prediksi['perlu_verifikasi']) {
                $sesudahKey = $prediksi['kondisi_prediksi'];  // e.g. 'baik'

                foreach ($stripmapList as $sm) {
                    $smAwal  = (float)$sm['sta_awal'];
                    $smAkhir = (float)$sm['sta_akhir'];

                    $overlapStart = max($pAwal, $smAwal);
                    $overlapEnd   = min($pAkhir, $smAkhir);
                    if ($overlapEnd <= $overlapStart) {
                        continue; // tidak ada overlap
                    }

                    $overlapLen = $overlapEnd - $overlapStart;
                    $smPanjang  = (float)$sm['panjang'] ?: 1;
                    $ratio      = $overlapLen / $smPanjang;

                    // Untuk setiap kondisi di dalam segmen ini, ubah proporsi yang di-overlap
                    foreach (['baik', 'sedang', 'rusak_ringan', 'rusak_berat'] as $k) {
                        $porsi = (float)($sm[$k] ?? 0) * $ratio;
                        if ($porsi <= 0) continue;
                        if ($k !== $sesudahKey) {
                            // Kurangi dari kondisi lama
                            $sesudah[$k] = max(0.0, $sesudah[$k] - $porsi);
                            // Tambahkan ke kondisi prediksi
                            $sesudah[$sesudahKey] += $porsi;
                        }
                    }
                }
            }
        }

        return [
            'sebelum' => $sebelum,
            'sesudah' => $sesudah,
            'detail'  => $detail,
        ];
    }

    // -------------------------------------------------------
    // Ambil kondisi & perkerasan dominan dalam rentang STA
    // -------------------------------------------------------

    private function getKondisiDominan(array $stripmapList, float $staAwal, float $staAkhir): array
    {
        $kondisiLengths   = ['baik' => 0.0, 'sedang' => 0.0, 'rusak_ringan' => 0.0, 'rusak_berat' => 0.0];
        $perkerasanLengths = ['rigid' => 0.0, 'aspal' => 0.0, 'agregat_tanah' => 0.0, 'belum_tembus' => 0.0];

        foreach ($stripmapList as $sm) {
            $smAwal  = (float)$sm['sta_awal'];
            $smAkhir = (float)$sm['sta_akhir'];

            $overlapStart = max($staAwal, $smAwal);
            $overlapEnd   = min($staAkhir, $smAkhir);
            if ($overlapEnd <= $overlapStart) {
                continue;
            }
            $overlapLen = $overlapEnd - $overlapStart;
            $smPanjang  = (float)$sm['panjang'] ?: 1;
            $ratio      = $overlapLen / $smPanjang;

            $kondisiLengths['baik']         += (float)($sm['baik'] ?? 0) * $ratio;
            $kondisiLengths['sedang']        += (float)($sm['sedang'] ?? 0) * $ratio;
            $kondisiLengths['rusak_ringan']  += (float)($sm['rusak_ringan'] ?? 0) * $ratio;
            $kondisiLengths['rusak_berat']   += (float)($sm['rusak_berat'] ?? 0) * $ratio;
        }

        // Kondisi dominan = yang terpanjang
        arsort($kondisiLengths);
        $kondisiDominan = array_key_first($kondisiLengths) ?? 'baik';

        // Untuk perkerasan, kita gunakan nilai dari stripmap juga (default aspal jika tidak ada data perkerasan)
        // Catatan: jika ada tabel perkerasan terpisah, bisa di-join, tapi untuk sekarang default ke 'aspal'
        $perkerasanDominan = 'aspal';

        return [$kondisiDominan, $perkerasanDominan];
    }

    // -------------------------------------------------------
    // MATRIKS PREDIKSI — Per Jenis Pelaksana
    // -------------------------------------------------------

    /**
     * Pihak Ke-3 (Rigid) → semua kondisi → Baik - Rigid
     */
    private function hitungPihakKe3Rigid(string $kondisi, string $perkerasan): array
    {
        return $this->hasil('baik', 'rigid',
            "Ditangani oleh Pihak Ke-3 (Rigid). Prediksi hasil: Baik - Rigid."
        );
    }

    /**
     * Pihak Ke-3 (Aspal) → semua kondisi → Baik - Aspal
     */
    private function hitungPihakKe3Aspal(string $kondisi, string $perkerasan): array
    {
        return $this->hasil('baik', 'aspal',
            "Ditangani oleh Pihak Ke-3 (Aspal). Prediksi hasil: Baik - Aspal."
        );
    }

    /**
     * Rutin UPTD:
     * - B → B (pertahankan kondisi Baik)
     * - S → B (naik ke Baik)
     * - RR, RB → TIDAK BISA → peringatan, rekomendasi URC
     */
    private function hitungRutinUptd(string $kondisi, string $perkerasan): array
    {
        return match ($kondisi) {
            'baik'   => $this->hasil('baik', $perkerasan,
                "Baik - {$perkerasan} ditangani oleh Rutin UPTD. Prediksi hasil: Baik - {$perkerasan}."
            ),
            'sedang' => $this->hasil('baik', $perkerasan,
                "Sedang - {$perkerasan} ditangani oleh Rutin UPTD. Prediksi hasil: Baik - {$perkerasan}."
            ),
            default  => $this->tidakBisaDilaksanakan(
                "Kondisi " . (self::KONDISI_LABELS[$kondisi] ?? $kondisi) . " tidak dapat ditangani oleh Rutin UPTD. " .
                "Perlu Verifikasi Manual & Rekomendasi Penanganan URC."
            ),
        };
    }

    /**
     * URC UPTD (Overlay Tanpa Finisher):
     * - B  → Baik - Aspal
     * - S   → Sedang - Aspal
     * - RR  → Sedang - Aspal
     * - RB  → Sedang - Aspal
     */
    private function hitungUrcOverlayTanpaFinisher(string $kondisi, string $perkerasan): array
    {
        $hasilKondisi = ($kondisi === 'baik') ? 'baik' : 'sedang';
        return $this->hasil($hasilKondisi, 'aspal',
            ucfirst(self::KONDISI_LABELS[$kondisi] ?? $kondisi) . " - {$perkerasan} ditangani URC UPTD (Overlay Tanpa Finisher). " .
            "Prediksi hasil: " . (self::KONDISI_LABELS[$hasilKondisi] ?? $hasilKondisi) . " - Aspal."
        );
    }

    /**
     * URC UPTD (Overlay Dengan Finisher):
     * - Semua kondisi → Baik - Aspal
     */
    private function hitungUrcOverlayDenganFinisher(string $kondisi, string $perkerasan): array
    {
        return $this->hasil('baik', 'aspal',
            ucfirst(self::KONDISI_LABELS[$kondisi] ?? $kondisi) . " - {$perkerasan} ditangani URC UPTD (Overlay Dengan Finisher). " .
            "Prediksi hasil: Baik - Aspal."
        );
    }

    /**
     * URC UPTD (Rigid):
     * - Semua kondisi → Baik - Rigid
     */
    private function hitungUrcRigid(string $kondisi, string $perkerasan): array
    {
        return $this->hasil('baik', 'rigid',
            ucfirst(self::KONDISI_LABELS[$kondisi] ?? $kondisi) . " - {$perkerasan} ditangani URC UPTD (Rigid). " .
            "Prediksi hasil: Baik - Rigid."
        );
    }

    /**
     * URC UPTD (Base):
     * - B, S, RR → Perlu Verifikasi Manual (kompleks, bergantung kondisi aktual)
     * - RB → RB - Kerikil
     */
    private function hitungUrcBase(string $kondisi, string $perkerasan): array
    {
        if ($kondisi === 'rusak_berat') {
            return $this->hasil('rusak_berat', 'agregat_tanah',
                "Rusak Berat ditangani URC UPTD (Base). Prediksi hasil: Rusak Berat - Kerikil."
            );
        }

        // B, S, RR → Perlu Verifikasi Manual
        return $this->hasilVerifikasi('baik', $perkerasan,
            "Kondisi " . (self::KONDISI_LABELS[$kondisi] ?? $kondisi) . " - {$perkerasan} ditangani URC UPTD (Base). " .
            "Perlu Verifikasi Manual:\n" .
            "A. Apakah Terjadi Penurunan Kondisi? (Ya/Tidak)\n" .
            "B. Apakah Untuk Menutup Lubang? (Ya/Tidak)\n" .
            "Jika A=Ya → Hasil: Rusak Berat - Kerikil\n" .
            "Jika A=Tidak → Hasil: " . (self::KONDISI_LABELS[$kondisi] ?? ucfirst($kondisi)) . " - {$perkerasan}"
        );
    }

    // -------------------------------------------------------
    // Helper Builders
    // -------------------------------------------------------

    private function hasil(string $kondisi, string $perkerasan, string $pesan): array
    {
        return [
            'kondisi_prediksi'  => $kondisi,
            'perkerasan_hasil'  => $perkerasan,
            'perlu_verifikasi'  => false,
            'bisa_dilaksanakan' => true,
            'pesan'             => $pesan,
        ];
    }

    private function hasilVerifikasi(string $kondisiDefault, string $perkerasanDefault, string $pesan): array
    {
        return [
            'kondisi_prediksi'  => $kondisiDefault,
            'perkerasan_hasil'  => $perkerasanDefault,
            'perlu_verifikasi'  => true,
            'bisa_dilaksanakan' => true,
            'pesan'             => $pesan,
        ];
    }

    private function tidakBisaDilaksanakan(string $pesan): array
    {
        return [
            'kondisi_prediksi'  => null,
            'perkerasan_hasil'  => null,
            'perlu_verifikasi'  => false,
            'bisa_dilaksanakan' => false,
            'pesan'             => $pesan,
        ];
    }

    private function tidakDiketahui(): array
    {
        return [
            'kondisi_prediksi'  => null,
            'perkerasan_hasil'  => null,
            'perlu_verifikasi'  => false,
            'bisa_dilaksanakan' => false,
            'pesan'             => 'Jenis pelaksana tidak dikenali.',
        ];
    }

    // -------------------------------------------------------
    // Helper: Label & Badge
    // -------------------------------------------------------

    public static function getPelaksanaLabel(string $key): string
    {
        return self::PELAKSANA_LABELS[$key] ?? ucfirst(str_replace('_', ' ', $key));
    }

    public static function getKondisiLabel(string $key): string
    {
        return self::KONDISI_LABELS[$key] ?? ucfirst(str_replace('_', ' ', $key));
    }

    public static function getKondisiColor(string $key): string
    {
        return self::KONDISI_COLORS[$key] ?? '#94a3b8';
    }

    public static function getPerkerasanLabel(string $key): string
    {
        return self::PERKERASAN_LABELS[$key] ?? ucfirst(str_replace('_', ' ', $key));
    }
}
