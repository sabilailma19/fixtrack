
<?php

class User
{
    private $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->connect();
    }

    // LOGIN
    public function findByEmail($email)
    {
        $query = "SELECT * FROM users
                  WHERE email = :email
                  AND is_active = 1
                  LIMIT 1";

        $stmt = $this->db->prepare($query);
        $stmt->execute([':email' => $email]);

        return $stmt->fetch();
    }

    public function verifyPassword($password, $hashedPassword)
    {
        return password_verify($password, $hashedPassword);
    }

    // MENAMPILKAN SEMUA PENGGUNA
    public function getAll()
    {
        $query = "SELECT * FROM users ORDER BY created_at DESC";

        return $this->db->query($query)->fetchAll();
    }

    // MENGECEK EMAIL
    public function emailExists($email, $excludeId = null)
    {
        $sql = "SELECT COUNT(*) FROM users WHERE email = :email";

        if ($excludeId !== null) {
            $sql .= " AND id != :id";
        }

        $stmt = $this->db->prepare($sql);

        $params = [':email' => $email];

        if ($excludeId !== null) {
            $params[':id'] = $excludeId;
        }

        $stmt->execute($params);

        return $stmt->fetchColumn() > 0;
    }

    // MENAMBAHKAN ATAU MENGEDIT PENGGUNA
    public function save($data)
    {
        if (!empty($data['id'])) {
            $sql = "UPDATE users SET
                    name = :name,
                    email = :email,
                    role = :role,
                    identity_number = :identity_number,
                    phone = :phone";

            $params = [
                ':name' => $data['name'],
                ':email' => $data['email'],
                ':role' => $data['role'],
                ':identity_number' => $data['identity_number'],
                ':phone' => $data['phone'],
                ':id' => $data['id']
            ];

            if (!empty($data['password'])) {
                $sql .= ", password = :password";
                $params[':password'] = password_hash(
                    $data['password'],
                    PASSWORD_DEFAULT
                );
            }

            $sql .= " WHERE id = :id";

        } else {
            $sql = "INSERT INTO users
                    (name, email, password, role,
                     identity_number, phone, is_active)
                    VALUES
                    (:name, :email, :password, :role,
                     :identity_number, :phone, 1)";

            $params = [
                ':name' => $data['name'],
                ':email' => $data['email'],
                ':password' => password_hash(
                    $data['password'],
                    PASSWORD_DEFAULT
                ),
                ':role' => $data['role'],
                ':identity_number' => $data['identity_number'],
                ':phone' => $data['phone']
            ];
        }

        $stmt = $this->db->prepare($sql);

        return $stmt->execute($params);
    }

    // MENGAKTIFKAN / MENONAKTIFKAN AKUN
    public function setActive($id, $status)
    {
        $sql = "UPDATE users SET is_active = :status
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':status' => $status,
            ':id' => $id
        ]);
    }

    // MENGAMBIL DATA PENGGUNA BERDASARKAN ID
    public function findById($id)
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM users WHERE id = :id"
        );

        $stmt->execute([':id' => $id]);

        return $stmt->fetch();
    }
}
