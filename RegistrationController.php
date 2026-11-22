<?php

class RegistrationController extends Controller
{
    private function guestOnly()
    {
        if (!empty($_SESSION['user'])) {
            $this->redirect(BASE_URL . '/login');
        }
    }

    private function input($key)
    {
        $value = $_POST[$key] ?? '';

        return is_string($value) ? trim($value) : '';
    }

    private function failed($message, $old)
    {
        $_SESSION['register_error'] = $message;
        $_SESSION['register_old'] = $old;

        $this->redirect(BASE_URL . '/register');
    }

    public function index()
    {
        $this->guestOnly();

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        $this->view('auth/register');
    }

    public function store()
    {
        $this->guestOnly();

        // Periksa token sebelum memproses data.
        $token = $_POST['csrf_token'] ?? '';

        if (
            !is_string($token) ||
            empty($_SESSION['csrf_token']) ||
            !hash_equals($_SESSION['csrf_token'], $token)
        ) {
            http_response_code(403);
            exit('Permintaan tidak valid. Muat ulang halaman registrasi.');
        }

        $name = $this->input('name');
        $email = strtolower($this->input('email'));
        $identityNumber = $this->input('identity_number');
        $phone = $this->input('phone');

        // Password tidak di-trim agar nilainya tidak berubah.
        $password = $_POST['password'] ?? '';
        $confirmation = $_POST['password_confirmation'] ?? '';

        if (!is_string($password)) {
            $password = '';
        }

        if (!is_string($confirmation)) {
            $confirmation = '';
        }

        // Hanya simpan isian biasa, jangan simpan password ke session.
        $old = [
            'name' => $name,
            'email' => $email,
            'identity_number' => $identityNumber,
            'phone' => $phone
        ];

        if (mb_strlen($name) < 2 || mb_strlen($name) > 100) {
            $this->failed('Nama harus terdiri dari 2–100 karakter.', $old);
        }

        if (
            !filter_var($email, FILTER_VALIDATE_EMAIL) ||
            strlen($email) > 100
        ) {
            $this->failed('Masukkan email yang valid, maksimal 100 karakter.', $old);
        }

        if ($identityNumber === '' || mb_strlen($identityNumber) > 30) {
            $this->failed('NIM/NIP wajib diisi, maksimal 30 karakter.', $old);
        }

        if (
            $phone !== '' &&
            !preg_match('/^\+?[0-9]{8,15}$/', $phone)
        ) {
            $this->failed(
                'Nomor telepon harus berisi 8–15 angka, boleh diawali +.',
                $old
            );
        }

        if (mb_strlen($password) < 8 || strlen($password) > 72) {
            $this->failed(
                'Password minimal 8 karakter dan maksimal 72 byte.',
                $old
            );
        }

        if (strpos($password, "\0") !== false) {
            $this->failed('Password mengandung karakter yang tidak valid.', $old);
        }

        if ($password !== $confirmation) {
            $this->failed('Konfirmasi password tidak sesuai.', $old);
        }

        try {
            $model = new User();

            // Mengecek semua akun, termasuk akun yang dinonaktifkan.
            if ($model->emailExists($email)) {
                $this->failed(
                    'Email sudah terdaftar. Gunakan email lain atau masuk ke akunmu.',
                    $old
                );
            }

            // Role ditentukan server, tidak diambil dari formulir.
            // User::save() sudah menggunakan password_hash().
            $saved = $model->save([
                'name' => $name,
                'email' => $email,
                'password' => $password,
                'role' => 'pelapor',
                'identity_number' => $identityNumber,
                'phone' => $phone !== '' ? $phone : null
            ]);

            if (!$saved) {
                throw new RuntimeException('Penyimpanan akun gagal.');
            }
        } catch (PDOException $e) {
            error_log('Registration: ' . $e->getMessage());

            // Menangani email duplikat jika dua permintaan bersamaan.
            if ((int) ($e->errorInfo[1] ?? 0) === 1062) {
                $this->failed(
                    'Email sudah terdaftar. Gunakan email lain atau masuk ke akunmu.',
                    $old
                );
            }

            $this->failed(
                'Pendaftaran belum berhasil. Silakan coba kembali.',
                $old
            );
        } catch (Throwable $e) {
            error_log('Registration: ' . $e->getMessage());

            $this->failed(
                'Pendaftaran belum berhasil. Silakan coba kembali.',
                $old
            );
        }

        unset(
            $_SESSION['register_old'],
            $_SESSION['register_error'],
            $_SESSION['error']
        );

        $_SESSION['success'] =
            'Registrasi berhasil! Silakan login menggunakan email dan passwordmu.';

        $this->redirect(BASE_URL . '/login');
    }
}