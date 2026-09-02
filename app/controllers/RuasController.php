<?php

/**
 * ============================================================
 * Controller: RuasController
 * ============================================================
 * CRUD untuk data ruas jalan (termasuk strip map & perkerasan).
 */

class RuasController
{
    private RuasService $service;

    public function __construct()
    {
        $this->service = new RuasService();
    }

    /**
     * Tampilkan daftar ruas jalan
     */
    public function index(): void
    {
        $data = [
            'title'    => 'Data Ruas Jalan',
            'ruasList' => $this->service->getAll(),
        ];
        view('layouts.app', array_merge($data, ['content' => 'ruas.index']));
    }

    /**
     * Form tambah ruas
     */
    public function create(): void
    {
        $data = [
            'title' => 'Tambah Ruas Jalan',
        ];
        view('layouts.app', array_merge($data, ['content' => 'ruas.form']));
    }

    /**
     * Proses simpan ruas baru beserta stripmap & perkerasan
     */
    public function store(): void
    {
        $result = $this->service->create($_POST);

        if ($result['success']) {
            $ruasId = $result['id'];

            // 1. Simpan Data Strip Map (Kondisi Jalan)
            if (isset($_POST['rows']) && is_array($_POST['rows'])) {
                $rows = array_filter($_POST['rows'], function($row) {
                    return !empty(trim($row['sta_awal'] ?? '')) || !empty(trim($row['sta_akhir'] ?? ''));
                });

                if (!empty($rows)) {
                    $stripmapService = new StripmapService();
                    $stripmapResult = $stripmapService->batchCreate($ruasId, array_values($rows));

                    if (!$stripmapResult['success']) {
                        flash('error', "Ruas berhasil dibuat, tapi gagal menyimpan strip map: " . $stripmapResult['message']);
                    }
                }
            }

            // 2. Simpan Data Jenis Perkerasan Jalan
            if (isset($_POST['perkerasan_rows']) && is_array($_POST['perkerasan_rows'])) {
                $pkRows = array_filter($_POST['perkerasan_rows'], function($row) {
                    return !empty(trim($row['sta_awal'] ?? '')) || !empty(trim($row['sta_akhir'] ?? ''));
                });

                if (!empty($pkRows)) {
                    $perkerasanService = new PerkerasanService();
                    $pkResult = $perkerasanService->batchCreate($ruasId, array_values($pkRows));

                    if (!$pkResult['success']) {
                        flash('error', "Ruas berhasil dibuat, tapi gagal menyimpan perkerasan: " . $pkResult['message']);
                    }
                }
            }

            // Sinkronisasi STA & panjang ruas dari data stripmap
            $this->service->syncStaFromStripmap($ruasId);

            flash('success', $result['message']);
            redirect(base_url('ruas'));
        } else {
            flash('error', $result['message']);
            if (isset($_POST['rows'])) {
                $_SESSION['old_rows'] = $_POST['rows'];
            }
            if (isset($_POST['perkerasan_rows'])) {
                $_SESSION['old_perkerasan_rows'] = $_POST['perkerasan_rows'];
            }
            redirect(base_url('ruas/create'));
        }
    }

    /**
     * Form edit ruas
     */
    public function edit(int $id): void
    {
        $ruas = $this->service->findById($id);
        if (!$ruas) {
            flash('error', 'Ruas jalan tidak ditemukan.');
            redirect(base_url('ruas'));
            return;
        }

        $stripmapService   = new StripmapService();
        $perkerasanService = new PerkerasanService();

        $stripmaps   = $stripmapService->getByRuasId($id);
        $perkerasans = $perkerasanService->getByRuasId($id);

        $data = [
            'title'       => 'Edit Ruas Jalan',
            'ruas'        => $ruas,
            'stripmaps'   => $stripmaps,
            'perkerasans' => $perkerasans,
        ];
        view('layouts.app', array_merge($data, ['content' => 'ruas.form']));
    }

    /**
     * Proses update ruas
     */
    public function update(int $id): void
    {
        $result = $this->service->update($id, $_POST);

        if ($result['success']) {
            // 1. Update Data Strip Map HANYA JIKA form mengirimkan data rows yang valid
            if (isset($_POST['rows']) && is_array($_POST['rows'])) {
                $rows = array_filter($_POST['rows'], function($row) {
                    return !empty(trim($row['sta_awal'] ?? '')) || !empty(trim($row['sta_akhir'] ?? ''));
                });

                if (!empty($rows)) {
                    $stripmapService = new StripmapService();
                    $stripmapService->deleteByRuasId($id);
                    $stripmapService->batchCreate($id, array_values($rows));
                }
            }

            // 2. Update Data Jenis Perkerasan HANYA JIKA form mengirimkan data perkerasan_rows yang valid
            if (isset($_POST['perkerasan_rows']) && is_array($_POST['perkerasan_rows'])) {
                $pkRows = array_filter($_POST['perkerasan_rows'], function($row) {
                    return !empty(trim($row['sta_awal'] ?? '')) || !empty(trim($row['sta_akhir'] ?? ''));
                });

                if (!empty($pkRows)) {
                    $perkerasanService = new PerkerasanService();
                    $perkerasanService->deleteByRuasId($id);
                    $perkerasanService->batchCreate($id, array_values($pkRows));
                }
            }

            // Sinkronisasi STA & panjang ruas
            $this->service->syncStaFromStripmap($id);

            flash('success', 'Data ruas jalan, strip map, dan perkerasan berhasil diperbarui.');
            redirect(base_url('ruas'));
        } else {
            flash('error', $result['message']);
            if (isset($_POST['rows'])) {
                $_SESSION['old_rows'] = $_POST['rows'];
            }
            if (isset($_POST['perkerasan_rows'])) {
                $_SESSION['old_perkerasan_rows'] = $_POST['perkerasan_rows'];
            }
            redirect(base_url('ruas/edit/' . $id));
        }
    }

    /**
     * Proses hapus ruas
     */
    public function delete(int $id): void
    {
        $result = $this->service->delete($id);

        if ($result['success']) {
            flash('success', $result['message']);
        } else {
            flash('error', $result['message']);
        }
        redirect(base_url('ruas'));
    }

    /**
     * Tampilkan detail ruas jalan
     */
    public function show(int $id): void
    {
        $ruas = $this->service->findById($id);
        if (!$ruas) {
            flash('error', 'Ruas jalan tidak ditemukan.');
            redirect(base_url('ruas'));
            return;
        }

        $stripmapService   = new StripmapService();
        $perkerasanService = new PerkerasanService();
        $penangananService = new PenangananService();

        $stripmaps         = $stripmapService->getByRuasId($id);
        $summary           = $stripmapService->getSummary($id);
        $perkerasans       = $perkerasanService->getByRuasId($id);
        $summaryPerkerasan = $perkerasanService->getSummary($id);
        $penanganans       = $penangananService->getByRuasId($id);
        $penangananSummary = $penangananService->getSummary($id);
        $penangananYears   = $penangananService->getAvailableYears($id);

        $data = [
            'title'             => 'Detail Ruas Jalan - ' . $ruas['nama_ruas'],
            'ruas'              => $ruas,
            'stripmaps'         => $stripmaps,
            'summary'           => $summary,
            'perkerasans'       => $perkerasans,
            'summaryPerkerasan' => $summaryPerkerasan,
            'penanganans'       => $penanganans,
            'penangananSummary' => $penangananSummary,
            'penangananYears'   => $penangananYears,
        ];
        view('layouts.app', array_merge($data, ['content' => 'ruas.show']));
    }

    /**
     * Tampilkan form upload import file Excel / CSV
     */
    public function importForm(): void
    {
        $data = [
            'title' => 'Import Data Rekapitulasi (Excel / CSV)',
        ];
        view('layouts.app', array_merge($data, ['content' => 'ruas.import']));
    }

    /**
     * Proses file upload import Excel / CSV
     */
    public function importProcess(): void
    {
        if (!isset($_FILES['file_excel']) || $_FILES['file_excel']['error'] !== UPLOAD_ERR_OK) {
            flash('error', 'Silakan pilih file Excel (.xlsx) atau CSV (.csv) yang valid.');
            redirect(base_url('ruas/import'));
            return;
        }

        $fileTmp  = $_FILES['file_excel']['tmp_name'];
        $fileName = $_FILES['file_excel']['name'];

        $importer = new ExcelImporter();
        $result   = $importer->import($fileTmp, $fileName);

        if ($result['success']) {
            flash('success', $result['message']);
            redirect(base_url('ruas'));
        } else {
            flash('error', $result['message']);
            redirect(base_url('ruas/import'));
        }
    }
}
