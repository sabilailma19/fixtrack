
<?php
$userName = htmlspecialchars($user['name'] ?? 'Administrator');
$success = $_SESSION['success'] ?? null;
$error = $_SESSION['error'] ?? null;

unset($_SESSION['success'], $_SESSION['error']);

$adminCount = 0;
$officerCount = 0;
$reporterCount = 0;

foreach ($users as $item) {
    if ($item['role'] === 'admin') $adminCount++;
    if ($item['role'] === 'petugas') $officerCount++;
    if ($item['role'] === 'pelapor') $reporterCount++;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Data Pengguna | FixTrack</title>

    <link rel="stylesheet"
          href="<?= BASE_URL ?>/assets/css/admin.css">

    <link rel="stylesheet"
          href="<?= BASE_URL ?>/assets/css/users.css">

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

        <?php require __DIR__ . '/../shared/admin-menu.php'; ?>

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
                    Pages / <strong>Data Pengguna</strong>
                </div>

                <h1>Data Pengguna</h1>
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

            <div class="users-heading">
                <div>
                    <h2>Manajemen Pengguna</h2>
                    <p>Kelola akun Admin, Petugas, dan Pelapor FixTrack.</p>
                </div>

                <button class="btn-add" onclick="openModal()">
                    <i class="bi bi-plus-lg"></i>
                    Tambah Pengguna
                </button>
            </div>


            <?php if ($success): ?>
                <div class="alert-success">
                    <i class="bi bi-check-circle"></i>
                    <?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert-danger">
                    <i class="bi bi-exclamation-circle"></i>
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>


            <!-- SUMMARY -->
            <div class="user-summary">

                <div class="summary-card">
                    <span>Total Pengguna</span>
                    <h2><?= count($users) ?></h2>
                </div>

                <div class="summary-card">
                    <span>Administrator</span>
                    <h2><?= $adminCount ?></h2>
                </div>

                <div class="summary-card">
                    <span>Petugas</span>
                    <h2><?= $officerCount ?></h2>
                </div>

                <div class="summary-card">
                    <span>Pelapor</span>
                    <h2><?= $reporterCount ?></h2>
                </div>

            </div>


            <!-- USER TABLE -->
            <div class="content-card users-table-card">

                <div class="table-heading">
                    <div>
                        <h3>Daftar Pengguna</h3>
                        <p>Data seluruh pengguna yang terdaftar di FixTrack.</p>
                    </div>
                </div>

                <div class="table-tools">

                    <input
                        type="text"
                        id="searchUser"
                        placeholder="Cari nama atau email..."
                    >

                    <select id="filterRole">
                        <option value="">Semua Role</option>
                        <option value="admin">Admin</option>
                        <option value="petugas">Petugas</option>
                        <option value="pelapor">Pelapor</option>
                    </select>

                    <select id="filterStatus">
                        <option value="">Semua Status</option>
                        <option value="1">Aktif</option>
                        <option value="0">Nonaktif</option>
                    </select>

                </div>

                <div class="table-responsive">

                    <table class="users-table">

                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Nama Pengguna</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>

                        <tbody id="userTableBody">

                        <?php foreach ($users as $index => $item): ?>

                            <tr
                                data-name="<?= htmlspecialchars(strtolower($item['name'])) ?>"
                                data-email="<?= htmlspecialchars(strtolower($item['email'])) ?>"
                                data-role="<?= htmlspecialchars($item['role']) ?>"
                                data-status="<?= (int)$item['is_active'] ?>"
                            >

                                <td><?= $index + 1 ?></td>

                                <td>
                                    <div class="user-cell">
                                        <div class="user-avatar">
                                            <?= strtoupper(substr($item['name'], 0, 1)) ?>
                                        </div>

                                        <strong>
                                            <?= htmlspecialchars($item['name']) ?>
                                        </strong>
                                    </div>
                                </td>

                                <td><?= htmlspecialchars($item['email']) ?></td>

                                <td>
                                    <span class="role-badge role-<?= $item['role'] ?>">
                                        <?= ucfirst($item['role']) ?>
                                    </span>
                                </td>

                                <td>
                                    <?php if ($item['is_active']): ?>
                                        <span class="status-badge active">Aktif</span>
                                    <?php else: ?>
                                        <span class="status-badge inactive">Nonaktif</span>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <div class="action-buttons">

                                        <button
                                            class="btn-edit"
                                            onclick='editUser(<?= json_encode([
                                                "id" => $item["id"],
                                                "name" => $item["name"],
                                                "email" => $item["email"],
                                                "role" => $item["role"],
                                                "identity_number" => $item["identity_number"],
                                                "phone" => $item["phone"]
                                            ], JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_HEX_TAG) ?>)'
                                            title="Edit"
                                        >
                                            <i class="bi bi-pencil"></i>
                                        </button>

                                        <?php if ((int)$item['id'] !== (int)$user['id']): ?>

                                            <form
                                                action="<?= BASE_URL ?>/admin/users/toggle"
                                                method="POST"
                                                onsubmit="return confirm('Yakin ingin mengubah status akun ini?')"
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

                                                <button class="btn-toggle" type="submit">
                                                    <i class="bi <?= $item['is_active'] ? 'bi-person-slash' : 'bi-person-check' ?>"></i>
                                                </button>

                                            </form>

                                        <?php endif; ?>

                                    </div>
                                </td>

                            </tr>

                        <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

                <div class="table-empty" id="emptyMessage" hidden>
                    Data pengguna tidak ditemukan.
                </div>

            </div>

        </section>

    </main>

</div>


<!-- MODAL TAMBAH / EDIT -->
<div class="modal-overlay" id="userModal">

    <div class="user-modal">

        <div class="modal-header">
            <div>
                <h2 id="modalTitle">Tambah Pengguna</h2>
                <p>Lengkapi informasi akun pengguna.</p>
            </div>

            <button type="button" onclick="closeModal()" class="btn-close">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <form action="<?= BASE_URL ?>/admin/users/save" method="POST">

            <input
                type="hidden"
                name="csrf_token"
                value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>"
            >

            <input type="hidden" name="id" id="userId">

            <div class="modal-body">

                <div class="form-field">
                    <label>Nama Lengkap *</label>
                    <input type="text" name="name" id="userName" required>
                </div>

                <div class="form-field">
                    <label>Email *</label>
                    <input type="email" name="email" id="userEmail" required>
                </div>

                <div class="form-field">
                    <label>Password <span id="passwordHint">(Minimal 8 karakter)</span></label>
                    <input
                        type="password"
                        name="password"
                        id="userPassword"
                        minlength="8"
                        autocomplete="new-password"
                    >
                </div>

                <div class="form-field">
                    <label>Role *</label>
                    <select name="role" id="userRole" required>
                        <option value="">Pilih Role</option>
                        <option value="admin">Admin</option>
                        <option value="petugas">Petugas</option>
                        <option value="pelapor">Pelapor</option>
                    </select>
                </div>

                <div class="form-field">
                    <label>NIM / NIP</label>
                    <input type="text" name="identity_number" id="userIdentity">
                </div>

                <div class="form-field">
                    <label>Nomor Telepon</label>
                    <input type="text" name="phone" id="userPhone">
                </div>

            </div>

            <div class="modal-footer">

                <button type="button" class="btn-cancel" onclick="closeModal()">
                    Batal
                </button>

                <button type="submit" class="btn-save">
                    <i class="bi bi-check-lg"></i>
                    Simpan Pengguna
                </button>

            </div>

        </form>

    </div>

</div>

<script src="<?= BASE_URL ?>/assets/js/users.js"></script>

</body>
</html>
