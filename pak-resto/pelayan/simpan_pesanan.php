<?php

session_start();

if (!isset($_SESSION['id_pegawai'])) {
    header("Location: ../auth/login.php");
    exit;
}

if ($_SESSION['role'] !== 'pelayan') {
    die("Akses ditolak.");
}

require_once "../config/database.php";


$id_meja = (int) ($_POST['id_meja'] ?? 0);
$jumlah_pelanggan = (int) ($_POST['jumlah_pelanggan'] ?? 0);

$jumlah_menu = $_POST['jumlah'] ?? [];

if ($id_meja <= 0 || $jumlah_pelanggan <= 0) {
    die("Data pesanan tidak valid.");
}


/*
 * Mulai transaction.
 */
$conn->begin_transaction();

try {

    /*
     * Pastikan meja masih tersedia.
     *
     * Ini penting kalau ada dua pelayan
     * yang bekerja bersamaan.
     */
    $sql = "
        SELECT *
        FROM meja
        WHERE id_meja = ?
        AND status_meja = 'tersedia'
        FOR UPDATE
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_meja);
    $stmt->execute();

    $meja_result = $stmt->get_result();

    if ($meja_result->num_rows !== 1) {
        throw new Exception(
            "Meja sudah digunakan oleh pelanggan lain."
        );
    }


    /*
     * Buat pesanan.
     */
    $id_pegawai = $_SESSION['id_pegawai'];

    $tanggal = date('Y-m-d');
    $waktu = date('H:i:s');

    $status_pemesanan = 'menunggu';


    $sql = "
        INSERT INTO pesanan
        (
            id_meja,
            id_pegawai,
            jumlah_pelanggan,
            tanggal,
            waktu,
            status_pemesanan
        )
        VALUES (?, ?, ?, ?, ?, ?)
    ";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "iiisss",
        $id_meja,
        $id_pegawai,
        $jumlah_pelanggan,
        $tanggal,
        $waktu,
        $status_pemesanan
    );

    $stmt->execute();

    $id_pesanan = $conn->insert_id;


    /*
     * Simpan detail pesanan.
     */
    $ada_menu = false;

    foreach ($jumlah_menu as $id_menu => $jumlah) {

        $id_menu = (int) $id_menu;
        $jumlah = (int) $jumlah;

        if ($jumlah <= 0) {
            continue;
        }

        $ada_menu = true;


        /*
         * Ambil harga + stok terbaru.
         */
        $sql = "
            SELECT harga, stok_menu
            FROM menu
            WHERE id_menu = ?
            FOR UPDATE
        ";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id_menu);
        $stmt->execute();

        $menu_result = $stmt->get_result();

        if ($menu_result->num_rows !== 1) {
            throw new Exception(
                "Menu tidak ditemukan."
            );
        }

        $menu = $menu_result->fetch_assoc();


        /*
         * Pastikan stok cukup.
         */
        if ($jumlah > $menu['stok_menu']) {
            throw new Exception(
                "Stok menu tidak mencukupi."
            );
        }


        $harga = $menu['harga'];

        $subtotal = $harga * $jumlah;

        $status_item = 'menunggu';


        /*
         * Insert detail.
         */
        $sql = "
            INSERT INTO detail_pesanan
            (
                id_pesanan,
                id_menu,
                jumlah,
                subtotal,
                status_item
            )
            VALUES (?, ?, ?, ?, ?)
        ";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param(
            "iiids",
            $id_pesanan,
            $id_menu,
            $jumlah,
            $subtotal,
            $status_item
        );

        $stmt->execute();


        /*
         * Kurangi stok.
         */
        $stok_baru = $menu['stok_menu'] - $jumlah;


        /*
         * Kalau stok habis,
         * status menu menjadi tidak tersedia.
         */
        $status_ketersediaan =
            ($stok_baru > 0)
                ? 'tersedia'
                : 'habis';


        $sql = "
            UPDATE menu
            SET
                stok_menu = ?,
                status_ketersediaan = ?
            WHERE id_menu = ?
        ";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param(
            "isi",
            $stok_baru,
            $status_ketersediaan,
            $id_menu
        );

        $stmt->execute();
    }


    if (!$ada_menu) {
        throw new Exception(
            "Pilih minimal satu menu."
        );
    }


    /*
     * Meja menjadi terisi.
     */
    $status_meja = 'terisi';

    $sql = "
        UPDATE meja
        SET status_meja = ?
        WHERE id_meja = ?
    ";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "si",
        $status_meja,
        $id_meja
    );

    $stmt->execute();


    /*
     * Semua berhasil.
     */
    $conn->commit();


    header(
        "Location: pesanan_berhasil.php?id_pesanan="
        . $id_pesanan
    );

    exit;


} catch (Exception $e) {

    /*
     * Kalau salah satu proses gagal,
     * batalkan SEMUA perubahan.
     */
    $conn->rollback();

    die(
        "Pesanan gagal disimpan: "
        . htmlspecialchars($e->getMessage())
    );
}
