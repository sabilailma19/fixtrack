<?php

$adminCurrentPath = rtrim(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH),
    '/'
);

$adminMenus = [
    ['dashboard', 'bi-grid', 'Dashboard'],
    ['users', 'bi-people', 'Data Pengguna'],
    ['categories', 'bi-tags', 'Kategori Kerusakan'],
    ['facilities', 'bi-building', 'Data Fasilitas'],
    ['complaints', 'bi-clipboard-check', 'Pengaduan'],
    ['assignments', 'bi-person-gear', 'Penugasan Petugas'],
    ['reports', 'bi-bar-chart', 'Laporan Statistik']
];

?>

<nav class="sidebar-menu" aria-label="Menu Admin">

    <?php foreach ($adminMenus as [$path, $icon, $label]): ?>
        <?php
        $url = BASE_URL . '/admin/' . $path;
        $active = $adminCurrentPath === $url;
        ?>

        <a href="<?= htmlspecialchars($url, ENT_QUOTES, 'UTF-8') ?>"
           class="menu-item <?= $active ? 'active' : '' ?>"
           aria-label="<?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?>"
           <?= $active ? 'aria-current="page"' : '' ?>>

            <i class="bi <?= htmlspecialchars($icon, ENT_QUOTES, 'UTF-8') ?>"></i>
            <span><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></span>
        </a>

    <?php endforeach; ?>

</nav>