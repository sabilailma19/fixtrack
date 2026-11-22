<?php

class AuthMiddleware
{
    private static function rejectSession($message)
    {
        // Hapus informasi login dan data session lama.
        $_SESSION = [];

        session_regenerate_id(true);

        // Siapkan token baru untuk halaman login.
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $_SESSION['error'] = $message;

        header('Location: ' . BASE_URL . '/login');
        exit;
    }

    public static function check()
    {
        $userId = (int) ($_SESSION['user']['id'] ?? 0);

        if ($userId <= 0) {
            self::rejectSession('Silakan login terlebih dahulu.');
        }

        // Ambil kondisi akun terbaru dari database,
        // termasuk akun yang sudah dinonaktifkan.
        $model = new User();
        $user = $model->findById($userId);

        if (!$user) {
            self::rejectSession(
                'Akun tidak ditemukan. Silakan hubungi administrator.'
            );
        }

        if ((int) $user['is_active'] !== 1) {
            self::rejectSession(
                'Akunmu telah dinonaktifkan. Silakan hubungi administrator.'
            );
        }

        $allowedRoles = ['admin', 'petugas', 'pelapor'];

        if (!in_array($user['role'], $allowedRoles, true)) {
            self::rejectSession(
                'Hak akses akun tidak valid. Silakan hubungi administrator.'
            );
        }

        $previousRole = $_SESSION['user']['role'] ?? '';

        // Perubahan role mengharuskan pengguna login kembali.
        if ($previousRole !== $user['role']) {
            self::rejectSession(
                'Hak akses akunmu telah berubah. Silakan login kembali.'
            );
        }

        // Sinkronkan nama dan email jika diubah oleh admin.
        $_SESSION['user'] = [
            'id' => (int) $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role']
        ];
    }

    public static function role($allowedRoles)
    {
        self::check();

        $role = $_SESSION['user']['role'];

        if (!in_array($role, $allowedRoles, true)) {
            http_response_code(403);
            exit('403 - Kamu tidak memiliki akses ke halaman ini.');
        }
    }
}