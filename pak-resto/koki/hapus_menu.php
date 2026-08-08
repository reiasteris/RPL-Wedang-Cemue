<?php

session_start();

if (!isset($_SESSION['id_pegawai'])) {
    header("Location: ../auth/login.php");
    exit;
}

if ($_SESSION['role'] !== 'koki') {
    die("Akses ditolak.");
}

require_once "../config/database.php";


$id_menu =
    (int) ($_GET['id'] ?? 0);


if ($id_menu <= 0) {
    die("ID menu tidak valid.");
}


$sql = "
    DELETE FROM menu
    WHERE id_menu = ?
";


$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "i",
    $id_menu
);


if (!$stmt->execute()) {

    /*
     * Bisa gagal jika menu sudah digunakan
     * oleh detail_pesanan karena foreign key.
     */
    die(
        "Menu tidak dapat dihapus. "
        . "Kemungkinan menu sudah digunakan "
        . "dalam transaksi."
    );
}


header("Location: menu.php");

exit;