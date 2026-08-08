<?php

session_start();

header("Content-Type: application/json");

if (!isset($_SESSION['id_pegawai'])) {

    http_response_code(401);

    echo json_encode([
        "success" => false,
        "message" => "Belum login."
    ]);

    exit;
}


if ($_SESSION['role'] !== 'koki') {

    http_response_code(403);

    echo json_encode([
        "success" => false,
        "message" => "Akses ditolak."
    ]);

    exit;
}


require_once "../config/database.php";


$id_pesanan =
    (int) ($_POST['id_pesanan'] ?? 0);

$status =
    $_POST['status'] ?? '';


$allowed_status = [
    'diproses',
    'selesai'
];


if (
    $id_pesanan <= 0 ||
    !in_array(
        $status,
        $allowed_status,
        true
    )
) {

    echo json_encode([
        "success" => false,
        "message" => "Data tidak valid."
    ]);

    exit;
}


/*
 * Update order status.
 */
$sql = "
    UPDATE pesanan

    SET status_pemesanan = ?

    WHERE id_pesanan = ?
";


$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "si",
    $status,
    $id_pesanan
);


if (!$stmt->execute()) {

    echo json_encode([
        "success" => false,
        "message" =>
            "Gagal memperbarui pesanan."
    ]);

    exit;
}


/*
 * When the order is marked finished,
 * update all unfinished detail items.
 */
if ($status === 'selesai') {

    $sql_detail = "
        UPDATE detail_pesanan

        SET status_item = 'selesai'

        WHERE id_pesanan = ?
    ";

    $stmt_detail =
        $conn->prepare($sql_detail);

    $stmt_detail->bind_param(
        "i",
        $id_pesanan
    );

    $stmt_detail->execute();
}


echo json_encode([
    "success" => true,
    "message" =>
        "Status pesanan berhasil diperbarui."
]);