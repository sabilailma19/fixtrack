
<?php

$userName = htmlspecialchars($user['name'] ?? 'Administrator');

$success = $_SESSION['success'] ?? null;
$error = $_SESSION['error'] ?? null;

unset($_SESSION['success'], $_SESSION['error']);

$activeCount = 0;
$inactiveCount = 0;

foreach ($categories as $item) {
    if ($item['is_active']) {
        $activeCount++;
    } else {
        $inactiveCount++;
    }
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Kategori Kerusakan | FixTrack</title>

    <link rel="stylesheet"
          href="<?= BASE_URL ?>/assets/css/admin.css">

    <link rel="stylesheet"
          href="<?= BASE_URL ?>/assets/css/categories.css">

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

            <a href="<?= BASE_URL ?>/admin/dashboard" class="menu-item">
                <i class="bi bi-grid"></i>
                <span>Dashboard</span>
            </a>

            <a href="<?= BASE_URL ?>/admin/users" class="menu-item">
                <i class="bi bi-people"></i>
                <span>Data Pengguna</span>
            </a>

            <a href="<?= BASE_URL ?>/admin/categories" class="menu-item active">
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

        <header class="topbar">

            <div>
                <div class="breadcrumb">
                    Pages / <strong>Kategori Kerusakan</strong>
                </div>

                <h1>Kategori Kerusakan</h1>
            </div>

            <div class="topbar-right">

                <div class="admin-welcome">
                    <span>Selamat datang,</span>
                    <strong><?= $userName ?></strong>
                </div>

                <div class="avatar top-avatar">A</div>

            </div>

        </header>


        <section class="dashboard-content">

            <!-- HEADING -->

            <div class="category-heading">

                <div>
                    <h2>Manajemen Kategori</h2>

                    <p>
                        Kelola jenis kerusakan fasilitas kampus
                        yang tersedia di FixTrack.
                    </p>
                </div>

                <button class="btn-category-add" onclick="openCategoryModal()">
                    <i class="bi bi-plus-lg"></i>
                    Tambah Kategori
                </button>

            </div>


            <!-- ALERT -->

            <?php if ($success): ?>

                <div class="category-alert success">
                    <i class="bi bi-check-circle"></i>
                    <?= htmlspecialchars($success) ?>
                </div>

            <?php endif; ?>


            <?php if ($error): ?>

                <div class="category-alert error">
                    <i class="bi bi-exclamation-circle"></i>
                    <?= htmlspecialchars($error) ?>
                </div>

            <?php endif; ?>


            <!-- SUMMARY -->

            <div class="category-summary">

                <div class="category-summary-card">
                    <span>Total Kategori</span>
                    <h2><?= count($categories) ?></h2>
                </div>

                <div class="category-summary-card">
                    <span>Kategori Aktif</span>
                    <h2><?= $activeCount ?></h2>
                </div>

                <div class="category-summary-card">
                    <span>Kategori Nonaktif</span>
                    <h2><?= $inactiveCount ?></h2>
                </div>

            </div>


            <!-- TABLE -->

            <div class="content-card category-table-card">

                <div class="category-table-heading">

                    <div>
                        <h3>Daftar Kategori Kerusakan</h3>

                        <p>
                            Seluruh kategori kerusakan yang terdaftar.
                        </p>
                    </div>

                </div>


                <div class="category-search">

                    <i class="bi bi-search"></i>

                    <input
                        type="text"
                        id="searchCategory"
                        placeholder="Cari nama atau deskripsi kategori..."
                    >

                </div>


                <div class="table-responsive">

                    <table class="category-table">

                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Nama Kategori</th>
                                <th>Deskripsi</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>

                        <tbody id="categoryTableBody">

                        <?php foreach ($categories as $index => $item): ?>

                            <tr
                                data-name="<?= htmlspecialchars(strtolower($item['category_name'])) ?>"
                                data-description="<?= htmlspecialchars(strtolower($item['description'] ?? '')) ?>"
                            >

                                <td><?= $index + 1 ?></td>

                                <td>
                                    <div class="category-name-cell">

                                        <div class="category-icon">
                                            <i class="bi bi-tag"></i>
                                        </div>

                                        <strong>
                                            <?= htmlspecialchars($item['category_name']) ?>
                                        </strong>

                                    </div>
                                </td>

                                <td>
                                    <?= htmlspecialchars($item['description'] ?? '-') ?>
                                </td>

                                <td>

                                    <?php if ($item['is_active']): ?>

                                        <span class="category-status active">
                                            Aktif
                                        </span>

                                    <?php else: ?>

                                        <span class="category-status inactive">
                                            Nonaktif
                                        </span>

                                    <?php endif; ?>

                                </td>

                                <td>

                                    <div class="category-actions">

                                        <button
                                            type="button"
                                            class="category-edit"
                                            onclick='editCategory(<?= json_encode([
                                                "id" => $item["id"],
                                                "name" => $item["category_name"],
                                                "description" => $item["description"]
                                            ], JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_HEX_TAG) ?>)'
                                            title="Edit kategori"
                                        >
                                            <i class="bi bi-pencil"></i>
                                        </button>


                                        <form
                                            action="<?= BASE_URL ?>/admin/categories/toggle"
                                            method="POST"
                                            onsubmit="return confirm('Yakin ingin mengubah status kategori ini?')"
                                        >

                                            <input
                                                type="hidden"
                                                name="csrf_token"
                                                value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>"
                                            >

                                            <input
                                                type="hidden"
                                                name="id"
                                                value="<?= (int)$item['id'] ?>"
                                            >

                                            <input
                                                type="hidden"
                                                name="status"
                                                value="<?= $item['is_active'] ? 0 : 1 ?>"
                                            >

                                            <button
                                                type="submit"
                                                class="category-toggle"
                                                title="<?= $item['is_active'] ? 'Nonaktifkan' : 'Aktifkan' ?>"
                                            >
                                                <i class="bi <?= $item['is_active'] ? 'bi-toggle-on' : 'bi-toggle-off' ?>"></i>
                                            </button>

                                        </form>

                                    </div>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>


                <div id="categoryEmpty" class="category-empty" hidden>
                    Kategori tidak ditemukan.
                </div>

            </div>

        </section>

    </main>

</div>


<!-- MODAL -->

<div class="category-modal-overlay" id="categoryModal">

    <div class="category-modal">

        <div class="category-modal-header">

            <div>
                <h2 id="categoryModalTitle">Tambah Kategori</h2>

                <p>
                    Lengkapi informasi kategori kerusakan.
                </p>
            </div>

            <button type="button" onclick="closeCategoryModal()">
                <i class="bi bi-x-lg"></i>
            </button>

        </div>


        <form action="<?= BASE_URL ?>/admin/categories/save" method="POST">

            <input
                type="hidden"
                name="csrf_token"
                value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>"
            >

            <input
                type="hidden"
                name="id"
                id="categoryId"
            >


            <div class="category-modal-body">

                <div class="category-field">

                    <label>Nama Kategori *</label>

                    <input
                        type="text"
                        name="category_name"
                        id="categoryName"
                        maxlength="100"
                        placeholder="Contoh: Kerusakan Elektronik"
                        required
                    >

                </div>


                <div class="category-field">

                    <label>Deskripsi</label>

                    <textarea
                        name="description"
                        id="categoryDescription"
                        rows="4"
                        placeholder="Tuliskan deskripsi kategori..."
                    ></textarea>

                </div>

            </div>


            <div class="category-modal-footer">

                <button
                    type="button"
                    class="category-cancel"
                    onclick="closeCategoryModal()"
                >
                    Batal
                </button>

                <button type="submit" class="category-save">
                    <i class="bi bi-check-lg"></i>
                    Simpan Kategori
                </button>

            </div>

        </form>

    </div>

</div>


<script src="<?= BASE_URL ?>/assets/js/categories.js"></script>

</body>
</html>
