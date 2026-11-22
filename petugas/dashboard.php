<?php

$user = $data['user'] ?? $_SESSION['user'] ?? [];
$assignments = $data['assignments'] ?? [];

/*
|--------------------------------------------------------------------------
| Konfigurasi halaman
|--------------------------------------------------------------------------
*/

$roleLabel = 'Petugas';
$pageTitle = 'Dashboard Petugas';
$showOverview = true;

$welcomeTitle = 'Siap Merawat Fasilitas Kampus?';

$welcomeText =
    'Pantau penugasanmu, mulai perbaikan, dan catat hasil pekerjaan.';

$bannerIcon = 'bi-tools';

$actionUrl = BASE_URL . '/petugas/assignments';
$actionLabel = 'Lihat Penugasan';

$summaryTitle = 'Ringkasan Penugasan';

/*
|--------------------------------------------------------------------------
| Menu sidebar
|--------------------------------------------------------------------------
*/

$menus = [
    [
        'url' => BASE_URL . '/petugas/dashboard',
        'icon' => 'bi-grid',
        'label' => 'Dashboard',
        'active' => true
    ],
    [
        'url' => BASE_URL . '/petugas/assignments',
        'icon' => 'bi-clipboard-check',
        'label' => 'Penugasan Saya',
        'active' => false
    ]
];

/*
|--------------------------------------------------------------------------
| Hitung status tugas milik petugas yang login
|--------------------------------------------------------------------------
*/

$statusLabels = [
    'ditugaskan' => 'Menunggu Diterima',
    'diterima' => 'Siap Dikerjakan',
    'diproses' => 'Sedang Diproses',
    'selesai' => 'Selesai',
    'dibatalkan' => 'Dibatalkan'
];

$statusCounts = array_fill_keys(
    array_keys($statusLabels),
    0
);

foreach ($assignments as $assignment) {
    $status = $assignment['status'] ?? 'lainnya';

    if (!isset($statusCounts[$status])) {
        $statusCounts[$status] = 0;
        $statusLabels[$status] = 'Status Lainnya';
    }

    $statusCounts[$status]++;
}

$total = count($assignments);
$confirmationCounts = CompletionStatus::counts($assignments);

$menunggu = $statusCounts['ditugaskan'];
$diterima = $statusCounts['diterima'];
$diproses = $statusCounts['diproses'];
$selesai = $statusCounts['selesai'];

/*
|--------------------------------------------------------------------------
| Kartu statistik
|--------------------------------------------------------------------------
*/

$cards = [
    [
        'label' => 'Total Tugas',
        'value' => $total,
        'hint' => 'Seluruh penugasan untukmu',
        'color' => 'blue',
        'icon' => 'bi-clipboard-data'
    ],
    [
        'label' => 'Menunggu Diterima',
        'value' => $menunggu,
        'hint' => 'Penugasan baru dari admin',
        'color' => 'orange',
        'icon' => 'bi-clock-history'
    ],
    [
        'label' => 'Sedang Diproses',
        'value' => $diproses,
        'hint' => $diterima . ' tugas diterima, belum dimulai',
        'color' => 'purple',
        'icon' => 'bi-tools'
    ],
    [
        'label' => 'Selesai Dikerjakan',
        'value' => $selesai,
        'hint' => 'Termasuk pekerjaan yang belum dikonfirmasi pelapor',
        'color' => 'green',
        'icon' => 'bi-check-circle'
    ]
];

$cards[] = [
    'label' => 'Menunggu Konfirmasi',
    'value' => $confirmationCounts['pending'],
    'hint' => 'Hasil pekerjaan menunggu pemeriksaan pelapor',
    'color' => 'orange',
    'icon' => 'bi-hourglass-split'
];

$cards[] = [
    'label' => 'Dikonfirmasi Selesai',
    'value' => $confirmationCounts['confirmed'],
    'hint' => 'Hasil pekerjaan sudah disetujui pelapor',
    'color' => 'green',
    'icon' => 'bi-check-circle'
];

$cards[] = [
    'label' => 'Riwayat Perbaikan Ulang',
    'value' => $confirmationCounts['needs_review'],
    'hint' => 'Penugasanmu yang pernah diminta diperbaiki ulang',
    'color' => 'purple',
    'icon' => 'bi-arrow-repeat'
];

/*
|--------------------------------------------------------------------------
| Informasi langkah berikutnya
|--------------------------------------------------------------------------
*/

if ($menunggu > 0) {
    $guidance =
        'Ada ' . $menunggu . ' tugas baru yang menunggu diterima. '
        . 'Buka Penugasan Saya, periksa detail pengaduan, '
        . 'lalu terima penugasannya.';
} elseif ($diterima > 0) {
    $guidance =
        'Ada ' . $diterima . ' tugas yang sudah kamu terima. '
        . 'Buka Penugasan Saya dan pilih Mulai Kerjakan '
        . 'saat kamu mulai menangani perbaikan.';
} elseif ($diproses > 0) {
    $guidance =
        'Ada ' . $diproses . ' tugas yang sedang kamu kerjakan. '
        . 'Setelah perbaikan selesai, tuliskan tindakan '
        . 'dan hasil pekerjaan melalui Penugasan Saya.';
} elseif ($selesai > 0) {
    $guidance =
        'Tidak ada tugas aktif yang perlu ditindaklanjuti. '
        . 'Kamu dapat melihat detail pekerjaan yang sudah selesai '
        . 'melalui Penugasan Saya.';
} else {
    $guidance =
        'Belum ada tugas aktif untukmu. '
        . 'Penugasan dari admin dapat dilihat '
        . 'melalui menu Penugasan Saya.';
}

/*
|--------------------------------------------------------------------------
| Tampilan bersama: sidebar, header, banner, statistik, dan ringkasan
|--------------------------------------------------------------------------
*/

require __DIR__ . '/../shared/dashboard-header.php';

?>

            <footer class="dashboard-footer">
                &copy; <?= date('Y') ?> FixTrack.
            </footer>

        </section>
    </main>
</div>

</body>
</html>