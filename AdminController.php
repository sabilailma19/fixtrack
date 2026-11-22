
<?php

class AdminController extends Controller
{
    public function dashboard()
{
    $model = new Complaint();

    $data = [
        'user' => $_SESSION['user'],
        'statistics' => $model->getStatistics(),
        'complaints' => array_slice($model->getAll(), 0, 5),
        'isReport' => false
    ];

    $this->view('admin/dashboard', $data);
}

public function reports()
{
    $model = new Complaint();

    $data = [
        'user' => $_SESSION['user'],
        'statistics' => $model->getStatistics(),
        'complaints' => $model->getAll(),
        'isReport' => true
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

    // =====================================
// SIMPAN DATA PENGGUNA
// =====================================

public function saveUser()
{
    $this->processUserChange(false);
}


// =====================================
// AKTIFKAN / NONAKTIFKAN PENGGUNA
// =====================================

public function toggleUser()
{
    $this->processUserChange(true);
}


// =====================================
// PROSES PERUBAHAN PENGGUNA
// =====================================

private function processUserChange($toggleOnly)
{
    AuthMiddleware::role(['admin']);
    $this->validateCsrf();

    $db = null;

    // Ambil nilai teks dengan aman.
    $input = static function ($key) {
        $value = $_POST[$key] ?? '';

        return is_string($value) ? trim($value) : '';
    };

    try {
        $rawId = $input('id');

        if (
            $rawId !== '' &&
            filter_var(
                $rawId,
                FILTER_VALIDATE_INT,
                ['options' => ['min_range' => 0]]
            ) === false
        ) {
            throw new DomainException('ID pengguna tidak valid.');
        }

        $id = (int) $rawId;
        $currentUserId = (int) $_SESSION['user']['id'];

        if ($toggleOnly && $id <= 0) {
            throw new DomainException('Pilih pengguna yang valid.');
        }

        $name = $input('name');
        $email = strtolower($input('email'));
        $role = $input('role');
        $identity = $input('identity_number');
        $phone = $input('phone');

        $password = $_POST['password'] ?? '';

        if (!is_string($password)) {
            throw new DomainException('Password tidak valid.');
        }

        $status = null;
        $passwordHash = null;

        if ($toggleOnly) {
            $rawStatus = $input('status');

            if (!in_array($rawStatus, ['0', '1'], true)) {
                throw new DomainException('Status akun tidak valid.');
            }

            $status = (int) $rawStatus;
        } else {
            if (mb_strlen($name) < 2 || mb_strlen($name) > 100) {
                throw new DomainException(
                    'Nama harus terdiri dari 2–100 karakter.'
                );
            }

            if (
                !filter_var($email, FILTER_VALIDATE_EMAIL) ||
                strlen($email) > 100
            ) {
                throw new DomainException(
                    'Email harus valid dan maksimal 100 karakter.'
                );
            }

            if (!in_array(
                $role,
                ['admin', 'petugas', 'pelapor'],
                true
            )) {
                throw new DomainException('Role pengguna tidak valid.');
            }

            if (mb_strlen($identity) > 30) {
                throw new DomainException(
                    'NIM/NIP maksimal 30 karakter.'
                );
            }

            if (
                $phone !== '' &&
                !preg_match('/^\+?[0-9]{8,15}$/', $phone)
            ) {
                throw new DomainException(
                    'Nomor telepon harus berisi 8–15 angka, boleh diawali +.'
                );
            }

            if ($id === 0 && $password === '') {
                throw new DomainException(
                    'Password wajib diisi untuk pengguna baru.'
                );
            }

            if ($password !== '') {
                if (
                    mb_strlen($password) < 8 ||
                    strlen($password) > 72 ||
                    strpos($password, "\0") !== false
                ) {
                    throw new DomainException(
                        'Password minimal 8 karakter, maksimal 72 byte, '
                        . 'dan tidak boleh mengandung karakter null.'
                    );
                }

                $passwordHash = password_hash(
                    $password,
                    PASSWORD_DEFAULT
                );
            }
        }

        $db = (new Database())->connect();
        $db->beginTransaction();

        $existing = null;

        if ($id > 0) {
            // Kunci akun selama pemeriksaan dan penyimpanan.
            // Proses penugasan juga mengunci akun petugas,
            // sehingga perubahan tidak saling mendahului.
            $stmt = $db->prepare(
                'SELECT *
                 FROM users
                 WHERE id = ?
                 FOR UPDATE'
            );

            $stmt->execute([$id]);
            $existing = $stmt->fetch();

            if (!$existing) {
                throw new DomainException('Pengguna tidak ditemukan.');
            }

            // Lindungi akun admin yang sedang dipakai.
            if ($id === $currentUserId) {
                if ($toggleOnly && $status === 0) {
                    throw new DomainException(
                        'Akun yang sedang digunakan tidak dapat dinonaktifkan.'
                    );
                }

                if (!$toggleOnly && $role !== 'admin') {
                    throw new DomainException(
                        'Role akun admin yang sedang digunakan tidak boleh diubah.'
                    );
                }
            }

            $willDeactivate = $toggleOnly && $status === 0;

            $willChangeRole = !$toggleOnly
                && $role !== $existing['role'];

            if ($willDeactivate || $willChangeRole) {
                $stmt = $db->prepare(
                    "SELECT COUNT(*)
                     FROM complaint_assignments
                     WHERE officer_id = ?
                       AND status IN (
                           'ditugaskan',
                           'diterima',
                           'diproses'
                       )"
                );

                $stmt->execute([$id]);
                $activeTasks = (int) $stmt->fetchColumn();

                if ($activeTasks > 0) {
                    throw new DomainException(
                        'Perubahan tidak dapat dilakukan karena pengguna '
                        . 'masih memiliki ' . $activeTasks . ' tugas aktif. '
                        . 'Selesaikan tugas terlebih dahulu.'
                    );
                }
            }
        }

        if ($toggleOnly) {
            $stmt = $db->prepare(
                'UPDATE users
                 SET is_active = ?
                 WHERE id = ?'
            );

            $stmt->execute([$status, $id]);

            $message = $status === 1
                ? 'Akun berhasil diaktifkan.'
                : 'Akun berhasil dinonaktifkan.';
        } else {
            // Periksa email, termasuk milik akun nonaktif.
            $stmt = $db->prepare(
                'SELECT id
                 FROM users
                 WHERE email = ?
                   AND id <> ?
                 LIMIT 1'
            );

            $stmt->execute([$email, $id]);

            if ($stmt->fetch()) {
                throw new DomainException('Email sudah digunakan.');
            }

            if ($id > 0) {
                $sql = 'UPDATE users SET
                            name = ?,
                            email = ?,
                            role = ?,
                            identity_number = ?,
                            phone = ?';

                $params = [
                    $name,
                    $email,
                    $role,
                    $identity !== '' ? $identity : null,
                    $phone !== '' ? $phone : null
                ];

                // Password kosong saat edit berarti tetap memakai
                // password yang sebelumnya tersimpan.
                if ($passwordHash !== null) {
                    $sql .= ', password = ?';
                    $params[] = $passwordHash;
                }

                $sql .= ' WHERE id = ?';
                $params[] = $id;

                $stmt = $db->prepare($sql);
                $stmt->execute($params);

                $message = 'Data pengguna berhasil diperbarui.';
            } else {
                $stmt = $db->prepare(
                    'INSERT INTO users (
                        name,
                        email,
                        password,
                        role,
                        identity_number,
                        phone,
                        is_active
                    ) VALUES (?, ?, ?, ?, ?, ?, 1)'
                );

                $stmt->execute([
                    $name,
                    $email,
                    $passwordHash,
                    $role,
                    $identity !== '' ? $identity : null,
                    $phone !== '' ? $phone : null
                ]);

                $message = 'Pengguna berhasil ditambahkan.';
            }
        }

        $db->commit();

        unset($_SESSION['error']);
        $_SESSION['success'] = $message;
    } catch (Throwable $e) {
        if ($db && $db->inTransaction()) {
            $db->rollBack();
        }

        unset($_SESSION['success']);

        if ($e instanceof DomainException) {
            $_SESSION['error'] = $e->getMessage();
        } elseif (
            $e instanceof PDOException &&
            (int) ($e->errorInfo[1] ?? 0) === 1062
        ) {
            $_SESSION['error'] = 'Email sudah digunakan.';
        } else {
            error_log('Pengelolaan pengguna: ' . $e->getMessage());

            $_SESSION['error'] =
                'Perubahan gagal disimpan. Silakan coba kembali.';
        }
    }

    $this->redirect(BASE_URL . '/admin/users');
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
