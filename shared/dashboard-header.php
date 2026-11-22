<?php

$escape = static function ($value) {
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES,
        'UTF-8'
    );
};

$userName = $escape($user['name'] ?? $roleLabel);
$initial = $escape(mb_substr($user['name'] ?? $roleLabel, 0, 1));

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Dashboard <?= $escape($roleLabel) ?> | FixTrack</title>

    <?php if ($roleLabel === 'Petugas'): ?>
        <!-- Tetap dipakai untuk tabel, tombol, dan modal petugas -->
        <link rel="stylesheet"
              href="<?= BASE_URL ?>/assets/css/petugas.css">
    <?php endif; ?>

    <link rel="stylesheet"
          href="<?= BASE_URL ?>/assets/css/admin.css">

    <link rel="stylesheet"
          href="<?= BASE_URL ?>/assets/css/dashboard-role.css">

    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body class="role-dashboard">

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
            MENU <?= $escape(strtoupper($roleLabel)) ?>
        </div>

        <nav class="sidebar-menu"
             aria-label="Menu <?= $escape($roleLabel) ?>">

            <?php foreach ($menus as $menu): ?>

                <a href="<?= $escape($menu['url']) ?>"
                   class="menu-item <?= $menu['active'] ? 'active' : '' ?>"
                   aria-label="<?= $escape($menu['label']) ?>"
                   <?= $menu['active'] ? 'aria-current="page"' : '' ?>>

                    <i class="bi <?= $escape($menu['icon']) ?>"
                       aria-hidden="true"></i>

                    <span><?= $escape($menu['label']) ?></span>
                </a>

            <?php endforeach; ?>

        </nav>

        <div class="sidebar-bottom">

            <div class="admin-profile">
                <div class="avatar"><?= $initial ?></div>

                <div class="profile-info">
                    <strong><?= $userName ?></strong>
                    <span><?= $escape($roleLabel) ?></span>
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

    <main class="main-content">

        <header class="topbar">

            <div>
                <div class="breadcrumb">
                    Pages / <strong>Dashboard</strong>
                </div>

                <h1>Dashboard <?= $escape($roleLabel) ?></h1>
            </div>

            <div class="topbar-right">

                <div class="admin-welcome">
                    <span>Selamat datang,</span>
                    <strong><?= $userName ?></strong>
                </div>

                <div class="avatar top-avatar">
                    <?= $initial ?>
                </div>

            </div>

        </header>

        <section class="dashboard-content">

            <div class="welcome-banner">

                <div>
                    <span class="banner-label">
                        PANEL <?= $escape(strtoupper($roleLabel)) ?>
                    </span>

                    <h2><?= $escape($welcomeTitle) ?></h2>

                    <p><?= $escape($welcomeText) ?></p>

                    <a href="<?= $escape($actionUrl) ?>"
                       class="banner-action">

                        <?= $escape($actionLabel) ?>
                        <i class="bi bi-arrow-right"></i>
                    </a>
                </div>

                <div class="banner-icon">
                    <i class="bi <?= $escape($bannerIcon) ?>"></i>
                </div>

            </div>

            <?php foreach (['success', 'error'] as $messageType): ?>
                <?php if (!empty($_SESSION[$messageType])): ?>

                    <div class="role-alert <?= $messageType ?>"
                         role="status">

                        <?= $escape($_SESSION[$messageType]) ?>
                    </div>

                    <?php unset($_SESSION[$messageType]); ?>

                <?php endif; ?>
            <?php endforeach; ?>

            <div class="section-heading">
                <h3><?= $escape($summaryTitle) ?></h3>
                <p>Ringkasan data milikmu yang tercatat di FixTrack</p>
            </div>

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

                <div class="content-card">

                    <div class="card-heading">
                        <div>
                            <h3>Ringkasan Status</h3>
                            <p><?= $escape($summaryTitle) ?> berdasarkan status</p>
                        </div>

                        <i class="bi bi-bar-chart-line"></i>
                    </div>

                    <div class="status-summary">

                        <?php foreach ($statusCounts as $status => $count): ?>

                            <div class="status-row">

                                <div>
                                    <span>
                                        <?= $escape($statusLabels[$status] ?? $status) ?>
                                    </span>

                                    <strong><?= (int) $count ?></strong>
                                </div>

                                <progress
                                    max="<?= max(1, $total) ?>"
                                    value="<?= (int) $count ?>"
                                    aria-label="<?= $escape($statusLabels[$status] ?? $status) ?>">
                                </progress>

                            </div>

                        <?php endforeach; ?>

                        <?php if ($total === 0): ?>
                            <p class="role-muted">
                                Belum ada data untuk ditampilkan.
                            </p>
                        <?php endif; ?>

                    </div>

                </div>

                <div class="content-card">

                    <div class="card-heading">
                        <div>
                            <h3>Langkah Berikutnya</h3>
                            <p><?= $escape($roleLabel) ?> FixTrack</p>
                        </div>

                        <i class="bi bi-info-circle"></i>
                    </div>

                    <p class="role-guidance">
                        <?= $escape($guidance) ?>
                    </p>

                    <a href="<?= $escape($actionUrl) ?>"
                       class="primary-link">

                        <?= $escape($actionLabel) ?>
                        <i class="bi bi-arrow-right"></i>
                    </a>

                    <div class="system-info">

                        <div>
                            <span>Pengguna</span>
                            <strong><?= $userName ?></strong>
                        </div>

                        <div>
                            <span>Peran</span>
                            <strong><?= $escape($roleLabel) ?></strong>
                        </div>

                    </div>

                </div>

            </div>