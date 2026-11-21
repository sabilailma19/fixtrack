
<?php

class Category
{
    private $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->connect();
    }

    // MENAMPILKAN SEMUA KATEGORI
    public function getAll()
    {
        $sql = "SELECT * FROM categories
                ORDER BY created_at DESC";

        return $this->db->query($sql)->fetchAll();
    }

    // MENGAMBIL KATEGORI BERDASARKAN ID
    public function findById($id)
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM categories WHERE id = :id"
        );

        $stmt->execute([':id' => $id]);

        return $stmt->fetch();
    }

    // MEMERIKSA NAMA KATEGORI
    public function nameExists($name, $excludeId = null)
    {
        $sql = "SELECT COUNT(*) FROM categories
                WHERE category_name = :name";

        if ($excludeId !== null) {
            $sql .= " AND id != :id";
        }

        $stmt = $this->db->prepare($sql);

        $params = [':name' => $name];

        if ($excludeId !== null) {
            $params[':id'] = $excludeId;
        }

        $stmt->execute($params);

        return $stmt->fetchColumn() > 0;
    }

    // TAMBAH ATAU EDIT KATEGORI
    public function save($data)
    {
        if (!empty($data['id'])) {

            $sql = "UPDATE categories
                    SET category_name = :name,
                        description = :description
                    WHERE id = :id";

            $params = [
                ':name' => $data['name'],
                ':description' => $data['description'],
                ':id' => $data['id']
            ];

        } else {

            $sql = "INSERT INTO categories
                    (category_name, description)
                    VALUES (:name, :description)";

            $params = [
                ':name' => $data['name'],
                ':description' => $data['description']
            ];
        }

        $stmt = $this->db->prepare($sql);

        return $stmt->execute($params);
    }

    // MENGUBAH STATUS KATEGORI
    public function setActive($id, $status)
    {
        $stmt = $this->db->prepare(
            "UPDATE categories
             SET is_active = :status
             WHERE id = :id"
        );

        return $stmt->execute([
            ':status' => $status,
            ':id' => $id
        ]);
    }
}
