
<?php

class Facility
{
    private $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->connect();
    }

    // MENAMPILKAN SEMUA FASILITAS
    public function getAll()
    {
        $sql = "SELECT * FROM facilities
                ORDER BY created_at DESC";

        return $this->db->query($sql)->fetchAll();
    }

    // MENGAMBIL FASILITAS BERDASARKAN ID
    public function findById($id)
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM facilities WHERE id = :id"
        );

        $stmt->execute([':id' => $id]);

        return $stmt->fetch();
    }

    // MENYIMPAN FASILITAS BARU ATAU EDIT
    public function save($data)
    {
        if (!empty($data['id'])) {

            $sql = "UPDATE facilities SET
                    building_name = :building,
                    room_name = :room,
                    facility_name = :facility,
                    description = :description
                    WHERE id = :id";

            $params = [
                ':building' => $data['building_name'],
                ':room' => $data['room_name'],
                ':facility' => $data['facility_name'],
                ':description' => $data['description'],
                ':id' => $data['id']
            ];

        } else {

            $sql = "INSERT INTO facilities
                    (
                        building_name,
                        room_name,
                        facility_name,
                        description
                    )
                    VALUES
                    (
                        :building,
                        :room,
                        :facility,
                        :description
                    )";

            $params = [
                ':building' => $data['building_name'],
                ':room' => $data['room_name'],
                ':facility' => $data['facility_name'],
                ':description' => $data['description']
            ];
        }

        $stmt = $this->db->prepare($sql);

        return $stmt->execute($params);
    }

    // MENGUBAH STATUS FASILITAS
    public function setActive($id, $status)
    {
        $stmt = $this->db->prepare(
            "UPDATE facilities
             SET is_active = :status
             WHERE id = :id"
        );

        return $stmt->execute([
            ':status' => $status,
            ':id' => $id
        ]);
    }

    // MENGAMBIL DAFTAR GEDUNG UNIK
    public function getBuildings()
    {
        $sql = "SELECT DISTINCT building_name
                FROM facilities
                ORDER BY building_name ASC";

        return $this->db->query($sql)->fetchAll(
            PDO::FETCH_COLUMN
        );
    }
}
