<?php

session_start();


/* ========================================
   CEK LOGIN
   ======================================== */

if (!isset($_SESSION['id_pegawai'])) {

    header("Location: ../auth/login.php");

    exit;
}


/* ========================================
   CEK ROLE
   ======================================== */

if ($_SESSION['role'] !== 'kasir') {

    die("Akses ditolak.");

}


require_once "../config/database.php";


/* ========================================
   DASHBOARD STATISTICS
   ======================================== */




/*
 * ========================================
 * 2. TRANSAKSI SELESAI HARI INI
 * ========================================
 *
 * Berdasarkan tabel pesanan.
 *
 * Hanya pesanan dengan:
 *
 * status_pemesanan = selesai
 *
 * dan tanggal = hari ini.
 */

$sql_transaksi_hari_ini = "

    SELECT
        COUNT(*) AS total

    FROM pesanan

    WHERE status_pemesanan = 'selesai'

    AND tanggal = CURDATE()

";


$result_transaksi_hari_ini =
    $conn->query(
        $sql_transaksi_hari_ini
    );


$transaksi_hari_ini = 0;


if ($result_transaksi_hari_ini) {

    $row =
        $result_transaksi_hari_ini->fetch_assoc();

    $transaksi_hari_ini =
        (int) $row['total'];

}



/*
 * ========================================
 * 3. PENDAPATAN HARI INI
 * ========================================
 *
 * Pendapatan dihitung dari:
 *
 * detail_pesanan
 *     jumlah
 *
 * dikalikan
 *
 * menu
 *     harga
 *
 * Hanya pesanan yang:
 *
 * status_pemesanan = selesai
 *
 * dan tanggal = hari ini.
 */

$sql_pendapatan_hari_ini = "

    SELECT

        COALESCE(
            SUM(
                dp.jumlah * m.harga
            ),
            0
        ) AS total

    FROM pesanan p

    INNER JOIN detail_pesanan dp
        ON p.id_pesanan =
           dp.id_pesanan

    INNER JOIN menu m
        ON dp.id_menu =
           m.id_menu

    WHERE p.status_pemesanan = 'selesai'

    AND p.tanggal = CURDATE()

";


$result_pendapatan_hari_ini =
    $conn->query(
        $sql_pendapatan_hari_ini
    );


$pendapatan_hari_ini = 0;


if ($result_pendapatan_hari_ini) {

    $row =
        $result_pendapatan_hari_ini->fetch_assoc();

    $pendapatan_hari_ini =
        (float) $row['total'];

}

?>


<!DOCTYPE html>

<html lang="id">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>
    Dashboard Kasir - Pak Resto
</title>


<style>

/* ========================================
   GLOBAL
   ======================================== */

* {
    box-sizing: border-box;
}


html {
    scroll-behavior: smooth;
}


body {

    margin: 0;

    font-family:
        Arial,
        sans-serif;

    background: #f4f7f9;

    color: #17345d;

}


/* ========================================
   LAYOUT
   ======================================== */

.page {

    min-height: 100vh;

    display: flex;

}


/* ========================================
   SIDEBAR
   ======================================== */

.sidebar {

    width: 250px;

    min-height: 100vh;

    background:
        linear-gradient(
            180deg,
            #243b64 0%,
            #24527a 55%,
            #208e91 100%
        );

    color: white;

    display: flex;

    flex-direction: column;

    position: fixed;

    left: 0;

    top: 0;

    bottom: 0;

    padding:
        28px 18px;

}


/* ========================================
   BRAND
   ======================================== */

.brand {

    padding:
        5px 12px 30px;

}


.brand-title {

    margin: 0;

    font-size: 25px;

    font-weight: 800;

    letter-spacing:
        0.5px;

}


.brand-subtitle {

    margin-top: 7px;

    font-size: 13px;

    color:
        rgba(
            255,
            255,
            255,
            0.75
        );

}


/* ========================================
   SIDEBAR MENU
   ======================================== */

.sidebar-menu {

    display: flex;

    flex-direction: column;

    gap: 5px;

}


.sidebar-link {

    display: block;

    padding:
        13px 12px;

    border-radius: 8px;

    color:
        rgba(
            255,
            255,
            255,
            0.88
        );

    text-decoration: none;

    font-size: 15px;

    transition:
        background 0.2s ease,
        color 0.2s ease,
        transform 0.2s ease;

}


.sidebar-link:hover {

    background:
        rgba(
            255,
            255,
            255,
            0.10
        );

    color: white;

    transform:
        translateX(3px);

}


.sidebar-link.active {

    background:
        rgba(
            255,
            255,
            255,
            0.15
        );

    color: white;

    font-weight: bold;

}


/* ========================================
   SIDEBAR BOTTOM
   ======================================== */

.sidebar-bottom {

    margin-top: auto;

    padding:
        18px 12px 5px;

    border-top:
        1px solid
        rgba(
            255,
            255,
            255,
            0.15
        );

}


.user-label {

    font-size: 11px;

    color:
        rgba(
            255,
            255,
            255,
            0.65
        );

    margin-bottom: 5px;

    text-transform: uppercase;

    letter-spacing:
        0.5px;

}


.user-name {

    font-size: 15px;

    font-weight: bold;

    margin-bottom: 14px;

}


.logout {

    display: block;

    width: 100%;

    padding:
        10px 12px;

    border-radius: 7px;

    border:
        1px solid
        rgba(
            255,
            255,
            255,
            0.25
        );

    color: white;

    text-decoration: none;

    text-align: center;

    font-size: 14px;

    transition:
        background 0.2s ease;

}


.logout:hover {

    background:
        rgba(
            255,
            255,
            255,
            0.12
        );

}


/* ========================================
   MAIN
   ======================================== */

.main {

    margin-left: 250px;

    width:
        calc(100% - 250px);

    min-height: 100vh;

    padding:
        35px 42px;

}


/* ========================================
   PAGE HEADER
   ======================================== */

.page-header {

    display: flex;

    justify-content:
        space-between;

    align-items:
        flex-start;

    margin-bottom: 25px;

}


.page-title {

    margin: 0;

    font-size: 30px;

    color: #17345d;

}


.page-description {

    margin-top: 7px;

    color: #71839b;

    font-size: 14px;

}


.date-box {

    color: #71839b;

    font-size: 13px;

    text-align: right;

}


/* ========================================
   SUMMARY
   ======================================== */

.summary-grid {

    display: grid;

    grid-template-columns:
        repeat(2, 1fr);

    gap: 15px;

    margin-bottom: 22px;

}


.summary-card {

    background: white;

    border-radius: 12px;

    padding:
        19px 22px;

    border:
        1px solid
        #e2e8ee;

    box-shadow:
        0 3px 12px
        rgba(
            31,
            55,
            80,
            0.06
        );

}


.summary-label {

    font-size: 14px;

    color: #71839b;

    margin-bottom: 8px;

}


.summary-value {

    font-size: 28px;

    font-weight: bold;

    color: #17345d;

}


.summary-note {

    margin-top: 5px;

    font-size: 12px;

    color: #8b9aad;

}


/* ========================================
   SUMMARY COLORS
   ======================================== */

.summary-card:first-child {

    border-top:
        3px solid
        #f0ad4e;

}


.summary-card:nth-child(2) {

    border-top:
        3px solid
        #198754;

}


.summary-card:nth-child(3) {

    border-top:
        3px solid
        #208e91;

}


/* ========================================
   MAIN CARD
   ======================================== */

.card {

    background: white;

    border-radius: 14px;

    padding: 28px;

    border:
        1px solid
        #e2e8ee;

    box-shadow:
        0 4px 16px
        rgba(
            31,
            55,
            80,
            0.07
        );

}


/* ========================================
   CARD HEADER
   ======================================== */

.card-header {

    margin-bottom: 20px;

}


.card-title {

    margin: 0;

    font-size: 24px;

    color: #17345d;

}


.card-description {

    margin:
        7px 0 0;

    color: #71839b;

    font-size: 14px;

}


/* ========================================
   ORDER
   ======================================== */

.order {

    border:
        1px solid
        #dfe5eb;

    border-radius: 10px;

    padding: 22px;

    margin-bottom: 16px;

    background: white;

    transition:
        box-shadow 0.2s ease,
        border-color 0.2s ease;

}


.order:hover {

    border-color:
        #c9d7e4;

    box-shadow:
        0 4px 14px
        rgba(
            31,
            55,
            80,
            0.07
        );

}


.order-header {

    display: flex;

    justify-content:
        space-between;

    align-items: center;

    gap: 15px;

}


.order-header h3 {

    margin: 0;

    color: #17345d;

    font-size: 20px;

}


.order p {

    color: #52677f;

    font-size: 14px;

}


.order hr {

    border: none;

    border-top:
        1px solid
        #e2e6ea;

    margin:
        18px 0;

}


/* ========================================
   TOTAL
   ======================================== */

.total {

    font-size: 20px;

    font-weight: bold;

    margin:
        18px 0;

    color: #17345d;

}


/* ========================================
   PAYMENT METHOD
   ======================================== */

select {

    padding:
        10px 12px;

    border:
        1px solid
        #d2d9df;

    border-radius: 7px;

    margin-right: 10px;

    font-size: 14px;

    color: #263f5d;

    background: white;

}


select:focus {

    outline: none;

    border-color:
        #208e91;

    box-shadow:
        0 0 0 3px
        rgba(
            32,
            142,
            145,
            0.12
        );

}


/* ========================================
   BUTTON
   ======================================== */

.button {

    display: inline-block;

    padding:
        10px 15px;

    border: none;

    border-radius: 7px;

    background:
        linear-gradient(
            135deg,
            #243b64,
            #24527a
        );

    color: white;

    text-decoration: none;

    cursor: pointer;

    font-size: 14px;

    transition:
        opacity 0.2s ease,
        transform 0.2s ease;

}


.button:hover {

    opacity: 0.95;

    transform:
        translateY(-1px);

}


.button-success {

    background:
        linear-gradient(
            135deg,
            #198754,
            #208e91
        );

}


/* ========================================
   EMPTY
   ======================================== */

.empty {

    text-align: center;

    color: #71839b;

    padding:
        45px 20px;

}


.empty-title {

    font-size: 16px;

    font-weight: bold;

    margin-bottom: 6px;

    color: #52677f;

}


.empty-description {

    font-size: 13px;

    color: #8b9aad;

}


/* ========================================
   RESPONSIVE
   ======================================== */

@media (max-width: 900px) {

    .sidebar {

        width: 210px;

    }


    .main {

        margin-left: 210px;

        width:
            calc(100% - 210px);

        padding: 25px;

    }


    .summary-grid {

        grid-template-columns:
            1fr;

    }

}


@media (max-width: 650px) {

    .page {

        display: block;

    }


    .sidebar {

        position: relative;

        width: 100%;

        min-height: auto;

        padding: 18px;

    }


    .brand {

        padding-bottom: 18px;

    }


    .sidebar-menu {

        flex-direction: row;

        flex-wrap: wrap;

    }


    .sidebar-link {

        flex: 1;

        min-width: 140px;

    }


    .sidebar-bottom {

        margin-top: 20px;

    }


    .main {

        margin-left: 0;

        width: 100%;

        padding: 20px;

    }


    .page-header {

        display: block;

    }


    .date-box {

        margin-top: 10px;

        text-align: left;

    }


    .order-header {

        align-items:
            flex-start;

    }


    select {

        width: 100%;

        margin:
            0 0 10px;

    }


    .button {

        width: 100%;

        text-align: center;

    }

}

</style>

</head>


<body>


<div class="page">


<!-- ========================================
     SIDEBAR
     ======================================== -->

<aside class="sidebar">


    <div class="brand">

        <h1 class="brand-title">
            PAK RESTO
        </h1>


        <div class="brand-subtitle">
            Sistem Manajemen Restoran
        </div>

    </div>



    <nav class="sidebar-menu">


        <a
            href="index.php"
            class="sidebar-link active"
        >
            Pesanan & Pembayaran
        </a>


        <a
            href="../pemilik/index.php"
            class="sidebar-link"
        >
            Laporan Pendapatan
        </a>

    </nav>



    <div class="sidebar-bottom">


        <div class="user-label">
            Kasir
        </div>


        <div class="user-name">

            <?= htmlspecialchars(
                $_SESSION['nama_pegawai']
            ) ?>

        </div>


        <a
            href="../auth/logout.php"
            class="logout"
        >
            Logout
        </a>

    </div>


</aside>



<!-- ========================================
     MAIN
     ======================================== -->

<main class="main">


    <!-- ====================================
         PAGE HEADER
         ==================================== -->

    <div class="page-header">


        <div>

            <h2 class="page-title">
                Kasir
            </h2>


            <div class="page-description">

                Kelola pembayaran
                dan transaksi pelanggan.

            </div>

        </div>


        <div class="date-box">

            <?= date('d F Y') ?>

        </div>


    </div>



    <!-- ====================================
         SUMMARY
         ==================================== -->

    <div class="summary-grid">





        <!-- =================================
             TRANSAKSI SELESAI
             ================================= -->

        <div class="summary-card">

            <div class="summary-label">
                Transaksi Selesai Hari Ini
            </div>


            <div class="summary-value">

                <?= $transaksi_hari_ini ?>

            </div>


            <div class="summary-note">

                Pesanan berstatus selesai

            </div>

        </div>



        <!-- =================================
             PENDAPATAN
             ================================= -->

        <div class="summary-card">

            <div class="summary-label">
                Pendapatan Hari Ini
            </div>


            <div class="summary-value">

                Rp
                <?= number_format(
                    $pendapatan_hari_ini,
                    0,
                    ',',
                    '.'
                ) ?>

            </div>


            <div class="summary-note">

                Total dari pesanan selesai hari ini

            </div>

        </div>


    </div>



    <!-- ====================================
         PESANAN SIAP DIBAYAR
         ==================================== -->

    <div class="card">


        <div class="card-header">


            <h2 class="card-title">

                Pesanan Siap Dibayar

            </h2>


            <p class="card-description">

                Daftar pesanan yang telah
                selesai diproses oleh dapur.

            </p>


        </div>



        <div id="payment-container">


            <div class="empty">

                <div class="empty-title">

                    Memuat pesanan...

                </div>


                <div class="empty-description">

                    Mengambil data pesanan
                    dari dapur.

                </div>

            </div>


        </div>


    </div>


</main>


</div>



<script>


/* =========================================
   LOAD PESANAN
   ========================================= */

function loadPayments() {


    /*
     * Simpan metode pembayaran
     * sebelum refresh.
     */

    const selectedMethods = {};


    document
        .querySelectorAll(
            ".payment-method"
        )
        .forEach(select => {


            selectedMethods[
                select.dataset.id
            ] = select.value;

        });



    fetch("get_pesanan.php")


        .then(response => {


            if (!response.ok) {

                throw new Error(
                    "Gagal mengambil data."
                );

            }


            return response.json();

        })


        .then(data => {


            const container =
                document.getElementById(
                    "payment-container"
                );



            /*
             * Tidak ada pesanan.
             */

            if (data.length === 0) {

                container.innerHTML = `

                    <div class="empty">

                        <div class="empty-title">

                            Tidak ada pesanan
                            yang perlu dibayar.

                        </div>


                        <div class="empty-description">

                            Pesanan yang selesai
                            dari dapur akan muncul
                            di sini.

                        </div>

                    </div>

                `;

                return;

            }



            let html = "";



            /*
             * Buat kartu setiap pesanan.
             */

            data.forEach(order => {


                html += `

                    <div class="order">


                        <div class="order-header">

                            <h3>

                                Pesanan
                                #${order.id_pesanan}

                                —

                                Meja
                                ${order.nomor_meja}

                            </h3>

                        </div>



                        <p>

                            Waktu:
                            ${order.waktu}

                        </p>



                        <hr>



                        ${order.detail}



                        <div class="total">

                            Total:

                            Rp
                            ${order.total_formatted}

                        </div>



                        <select
                            class="payment-method"
                            data-id="${order.id_pesanan}"
                            id="method-${order.id_pesanan}"
                        >

                            <option value="">

                                Pilih metode pembayaran

                            </option>


                            <option value="tunai">

                                Tunai

                            </option>


                            <option value="debit">

                                Debit

                            </option>


                            <option value="transfer">

                                Transfer

                            </option>


                            <option value="qris">

                                QRIS

                            </option>

                        </select>



                        <button
                            class="
                                button
                                button-success
                            "

                            onclick="
                                validatePayment(
                                    ${order.id_pesanan},
                                    ${order.total_bayar}
                                )
                            "
                        >

                            Validasi Pembayaran

                        </button>


                    </div>

                `;

            });



            /*
             * Masukkan kartu ke halaman.
             */

            container.innerHTML =
                html;



            /*
             * Kembalikan metode pembayaran
             * yang dipilih sebelum refresh.
             */

            document
                .querySelectorAll(
                    ".payment-method"
                )
                .forEach(select => {


                    const id =
                        select.dataset.id;


                    if (
                        selectedMethods[id]
                    ) {

                        select.value =
                            selectedMethods[id];

                    }

                });

        })


        .catch(error => {


            console.error(error);


            document.getElementById(
                "payment-container"
            ).innerHTML = `

                <div class="empty">

                    <div class="empty-title">

                        Gagal memuat
                        data pesanan.

                    </div>


                    <div class="empty-description">

                        Periksa koneksi
                        server.

                    </div>

                </div>

            `;

        });

}



/* =========================================
   VALIDASI PEMBAYARAN
   ========================================= */

function validatePayment(
    idPesanan,
    totalBayar
) {


    const method =
        document.getElementById(
            "method-" + idPesanan
        ).value;



    /*
     * Pastikan metode pembayaran
     * sudah dipilih.
     */

    if (method === "") {

        alert(
            "Silakan pilih metode pembayaran."
        );

        return;

    }



    /*
     * Konfirmasi pembayaran.
     */

    const confirmation =
        confirm(
            "Apakah pembayaran sudah diterima?"
        );


    if (!confirmation) {

        return;

    }



    fetch(
        "proses_pembayaran.php",
        {

            method: "POST",

            headers: {

                "Content-Type":
                    "application/x-www-form-urlencoded"

            },


            body:

                "id_pesanan=" +
                encodeURIComponent(
                    idPesanan
                ) +

                "&total_bayar=" +
                encodeURIComponent(
                    totalBayar
                ) +

                "&metode_bayar=" +
                encodeURIComponent(
                    method
                )

        }

    )


    .then(response => {


        if (!response.ok) {

            throw new Error(
                "Gagal menghubungi server."
            );

        }


        return response.json();

    })


    .then(data => {


        if (!data.success) {

            alert(
                data.message
            );

            return;

        }


        alert(
            "Pembayaran berhasil divalidasi."
        );


        /*
         * Refresh daftar pembayaran.
         */

        loadPayments();

    })


    .catch(error => {


        console.error(error);


        alert(
            "Terjadi kesalahan saat " +
            "memproses pembayaran."
        );

    });

}



/* =========================================
   LOAD PERTAMA KALI
   ========================================= */

loadPayments();


/* =========================================
   AUTO REFRESH SETIAP 3 DETIK
   ========================================= */

setInterval(
    loadPayments,
    3000
);

</script>


</body>

</html>