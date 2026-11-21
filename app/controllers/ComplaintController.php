
<?php

class ComplaintController extends Controller
{
    // =====================================
    // HALAMAN PENGADUAN ADMIN
    // =====================================

    public function adminIndex()
    {
        $model = new Complaint();

        $data = [
            'user' => $_SESSION['user'],
            'complaints' => $model->getAll(),
            'statistics' => $model->getStatistics()
        ];

        $this->view('admin/complaints', $data);
    }


    // =====================================
    // HALAMAN RIWAYAT PENGADUAN PELAPOR
    // =====================================

    public function reporterIndex()
    {
        $model = new Complaint();

        $userId = (int) $_SESSION['user']['id'];

        $data = [
            'user' => $_SESSION['user'],
            'complaints' => $model->getByReporter($userId)
        ];

        $this->view('pelapor/complaints', $data);
    }


    // =====================================
    // FORM BUAT PENGADUAN
    // =====================================

    public function create()
    {
            // Membuat token CSRF jika belum tersedia
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
        $categoryModel = new Category();
        $facilityModel = new Facility();

        $categories = array_filter(
            $categoryModel->getAll(),
            fn($item) => (int) $item['is_active'] === 1
        );

        $facilities = array_filter(
            $facilityModel->getAll(),
            fn($item) => (int) $item['is_active'] === 1
        );

        $data = [
            'user' => $_SESSION['user'],
            'categories' => array_values($categories),
            'facilities' => array_values($facilities)
        ];

        $this->view('pelapor/create-complaint', $data);
    }


    // =====================================
    // MENYIMPAN PENGADUAN BARU
    // =====================================

    public function store()
    {
        $this->validateCsrf();

        $reporterId = (int) $_SESSION['user']['id'];

        $categoryId = (int) ($_POST['category_id'] ?? 0);
        $facilityId = (int) ($_POST['facility_id'] ?? 0);

        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');

        // VALIDASI INPUT

        if (
            $categoryId <= 0 ||
            $facilityId <= 0 ||
            $title === '' ||
            $description === ''
        ) {
            $_SESSION['error'] = 'Semua kolom wajib diisi.';
            $this->redirect('/fixtrack/public/pelapor/complaints/create');
        }

        if (strlen($title) > 150) {
            $_SESSION['error'] = 'Judul maksimal 150 karakter.';
            $this->redirect('/fixtrack/public/pelapor/complaints/create');
        }

        // VALIDASI KATEGORI

        $categoryModel = new Category();
        $category = $categoryModel->findById($categoryId);

        if (!$category || (int) $category['is_active'] !== 1) {
            $_SESSION['error'] = 'Kategori tidak tersedia.';
            $this->redirect('/fixtrack/public/pelapor/complaints/create');
        }

        // VALIDASI FASILITAS

        $facilityModel = new Facility();
        $facility = $facilityModel->findById($facilityId);

        if (!$facility || (int) $facility['is_active'] !== 1) {
            $_SESSION['error'] = 'Fasilitas tidak tersedia.';
            $this->redirect('/fixtrack/public/pelapor/complaints/create');
        }

        // UPLOAD FOTO

        $photoPath = null;

        if (
            isset($_FILES['photo']) &&
            $_FILES['photo']['error'] !== UPLOAD_ERR_NO_FILE
        ) {
            $photo = $_FILES['photo'];

            if ($photo['error'] !== UPLOAD_ERR_OK) {
                $_SESSION['error'] = 'Upload foto gagal.';
                $this->redirect('/fixtrack/public/pelapor/complaints/create');
            }

            // Maksimal 3 MB

            if ($photo['size'] > 3 * 1024 * 1024) {
                $_SESSION['error'] = 'Ukuran foto maksimal 3 MB.';
                $this->redirect('/fixtrack/public/pelapor/complaints/create');
            }

            // Validasi MIME berdasarkan isi file

            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->file($photo['tmp_name']);

            $allowedTypes = [
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                'image/webp' => 'webp'
            ];

            if (!isset($allowedTypes[$mime])) {
                $_SESSION['error'] = 'Format foto harus JPG, PNG, atau WEBP.';
                $this->redirect('/fixtrack/public/pelapor/complaints/create');
            }

            $uploadDir = dirname(__DIR__, 2)
                . '/public/uploads/complaints';

            if (
                !is_dir($uploadDir) &&
                !mkdir($uploadDir, 0755, true)
            ) {
                $_SESSION['error'] = 'Folder upload tidak dapat dibuat.';
                $this->redirect('/fixtrack/public/pelapor/complaints/create');
            }

            $fileName = bin2hex(random_bytes(16))
                . '.' . $allowedTypes[$mime];

            $destination = $uploadDir . '/' . $fileName;

            if (!move_uploaded_file($photo['tmp_name'], $destination)) {
                $_SESSION['error'] = 'Foto gagal disimpan.';
                $this->redirect('/fixtrack/public/pelapor/complaints/create');
            }

            $photoPath = 'uploads/complaints/' . $fileName;
        }

        // GENERATE KODE PENGADUAN

        $complaintCode = 'FT-' .
            date('Ymd') . '-' .
            strtoupper(bin2hex(random_bytes(3)));

        // SIMPAN DATABASE

        $model = new Complaint();

        try {

            $saved = $model->create([
                'complaint_code' => $complaintCode,
                'reporter_id' => $reporterId,
                'category_id' => $categoryId,
                'facility_id' => $facilityId,
                'title' => $title,
                'description' => $description,
                'photo' => $photoPath
            ]);

            if (!$saved) {
                throw new Exception('Gagal menyimpan pengaduan.');
            }

        } catch (Throwable $e) {

            // Hapus foto jika penyimpanan database gagal

            if ($photoPath !== null && isset($destination) && is_file($destination)) {
                unlink($destination);
            }

            error_log($e->getMessage());

            $_SESSION['error'] = 'Pengaduan gagal disimpan. Silakan coba kembali.';
            $this->redirect('/fixtrack/public/pelapor/complaints/create');
        }

        $_SESSION['success'] =
            'Pengaduan berhasil dikirim dengan kode ' . $complaintCode;

        $this->redirect('/fixtrack/public/pelapor/complaints');
    }


    // =====================================
    // VERIFIKASI PENGADUAN OLEH ADMIN
    // =====================================

    public function verify()
    {
        $this->validateCsrf();

        $id = (int) ($_POST['id'] ?? 0);

        $model = new Complaint();
        $complaint = $model->findById($id);

        if (!$complaint) {
            $_SESSION['error'] = 'Pengaduan tidak ditemukan.';
            $this->redirect('/fixtrack/public/admin/complaints');
        }

        if ($complaint['status'] !== 'menunggu_verifikasi') {
            $_SESSION['error'] = 'Pengaduan tidak dapat diverifikasi pada status ini.';
            $this->redirect('/fixtrack/public/admin/complaints');
        }

        $note = trim($_POST['admin_note'] ?? '');

        $model->updateStatus(
            $id,
            'diverifikasi',
            $note ?: null
        );

        $_SESSION['success'] = 'Pengaduan berhasil diverifikasi.';

        $this->redirect('/fixtrack/public/admin/complaints');
    }


    // =====================================
    // MENOLAK PENGADUAN OLEH ADMIN
    // =====================================

    public function reject()
    {
        $this->validateCsrf();

        $id = (int) ($_POST['id'] ?? 0);
        $reason = trim($_POST['rejection_reason'] ?? '');

        if ($reason === '') {
            $_SESSION['error'] = 'Alasan penolakan wajib diisi.';
            $this->redirect('/fixtrack/public/admin/complaints');
        }

        $model = new Complaint();
        $complaint = $model->findById($id);

        if (!$complaint) {
            $_SESSION['error'] = 'Pengaduan tidak ditemukan.';
            $this->redirect('/fixtrack/public/admin/complaints');
        }

        if ($complaint['status'] !== 'menunggu_verifikasi') {
            $_SESSION['error'] = 'Pengaduan tidak dapat ditolak pada status ini.';
            $this->redirect('/fixtrack/public/admin/complaints');
        }

        $model->updateStatus(
            $id,
            'ditolak',
            $reason
        );

        $_SESSION['success'] = 'Pengaduan berhasil ditolak.';

        $this->redirect('/fixtrack/public/admin/complaints');
    }
}
