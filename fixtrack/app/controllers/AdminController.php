
<?php

class AdminController extends Controller
{
    public function dashboard()
    {
        $data = [
            'user' => $_SESSION['user']
        ];

        $this->view('admin/dashboard', $data);
    }

    // HALAMAN DATA PENGGUNA
    public function users()
    {
        $model = new User();

        $data = [
            'user' => $_SESSION['user'],
            'users' => $model->getAll()
        ];

        $this->view('admin/users', $data);
    }

    // SIMPAN PENGGUNA
    public function saveUser()
    {
        $this->validateCsrf();

        $id = (int) ($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? '';
        $identity = trim($_POST['identity_number'] ?? '');
        $phone = trim($_POST['phone'] ?? '');

        $allowedRoles = ['admin', 'petugas', 'pelapor'];

        if (
            $name === '' ||
            !filter_var($email, FILTER_VALIDATE_EMAIL) ||
            !in_array($role, $allowedRoles, true)
        ) {
            $_SESSION['error'] = 'Periksa kembali data pengguna.';
            $this->redirect('/fixtrack/public/admin/users');
        }

        if ($id === 0 && strlen($password) < 8) {
            $_SESSION['error'] = 'Password minimal 8 karakter.';
            $this->redirect('/fixtrack/public/admin/users');
        }

        if ($id > 0 && $password !== '' && strlen($password) < 8) {
            $_SESSION['error'] = 'Password minimal 8 karakter.';
            $this->redirect('/fixtrack/public/admin/users');
        }

        if ($id === (int) $_SESSION['user']['id']) {
            if ($role !== 'admin') {
                $_SESSION['error'] = 'Role akun Admin yang sedang digunakan tidak boleh diubah.';
                $this->redirect('/fixtrack/public/admin/users');
            }
        }

        $model = new User();

        if ($model->emailExists($email, $id ?: null)) {
            $_SESSION['error'] = 'Email sudah digunakan.';
            $this->redirect('/fixtrack/public/admin/users');
        }

        $model->save([
            'id' => $id ?: null,
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'role' => $role,
            'identity_number' => $identity,
            'phone' => $phone
        ]);

        $_SESSION['success'] = $id > 0
            ? 'Data pengguna berhasil diperbarui.'
            : 'Pengguna berhasil ditambahkan.';

        $this->redirect('/fixtrack/public/admin/users');
    }

    // AKTIVASI DAN NONAKTIVASI
    public function toggleUser()
    {
        $this->validateCsrf();

        $id = (int) ($_POST['id'] ?? 0);
        $status = (int) ($_POST['status'] ?? -1);

        if (
            $id <= 0 ||
            !in_array($status, [0, 1], true)
        ) {
            $this->redirect('/fixtrack/public/admin/users');
        }

        if ($id === (int) $_SESSION['user']['id']) {
            $_SESSION['error'] = 'Akun yang sedang digunakan tidak dapat dinonaktifkan.';
            $this->redirect('/fixtrack/public/admin/users');
        }

        $model = new User();
        $model->setActive($id, $status);

        $_SESSION['success'] = $status === 1
            ? 'Akun berhasil diaktifkan.'
            : 'Akun berhasil dinonaktifkan.';

        $this->redirect('/fixtrack/public/admin/users');
    }

    protected function validateCsrf()
    {
        $token = $_POST['csrf_token'] ?? '';

        if (
            empty($_SESSION['csrf_token']) ||
            !hash_equals($_SESSION['csrf_token'], $token)
        ) {
            http_response_code(403);
            exit('Permintaan tidak valid.');
        }
    }


    // =====================================
    // HALAMAN KATEGORI KERUSAKAN
    // =====================================

    public function categories()
    {
        $model = new Category();

        $data = [
            'user' => $_SESSION['user'],
            'categories' => $model->getAll()
        ];

        $this->view('admin/categories', $data);
    }


    // =====================================
    // TAMBAH / EDIT KATEGORI
    // =====================================

    public function saveCategory()
    {
        $this->validateCsrf();

        $id = (int) ($_POST['id'] ?? 0);
        $name = trim($_POST['category_name'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if ($name === '') {
            $_SESSION['error'] = 'Nama kategori wajib diisi.';
            $this->redirect('/fixtrack/public/admin/categories');
        }

        if (strlen($name) > 100) {
            $_SESSION['error'] = 'Nama kategori maksimal 100 karakter.';
            $this->redirect('/fixtrack/public/admin/categories');
        }

        $model = new Category();

        if ($id > 0 && !$model->findById($id)) {
            $_SESSION['error'] = 'Kategori tidak ditemukan.';
            $this->redirect('/fixtrack/public/admin/categories');
        }

        if ($model->nameExists($name, $id ?: null)) {
            $_SESSION['error'] = 'Nama kategori sudah digunakan.';
            $this->redirect('/fixtrack/public/admin/categories');
        }

        $model->save([
            'id' => $id ?: null,
            'name' => $name,
            'description' => $description
        ]);

        $_SESSION['success'] = $id > 0
            ? 'Kategori berhasil diperbarui.'
            : 'Kategori berhasil ditambahkan.';

        $this->redirect('/fixtrack/public/admin/categories');
    }


    // =====================================
    // AKTIFKAN / NONAKTIFKAN KATEGORI
    // =====================================

    public function toggleCategory()
    {
        $this->validateCsrf();

        $id = (int) ($_POST['id'] ?? 0);
        $status = (int) ($_POST['status'] ?? -1);

        if (
            $id <= 0 ||
            !in_array($status, [0, 1], true)
        ) {
            $_SESSION['error'] = 'Permintaan tidak valid.';
            $this->redirect('/fixtrack/public/admin/categories');
        }

        $model = new Category();

        if (!$model->findById($id)) {
            $_SESSION['error'] = 'Kategori tidak ditemukan.';
            $this->redirect('/fixtrack/public/admin/categories');
        }

        $model->setActive($id, $status);

        $_SESSION['success'] = $status === 1
            ? 'Kategori berhasil diaktifkan.'
            : 'Kategori berhasil dinonaktifkan.';

        $this->redirect('/fixtrack/public/admin/categories');
    }


    // =====================================
    // HALAMAN DATA FASILITAS
    // =====================================

    public function facilities()
    {
        $model = new Facility();

        $data = [
            'user' => $_SESSION['user'],
            'facilities' => $model->getAll(),
            'buildings' => $model->getBuildings()
        ];

        $this->view('admin/facilities', $data);
    }


    // =====================================
    // TAMBAH / EDIT FASILITAS
    // =====================================

    public function saveFacility()
    {
        $this->validateCsrf();

        $id = (int) ($_POST['id'] ?? 0);

        $building = trim($_POST['building_name'] ?? '');
        $room = trim($_POST['room_name'] ?? '');
        $facility = trim($_POST['facility_name'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if (
            $building === '' ||
            $room === '' ||
            $facility === ''
        ) {
            $_SESSION['error'] = 'Gedung, ruangan, dan nama fasilitas wajib diisi.';
            $this->redirect('/fixtrack/public/admin/facilities');
        }

        if (
            strlen($building) > 100 ||
            strlen($room) > 100 ||
            strlen($facility) > 150
        ) {
            $_SESSION['error'] = 'Terdapat data yang melebihi batas karakter.';
            $this->redirect('/fixtrack/public/admin/facilities');
        }

        $model = new Facility();

        if ($id > 0 && !$model->findById($id)) {
            $_SESSION['error'] = 'Data fasilitas tidak ditemukan.';
            $this->redirect('/fixtrack/public/admin/facilities');
        }

        $saved = $model->save([
            'id' => $id ?: null,
            'building_name' => $building,
            'room_name' => $room,
            'facility_name' => $facility,
            'description' => $description
        ]);

        if (!$saved) {
            $_SESSION['error'] = 'Data fasilitas gagal disimpan.';
            $this->redirect('/fixtrack/public/admin/facilities');
        }

        $_SESSION['success'] = $id > 0
            ? 'Data fasilitas berhasil diperbarui.'
            : 'Fasilitas berhasil ditambahkan.';

        $this->redirect('/fixtrack/public/admin/facilities');
    }


    // =====================================
    // AKTIFKAN / NONAKTIFKAN FASILITAS
    // =====================================

    public function toggleFacility()
    {
        $this->validateCsrf();

        $id = (int) ($_POST['id'] ?? 0);
        $status = (int) ($_POST['status'] ?? -1);

        if (
            $id <= 0 ||
            !in_array($status, [0, 1], true)
        ) {
            $_SESSION['error'] = 'Permintaan tidak valid.';
            $this->redirect('/fixtrack/public/admin/facilities');
        }

        $model = new Facility();

        if (!$model->findById($id)) {
            $_SESSION['error'] = 'Data fasilitas tidak ditemukan.';
            $this->redirect('/fixtrack/public/admin/facilities');
        }

        $model->setActive($id, $status);

        $_SESSION['success'] = $status === 1
            ? 'Fasilitas berhasil diaktifkan.'
            : 'Fasilitas berhasil dinonaktifkan.';

        $this->redirect('/fixtrack/public/admin/facilities');
    }


}
