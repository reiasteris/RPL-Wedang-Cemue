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

if ($_SESSION['role'] !== 'pelayan') {

    die("Akses ditolak.");

}


// ========================================
// AMBIL ID PESANAN
// ========================================

$id_pesanan =
    (int) ($_GET['id_pesanan'] ?? 0);

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
    Pesanan Berhasil | Pak Resto
</title>


<style>

/* =========================================
   GLOBAL
   ========================================= */

* {

    box-sizing:
        border-box;

}


html {

    scroll-behavior:
        smooth;

}


body {

    margin:
        0;

    min-height:
        100vh;

    font-family:
        Arial,
        Helvetica,
        sans-serif;

    background:
        #f4f7f9;

    color:
        #1f2937;

}


/* =========================================
   TOP HEADER
   ========================================= */

.header {

    height:
        78px;

    display:
        flex;

    align-items:
        center;

    justify-content:
        space-between;

    padding:
        0 7%;

    color:
        white;

    background:
        linear-gradient(
            100deg,
            #243b64 0%,
            #245776 55%,
            #159c9e 100%
        );

    box-shadow:
        0 4px 15px
        rgba(20,50,70,0.12);

}


.brand {

    display:
        flex;

    align-items:
        center;

    gap:
        11px;

}


.logo {

    width:
        39px;

    height:
        39px;

    display:
        flex;

    align-items:
        center;

    justify-content:
        center;

    border-radius:
        10px;

    background:
        rgba(255,255,255,0.14);

    border:
        1px solid
        rgba(255,255,255,0.25);

    font-size:
        13px;

    font-weight:
        900;

}


.brand-name {

    font-size:
        17px;

    font-weight:
        900;

    letter-spacing:
        -0.3px;

}


.brand-role {

    margin-top:
        2px;

    font-size:
        10px;

    color:
        rgba(255,255,255,0.72);

}


/* =========================================
   LOGOUT
   ========================================= */

.logout {

    padding:
        8px 15px;

    border:
        1px solid
        rgba(255,255,255,0.30);

    border-radius:
        8px;

    color:
        white;

    text-decoration:
        none;

    font-size:
        12px;

    background:
        rgba(255,255,255,0.08);

    transition:
        0.2s;

}


.logout:hover {

    background:
        rgba(255,255,255,0.17);

}


/* =========================================
   MAIN
   ========================================= */

.page {

    min-height:
        calc(100vh - 78px);

    display:
        flex;

    align-items:
        center;

    justify-content:
        center;

    padding:
        45px 20px;

}


.success-card {

    width:
        100%;

    max-width:
        650px;

    padding:
        45px 45px 40px;

    text-align:
        center;

    background:
        white;

    border:
        1px solid
        #e2e8ed;

    border-radius:
        18px;

    box-shadow:
        0 10px 35px
        rgba(30,55,75,0.08);

}


/* =========================================
   SUCCESS ICON
   ========================================= */

.success-icon {

    width:
        72px;

    height:
        72px;

    margin:
        0 auto 22px;

    display:
        flex;

    align-items:
        center;

    justify-content:
        center;

    border-radius:
        50%;

    background:
        linear-gradient(
            135deg,
            #dff7f2,
            #dff1f7
        );

    border:
        1px solid
        #c7e9e6;

    color:
        #159c9e;

    font-size:
        32px;

    font-weight:
        bold;

}


/* =========================================
   TITLE
   ========================================= */

.success-title {

    margin:
        0;

    color:
        #243b64;

    font-size:
        27px;

    font-weight:
        800;

}


.success-subtitle {

    max-width:
        470px;

    margin:
        10px auto 0;

    color:
        #7c8796;

    font-size:
        13px;

    line-height:
        1.6;

}


/* =========================================
   ORDER NUMBER
   ========================================= */

.order-box {

    margin:
        28px 0;

    padding:
        19px 20px;

    border-radius:
        12px;

    background:
        linear-gradient(
            100deg,
            #f1f7fb,
            #eef9f8
        );

    border:
        1px solid
        #dcecef;

}


.order-label {

    font-size:
        11px;

    color:
        #8792a2;

    text-transform:
        uppercase;

    letter-spacing:
        0.7px;

}


.order-number {

    margin-top:
        7px;

    color:
        #243b64;

    font-size:
        25px;

    font-weight:
        900;

}


/* =========================================
   PROCESS STATUS
   ========================================= */

.status-row {

    display:
        flex;

    align-items:
        center;

    justify-content:
        center;

    gap:
        10px;

    margin:
        25px 0 30px;

}


.status-line {

    width:
        55px;

    height:
        1px;

    background:
        #dce4e9;

}


.status-item {

    display:
        flex;

    align-items:
        center;

    gap:
        6px;

    color:
        #637083;

    font-size:
        11px;

}


.status-dot {

    width:
        9px;

    height:
        9px;

    border-radius:
        50%;

    background:
        #159c9e;

}


.status-dot.pending {

    background:
        #d9e0e5;

}


/* =========================================
   INFORMATION
   ========================================= */

.info {

    margin-bottom:
        28px;

    color:
        #7c8796;

    font-size:
        12px;

    line-height:
        1.6;

}


.info strong {

    color:
        #435166;

}


/* =========================================
   BUTTONS
   ========================================= */

.actions {

    display:
        flex;

    justify-content:
        center;

    gap:
        10px;

}


.button {

    display:
        inline-flex;

    align-items:
        center;

    justify-content:
        center;

    min-width:
        190px;

    padding:
        12px 20px;

    border:
        none;

    border-radius:
        9px;

    background:
        linear-gradient(
            100deg,
            #243b64,
            #245c7c,
            #159c9e
        );

    color:
        white;

    text-decoration:
        none;

    font-size:
        12px;

    font-weight:
        bold;

    transition:
        0.2s;

}


.button:hover {

    transform:
        translateY(-1px);

    box-shadow:
        0 5px 15px
        rgba(36,91,115,0.18);

}


.button-secondary {

    min-width:
        auto;

    background:
        white;

    color:
        #536173;

    border:
        1px solid
        #d9e1e6;

}


.button-secondary:hover {

    background:
        #f6f8f9;

    box-shadow:
        none;

}


/* =========================================
   FOOTER
   ========================================= */

.footer {

    margin-top:
        25px;

    color:
        #a0a8b2;

    font-size:
        10px;

}


/* =========================================
   RESPONSIVE
   ========================================= */

@media (max-width: 600px) {

    .header {

        padding:
            0 20px;

    }


    .brand-role {

        display:
            none;

    }


    .success-card {

        padding:
            35px 22px 30px;

    }


    .success-title {

        font-size:
            23px;

    }


    .status-row {

        flex-wrap:
            wrap;

    }


    .status-line {

        width:
            25px;

    }


    .actions {

        flex-direction:
            column;

    }


    .button {

        width:
            100%;

    }

}

</style>

</head>


<body>


<!-- =========================================
     HEADER
     ========================================= -->

<header class="header">


    <div class="brand">


        <div class="logo">

            PR

        </div>


        <div>

            <div class="brand-name">

                PAK RESTO

            </div>


            <div class="brand-role">

                SISTEM MANAJEMEN RESTORAN

            </div>

        </div>


    </div>


    <a
        href="../auth/logout.php"
        class="logout"
    >

        Logout

    </a>


</header>



<!-- =========================================
     CONTENT
     ========================================= -->

<main class="page">


    <section class="success-card">


        <!-- SUCCESS ICON -->

        <div class="success-icon">

            ✓

        </div>



        <!-- TITLE -->

        <h1 class="success-title">

            Pesanan Berhasil Dicatat

        </h1>


        <p class="success-subtitle">

            Pesanan pelanggan telah berhasil
            disimpan dan diteruskan ke bagian dapur.

        </p>



        <!-- ORDER NUMBER -->

        <?php if ($id_pesanan > 0): ?>


            <div class="order-box">


                <div class="order-label">

                    Nomor Pesanan

                </div>


                <div class="order-number">

                    #<?= $id_pesanan ?>

                </div>


            </div>


        <?php endif; ?>



        <!-- STATUS -->

        <div class="status-row">


            <div class="status-item">

                <span class="status-dot"></span>

                Pesanan Dicatat

            </div>


            <div class="status-line"></div>


            <div class="status-item">

                <span class="status-dot"></span>

                Diteruskan ke Dapur

            </div>


            <div class="status-line"></div>


            <div class="status-item">

                <span class="status-dot pending"></span>

                Diproses

            </div>


        </div>



        <!-- INFORMATION -->

        <div class="info">

            Pesanan sekarang dapat dilihat oleh
            <strong>Koki</strong> melalui dashboard dapur.
            Setelah selesai diproses, pesanan akan
            diteruskan ke tahap pembayaran oleh kasir.

        </div>



        <!-- ACTIONS -->

        <div class="actions">


            <a
                href="index.php"
                class="button"
            >

                Buat Pesanan Baru
                &nbsp; →

            </a>


        </div>



        <div class="footer">

            Pak Resto · Sistem Manajemen Restoran

        </div>


    </section>


</main>


</body>

</html>