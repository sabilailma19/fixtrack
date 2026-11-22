<?php
$error = $_SESSION['error'] ?? null;
$success = $_SESSION['success'] ?? null;

unset($_SESSION['error'], $_SESSION['success']);

$e = static function ($value) {
    return htmlspecialchars(
        (string) ($value ?? ''),
        ENT_QUOTES,
        'UTF-8'
    );
};
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Login | FixTrack</title>

    <link
        rel="stylesheet"
        href="<?= $e(BASE_URL) ?>/assets/css/style.css"
    >

    <?php if (is_file(
        dirname(__DIR__, 3) . '/public/assets/css/auth-flip.css'
    )): ?>
        <link
            rel="stylesheet"
            href="<?= $e(BASE_URL) ?>/assets/css/auth-flip.css"
        >
    <?php endif; ?>

    <style>
        .auth-success {
            padding: 14px 16px;
            margin-bottom: 20px;
            border: 1px solid #bbf7d0;
            border-radius: 12px;
            background: #f0fdf4;
            color: #166534;
            font-size: 14px;
            line-height: 1.6;
        }

        .auth-register-link {
            margin-top: 14px;
            text-align: center;
            color: #64748b;
            font-size: 14px;
            line-height: 1.6;
        }

        .auth-register-link a {
            color: #2563eb;
            font-weight: 600;
            text-decoration: none;
        }

        .auth-register-link a:hover {
            text-decoration: underline;
        }

        .auth-building-icon {
            width: 64px;
            height: 64px;
            color: inherit;
        }
    </style>
</head>

<body class="login-body">

<div class="login-container">

    <div class="login-left">

        <div class="brand">
            <div class="brand-icon">F</div>
            <span>FixTrack</span>
        </div>

        <div class="login-intro">
            <span class="eyebrow">
                CAMPUS FACILITY MANAGEMENT
            </span>

            <h1>
                Fasilitas Kampus
                <span>Lebih Terawat.</span>
            </h1>

            <p>
                Laporkan kerusakan fasilitas kampus dengan mudah.
                Pantau proses perbaikan dan lihat hasil
                penyelesaiannya dalam satu tempat.
            </p>
        </div>

        <div class="login-illustration">
            <div class="illustration-circle">
                <svg
                    class="auth-building-icon"
                    viewBox="0 0 64 64"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="3"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    aria-hidden="true"
                >
                    <path d="M10 56V24h20V8h24v48H10Z"/>
                    <path d="M30 24v32"/>
                    <path d="M18 32h4M18 40h4M18 48h4"/>
                    <path d="M38 16h4M46 16h1"/>
                    <path d="M38 26h4M46 26h1"/>
                    <path d="M38 36h4M46 36h1"/>
                    <path d="M38 56V46h8v10"/>
                </svg>
            </div>

            <div class="illustration-card">
                <span class="status-dot"></span>
                Bersama merawat fasilitas kampus
            </div>
        </div>

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
                <span class="welcome-label">SELAMAT DATANG KEMBALI</span>

                <h2>Masuk ke FixTrack</h2>

                <p>
                    Silakan masuk menggunakan email dan password akunmu.
                </p>
            </div>

            <!-- Pesan ditampilkan sebelum formulir. -->
            <?php if ($error): ?>
                <div class="alert-error" role="alert">
                    <?= $e($error) ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="auth-success" role="status">
                    <?= $e($success) ?>
                </div>
            <?php endif; ?>

            <!-- Hanya ada satu formulir login. -->
            <form
                action="<?= $e(BASE_URL) ?>/login"
                method="POST"
            >
                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= $e($_SESSION['csrf_token'] ?? '') ?>"
                >

                <div class="form-group">
                    <label for="email">Email</label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="nama@email.com"
                        maxlength="100"
                        autocomplete="username"
                        required
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
                            autocomplete="current-password"
                            required
                        >

                        <button
                            type="button"
                            id="loginPasswordToggle"
                            class="toggle-password"
                            aria-controls="password"
                            aria-label="Tampilkan password"
                            aria-pressed="false"
                        >
                            Lihat
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-login">
                    Masuk ke FixTrack
                    <span aria-hidden="true">&rarr;</span>
                </button>
            </form>

            <!-- Tautan registrasi berada di luar formulir. -->
            <p class="auth-register-link">
                Belum punya akun?

                <a
                    href="<?= $e(BASE_URL) ?>/register"
                    data-auth-flip="next"
                >
                    Daftar sekarang
                </a>
            </p>

            <div class="login-note">
                Mengalami kendala saat masuk?
                Hubungi administrator kampus.
            </div>

        </div>
    </div>

</div>

<script>
    (() => {
        const password = document.getElementById('password');
        const toggle = document.getElementById('loginPasswordToggle');

        toggle.addEventListener('click', () => {
            const visible = password.type === 'password';

            password.type = visible ? 'text' : 'password';
            toggle.textContent = visible ? 'Sembunyikan' : 'Lihat';

            toggle.setAttribute('aria-pressed', String(visible));
            toggle.setAttribute(
                'aria-label',
                visible ? 'Sembunyikan password' : 'Tampilkan password'
            );
        });
    })();
</script>

<?php if (is_file(
    dirname(__DIR__, 3) . '/public/assets/js/auth-flip.js'
)): ?>
    <script src="<?= $e(BASE_URL) ?>/assets/js/auth-flip.js"></script>
<?php endif; ?>

</body>
</html>