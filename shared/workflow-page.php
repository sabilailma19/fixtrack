<?php

$e = static function ($value) {
    return htmlspecialchars(
        (string) ($value ?? ''),
        ENT_QUOTES,
        'UTF-8'
    );
};

$role = $user['role'];

$roleLabel = [
    'admin' => 'Administrator',
    'petugas' => 'Petugas',
    'pelapor' => 'Pelapor'
][$role];

$labels = [
    'menunggu_verifikasi' => 'Menunggu Verifikasi',
    'diverifikasi' => 'Diverifikasi',
    'ditugaskan' => 'Ditugaskan',
    'diterima' => 'Diterima',
    'diproses' => 'Sedang Diproses',
    'selesai' => 'Selesai dikerjakan',
    'ditolak' => 'Ditolak',
    'dibatalkan' => 'Dibatalkan'
];

$confirmationLabels = [
    'confirmed' => 'Dikonfirmasi selesai',
    'needs_review' => 'Diminta perbaikan ulang'
];

$currentPath = rtrim(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH),
    '/'
);

$back = BASE_URL . '/' . $role
    . ($role === 'petugas' ? '/assignments' : '/complaints');

$csrf = $e($_SESSION['csrf_token'] ?? '');

$photoUrl = static function ($path) {
    if (
        !$path ||
        !preg_match(
            '~^uploads/(complaints|completions)/[A-Za-z0-9_.-]+\.(jpg|jpeg|png|webp)$~i',
            $path
        )
    ) {
        return null;
    }

    return BASE_URL . '/' . $path;
};

if ($role === 'admin') {
    $menus = [
        ['dashboard', 'bi-grid', 'Dashboard'],
        ['users', 'bi-people', 'Data Pengguna'],
        ['categories', 'bi-tags', 'Kategori Kerusakan'],
        ['facilities', 'bi-building', 'Data Fasilitas'],
        ['complaints', 'bi-clipboard-check', 'Pengaduan'],
        ['assignments', 'bi-person-gear', 'Penugasan Petugas'],
        ['reports', 'bi-bar-chart', 'Laporan Statistik']
    ];
} elseif ($role === 'petugas') {
    $menus = [
        ['dashboard', 'bi-grid', 'Dashboard'],
        ['assignments', 'bi-clipboard-check', 'Penugasan Saya']
    ];
} else {
    $menus = [
        ['dashboard', 'bi-grid', 'Dashboard'],
        ['complaints', 'bi-clipboard-check', 'Riwayat Pengaduan'],
        ['complaints/create', 'bi-plus-circle', 'Buat Pengaduan']
    ];
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?= $e($title) ?> | FixTrack</title>

    <link rel="stylesheet"
          href="<?= BASE_URL ?>/assets/css/admin.css">

    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        .wf-card {
            margin-bottom: 20px;
        }

        .wf-text {
            margin: 14px 0;
            font-size: 13px;
            line-height: 1.8;
            white-space: pre-wrap;
            overflow-wrap: anywhere;
        }

        .wf-caption {
            margin: 12px 0;
            font-size: 12px;
            color: #64748b;
            line-height: 1.7;
        }

        .wf-form,
        .wf-filter {
            display: flex;
            flex-wrap: wrap;
            align-items: end;
            gap: 14px;
            margin-top: 18px;
        }

        .wf-form label,
        .wf-filter label {
            display: flex;
            flex-direction: column;
            gap: 8px;
            font-size: 12px;
            color: #475569;
            flex: 1 1 170px;
        }

        .wf-form .wide {
            flex-basis: 100%;
        }

        .wf-form input,
        .wf-form select,
        .wf-form textarea,
        .wf-filter input,
        .wf-filter select {
            width: 100%;
            max-width: 100%;
            border: 1px solid #cbd5e1;
            padding: 11px;
            border-radius: 8px;
            background: white;
            font: inherit;
        }

        textarea {
            resize: vertical;
        }

        .wf-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 11px 15px;
            border: 0;
            border-radius: 8px;
            background: #2563eb;
            color: white;
            font: 600 12px Inter, Arial, sans-serif;
            text-decoration: none;
            cursor: pointer;
        }

        .wf-button.secondary {
            background: #eff6ff;
            color: #1d4ed8;
        }

        .wf-button.danger {
            background: #b91c1c;
        }

        .wf-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
            margin-top: 15px;
        }

        .wf-table-wrap {
            overflow-x: auto;
            margin-top: 20px;
        }

        .wf-table {
            width: 100%;
            min-width: 700px;
            border-collapse: collapse;
        }

        .wf-table th,
        .wf-table td {
            padding: 13px 12px;
            border-bottom: 1px solid #e8edf4;
            text-align: left;
            font-size: 12px;
            line-height: 1.7;
        }

        .wf-table th {
            background: #f8fafc;
            color: #64748b;
        }

        .wf-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 18px;
            background: #eff6ff;
            color: #1d4ed8;
            font-size: 11px;
            white-space: nowrap;
        }

        .wf-badge.selesai {
            background: #dcfce7;
            color: #166534;
        }

        .wf-badge.ditolak,
        .wf-badge.dibatalkan {
            background: #fee2e2;
            color: #991b1b;
        }

        .wf-badge.diproses {
            background: #ede9fe;
            color: #6d28d9;
        }

        .wf-detail-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px;
            margin: 22px 0;
        }

        .wf-detail-grid dt {
            color: #64748b;
            font-size: 11px;
            margin-bottom: 7px;
        }

        .wf-detail-grid dd {
            margin: 0;
            font-size: 13px;
            overflow-wrap: anywhere;
        }

        .wf-photo {
            display: block;
            max-width: 100%;
            max-height: 350px;
            object-fit: contain;
            border-radius: 10px;
            margin: 16px 0;
        }

        .wf-notice {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 9px;
            background: #eff6ff;
            color: #1d4ed8;
            font-size: 13px;
        }

        .wf-notice.success {
            background: #dcfce7;
            color: #166534;
        }

        .wf-notice.error {
            background: #fee2e2;
            color: #991b1b;
        }

        .wf-pagination {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: center;
            gap: 15px;
            padding: 20px 0;
            font-size: 12px;
        }

        .wf-pagination a {
            color: #2563eb;
            padding: 10px;
            border: 1px solid #bfdbfe;
            border-radius: 8px;
        }

        .sidebar {
            overflow-y: auto;
        }

        :focus-visible {
            outline: 3px solid #60a5fa;
            outline-offset: 3px;
        }

        @media (max-width: 768px) {
            .wf-detail-grid {
                grid-template-columns: 1fr;
            }

            .sidebar .logout-link {
                display: flex;
                justify-content: center;
            }

            .logout-link span {
                display: none;
            }

            .topbar h1 {
                font-size: 16px;
            }

            .card-heading {
                flex-wrap: wrap;
            }
        }

        @media print {
            .sidebar,
            .topbar,
            .no-print,
            .dashboard-footer {
                display: none !important;
            }

            .main-content {
                margin: 0 !important;
                width: 100% !important;
            }

            .dashboard-content {
                padding: 0 !important;
            }

            .admin-layout {
                display: block;
            }

            .wf-table-wrap {
                overflow: visible;
            }

            .wf-table {
                min-width: 0;
                width: 100%;
            }

            .wf-table th,
            .wf-table td {
                font-size: 9pt;
                padding: 6px;
            }

            thead {
                display: table-header-group;
            }

            tr {
                break-inside: avoid;
            }

            body {
                background: white;
                color: black;
            }
        }
    </style>
</head>

<body>

<div class="admin-layout">

    <aside class="sidebar">

        <div class="sidebar-brand">
            <div class="brand-logo">F</div>

            <div>
                <h2>FixTrack</h2>
                <span>Facility Management</span>
            </div>
        </div>

        <div class="sidebar-label">
            MENU <?= $e(strtoupper($roleLabel)) ?>
        </div>

        <nav class="sidebar-menu">

            <?php foreach ($menus as [$path, $icon, $label]): ?>
                <?php
                $url = BASE_URL . '/' . $role . '/' . $path;

                $active = $currentPath === $url
                    || ($mode === 'detail' && $url === $back);
                ?>

                <a href="<?= $e($url) ?>"
                   class="menu-item <?= $active ? 'active' : '' ?>"
                   aria-label="<?= $e($label) ?>"
                   <?= $active ? 'aria-current="page"' : '' ?>>

                    <i class="bi <?= $e($icon) ?>"></i>
                    <span><?= $e($label) ?></span>
                </a>

            <?php endforeach; ?>

        </nav>

        <div class="sidebar-bottom">

            <div class="admin-profile">
                <div class="avatar">
                    <?= $e(mb_substr($user['name'], 0, 1)) ?>
                </div>

                <div class="profile-info">
                    <strong><?= $e($user['name']) ?></strong>
                    <span><?= $e($roleLabel) ?></span>
                </div>
            </div>

            <a class="logout-link"
               href="<?= BASE_URL ?>/logout"
               aria-label="Logout">

                <i class="bi bi-box-arrow-right"></i>
                <span>Logout</span>
            </a>

        </div>

    </aside>

    <main class="main-content">

        <header class="topbar">
            <div>
                <div class="breadcrumb">
                    Pages / <?= $e($title) ?>
                </div>

                <h1><?= $e($title) ?></h1>
            </div>

            <div class="admin-welcome">
                <span>Selamat datang,</span>
                <strong><?= $e($user['name']) ?></strong>
            </div>
        </header>

        <section class="dashboard-content">

            <?php foreach (['success', 'error'] as $kind): ?>
                <?php if (!empty($_SESSION[$kind])): ?>

                    <div class="wf-notice <?= $kind ?>" role="status">
                        <?= $e($_SESSION[$kind]) ?>
                    </div>

                    <?php unset($_SESSION[$kind]); ?>

                <?php endif; ?>
            <?php endforeach; ?>

            <?php if ($mode !== 'detail'): ?>

                <!-- FILTER -->
                <div class="content-card wf-card no-print">

                    <h3>Filter <?= $e($title) ?></h3>

                    <form class="wf-filter"
                          method="GET"
                          action="<?= $e($currentPath) ?>">

                        <label>
                            Cari
                            <input name="q"
                                   maxlength="150"
                                   value="<?= $e($filters['q']) ?>"
                                   placeholder="Kode, judul, nama, lokasi">
                        </label>

                        <label>
                            Status
                            <select name="status">
                                <option value="">Semua status</option>

                                <?php
                                $options = $assignmentPage
                                    ? ['ditugaskan', 'diterima', 'diproses', 'selesai', 'dibatalkan']
                                    : ['menunggu_verifikasi', 'diverifikasi', 'ditugaskan', 'diproses', 'selesai', 'ditolak'];
                                ?>

                                <?php foreach ($options as $status): ?>
                                    <option
                                        value="<?= $status ?>"
                                        <?= $filters['status'] === $status ? 'selected' : '' ?>>
                                        <?= $e($labels[$status]) ?>
                                    </option>
                                <?php endforeach; ?>

                            </select>
                        </label>

                        <label>
                            Dari tanggal
                            <input type="date"
                                   name="from"
                                   value="<?= $e($filters['from']) ?>">
                        </label>

                        <label>
                            Sampai tanggal
                            <input type="date"
                                   name="to"
                                   value="<?= $e($filters['to']) ?>">
                        </label>

                        <label>
    Konfirmasi pelapor

    <select name="confirmation">
        <option value="">Semua konfirmasi</option>

        <?php foreach (CompletionStatus::labels() as $key => $label): ?>
            <option
                value="<?= $e($key) ?>"
                <?= ($filters['confirmation'] ?? '') === $key
                    ? 'selected'
                    : '' ?>
            >
                <?= $e($label) ?>
            </option>
        <?php endforeach; ?>
    </select>
</label>

                        <button class="wf-button" type="submit">
                            Terapkan
                        </button>

                        <a class="wf-button secondary"
                           href="<?= $e($currentPath) ?>">
                            Reset
                        </a>

                    </form>

                    <p class="wf-caption">
                        Tanggal berdasarkan
                        <?= $assignmentPage ? 'tanggal penugasan' : 'tanggal laporan dibuat' ?>.
                        <?= (int) $total ?> hasil ditemukan.
                    </p>

                </div>

                <!-- LAPORAN -->
                <?php if ($mode === 'reports'): ?>

                    <div class="content-card wf-card">

                        <h3>Rekap Pengaduan</h3>

                        <div class="wf-notice">
    <strong>Ringkasan Konfirmasi Pelapor</strong>

    <p class="wf-caption">
        Menunggu konfirmasi:
        <strong>
            <?= (int) ($confirmationCounts['pending'] ?? 0) ?>
        </strong>
        pengaduan.
    </p>

    <p class="wf-caption">
        Dikonfirmasi selesai:
        <strong>
            <?= (int) ($confirmationCounts['confirmed'] ?? 0) ?>
        </strong>
        pengaduan.
    </p>

    <p class="wf-caption">
        Diminta perbaikan ulang:
        <strong>
            <?= (int) ($confirmationCounts['needs_review'] ?? 0) ?>
        </strong>
        pengaduan.
    </p>

    <p class="wf-caption">
        Ringkasan mengikuti filter dan penugasan terbaru.
        Permintaan perbaikan ulang yang sudah ditugaskan kembali
        mengikuti status pekerjaan barunya.
    </p>
</div>

                        <p class="wf-caption">
    Periode:
    <?= $e($filters['from'] ?: 'Awal data') ?>
    sampai
    <?= $e($filters['to'] ?: 'Akhir data') ?>.

    Status:
    <?= $e($labels[$filters['status']] ?? 'Semua') ?>.

    Pencarian:
    <?= $e($filters['q'] ?: 'Semua') ?>.

    Konfirmasi:
    <?= $e(
        CompletionStatus::labels()[$filters['confirmation'] ?? '']
        ?? 'Semua'
    ) ?>.
</p>

                        <div class="wf-actions no-print">

                            <a class="wf-button"
                               href="<?= BASE_URL ?>/admin/reports/export?<?= $e(http_build_query($filters)) ?>">
                                Ekspor CSV
                            </a>

                            <?php if (!$printMode): ?>

                                <a class="wf-button secondary"
                                   href="<?= BASE_URL ?>/admin/reports?<?= $e(http_build_query(array_merge($filters, ['print' => '1']))) ?>">
                                    Tampilan cetak semua hasil
                                </a>

                            <?php else: ?>

                                <button class="wf-button secondary"
                                        type="button"
                                        onclick="window.print()">
                                    Cetak / Simpan PDF
                                </button>

                            <?php endif; ?>

                        </div>

                        <div class="wf-table-wrap">
                            <table class="wf-table">
                                <thead>
                                    <tr>
                                        <th>Status</th>
                                        <th>Jumlah</th>
                                        <th>Persentase</th>
                                    </tr>
                                </thead>

                                <tbody>

                                <?php foreach ([
                                    'menunggu_verifikasi',
                                    'diverifikasi',
                                    'ditugaskan',
                                    'diproses',
                                    'selesai',
                                    'ditolak'
                                ] as $status): ?>

                                    <?php $count = $statusCounts[$status] ?? 0; ?>

                                    <tr>
                                        <td><?= $e($labels[$status]) ?></td>
                                        <td><?= (int) $count ?></td>
                                        <td>
                                            <?= number_format(
                                                $total > 0 ? $count / $total * 100 : 0,
                                                1,
                                                ',',
                                                '.'
                                            ) ?>%
                                        </td>
                                    </tr>

                                <?php endforeach; ?>

                                </tbody>
                            </table>
                        </div>

                    </div>

                <?php endif; ?>

                <!-- ANTREAN PENUGASAN ADMIN -->
                <?php if ($mode === 'admin-assignments'): ?>

                    <div class="content-card wf-card">

                        <h3>Pengaduan Siap Ditugaskan</h3>

                        <p class="wf-caption">
                            Termasuk permintaan perbaikan ulang.
                            Klik Detail untuk membaca alasan pelapor.
                            Filter di atas berlaku untuk riwayat penugasan,
                            bukan antrean ini.
                        </p>

                        <?php if (!$ready): ?>
                            <p class="wf-text">Belum ada pengaduan siap ditugaskan.</p>
                        <?php endif; ?>

                        <?php foreach ($ready as $available): ?>

                            <div class="content-card wf-card">

                                <h4>
                                    <?= $e($available['complaint_code']) ?>
                                    — <?= $e($available['title']) ?>
                                </h4>

                                <div class="wf-actions">
                                    <a class="wf-button secondary"
                                       href="<?= BASE_URL ?>/complaints/detail?id=<?= (int) $available['id'] ?>">
                                        Detail Pengaduan
                                    </a>
                                </div>

                                <form class="wf-form"
                                      method="POST"
                                      action="<?= BASE_URL ?>/admin/assignments/store">

                                    <input type="hidden"
                                           name="csrf_token"
                                           value="<?= $csrf ?>">

                                    <input type="hidden"
                                           name="complaint_id"
                                           value="<?= (int) $available['id'] ?>">

                                    <label>
                                        Petugas
                                        <select name="officer_id" required>
                                            <option value="">Pilih petugas</option>

                                            <?php foreach ($officers as $officer): ?>
                                                <option value="<?= (int) $officer['id'] ?>">
                                                    <?= $e($officer['name']) ?>
                                                </option>
                                            <?php endforeach; ?>

                                        </select>
                                    </label>

                                    <label>
                                        Catatan penugasan
                                        <input name="notes" maxlength="2000">
                                    </label>

                                    <button type="submit"
                                            class="wf-button"
                                            <?= !$officers ? 'disabled' : '' ?>>
                                        Tugaskan
                                    </button>

                                </form>

                            </div>

                        <?php endforeach; ?>

                    </div>

                <?php endif; ?>

                <!-- DAFTAR -->
                <div class="content-card wf-card">

                    <div class="card-heading">
                        <div>
                            <h3><?= $e($title) ?></h3>
                            <p><?= (int) $total ?> data sesuai filter</p>
                        </div>

                        <?php if ($role === 'pelapor'): ?>
                            <a class="wf-button"
                               href="<?= BASE_URL ?>/pelapor/complaints/create">
                                Buat Pengaduan
                            </a>
                        <?php endif; ?>
                    </div>

                    <?php if (!$rows): ?>

                        <div class="empty-table">
                            <i class="bi bi-inbox"></i>
                            <strong>Tidak Ada Data</strong>
                            <span>Belum ada data yang sesuai dengan filter.</span>
                        </div>

                    <?php else: ?>

                        <div class="wf-table-wrap">
                            <table class="wf-table">

                                <thead>
                                    <tr>
                                        <th>Kode</th>
                                        <th>Pengaduan</th>
                                        <th>Lokasi</th>
                                        <th>Tanggal</th>
                                        <th>Status</th>
                                        <th class="no-print">Aksi</th>
                                    </tr>
                                </thead>

                                <tbody>

                                <?php foreach ($rows as $row): ?>
                                    <?php
                                    $complaintId = $assignmentPage
                                        ? (int) $row['complaint_id']
                                        : (int) $row['id'];
                                    ?>

                                    <tr>
                                        <td><?= $e($row['complaint_code']) ?></td>

                                        <td>
                                            <?= $e($row['title']) ?>

                                            <?php if (!empty($row['officer_name'])): ?>
                                                <div class="wf-caption">
                                                    Petugas: <?= $e($row['officer_name']) ?>
                                                </div>
                                            <?php endif; ?>
                                        </td>

                                        <td>
                                            <?= $e(
                                                ($row['building_name'] ?? '-')
                                                . ' - '
                                                . ($row['room_name'] ?? '-')
                                            ) ?>
                                        </td>

                                        <td>
                                            <?= $e($row[$dateColumn] ?? '-') ?>
                                        </td>

                                        <td>
                                            <span class="wf-badge <?= $e($row['status']) ?>">
                                                <?= $e($labels[$row['status']] ?? $row['status']) ?>
                                            </span>
                                            <?php if ($confirmationText = CompletionStatus::label($row)): ?>
    <div class="wf-caption">
        <strong><?= $e($confirmationText) ?></strong>
    </div>
<?php endif; ?>
                                        </td>

                                        <td class="no-print">
                                            <a class="wf-button secondary"
                                               href="<?= BASE_URL ?>/complaints/detail?id=<?= $complaintId ?>">
                                                Detail / Tindak Lanjut
                                            </a>
                                        </td>
                                    </tr>

                                <?php endforeach; ?>

                                </tbody>

                            </table>
                        </div>

                    <?php endif; ?>

                </div>

                <?php if (!$printMode): ?>

                    <nav class="wf-pagination no-print"
                         aria-label="Halaman daftar">

                        <?php if ($page > 1): ?>
                            <a href="?<?= $e(http_build_query(array_merge($filters, ['page' => $page - 1]))) ?>">
                                Sebelumnya
                            </a>
                        <?php endif; ?>

                        <span>
                            Halaman <?= (int) $page ?>
                            dari <?= (int) $pages ?>
                        </span>

                        <?php if ($page < $pages): ?>
                            <a href="?<?= $e(http_build_query(array_merge($filters, ['page' => $page + 1]))) ?>">
                                Berikutnya
                            </a>
                        <?php endif; ?>

                    </nav>

                <?php endif; ?>

            <?php else: ?>

                <!-- DETAIL PENGADUAN -->
                <?php $latest = $complaint['history'][0] ?? null; ?>

                <div class="wf-actions no-print">
                    <a class="wf-button secondary"
                       href="<?= $e($back) ?>">
                        Kembali ke Daftar
                    </a>
                </div>

                <article class="content-card recent-card wf-card">

                    <div class="card-heading">
                        <div>
                            <h3>
                                <?= $e($complaint['complaint_code']) ?>
                                — <?= $e($complaint['title']) ?>
                            </h3>

                            <p>
                                <?= $e($labels[$complaint['status']] ?? $complaint['status']) ?>
                            </p>
                        </div>
                    </div>

                    <dl class="wf-detail-grid">

                        <?php foreach ([
                            'reporter_name' => 'Pelapor',
                            'category_name' => 'Kategori',
                            'building_name' => 'Gedung',
                            'room_name' => 'Ruangan',
                            'facility_name' => 'Fasilitas',
                            'created_at' => 'Tanggal Laporan'
                        ] as $key => $label): ?>

                            <div>
                                <dt><?= $e($label) ?></dt>
                                <dd><?= $e($complaint[$key] ?? '-') ?></dd>
                            </div>

                        <?php endforeach; ?>

                    </dl>

                    <h4>Deskripsi Kerusakan</h4>
                    <p class="wf-text"><?= $e($complaint['description']) ?></p>

                    <?php if ($url = $photoUrl($complaint['photo'])): ?>

                        <a href="<?= $e($url) ?>"
                           target="_blank"
                           rel="noopener">

                            <img class="wf-photo"
                                 src="<?= $e($url) ?>"
                                 alt="Foto kerusakan">
                        </a>

                    <?php else: ?>

                        <p class="wf-caption">Tidak ada foto kerusakan.</p>

                    <?php endif; ?>

                    <?php if (!empty($complaint['admin_note'])): ?>
                        <h4>Catatan Admin</h4>
                        <p class="wf-text"><?= $e($complaint['admin_note']) ?></p>
                    <?php endif; ?>

                    <?php if (!empty($complaint['rejection_reason'])): ?>
                        <h4>Alasan Penolakan</h4>
                        <p class="wf-text"><?= $e($complaint['rejection_reason']) ?></p>
                    <?php endif; ?>

                </article>

                <!-- VERIFIKASI ADMIN -->
                <?php if (
                    $role === 'admin' &&
                    $complaint['status'] === 'menunggu_verifikasi'
                ): ?>

                    <section class="content-card wf-card">

                        <h3>Verifikasi Pengaduan</h3>

                        <form class="wf-form"
                              method="POST"
                              action="<?= BASE_URL ?>/admin/complaints/verify">

                            <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                            <input type="hidden" name="id" value="<?= (int) $complaint['id'] ?>">

                            <label>
                                Catatan admin
                                <textarea name="admin_note"
                                          rows="3"
                                          maxlength="2000"></textarea>
                            </label>

                            <button class="wf-button" type="submit">
                                Verifikasi
                            </button>

                        </form>

                        <form class="wf-form"
                              method="POST"
                              action="<?= BASE_URL ?>/admin/complaints/reject">

                            <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                            <input type="hidden" name="id" value="<?= (int) $complaint['id'] ?>">

                            <label>
                                Alasan penolakan
                                <textarea name="rejection_reason"
                                          rows="3"
                                          maxlength="2000"
                                          required></textarea>
                            </label>

                            <button class="wf-button danger" type="submit">
                                Tolak Pengaduan
                            </button>

                        </form>

                    </section>

                <?php endif; ?>

                <?php if (
                    $role === 'admin' &&
                    $complaint['status'] === 'diverifikasi'
                ): ?>

                    <div class="content-card wf-card">
                        <h3>Pengaduan Siap Ditugaskan</h3>

                        <p class="wf-text">Baca riwayat dan catatan pelapor sebelum memilih petugas.</p>

                        <a class="wf-button"
                           href="<?= BASE_URL ?>/admin/assignments">
                            Buka Penugasan Petugas
                        </a>
                    </div>

                <?php endif; ?>

                <!-- RIWAYAT -->
                <div class="section-heading">
                    <h3>Riwayat Penanganan</h3>
                    <p>Penugasan terbaru ditampilkan paling atas.</p>
                </div>

                <?php if (!$complaint['history']): ?>
                    <div class="content-card wf-card">
                        <p class="wf-text">Belum ada penugasan petugas.</p>
                    </div>
                <?php endif; ?>

                <?php foreach ($complaint['history'] as $attempt): ?>

                    <article class="content-card wf-card">

                        <div class="card-heading">
                            <h3>
                                Penugasan #<?= (int) $attempt['id'] ?>
                                — <?= $e($attempt['officer_name']) ?>
                            </h3>

                            <span class="wf-badge <?= $e($attempt['status']) ?>">
                                <?= $e($labels[$attempt['status']] ?? $attempt['status']) ?>
                            </span>
                        </div>

                        <dl class="wf-detail-grid">

                            <?php foreach ([
                                'assigned_at' => 'Ditugaskan',
                                'accepted_at' => 'Diterima',
                                'started_at' => 'Mulai Dikerjakan',
                                'completed_at' => 'Selesai'
                            ] as $key => $label): ?>

                                <div>
                                    <dt><?= $e($label) ?></dt>
                                    <dd>
                                        <?= $e($attempt[$key] ?: 'Belum tercatat') ?>
                                    </dd>
                                </div>

                            <?php endforeach; ?>

                        </dl>

                        <?php if ($attempt['notes']): ?>
                            <h4>Catatan Penugasan</h4>
                            <p class="wf-text"><?= $e($attempt['notes']) ?></p>
                        <?php endif; ?>

                        <?php if ($attempt['completion_notes']): ?>
                            <h4>Hasil Pekerjaan</h4>
                            <p class="wf-text"><?= $e($attempt['completion_notes']) ?></p>
                        <?php endif; ?>

                        <?php if ($url = $photoUrl($attempt['completion_photo'])): ?>

                            <h4>Foto Hasil Perbaikan</h4>

                            <a href="<?= $e($url) ?>"
                               target="_blank"
                               rel="noopener">

                                <img class="wf-photo"
                                     src="<?= $e($url) ?>"
                                     alt="Foto hasil perbaikan">
                            </a>

                        <?php elseif ($attempt['status'] === 'selesai'): ?>

                            <p class="wf-caption">
                                Pekerjaan lama ini belum memiliki foto hasil perbaikan.
                            </p>

                        <?php endif; ?>

                        <?php if ($attempt['reporter_confirmation']): ?>

                            <div class="wf-notice">
                                <strong>
                                    <?= $e($confirmationLabels[$attempt['reporter_confirmation']] ?? '-') ?>
                                </strong>

                                <p class="wf-text"><?= $e($attempt['reporter_feedback'] ?: 'Tanpa catatan tambahan.') ?></p>

                                <small><?= $e($attempt['feedback_at']) ?></small>
                            </div>

                        <?php elseif ($attempt['status'] === 'selesai'): ?>

                            <p class="wf-caption">
                                Belum dikonfirmasi pelapor.
                            </p>

                        <?php endif; ?>

                        <!-- AKSI HANYA UNTUK PETUGAS PENUGASAN TERBARU -->
                        <?php if (
                            $role === 'petugas' &&
                            (int) $attempt['officer_id'] === (int) $user['id'] &&
                            $latest &&
                            (int) $attempt['id'] === (int) $latest['id']
                        ): ?>

                            <?php if (in_array(
                                $attempt['status'],
                                ['ditugaskan', 'diterima'],
                                true
                            )): ?>

                                <?php
                                $action = $attempt['status'] === 'ditugaskan'
                                    ? 'accept'
                                    : 'start';
                                ?>

                                <form class="wf-actions"
                                      method="POST"
                                      action="<?= BASE_URL ?>/petugas/assignments/<?= $action ?>">

                                    <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                                    <input type="hidden" name="id" value="<?= (int) $attempt['id'] ?>">

                                    <button class="wf-button" type="submit">
                                        <?= $action === 'accept' ? 'Terima Tugas' : 'Mulai Kerjakan' ?>
                                    </button>

                                </form>

                            <?php elseif ($attempt['status'] === 'diproses'): ?>

                                <form class="wf-form"
                                      method="POST"
                                      enctype="multipart/form-data"
                                      action="<?= BASE_URL ?>/petugas/assignments/complete">

                                    <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                                    <input type="hidden" name="id" value="<?= (int) $attempt['id'] ?>">

                                    <label class="wide">
                                        Catatan penyelesaian
                                        <textarea name="completion_notes"
                                                  rows="4"
                                                  minlength="5"
                                                  maxlength="2000"
                                                  required></textarea>
                                    </label>

                                    <label class="wide">
                                        Foto hasil perbaikan — maksimal 3 MB
                                        <input type="file"
                                               name="completion_photo"
                                               accept="image/jpeg,image/png,image/webp"
                                               required>
                                    </label>

                                    <button class="wf-button" type="submit">
                                        Selesaikan Pekerjaan
                                    </button>

                                </form>

                            <?php endif; ?>

                        <?php endif; ?>

                    </article>

                <?php endforeach; ?>

                <!-- KONFIRMASI PELAPOR -->
                <?php if (
                    $role === 'pelapor' &&
                    $complaint['status'] === 'selesai' &&
                    $latest &&
                    $latest['status'] === 'selesai' &&
                    $latest['reporter_confirmation'] === null
                ): ?>

                    <section class="content-card wf-card">

                        <h3>Konfirmasi Hasil Perbaikan</h3>

                        <p class="wf-text">Jika masalah belum selesai, laporan akan kembali ke antrean penugasan admin. Riwayat pekerjaan sebelumnya tetap tersimpan.</p>

                        <form class="wf-form"
                              method="POST"
                              action="<?= BASE_URL ?>/pelapor/complaints/feedback">

                            <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                            <input type="hidden" name="complaint_id" value="<?= (int) $complaint['id'] ?>">
                            <input type="hidden" name="assignment_id" value="<?= (int) $latest['id'] ?>">

                            <label class="wide">
                                Hasil pemeriksaan

                                <select name="decision"
                                        id="feedbackDecision"
                                        required>

                                    <option value="">Pilih hasil</option>
                                    <option value="confirmed">Sudah diperbaiki</option>
                                    <option value="needs_review">Masalah belum selesai</option>
                                </select>
                            </label>

                            <label class="wide">
                                Catatan
                                <textarea name="feedback"
                                          id="feedbackNotes"
                                          rows="4"
                                          maxlength="2000"
                                          placeholder="Jika belum selesai, jelaskan masalahnya minimal 5 karakter."></textarea>
                            </label>

                            <button class="wf-button" type="submit">
                                Kirim Konfirmasi
                            </button>

                        </form>

                    </section>

                    <script>
                        document.getElementById('feedbackDecision')
                            .addEventListener('change', function () {
                                const notes = document.getElementById('feedbackNotes');

                                notes.required = this.value === 'needs_review';
                                notes.minLength = notes.required ? 5 : 0;
                            });
                    </script>

                <?php endif; ?>

            <?php endif; ?>

            <footer class="dashboard-footer">
                &copy; <?= date('Y') ?> FixTrack.
            </footer>

        </section>

    </main>

</div>

</body>
</html>