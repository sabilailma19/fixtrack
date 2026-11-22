<?php
$error = $_SESSION['register_error'] ?? null;
$old = $_SESSION['register_old'] ?? [];

unset($_SESSION['register_error'], $_SESSION['register_old']);

$e = static function ($value) {
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES,
        'UTF-8'
    );
};
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Daftar Akun | FixTrack</title>

    <link
        rel="stylesheet"
        href="<?= $e(BASE_URL) ?>/assets/css/style.css"
    >

    <style>
        .register-page .login-container {
            min-height: 760px;
        }

        .register-page .login-form-wrapper {
            width: 100%;
            max-width: 460px;
        }

        .register-page .form-heading {
            margin-bottom: 24px;
        }

        .register-page .form-group {
            margin-bottom: 17px;
        }

        .register-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .register-help {
            display: block;
            margin-top: 7px;
            color: #64748b;
            font-size: 12px;
            line-height: 1.6;
        }

        .register-password-toggle {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 0 0 20px;
            color: #475569;
            font-size: 13px;
            cursor: pointer;
        }

        .register-password-toggle input {
            width: 16px;
            height: 16px;
            accent-color: #2563eb;
        }

        .register-login-link {
            margin-top: 22px;
            text-align: center;
            color: #64748b;
            font-size: 14px;
        }

        .register-login-link a {
            color: #2563eb;
            font-weight: 600;
            text-decoration: none;
        }

        .register-login-link a:hover {
            text-decoration: underline;
        }

        .register-benefits {
            position: relative;
            z-index: 1;
            margin: 28px 0;
            padding-left: 20px;
            line-height: 2;
            color: #dbeafe;
            font-size: 14px;
        }

        .register-page .login-footer {
            margin-top: auto;
        }

        @media (max-width: 600px) {
            .register-grid {
                grid-template-columns: 1fr;
                gap: 0;
            }

            .register-page.login-body {
                padding: 16px;
            }

            .register-page .login-right {
                padding: 28px 20px;
            }
        }
    </style>
</head>

<body class="login-body register-page">

<div class="login-container">

    <div class="login-left">
        <div class="brand">
            <div class="brand-icon">F</div>
            <span>FixTrack</span>
        </div>

        <div class="login-intro">
            <span class="eyebrow">CAMPUS FACILITY MANAGEMENT</span>

            <h1>
                Mulai Peduli,
                <span>Kampus Lebih Nyaman.</span>
            </h1>

            <p>
                Buat akun pelapor untuk melaporkan kerusakan fasilitas
                dan memantau perkembangan perbaikannya.
            </p>
        </div>

        <ul class="register-benefits">
            <li>Kirim pengaduan beserta foto fasilitas.</li>
            <li>Pantau status dan riwayat penanganan.</li>
            <li>Berikan konfirmasi hasil perbaikan.</li>
        </ul>

        <div class="login-footer">
            &copy; <?= date('Y') ?> FixTrack. All rights reserved.
        </div>
    </div>

    <div class="login-right">
        <div class="login-form-wrapper">

            <div class="mobile-brand">
                <div class="brand-icon">F</div>
                <span>FixTrack</span>
            </div>

            <div class="form-heading">
                <span class="welcome-label">DAFTAR AKUN PELAPOR</span>
                <h2>Buat Akun Baru</h2>
                <p>Lengkapi data berikut untuk mulai menggunakan FixTrack.</p>
            </div>

            <?php if ($error): ?>
                <div class="alert-error" role="alert">
                    <?= $e($error) ?>
                </div>
            <?php endif; ?>

            <form action="<?= $e(BASE_URL) ?>/register" method="POST">

                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= $e($_SESSION['csrf_token'] ?? '') ?>"
                >

                <div class="form-group">
                    <label for="name">Nama Lengkap</label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        placeholder="Masukkan nama lengkap"
                        value="<?= $e($old['name'] ?? '') ?>"
                        minlength="2"
                        maxlength="100"
                        autocomplete="name"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="nama@email.com"
                        value="<?= $e($old['email'] ?? '') ?>"
                        maxlength="100"
                        autocomplete="email"
                        required
                    >
                </div>

                <div class="register-grid">
                    <div class="form-group">
                        <label for="identity_number">NIM / NIP</label>
                        <input
                            type="text"
                            id="identity_number"
                            name="identity_number"
                            placeholder="Masukkan NIM / NIP"
                            value="<?= $e($old['identity_number'] ?? '') ?>"
                            maxlength="30"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="phone">Nomor Telepon</label>
                        <input
                            type="tel"
                            id="phone"
                            name="phone"
                            placeholder="08xxxxxxxxxx"
                            value="<?= $e($old['phone'] ?? '') ?>"
                            maxlength="16"
                            autocomplete="tel"
                            aria-describedby="phone-help"
                        >
                        <small class="register-help" id="phone-help">
                            Opsional, tulis tanpa spasi atau tanda hubung.
                        </small>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Minimal 8 karakter"
                        minlength="8"
                        maxlength="72"
                        autocomplete="new-password"
                        aria-describedby="password-help"
                        required
                    >
                    <small class="register-help" id="password-help">
                        Minimal 8 karakter. Gunakan kombinasi huruf,
                        angka, dan simbol.
                    </small>
                </div>

                <div class="form-group">
                    <label for="password_confirmation">
                        Konfirmasi Password
                    </label>
                    <input
                        type="password"
                        id="password_confirmation"
                        name="password_confirmation"
                        placeholder="Masukkan kembali password"
                        minlength="8"
                        maxlength="72"
                        autocomplete="new-password"
                        required
                    >
                </div>

                <label class="register-password-toggle">
                    <input type="checkbox" id="show-passwords">
                    <span>Tampilkan password</span>
                </label>

                <button type="submit" class="btn-login">
                    Daftar Sekarang
                    <span aria-hidden="true">&rarr;</span>
                </button>
            </form>

            <p class="register-login-link">
                Sudah punya akun?
                <a href="<?= $e(BASE_URL) ?>/login">Masuk di sini</a>
            </p>

        </div>
    </div>

</div>

<script>
    const passwordInput = document.getElementById('password');
    const confirmationInput = document.getElementById('password_confirmation');
    const showPasswords = document.getElementById('show-passwords');

    showPasswords.addEventListener('change', function () {
        const type = this.checked ? 'text' : 'password';

        passwordInput.type = type;
        confirmationInput.type = type;
    });

    function validateConfirmation() {
        const mismatch =
            confirmationInput.value !== '' &&
            confirmationInput.value !== passwordInput.value;

        confirmationInput.setCustomValidity(
            mismatch ? 'Konfirmasi password tidak sesuai.' : ''
        );
    }

    passwordInput.addEventListener('input', validateConfirmation);
    confirmationInput.addEventListener('input', validateConfirmation);
</script>

</body>
</html>