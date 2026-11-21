
<?php

class AuthMiddleware
{
    public static function check()
    {
        if (empty($_SESSION['user'])) {
            header("Location: /fixtrack/public/login");
            exit;
        }
    }

    public static function role($allowedRoles)
    {
        self::check();

        $role = $_SESSION['user']['role'] ?? '';

        if (!in_array($role, $allowedRoles, true)) {
            http_response_code(403);
            exit("403 - Akses tidak diizinkan.");
        }
    }
}
