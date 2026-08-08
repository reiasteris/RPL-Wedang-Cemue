<?php

session_start();


// ========================================
// CEK LOGIN
// ========================================

if (!isset($_SESSION['id_pegawai'])) {

    echo json_encode([
        "success" => false,
        "message" => "Sesi login tidak ditemukan."
    ]);

    exit;
}


// ========================================
// CEK ROLE
// ========================================

if ($_SESSION['role'] !== 'kasir') {

    echo json_encode([
        "success" => false,
        "message" => "Akses ditolak."
    ]);

    exit;
}


require_once "../config/database.php";


// ========================================
// RESPONSE JSON
// ========================================

header(
    "Content-Type: application/json"
);


// ========================================
// AMBIL DATA DARI REQUEST
// ========================================

$id_pesanan =
    (int) ($_POST['id_pesanan'] ?? 0);


$total_bayar =
    (float) ($_POST['total_bayar'] ?? 0);


$metode_bayar =
    trim(
        $_POST['metode_bayar'] ?? ''
    );


$id_pegawai =
    (int) $_SESSION['id_pegawai'];


// ========================================
// VALIDASI INPUT
// ========================================

if (
    $id_pesanan <= 0 ||
    $total_bayar <= 0 ||
    $metode_bayar === ''
) {

    echo json_encode([
        "success" => false,
        "message" => "Data pembayaran tidak valid."
    ]);

    exit;
}


// ========================================
// VALIDASI METODE PEMBAYARAN
// ========================================

$metode_valid = [

    'tunai',
    'debit',
    'transfer',
    'qris'

];


if (
    !in_array(
        $metode_bayar,
        $metode_valid,
        true
    )
) {

    echo json_encode([
        "success" => false,
        "message" => "Metode pembayaran tidak valid."
    ]);

    exit;
}


// ========================================
// MULAI TRANSACTION
// ========================================

$conn->begin_transaction();


try {


    // ====================================
    // AMBIL DATA PESANAN + MEJA
    // ====================================

    /*
     * FOR UPDATE digunakan supaya pesanan
     * tidak dapat diproses oleh dua Kasir
     * secara bersamaan.
     */

    $sql = "

        SELECT

            p.id_pesanan,
            p.id_meja,
            p.status_pemesanan,
            m.status_meja

        FROM pesanan p

        INNER JOIN meja m
            ON p.id_meja = m.id_meja

        WHERE p.id_pesanan = ?

        FOR UPDATE

    ";


    $stmt =
        $conn->prepare($sql);


    if (!$stmt) {

        throw new Exception(
            "Gagal menyiapkan data pesanan."
        );

    }


    $stmt->bind_param(
        "i",
        $id_pesanan
    );


    $stmt->execute();


    $result =
        $stmt->get_result();


    if (
        $result->num_rows !== 1
    ) {

        throw new Exception(
            "Pesanan tidak ditemukan."
        );

    }


    $pesanan =
        $result->fetch_assoc();


    $stmt->close();



    // ====================================
    // CEK STATUS PESANAN
    // ====================================

    if (
        $pesanan['status_pemesanan']
        !== 'selesai'
    ) {

        throw new Exception(
            "Pesanan belum selesai diproses oleh koki."
        );

    }



    // ====================================
    // CEK APAKAH SUDAH DIBAYAR
    // ====================================

    $sql = "

        SELECT
            id_pembayaran

        FROM pembayaran

        WHERE id_pesanan = ?

        AND status_validasi = 'berhasil'

        LIMIT 1

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


    if (
        $result->num_rows > 0
    ) {

        throw new Exception(
            "Pesanan ini sudah dibayar."
        );

    }


    $stmt->close();



    // ====================================
    // VALIDASI TOTAL PEMBAYARAN
    // ====================================

    /*
     * Hitung ulang total dari database.
     *
     * Jangan hanya percaya nilai
     * total_bayar yang dikirim JavaScript.
     */

    $sql = "

        SELECT
            COALESCE(
                SUM(subtotal),
                0
            ) AS total_seharusnya

        FROM detail_pesanan

        WHERE id_pesanan = ?

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


    $data_total =
        $result->fetch_assoc();


    $stmt->close();


    $total_seharusnya =
        (float)
        $data_total[
            'total_seharusnya'
        ];



    /*
     * Bandingkan total yang dikirim
     * dengan total sebenarnya.
     */

    if (
        abs(
            $total_bayar
            - $total_seharusnya
        ) > 0.01
    ) {

        throw new Exception(
            "Total pembayaran tidak sesuai dengan total pesanan."
        );

    }



    // ====================================
    // SIMPAN PEMBAYARAN
    // ====================================

    $status_validasi =
        'berhasil';


    $waktu_bayar =
        date(
            'Y-m-d H:i:s'
        );


    $sql = "

        INSERT INTO pembayaran

        (
            id_pesanan,
            id_pegawai,
            total_bayar,
            metode_bayar,
            status_validasi,
            waktu_bayar
        )

        VALUES (?, ?, ?, ?, ?, ?)

    ";


    $stmt =
        $conn->prepare($sql);


    if (!$stmt) {

        throw new Exception(
            "Gagal menyiapkan data pembayaran."
        );

    }


    $stmt->bind_param(
        "iidsss",
        $id_pesanan,
        $id_pegawai,
        $total_bayar,
        $metode_bayar,
        $status_validasi,
        $waktu_bayar
    );


    if (
        !$stmt->execute()
    ) {

        throw new Exception(
            "Gagal menyimpan pembayaran: "
            . $stmt->error
        );

    }


    $id_pembayaran =
        $conn->insert_id;


    $stmt->close();



    // ====================================
    // PASTIKAN STATUS PESANAN SELESAI
    // ====================================

    /*
     * Pesanan sudah selesai dari Koki,
     * tetapi kita tetap memastikan status
     * finalnya selesai.
     */

    $status_pesanan =
        'selesai';


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
        $status_pesanan,
        $id_pesanan
    );


    if (
        !$stmt->execute()
    ) {

        throw new Exception(
            "Gagal memperbarui status pesanan."
        );

    }


    $stmt->close();



    // ====================================
    // MEJA KEMBALI TERSEDIA
    // ====================================

    $status_meja =
        'tersedia';


    $id_meja =
        (int)
        $pesanan['id_meja'];


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


    if (
        !$stmt->execute()
    ) {

        throw new Exception(
            "Gagal mengubah status meja."
        );

    }


    $stmt->close();



    // ====================================
    // SEMUA BERHASIL
    // ====================================

    $conn->commit();



    echo json_encode([

        "success" => true,

        "message" =>
            "Pembayaran berhasil divalidasi.",

        "id_pembayaran" =>
            $id_pembayaran,

        "id_pesanan" =>
            $id_pesanan,

        "id_meja" =>
            $id_meja

    ]);


    exit;



} catch (Exception $e) {


    // ====================================
    // BATALKAN SEMUA PERUBAHAN
    // ====================================

    $conn->rollback();


    echo json_encode([

        "success" => false,

        "message" =>
            $e->getMessage()

    ]);


    exit;

}

?>