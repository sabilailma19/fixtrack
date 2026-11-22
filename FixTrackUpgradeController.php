<?php

require_once __DIR__ . '/../models/FixTrackWorkflow.php';

class FixTrackUpgradeController extends Controller
{
    private function input($source, $key)
    {
        return isset($source[$key]) && is_string($source[$key])
            ? trim($source[$key])
            : '';
    }

    private function filterRows($rows, $dateColumn)
    {
        $filters = [
    'q' => mb_substr($this->input($_GET, 'q'), 0, 150),
    'status' => $this->input($_GET, 'status'),
    'confirmation' => $this->input($_GET, 'confirmation'),
    'from' => $this->input($_GET, 'from'),
    'to' => $this->input($_GET, 'to')
];

if (
    $filters['confirmation'] !== '' &&
    !array_key_exists(
        $filters['confirmation'],
        CompletionStatus::labels()
    )
) {
    http_response_code(400);
    exit('Filter konfirmasi tidak valid.');
}

        $rows = array_values(array_filter(
            $rows,
            static function ($row) use ($filters, $dateColumn) {
                $date = substr(
                    (string) ($row[$dateColumn] ?? ''),
                    0,
                    10
                );

                if ($filters['from'] !== '' && $date < $filters['from']) {
                    return false;
                }

                if (
                    $filters['to'] !== '' &&
                    ($date === '' || $date > $filters['to'])
                ) {
                    return false;
                }

                if (
                    $filters['status'] !== '' &&
                    ($row['status'] ?? '') !== $filters['status']
                ) {
                    return false;
                }
                if (
    $filters['confirmation'] !== '' &&
    CompletionStatus::key($row) !== $filters['confirmation']
) {
    return false;
}

                if ($filters['q'] !== '') {
                    $search = [];

                    foreach ([
                        'complaint_code',
                        'title',
                        'reporter_name',
                        'officer_name',
                        'category_name',
                        'building_name',
                        'room_name',
                        'facility_name'
                    ] as $column) {
                        $search[] = $row[$column] ?? '';
                    }

                    if (
                        mb_stripos(
                            implode(' ', $search),
                            $filters['q']
                        ) === false
                    ) {
                        return false;
                    }
                }

                return true;
            }
        ));

        return [$rows, $filters];
    }

    public function listing($mode)
    {
        $allowed = [
            'admin-complaints' => ['admin'],
            'admin-assignments' => ['admin'],
            'reporter' => ['pelapor'],
            'officer' => ['petugas'],
            'reports' => ['admin']
        ];

        if (!isset($allowed[$mode])) {
            http_response_code(404);
            exit('Halaman tidak ditemukan.');
        }

        AuthMiddleware::role($allowed[$mode]);

        $user = $_SESSION['user'];
        $assignmentPage = in_array(
            $mode,
            ['admin-assignments', 'officer'],
            true
        );

        $dateColumn = $assignmentPage ? 'assigned_at' : 'created_at';

        if ($mode === 'reporter') {
            $rows = (new Complaint())->getByReporter((int) $user['id']);
        } elseif ($mode === 'officer') {
            $rows = (new ComplaintAssignment())->getByOfficer(
                (int) $user['id']
            );
        } elseif ($mode === 'admin-assignments') {
            $rows = (new ComplaintAssignment())->getAll();
        } else {
            $rows = (new Complaint())->getAll();
        }

        [$filtered, $filters] = $this->filterRows($rows, $dateColumn);

        $total = count($filtered);
        $pages = max(1, (int) ceil($total / 10));
        $page = min(
            $pages,
            max(1, (int) $this->input($_GET, 'page'))
        );

        $printMode = $mode === 'reports'
            && $this->input($_GET, 'print') === '1';

        $statusCounts = [];

        foreach ($filtered as $row) {
            $status = $row['status'];

            $statusCounts[$status] =
                ($statusCounts[$status] ?? 0) + 1;
        }

        $titles = [
            'admin-complaints' => 'Pengaduan',
            'admin-assignments' => 'Penugasan Petugas',
            'reporter' => 'Riwayat Pengaduan',
            'officer' => 'Penugasan Saya',
            'reports' => 'Laporan Statistik'
        ];

        $this->view('shared/workflow-page', [
            'mode' => $mode,
            'title' => $titles[$mode],
            'user' => $user,
            'assignmentPage' => $assignmentPage,
            'dateColumn' => $dateColumn,
            'filters' => $filters,
            'total' => $total,
            'pages' => $pages,
            'page' => $page,
            'printMode' => $printMode,
            'statusCounts' => $statusCounts,
            'confirmationCounts' => CompletionStatus::counts($filtered),
            'rows' => $printMode
                ? $filtered
                : array_slice($filtered, ($page - 1) * 10, 10),
            'ready' => $mode === 'admin-assignments'
                ? (new FixTrackWorkflow())->readyComplaints()
                : [],
            'officers' => $mode === 'admin-assignments'
                ? (new ComplaintAssignment())->getActiveOfficers()
                : []
        ]);
    }

    public function detail()
    {
        AuthMiddleware::role(['admin', 'petugas', 'pelapor']);

        $id = (int) $this->input($_GET, 'id');

        $complaint = (new FixTrackWorkflow())->detail(
            $id,
            $_SESSION['user']
        );

        if (!$complaint) {
            http_response_code(404);
            exit('Pengaduan tidak ditemukan atau tidak dapat diakses.');
        }

        $this->view('shared/workflow-page', [
            'mode' => 'detail',
            'title' => 'Detail Pengaduan',
            'user' => $_SESSION['user'],
            'complaint' => $complaint,
            'printMode' => false
        ]);
    }

    private function savePhoto()
    {
        $file = $_FILES['completion_photo'] ?? null;

        if (
            !$file ||
            ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK
        ) {
            throw new DomainException(
                'Unggah foto hasil perbaikan. Periksa juga batas ukuran upload server.'
            );
        }

        if ($file['size'] <= 0 || $file['size'] > 3 * 1024 * 1024) {
            throw new DomainException('Foto maksimal 3 MB.');
        }

        $mime = (new finfo(FILEINFO_MIME_TYPE))->file(
            $file['tmp_name']
        );

        $types = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp'
        ];

        if (
            !isset($types[$mime]) ||
            !@getimagesize($file['tmp_name'])
        ) {
            throw new DomainException(
                'Gunakan gambar JPG, PNG, atau WEBP yang valid.'
            );
        }

        $directory = dirname(__DIR__, 2)
            . '/public/uploads/completions';

        if (
            !is_dir($directory) &&
            !mkdir($directory, 0755, true) &&
            !is_dir($directory)
        ) {
            throw new RuntimeException('Folder foto tidak dapat dibuat.');
        }

        $relative = 'uploads/completions/'
            . bin2hex(random_bytes(16))
            . '.'
            . $types[$mime];

        $absolute = dirname(__DIR__, 2)
            . '/public/'
            . $relative;

        if (!move_uploaded_file($file['tmp_name'], $absolute)) {
            throw new RuntimeException('Foto tidak dapat disimpan.');
        }

        return [
            'path' => $relative,
            'absolute' => $absolute
        ];
    }

    public function action($action)
    {
        $roles = [
            'verify' => ['admin'],
            'reject' => ['admin'],
            'assign' => ['admin'],
            'accept' => ['petugas'],
            'start' => ['petugas'],
            'complete' => ['petugas'],
            'feedback' => ['pelapor']
        ];

        if (!isset($roles[$action])) {
            http_response_code(404);
            exit('Aksi tidak ditemukan.');
        }

        AuthMiddleware::role($roles[$action]);
        $this->validateCsrf();

        $userId = (int) $_SESSION['user']['id'];
        $id = (int) $this->input($_POST, 'id');
        $upload = null;

        $back = BASE_URL . '/petugas/assignments';

        if (in_array($action, ['verify', 'reject'], true)) {
            $back = BASE_URL . '/admin/complaints';
        } elseif ($action === 'assign') {
            $back = BASE_URL . '/admin/assignments';
        } elseif ($action === 'feedback') {
            $back = BASE_URL . '/complaints/detail?id='
                . (int) $this->input($_POST, 'complaint_id');
        }

        try {
            $workflow = new FixTrackWorkflow();

            if ($action === 'verify' || $action === 'reject') {
                $workflow->review(
                    $id,
                    $action === 'verify' ? 'diverifikasi' : 'ditolak',
                    $this->input(
                        $_POST,
                        $action === 'verify'
                            ? 'admin_note'
                            : 'rejection_reason'
                    )
                );

                $message = 'Status pengaduan berhasil diperbarui.';
            } elseif ($action === 'assign') {
                $workflow->assign(
                    (int) $this->input($_POST, 'complaint_id'),
                    (int) $this->input($_POST, 'officer_id'),
                    $userId,
                    $this->input($_POST, 'notes')
                );

                $message = 'Penugasan berhasil disimpan.';
            } elseif ($action === 'feedback') {
                $decision = $this->input($_POST, 'decision');

                $workflow->feedback(
                    (int) $this->input($_POST, 'complaint_id'),
                    (int) $this->input($_POST, 'assignment_id'),
                    $userId,
                    $decision,
                    $this->input($_POST, 'feedback')
                );

                $message = $decision === 'confirmed'
                    ? 'Hasil perbaikan telah dikonfirmasi.'
                    : 'Permintaan perbaikan ulang masuk ke antrean admin.';
            } else {
                $targets = [
                    'accept' => 'diterima',
                    'start' => 'diproses',
                    'complete' => 'selesai'
                ];

                $notes = null;

                if ($action === 'complete') {
                    $notes = $this->input($_POST, 'completion_notes');

                    if (
                        mb_strlen($notes) < 5 ||
                        mb_strlen($notes) > 2000
                    ) {
                        throw new DomainException(
                            'Catatan penyelesaian harus 5–2000 karakter.'
                        );
                    }

                    $upload = $this->savePhoto();
                }

                $workflow->advance(
                    $id,
                    $userId,
                    $targets[$action],
                    $notes,
                    $upload['path'] ?? null
                );

                $message = 'Status pekerjaan berhasil diperbarui.';
            }

            $_SESSION['success'] = $message;
        } catch (Throwable $e) {
            if ($upload && is_file($upload['absolute'])) {
                unlink($upload['absolute']);
            }

            error_log($e->getMessage());

            $_SESSION['error'] = $e instanceof DomainException
                ? $e->getMessage()
                : 'Perubahan gagal disimpan. Silakan coba kembali.';
        }

        $this->redirect($back);
    }

    public function export()
    {
        AuthMiddleware::role(['admin']);

        [$rows] = $this->filterRows(
            (new Complaint())->getAll(),
            'created_at'
        );

        header('Content-Type: text/csv; charset=UTF-8');
        header(
            'Content-Disposition: attachment; filename="fixtrack_laporan.csv"'
        );
        header('Cache-Control: no-store');

        $out = fopen('php://output', 'w');

        fwrite($out, "\xEF\xBB\xBF");

        fputcsv($out, [
            'Kode',
            'Judul',
            'Pelapor',
            'Gedung',
            'Ruangan',
            'Status',
            'Tanggal Laporan',
            'Petugas',
            'Catatan Penyelesaian',
'Konfirmasi Pelapor'
        ]);

        foreach ($rows as $row) {
            $cells = [];

            foreach ([
                'complaint_code',
                'title',
                'reporter_name',
                'building_name',
                'room_name',
                'status',
                'created_at',
                'officer_name',
                'completion_notes'
            ] as $key) {
                $value = (string) ($row[$key] ?? '');

                // Hindari teks pengguna dibaca sebagai formula spreadsheet.
                if (
                    preg_match('/^[\s\x00-\x1f]*[=+@-]/u', $value) ||
                    preg_match('/^[\t\r\n]/', $value)
                ) {
                    $value = "'" . $value;
                }

                $cells[] = $value;
            }

            $cells[] = CompletionStatus::label($row) ?: 'Belum tersedia';
            fputcsv($out, $cells);
        }

        fclose($out);
        exit;
    }
}