<?php

session_start();

header("Content-Type: application/json; charset=UTF-8");


/*
 * ==============================
 * CEK LOGIN
 * ==============================
 */

if (!isset($_SESSION['id_pegawai'])) {

    http_response_code(401);

    echo json_encode([
        "success" => false,
        "message" => "Session tidak ditemukan."
    ]);

    exit;
}


/*
 * ==============================
 * CEK ROLE
 * ==============================
 */

if ($_SESSION['role'] !== 'koki') {

    http_response_code(403);

    echo json_encode([
        "success" => false,
        "message" => "Akses ditolak."
    ]);

    exit;
}


/*
 * ==============================
 * DATABASE
 * ==============================
 */

require_once "../config/database.php";


/*
 * ==============================
 * AMBIL ID PESANAN
 * ==============================
 */

$id_pesanan =
    (int) ($_POST['id_pesanan'] ?? 0);


if ($id_pesanan <= 0) {

    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "ID pesanan tidak valid."
    ]);

    exit;
}


/*
 * ==============================
 * MULAI TRANSACTION
 * ==============================
 */

$conn->begin_transaction();


try {


    /*
     * ==========================
     * AMBIL PESANAN
     * ==========================
     */

    $sql = "

        SELECT
            id_pesanan,
            id_meja,
            status_pemesanan

        FROM pesanan

        WHERE id_pesanan = ?

        FOR UPDATE

    ";


    $stmt =
        $conn->prepare($sql);


    $stmt->bind_param(
        "i",
        $id_pesanan
    );


    $stmt->execute();


    $result =
        $stmt->get_result();


    if ($result->num_rows !== 1) {

        throw new Exception(
            "Pesanan tidak ditemukan."
        );

    }


    $pesanan =
        $result->fetch_assoc();


    /*
     * ==========================
     * CEK STATUS
     * ==========================
     *
     * Pesanan yang sudah selesai
     * tidak boleh dibatalkan oleh
     * koki.
     */

    if (
        $pesanan['status_pemesanan']
        !== 'menunggu'

        &&

        $pesanan['status_pemesanan']
        !== 'diproses'
    ) {

        throw new Exception(
            "Pesanan tidak dapat dibatalkan."
        );

    }


    $id_meja =
        (int) $pesanan['id_meja'];


    /*
     * ==========================
     * AMBIL DETAIL PESANAN
     * ==========================
     */

    $sql = "

        SELECT
            id_detail,
            id_menu,
            jumlah,
            status_item

        FROM detail_pesanan

        WHERE id_pesanan = ?

        FOR UPDATE

    ";


    $stmt =
        $conn->prepare($sql);


    $stmt->bind_param(
        "i",
        $id_pesanan
    );


    $stmt->execute();


    $detail_result =
        $stmt->get_result();


    /*
     * ==========================
     * RESTORE STOK
     * ==========================
     */

    while (
        $detail =
        $detail_result->fetch_assoc()
    ) {


        $id_menu =
            (int) $detail['id_menu'];


        $jumlah =
            (int) $detail['jumlah'];


        /*
         * Ambil menu dan lock row.
         */

        $sql_menu = "

            SELECT
                stok_menu

            FROM menu

            WHERE id_menu = ?

            FOR UPDATE

        ";


        $stmt_menu =
            $conn->prepare($sql_menu);


        $stmt_menu->bind_param(
            "i",
            $id_menu
        );


        $stmt_menu->execute();


        $menu_result =
            $stmt_menu->get_result();


        if (
            $menu_result->num_rows !== 1
        ) {

            throw new Exception(
                "Menu tidak ditemukan."
            );

        }


        $menu =
            $menu_result->fetch_assoc();


        $stok_lama =
            (int) $menu['stok_menu'];


        $stok_baru =
            $stok_lama + $jumlah;


        /*
         * Tentukan status menu.
         */

        $status_ketersediaan =
            ($stok_baru > 0)
                ? 'tersedia'
                : 'habis';


        /*
         * Restore stok.
         */

        $sql_update_menu = "

            UPDATE menu

            SET
                stok_menu = ?,
                status_ketersediaan = ?

            WHERE id_menu = ?

        ";


        $stmt_update_menu =
            $conn->prepare(
                $sql_update_menu
            );


        $stmt_update_menu->bind_param(
            "isi",
            $stok_baru,
            $status_ketersediaan,
            $id_menu
        );


        $stmt_update_menu->execute();


        /*
         * Ubah status item.
         */

        $status_item =
            'dibatalkan';


        $sql_update_detail = "

            UPDATE detail_pesanan

            SET
                status_item = ?

            WHERE id_detail = ?

        ";


        $stmt_update_detail =
            $conn->prepare(
                $sql_update_detail
            );


        $id_detail =
            (int) $detail['id_detail'];


        $stmt_update_detail->bind_param(
            "si",
            $status_item,
            $id_detail
        );


        $stmt_update_detail->execute();

    }


    /*
     * ==========================
     * BATALKAN PESANAN
     * ==========================
     */

    $status_pemesanan =
        'dibatalkan';


    $sql = "

        UPDATE pesanan

        SET
            status_pemesanan = ?

        WHERE id_pesanan = ?

    ";


    $stmt =
        $conn->prepare($sql);


    $stmt->bind_param(
        "si",
        $status_pemesanan,
        $id_pesanan
    );


    $stmt->execute();


    /*
     * ==========================
     * KEMBALIKAN MEJA
     * ==========================
     */

    $status_meja =
        'tersedia';


    $sql = "

        UPDATE meja

        SET
            status_meja = ?

        WHERE id_meja = ?

    ";


    $stmt =
        $conn->prepare($sql);


    $stmt->bind_param(
        "si",
        $status_meja,
        $id_meja
    );


    $stmt->execute();


    /*
     * ==========================
     * SEMUA BERHASIL
     * ==========================
     */

    $conn->commit();


    echo json_encode([
        "success" => true,
        "message" =>
            "Pesanan berhasil dibatalkan."
    ]);


    exit;


}


/*
 * ==============================
 * ERROR
 * ==============================
 */

catch (Exception $e) {

    $conn->rollback();


    http_response_code(500);


    echo json_encode([
        "success" => false,
        "message" =>
            "Pesanan gagal dibatalkan: "
            . $e->getMessage()
    ]);


    exit;

}