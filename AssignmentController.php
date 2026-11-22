<?php

class AssignmentController extends Controller
{
    // =====================================
    // HALAMAN PENUGASAN ADMIN
    // =====================================

    public function adminIndex()
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        $model = new ComplaintAssignment();

        $data = [
            'user' => $_SESSION['user'],
            'complaints' => $model->getVerifiedComplaints(),
            'officers' => $model->getActiveOfficers(),
            'assignments' => $model->getAll()
        ];

        $this->view('admin/assignments', $data);
    }

    // =====================================
    // MENYIMPAN PENUGASAN PETUGAS
    // =====================================

    public function store()
    {
        $this->validateCsrf();

        $complaintId = (int) ($_POST['complaint_id'] ?? 0);
        $officerId = (int) ($_POST['officer_id'] ?? 0);
        $notes = trim($_POST['notes'] ?? '');

        if ($complaintId <= 0 || $officerId <= 0) {
            $_SESSION['error'] =
                'Pengaduan dan petugas wajib dipilih.';

            $this->redirect('/fixtrack/public/admin/assignments');
        }

        $model = new ComplaintAssignment();

        // Pastikan pengaduan siap ditugaskan.
        $verifiedComplaints = $model->getVerifiedComplaints();
        $validComplaint = false;

        foreach ($verifiedComplaints as $complaint) {
            if ((int) $complaint['id'] === $complaintId) {
                $validComplaint = true;
                break;
            }
        }

        if (!$validComplaint) {
            $_SESSION['error'] =
                'Pengaduan tidak tersedia untuk ditugaskan.';

            $this->redirect('/fixtrack/public/admin/assignments');
        }

        // Pastikan petugas aktif.
        $officers = $model->getActiveOfficers();
        $validOfficer = false;

        foreach ($officers as $officer) {
            if ((int) $officer['id'] === $officerId) {
                $validOfficer = true;
                break;
            }
        }

        if (!$validOfficer) {
            $_SESSION['error'] =
                'Petugas tidak ditemukan atau tidak aktif.';

            $this->redirect('/fixtrack/public/admin/assignments');
        }

        try {
            $saved = $model->create([
                'complaint_id' => $complaintId,
                'officer_id' => $officerId,
                'assigned_by' => (int) $_SESSION['user']['id'],
                'notes' => $notes
            ]);

            if (!$saved) {
                throw new Exception('Penugasan gagal disimpan.');
            }

            $complaintModel = new Complaint();

            $complaintModel->updateStatus(
                $complaintId,
                'ditugaskan',
                null
            );

            $_SESSION['success'] = 'Petugas berhasil ditugaskan.';
        } catch (Throwable $e) {
            error_log($e->getMessage());

            $_SESSION['error'] = 'Penugasan gagal disimpan.';
        }

        $this->redirect('/fixtrack/public/admin/assignments');
    }

    // =====================================
    // DASHBOARD PETUGAS
    // =====================================

    public function officerIndex()
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        $officerId = (int) $_SESSION['user']['id'];

        $model = new ComplaintAssignment();

        $data = [
            'user' => $_SESSION['user'],
            'assignments' => $model->getByOfficer($officerId)
        ];

        $this->view('petugas/dashboard', $data);
    }

    // =====================================
    // HALAMAN PENUGASAN SAYA
    // =====================================

    public function officerAssignments()
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        $officerId = (int) $_SESSION['user']['id'];

        $model = new ComplaintAssignment();

        $data = [
            'user' => $_SESSION['user'],
            'assignments' => $model->getByOfficer($officerId)
        ];

        $this->view('petugas/assignments', $data);
    }

    // =====================================
    // MENERIMA PENUGASAN
    // =====================================

    public function accept()
    {
        $this->validateCsrf();

        $id = (int) ($_POST['id'] ?? 0);
        $officerId = (int) $_SESSION['user']['id'];

        $model = new ComplaintAssignment();
        $assignment = $model->findByIdForOfficer($id, $officerId);

        if (!$assignment || $assignment['status'] !== 'ditugaskan') {
            $_SESSION['error'] =
                'Penugasan tidak ditemukan atau tidak dapat diterima.';

            $this->redirect('/fixtrack/public/petugas/assignments');
        }

        $updated = $model->updateStatusForOfficer(
            $id,
            $officerId,
            'diterima'
        );

        $_SESSION[$updated ? 'success' : 'error'] = $updated
            ? 'Tugas berhasil diterima.'
            : 'Tugas gagal diterima.';

        $this->redirect('/fixtrack/public/petugas/assignments');
    }

    // =====================================
    // MEMULAI PENGERJAAN
    // =====================================

    public function start()
    {
        $this->validateCsrf();

        $id = (int) ($_POST['id'] ?? 0);
        $officerId = (int) $_SESSION['user']['id'];

        $model = new ComplaintAssignment();
        $assignment = $model->findByIdForOfficer($id, $officerId);

        if (!$assignment || $assignment['status'] !== 'diterima') {
            $_SESSION['error'] =
                'Tugas harus diterima terlebih dahulu.';

            $this->redirect('/fixtrack/public/petugas/assignments');
        }

        $updated = $model->updateStatusForOfficer(
            $id,
            $officerId,
            'diproses'
        );

        if ($updated) {
            $complaintModel = new Complaint();

            $complaintModel->updateStatus(
                (int) $assignment['complaint_id'],
                'diproses',
                null
            );

            $_SESSION['success'] =
                'Pengerjaan tugas berhasil dimulai.';
        } else {
            $_SESSION['error'] =
                'Status tugas gagal diperbarui.';
        }

        $this->redirect('/fixtrack/public/petugas/assignments');
    }

    // =====================================
    // MENYELESAIKAN PEKERJAAN
    // =====================================

    public function complete()
    {
        $this->validateCsrf();

        $id = (int) ($_POST['id'] ?? 0);
        $officerId = (int) $_SESSION['user']['id'];
        $notes = trim($_POST['completion_notes'] ?? '');

        if ($notes === '') {
            $_SESSION['error'] =
                'Catatan penyelesaian wajib diisi.';

            $this->redirect('/fixtrack/public/petugas/assignments');
        }

        $model = new ComplaintAssignment();
        $assignment = $model->findByIdForOfficer($id, $officerId);

        if (!$assignment || $assignment['status'] !== 'diproses') {
            $_SESSION['error'] =
                'Tugas tidak ditemukan atau belum dalam status diproses.';

            $this->redirect('/fixtrack/public/petugas/assignments');
        }

        try {
            $updated = $model->completeWithNotes(
                $id,
                $officerId,
                $notes
            );

            if (!$updated) {
                throw new Exception(
                    'Gagal memperbarui penugasan.'
                );
            }

            $complaintModel = new Complaint();

            $complaintModel->updateStatus(
                (int) $assignment['complaint_id'],
                'selesai',
                null
            );

            $_SESSION['success'] =
                'Pekerjaan berhasil diselesaikan.';
        } catch (Throwable $e) {
            error_log($e->getMessage());

            $_SESSION['error'] =
                'Pekerjaan gagal diselesaikan.';
        }

        $this->redirect('/fixtrack/public/petugas/assignments');
    }
}