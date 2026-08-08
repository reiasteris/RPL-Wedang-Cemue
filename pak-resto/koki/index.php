<?php

session_start();

if (!isset($_SESSION['id_pegawai'])) {
    header("Location: ../auth/login.php");
    exit;
}

if ($_SESSION['role'] !== 'koki') {
    die("Akses ditolak.");
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

<title>Dashboard Dapur - Pak Resto</title>


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
    font-family: Arial, sans-serif;
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

    padding: 28px 18px;
}


/* BRAND */

.brand {
    padding: 5px 12px 30px;
}

.brand-title {
    margin: 0;

    font-size: 25px;
    font-weight: 800;

    letter-spacing: 0.5px;
}

.brand-subtitle {
    margin-top: 7px;

    font-size: 13px;

    color: rgba(255,255,255,0.75);
}


/* NAVIGATION */

.sidebar-menu {
    display: flex;
    flex-direction: column;

    gap: 5px;
}


.sidebar-link {
    display: block;

    padding: 13px 12px;

    border-radius: 8px;

    color: rgba(255,255,255,0.88);

    text-decoration: none;

    font-size: 15px;

    transition:
        background 0.2s ease,
        color 0.2s ease,
        transform 0.2s ease;
}


.sidebar-link:hover {
    background: rgba(255,255,255,0.10);

    color: white;

    transform: translateX(3px);
}


.sidebar-link.active {
    background: rgba(255,255,255,0.15);

    color: white;

    font-weight: bold;
}


/* SIDEBAR BOTTOM */

.sidebar-bottom {
    margin-top: auto;

    padding: 18px 12px 5px;

    border-top:
        1px solid
        rgba(255,255,255,0.15);
}


.user-label {
    font-size: 11px;

    color:
        rgba(255,255,255,0.65);

    margin-bottom: 5px;

    text-transform: uppercase;

    letter-spacing: 0.5px;
}


.user-name {
    font-size: 15px;

    font-weight: bold;

    margin-bottom: 14px;
}


.logout {
    display: block;

    width: 100%;

    padding: 10px 12px;

    border-radius: 7px;

    border:
        1px solid
        rgba(255,255,255,0.25);

    color: white;

    text-decoration: none;

    text-align: center;

    font-size: 14px;

    transition:
        background 0.2s ease;
}


.logout:hover {
    background:
        rgba(255,255,255,0.12);
}


/* ========================================
   MAIN
   ======================================== */

.main {
    margin-left: 250px;

    width: calc(100% - 250px);

    min-height: 100vh;

    padding: 35px 42px;
}


/* ========================================
   TOP HEADER
   ======================================== */

.page-header {
    margin-bottom: 25px;

    display: flex;

    justify-content: space-between;

    align-items: flex-start;
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
        repeat(3, 1fr);

    gap: 15px;

    margin-bottom: 22px;
}


.summary-card {
    background: white;

    border-radius: 12px;

    padding: 18px 20px;

    border:
        1px solid
        #e2e8ee;

    box-shadow:
        0 3px 12px
        rgba(31,55,80,0.06);
}


.summary-label {
    font-size: 13px;

    color: #71839b;

    margin-bottom: 8px;
}


.summary-value {
    font-size: 25px;

    font-weight: bold;

    color: #17345d;
}


.summary-note {
    margin-top: 5px;

    font-size: 12px;

    color: #8b9aad;
}


/* ========================================
   MAIN ORDER CONTAINER
   ======================================== */

.card {
    background: white;

    border-radius: 14px;

    padding: 26px;

    border:
        1px solid
        #e2e8ee;

    box-shadow:
        0 4px 16px
        rgba(31,55,80,0.07);
}


.card-header {
    display: flex;

    justify-content: space-between;

    align-items: center;

    margin-bottom: 20px;
}


.card-title {
    margin: 0;

    font-size: 21px;

    color: #17345d;
}


.card-description {
    margin: 6px 0 0;

    color: #71839b;

    font-size: 13px;
}


/* ========================================
   ORDER
   ======================================== */

.order {
    border:
        1px solid
        #dfe5eb;

    border-radius: 10px;

    padding: 20px;

    margin-bottom: 15px;

    background: #fff;

    transition:
        box-shadow 0.2s ease,
        border-color 0.2s ease;
}


.order:hover {
    border-color: #c9d7e4;

    box-shadow:
        0 4px 14px
        rgba(31,55,80,0.07);
}


.order-header {
    display: flex;

    justify-content: space-between;

    align-items: center;

    gap: 15px;
}


.order-header h3 {
    margin: 0;

    color: #17345d;

    font-size: 18px;
}


.order-info {
    display: grid;

    grid-template-columns:
        repeat(2, 1fr);

    gap: 8px 20px;

    margin-top: 15px;
}


.order-info p {
    margin: 0;

    color: #52677f;

    font-size: 14px;
}


.order hr {
    border: none;

    border-top:
        1px solid
        #e2e6ea;

    margin: 17px 0;
}


.order-items p {
    margin: 8px 0;

    color: #263f5d;

    font-size: 14px;
}


.status {
    padding: 6px 11px;

    border-radius: 20px;

    font-size: 12px;

    font-weight: bold;

    text-transform: capitalize;

    white-space: nowrap;
}


.menunggu {
    background: #fff3cd;

    color: #856404;
}


.diproses {
    background: #d8edf8;

    color: #12607c;
}


.selesai {
    background: #d9f1e6;

    color: #17633f;
}


/* ========================================
   BUTTON
   ======================================== */

.button {
    display: inline-block;

    padding: 10px 15px;

    border: none;

    border-radius: 7px;

    background: #243b64;

    color: white;

    text-decoration: none;

    cursor: pointer;

    font-size: 14px;

    transition:
        opacity 0.2s ease,
        transform 0.2s ease;
}


.button:hover {
    opacity: 0.9;

    transform: translateY(-1px);
}


.button-success {
    background: #198754;
}


.button-cancel {
    background: #dc3545;
}


.button-cancel:hover {
    background: #bb2d3b;

    opacity: 1;
}


/* ========================================
   ORDER ACTIONS
   ======================================== */

.order-actions {
    display: flex;

    justify-content: flex-end;

    align-items: center;

    gap: 10px;

    margin-top: 20px;
}


/* ========================================
   EMPTY
   ======================================== */

.empty {
    text-align: center;

    color: #7c8b9b;

    padding: 45px 20px;
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

        width: calc(100% - 210px);

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

    .order-info {
        grid-template-columns:
            1fr;
    }

    .order-header {
        align-items: flex-start;
    }

    .order-actions {
        flex-direction: column;

        align-items: stretch;
    }

    .order-actions .button {
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
            href="#antrian"
            class="sidebar-link active"
            onclick="scrollToSection('antrian')"
        >
            Antrian Pesanan
        </a>


        <a
            href="menu.php"
            class="sidebar-link"
        >
            Kelola Menu & Stok
        </a>


    </nav>


    <div class="sidebar-bottom">

        <div class="user-label">
            Koki
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
     MAIN CONTENT
     ======================================== -->

<main class="main">


    <!-- PAGE HEADER -->

    <div class="page-header">

        <div>

            <h2 class="page-title">
                Dapur
            </h2>

            <div class="page-description">
                Kelola dan proses pesanan pelanggan.
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


        <div class="summary-card">

            <div class="summary-label">
                Pesanan Masuk
            </div>

            <div
                class="summary-value"
                id="summary-menunggu"
            >
                -
            </div>

            <div class="summary-note">
                Menunggu diproses
            </div>

        </div>


        <div class="summary-card">

            <div class="summary-label">
                Sedang Diproses
            </div>

            <div
                class="summary-value"
                id="summary-diproses"
            >
                -
            </div>

            <div class="summary-note">
                Pesanan aktif di dapur
            </div>

        </div>


        <div class="summary-card">

            <div class="summary-label">
                Total Antrian
            </div>

            <div
                class="summary-value"
                id="summary-total"
            >
                -
            </div>

            <div class="summary-note">
                Pesanan yang perlu ditangani
            </div>

        </div>


    </div>



    <!-- ====================================
         ANTRIAN PESANAN
         ==================================== -->

    <div
        class="card"
        id="antrian"
    >


        <div class="card-header">

            <div>

                <h2 class="card-title">
                    Antrian Pesanan
                </h2>

                <p class="card-description">
                    Pesanan akan diperbarui secara otomatis.
                </p>

            </div>

        </div>


        <div id="order-container">

            <div class="empty">

                Memuat pesanan...

            </div>

        </div>


    </div>


</main>


</div>



<script>


// ========================================
// SCROLL NAVIGATION
// ========================================

function scrollToSection(id) {

    const element =
        document.getElementById(id);

    if (!element) {
        return;
    }

    element.scrollIntoView({
        behavior: "smooth",
        block: "start"
    });

}



// ========================================
// LOAD PESANAN
// ========================================

function loadOrders() {

    fetch("get_pesanan.php")

        .then(response => {

            if (!response.ok) {

                throw new Error(
                    "Gagal mengambil data pesanan."
                );

            }

            return response.json();

        })


        .then(data => {

            const container =
                document.getElementById(
                    "order-container"
                );


            /*
             * SUMMARY
             */

            let menunggu = 0;

            let diproses = 0;


            data.forEach(order => {

                if (
                    order.status_pemesanan
                    === "menunggu"
                ) {

                    menunggu++;

                }


                if (
                    order.status_pemesanan
                    === "diproses"
                ) {

                    diproses++;

                }

            });


            document.getElementById(
                "summary-menunggu"
            ).textContent = menunggu;


            document.getElementById(
                "summary-diproses"
            ).textContent = diproses;


            document.getElementById(
                "summary-total"
            ).textContent =
                menunggu + diproses;



            /*
             * TIDAK ADA PESANAN
             */

            if (data.length === 0) {

                container.innerHTML = `

                    <div class="empty">

                        <div class="empty-title">
                            Tidak ada pesanan saat ini.
                        </div>

                        <div class="empty-description">
                            Pesanan baru dari pelayan
                            akan muncul secara otomatis.
                        </div>

                    </div>

                `;

                return;

            }



            let html = "";



            /*
             * TAMPILKAN SETIAP PESANAN
             */

            data.forEach(order => {


                let statusClass =
                    order.status_pemesanan;



                /*
                 * BUTTON BATALKAN
                 */

                let cancelButton = `

                    <button
                        type="button"
                        class="button button-cancel"
                        onclick="
                            cancelOrder(
                                ${order.id_pesanan}
                            )
                        "
                    >
                        Batalkan Pesanan
                    </button>

                `;



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


                            <span
                                class="
                                    status
                                    ${statusClass}
                                "
                            >

                                ${order.status_pemesanan}

                            </span>

                        </div>



                        <div class="order-info">

                            <p>

                                <strong>
                                    Waktu:
                                </strong>

                                ${order.waktu}

                            </p>


                            <p>

                                <strong>
                                    Pelanggan:
                                </strong>

                                ${order.jumlah_pelanggan}

                                orang

                            </p>

                        </div>



                        <hr>



                        <div class="order-items">

                            ${order.detail}

                        </div>



                        <div class="order-actions">

                            ${order.action}

                            ${cancelButton}

                        </div>


                    </div>

                `;

            });



            container.innerHTML = html;

        })


        .catch(error => {

            console.error(error);


            document.getElementById(
                "order-container"
            ).innerHTML = `

                <div class="empty">

                    <div class="empty-title">
                        Gagal memuat pesanan.
                    </div>

                    <div class="empty-description">
                        Periksa koneksi server.
                    </div>

                </div>

            `;


            document.getElementById(
                "summary-menunggu"
            ).textContent = "-";


            document.getElementById(
                "summary-diproses"
            ).textContent = "-";


            document.getElementById(
                "summary-total"
            ).textContent = "-";

        });

}



// ========================================
// UPDATE PESANAN
// ========================================

function updateOrder(
    idPesanan,
    status
) {


    let message = "";


    if (
        status === "diproses"
    ) {

        message =
            "Mulai proses pesanan ini?";

    }


    else if (
        status === "selesai"
    ) {

        message =
            "Tandai pesanan ini sebagai selesai?";

    }


    else {

        return;

    }


    if (
        !confirm(message)
    ) {

        return;

    }


    fetch(
        "update_pesanan.php",
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
                "&status=" +
                encodeURIComponent(
                    status
                )

        }
    )


    .then(
        response =>
            response.json()
    )


    .then(data => {

        if (!data.success) {

            alert(
                data.message ||
                "Gagal memperbarui pesanan."
            );

            return;

        }


        loadOrders();

    })


    .catch(error => {

        console.error(error);

        alert(
            "Terjadi kesalahan saat memperbarui pesanan."
        );

    });

}



// ========================================
// BATALKAN PESANAN
// ========================================

function cancelOrder(
    idPesanan
) {


    const yakin = confirm(

        "Batalkan pesanan ini?\n\n" +

        "Stok menu akan dikembalikan " +

        "dan meja akan kembali tersedia."

    );


    if (!yakin) {

        return;

    }


    fetch(
        "batalkan_pesanan.php",
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
                )

        }
    )


    .then(
        response =>
            response.json()
    )


    .then(data => {


        if (!data.success) {

            alert(
                data.message ||
                "Pesanan gagal dibatalkan."
            );

            return;

        }


        alert(
            "Pesanan berhasil dibatalkan."
        );


        loadOrders();

    })


    .catch(error => {

        console.error(error);


        alert(
            "Terjadi kesalahan saat membatalkan pesanan."
        );

    });

}



// ========================================
// LOAD PERTAMA
// ========================================

loadOrders();



// ========================================
// AUTO REFRESH
// ========================================

setInterval(
    loadOrders,
    3000
);


</script>


</body>

</html>