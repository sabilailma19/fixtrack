
<?php

$user = $data['user'];
$assignments = $data['assignments'] ?? [];

$total = count($assignments);

$ditugaskan = 0;
$diterima = 0;
$diproses = 0;
$selesai = 0;

foreach ($assignments as $item) {
    switch ($item['status']) {
        case 'ditugaskan':
            $ditugaskan++;
            break;

        case 'diterima':
            $diterima++;
            break;

        case 'diproses':
            $diproses++;
            break;

        case 'selesai':
            $selesai++;
            break;
    }
}

function statusLabel($status)
{
    $labels = [
        'ditugaskan' => 'Menunggu Diterima',
        'diterima' => 'Diterima',
        'diproses' => 'Sedang Diproses',
        'selesai' => 'Selesai'
    ];

    return $labels[$status] ?? ucfirst($status);
}

function e($value)
{
    return htmlspecialchars((string) ($value ?? '-'), ENT_QUOTES, 'UTF-8');
}

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard Petugas | FixTrack</title>

    <link rel="stylesheet"
          href="<?= BASE_URL ?>/assets/css/petugas.css">
</head>

<body>

<div class="dashboard">

    <!-- SIDEBAR -->
    <aside class="sidebar">

        <div class="brand">
            FixTrack
        </div>

        <div class="profile">

            <div class="avatar">
                <?= strtoupper(substr($user['name'] ?? 'P', 0, 1)) ?>
            </div>

            <div>
                <strong><?= e($user['name'] ?? 'Petugas') ?></strong>
                <small>Petugas</small>
            </div>

        </div>

        <nav>
            <a href="<?= BASE_URL ?>/petugas/dashboard"
               class="active">
                Dashboard
            </a>
        </nav>

        <a href="<?= BASE_URL ?>/logout" class="logout">
            Keluar
        </a>

    </aside>


    <!-- MAIN CONTENT -->
    <main class="main-content">

        <header class="topbar">

            <div>
                <h1>Dashboard Petugas</h1>
                <p>Kelola dan pantau tugas pemeliharaan fasilitas.</p>
            </div>

        </header>


        <!-- NOTIFIKASI -->
        <?php if (!empty($_SESSION['success'])): ?>

            <div class="alert success">
                <?= e($_SESSION['success']) ?>
            </div>

            <?php unset($_SESSION['success']); ?>

        <?php endif; ?>


        <?php if (!empty($_SESSION['error'])): ?>

            <div class="alert error">
                <?= e($_SESSION['error']) ?>
            </div>

            <?php unset($_SESSION['error']); ?>

        <?php endif; ?>


        <!-- STATISTIK -->
        <section class="stats">

            <div class="stat-card">
                <span>Total Tugas</span>
                <h2><?= $total ?></h2>
            </div>

            <div class="stat-card">
                <span>Menunggu Diterima</span>
                <h2><?= $ditugaskan ?></h2>
            </div>

            <div class="stat-card">
                <span>Sedang Diproses</span>
                <h2><?= $diproses ?></h2>
            </div>

            <div class="stat-card">
                <span>Selesai</span>
                <h2><?= $selesai ?></h2>
            </div>

        </section>


        <!-- DAFTAR PENUGASAN -->
        <section class="task-section">

            <div class="section-heading">

                <div>
                    <h2>Daftar Penugasan</h2>
                    <p>Daftar pengaduan yang ditugaskan kepada kamu.</p>
                </div>

            </div>


            <?php if (empty($assignments)): ?>

                <div class="empty-state">
                    <h3>Belum Ada Penugasan</h3>
                    <p>Penugasan dari admin akan muncul di halaman ini.</p>
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

                            <tr>

                                <td>
                                    <strong>
                                        <?= e($item['complaint_code']) ?>
                                    </strong>
                                </td>


                                <td>
                                    <?= e($item['title']) ?>
                                </td>


                                <td>
                                    <?= e(
                                        ($item['building_name'] ?? '-') .
                                        ' - ' .
                                        ($item['room_name'] ?? '-')
                                    ) ?>
                                </td>


                                <td>
                                    <?= !empty($item['assigned_at'])
                                        ? date('d/m/Y', strtotime($item['assigned_at']))
                                        : '-' ?>
                                </td>


                                <td>
                                    <span class="status <?= e($item['status']) ?>">
                                        <?= e(statusLabel($item['status'])) ?>
                                    </span>
                                </td>


                                <!-- AKSI -->
                                <td>

                                    <div class="actions">

                                        <!-- TOMBOL DETAIL -->
                                        <button
                                            type="button"
                                            class="btn detail"
                                            onclick="showComplaintDetail(this)"

                                            data-code="<?= e($item['complaint_code']) ?>"
                                            data-title="<?= e($item['title']) ?>"
                                            data-description="<?= e($item['description'] ?? '') ?>"
                                            data-category="<?= e($item['category_name'] ?? '') ?>"
                                            data-building="<?= e($item['building_name'] ?? '') ?>"
                                            data-room="<?= e($item['room_name'] ?? '') ?>"
                                            data-facility="<?= e($item['facility_name'] ?? '') ?>"
                                            data-reporter="<?= e($item['reporter_name'] ?? '') ?>"
                                            data-status="<?= e($item['complaint_status'] ?? '') ?>"
                                            data-notes="<?= e($item['notes'] ?? '') ?>"
                                            data-completion-notes="<?= e($item['completion_notes'] ?? '') ?>"
                                            data-admin-note="<?= e($item['admin_note'] ?? '') ?>"
                                            data-photo="<?= e($item['photo'] ?? '') ?>"
                                        >
                                            Detail
                                        </button>


                                        <!-- STATUS DITUGASKAN -->
                                        <?php if ($item['status'] === 'ditugaskan'): ?>

                                            <form method="POST"
                                                  action="<?= BASE_URL ?>/petugas/assignments/accept">

                                                <input
                                                    type="hidden"
                                                    name="csrf_token"
                                                    value="<?= e($_SESSION['csrf_token'] ?? '') ?>"
                                                >

                                                <input
                                                    type="hidden"
                                                    name="id"
                                                    value="<?= (int) $item['id'] ?>"
                                                >

                                                <button type="submit" class="btn accept">
                                                    Terima
                                                </button>

                                            </form>


                                        <!-- STATUS DITERIMA -->
                                        <?php elseif ($item['status'] === 'diterima'): ?>

                                            <form method="POST"
                                                  action="<?= BASE_URL ?>/petugas/assignments/start">

                                                <input
                                                    type="hidden"
                                                    name="csrf_token"
                                                    value="<?= e($_SESSION['csrf_token'] ?? '') ?>"
                                                >

                                                <input
                                                    type="hidden"
                                                    name="id"
                                                    value="<?= (int) $item['id'] ?>"
                                                >

                                                <button type="submit" class="btn process">
                                                    Mulai Kerjakan
                                                </button>

                                            </form>


                                        <!-- STATUS DIPROSES -->
                                        <?php elseif ($item['status'] === 'diproses'): ?>

                                            <button
                                                type="button"
                                                class="btn complete"
                                                onclick="openCompletionModal(<?= (int) $item['id'] ?>)"
                                            >
                                                Selesaikan
                                            </button>


                                        <!-- STATUS SELESAI -->
                                        <?php else: ?>

                                            <span class="done">
                                                Tuntas
                                            </span>

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

    </main>

</div>


<!-- ====================================== -->
<!-- MODAL DETAIL PENGADUAN -->
<!-- ====================================== -->

<div id="detailModal" class="modal">

    <div class="modal-content">

        <div class="modal-header">

            <div>
                <h2>Detail Pengaduan</h2>
                <p>Informasi lengkap laporan kerusakan fasilitas.</p>
            </div>

            <button
                type="button"
                class="close-modal"
                onclick="closeComplaintDetail()"
            >
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
                <span>Catatan Penyelesaian Petugas</span>
                <p id="detailCompletionNotes">-</p>
            </div>


            <div class="detail-item full">
                <span>Foto Kerusakan</span>

                <img
                    id="detailPhoto"
                    src=""
                    alt="Foto kerusakan"
                    style="display:none; max-width:100%; max-height:300px; border-radius:10px;"
                >

                <p id="noPhoto">
                    Tidak ada foto yang dilampirkan.
                </p>

            </div>

        </div>

    </div>

</div>


<!-- ====================================== -->
<!-- MODAL PENYELESAIAN TUGAS -->
<!-- ====================================== -->

<div id="completionModal" class="modal">

    <div class="modal-content"
         style="width:100%; max-width:650px; padding:28px;">

        <div class="modal-header">

            <div>
                <h2>Catatan Penyelesaian</h2>
                <p>Jelaskan tindakan perbaikan yang telah dilakukan.</p>
            </div>

            <button
                type="button"
                class="close-modal"
                onclick="closeCompletionModal()"
            >
                &times;
            </button>

        </div>


        <form
            method="POST"
            action="<?= BASE_URL ?>/petugas/assignments/complete"
            style="display:block; width:100%;"
        >

            <input
                type="hidden"
                name="csrf_token"
                value="<?= e($_SESSION['csrf_token'] ?? '') ?>"
            >

            <input
                type="hidden"
                name="id"
                id="completionAssignmentId"
            >


            <div style="
                display:flex;
                flex-direction:column;
                align-items:stretch;
                gap:10px;
                width:100%;
                margin-top:25px;
            ">

                <label
                    for="completionNotes"
                    style="
                        display:block;
                        font-size:14px;
                        font-weight:600;
                        color:#334155;
                    "
                >
                    Tindakan Perbaikan
                </label>


                <textarea
                    name="completion_notes"
                    id="completionNotes"
                    rows="5"
                    minlength="5"
                    maxlength="2000"
                    placeholder="Contoh: Memeriksa kabel power, memperbaiki konektor, dan menguji kembali proyektor."
                    required
                    style="
                        display:block;
                        width:100%;
                        min-height:130px;
                        padding:13px;
                        border:1px solid #cbd5e1;
                        border-radius:8px;
                        font-family:Arial,sans-serif;
                        font-size:14px;
                        line-height:1.6;
                        resize:vertical;
                        box-sizing:border-box;
                    "
                ></textarea>


                <small style="
                    display:block;
                    color:#64748b;
                    font-size:12px;
                ">
                    Tuliskan tindakan yang dilakukan dan hasil perbaikannya.
                </small>

            </div>


            <div style="
                display:flex;
                justify-content:flex-end;
                gap:10px;
                margin-top:24px;
            ">

                <button
                    type="button"
                    class="btn cancel"
                    onclick="closeCompletionModal()"
                >
                    Batal
                </button>


                <button type="submit" class="btn complete">
                    Kirim Penyelesaian
                </button>

            </div>

        </form>

    </div>

</div>


<!-- JAVASCRIPT -->
<script>
    window.FIXTRACK_BASE_URL = <?= json_encode(BASE_URL) ?>;
</script>

<script src="<?= BASE_URL ?>/assets/js/petugas.js"></script>

</body>
</html>
