
<?php

$userName = htmlspecialchars($user['name'] ?? 'Mahasiswa');

$success = $_SESSION['success'] ?? null;
$error = $_SESSION['error'] ?? null;

unset($_SESSION['success'], $_SESSION['error']);

function reporterStatus($status)
{
    $labels = [
        'menunggu_verifikasi' => 'Menunggu Verifikasi',
        'diverifikasi' => 'Diverifikasi',
        'ditolak' => 'Ditolak',
        'ditugaskan' => 'Ditugaskan',
        'diproses' => 'Sedang Diproses',
        'selesai' => 'Selesai'
    ];

    return $labels[$status] ?? $status;
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Riwayat Pengaduan | FixTrack</title>

    <link rel="stylesheet"
      href="<?= BASE_URL ?>/assets/css/admin.css">

    <link rel="stylesheet"
          href="<?= BASE_URL ?>/assets/css/reporter.css">

    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

</head>

<body>

<div class="reporter-layout">

    <aside class="reporter-sidebar">

        <div class="reporter-brand">
            <div class="reporter-logo">F</div>

            <div>
                <h2>FixTrack</h2>
                <span>Facility Management</span>
            </div>
        </div>

        <div class="reporter-menu-label">MENU PELAPOR</div>

        <?php
$currentPath = rtrim(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH),
    '/'
);

$reporterMenus = [
    [
        'url' => BASE_URL . '/pelapor/dashboard',
        'icon' => 'bi-grid',
        'label' => 'Dashboard'
    ],
    [
        'url' => BASE_URL . '/pelapor/complaints',
        'icon' => 'bi-clipboard-check',
        'label' => 'Riwayat Pengaduan'
    ],
    [
        'url' => BASE_URL . '/pelapor/complaints/create',
        'icon' => 'bi-plus-circle',
        'label' => 'Buat Pengaduan'
    ]
];
?>

<nav class="reporter-menu">

    <?php foreach ($reporterMenus as $menu): ?>
        <?php $isActive = $currentPath === $menu['url']; ?>

        <a href="<?= htmlspecialchars($menu['url']) ?>"
           class="<?= $isActive ? 'active' : '' ?>"
           aria-label="<?= htmlspecialchars($menu['label']) ?>"
           <?= $isActive ? 'aria-current="page"' : '' ?>>

            <i class="bi <?= htmlspecialchars($menu['icon']) ?>"></i>
            <span><?= htmlspecialchars($menu['label']) ?></span>
        </a>

    <?php endforeach; ?>

</nav>

        <div class="reporter-sidebar-bottom">

            <div class="reporter-profile">
                <div class="reporter-avatar">M</div>

                <div>
                    <strong><?= $userName ?></strong>
                    <span>Pelapor</span>
                </div>
            </div>

           <a href="<?= BASE_URL ?>/logout"
   class="reporter-logout"
   aria-label="Logout">

    <i class="bi bi-box-arrow-right"></i>
    <span>Logout</span>
</a>

        </div>

    </aside>


    <main class="reporter-main">

        <header class="reporter-topbar">

            <div>
                <span>Pages / Riwayat Pengaduan</span>
                <h1>Riwayat Pengaduan</h1>
            </div>

            <div class="reporter-top-user">
                <span>Selamat datang,</span>
                <strong><?= $userName ?></strong>
            </div>

        </header>


        <section class="reporter-content">

            <div class="reporter-heading">

                <div>
                    <h2>Daftar Pengaduan Saya</h2>

                    <p>
                        Pantau perkembangan laporan kerusakan
                        fasilitas yang telah kamu kirim.
                    </p>
                </div>

                <a
                    href="<?= BASE_URL ?>/pelapor/complaints/create"
                    class="reporter-new-button"
                >
                    <i class="bi bi-plus-lg"></i>
                    Buat Pengaduan
                </a>

            </div>


            <?php if ($success): ?>
                <div class="reporter-alert success">
                    <i class="bi bi-check-circle"></i>
                    <?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="reporter-alert error">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>


            <div class="reporter-history-card">

                <div class="reporter-history-heading">

                    <div>
                        <h3>Pengaduan Saya</h3>
                        <p>
                            Total <?= count($complaints) ?> laporan.
                        </p>
                    </div>

                </div>


                <?php if (empty($complaints)): ?>

                    <div class="reporter-no-data">
                        <i class="bi bi-clipboard-x"></i>

                        <h3>Belum Ada Pengaduan</h3>

                        <p>
                            Kamu belum pernah mengirim laporan
                            kerusakan fasilitas kampus.
                        </p>

                        <a href="<?= BASE_URL ?>/pelapor/complaints/create">
                            Buat Pengaduan Pertama
                        </a>
                    </div>

                <?php else: ?>

                    <div class="reporter-history-list">

                        <?php foreach ($complaints as $item): ?>

                            <article class="reporter-history-item">

                                <div class="reporter-history-top">

                                    <div>
                                        <strong>
                                            <?= htmlspecialchars($item['complaint_code']) ?>
                                        </strong>

                                        <span>
                                            <?= date('d/m/Y H:i', strtotime($item['created_at'])) ?>
                                        </span>
                                    </div>

                                    <span class="complaint-status <?= htmlspecialchars($item['status']) ?>">
                                        <?= htmlspecialchars(reporterStatus($item['status'])) ?>
                                    </span>

                                </div>


                                <h3>
                                    <?= htmlspecialchars($item['title']) ?>
                                </h3>

                                <p>
                                    <?= htmlspecialchars($item['building_name']) ?>
                                    -
                                    <?= htmlspecialchars($item['room_name']) ?>
                                    -
                                    <?= htmlspecialchars($item['facility_name']) ?>
                                </p>

                                <div class="reporter-history-bottom">

                                    <span>
                                        <i class="bi bi-tag"></i>
                                        <?= htmlspecialchars($item['category_name']) ?>
                                    </span>

                                    <?php if ($item['status'] === 'ditolak' && !empty($item['rejection_reason'])): ?>

                                        <span class="rejection-note">
                                            Alasan: <?= htmlspecialchars($item['rejection_reason']) ?>
                                        </span>

                                    <?php elseif (!empty($item['admin_note'])): ?>

                                        <span>
                                            Catatan: <?= htmlspecialchars($item['admin_note']) ?>
                                        </span>

                                    <?php endif; ?>

                                </div>

                            </article>

                        <?php endforeach; ?>

                    </div>

                <?php endif; ?>

            </div>

        </section>

    </main>

</div>

</body>
</html>
