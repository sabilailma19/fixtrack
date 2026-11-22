
<?php

$userName = htmlspecialchars($user['name'] ?? 'Mahasiswa');

$success = $_SESSION['success'] ?? null;
$error = $_SESSION['error'] ?? null;

unset($_SESSION['success'], $_SESSION['error']);

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Buat Pengaduan | FixTrack</title>

    <link rel="stylesheet"
      href="<?= BASE_URL ?>/assets/css/admin.css">
      
    <link rel="stylesheet"
          href="<?= BASE_URL ?>/assets/css/reporter.css">

    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

</head>

<body>

<div class="reporter-layout">

    <!-- SIDEBAR -->

    <aside class="reporter-sidebar">

        <div class="reporter-brand">
            <div class="reporter-logo">F</div>

            <div>
                <h2>FixTrack</h2>
                <span>Facility Management</span>
            </div>
        </div>

        <div class="reporter-menu-label">MENU PELAPOR</div>

        <?php
$currentPath = rtrim(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH),
    '/'
);

$reporterMenus = [
    [
        'url' => BASE_URL . '/pelapor/dashboard',
        'icon' => 'bi-grid',
        'label' => 'Dashboard'
    ],
    [
        'url' => BASE_URL . '/pelapor/complaints',
        'icon' => 'bi-clipboard-check',
        'label' => 'Riwayat Pengaduan'
    ],
    [
        'url' => BASE_URL . '/pelapor/complaints/create',
        'icon' => 'bi-plus-circle',
        'label' => 'Buat Pengaduan'
    ]
];
?>

<nav class="reporter-menu">

    <?php foreach ($reporterMenus as $menu): ?>
        <?php $isActive = $currentPath === $menu['url']; ?>

        <a href="<?= htmlspecialchars($menu['url']) ?>"
           class="<?= $isActive ? 'active' : '' ?>"
           aria-label="<?= htmlspecialchars($menu['label']) ?>"
           <?= $isActive ? 'aria-current="page"' : '' ?>>

            <i class="bi <?= htmlspecialchars($menu['icon']) ?>"></i>
            <span><?= htmlspecialchars($menu['label']) ?></span>
        </a>

    <?php endforeach; ?>

</nav>

        <div class="reporter-sidebar-bottom">

            <div class="reporter-profile">
                <div class="reporter-avatar">M</div>

                <div>
                    <strong><?= $userName ?></strong>
                    <span>Pelapor</span>
                </div>
            </div>

            <a href="<?= BASE_URL ?>/logout"
   class="reporter-logout"
   aria-label="Logout">

    <i class="bi bi-box-arrow-right"></i>
    <span>Logout</span>
</a>

        </div>

    </aside>


    <!-- MAIN -->

    <main class="reporter-main">

        <header class="reporter-topbar">

            <div>
                <span>Pages / Pengaduan / Buat Pengaduan</span>
                <h1>Buat Pengaduan</h1>
            </div>

            <div class="reporter-top-user">
                <span>Selamat datang,</span>
                <strong><?= $userName ?></strong>
            </div>

        </header>


        <section class="reporter-content">

            <div class="reporter-heading">
                <div>
                    <h2>Laporkan Kerusakan Fasilitas</h2>

                    <p>
                        Sampaikan laporan kerusakan fasilitas kampus
                        agar dapat segera ditindaklanjuti.
                    </p>
                </div>
            </div>


            <?php if ($success): ?>
                <div class="reporter-alert success">
                    <?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="reporter-alert error">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>


            <div class="reporter-form-card">

                <div class="reporter-form-heading">
                    <div class="reporter-form-icon">
                        <i class="bi bi-clipboard-plus"></i>
                    </div>

                    <div>
                        <h3>Formulir Pengaduan</h3>
                        <p>Lengkapi informasi kerusakan di bawah ini.</p>
                    </div>
                </div>


                <form
                    action="<?= BASE_URL ?>/pelapor/complaints/store"
                    method="POST"
                    enctype="multipart/form-data"
                    id="complaintForm"
                >

                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>"
                    >


                    <div class="reporter-field">

                        <label>Judul Pengaduan *</label>

                        <input
                            type="text"
                            name="title"
                            maxlength="150"
                            placeholder="Contoh: AC ruang kelas tidak dingin"
                            required
                        >

                    </div>


                    <div class="reporter-form-grid">

                        <div class="reporter-field">

                            <label>Kategori Kerusakan *</label>

                            <select name="category_id" required>

                                <option value="">
                                    Pilih kategori kerusakan
                                </option>

                                <?php foreach ($categories as $category): ?>

                                    <option value="<?= (int)$category['id'] ?>">
                                        <?= htmlspecialchars($category['category_name']) ?>
                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>


                        <div class="reporter-field">

                            <label>Fasilitas Kampus *</label>

                            <select name="facility_id" required>

                                <option value="">
                                    Pilih fasilitas
                                </option>

                                <?php foreach ($facilities as $facility): ?>

                                    <option value="<?= (int)$facility['id'] ?>">
                                        <?= htmlspecialchars(
                                            $facility['building_name'] .
                                            ' - ' .
                                            $facility['room_name'] .
                                            ' - ' .
                                            $facility['facility_name']
                                        ) ?>
                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>

                    </div>


                    <div class="reporter-field">

                        <label>Deskripsi Kerusakan *</label>

                        <textarea
                            name="description"
                            rows="5"
                            placeholder="Jelaskan kondisi kerusakan, lokasi detail, dan informasi tambahan..."
                            required
                        ></textarea>

                    </div>


                    <div class="reporter-field">

                        <label>Foto Kerusakan</label>

                        <div class="reporter-upload" id="uploadArea">

                            <i class="bi bi-cloud-arrow-up"></i>

                            <strong>Unggah Foto Kerusakan</strong>

                            <span>
                                Format JPG, PNG, WEBP. Maksimal 3 MB.
                            </span>

                            <input
                                type="file"
                                name="photo"
                                id="photoInput"
                                accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                            >

                            <button type="button" id="choosePhoto">
                                Pilih Foto
                            </button>

                        </div>

                        <div id="photoPreviewContainer" hidden>

                            <img id="photoPreview" alt="Preview foto">

                            <button type="button" id="removePhoto">
                                Hapus Foto
                            </button>

                        </div>

                    </div>


                    <div class="reporter-form-info">

                        <i class="bi bi-info-circle"></i>

                        <p>
                            Pengaduan yang dikirim akan diperiksa oleh
                            Administrator sebelum diteruskan kepada petugas.
                        </p>

                    </div>


                    <div class="reporter-form-actions">

                        <a href="<?= BASE_URL ?>/pelapor/complaints">
                            Batal
                        </a>

                        <button type="submit">
                            <i class="bi bi-send"></i>
                            Kirim Pengaduan
                        </button>

                    </div>

                </form>

            </div>

        </section>

    </main>

</div>


<script src="<?= BASE_URL ?>/assets/js/reporter.js"></script>

</body>
</html>
