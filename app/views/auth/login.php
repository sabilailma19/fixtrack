
<?php
$error = $_SESSION['error'] ?? null;
unset($_SESSION['error']);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login | FixTrack</title>

    <link rel="stylesheet"
          href="<?= BASE_URL ?>/assets/css/style.css">
</head>

<body class="login-body">

<div class="login-container">

    <div class="login-left">

        <div class="brand">
            <div class="brand-icon">F</div>
            <span>FixTrack</span>
        </div>

        <div class="login-intro">

            <span class="eyebrow">CAMPUS FACILITY MANAGEMENT</span>

            <h1>
                Fasilitas Kampus
                <span>Lebih Terawat.</span>
            </h1>

            <p>
                Laporkan kerusakan fasilitas kampus dengan mudah.
                Pantau proses perbaikan dan dapatkan informasi
                penyelesaian secara transparan.
            </p>

        </div>

        <div class="login-illustration">
            <div class="illustration-circle">
                <span>🔧</span>
            </div>
            <div class="illustration-card">
                <span class="status-dot"></span>
                Facility maintenance made easier
            </div>
        </div>

        <div class="login-footer">
            © <?= date('Y') ?> FixTrack. All rights reserved.
        </div>

    </div>


    <div class="login-right">

        <div class="login-form-wrapper">

            <div class="mobile-brand">
                <div class="brand-icon">F</div>
                <span>FixTrack</span>
            </div>

            <div class="form-heading">
                <span class="welcome-label">WELCOME BACK</span>

                <h2>Selamat Datang!</h2>

                <p>
                    Silakan masuk untuk mengakses akun FixTrack Anda.
                </p>
            </div>

            <?php if ($error): ?>
                <div class="alert-error">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form action="<?= BASE_URL ?>/login" method="POST">

                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>"
                >

                <div class="form-group">
                    <label for="email">Email</label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="nama@email.com"
                        required
                        autocomplete="email"
                    >
                </div>

                <div class="form-group">

                    <div class="label-row">
                        <label for="password">Password</label>
                    </div>

                    <div class="password-wrapper">

                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Masukkan password"
                            required
                            autocomplete="current-password"
                        >

                        <button
                            type="button"
                            id="togglePassword"
                            class="toggle-password"
                        >
                            Lihat
                        </button>

                    </div>

                </div>

                <button type="submit" class="btn-login">
                    Masuk ke FixTrack
                    <span>→</span>
                </button>

            </form>

            <div class="login-note">
                Mengalami kendala saat masuk?
                Hubungi administrator kampus.
            </div>

        </div>

    </div>

</div>

<script src="<?= BASE_URL ?>/assets/js/app.js"></script>

</body>
</html>
