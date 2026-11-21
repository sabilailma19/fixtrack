
<?php

$userName = htmlspecialchars($user['name'] ?? 'Administrator');

$success = $_SESSION['success'] ?? null;
$error = $_SESSION['error'] ?? null;

unset($_SESSION['success'], $_SESSION['error']);

$activeCount = 0;
$inactiveCount = 0;

foreach ($facilities as $item) {
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

    <title>Data Fasilitas | FixTrack</title>

    <link rel="stylesheet"
          href="<?= BASE_URL ?>/assets/css/admin.css">

    <link rel="stylesheet"
          href="<?= BASE_URL ?>/assets/css/facilities.css">

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

            <a href="<?= BASE_URL ?>/admin/categories" class="menu-item">
                <i class="bi bi-tags"></i>
                <span>Kategori Kerusakan</span>
            </a>

            <a href="<?= BASE_URL ?>/admin/facilities" class="menu-item active">
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
                    Pages / <strong>Data Fasilitas</strong>
                </div>

                <h1>Data Fasilitas</h1>
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

            <div class="facility-heading">

                <div>
                    <h2>Manajemen Fasilitas Kampus</h2>

                    <p>
                        Kelola data gedung, ruangan, dan fasilitas
                        yang tersedia di lingkungan kampus.
                    </p>
                </div>

                <button
                    class="btn-facility-add"
                    onclick="openFacilityModal()"
                >
                    <i class="bi bi-plus-lg"></i>
                    Tambah Fasilitas
                </button>

            </div>


            <!-- ALERT -->

            <?php if ($success): ?>

                <div class="facility-alert success">
                    <i class="bi bi-check-circle"></i>
                    <?= htmlspecialchars($success) ?>
                </div>

            <?php endif; ?>


            <?php if ($error): ?>

                <div class="facility-alert error">
                    <i class="bi bi-exclamation-circle"></i>
                    <?= htmlspecialchars($error) ?>
                </div>

            <?php endif; ?>


            <!-- SUMMARY -->

            <div class="facility-summary">

                <div class="facility-summary-card">

                    <div class="summary-icon blue">
                        <i class="bi bi-building"></i>
                    </div>

                    <span>Total Fasilitas</span>
                    <h2><?= count($facilities) ?></h2>

                </div>


                <div class="facility-summary-card">

                    <div class="summary-icon green">
                        <i class="bi bi-check-circle"></i>
                    </div>

                    <span>Fasilitas Aktif</span>
                    <h2><?= $activeCount ?></h2>

                </div>


                <div class="facility-summary-card">

                    <div class="summary-icon orange">
                        <i class="bi bi-pause-circle"></i>
                    </div>

                    <span>Fasilitas Nonaktif</span>
                    <h2><?= $inactiveCount ?></h2>

                </div>

            </div>


            <!-- TABLE -->

            <div class="content-card facility-table-card">

                <div class="facility-table-heading">

                    <div>
                        <h3>Daftar Fasilitas Kampus</h3>

                        <p>
                            Informasi seluruh fasilitas yang terdaftar
                            di FixTrack.
                        </p>
                    </div>

                </div>


                <!-- SEARCH AND FILTER -->

                <div class="facility-tools">

                    <div class="facility-search">

                        <i class="bi bi-search"></i>

                        <input
                            type="text"
                            id="searchFacility"
                            placeholder="Cari gedung, ruangan, atau fasilitas..."
                        >

                    </div>


                    <select id="filterBuilding">

                        <option value="">Semua Gedung</option>

                        <?php foreach ($buildings as $building): ?>

                            <option value="<?= htmlspecialchars($building, ENT_QUOTES, 'UTF-8') ?>">
                                <?= htmlspecialchars($building) ?>
                            </option>

                        <?php endforeach; ?>

                    </select>


                    <select id="filterFacilityStatus">

                        <option value="">Semua Status</option>
                        <option value="1">Aktif</option>
                        <option value="0">Nonaktif</option>

                    </select>

                </div>


                <!-- DATA TABLE -->

                <div class="table-responsive">

                    <table class="facility-table">

                        <thead>

                            <tr>
                                <th>No.</th>
                                <th>Gedung</th>
                                <th>Ruangan</th>
                                <th>Nama Fasilitas</th>
                                <th>Deskripsi</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>

                        </thead>


                        <tbody id="facilityTableBody">

                        <?php foreach ($facilities as $index => $item): ?>

                            <tr
                                data-building="<?= htmlspecialchars($item['building_name']) ?>"
                                data-search="<?= htmlspecialchars(strtolower(
                                    $item['building_name'] . ' ' .
                                    $item['room_name'] . ' ' .
                                    $item['facility_name'] . ' ' .
                                    ($item['description'] ?? '')
                                )) ?>"
                                data-status="<?= (int)$item['is_active'] ?>"
                            >

                                <td><?= $index + 1 ?></td>


                                <td>

                                    <div class="facility-building">

                                        <div class="facility-icon">
                                            <i class="bi bi-buildings"></i>
                                        </div>

                                        <strong>
                                            <?= htmlspecialchars($item['building_name']) ?>
                                        </strong>

                                    </div>

                                </td>


                                <td>
                                    <?= htmlspecialchars($item['room_name']) ?>
                                </td>


                                <td>
                                    <strong>
                                        <?= htmlspecialchars($item['facility_name']) ?>
                                    </strong>
                                </td>


                                <td>
                                    <?= htmlspecialchars($item['description'] ?? '-') ?>
                                </td>


                                <td>

                                    <?php if ($item['is_active']): ?>

                                        <span class="facility-status active">
                                            Aktif
                                        </span>

                                    <?php else: ?>

                                        <span class="facility-status inactive">
                                            Nonaktif
                                        </span>

                                    <?php endif; ?>

                                </td>


                                <td>

                                    <div class="facility-actions">

                                        <button
                                            type="button"
                                            class="facility-edit"
                                            title="Edit fasilitas"
                                            onclick='editFacility(<?= json_encode([
                                                "id" => $item["id"],
                                                "building_name" => $item["building_name"],
                                                "room_name" => $item["room_name"],
                                                "facility_name" => $item["facility_name"],
                                                "description" => $item["description"]
                                            ], JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_HEX_TAG) ?>)'
                                        >
                                            <i class="bi bi-pencil"></i>
                                        </button>


                                        <form
                                            action="<?= BASE_URL ?>/admin/facilities/toggle"
                                            method="POST"
                                            onsubmit="return confirm('Yakin ingin mengubah status fasilitas ini?')"
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
                                                class="facility-toggle"
                                                title="Ubah status"
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


                <div id="facilityEmpty" class="facility-empty" hidden>
                    Data fasilitas tidak ditemukan.
                </div>

            </div>

        </section>

    </main>

</div>


<!-- MODAL TAMBAH / EDIT -->

<div class="facility-modal-overlay" id="facilityModal">

    <div class="facility-modal">

        <div class="facility-modal-header">

            <div>
                <h2 id="facilityModalTitle">Tambah Fasilitas</h2>

                <p>
                    Lengkapi informasi fasilitas kampus.
                </p>
            </div>

            <button type="button" onclick="closeFacilityModal()">
                <i class="bi bi-x-lg"></i>
            </button>

        </div>


        <form
            action="<?= BASE_URL ?>/admin/facilities/save"
            method="POST"
        >

            <input
                type="hidden"
                name="csrf_token"
                value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>"
            >

            <input
                type="hidden"
                name="id"
                id="facilityId"
            >


            <div class="facility-modal-body">

                <div class="facility-field">

                    <label>Nama Gedung *</label>

                    <input
                        type="text"
                        name="building_name"
                        id="buildingName"
                        maxlength="100"
                        placeholder="Contoh: Gedung Fasilkom"
                        required
                    >

                </div>


                <div class="facility-field">

                    <label>Nama Ruangan *</label>

                    <input
                        type="text"
                        name="room_name"
                        id="roomName"
                        maxlength="100"
                        placeholder="Contoh: Lab Komputer 1"
                        required
                    >

                </div>


                <div class="facility-field">

                    <label>Nama Fasilitas *</label>

                    <input
                        type="text"
                        name="facility_name"
                        id="facilityName"
                        maxlength="150"
                        placeholder="Contoh: Komputer"
                        required
                    >

                </div>


                <div class="facility-field">

                    <label>Deskripsi</label>

                    <textarea
                        name="description"
                        id="facilityDescription"
                        rows="4"
                        placeholder="Tuliskan deskripsi fasilitas..."
                    ></textarea>

                </div>

            </div>


            <div class="facility-modal-footer">

                <button
                    type="button"
                    class="facility-cancel"
                    onclick="closeFacilityModal()"
                >
                    Batal
                </button>

                <button type="submit" class="facility-save">
                    <i class="bi bi-check-lg"></i>
                    Simpan Fasilitas
                </button>

            </div>

        </form>

    </div>

</div>


<script src="<?= BASE_URL ?>/assets/js/facilities.js"></script>

</body>
</html>
