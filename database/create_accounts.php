
<?php

require_once __DIR__ . '/../app/core/Database.php';

$db = new Database();
$conn = $db->connect();

$accounts = [
    [
        'name' => 'Administrator',
        'email' => 'admin@fixtrack.test',
        'password' => 'Admin123!',
        'role' => 'admin'
    ],
    [
        'name' => 'Petugas Kampus',
        'email' => 'petugas@fixtrack.test',
        'password' => 'Petugas123!',
        'role' => 'petugas'
    ],
    [
        'name' => 'Mahasiswa',
        'email' => 'pelapor@fixtrack.test',
        'password' => 'Pelapor123!',
        'role' => 'pelapor'
    ]
];

$sql = "INSERT INTO users
        (name, email, password, role, is_active)
        VALUES (:name, :email, :password, :role, 1)";

$stmt = $conn->prepare($sql);

foreach ($accounts as $account) {

    $stmt->execute([
        ':name' => $account['name'],
        ':email' => $account['email'],
        ':password' => password_hash(
            $account['password'],
            PASSWORD_DEFAULT
        ),
        ':role' => $account['role']
    ]);

}

echo "Semua akun berhasil dibuat!";
