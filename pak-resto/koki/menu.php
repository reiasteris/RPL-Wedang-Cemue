<?php

session_start();

if (!isset($_SESSION['id_pegawai'])) {
    header("Location: ../auth/login.php");
    exit;
}

if ($_SESSION['role'] !== 'koki') {
    die("Akses ditolak.");
}

require_once "../config/database.php";


/*
 * Ambil seluruh menu
 */

$sql = "
    SELECT
        id_menu,
        nama_menu,
        kategori,
        harga,
        stok_menu,
        status_ketersediaan
    FROM menu
    ORDER BY id_menu ASC
";

$result = $conn->query($sql);

?>

<!DOCTYPE html>

<html lang="id">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>Kelola Menu - Pak Resto</title>


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

    color:
        rgba(255,255,255,0.75);
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

    color:
        rgba(255,255,255,0.88);

    text-decoration: none;

    font-size: 15px;

    transition:
        background 0.2s ease,
        color 0.2s ease,
        transform 0.2s ease;
}


.sidebar-link:hover {
    background:
        rgba(255,255,255,0.10);

    color: white;

    transform:
        translateX(3px);
}


.sidebar-link.active {
    background:
        rgba(255,255,255,0.15);

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
   PAGE HEADER
   ======================================== */

.page-header {
    display: flex;

    justify-content: space-between;

    align-items: flex-start;

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
   CARD
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

    align-items: flex-start;

    margin-bottom: 22px;
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
   ADD BUTTON
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

    transform:
        translateY(-1px);
}


.button-success {
    background: #198754;
}


.button-success:hover {
    background: #157347;

    opacity: 1;
}


.button-danger {
    background: #dc3545;
}


.button-danger:hover {
    background: #bb2d3b;

    opacity: 1;
}


/* ========================================
   MENU TABLE
   ======================================== */

.table-wrapper {
    width: 100%;

    overflow-x: auto;

    border:
        1px solid
        #e1e6eb;

    border-radius: 10px;
}


table {
    width: 100%;

    border-collapse: collapse;

    min-width: 850px;
}


th,
td {
    padding: 14px 15px;

    text-align: left;

    border-bottom:
        1px solid
        #e4e8ec;
}


th {
    background: #f3f6f8;

    color: #52677f;

    font-size: 12px;

    text-transform: uppercase;

    letter-spacing: 0.4px;

    font-weight: bold;
}


td {
    color: #263f5d;

    font-size: 14px;
}


tbody tr {
    transition:
        background 0.15s ease;
}


tbody tr:hover {
    background: #f8fafb;
}


tbody tr:last-child td {
    border-bottom: none;
}


/* ========================================
   MENU NAME
   ======================================== */

.menu-name {
    font-weight: bold;

    color: #17345d;
}


.menu-category {
    color: #71839b;
}


/* ========================================
   PRICE
   ======================================== */

.price {
    font-weight: bold;

    color: #17345d;

    white-space: nowrap;
}


/* ========================================
   STOCK
   ======================================== */

.stock {
    font-weight: bold;
}


.stock-low {
    color: #d97706;
}


.stock-empty {
    color: #dc3545;
}


/* ========================================
   STATUS
   ======================================== */

.status {
    display: inline-block;

    padding: 5px 10px;

    border-radius: 20px;

    font-size: 11px;

    font-weight: bold;

    text-transform: uppercase;
}


.status-tersedia {
    background: #d9f1e6;

    color: #17633f;
}


.status-habis {
    background: #fde2e2;

    color: #b4232c;
}


/* ========================================
   ACTIONS
   ======================================== */

.actions {
    display: flex;

    align-items: center;

    gap: 7px;

    white-space: nowrap;
}


.action-button {
    padding: 8px 12px;

    font-size: 13px;
}


/* ========================================
   EMPTY TABLE
   ======================================== */

.empty {
    text-align: center;

    padding: 45px 20px;

    color: #71839b;
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

    .card-header {
        display: block;
    }

    .add-button {
        margin-top: 15px;
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
            class="sidebar-link"
        >
            Antrian Pesanan
        </a>


        <a
            href="menu.php"
            class="sidebar-link active"
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
                Kelola Menu
            </h2>

            <div class="page-description">
                Kelola daftar menu, harga, dan stok restoran.
            </div>

        </div>


        <div class="date-box">

            <?= date('d F Y') ?>

        </div>

    </div>



    <!-- ====================================
         MENU CARD
         ==================================== -->

    <div class="card">


        <div class="card-header">

            <div>

                <h2 class="card-title">
                    Daftar Menu
                </h2>

                <p class="card-description">
                    Tambahkan, ubah, atau hapus menu restoran.
                </p>

            </div>


            <a
                href="tambah_menu.php"
                class="button button-success add-button"
            >
                + Tambah Menu
            </a>

        </div>



        <!-- =================================
             TABLE
             ================================= -->

        <div class="table-wrapper">

            <table>


                <thead>

                    <tr>

                        <th>
                            ID
                        </th>

                        <th>
                            Nama Menu
                        </th>

                        <th>
                            Kategori
                        </th>

                        <th>
                            Harga
                        </th>

                        <th>
                            Stok
                        </th>

                        <th>
                            Status
                        </th>

                        <th>
                            Aksi
                        </th>

                    </tr>

                </thead>



                <tbody>


                <?php if (
                    $result &&
                    $result->num_rows > 0
                ): ?>


                    <?php while (
                        $menu =
                        $result->fetch_assoc()
                    ): ?>


                        <tr>


                            <!-- ID -->

                            <td>

                                <?= $menu['id_menu'] ?>

                            </td>



                            <!-- NAMA -->

                            <td>

                                <div class="menu-name">

                                    <?= htmlspecialchars(
                                        $menu['nama_menu']
                                    ) ?>

                                </div>

                            </td>



                            <!-- KATEGORI -->

                            <td>

                                <div class="menu-category">

                                    <?= htmlspecialchars(
                                        $menu['kategori']
                                    ) ?>

                                </div>

                            </td>



                            <!-- HARGA -->

                            <td>

                                <div class="price">

                                    Rp
                                    <?= number_format(
                                        $menu['harga'],
                                        0,
                                        ',',
                                        '.'
                                    ) ?>

                                </div>

                            </td>



                            <!-- STOK -->

                            <td>

                                <?php

                                $stok =
                                    (int)
                                    $menu['stok_menu'];

                                ?>


                                <span
                                    class="
                                        stock

                                        <?php
                                        if ($stok <= 0) {
                                            echo 'stock-empty';
                                        }
                                        elseif ($stok <= 5) {
                                            echo 'stock-low';
                                        }
                                        ?>
                                    "
                                >

                                    <?= $stok ?>

                                </span>

                            </td>



                            <!-- STATUS -->

                            <td>


                                <?php if (
                                    $menu[
                                        'status_ketersediaan'
                                    ]
                                    === 'tersedia'
                                ): ?>


                                    <span
                                        class="
                                            status
                                            status-tersedia
                                        "
                                    >
                                        Tersedia
                                    </span>


                                <?php else: ?>


                                    <span
                                        class="
                                            status
                                            status-habis
                                        "
                                    >
                                        Habis
                                    </span>


                                <?php endif; ?>


                            </td>



                            <!-- ACTION -->

                            <td>

                                <div class="actions">


                                    <a
                                        href="
                                            edit_menu.php?id=
                                            <?= $menu['id_menu'] ?>
                                        "
                                        class="
                                            button
                                            action-button
                                        "
                                    >
                                        Ubah
                                    </a>


                                    <a
                                        href="
                                            hapus_menu.php?id=
                                            <?= $menu['id_menu'] ?>
                                        "
                                        class="
                                            button
                                            button-danger
                                            action-button
                                        "
                                        onclick="
                                            return confirm(
                                                'Yakin ingin menghapus menu ini?'
                                            );
                                        "
                                    >
                                        Hapus
                                    </a>


                                </div>

                            </td>


                        </tr>


                    <?php endwhile; ?>


                <?php else: ?>


                    <tr>

                        <td
                            colspan="7"
                            class="empty"
                        >

                            Belum ada menu
                            yang tersedia.

                        </td>

                    </tr>


                <?php endif; ?>


                </tbody>


            </table>

        </div>


    </div>


</main>


</div>


</body>

</html>