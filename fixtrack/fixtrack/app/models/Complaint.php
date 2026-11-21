
<?php

class Complaint
{
    private $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->connect();
    }

    // =====================================
    // MENAMPILKAN SEMUA PENGADUAN
    // =====================================


public function getAll()
{
    $sql = "SELECT
                c.*,
                u.name AS reporter_name,
                u.email AS reporter_email,
                cat.category_name,
                f.building_name,
                f.room_name,
                f.facility_name,

                a.id AS assignment_id,
                a.status AS assignment_status,
                a.notes AS assignment_notes,
                a.completion_notes,
                a.assigned_at,
                a.accepted_at,
                a.completed_at,

                officer.name AS officer_name

            FROM complaints c

            JOIN users u
                ON c.reporter_id = u.id

            JOIN categories cat
                ON c.category_id = cat.id

            JOIN facilities f
                ON c.facility_id = f.id

            LEFT JOIN (
                SELECT ca.*
                FROM complaint_assignments ca
                INNER JOIN (
                    SELECT
                        complaint_id,
                        MAX(id) AS latest_id
                    FROM complaint_assignments
                    GROUP BY complaint_id
                ) latest
                    ON ca.id = latest.latest_id
            ) a
                ON a.complaint_id = c.id

            LEFT JOIN users officer
                ON officer.id = a.officer_id

            ORDER BY c.created_at DESC";

    return $this->db->query($sql)->fetchAll();
}


    // =====================================
    // MENAMPILKAN PENGADUAN MILIK PELAPOR
    // =====================================

    public function getByReporter($reporterId)
    {
        $sql = "SELECT
                    c.*,
                    cat.category_name,
                    f.building_name,
                    f.room_name,
                    f.facility_name
                FROM complaints c
                JOIN categories cat ON c.category_id = cat.id
                JOIN facilities f ON c.facility_id = f.id
                WHERE c.reporter_id = :reporter_id
                ORDER BY c.created_at DESC";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':reporter_id' => $reporterId
        ]);

        return $stmt->fetchAll();
    }

    // =====================================
    // MENCARI PENGADUAN BERDASARKAN ID
    // =====================================

    public function findById($id)
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM complaints WHERE id = :id"
        );

        $stmt->execute([
            ':id' => $id
        ]);

        return $stmt->fetch();
    }

    // =====================================
    // MEMBUAT PENGADUAN
    // =====================================

    public function create($data)
    {
        $sql = "INSERT INTO complaints (
                    complaint_code,
                    reporter_id,
                    category_id,
                    facility_id,
                    title,
                    description,
                    photo
                ) VALUES (
                    :code,
                    :reporter,
                    :category,
                    :facility,
                    :title,
                    :description,
                    :photo
                )";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':code' => $data['complaint_code'],
            ':reporter' => $data['reporter_id'],
            ':category' => $data['category_id'],
            ':facility' => $data['facility_id'],
            ':title' => $data['title'],
            ':description' => $data['description'],
            ':photo' => $data['photo']
        ]);
    }

    // =====================================
    // MENGUBAH STATUS PENGADUAN
    // =====================================

 
        public function updateStatus($id, $status, $note = null)
        {
            $rejectionReason = $status === 'ditolak'
                ? $note
                : null;

            $sql = "UPDATE complaints
                    SET status = :status,
                        admin_note = :admin_note,
                        rejection_reason = :rejection_reason
                    WHERE id = :id";

            $stmt = $this->db->prepare($sql);

            return $stmt->execute([
                ':status' => $status,
                ':admin_note' => $note,
                ':rejection_reason' => $rejectionReason,
                ':id' => $id
            ]);
        }

    // =====================================
    // MENGAMBIL STATISTIK PENGADUAN
    // =====================================

    public function getStatistics()
    {
        $sql = "SELECT
                    COUNT(*) AS total,
                    SUM(status = 'menunggu_verifikasi') AS waiting,
                    SUM(status = 'diverifikasi') AS verified,
                    SUM(status = 'ditugaskan') AS assigned,
                    SUM(status = 'diproses') AS processing,
                    SUM(status = 'selesai') AS completed,
                    SUM(status = 'ditolak') AS rejected
                FROM complaints";

        return $this->db->query($sql)->fetch();
    }
}
