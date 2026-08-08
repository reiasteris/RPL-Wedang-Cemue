<?php

session_start();

header("Content-Type: application/json");

if (!isset($_SESSION['id_pegawai'])) {
    http_response_code(401);
    echo json_encode([]);
    exit;
}

if ($_SESSION['role'] !== 'koki') {
    http_response_code(403);
    echo json_encode([]);
    exit;
}

require_once "../config/database.php";


$sql = "
    SELECT
        p.id_pesanan,
        p.id_meja,
        p.jumlah_pelanggan,
        p.tanggal,
        p.waktu,
        p.status_pemesanan,
        m.nomor_meja

    FROM pesanan p

    INNER JOIN meja m
        ON p.id_meja = m.id_meja

    WHERE p.status_pemesanan
        IN ('menunggu', 'diproses')

    ORDER BY
        p.tanggal ASC,
        p.waktu ASC
";


$result = $conn->query($sql);

$orders = [];


while ($order = $result->fetch_assoc()) {

    $id_pesanan =
        (int) $order['id_pesanan'];


    /*
     * Get order details.
     */
    $sql_detail = "
        SELECT
            dp.jumlah,
            dp.status_item,
            menu.nama_menu

        FROM detail_pesanan dp

        INNER JOIN menu
            ON dp.id_menu = menu.id_menu

        WHERE dp.id_pesanan = ?

        ORDER BY dp.id_detail ASC
    ";


    $stmt = $conn->prepare(
        $sql_detail
    );

    $stmt->bind_param(
        "i",
        $id_pesanan
    );

    $stmt->execute();

    $detail_result =
        $stmt->get_result();


    $detail_html = "";


    while (
        $detail =
        $detail_result->fetch_assoc()
    ) {

        $detail_html .= "
            <p>
                <strong>"
                . htmlspecialchars(
                    $detail['nama_menu']
                )
                . "</strong>
                × "
                . $detail['jumlah']
                . "
            </p>
        ";

    }


    /*
     * Determine available action.
     */
    if (
        $order['status_pemesanan']
        === 'menunggu'
    ) {

        $action = "
            <button
                class='button'
                onclick='updateOrder(
                    {$id_pesanan},
                    \"diproses\"
                )'
            >
                Mulai Proses
            </button>
        ";

    } else {

        $action = "
            <button
                class='button button-success'
                onclick='updateOrder(
                    {$id_pesanan},
                    \"selesai\"
                )'
            >
                Pesanan Selesai
            </button>
        ";

    }


    $order['detail'] =
        $detail_html;

    $order['action'] =
        $action;

    $orders[] =
        $order;

}


echo json_encode(
    $orders,
    JSON_UNESCAPED_UNICODE
);