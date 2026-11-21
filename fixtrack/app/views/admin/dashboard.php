
<?php
$userName = htmlspecialchars($user['name'] ?? 'Administrator');
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard Admin | FixTrack</title>

    <link rel="stylesheet"
          href="<?= BASE_URL ?>/assets/css/admin.css">

    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
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

        <div class="sidebar-label">MENU UTAMA</div>

        <nav class="sidebar-menu">

            <a href="<?= BASE_URL ?>/admin/dashboard" class="menu-item active">
                <i class="bi bi-grid"></i>
                <span>Dashboard</span>
            </a>

            <a href="#" class="menu-item">
                <i class="bi bi-people"></i>
                <span>Data Pengguna</span>
            </a>

           <a href="<?= BASE_URL ?>/admin/categories" class="menu-item">
                <i class="bi bi-tags"></i>
                <span>Kategori Kerusakan</span>
            </a>

            <a href="<?= BASE_URL ?>/admin/facilities" class="menu-item">
                <i class="bi bi-building"></i>
                <span>Data Fasilitas</span>
            </a>

        <a href="<?= BASE_URL ?>/admin/complaints">
            <i class="bi bi-clipboard-check"></i>
            <span>Pengaduan</span>
        </a>

        <a href="<?= BASE_URL ?>/admin/assignments" class="menu-item">
            <i class="bi bi-person-gear"></i>
            <span>Penugasan Petugas</span>
        </a>

            <a href="#" class="menu-item">
                <i class="bi bi-bar-chart"></i>
                <span>Laporan Statistik</span>
            </a>

        </nav>

        <div class="sidebar-bottom">
            <div class="admin-profile">
                <div class="avatar">A</div>
                <div class="profile-info">
                    <strong><?= $userName ?></strong>
                    <span>Administrator</span>
                </div>
            </div>

            <a href="<?= BASE_URL ?>/logout" class="logout-link">
                <i class="bi bi-box-arrow-right"></i>
                Logout
            </a>
        </div>

    </aside>


    <!-- MAIN CONTENT -->
    <main class="main-content">

        <!-- TOPBAR -->
        <header class="topbar">

            <div>
                <div class="breadcrumb">
                    Pages / <strong>Dashboard</strong>
                </div>
                <h1>Dashboard</h1>
            </div>

            <div class="topbar-right">

                <div class="admin-welcome">
                    <span>Selamat datang,</span>
                    <strong><?= $userName ?></strong>
                </div>

                <div class="avatar top-avatar">A</div>

            </div>

        </header>


        <!-- PAGE CONTENT -->
        <section class="dashboard-content">

            <div class="welcome-banner">

                <div>
                    <span class="banner-label">ADMINISTRATOR PANEL</span>

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


            <!-- STATISTICS -->
            <div class="section-heading">
                <div>
                    <h3>Ringkasan Pengaduan</h3>
                    <p>Informasi pengelolaan laporan fasilitas kampus</p>
                </div>
            </div>


            <div class="stats-grid">

                <div class="stat-card">
                    <div class="stat-top">
                        <span>Total Pengaduan</span>
                        <div class="stat-icon blue">
                            <i class="bi bi-clipboard-data"></i>
                        </div>
                    </div>
                    <h2>--</h2>
                    <p>Menunggu integrasi data</p>
                </div>

                <div class="stat-card">
                    <div class="stat-top">
                        <span>Menunggu Verifikasi</span>
                        <div class="stat-icon orange">
                            <i class="bi bi-clock-history"></i>
                        </div>
                    </div>
                    <h2>--</h2>
                    <p>Menunggu integrasi data</p>
                </div>

                <div class="stat-card">
                    <div class="stat-top">
                        <span>Sedang Diproses</span>
                        <div class="stat-icon purple">
                            <i class="bi bi-tools"></i>
                        </div>
                    </div>
                    <h2>--</h2>
                    <p>Menunggu integrasi data</p>
                </div>

                <div class="stat-card">
                    <div class="stat-top">
                        <span>Pengaduan Selesai</span>
                        <div class="stat-icon green">
                            <i class="bi bi-check-circle"></i>
                        </div>
                    </div>
                    <h2>--</h2>
                    <p>Menunggu integrasi data</p>
                </div>

            </div>


            <!-- CONTENT GRID -->
            <div class="dashboard-grid">

                <div class="content-card chart-card">

                    <div class="card-heading">
                        <div>
                            <h3>Statistik Pengaduan</h3>
                            <p>Ringkasan laporan berdasarkan status</p>
                        </div>

                        <i class="bi bi-bar-chart-line"></i>
                    </div>

                    <div class="empty-chart">
                        <i class="bi bi-graph-up"></i>
                        <strong>Grafik belum tersedia</strong>
                        <span>
                            Grafik akan ditampilkan setelah
                            data pengaduan tersedia.
                        </span>
                    </div>

                </div>


                <div class="content-card">

                    <div class="card-heading">
                        <div>
                            <h3>Informasi Sistem</h3>
                            <p>Ringkasan FixTrack</p>
                        </div>
                        <i class="bi bi-info-circle"></i>
                    </div>

                    <div class="system-info">

                        <div>
                            <span>Status Sistem</span>
                            <strong class="online">
                                <i class="bi bi-circle-fill"></i>
                                Online
                            </strong>
                        </div>

                        <div>
                            <span>Pengguna Login</span>
                            <strong>Administrator</strong>
                        </div>

                        <div>
                            <span>Platform</span>
                            <strong>FixTrack Web</strong>
                        </div>

                        <div>
                            <span>Tanggal</span>
                            <strong><?= date('d/m/Y') ?></strong>
                        </div>

                    </div>

                </div>

            </div>


            <!-- RECENT COMPLAINTS -->
            <div class="content-card recent-card">

                <div class="card-heading">
                    <div>
                        <h3>Pengaduan Terbaru</h3>
                        <p>Daftar laporan fasilitas kampus terbaru</p>
                    </div>

                    <a href="#" class="view-all">
                        Lihat Semua
                        <i class="bi bi-arrow-right"></i>
                    </a>
                </div>

                <div class="empty-table">

                    <i class="bi bi-inbox"></i>

                    <strong>Belum Ada Data Pengaduan</strong>

                    <span>
                        Pengaduan yang masuk nantinya akan
                        ditampilkan pada tabel ini.
                    </span>

                </div>

            </div>

            <footer class="dashboard-footer">
                © <?= date('Y') ?> FixTrack. All rights reserved.
            </footer>

        </section>

    </main>

</div>

</body>
</html>
