<?php

$escape = static function ($value) {
    return htmlspecialchars(
        (string) ($value ?? ''),
        ENT_QUOTES,
        'UTF-8'
    );
};

$userName = $user['name'] ?? 'Administrator';
$initial = mb_substr($userName, 0, 1);

$isReport = $isReport ?? false;
$statistics = $statistics ?? [];
$complaints = $complaints ?? [];

$pageTitle = $isReport
    ? 'Laporan Statistik'
    : 'Dashboard Admin';

$total = (int) ($statistics['total'] ?? 0);

$statusRows = [
    [
        'key' => 'waiting',
        'label' => 'Menunggu Verifikasi',
        'color' => '#ea580c'
    ],
    [
        'key' => 'verified',
        'label' => 'Diverifikasi',
        'color' => '#2563eb'
    ],
    [
        'key' => 'assigned',
        'label' => 'Ditugaskan',
        'color' => '#7c3aed'
    ],
    [
        'key' => 'processing',
        'label' => 'Sedang Diproses',
        'color' => '#0891b2'
    ],
    [
        'key' => 'completed',
        'label' => 'Selesai',
        'color' => '#16a34a'
    ],
    [
        'key' => 'rejected',
        'label' => 'Ditolak',
        'color' => '#dc2626'
    ]
];

$statusLabels = [
    'menunggu_verifikasi' => 'Menunggu Verifikasi',
    'diverifikasi' => 'Diverifikasi',
    'ditugaskan' => 'Ditugaskan',
    'diproses' => 'Sedang Diproses',
    'selesai' => 'Selesai',
    'ditolak' => 'Ditolak'
];

$cards = [
    [
        'label' => 'Total Pengaduan',
        'value' => $total,
        'hint' => 'Seluruh laporan yang tercatat',
        'color' => 'blue',
        'icon' => 'bi-clipboard-data'
    ],
    [
        'label' => 'Menunggu Verifikasi',
        'value' => (int) ($statistics['waiting'] ?? 0),
        'hint' => 'Perlu diperiksa oleh admin',
        'color' => 'orange',
        'icon' => 'bi-clock-history'
    ],
    [
        'label' => 'Sedang Diproses',
        'value' => (int) ($statistics['processing'] ?? 0),
        'hint' => 'Sedang ditangani petugas',
        'color' => 'purple',
        'icon' => 'bi-tools'
    ],
    [
        'label' => 'Dikonfirmasi Selesai',
'value' => (int) ($statistics['confirmation_confirmed'] ?? 0),
'hint' => 'Hasil perbaikan disetujui pelapor',
        'color' => 'green',
        'icon' => 'bi-check-circle'
    ]
];

$cards[] = [
    'label' => 'Menunggu Konfirmasi',
    'value' => (int) ($statistics['confirmation_pending'] ?? 0),
    'hint' => 'Sudah dikerjakan, menunggu pemeriksaan pelapor',
    'color' => 'orange',
    'icon' => 'bi-hourglass-split'
];

$cards[] = [
    'label' => 'Perbaikan Ulang',
    'value' => (int) ($statistics['confirmation_needs_review'] ?? 0),
    'hint' => 'Permintaan pelapor yang belum ditugaskan kembali',
    'color' => 'purple',
    'icon' => 'bi-arrow-repeat'
];

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1">

    <title><?= $escape($pageTitle) ?> | FixTrack</title>

    <link rel="stylesheet"
          href="<?= BASE_URL ?>/assets/css/admin.css">

    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        .admin-status-list {
            display: grid;
            gap: 18px;
            margin-top: 24px;
        }

        .admin-status-row > div {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 8px;
            font-size: 12px;
        }

        .admin-status-row progress {
            display: block;
            width: 100%;
            height: 8px;
            border: 0;
            border-radius: 8px;
            overflow: hidden;
            accent-color: var(--bar-color);
        }

        .admin-status-row progress::-webkit-progress-bar {
            background: #f1f5f9;
        }

        .admin-status-row progress::-webkit-progress-value {
            background: var(--bar-color);
        }

        .admin-status-row progress::-moz-progress-bar {
            background: var(--bar-color);
        }

        .admin-table-wrap {
            overflow-x: auto;
            margin-top: 20px;
        }

        .admin-table {
            width: 100%;
            min-width: 720px;
            border-collapse: collapse;
        }

        .admin-table th,
        .admin-table td {
            padding: 14px 12px;
            border-bottom: 1px solid #e8edf4;
            text-align: left;
            font-size: 12px;
            line-height: 1.6;
        }

        .admin-table th {
            background: #f8fafc;
            color: #64748b;
        }

        .admin-table td {
            overflow-wrap: anywhere;
        }

        .admin-status {
            display: inline-block;
            padding: 6px 10px;
            border-radius: 20px;
            background: #eff6ff;
            color: #1d4ed8;
            font-size: 11px;
            white-space: nowrap;
        }

        .admin-status.menunggu_verifikasi {
            background: #fff7ed;
            color: #9a3412;
        }

        .admin-status.ditugaskan,
        .admin-status.diproses {
            background: #f5f3ff;
            color: #6d28d9;
        }

        .admin-status.selesai {
            background: #dcfce7;
            color: #166534;
        }

        .admin-status.ditolak {
            background: #fee2e2;
            color: #991b1b;
        }

        .admin-shortcuts {
            display: grid;
            gap: 12px;
            margin-top: 22px;
        }

        .admin-shortcuts a {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            padding: 15px;
            border: 1px solid #e8edf4;
            border-radius: 9px;
            font-size: 12px;
        }

        .admin-shortcuts a:hover {
            background: #eff6ff;
            border-color: #bfdbfe;
        }

        .admin-shortcuts strong {
            color: #2563eb;
        }

        .admin-note {
            margin-top: 18px;
            font-size: 12px;
            line-height: 1.8;
            color: #64748b;
        }

        .sidebar {
            overflow-y: auto;
        }

        .avatar {
            flex-shrink: 0;
        }

        .profile-info strong {
            overflow-wrap: anywhere;
        }

        @media (max-width: 768px) {
            .sidebar .logout-link {
                display: flex;
                justify-content: center;
            }

            .logout-link span {
                display: none;
            }

            .card-heading {
                flex-wrap: wrap;
            }
        }
    </style>
</head>

<body>

<div class="admin-layout">

    <!-- SIDEBAR -->
    <aside class="sidebar">

        <div class="sidebar-brand">
            <div class="brand-logo">F</div>

            <div>
                <h2>FixTrack</h2>
                <span>Facility Management</span>
            </div>
        </div>

        <div class="sidebar-label">MENU ADMIN</div>

        <?php require __DIR__ . '/../shared/admin-menu.php'; ?>

        <div class="sidebar-bottom">

            <div class="admin-profile">

                <div class="avatar">
                    <?= $escape($initial) ?>
                </div>

                <div class="profile-info">
                    <strong><?= $escape($userName) ?></strong>
                    <span>Administrator</span>
                </div>

            </div>

            <a href="<?= BASE_URL ?>/logout"
               class="logout-link"
               aria-label="Logout">

                <i class="bi bi-box-arrow-right"></i>
                <span>Logout</span>
            </a>

        </div>

    </aside>

    <!-- KONTEN UTAMA -->
    <main class="main-content">

        <header class="topbar">

            <div>
                <div class="breadcrumb">
                    Pages / <strong><?= $escape($pageTitle) ?></strong>
                </div>

                <h1><?= $escape($pageTitle) ?></h1>
            </div>

            <div class="topbar-right">

                <div class="admin-welcome">
                    <span>Selamat datang,</span>
                    <strong><?= $escape($userName) ?></strong>
                </div>

                <div class="avatar top-avatar">
                    <?= $escape($initial) ?>
                </div>

            </div>

        </header>

        <section class="dashboard-content">

            <?php if (!$isReport): ?>

                <div class="welcome-banner">

                    <div>
                        <span class="banner-label">
                            ADMINISTRATOR PANEL
                        </span>

                        <h2>Selamat Datang di FixTrack!</h2>

                        <p>
                            Kelola pengaduan dan pemeliharaan
                            fasilitas kampus melalui satu platform.
                        </p>
                    </div>

                    <div class="banner-icon">
                        <i class="bi bi-buildings"></i>
                    </div>

                </div>

            <?php endif; ?>

            <div class="section-heading">
                <h3>
                    <?= $isReport
                        ? 'Rekap Seluruh Pengaduan'
                        : 'Ringkasan Pengaduan' ?>
                </h3>

                <p>
                    Data seluruh periode berdasarkan status pengaduan saat ini
                </p>
            </div>

            <!-- KARTU STATISTIK -->
            <div class="stats-grid">

                <?php foreach ($cards as $card): ?>

                    <div class="stat-card">

                        <div class="stat-top">
                            <span><?= $escape($card['label']) ?></span>

                            <div class="stat-icon <?= $escape($card['color']) ?>">
                                <i class="bi <?= $escape($card['icon']) ?>"></i>
                            </div>
                        </div>

                        <h2><?= (int) $card['value'] ?></h2>
                        <p><?= $escape($card['hint']) ?></p>

                    </div>

                <?php endforeach; ?>

            </div>

            <div class="dashboard-grid">

                <!-- DISTRIBUSI STATUS -->
                <div class="content-card">

                    <div class="card-heading">

                        <div>
                            <h3>Distribusi Status Pengaduan</h3>
                            <p>Jumlah dan persentase dari seluruh laporan</p>
                        </div>

                        <i class="bi bi-bar-chart-line"></i>
                    </div>

                    <div class="admin-status-list">

                        <?php foreach ($statusRows as $row): ?>
                            <?php
                            $count = (int) ($statistics[$row['key']] ?? 0);

                            $percentage = $total > 0
                                ? ($count / $total) * 100
                                : 0;
                            ?>

                            <div class="admin-status-row"
                                 style="--bar-color: <?= $escape($row['color']) ?>;">

                                <div>
                                    <span><?= $escape($row['label']) ?></span>

                                    <strong>
                                        <?= $count ?>
                                        (<?= number_format($percentage, 1, ',', '.') ?>%)
                                    </strong>
                                </div>

                                <progress
                                    value="<?= $count ?>"
                                    max="<?= max(1, $total) ?>"
                                    aria-label="<?= $escape($row['label']) ?>">
                                </progress>

                            </div>

                        <?php endforeach; ?>

                    </div>

                    <?php if ($total === 0): ?>
                        <p class="admin-note">
                            Belum ada pengaduan yang tercatat.
                        </p>
                    <?php endif; ?>

                </div>

                <!-- TINDAK LANJUT ATAU KETERANGAN LAPORAN -->
                <div class="content-card">

                    <div class="card-heading">
                        <div>
                            <h3>
                                <?= $isReport
                                    ? 'Keterangan Laporan'
                                    : 'Perlu Ditindaklanjuti' ?>
                            </h3>

                            <p>Pengelolaan pengaduan fasilitas</p>
                        </div>

                        <i class="bi bi-info-circle"></i>
                    </div>

                    <?php if ($isReport): ?>

                        <p class="admin-note">
                            Rekap ini mencakup seluruh periode.
                            Setiap pengaduan dihitung berdasarkan
                            status terakhirnya, bukan jumlah
                            perubahan status atau jumlah penugasan.
                        </p>

                        <div class="system-info">

                            <div>
                                <span>Cakupan</span>
                                <strong>Seluruh periode</strong>
                            </div>

                            <div>
                                <span>Total pengaduan</span>
                                <strong><?= $total ?></strong>
                            </div>

                            <div>
                                <span>Administrator</span>
                                <strong><?= $escape($userName) ?></strong>
                            </div>

                        </div>

                    <?php else: ?>

                        <div class="admin-shortcuts">

                            <a href="<?= BASE_URL ?>/admin/complaints">
                                <span>Periksa pengaduan masuk</span>
                                <strong>
                                    <?= (int) ($statistics['waiting'] ?? 0) ?>
                                </strong>
                            </a>

                            <a href="<?= BASE_URL ?>/admin/assignments">
                                <span>Pengaduan berstatus diverifikasi</span>
                                <strong>
                                    <?= (int) ($statistics['verified'] ?? 0) ?>
                                </strong>
                            </a>

                            <a href="<?= BASE_URL ?>/admin/users">
                                <span>Kelola data pengguna</span>
                                <i class="bi bi-arrow-right"></i>
                            </a>

                            <a href="<?= BASE_URL ?>/admin/reports">
                                <span>Buka laporan statistik</span>
                                <i class="bi bi-arrow-right"></i>
                            </a>

                        </div>

                    <?php endif; ?>

                </div>

            </div>

            <!-- DAFTAR PENGADUAN -->
            <div class="content-card recent-card">

                <div class="card-heading">

                    <div>
                        <h3>
                            <?= $isReport
                                ? 'Daftar Seluruh Pengaduan'
                                : 'Pengaduan Terbaru' ?>
                        </h3>

                        <p>
                            <?= $isReport
                                ? 'Rincian laporan beserta status terakhir'
                                : 'Lima laporan terbaru yang masuk' ?>
                        </p>
                    </div>

                    <a href="<?= BASE_URL ?>/admin/complaints"
                       class="view-all">

                        <?= $isReport ? 'Kelola Pengaduan' : 'Lihat Semua' ?>
                        <i class="bi bi-arrow-right"></i>
                    </a>

                </div>

                <?php if (empty($complaints)): ?>

                    <div class="empty-table">
                        <i class="bi bi-inbox"></i>
                        <strong>Belum Ada Data Pengaduan</strong>

                        <span>
                            Laporan yang masuk akan ditampilkan di sini.
                        </span>
                    </div>

                <?php else: ?>

                    <div class="admin-table-wrap">

                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Kode</th>
                                    <th>Pengaduan</th>
                                    <th>Pelapor</th>
                                    <th>Lokasi</th>
                                    <th>Tanggal</th>
                                    <th>Status</th>
                                </tr>
                            </thead>

                            <tbody>

                            <?php foreach ($complaints as $item): ?>

                                <?php
                                $status = $item['status'] ?? '';

                                $timestamp = !empty($item['created_at'])
                                    ? strtotime($item['created_at'])
                                    : false;

                                $dateLabel = $timestamp !== false
                                    ? date('d/m/Y', $timestamp)
                                    : '-';
                                ?>

                                <tr>

                                    <td>
                                        <strong>
                                            <?= $escape($item['complaint_code'] ?? '-') ?>
                                        </strong>
                                    </td>

                                    <td>
                                        <?= $escape($item['title'] ?? '-') ?>
                                    </td>

                                    <td>
                                        <?= $escape($item['reporter_name'] ?? '-') ?>
                                    </td>

                                    <td>
                                        <?= $escape(
                                            ($item['building_name'] ?? '-')
                                            . ' - '
                                            . ($item['room_name'] ?? '-')
                                        ) ?>
                                    </td>

                                    <td><?= $escape($dateLabel) ?></td>

                                    <td>
    <span class="admin-status <?= $escape($status) ?>">
        <?= $escape($statusLabels[$status] ?? $status) ?>
    </span>

    <?php if ($confirmationText = CompletionStatus::label($item)): ?>
        <div style="margin-top: 6px; font-size: 12px; color: #64748b;">
            <?= $escape($confirmationText) ?>
        </div>
    <?php endif; ?>
</td>

                                </tr>

                            <?php endforeach; ?>

                            </tbody>
                        </table>

                    </div>

                <?php endif; ?>

            </div>

            <footer class="dashboard-footer">
                &copy; <?= date('Y') ?> FixTrack.
            </footer>

        </section>

    </main>

</div>

</body>
</html>