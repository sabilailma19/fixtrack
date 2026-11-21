
<?php

class AuthController extends Controller
{
    public function login()
    {
        if (!empty($_SESSION['user'])) {
            $this->redirect($this->dashboardUrl(
                $_SESSION['user']['role']
            ));
        }

        $this->view('auth/login');
    }

    public function authenticate()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/fixtrack/public/login');
        }

        $token = $_POST['csrf_token'] ?? '';

        if (
            empty($_SESSION['csrf_token']) ||
            !hash_equals($_SESSION['csrf_token'], $token)
        ) {
            http_response_code(403);
            exit('Permintaan tidak valid.');
        }

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
            $_SESSION['error'] = 'Email atau password tidak valid.';
            $this->redirect('/fixtrack/public/login');
        }

        $userModel = new User();
        $user = $userModel->findByEmail($email);

        if (!$user || !$userModel->verifyPassword(
            $password,
            $user['password']
        )) {
            $_SESSION['error'] = 'Email atau password salah.';
            $this->redirect('/fixtrack/public/login');
        }

        session_regenerate_id(true);

        $_SESSION['user'] = [
            'id'    => $user['id'],
            'name'  => $user['name'],
            'email' => $user['email'],
            'role'  => $user['role']
        ];

        $this->redirect($this->dashboardUrl($user['role']));
    }

    public function logout()
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();

            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();

        $this->redirect('/fixtrack/public/login');
    }

    private function dashboardUrl($role)
    {
        $routes = [
            'admin'   => '/fixtrack/public/admin/dashboard',
            'petugas' => '/fixtrack/public/petugas/dashboard',
            'pelapor' => '/fixtrack/public/pelapor/dashboard'
        ];

        return $routes[$role] ?? '/fixtrack/public/login';
    }
}
