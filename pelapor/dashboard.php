<?php

$roleLabel = 'Pelapor';

$welcomeTitle = 'Bantu Jaga Fasilitas Kampus';

$welcomeText =
    'Laporkan kerusakan dan pantau perkembangan pengaduanmu dalam satu tempat.';

$bannerIcon = 'bi-buildings';

$actionUrl = BASE_URL . '/pelapor/complaints/create';
$actionLabel = 'Buat Pengaduan';

$summaryTitle = 'Ringkasan Pengaduan Saya';

$menus = [
    [
        'url' => BASE_URL . '/pelapor/dashboard',
        'icon' => 'bi-grid',
        'label' => 'Dashboard',
        'active' => true
    ],
    [
        'url' => BASE_URL . '/pelapor/complaints',
        'icon' => 'bi-clipboard-check',
        'label' => 'Riwayat Pengaduan',
        'active' => false
    ],
    [
        'url' => $actionUrl,
        'icon' => 'bi-plus-circle',
        'label' => 'Buat Pengaduan',
        'active' => false
    ]
];

$complaints = $complaints ?? [];
$total = count($complaints);
$confirmationCounts = CompletionStatus::counts($complaints);

$statusLabels = [
    'menunggu_verifikasi' => 'Menunggu Verifikasi',
    'diverifikasi' => 'Diverifikasi',
    'ditugaskan' => 'Ditugaskan',
    'diproses' => 'Sedang Diproses',
    'selesai' => 'Selesai dikerjakan',
    'ditolak' => 'Ditolak'
];

$statusCounts = array_fill_keys(array_keys($statusLabels), 0);

foreach ($complaints as $item) {
    $status = $item['status'];
    $statusCounts[$status] = ($statusCounts[$status] ?? 0) + 1;
}

$cards = [
    [
        'label' => 'Total Pengaduan',
        'value' => $total,
        'hint' => 'Seluruh laporan yang kamu kirim',
        'color' => 'blue',
        'icon' => 'bi-clipboard-data'
    ],
    [
        'label' => 'Menunggu Verifikasi',
        'value' => $statusCounts['menunggu_verifikasi'],
        'hint' => 'Menunggu pemeriksaan admin',
        'color' => 'orange',
        'icon' => 'bi-clock-history'
    ],
    [
        'label' => 'Sedang Diproses',
        'value' => $statusCounts['diproses'],
        'hint' => 'Sedang ditangani petugas',
        'color' => 'purple',
        'icon' => 'bi-tools'
    ],
    [
        'label' => 'Dikonfirmasi Selesai',
'value' => $confirmationCounts['confirmed'],
'hint' => 'Hasil perbaikan yang sudah kamu setujui',
        'color' => 'green',
        'icon' => 'bi-check-circle'
    ]
];

$cards[] = [
    'label' => 'Menunggu Konfirmasimu',
    'value' => $confirmationCounts['pending'],
    'hint' => 'Buka detail pengaduan untuk memeriksa hasil',
    'color' => 'orange',
    'icon' => 'bi-hourglass-split'
];

$cards[] = [
    'label' => 'Perbaikan Ulang',
    'value' => $confirmationCounts['needs_review'],
    'hint' => 'Menunggu penugasan kembali oleh admin',
    'color' => 'purple',
    'icon' => 'bi-arrow-repeat'
];

$guidance =
    'Temukan fasilitas rusak? Buat pengaduan dengan lokasi, deskripsi, '
    . 'dan foto yang jelas. Pantau status serta catatan admin melalui '
    . 'Riwayat Pengaduan.';

require __DIR__ . '/../shared/dashboard-header.php';

?>

<div class="content-card recent-card">

    <div class="card-heading">

        <div>
            <h3>Pengaduan Terbaru</h3>
            <p>Lima laporan terakhirmu</p>
        </div>

        <a href="<?= BASE_URL ?>/pelapor/complaints"
           class="view-all">

            Lihat Semua
            <i class="bi bi-arrow-right"></i>
        </a>

    </div>

    <?php if (empty($complaints)): ?>

        <div class="empty-table">

            <i class="bi bi-inbox"></i>

            <strong>Belum Ada Pengaduan</strong>

            <span>
                Laporkan kerusakan fasilitas agar dapat ditindaklanjuti.
            </span>

            <a href="<?= $escape($actionUrl) ?>"
               class="primary-link">

                Buat Pengaduan Pertama
            </a>

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
                    </tr>
                </thead>

                <tbody>

                    <?php foreach (array_slice($complaints, 0, 5) as $item): ?>

                        <tr>

                            <td>
                                <strong>
                                    <?= $escape($item['complaint_code']) ?>
                                </strong>
                            </td>

                            <td>
                                <?= $escape($item['title']) ?>
                            </td>

                            <td>
                                <?= $escape(
                                    ($item['building_name'] ?? '-')
                                    . ' - '
                                    . ($item['room_name'] ?? '-')
                                ) ?>
                            </td>

                            <td>
                                <?= $escape(date(
                                    'd/m/Y',
                                    strtotime($item['created_at'])
                                )) ?>
                            </td>

                            <td>
    <span class="status <?= $escape($item['status']) ?>">
        <?= $escape(
            $statusLabels[$item['status']] ?? $item['status']
        ) ?>
    </span>

    <?php if ($confirmationText = CompletionStatus::label($item)): ?>
        <div style="margin-top: 6px; font-size: 12px; color: #64748b;">
            <?= $escape($confirmationText) ?>
        </div>
    <?php endif; ?>
</td>

                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    <?php endif; ?>

</div>

<footer class="dashboard-footer">
    &copy; <?= date('Y') ?> FixTrack.
</footer>

        </section>
    </main>
</div>

</body>
</html>