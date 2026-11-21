
<?php

$userName = htmlspecialchars($user['name'] ?? 'Administrator');

$success = $_SESSION['success'] ?? null;
$error = $_SESSION['error'] ?? null;

unset($_SESSION['success'], $_SESSION['error']);

$stats = $statistics ?: [];

function complaintStatus($status)
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

    <title>Pengaduan | FixTrack</title>

    <link rel="stylesheet"
          href="<?= BASE_URL ?>/assets/css/admin.css">

    <link rel="stylesheet"
          href="<?= BASE_URL ?>/assets/css/complaints.css">

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

            <a href="<?= BASE_URL ?>/admin/complaints" class="menu-item active">
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
                    Pages / <strong>Pengaduan</strong>
                </div>

                <h1>Pengaduan Kerusakan</h1>
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

            <div class="complaint-heading">
                <div>
                    <h2>Manajemen Pengaduan</h2>

                    <p>
                        Kelola dan verifikasi laporan kerusakan
                        fasilitas kampus.
                    </p>
                </div>
            </div>


            <!-- ALERT -->

            <?php if ($success): ?>
                <div class="complaint-alert success">
                    <i class="bi bi-check-circle"></i>
                    <?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="complaint-alert error">
                    <i class="bi bi-exclamation-circle"></i>
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>


            <!-- SUMMARY -->

            <div class="complaint-summary">

                <div class="complaint-summary-card">
                    <div class="summary-icon blue">
                        <i class="bi bi-clipboard-data"></i>
                    </div>

                    <span>Total Pengaduan</span>
                    <h2><?= (int)($stats['total'] ?? 0) ?></h2>
                </div>

                <div class="complaint-summary-card">
                    <div class="summary-icon orange">
                        <i class="bi bi-clock-history"></i>
                    </div>

                    <span>Menunggu Verifikasi</span>
                    <h2><?= (int)($stats['waiting'] ?? 0) ?></h2>
                </div>

                <div class="complaint-summary-card">
                    <div class="summary-icon purple">
                        <i class="bi bi-tools"></i>
                    </div>

                    <span>Sedang Diproses</span>
                    <h2><?= (int)($stats['processing'] ?? 0) ?></h2>
                </div>

                <div class="complaint-summary-card">
                    <div class="summary-icon green">
                        <i class="bi bi-check-circle"></i>
                    </div>

                    <span>Pengaduan Selesai</span>
                    <h2><?= (int)($stats['completed'] ?? 0) ?></h2>
                </div>

            </div>


            <!-- TABLE -->

            <div class="content-card complaint-table-card">

                <div class="complaint-table-heading">
                    <div>
                        <h3>Daftar Pengaduan</h3>

                        <p>
                            Seluruh laporan kerusakan yang dikirim
                            oleh pengguna FixTrack.
                        </p>
                    </div>
                </div>


                <!-- FILTER -->

                <div class="complaint-tools">

                    <div class="complaint-search">
                        <i class="bi bi-search"></i>

                        <input
                            type="text"
                            id="searchComplaint"
                            placeholder="Cari kode, judul, atau nama pelapor..."
                        >
                    </div>

                    <select id="filterComplaintStatus">
                        <option value="">Semua Status</option>
                        <option value="menunggu_verifikasi">Menunggu Verifikasi</option>
                        <option value="diverifikasi">Diverifikasi</option>
                        <option value="ditolak">Ditolak</option>
                        <option value="ditugaskan">Ditugaskan</option>
                        <option value="diproses">Diproses</option>
                        <option value="selesai">Selesai</option>
                    </select>

                </div>


                <!-- DATA -->

                <div class="table-responsive">

                    <table class="complaint-table">

                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Kode Pengaduan</th>
                                <th>Pelapor</th>
                                <th>Fasilitas</th>
                                <th>Kategori</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>

                        <tbody id="complaintTableBody">

                        <?php foreach ($complaints as $index => $item): ?>

                            <?php
                            $searchText = strtolower(
                                $item['complaint_code'] . ' ' .
                                $item['title'] . ' ' .
                                $item['reporter_name'] . ' ' .
                                $item['facility_name'] . ' ' .
                                $item['category_name']
                            );
                            ?>

                            <tr
                                data-search="<?= htmlspecialchars($searchText, ENT_QUOTES, 'UTF-8') ?>"
                                data-status="<?= htmlspecialchars($item['status']) ?>"
                            >

                                <td><?= $index + 1 ?></td>

                                <td>
                                    <strong>
                                        <?= htmlspecialchars($item['complaint_code']) ?>
                                    </strong>

                                    <div class="complaint-subtitle">
                                        <?= htmlspecialchars($item['title']) ?>
                                    </div>
                                </td>

                                <td>
                                    <?= htmlspecialchars($item['reporter_name']) ?>
                                </td>

                                <td>
                                    <strong>
                                        <?= htmlspecialchars($item['facility_name']) ?>
                                    </strong>

                                    <div class="complaint-subtitle">
                                        <?= htmlspecialchars($item['building_name']) ?>
                                        - <?= htmlspecialchars($item['room_name']) ?>
                                    </div>
                                </td>

                                <td>
                                    <?= htmlspecialchars($item['category_name']) ?>
                                </td>

                                <td>
                                    <?= date('d/m/Y', strtotime($item['created_at'])) ?>
                                </td>

                                <td>
                                    <span class="complaint-status <?= htmlspecialchars($item['status']) ?>">
                                        <?= htmlspecialchars(complaintStatus($item['status'])) ?>
                                    </span>
                                </td>

                                <td>
                                    <button
                                        type="button"
                                        class="complaint-action"
                                        onclick="showComplaint(<?= (int)$item['id'] ?>)"
                                    >
                                        <i class="bi bi-eye"></i>
                                        Detail
                                    </button>
                                </td>

                            </tr>

                        <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

                <div id="complaintEmpty" class="complaint-empty" hidden>
                    Pengaduan tidak ditemukan.
                </div>

            </div>

        </section>

    </main>

</div>


<!-- DETAIL MODAL -->


<div class="complaint-modal-overlay" id="complaintModal">
    <div class="complaint-modal">

        <!-- HEADER -->
        <div class="complaint-modal-header">
            <div>
                <div class="modal-title-row">
                    <span class="modal-title-icon">
                        <i class="bi bi-clipboard2-check"></i>
                    </span>
                    <h2>Detail Pengaduan</h2>
                </div>
                <p class="modal-code" id="detailCode"></p>
            </div>

            <button type="button" onclick="closeComplaint()" class="modal-close">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <!-- BODY -->
        <div class="complaint-modal-body">

            <!-- INFORMASI PENGADUAN -->
            <section class="modal-section">
                <div class="modal-section-heading">
                    <i class="bi bi-info-circle"></i>
                    <h3>Informasi Pengaduan</h3>
                </div>

                <div class="complaint-info-grid">
                    <div class="info-card">
                        <span>Judul Pengaduan</span>
                        <strong id="detailTitle">-</strong>
                    </div>

                    <div class="info-card">
                        <span>Nama Pelapor</span>
                        <strong id="detailReporter">-</strong>
                    </div>

                    <div class="info-card">
                        <span>Lokasi Fasilitas</span>
                        <strong id="detailFacility">-</strong>
                    </div>

                    <div class="info-card">
                        <span>Kategori Kerusakan</span>
                        <strong id="detailCategory">-</strong>
                    </div>
                </div>
            </section>

            <!-- DESKRIPSI -->
            <section class="modal-section">
                <div class="modal-section-heading">
                    <i class="bi bi-card-text"></i>
                    <h3>Deskripsi Kerusakan</h3>
                </div>

                <div class="description-card">
                    <p id="detailDescription">-</p>
                </div>
            </section>

            <!-- FOTO -->
            <section class="modal-section" id="detailPhotoContainer" hidden>
                <div class="modal-section-heading">
                    <i class="bi bi-image"></i>
                    <h3>Foto Kerusakan</h3>
                </div>

                <div class="photo-card">
                    <img id="detailPhoto" alt="Foto kerusakan fasilitas">
                </div>
            </section>

            <!-- CATATAN ADMIN -->
            <section class="modal-section" id="detailNoteContainer" hidden>
                <div class="modal-section-heading">
                    <i class="bi bi-chat-left-text"></i>
                    <h3>Catatan Admin</h3>
                </div>

                <div class="note-card admin-note-card">
                    <p id="detailNote">-</p>
                </div>
            </section>

            <!-- INFORMASI PENUGASAN -->
            <section
                id="detailAssignmentContainer"
                class="modal-section assignment-detail-section"
                hidden
            >
                <div class="modal-section-heading">
                    <i class="bi bi-person-gear"></i>
                    <h3>Informasi Penugasan</h3>
                </div>

                <div class="complaint-info-grid assignment-grid">

                    <div class="info-card">
                        <span>Nama Petugas</span>
                        <strong id="detailOfficer">-</strong>
                    </div>

                    <div class="info-card">
                        <span>Status Penugasan</span>
                        <strong id="detailAssignmentStatus" class="assignment-status-text">-</strong>
                    </div>

                    <div class="info-card">
                        <span>Waktu Penugasan</span>
                        <strong id="detailAssignedAt">-</strong>
                    </div>

                    <div class="info-card">
                        <span>Waktu Diterima</span>
                        <strong id="detailAcceptedAt">-</strong>
                    </div>

                    <div class="info-card">
                        <span>Waktu Diselesaikan</span>
                        <strong id="detailCompletedAt">-</strong>
                    </div>

                </div>

                <div class="note-card assignment-note-card">
                    <div>
                        <i class="bi bi-pencil-square"></i>
                        <span>Catatan Penugasan Admin</span>
                    </div>
                    <p id="detailAssignmentNotes">-</p>
                </div>

                <div class="note-card completion-note-card">
                    <div>
                        <i class="bi bi-check-circle"></i>
                        <span>Catatan Penyelesaian Petugas</span>
                    </div>
                    <p id="detailCompletionNotes">-</p>
                </div>
            </section>

        </div>

        <!-- FOOTER: AKSI VERIFIKASI -->
        <div class="complaint-modal-footer" id="complaintActions">

            <div class="modal-footer-heading">
                <h3>Tindak Lanjut Pengaduan</h3>
                <p>Periksa laporan sebelum menentukan tindakan.</p>
            </div>

            <form
                action="<?= BASE_URL ?>/admin/complaints/verify"
                method="POST"
                onsubmit="return confirm('Verifikasi pengaduan ini?')"
                class="modal-action-form"
            >
                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>"
                >

                <input type="hidden" name="id" id="verifyId">

                <input
                    type="text"
                    name="admin_note"
                    placeholder="Catatan admin (opsional)"
                >

                <button type="submit" class="btn-verify">
                    <i class="bi bi-check-lg"></i>
                    Verifikasi
                </button>
            </form>

            <form
                action="<?= BASE_URL ?>/admin/complaints/reject"
                method="POST"
                onsubmit="return confirm('Yakin ingin menolak pengaduan ini?')"
                class="modal-action-form"
            >
                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>"
                >

                <input type="hidden" name="id" id="rejectId">

                <input
                    type="text"
                    name="rejection_reason"
                    placeholder="Masukkan alasan penolakan..."
                    required
                >

                <button type="submit" class="btn-reject">
                    <i class="bi bi-x-lg"></i>
                    Tolak Pengaduan
                </button>
            </form>

        </div>

    </div>
</div>


<!-- INFORMASI PENUGASAN -->
<div id="detailAssignmentContainer" class="assignment-detail-section" hidden>

    <h3>
        <i class="bi bi-person-gear"></i>
        Informasi Penugasan
    </h3>

    <div class="assignment-detail-grid">

        <div class="assignment-detail-item">
            <span>Nama Petugas</span>
            <strong id="detailOfficer">-</strong>
        </div>

        <div class="assignment-detail-item">
            <span>Status Penugasan</span>
            <strong id="detailAssignmentStatus">-</strong>
        </div>

        <div class="assignment-detail-item">
            <span>Waktu Penugasan</span>
            <strong id="detailAssignedAt">-</strong>
        </div>

        <div class="assignment-detail-item">
            <span>Waktu Diterima</span>
            <strong id="detailAcceptedAt">-</strong>
        </div>

        <div class="assignment-detail-item">
            <span>Waktu Diselesaikan</span>
            <strong id="detailCompletedAt">-</strong>
        </div>

    </div>

    <div class="assignment-completion-note">
        <span>Catatan Penugasan Admin</span>
        <p id="detailAssignmentNotes">-</p>
    </div>

    <div class="assignment-completion-note">
        <span>Catatan Penyelesaian Petugas</span>
        <p id="detailCompletionNotes">-</p>
    </div>

</div>

        </div>

        <div class="complaint-modal-footer" id="complaintActions">

            <form
                action="<?= BASE_URL ?>/admin/complaints/verify"
                method="POST"
                onsubmit="return confirm('Verifikasi pengaduan ini?')"
            >

                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>"
                >

                <input type="hidden" name="id" id="verifyId">

                <input
                    type="text"
                    name="admin_note"
                    placeholder="Catatan admin (opsional)"
                >

                <button type="submit" class="btn-verify">
                    <i class="bi bi-check-lg"></i>
                    Verifikasi
                </button>

            </form>


            <form
                action="<?= BASE_URL ?>/admin/complaints/reject"
                method="POST"
                onsubmit="return confirm('Yakin ingin menolak pengaduan ini?')"
            >

                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>"
                >

                <input type="hidden" name="id" id="rejectId">

                <input
                    type="text"
                    name="rejection_reason"
                    placeholder="Alasan penolakan..."
                    required
                >

                <button type="submit" class="btn-reject">
                    <i class="bi bi-x-lg"></i>
                    Tolak
                </button>

            </form>

        </div>

    </div>

</div>


<script>
const complaintData = <?= json_encode(
    $complaints,
    JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP
) ?>;

const complaintBaseUrl = <?= json_encode(BASE_URL) ?>;
</script>

<script src="<?= BASE_URL ?>/assets/js/complaints.js"></script>

</body>
</html>
