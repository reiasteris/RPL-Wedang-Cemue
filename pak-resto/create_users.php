<?php

require_once "config/database.php";

$users = [
    [
        'nama' => 'Andi',
        'role' => 'pelayan',
        'username' => 'pelayan',
        'password' => '12345'
    ],
    [
        'nama' => 'Citra',
        'role' => 'kasir',
        'username' => 'kasir',
        'password' => '12345'
    ],
    [
        'nama' => 'Budi',
        'role' => 'koki',
        'username' => 'koki',
        'password' => '12345'
    ],
    [
        'nama' => 'Citra',
        'role' => 'kasir',
        'username' => 'kasir',
        'password' => '12345'
    ]
];

foreach ($users as $user) {

    $hash = password_hash(
        $user['password'],
        PASSWORD_DEFAULT
    );

    $sql = "
        INSERT INTO pegawai
        (nama_pegawai, role, username, password)
        VALUES (?, ?, ?, ?)
    ";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "ssss",
        $user['nama'],
        $user['role'],
        $user['username'],
        $hash
    );

    if ($stmt->execute()) {
        echo "User {$user['username']} berhasil dibuat.<br>";
    } else {
        echo "Gagal membuat {$user['username']}: {$stmt->error}<br>";
    }
}

?>