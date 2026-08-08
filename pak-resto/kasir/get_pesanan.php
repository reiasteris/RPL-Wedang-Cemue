<?php

session_start();

header("Content-Type: application/json");

if (!isset($_SESSION['id_pegawai'])) {

    http_response_code(401);

    echo json_encode([]);

    exit;
}

if ($_SESSION['role'] !== 'kasir') {

    http_response_code(403);

    echo json_encode([]);

    exit;
}

require_once "../config/database.php";


$sql = "

    SELECT

        p.id_pesanan,
        p.id_meja,
        p.waktu,
        m.nomor_meja

    FROM pesanan p

    INNER JOIN meja m
        ON p.id_meja = m.id_meja

    WHERE p.status_pemesanan = 'selesai'

    AND NOT EXISTS (

        SELECT 1

        FROM pembayaran pb

        WHERE pb.id_pesanan = p.id_pesanan

        AND pb.status_validasi = 'berhasil'

    )

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
     * Ambil detail pesanan.
     */

    $sql_detail = "

        SELECT

            dp.jumlah,
            dp.subtotal,
            menu.nama_menu

        FROM detail_pesanan dp

        INNER JOIN menu
            ON dp.id_menu = menu.id_menu

        WHERE dp.id_pesanan = ?

        ORDER BY dp.id_detail ASC

    ";


    $stmt =
        $conn->prepare($sql_detail);

    $stmt->bind_param(
        "i",
        $id_pesanan
    );

    $stmt->execute();

    $detail_result =
        $stmt->get_result();


    $detail_html = "";

    $total = 0;


    while (
        $detail =
        $detail_result->fetch_assoc()
    ) {

        $subtotal =
            (float) $detail['subtotal'];

        $total += $subtotal;


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

                — Rp "
                . number_format(
                    $subtotal,
                    0,
                    ',',
                    '.'
                )
                . "

            </p>

        ";

    }


    $order['detail'] =
        $detail_html;

    $order['total_bayar'] =
        $total;

    $order['total_formatted'] =
        number_format(
            $total,
            0,
            ',',
            '.'
        );


    $orders[] =
        $order;

}


echo json_encode(
    $orders,
    JSON_UNESCAPED_UNICODE
);