<?php

class FixTrackWorkflow
{
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->connect();
    }

    private function one($sql, $params = [])
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetch();
    }

    private function execute($sql, $params = [])
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->rowCount();
    }

    private function transaction($callback)
    {
        $this->db->beginTransaction();

        try {
            $result = $callback();

            $this->db->commit();

            return $result;
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            throw $e;
        }
    }

    public function readyComplaints()
    {
        $sql = "
            SELECT
                c.*,
                u.name AS reporter_name
            FROM complaints c
            JOIN users u ON u.id = c.reporter_id
            WHERE c.status = 'diverifikasi'
              AND NOT EXISTS (
                  SELECT 1
                  FROM complaint_assignments a
                  WHERE a.complaint_id = c.id
                    AND a.status IN (
                        'ditugaskan',
                        'diterima',
                        'diproses'
                    )
              )
            ORDER BY c.created_at ASC
        ";

        return $this->db->query($sql)->fetchAll();
    }

    public function review($id, $target, $note)
    {
        if (!in_array($target, ['diverifikasi', 'ditolak'], true)) {
            throw new DomainException('Status verifikasi tidak valid.');
        }

        if (mb_strlen($note) > 2000) {
            throw new DomainException('Catatan maksimal 2000 karakter.');
        }

        if ($target === 'ditolak' && $note === '') {
            throw new DomainException('Alasan penolakan wajib diisi.');
        }

        $params = [$target];

        $sql = 'UPDATE complaints SET status = ?';

        if ($target === 'ditolak') {
            $sql .= ', rejection_reason = ?';
            $params[] = $note;
        } elseif ($note !== '') {
            $sql .= ', admin_note = ?';
            $params[] = $note;
        }

        $sql .= " WHERE id = ? AND status = 'menunggu_verifikasi'";
        $params[] = $id;

        if ($this->execute($sql, $params) !== 1) {
            throw new DomainException(
                'Pengaduan tidak ditemukan atau statusnya sudah berubah.'
            );
        }
    }

    public function assign($complaintId, $officerId, $adminId, $notes)
    {
        if (mb_strlen($notes) > 2000) {
            throw new DomainException('Catatan maksimal 2000 karakter.');
        }

        return $this->transaction(function () use (
            $complaintId,
            $officerId,
            $adminId,
            $notes
        ) {
            $complaint = $this->one(
                'SELECT * FROM complaints WHERE id = ? FOR UPDATE',
                [$complaintId]
            );

            if (
                !$complaint ||
                $complaint['status'] !== 'diverifikasi'
            ) {
                throw new DomainException(
                    'Pengaduan belum siap ditugaskan atau sudah berubah.'
                );
            }

            $officer = $this->one(
                "SELECT id
                 FROM users
                 WHERE id = ?
                   AND role = 'petugas'
                   AND is_active = 1
                 FOR UPDATE",
                [$officerId]
            );

            if (!$officer) {
                throw new DomainException('Pilih petugas yang aktif.');
            }

            $activeAssignment = $this->one(
                "SELECT id
                 FROM complaint_assignments
                 WHERE complaint_id = ?
                   AND status IN ('ditugaskan', 'diterima', 'diproses')
                 LIMIT 1
                 FOR UPDATE",
                [$complaintId]
            );

            if ($activeAssignment) {
                throw new DomainException(
                    'Pengaduan masih memiliki penugasan aktif.'
                );
            }

            $this->execute(
                "INSERT INTO complaint_assignments
                    (complaint_id, officer_id, assigned_by, notes, status)
                 VALUES (?, ?, ?, ?, 'ditugaskan')",
                [$complaintId, $officerId, $adminId, $notes]
            );

            // Catatan admin tidak ditimpa.
            $this->execute(
                "UPDATE complaints
                 SET status = 'ditugaskan'
                 WHERE id = ?",
                [$complaintId]
            );
        });
    }

    public function advance(
        $assignmentId,
        $officerId,
        $target,
        $notes = null,
        $photo = null
    ) {
        $expected = [
            'diterima' => 'ditugaskan',
            'diproses' => 'diterima',
            'selesai' => 'diproses'
        ];

        if (!isset($expected[$target])) {
            throw new DomainException('Status tujuan tidak valid.');
        }

        if (
            $target === 'selesai' &&
            (
                !$photo ||
                mb_strlen(trim((string) $notes)) < 5 ||
                mb_strlen((string) $notes) > 2000
            )
        ) {
            throw new DomainException(
                'Foto hasil perbaikan dan catatan 5–2000 karakter wajib diisi.'
            );
        }

        return $this->transaction(function () use (
            $assignmentId,
            $officerId,
            $target,
            $expected,
            $notes,
            $photo
        ) {
            $reference = $this->one(
                'SELECT complaint_id
                 FROM complaint_assignments
                 WHERE id = ? AND officer_id = ?',
                [$assignmentId, $officerId]
            );

            if (!$reference) {
                throw new DomainException('Penugasan tidak ditemukan.');
            }

            // Selalu kunci pengaduan terlebih dahulu.
            $complaint = $this->one(
                'SELECT * FROM complaints WHERE id = ? FOR UPDATE',
                [$reference['complaint_id']]
            );

            $assignment = $this->one(
                'SELECT *
                 FROM complaint_assignments
                 WHERE id = ? AND officer_id = ?
                 FOR UPDATE',
                [$assignmentId, $officerId]
            );

            $expectedComplaintStatus = $target === 'selesai'
                ? 'diproses'
                : 'ditugaskan';

            if (
                !$complaint ||
                !$assignment ||
                $assignment['status'] !== $expected[$target] ||
                $complaint['status'] !== $expectedComplaintStatus
            ) {
                throw new DomainException(
                    'Status tugas sudah berubah. Muat ulang halaman.'
                );
            }

            if ($target === 'diterima') {
                $this->execute(
                    "UPDATE complaint_assignments
                     SET status = 'diterima', accepted_at = NOW()
                     WHERE id = ?",
                    [$assignmentId]
                );
            } elseif ($target === 'diproses') {
                $this->execute(
                    "UPDATE complaint_assignments
                     SET status = 'diproses', started_at = NOW()
                     WHERE id = ?",
                    [$assignmentId]
                );

                $this->execute(
                    "UPDATE complaints SET status = 'diproses' WHERE id = ?",
                    [$complaint['id']]
                );
            } else {
                $this->execute(
                    "UPDATE complaint_assignments
                     SET status = 'selesai',
                         completion_notes = ?,
                         completion_photo = ?,
                         completed_at = NOW()
                     WHERE id = ?",
                    [$notes, $photo, $assignmentId]
                );

                $this->execute(
                    "UPDATE complaints SET status = 'selesai' WHERE id = ?",
                    [$complaint['id']]
                );
            }
        });
    }

    public function feedback(
        $complaintId,
        $assignmentId,
        $reporterId,
        $decision,
        $notes
    ) {
        if (!in_array($decision, ['confirmed', 'needs_review'], true)) {
            throw new DomainException('Pilih hasil konfirmasi.');
        }

        if (
            mb_strlen($notes) > 2000 ||
            ($decision === 'needs_review' && mb_strlen($notes) < 5)
        ) {
            throw new DomainException(
                'Jelaskan masalah yang belum selesai dalam 5–2000 karakter.'
            );
        }

        return $this->transaction(function () use (
            $complaintId,
            $assignmentId,
            $reporterId,
            $decision,
            $notes
        ) {
            $complaint = $this->one(
                'SELECT *
                 FROM complaints
                 WHERE id = ? AND reporter_id = ?
                 FOR UPDATE',
                [$complaintId, $reporterId]
            );

            if (!$complaint || $complaint['status'] !== 'selesai') {
                throw new DomainException(
                    'Pengaduan belum dapat dikonfirmasi.'
                );
            }

            $assignment = $this->one(
                'SELECT *
                 FROM complaint_assignments
                 WHERE complaint_id = ?
                 ORDER BY id DESC
                 LIMIT 1
                 FOR UPDATE',
                [$complaintId]
            );

            if (
                !$assignment ||
                (int) $assignment['id'] !== $assignmentId ||
                $assignment['status'] !== 'selesai' ||
                $assignment['reporter_confirmation'] !== null
            ) {
                throw new DomainException(
                    'Pekerjaan sudah berubah atau telah dikonfirmasi.'
                );
            }

            $this->execute(
                'UPDATE complaint_assignments
                 SET reporter_confirmation = ?,
                     reporter_feedback = ?,
                     feedback_at = NOW()
                 WHERE id = ?',
                [$decision, $notes, $assignmentId]
            );

            if ($decision === 'needs_review') {
                // Penugasan lama tetap tersimpan sebagai riwayat.
                $this->execute(
                    "UPDATE complaints
                     SET status = 'diverifikasi'
                     WHERE id = ?",
                    [$complaintId]
                );
            }
        });
    }

    public function detail($id, $user)
    {
        $sql = "
            SELECT
                c.*,
                u.name AS reporter_name,
                cat.category_name,
                f.building_name,
                f.room_name,
                f.facility_name
            FROM complaints c
            JOIN users u ON u.id = c.reporter_id
            JOIN categories cat ON cat.id = c.category_id
            JOIN facilities f ON f.id = c.facility_id
            WHERE c.id = ?
        ";

        $params = [$id];

        if ($user['role'] === 'pelapor') {
            $sql .= ' AND c.reporter_id = ?';
            $params[] = (int) $user['id'];
        } elseif ($user['role'] === 'petugas') {
            $sql .= '
                AND EXISTS (
                    SELECT 1
                    FROM complaint_assignments a
                    WHERE a.complaint_id = c.id
                      AND a.officer_id = ?
                )
            ';

            $params[] = (int) $user['id'];
        } elseif ($user['role'] !== 'admin') {
            return null;
        }

        $complaint = $this->one($sql, $params);

        if (!$complaint) {
            return null;
        }

        $stmt = $this->db->prepare(
            'SELECT a.*, u.name AS officer_name
             FROM complaint_assignments a
             JOIN users u ON u.id = a.officer_id
             WHERE a.complaint_id = ?
             ORDER BY a.id DESC'
        );

        $stmt->execute([$id]);

        $complaint['history'] = $stmt->fetchAll();

        return $complaint;
    }
}