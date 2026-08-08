<?php

session_start();


// ========================================
// CEK LOGIN
// ========================================

if (!isset($_SESSION['id_pegawai'])) {

    header("Location: ../auth/login.php");

    exit;
}


// ========================================
// CEK ROLE
// ========================================

if ($_SESSION['role'] !== 'koki') {

    die("Akses ditolak.");

}


require_once "../config/database.php";


// ========================================
// AMBIL DATA DARI FORM
// ========================================

$id_menu =
    (int) ($_POST['id_menu'] ?? 0);


$nama_menu =
    trim(
        $_POST['nama_menu'] ?? ''
    );


$kategori =
    trim(
        $_POST['kategori'] ?? ''
    );


$harga =
    (float)
    ($_POST['harga'] ?? 0);


$stok =
    (int)
    ($_POST['stok_menu'] ?? 0);


// ========================================
// VALIDASI DATA
// ========================================

if (
    $id_menu <= 0 ||
    $nama_menu === '' ||
    $kategori === '' ||
    $harga < 0 ||
    $stok < 0
) {

    die(
        "Data menu tidak valid."
    );

}


// ========================================
// TENTUKAN STATUS MENU
// ========================================

$status_ketersediaan =
    ($stok > 0)
        ? 'tersedia'
        : 'habis';


// ========================================
// UPDATE DATABASE
// ========================================

$sql = "

    UPDATE menu

    SET

        nama_menu = ?,

        kategori = ?,

        harga = ?,

        stok_menu = ?,

        status_ketersediaan = ?

    WHERE id_menu = ?

";


$stmt =
    $conn->prepare($sql);


if (!$stmt) {

    die(
        "Gagal menyiapkan query: "
        . htmlspecialchars(
            $conn->error
        )
    );

}


// ========================================
// BIND PARAMETER
// ========================================
//
// s = nama_menu
// s = kategori
// d = harga
// i = stok
// s = status
// i = id_menu
//
// Format:
// ssdisi
//

$stmt->bind_param(
    "ssdisi",
    $nama_menu,
    $kategori,
    $harga,
    $stok,
    $status_ketersediaan,
    $id_menu
);


// ========================================
// EKSEKUSI
// ========================================

if (!$stmt->execute()) {

    die(
        "Gagal mengubah menu: "
        . htmlspecialchars(
            $stmt->error
        )
    );

}


// ========================================
// SELESAI
// ========================================

$stmt->close();

$conn->close();


header(
    "Location: menu.php?success=updated"
);

exit;

?>