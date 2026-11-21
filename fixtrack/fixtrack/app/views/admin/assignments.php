
<?php

$userName = htmlspecialchars($user['name'] ?? 'Administrator');

$success = $_SESSION['success'] ?? null;
$error = $_SESSION['error'] ?? null;

unset($_SESSION['success'], $_SESSION['error']);

$totalAssignments = count($assignments);
$totalAvailable = count($complaints);

function assignmentStatus($status)
{
    $labels = [
        'ditugaskan' => 'Ditugaskan',
        'diterima' => 'Diterima',
        'diproses' => 'Sedang Diproses',
        'selesai' => 'Selesai',
        'dibatalkan' => 'Dibatalkan'
    ];

    return $labels[$status] ?? $status;
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Penugasan Petugas | FixTrack</title>

    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/admin.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/assignments.css">

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

            <a href="<?= BASE_URL ?>/admin/facilities" class="menu-item">
                <i class="bi bi-building"></i>
                <span>Data Fasilitas</span>
            </a>

            <a href="<?= BASE_URL ?>/admin/complaints" class="menu-item">
                <i class="bi bi-clipboard-check"></i>
                <span>Pengaduan</span>
            </a>

            <a href="<?= BASE_URL ?>/admin/assignments" class="menu-item active">
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


    <!-- MAIN -->
    <main class="main-content">

        <header class="topbar">

            <div>
                <div class="breadcrumb">
                    Pages / <strong>Penugasan Petugas</strong>
                </div>

                <h1>Penugasan Petugas</h1>
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

            <div class="assignment-heading">
                <div>
                    <h2>Manajemen Penugasan</h2>
                    <p>
                        Kelola pembagian tugas perbaikan fasilitas
                        kepada petugas kampus.
                    </p>
                </div>
            </div>


            <!-- ALERT -->
            <?php if ($success): ?>
                <div class="assignment-alert success">
                    <i class="bi bi-check-circle"></i>
                    <?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="assignment-alert error">
                    <i class="bi bi-exclamation-circle"></i>
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>


            <!-- SUMMARY -->
            <div class="assignment-summary">

                <div class="assignment-summary-card">
                    <div class="assignment-icon blue">
                        <i class="bi bi-clipboard-check"></i>
                    </div>

                    <span>Siap Ditugaskan</span>
                    <h2><?= $totalAvailable ?></h2>
                </div>

                <div class="assignment-summary-card">
                    <div class="assignment-icon purple">
                        <i class="bi bi-person-gear"></i>
                    </div>

                    <span>Total Penugasan</span>
                    <h2><?= $totalAssignments ?></h2>
                </div>

                <div class="assignment-summary-card">
                    <div class="assignment-icon green">
                        <i class="bi bi-check-circle"></i>
                    </div>

                    <span>Penugasan Selesai</span>
                    <h2>
                        <?= count(array_filter(
                            $assignments,
                            fn($a) => $a['status'] === 'selesai'
                        )) ?>
                    </h2>
                </div>

            </div>


            <!-- AVAILABLE COMPLAINTS -->
            <div class="content-card assignment-card">

                <div class="assignment-card-heading">
                    <div>
                        <h3>Pengaduan Siap Ditugaskan</h3>
                        <p>
                            Pengaduan yang telah diverifikasi
                            dan belum memiliki petugas.
                        </p>
                    </div>
                </div>


                <?php if (empty($complaints)): ?>

                    <div class="assignment-empty">
                        <i class="bi bi-clipboard-check"></i>
                        <h3>Tidak Ada Pengaduan</h3>
                        <p>
                            Belum ada pengaduan yang siap ditugaskan
                            kepada petugas.
                        </p>
                    </div>

                <?php else: ?>

                    <div class="assignment-list">

                        <?php foreach ($complaints as $item): ?>

                            <div class="assignment-item">

                                <div class="assignment-item-info">

                                    <span class="assignment-code">
                                        <?= htmlspecialchars($item['complaint_code']) ?>
                                    </span>

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

                                    <div class="assignment-meta">
                                        <span>
                                            <i class="bi bi-person"></i>
                                            <?= htmlspecialchars($item['reporter_name']) ?>
                                        </span>

                                        <span>
                                            <i class="bi bi-tag"></i>
                                            <?= htmlspecialchars($item['category_name']) ?>
                                        </span>

                                        <span class="assignment-verified">
                                            <i class="bi bi-check-circle"></i>
                                            Diverifikasi
                                        </span>
                                    </div>

                                </div>


                                <div class="assignment-form-container">

                                    <form
                                        action="<?= BASE_URL ?>/admin/assignments/store"
                                        method="POST"
                                    >

                                        <input
                                            type="hidden"
                                            name="csrf_token"
                                            value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>"
                                        >

                                        <input
                                            type="hidden"
                                            name="complaint_id"
                                            value="<?= (int)$item['id'] ?>"
                                        >

                                        <label>Pilih Petugas</label>

                                        <select name="officer_id" required>

                                            <option value="">
                                                Pilih petugas
                                            </option>

                                            <?php foreach ($officers as $officer): ?>

                                                <option value="<?= (int)$officer['id'] ?>">
                                                    <?= htmlspecialchars($officer['name']) ?>
                                                </option>

                                            <?php endforeach; ?>

                                        </select>

                                        <label>Catatan Penugasan</label>

                                        <textarea
                                            name="notes"
                                            rows="2"
                                            placeholder="Instruksi tambahan untuk petugas..."
                                        ></textarea>

                                        <button
                                            type="submit"
                                            <?= empty($officers) ? 'disabled' : '' ?>
                                            onclick="return confirm('Tugaskan pengaduan ini kepada petugas yang dipilih?')"
                                        >
                                            <i class="bi bi-send"></i>
                                            Tugaskan Petugas
                                        </button>

                                    </form>

                                </div>

                            </div>

                        <?php endforeach; ?>

                    </div>

                <?php endif; ?>

            </div>


            <!-- ASSIGNMENT HISTORY -->
            <div class="content-card assignment-card">

                <div class="assignment-card-heading">
                    <div>
                        <h3>Riwayat Penugasan</h3>
                        <p>
                            Daftar pengaduan yang telah diberikan
                            kepada petugas.
                        </p>
                    </div>
                </div>

                <div class="table-responsive">

                    <table class="assignment-table">

                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Kode Pengaduan</th>
                                <th>Judul Pengaduan</th>
                                <th>Petugas</th>
                                <th>Tanggal Penugasan</th>
                                <th>Status</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php if (empty($assignments)): ?>

                                <tr>
                                    <td colspan="6" class="text-center">
                                        Belum ada riwayat penugasan.
                                    </td>
                                </tr>

                            <?php else: ?>

                                <?php foreach ($assignments as $index => $assignment): ?>

                                    <tr>
                                        <td><?= $index + 1 ?></td>

                                        <td>
                                            <strong>
                                                <?= htmlspecialchars($assignment['complaint_code']) ?>
                                            </strong>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($assignment['title']) ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($assignment['officer_name']) ?>
                                        </td>

                                        <td>
                                            <?= date(
                                                'd/m/Y H:i',
                                                strtotime($assignment['assigned_at'])
                                            ) ?>
                                        </td>

                                        <td>
                                            <span class="assignment-status <?= htmlspecialchars($assignment['status']) ?>">
                                                <?= htmlspecialchars(assignmentStatus($assignment['status'])) ?>
                                            </span>
                                        </td>
                                    </tr>

                                <?php endforeach; ?>

                            <?php endif; ?>

                        </tbody>

                    </table>

                </div>

            </div>

        </section>

    </main>

</div>

</body>
</html>
