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


require_once "../config/database.php";


// ========================================
// DATA MEJA
// ========================================

$sql_meja = "
    SELECT
        id_meja,
        nomor_meja,
        kapasitas,
        status_meja
    FROM meja
    ORDER BY id_meja ASC
";

$result_meja = $conn->query($sql_meja);


if (!$result_meja) {

    die(
        "Gagal mengambil data meja: "
        . htmlspecialchars($conn->error)
    );

}


// ========================================
// DATA MENU
// ========================================

$sql_menu = "
    SELECT
        id_menu,
        nama_menu,
        kategori,
        harga,
        stok_menu,
        status_ketersediaan
    FROM menu
    ORDER BY kategori ASC, nama_menu ASC
";

$result_menu = $conn->query($sql_menu);


if (!$result_menu) {

    die(
        "Gagal mengambil data menu: "
        . htmlspecialchars($conn->error)
    );

}


// ========================================
// RINGKASAN MEJA
// ========================================

$sql_meja_summary = "
    SELECT

        SUM(
            CASE
                WHEN status_meja = 'tersedia'
                THEN 1
                ELSE 0
            END
        ) AS meja_tersedia,

        SUM(
            CASE
                WHEN status_meja = 'terisi'
                THEN 1
                ELSE 0
            END
        ) AS meja_terisi

    FROM meja
";

$result_meja_summary =
    $conn->query($sql_meja_summary);

$meja_summary =
    $result_meja_summary->fetch_assoc();


$meja_tersedia =
    (int) ($meja_summary['meja_tersedia'] ?? 0);

$meja_terisi =
    (int) ($meja_summary['meja_terisi'] ?? 0);


// ========================================
// PESANAN SEDANG DIPROSES
// ========================================

$sql_processing = "
    SELECT COUNT(*) AS total
    FROM pesanan
    WHERE status_pemesanan = 'diproses'
";

$result_processing =
    $conn->query($sql_processing);

$processing_data =
    $result_processing->fetch_assoc();

$pesanan_diproses =
    (int) ($processing_data['total'] ?? 0);


// ========================================
// TANGGAL
// ========================================

$hari = [
    'Sunday'    => 'Minggu',
    'Monday'    => 'Senin',
    'Tuesday'   => 'Selasa',
    'Wednesday' => 'Rabu',
    'Thursday'  => 'Kamis',
    'Friday'    => 'Jumat',
    'Saturday'  => 'Sabtu'
];

$bulan = [
    1  => 'Januari',
    2  => 'Februari',
    3  => 'Maret',
    4  => 'April',
    5  => 'Mei',
    6  => 'Juni',
    7  => 'Juli',
    8  => 'Agustus',
    9  => 'September',
    10 => 'Oktober',
    11 => 'November',
    12 => 'Desember'
];

$hari_ini =
    $hari[date('l')];

$tanggal_hari_ini =
    date('j')
    . ' '
    . $bulan[(int) date('n')]
    . ' '
    . date('Y');

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
    Pelayan | Pak Resto
</title>


<style>

/* =========================================
   GLOBAL
   ========================================= */

* {
    box-sizing: border-box;
}


body {

    margin: 0;

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
   APP LAYOUT
   ========================================= */

.app {

    min-height:
        100vh;

    display:
        flex;

}


/* =========================================
   SIDEBAR
   ========================================= */

.sidebar {

    width:
        245px;

    min-height:
        100vh;

    position:
        fixed;

    left: 0;

    top: 0;

    bottom: 0;

    display:
        flex;

    flex-direction:
        column;

    background:
        linear-gradient(
            180deg,
            #243b64 0%,
            #245776 60%,
            #187f86 100%
        );

    color:
        white;

    padding:
        28px 20px;

}


/* =========================================
   LOGO
   ========================================= */

.logo-area {

    display:
        flex;

    align-items:
        center;

    gap:
        12px;

    padding:
        5px 8px 30px;

}


.logo {

    width:
        45px;

    height:
        45px;

    border-radius:
        13px;

    display:
        flex;

    align-items:
        center;

    justify-content:
        center;

    background:
        rgba(255,255,255,0.16);

    border:
        1px solid
        rgba(255,255,255,0.25);

    font-size:
        16px;

    font-weight:
        900;

}


.logo-name {

    font-size:
        19px;

    font-weight:
        900;

    letter-spacing:
        -0.4px;

}


.logo-subtitle {

    margin-top:
        3px;

    font-size:
        10px;

    color:
        rgba(255,255,255,0.65);

}


/* =========================================
   SIDEBAR MENU
   ========================================= */

.sidebar-menu {

    display:
        flex;

    flex-direction:
        column;

    gap:
        4px;

}


.sidebar-item {

    padding:
        12px 14px;

    border-radius:
        8px;

    color:
        rgba(255,255,255,0.78);

    font-size:
        13px;

    text-decoration:
        none;

    background:
        transparent;

    transition:
        background 0.2s ease,
        color 0.2s ease;

}


/* Semua menu mendapat hover */

.sidebar-item:hover {

    color:
        white;

    background:
        rgba(255,255,255,0.10);

}


/* Menu aktif juga tetap transparan */

.sidebar-item.active {

    color:
        white;

    background:
        transparent;

}


/* Aktif + hover */

.sidebar-item.active:hover {

    color:
        white;

    background:
        rgba(255,255,255,0.10);

}


/* =========================================
   SIDEBAR USER
   ========================================= */

.sidebar-bottom {

    margin-top:
        auto;

}


.user-card {

    padding:
        15px;

    border-radius:
        12px;

    background:
        rgba(255,255,255,0.09);

    border:
        1px solid
        rgba(255,255,255,0.10);

}


.user-name {

    font-weight:
        bold;

    font-size:
        13px;

}


.user-role {

    margin-top:
        4px;

    font-size:
        11px;

    color:
        rgba(255,255,255,0.65);

}


.logout {

    display:
        block;

    margin-top:
        12px;

    text-align:
        center;

    padding:
        9px;

    border-radius:
        8px;

    color:
        white;

    text-decoration:
        none;

    font-size:
        12px;

    background:
        rgba(255,255,255,0.10);

}


.logout:hover {

    background:
        rgba(255,255,255,0.18);

}


/* =========================================
   MAIN
   ========================================= */

.main {

    margin-left:
        245px;

    width:
        calc(100% - 245px);

    min-height:
        100vh;

}


/* =========================================
   TOP BAR
   ========================================= */

.topbar {

    height:
        92px;

    padding:
        0 40px;

    display:
        flex;

    align-items:
        center;

    justify-content:
        space-between;

    background:
        white;

    border-bottom:
        1px solid
        #e6ebef;

}


.page-title {

    margin: 0;

    font-size:
        23px;

    color:
        #1d2939;

}


.page-description {

    margin-top:
        5px;

    font-size:
        12px;

    color:
        #8792a2;

}


.date-box {

    text-align:
        right;

}


.date-day {

    font-size:
        12px;

    color:
        #8a94a6;

}


.date-value {

    margin-top:
        3px;

    font-size:
        13px;

    font-weight:
        bold;

    color:
        #243b64;

}


/* =========================================
   CONTENT
   ========================================= */

.content {

    max-width:
        1180px;

    margin:
        0 auto;

    padding:
        30px 35px 60px;

}


/* =========================================
   SUMMARY
   ========================================= */

.summary-grid {

    display:
        grid;

    grid-template-columns:
        repeat(3, 1fr);

    gap:
        16px;

    margin-bottom:
        25px;

}


.summary-card {

    background:
        white;

    border:
        1px solid
        #e4e9ed;

    border-radius:
        14px;

    padding:
        20px;

    box-shadow:
        0 4px 15px
        rgba(25,55,75,0.05);

}


.summary-top {

    display:
        flex;

    align-items:
        center;

    justify-content:
        space-between;

}


.summary-label {

    font-size:
        12px;

    color:
        #7c8796;

}


.summary-icon {

    width:
        35px;

    height:
        35px;

    display:
        flex;

    align-items:
        center;

    justify-content:
        center;

    border-radius:
        9px;

    background:
        #edf6f7;

    color:
        #159c9e;

    font-size:
        15px;

}


.summary-number {

    margin-top:
        10px;

    font-size:
        28px;

    font-weight:
        800;

    color:
        #243b64;

}


.summary-note {

    margin-top:
        4px;

    font-size:
        11px;

    color:
        #9aa3ae;

}


/* =========================================
   PROCESS INFO
   ========================================= */

.process-info {

    margin-bottom:
        22px;

    padding:
        17px 20px;

    border-radius:
        12px;

    background:
        linear-gradient(
            100deg,
            #eef8f8,
            #f2f7fb
        );

    border:
        1px solid
        #dbeaec;

    color:
        #536173;

    font-size:
        12px;

    line-height:
        1.5;

}


.process-info strong {

    color:
        #243b64;

}


/* =========================================
   STEPPER
   ========================================= */

.stepper {

    display:
        flex;

    align-items:
        center;

    background:
        white;

    border:
        1px solid
        #e4e9ed;

    border-radius:
        14px;

    padding:
        16px 20px;

    margin-bottom:
        22px;

    box-shadow:
        0 4px 15px
        rgba(30,55,75,0.05);

}


.step {

    display:
        flex;

    align-items:
        center;

    gap:
        8px;

    font-size:
        12px;

    font-weight:
        bold;

    color:
        #9aa3ae;

    white-space:
        nowrap;

}


.step-number {

    width:
        28px;

    height:
        28px;

    border-radius:
        50%;

    display:
        flex;

    align-items:
        center;

    justify-content:
        center;

    background:
        #e9eef2;

    color:
        #8a94a6;

    font-size:
        11px;

}


.step.active {

    color:
        #243b64;

}


.step.active
.step-number {

    background:
        linear-gradient(
            135deg,
            #243b64,
            #159c9e
        );

    color:
        white;

}


.step-line {

    flex:
        1;

    height:
        1px;

    background:
        #dfe5ea;

    margin:
        0 12px;

}


/* =========================================
   CARD
   ========================================= */

.card {

    background:
        white;

    border:
        1px solid
        #e4e9ed;

    border-radius:
        15px;

    padding:
        27px;

    margin-bottom:
        20px;

    box-shadow:
        0 4px 18px
        rgba(30,55,75,0.05);

}


.card-title {

    margin:
        0;

    font-size:
        19px;

    color:
        #1d2939;

}


.card-description {

    margin:
        6px 0 22px;

    font-size:
        12px;

    color:
        #8a94a6;

}


/* =========================================
   CUSTOMER
   ========================================= */

.customer-section {

    display:
        flex;

    justify-content:
        space-between;

    align-items:
        center;

    padding:
        18px;

    border-radius:
        12px;

    background:
        #f6f9fa;

    border:
        1px solid
        #e3eaee;

}


.customer-label {

    font-size:
        13px;

    font-weight:
        bold;

    color:
        #435166;

}


.customer-help {

    margin-top:
        5px;

    font-size:
        11px;

    color:
        #8a94a6;

}


.counter {

    display:
        flex;

    align-items:
        center;

    gap:
        7px;

}


.counter-button {

    width:
        38px;

    height:
        38px;

    border:
        none;

    border-radius:
        8px;

    background:
        #e7eef3;

    color:
        #243b64;

    font-size:
        18px;

    font-weight:
        bold;

    cursor:
        pointer;

}


.counter-button:hover {

    background:
        #dbe6ec;

}


#jumlah_pelanggan {

    width:
        60px;

    height:
        38px;

    text-align:
        center;

    border:
        1px solid
        #d7dfe5;

    border-radius:
        8px;

    font-size:
        14px;

    font-weight:
        bold;

    color:
        #243b64;

}


/* =========================================
   TABLE
   ========================================= */

.table-grid {

    display:
        grid;

    grid-template-columns:
        repeat(
            auto-fill,
            minmax(155px, 1fr)
        );

    gap:
        13px;

}


.table-option {

    border:
        2px solid
        #e1e7eb;

    border-radius:
        12px;

    padding:
        16px;

    background:
        white;

    cursor:
        pointer;

    transition:
        0.2s;

}


.table-option:hover {

    transform:
        translateY(-2px);

    border-color:
        #8ccfd0;

}


.table-option.selected {

    border-color:
        #159c9e;

    background:
        #f0fafb;

    box-shadow:
        0 5px 15px
        rgba(21,156,158,0.10);

}


.table-option.disabled {

    opacity:
        0.30;

    cursor:
        not-allowed;

    transform:
        none;

}


.table-top {

    display:
        flex;

    align-items:
        center;

    justify-content:
        space-between;

}


.table-number {

    font-size:
        16px;

    font-weight:
        800;

    color:
        #243b64;

}


.table-icon {

    width:
        28px;

    height:
        28px;

    border-radius:
        7px;

    display:
        flex;

    align-items:
        center;

    justify-content:
        center;

    background:
        #edf6f7;

    color:
        #159c9e;

    font-size:
        12px;

}


.table-capacity {

    margin-top:
        10px;

    font-size:
        11px;

    color:
        #7c8796;

}


.table-status {

    display:
        inline-block;

    margin-top:
        10px;

    padding:
        4px 8px;

    border-radius:
        20px;

    background:
        #e4f7ee;

    color:
        #16804d;

    font-size:
        9px;

    font-weight:
        bold;

}


/* =========================================
   MENU
   ========================================= */

.menu-grid {

    display:
        grid;

    grid-template-columns:
        repeat(
            auto-fill,
            minmax(245px, 1fr)
        );

    gap:
        14px;

}


.menu-item {

    border:
        1px solid
        #e1e6eb;

    border-radius:
        12px;

    padding:
        18px;

    background:
        white;

}


.menu-item.habis {

    opacity:
        0.55;

    background:
        #f4f5f6;

}


.menu-top {

    display:
        flex;

    align-items:
        flex-start;

    justify-content:
        space-between;

    gap:
        8px;

}


.menu-name {

    font-size:
        15px;

    font-weight:
        bold;

    color:
        #243b64;

}


.menu-category {

    margin-top:
        4px;

    font-size:
        11px;

    color:
        #8a94a6;

}


.stock-badge {

    padding:
        4px 7px;

    border-radius:
        20px;

    background:
        #e4f7ee;

    color:
        #16804d;

    font-size:
        9px;

    font-weight:
        bold;

}


.stock-badge.empty {

    background:
        #ffe7e7;

    color:
        #b42318;

}


.menu-price {

    margin-top:
        14px;

    font-size:
        15px;

    font-weight:
        bold;

}


.menu-stock {

    margin-top:
        4px;

    font-size:
        11px;

    color:
        #7c8796;

}


.quantity {

    display:
        flex;

    align-items:
        center;

    justify-content:
        space-between;

    margin-top:
        15px;

    padding-top:
        13px;

    border-top:
        1px solid
        #edf0f2;

}


.quantity-label {

    font-size:
        11px;

    font-weight:
        bold;

    color:
        #536173;

}


.quantity-control {

    display:
        flex;

    align-items:
        center;

    gap:
        5px;

}


.quantity-button {

    width:
        29px;

    height:
        29px;

    border:
        none;

    border-radius:
        7px;

    background:
        #edf2f5;

    color:
        #243b64;

    font-weight:
        bold;

    cursor:
        pointer;

}


.quantity-input {

    width:
        42px;

    height:
        29px;

    text-align:
        center;

    border:
        1px solid
        #d9e0e6;

    border-radius:
        7px;

}


/* =========================================
   WARNING
   ========================================= */

.warning {

    display:
        none;

    margin-top:
        14px;

    padding:
        10px 12px;

    border-radius:
        8px;

    background:
        #fff7df;

    border:
        1px solid
        #f2df9f;

    color:
        #8a6500;

    font-size:
        11px;

}


/* =========================================
   SUBMIT
   ========================================= */

.submit-area {

    display:
        flex;

    align-items:
        center;

    justify-content:
        space-between;

    gap:
        20px;

}


.submit-info {

    font-size:
        11px;

    color:
        #8a94a6;

}


.button {

    min-width:
        190px;

    padding:
        13px 20px;

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

    background-size:
        200% 100%;

    color:
        white;

    font-size:
        13px;

    font-weight:
        bold;

    cursor:
        pointer;

    transition:
        0.25s;

}


.button:hover:not(:disabled) {

    background-position:
        100% 0;

    transform:
        translateY(-1px);

}


.button:disabled {

    background:
        #b7c0c8;

    cursor:
        not-allowed;

}


/* =========================================
   EMPTY
   ========================================= */

.empty {

    text-align:
        center;

    padding:
        30px;

    color:
        #8a94a6;

    font-size:
        12px;

}


/* =========================================
   RESPONSIVE
   ========================================= */

@media (max-width: 850px) {

    .sidebar {

        width:
            205px;

    }


    .main {

        margin-left:
            205px;

        width:
            calc(100% - 205px);

    }


    .topbar {

        padding:
            0 25px;

    }


    .content {

        padding:
            25px 20px 50px;

    }

}


@media (max-width: 700px) {

    .app {

        display:
            block;

    }


    .sidebar {

        position:
            relative;

        width:
            100%;

        min-height:
            auto;

        padding:
            15px 18px;

    }


    .logo-area {

        padding:
            3px 5px 15px;

    }


    .sidebar-menu {

        display:
            none;

    }


    .sidebar-bottom {

        margin-top:
            0;

    }


    .user-card {

        display:
            flex;

        align-items:
            center;

        justify-content:
            space-between;

        gap:
            10px;

    }


    .logout {

        margin-top:
            0;

        padding:
            8px 13px;

    }


    .main {

        margin-left:
            0;

        width:
            100%;

    }


    .topbar {

        height:
            auto;

        padding:
            20px;

    }


    .summary-grid {

        grid-template-columns:
            1fr;

    }


    .stepper {

        overflow-x:
            auto;

    }


    .customer-section {

        flex-direction:
            column;

        align-items:
            flex-start;

    }


    .submit-area {

        flex-direction:
            column;

        align-items:
            stretch;

    }


    .button {

        width:
            100%;

    }

}

</style>

</head>


<body>


<div class="app">


<!-- =========================================
     SIDEBAR
     ========================================= -->

<aside class="sidebar">


    <div class="logo-area">


        <div class="logo">

            PR

        </div>


        <div>

            <div class="logo-name">

                PAK RESTO

            </div>


            <div class="logo-subtitle">

                SISTEM MANAJEMEN RESTORAN

            </div>

        </div>


    </div>


    <nav class="sidebar-menu">


        <a
            href="#pelanggan"
            class="sidebar-item active"
        >

            Jumlah Pelanggan

        </a>


        <a
            href="#meja"
            class="sidebar-item"
        >

            Status Meja

        </a>


        <a
            href="#menu"
            class="sidebar-item"
        >

            Daftar Menu

        </a>


    </nav>


    <div class="sidebar-bottom">


        <div class="user-card">


            <div>


                <div class="user-name">

                    <?= htmlspecialchars(
                        $_SESSION['nama_pegawai']
                    ) ?>

                </div>


                <div class="user-role">

                    Pelayan

                </div>


            </div>


            <a
                href="../auth/logout.php"
                class="logout"
            >

                Logout

            </a>


        </div>


    </div>


</aside>



<!-- =========================================
     MAIN
     ========================================= -->

<main class="main">


    <!-- =====================================
         TOPBAR
         ===================================== -->

    <header class="topbar">


        <div>

            <h1 class="page-title">

                Buat Pesanan

            </h1>


            <div class="page-description">

                Kelola meja, menu, dan pesanan pelanggan.

            </div>

        </div>


        <div class="date-box">


            <div class="date-day">

                <?= $hari_ini ?>

            </div>


            <div class="date-value">

                <?= $tanggal_hari_ini ?>

            </div>


        </div>


    </header>



    <div class="content">


        <!-- =================================
             SUMMARY
             ================================= -->

        <div class="summary-grid">


            <div class="summary-card">


                <div class="summary-top">


                    <div class="summary-label">

                        Meja Tersedia

                    </div>


                    <div class="summary-icon">

                        □

                    </div>


                </div>


                <div class="summary-number">

                    <?= $meja_tersedia ?>

                </div>


                <div class="summary-note">

                    Siap digunakan pelanggan

                </div>


            </div>



            <div class="summary-card">


                <div class="summary-top">


                    <div class="summary-label">

                        Meja Terisi

                    </div>


                    <div class="summary-icon">

                        ◇

                    </div>


                </div>


                <div class="summary-number">

                    <?= $meja_terisi ?>

                </div>


                <div class="summary-note">

                    Sedang digunakan pelanggan

                </div>


            </div>



            <div class="summary-card">


                <div class="summary-top">


                    <div class="summary-label">

                        Pesanan Diproses

                    </div>


                    <div class="summary-icon">

                        ≋

                    </div>


                </div>


                <div class="summary-number">

                    <?= $pesanan_diproses ?>

                </div>


                <div class="summary-note">

                    Sedang dikerjakan di dapur

                </div>


            </div>


        </div>



        <!-- =================================
             PROCESS INFORMATION
             ================================= -->

        <div class="process-info">

            <strong>

                Informasi pelayanan:

            </strong>

            Gunakan jumlah pelanggan untuk
            menentukan meja yang sesuai.
            Pesanan yang sudah disimpan akan
            diteruskan ke dapur untuk diproses.
            Jumlah pesanan yang sedang dikerjakan
            dapat menjadi indikator antrean dapur.

        </div>



        <!-- =================================
             STEPPER
             ================================= -->

        <div class="stepper">


            <div class="step active">

                <div class="step-number">

                    1

                </div>

                Pelanggan

            </div>


            <div class="step-line"></div>


            <div class="step active">

                <div class="step-number">

                    2

                </div>

                Meja

            </div>


            <div class="step-line"></div>


            <div class="step active">

                <div class="step-number">

                    3

                </div>

                Menu

            </div>


            <div class="step-line"></div>


            <div class="step">

                <div class="step-number">

                    4

                </div>

                Konfirmasi

            </div>


        </div>



        <!-- =================================
             FORM
             ================================= -->

        <form
            method="POST"
            action="simpan_pesanan.php"
            id="order-form"
        >


        <!-- =================================
             CUSTOMER
             ================================= -->

        <div
            class="card"
            id="pelanggan"
        >


            <h2 class="card-title">

                Jumlah Pelanggan

            </h2>


            <p class="card-description">

                Tentukan jumlah pelanggan sebelum
                memilih meja.

            </p>


            <div class="customer-section">


                <div>

                    <div class="customer-label">

                        Berapa orang yang akan duduk?

                    </div>


                    <div class="customer-help">

                        Sistem akan menyesuaikan meja
                        berdasarkan kapasitas.

                    </div>

                </div>


                <div class="counter">


                    <button
                        type="button"
                        class="counter-button"
                        onclick="changeCustomers(-1)"
                    >

                        −

                    </button>


                    <input
                        type="number"
                        id="jumlah_pelanggan"
                        name="jumlah_pelanggan"
                        min="1"
                        value="1"
                        required
                    >


                    <button
                        type="button"
                        class="counter-button"
                        onclick="changeCustomers(1)"
                    >

                        +

                    </button>


                </div>


            </div>


            <div
                id="table-warning"
                class="warning"
            >

                Tidak ada meja yang sesuai
                dengan jumlah pelanggan.

            </div>


        </div>



        <!-- =================================
             TABLE
             ================================= -->

        <div
            class="card"
            id="meja"
        >


            <h2 class="card-title">

                Pilih Meja

            </h2>


            <p class="card-description">

                Pilih meja tersedia dengan kapasitas
                yang sesuai.

            </p>


            <div class="table-grid">


            <?php

            $ada_meja = false;


            while (
                $meja =
                $result_meja->fetch_assoc()
            ) {

                $ada_meja = true;


                $tersedia =
                    $meja['status_meja']
                    === 'tersedia';

            ?>


                <div
                    class="table-option
                    <?= !$tersedia
                        ? 'disabled'
                        : '' ?>"
                    data-capacity="<?= htmlspecialchars(
                        $meja['kapasitas']
                    ) ?>"
                    onclick="selectTable(this)"
                >


                    <div class="table-top">


                        <div class="table-number">

                            Meja
                            <?= htmlspecialchars(
                                $meja['nomor_meja']
                            ) ?>

                        </div>


                        <div class="table-icon">

                            □

                        </div>


                    </div>


                    <div class="table-capacity">

                        Kapasitas:
                        <?= htmlspecialchars(
                            $meja['kapasitas']
                        ) ?>
                        orang

                    </div>


                    <div class="table-status">

                        TERSEDIA

                    </div>


                    <input
                        type="radio"
                        name="id_meja"
                        value="<?= htmlspecialchars(
                            $meja['id_meja']
                        ) ?>"
                        style="display:none;"
                        <?= !$tersedia
                            ? 'disabled'
                            : '' ?>
                    >


                </div>


            <?php

            }


            if (!$ada_meja) {

            ?>


                <div class="empty">

                    Tidak ada meja.

                </div>


            <?php

            }

            ?>


            </div>


        </div>



        <!-- =================================
             MENU
             ================================= -->

        <div
            class="card"
            id="menu"
        >


            <h2 class="card-title">

                Pilih Menu

            </h2>


            <p class="card-description">

                Tentukan jumlah makanan dan minuman
                yang dipesan.

            </p>


            <div class="menu-grid">


            <?php

            $ada_menu = false;


            while (
                $menu =
                $result_menu->fetch_assoc()
            ) {

                $ada_menu = true;


                $habis =
                    (
                        $menu['stok_menu'] <= 0
                        ||
                        $menu['status_ketersediaan']
                        === 'habis'
                    );

            ?>


                <div
                    class="menu-item
                    <?= $habis
                        ? 'habis'
                        : '' ?>"
                >


                    <div class="menu-top">


                        <div>


                            <div class="menu-name">

                                <?= htmlspecialchars(
                                    $menu['nama_menu']
                                ) ?>

                            </div>


                            <div class="menu-category">

                                <?= htmlspecialchars(
                                    $menu['kategori']
                                ) ?>

                            </div>


                        </div>


                        <div
                            class="stock-badge
                            <?= $habis
                                ? 'empty'
                                : '' ?>"
                        >

                            <?= $habis
                                ? 'HABIS'
                                : 'TERSEDIA' ?>

                        </div>


                    </div>


                    <div class="menu-price">

                        Rp
                        <?= number_format(
                            $menu['harga'],
                            0,
                            ',',
                            '.'
                        ) ?>

                    </div>


                    <div class="menu-stock">

                        Stok:
                        <?= htmlspecialchars(
                            $menu['stok_menu']
                        ) ?>

                    </div>


                    <div class="quantity">


                        <span class="quantity-label">

                            Jumlah

                        </span>


                        <div class="quantity-control">


                            <button
                                type="button"
                                class="quantity-button"
                                onclick="changeQuantity(
                                    this,
                                    -1
                                )"
                                <?= $habis
                                    ? 'disabled'
                                    : '' ?>
                            >

                                −

                            </button>


                            <input
                                type="number"
                                class="quantity-input"
                                name="jumlah[<?= htmlspecialchars(
                                    $menu['id_menu']
                                ) ?>]"
                                min="0"
                                max="<?= htmlspecialchars(
                                    $menu['stok_menu']
                                ) ?>"
                                value="0"
                                <?= $habis
                                    ? 'disabled'
                                    : '' ?>
                            >


                            <button
                                type="button"
                                class="quantity-button"
                                onclick="changeQuantity(
                                    this,
                                    1
                                )"
                                <?= $habis
                                    ? 'disabled'
                                    : '' ?>
                            >

                                +

                            </button>


                        </div>


                    </div>


                </div>


            <?php

            }


            if (!$ada_menu) {

            ?>


                <div class="empty">

                    Belum ada menu.

                </div>


            <?php

            }

            ?>


            </div>


        </div>



        <!-- =================================
             SUBMIT
             ================================= -->

        <div class="card submit-area">


            <div class="submit-info">

                Pastikan meja dan minimal satu
                menu telah dipilih.

            </div>


            <button
                type="submit"
                class="button"
                id="submit-button"
                disabled
            >

                SIMPAN PESANAN
                &nbsp; →

            </button>


        </div>


        </form>


    </div>


</main>

</div>



<script>


// ========================================
// CUSTOMER
// ========================================

function changeCustomers(change) {

    const input =
        document.getElementById(
            "jumlah_pelanggan"
        );


    let value =
        parseInt(input.value) || 1;


    value += change;


    if (value < 1) {

        value = 1;

    }


    input.value = value;


    updateTables();

}



// ========================================
// SELECT TABLE
// ========================================

function selectTable(element) {


    if (
        element.classList.contains(
            "disabled"
        )
    ) {

        return;

    }


    const radio =
        element.querySelector(
            'input[type="radio"]'
        );


    if (!radio || radio.disabled) {

        return;

    }


    document
        .querySelectorAll(
            ".table-option"
        )
        .forEach(
            function(table) {

                table.classList.remove(
                    "selected"
                );

            }
        );


    element.classList.add(
        "selected"
    );


    radio.checked = true;


    checkForm();

}



// ========================================
// FILTER TABLE
// ========================================

function updateTables() {


    const jumlahPelanggan =
        parseInt(
            document.getElementById(
                "jumlah_pelanggan"
            ).value
        ) || 0;


    let adaMeja = false;


    document
        .querySelectorAll(
            ".table-option"
        )
        .forEach(
            function(table) {


                const kapasitas =
                    parseInt(
                        table.dataset.capacity
                    );


                const radio =
                    table.querySelector(
                        'input[type="radio"]'
                    );


                if (
                    !radio.disabled
                    &&
                    kapasitas >=
                    jumlahPelanggan
                ) {


                    table.classList.remove(
                        "disabled"
                    );


                    adaMeja = true;


                }

                else {


                    table.classList.add(
                        "disabled"
                    );


                    if (
                        radio.checked
                    ) {

                        radio.checked =
                            false;

                        table.classList.remove(
                            "selected"
                        );

                    }

                }

            }
        );


    const warning =
        document.getElementById(
            "table-warning"
        );


    warning.style.display =
        adaMeja
            ? "none"
            : "block";


    checkForm();

}



// ========================================
// QUANTITY
// ========================================

function changeQuantity(
    button,
    change
) {


    const container =
        button.parentElement;


    const input =
        container.querySelector(
            ".quantity-input"
        );


    if (!input || input.disabled) {

        return;

    }


    let value =
        parseInt(input.value) || 0;


    const max =
        parseInt(input.max);


    value += change;


    if (value < 0) {

        value = 0;

    }


    if (
        !isNaN(max)
        &&
        value > max
    ) {

        value = max;

    }


    input.value =
        value;


    checkForm();

}



// ========================================
// CHECK FORM
// ========================================

function checkForm() {


    const selectedTable =
        document.querySelector(
            'input[name="id_meja"]:checked'
        );


    const jumlahPelanggan =
        parseInt(
            document.getElementById(
                "jumlah_pelanggan"
            ).value
        ) || 0;


    const quantities =
        document.querySelectorAll(
            'input[name^="jumlah["]'
        );


    let adaMenu = false;


    quantities.forEach(
        function(input) {


            if (
                !input.disabled
                &&
                parseInt(input.value) > 0
            ) {

                adaMenu = true;

            }

        }
    );


    const button =
        document.getElementById(
            "submit-button"
        );


    button.disabled = !(
        jumlahPelanggan > 0
        &&
        selectedTable
        &&
        adaMenu
    );

}



// ========================================
// INPUT EVENTS
// ========================================

document
    .getElementById(
        "jumlah_pelanggan"
    )
    .addEventListener(
        "input",
        updateTables
    );


document
    .querySelectorAll(
        'input[name^="jumlah["]'
    )
    .forEach(
        function(input) {


            input.addEventListener(
                "input",
                checkForm
            );


            input.addEventListener(
                "change",
                checkForm
            );

        }
    );



// ========================================
// INITIALIZE
// ========================================

updateTables();

checkForm();


</script>


</body>

</html>