<?php

$user = $data['user'] ?? $_SESSION['user'] ?? [];
$assignments = $data['assignments'] ?? [];

$escape = static function ($value) {
    return htmlspecialchars(
        (string) ($value ?? ''),
        ENT_QUOTES,
        'UTF-8'
    );
};

$userName = $user['name'] ?? 'Petugas';
$initial = mb_substr($userName, 0, 1);

$statusLabels = [
    'menunggu_verifikasi' => 'Menunggu Verifikasi',
    'diverifikasi' => 'Diverifikasi',
    'ditugaskan' => 'Menunggu Diterima',
    'diterima' => 'Siap Dikerjakan',
    'diproses' => 'Sedang Diproses',
    'selesai' => 'Selesai',
    'ditolak' => 'Ditolak',
    'dibatalkan' => 'Dibatalkan'
];

$success = $_SESSION['success'] ?? null;
$error = $_SESSION['error'] ?? null;

unset($_SESSION['success'], $_SESSION['error']);

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1">

    <title>Penugasan Saya | FixTrack</title>

    <!-- Urutan CSS dipertahankan agar layout mengikuti dashboard -->
    <link rel="stylesheet"
          href="<?= BASE_URL ?>/assets/css/petugas.css">

    <link rel="stylesheet"
          href="<?= BASE_URL ?>/assets/css/admin.css">

    <link rel="stylesheet"
          href="<?= BASE_URL ?>/assets/css/dashboard-role.css">

    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body class="role-dashboard">

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

        <div class="sidebar-label">MENU PETUGAS</div>

        <nav class="sidebar-menu" aria-label="Menu Petugas">

            <a href="<?= BASE_URL ?>/petugas/dashboard"
               class="menu-item"
               aria-label="Dashboard">

                <i class="bi bi-grid" aria-hidden="true"></i>
                <span>Dashboard</span>
            </a>

            <a href="<?= BASE_URL ?>/petugas/assignments"
               class="menu-item active"
               aria-label="Penugasan Saya"
               aria-current="page">

                <i class="bi bi-clipboard-check" aria-hidden="true"></i>
                <span>Penugasan Saya</span>
            </a>

        </nav>

        <div class="sidebar-bottom">

            <div class="admin-profile">
                <div class="avatar">
                    <?= $escape($initial) ?>
                </div>

                <div class="profile-info">
                    <strong><?= $escape($userName) ?></strong>
                    <span>Petugas</span>
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
                    Pages / <strong>Penugasan Saya</strong>
                </div>

                <h1>Penugasan Saya</h1>
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

            <?php if ($success): ?>
                <div class="role-alert success" role="status">
                    <?= $escape($success) ?>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="role-alert error" role="alert">
                    <?= $escape($error) ?>
                </div>
            <?php endif; ?>

            <section class="task-section content-card recent-card">

                <div class="card-heading">

                    <div>
                        <h3>Daftar Penugasan</h3>
                        <p>
                            Kelola <?= count($assignments) ?> tugas
                            yang diberikan kepadamu.
                        </p>
                    </div>

                    <i class="bi bi-clipboard-check"></i>

                </div>

                <?php if (empty($assignments)): ?>

                    <div class="empty-table">
                        <i class="bi bi-inbox"></i>

                        <strong>Belum Ada Penugasan</strong>

                        <span>
                            Penugasan dari admin akan muncul di halaman ini.
                        </span>
                    </div>

                <?php else: ?>

                    <div class="table-wrapper">

                        <table>
                            <thead>
                                <tr>
                                    <th>Kode</th>
                                    <th>Pengaduan</th>
                                    <th>Lokasi</th>
                                    <th>Tanggal</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>

                            <tbody>

                            <?php foreach ($assignments as $item): ?>

                                <?php
                                $assignmentId = (int) $item['id'];

                                $status = $item['status'] ?? '';

                                $complaintStatus =
                                    $item['complaint_status'] ?? $status;

                                $assignedTimestamp = !empty($item['assigned_at'])
                                    ? strtotime($item['assigned_at'])
                                    : false;

                                $assignedDate = $assignedTimestamp !== false
                                    ? date('d/m/Y', $assignedTimestamp)
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
                                        <?= $escape(
                                            ($item['building_name'] ?? '-')
                                            . ' - '
                                            . ($item['room_name'] ?? '-')
                                        ) ?>
                                    </td>

                                    <td><?= $escape($assignedDate) ?></td>

                                    <td>
                                        <span class="status <?= $escape($status) ?>">
                                            <?= $escape(
                                                $statusLabels[$status] ?? $status
                                            ) ?>
                                        </span>
                                    </td>

                                    <td>

                                        <div class="actions">

                                            <!-- DETAIL -->
                                            <button
                                                type="button"
                                                class="btn detail"
                                                data-open-detail
                                                data-code="<?= $escape($item['complaint_code'] ?? '-') ?>"
                                                data-title="<?= $escape($item['title'] ?? '-') ?>"
                                                data-description="<?= $escape($item['description'] ?? '-') ?>"
                                                data-category="<?= $escape($item['category_name'] ?? '-') ?>"
                                                data-building="<?= $escape($item['building_name'] ?? '-') ?>"
                                                data-room="<?= $escape($item['room_name'] ?? '-') ?>"
                                                data-facility="<?= $escape($item['facility_name'] ?? '-') ?>"
                                                data-reporter="<?= $escape($item['reporter_name'] ?? '-') ?>"
                                                data-status="<?= $escape($statusLabels[$complaintStatus] ?? $complaintStatus) ?>"
                                                data-assignment-status="<?= $escape($statusLabels[$status] ?? $status) ?>"
                                                data-notes="<?= $escape($item['notes'] ?? '') ?>"
                                                data-admin-note="<?= $escape($item['admin_note'] ?? '') ?>"
                                                data-completion-notes="<?= $escape($item['completion_notes'] ?? '') ?>"
                                                data-photo="<?= $escape($item['photo'] ?? '') ?>"
                                            >
                                                Detail
                                            </button>

                                            <?php if ($status === 'ditugaskan'): ?>

                                                <!-- TERIMA TUGAS -->
                                                <form
                                                    method="POST"
                                                    action="<?= BASE_URL ?>/petugas/assignments/accept">

                                                    <input
                                                        type="hidden"
                                                        name="csrf_token"
                                                        value="<?= $escape($_SESSION['csrf_token'] ?? '') ?>">

                                                    <input
                                                        type="hidden"
                                                        name="id"
                                                        value="<?= $assignmentId ?>">

                                                    <button
                                                        type="submit"
                                                        class="btn accept">
                                                        Terima
                                                    </button>

                                                </form>

                                            <?php elseif ($status === 'diterima'): ?>

                                                <!-- MULAI PENGERJAAN -->
                                                <form
                                                    method="POST"
                                                    action="<?= BASE_URL ?>/petugas/assignments/start">

                                                    <input
                                                        type="hidden"
                                                        name="csrf_token"
                                                        value="<?= $escape($_SESSION['csrf_token'] ?? '') ?>">

                                                    <input
                                                        type="hidden"
                                                        name="id"
                                                        value="<?= $assignmentId ?>">

                                                    <button
                                                        type="submit"
                                                        class="btn process">
                                                        Mulai Kerjakan
                                                    </button>

                                                </form>

                                            <?php elseif ($status === 'diproses'): ?>

                                                <!-- SELESAIKAN -->
                                                <button
                                                    type="button"
                                                    class="btn complete"
                                                    data-complete-id="<?= $assignmentId ?>">
                                                    Selesaikan
                                                </button>

                                            <?php elseif ($status === 'selesai'): ?>

                                                <span class="done">Tuntas</span>

                                            <?php endif; ?>

                                        </div>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                            </tbody>
                        </table>

                    </div>

                <?php endif; ?>

            </section>

            <footer class="dashboard-footer">
                &copy; <?= date('Y') ?> FixTrack.
            </footer>

        </section>

    </main>

</div>

<!-- MODAL DETAIL -->
<div id="detailModal"
     class="modal"
     role="dialog"
     aria-modal="true"
     aria-labelledby="detailModalTitle"
     aria-hidden="true">

    <div class="modal-content">

        <div class="modal-header">

            <div>
                <h2 id="detailModalTitle">Detail Pengaduan</h2>
                <p>Informasi laporan dan penanganan fasilitas.</p>
            </div>

            <button
                type="button"
                class="close-modal"
                data-close-modal="detailModal"
                aria-label="Tutup detail">
                &times;
            </button>

        </div>

        <div class="detail-body">

            <div class="detail-item">
                <span>Kode Pengaduan</span>
                <strong id="detailCode">-</strong>
            </div>

            <div class="detail-item">
                <span>Judul Pengaduan</span>
                <strong id="detailTitle">-</strong>
            </div>

            <div class="detail-item">
                <span>Kategori</span>
                <strong id="detailCategory">-</strong>
            </div>

            <div class="detail-item">
                <span>Nama Pelapor</span>
                <strong id="detailReporter">-</strong>
            </div>

            <div class="detail-item">
                <span>Lokasi</span>
                <strong id="detailLocation">-</strong>
            </div>

            <div class="detail-item">
                <span>Fasilitas</span>
                <strong id="detailFacility">-</strong>
            </div>

            <div class="detail-item">
                <span>Status Pengaduan</span>
                <strong id="detailStatus">-</strong>
            </div>

            <div class="detail-item">
                <span>Status Penugasan</span>
                <strong id="detailAssignmentStatus">-</strong>
            </div>

            <div class="detail-item full">
                <span>Deskripsi Kerusakan</span>
                <p id="detailDescription">-</p>
            </div>

            <div class="detail-item full">
                <span>Catatan Penugasan Admin</span>
                <p id="detailNotes">-</p>
            </div>

            <div class="detail-item full">
                <span>Catatan Admin</span>
                <p id="detailAdminNote">-</p>
            </div>

            <div class="detail-item full">
                <span>Catatan Penyelesaian</span>
                <p id="detailCompletionNotes">-</p>
            </div>

            <div class="detail-item full">
                <span>Foto Kerusakan</span>

                <img
                    id="detailPhoto"
                    alt="Foto kerusakan fasilitas"
                    style="display: none;">

                <p id="noPhoto">Tidak ada foto yang dilampirkan.</p>
            </div>

        </div>

    </div>

</div>

<!-- MODAL PENYELESAIAN -->
<div id="completionModal"
     class="modal"
     role="dialog"
     aria-modal="true"
     aria-labelledby="completionModalTitle"
     aria-hidden="true">

    <div class="modal-content completion-content">

        <div class="modal-header">

            <div>
                <h2 id="completionModalTitle">Selesaikan Pekerjaan</h2>
                <p>Jelaskan tindakan dan hasil perbaikan.</p>
            </div>

            <button
                type="button"
                class="close-modal"
                data-close-modal="completionModal"
                aria-label="Tutup formulir penyelesaian">
                &times;
            </button>

        </div>

        <form
            method="POST"
            action="<?= BASE_URL ?>/petugas/assignments/complete">

            <input
                type="hidden"
                name="csrf_token"
                value="<?= $escape($_SESSION['csrf_token'] ?? '') ?>">

            <input
                type="hidden"
                name="id"
                id="completionAssignmentId">

            <div class="form-group">

                <label for="completionNotes">
                    Catatan Penyelesaian
                </label>

                <textarea
                    name="completion_notes"
                    id="completionNotes"
                    rows="5"
                    minlength="5"
                    maxlength="2000"
                    required
                    placeholder="Contoh: Mengganti lampu yang rusak dan menguji kembali pencahayaan ruangan."></textarea>

                <small>
                    Tuliskan tindakan yang dilakukan dan hasil perbaikannya.
                </small>

            </div>

            <div class="modal-actions">

                <button
                    type="button"
                    class="btn cancel"
                    data-close-modal="completionModal">
                    Batal
                </button>

                <button type="submit" class="btn complete">
                    Kirim Penyelesaian
                </button>

            </div>

        </form>

    </div>

</div>

<script>
(() => {
    'use strict';

    const baseUrl = <?= json_encode(
        BASE_URL,
        JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
    ) ?>;

    let activeModal = null;
    let previousFocus = null;
    let previousOverflow = '';

    function openModal(id) {
        const modal = document.getElementById(id);

        if (!modal) return;

        previousFocus = document.activeElement;
        previousOverflow = document.body.style.overflow;
        activeModal = modal;

        modal.classList.add('show');
        modal.setAttribute('aria-hidden', 'false');

        document.body.style.overflow = 'hidden';

        const firstControl = modal.querySelector(
            'button, textarea, input:not([type="hidden"])'
        );

        if (firstControl) firstControl.focus();
    }

    function closeModal() {
        if (!activeModal) return;

        activeModal.classList.remove('show');
        activeModal.setAttribute('aria-hidden', 'true');

        document.body.style.overflow = previousOverflow;
        activeModal = null;

        if (previousFocus) previousFocus.focus();
    }

    function setText(id, value) {
        const element = document.getElementById(id);

        if (element) {
            element.textContent = value || '-';
        }
    }

    function showDetail(button) {
        const values = button.dataset;

        const fields = {
            detailCode: values.code,
            detailTitle: values.title,
            detailCategory: values.category,
            detailReporter: values.reporter,
            detailFacility: values.facility,
            detailStatus: values.status,
            detailAssignmentStatus: values.assignmentStatus,
            detailDescription: values.description,
            detailNotes: values.notes,
            detailAdminNote: values.adminNote,
            detailCompletionNotes: values.completionNotes,
            detailLocation:
                (values.building || '-') + ' - ' + (values.room || '-')
        };

        Object.entries(fields).forEach(([id, value]) => {
            setText(id, value);
        });

        const image = document.getElementById('detailPhoto');
        const message = document.getElementById('noPhoto');

        image.onload = null;
        image.onerror = null;
        image.style.display = 'none';
        image.removeAttribute('src');

        message.style.display = 'block';
        message.textContent = 'Tidak ada foto yang dilampirkan.';

        const photoPath = values.photo || '';

        if (photoPath) {
            message.textContent = 'Memuat foto...';

            image.onload = () => {
                image.style.display = 'block';
                message.style.display = 'none';
            };

            image.onerror = () => {
                image.style.display = 'none';
                message.style.display = 'block';
                message.textContent = 'Foto gagal dimuat.';
            };

            try {
                const photoUrl = /^https?:\/\//i.test(photoPath)
                    ? new URL(photoPath)
                    : new URL(
                        baseUrl + '/' + photoPath.replace(/^\/+/, ''),
                        window.location.origin
                    );

                if (!['http:', 'https:'].includes(photoUrl.protocol)) {
                    throw new Error('Alamat foto tidak valid.');
                }

                image.src = photoUrl.href;
            } catch (error) {
                message.textContent = 'Alamat foto tidak valid.';
            }
        }

        openModal('detailModal');
    }

    document.querySelectorAll('[data-open-detail]').forEach(button => {
        button.addEventListener('click', () => showDetail(button));
    });

    document.querySelectorAll('[data-complete-id]').forEach(button => {
        button.addEventListener('click', () => {
            document.getElementById('completionAssignmentId').value =
                button.dataset.completeId;

            document.getElementById('completionNotes').value = '';

            openModal('completionModal');
            document.getElementById('completionNotes').focus();
        });
    });

    document.querySelectorAll('[data-close-modal]').forEach(button => {
        button.addEventListener('click', closeModal);
    });

    document.querySelectorAll('.modal').forEach(modal => {
        modal.addEventListener('click', event => {
            if (event.target === modal) closeModal();
        });
    });

    document.addEventListener('keydown', event => {
        if (!activeModal) return;

        if (event.key === 'Escape') {
            closeModal();
            return;
        }

        if (event.key !== 'Tab') return;

        const controls = Array.from(activeModal.querySelectorAll(
            'button:not([disabled]), textarea:not([disabled]), ' +
            'input:not([type="hidden"]):not([disabled]), a[href]'
        )).filter(element => element.getClientRects().length > 0);

        if (controls.length === 0) return;

        const first = controls[0];
        const last = controls[controls.length - 1];

        if (event.shiftKey && document.activeElement === first) {
            event.preventDefault();
            last.focus();
        } else if (!event.shiftKey && document.activeElement === last) {
            event.preventDefault();
            first.focus();
        }
    });
})();
</script>

</body>
</html>