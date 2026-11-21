
<?php

class ComplaintAssignment
{
    private $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->connect();
    }

    // =====================================
    // MEMBUAT PENUGASAN PETUGAS
    // =====================================

    public function create($data)
    {
        $sql = "INSERT INTO complaint_assignments (
                    complaint_id,
                    officer_id,
                    assigned_by,
                    notes
                ) VALUES (
                    :complaint,
                    :officer,
                    :admin,
                    :notes
                )";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':complaint' => $data['complaint_id'],
            ':officer' => $data['officer_id'],
            ':admin' => $data['assigned_by'],
            ':notes' => $data['notes']
        ]);
    }

    // =====================================
    // MENAMPILKAN PENUGASAN PETUGAS
    // =====================================

    public function getByOfficer($officerId)
    {
        $sql = "SELECT
                    a.*,
                    c.complaint_code,
                    c.title,
                    c.description,
                    c.photo,
                    c.admin_note,
                    c.rejection_reason,
                    c.status AS complaint_status,
                    cat.category_name,
                    f.building_name,
                    f.room_name,
                    f.facility_name,
                    u.name AS reporter_name
                FROM complaint_assignments a
                JOIN complaints c ON a.complaint_id = c.id
                JOIN categories cat ON c.category_id = cat.id
                JOIN facilities f ON c.facility_id = f.id
                JOIN users u ON c.reporter_id = u.id
                WHERE a.officer_id = :officer
                ORDER BY a.assigned_at DESC";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':officer' => $officerId
        ]);

        return $stmt->fetchAll();
    }

    // =====================================
    // MENCARI PENUGASAN
    // =====================================

    public function findById($id)
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM complaint_assignments
             WHERE id = :id"
        );

        $stmt->execute([
            ':id' => $id
        ]);

        return $stmt->fetch();
    }

    // =====================================
    // MENGUBAH STATUS PENUGASAN
    // =====================================

    public function updateStatus($id, $status)
    {
        $sql = "UPDATE complaint_assignments
                SET status = :status,
                    accepted_at = CASE
                        WHEN :status1 = 'diterima'
                        THEN COALESCE(accepted_at, NOW())
                        ELSE accepted_at
                    END,
                    completed_at = CASE
                        WHEN :status2 = 'selesai'
                        THEN NOW()
                        ELSE completed_at
                    END
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':status' => $status,
            ':status1' => $status,
            ':status2' => $status,
            ':id' => $id
        ]);
    }


    // =====================================
    // MENGAMBIL PENGADUAN YANG SIAP DITUGASKAN
    // =====================================

    public function getVerifiedComplaints()
    {
        $sql = "SELECT
                    c.*,
                    u.name AS reporter_name,
                    cat.category_name,
                    f.building_name,
                    f.room_name,
                    f.facility_name
                FROM complaints c
                JOIN users u ON c.reporter_id = u.id
                JOIN categories cat ON c.category_id = cat.id
                JOIN facilities f ON c.facility_id = f.id
                WHERE c.status = 'diverifikasi'
                AND NOT EXISTS (
                    SELECT 1
                    FROM complaint_assignments a
                    WHERE a.complaint_id = c.id
                    AND a.status != 'dibatalkan'
                )
                ORDER BY c.created_at ASC";

        return $this->db->query($sql)->fetchAll();
    }


    // =====================================
    // MENGAMBIL PETUGAS AKTIF
    // =====================================

    public function getActiveOfficers()
    {
        $sql = "SELECT id, name, email, phone
                FROM users
                WHERE role = 'petugas'
                AND is_active = 1
                ORDER BY name ASC";

        return $this->db->query($sql)->fetchAll();
    }


    // =====================================
    // MENAMPILKAN SEMUA RIWAYAT PENUGASAN
    // =====================================

    public function getAll()
    {
        $sql = "SELECT
                    a.*,
                    c.complaint_code,
                    c.title,
                    c.status AS complaint_status,
                    u.name AS officer_name,
                    admin.name AS admin_name,
                    reporter.name AS reporter_name,
                    f.building_name,
                    f.room_name,
                    f.facility_name
                FROM complaint_assignments a
                JOIN complaints c ON a.complaint_id = c.id
                JOIN users u ON a.officer_id = u.id
                JOIN users admin ON a.assigned_by = admin.id
                JOIN users reporter ON c.reporter_id = reporter.id
                JOIN facilities f ON c.facility_id = f.id
                ORDER BY a.assigned_at DESC";

        return $this->db->query($sql)->fetchAll();
    }


    // =====================================
    // MENCARI PENUGASAN MILIK PETUGAS
    // =====================================

    public function findByIdForOfficer($id, $officerId)
    {
        $sql = "SELECT *
                FROM complaint_assignments
                WHERE id = :id
                AND officer_id = :officer_id
                LIMIT 1";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':id' => $id,
            ':officer_id' => $officerId
        ]);

        return $stmt->fetch();
    }


    // =====================================
    // MENGUBAH STATUS PENUGASAN PETUGAS
    // =====================================

    public function updateStatusForOfficer($id, $officerId, $status)
    {
        $sql = "UPDATE complaint_assignments
                SET status = :status,
                    accepted_at = CASE
                        WHEN :status1 = 'diterima'
                        THEN COALESCE(accepted_at, NOW())
                        ELSE accepted_at
                    END,
                    completed_at = CASE
                        WHEN :status2 = 'selesai'
                        THEN NOW()
                        ELSE completed_at
                    END
                WHERE id = :id
                AND officer_id = :officer_id";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':status' => $status,
            ':status1' => $status,
            ':status2' => $status,
            ':id' => $id,
            ':officer_id' => $officerId
        ]);
    }


// ==========================================
// MENYELESAIKAN TUGAS DENGAN CATATAN
// ==========================================

public function completeWithNotes($id, $officerId, $notes)
{
    $sql = "UPDATE complaint_assignments
            SET
                status = 'selesai',
                completion_notes = :notes,
                completed_at = NOW()
            WHERE id = :id
              AND officer_id = :officer
              AND status = 'diproses'";

    $stmt = $this->db->prepare($sql);

    $stmt->execute([
        ':notes' => $notes,
        ':id' => $id,
        ':officer' => $officerId
    ]);

    return $stmt->rowCount() > 0;
}


}
