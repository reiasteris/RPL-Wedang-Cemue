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


$nama_menu =
    trim($_POST['nama_menu'] ?? '');

$kategori =
    trim($_POST['kategori'] ?? '');

$harga =
    (float) ($_POST['harga'] ?? 0);

$stok =
    (int) ($_POST['stok_menu'] ?? 0);


if (
    $nama_menu === '' ||
    $kategori === '' ||
    $harga < 0 ||
    $stok < 0
) {
    die("Data menu tidak valid.");
}


/*
 * Tentukan status berdasarkan stok.
 */
$status_ketersediaan =
    $stok > 0
        ? 'tersedia'
        : 'habis';


$sql = "
    INSERT INTO menu
    (
        nama_menu,
        kategori,
        harga,
        stok_menu,
        status_ketersediaan
    )
    VALUES (?, ?, ?, ?, ?)
";


$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "ssdis",
    $nama_menu,
    $kategori,
    $harga,
    $stok,
    $status_ketersediaan
);


if ($stmt->execute()) {

    header("Location: menu.php");
    exit;

}


die(
    "Gagal menambahkan menu: "
    . $stmt->error
);