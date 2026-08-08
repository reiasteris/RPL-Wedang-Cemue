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

if ($_SESSION['role'] !== 'koki') {

    die("Akses ditolak.");

}


require_once "../config/database.php";



/* ========================================
   PROSES TAMBAH MENU
   ======================================== */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {


    $nama_menu =
        trim($_POST['nama_menu'] ?? '');


    $kategori =
        trim($_POST['kategori'] ?? '');


    $harga =
        (float) ($_POST['harga'] ?? 0);


    $stok_menu =
        (int) ($_POST['stok_menu'] ?? 0);



    /* ====================================
       VALIDASI
       ==================================== */

    if (
        $nama_menu === '' ||
        $kategori === '' ||
        $harga < 0 ||
        $stok_menu < 0
    ) {

        die("Data menu tidak valid.");

    }



    /* ====================================
       STATUS OTOMATIS
       ==================================== */

    $status_ketersediaan =
        ($stok_menu > 0)
            ? 'tersedia'
            : 'habis';



    /* ====================================
       INSERT MENU
       ==================================== */

    $sql = "

        INSERT INTO menu
        (
            nama_menu,
            kategori,
            harga,
            stok_menu,
            status_ketersediaan
        )

        VALUES (?, ?, ?, ?, ?)

    ";


    $stmt =
        $conn->prepare($sql);


    if (!$stmt) {

        die(
            "Gagal menyiapkan query: "
            . htmlspecialchars($conn->error)
        );

    }



    /* ====================================
       BIND PARAMETER
       ==================================== */

    $stmt->bind_param(
        "ssdis",
        $nama_menu,
        $kategori,
        $harga,
        $stok_menu,
        $status_ketersediaan
    );



    /* ====================================
       EXECUTE
       ==================================== */

    if (!$stmt->execute()) {

        die(
            "Gagal menambahkan menu: "
            . htmlspecialchars($stmt->error)
        );

    }


    $stmt->close();



    /* ====================================
       KEMBALI KE MENU
       ==================================== */

    header(
        "Location: menu.php?success=added"
    );

    exit;

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
        Tambah Menu - Pak Resto
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

            padding: 28px 18px;

        }



        /* BRAND */

        .brand {

            padding:
                5px 12px 30px;

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
                rgba(
                    255,
                    255,
                    255,
                    0.75
                );

        }



        /* ========================================
           SIDEBAR NAVIGATION
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
           FORM CARD
           ======================================== */

        .card {

            max-width: 760px;

            background: white;

            border-radius: 14px;

            padding: 30px;

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

        .card-title {

            margin: 0;

            font-size: 22px;

            color: #17345d;

        }


        .card-description {

            margin:
                7px 0 24px;

            color: #71839b;

            font-size: 13px;

        }



        /* ========================================
           INFORMATION BOX
           ======================================== */

        .info {

            background:
                linear-gradient(
                    135deg,
                    #eef7fb,
                    #eaf8f7
                );

            border:
                1px solid
                #d4ebed;

            padding:
                14px 16px;

            border-radius: 9px;

            margin-bottom: 25px;

            font-size: 13px;

            line-height: 1.6;

            color: #52677f;

        }


        .info strong {

            color: #24527a;

        }



        /* ========================================
           FORM GROUP
           ======================================== */

        .form-group {

            margin-bottom: 21px;

        }


        .form-group label {

            display: block;

            font-weight: bold;

            margin-bottom: 8px;

            font-size: 14px;

            color: #263f5d;

        }


        .required {

            color: #dc3545;

        }



        /* ========================================
           INPUT / SELECT
           ======================================== */

        .form-group input,
        .form-group select {

            width: 100%;

            padding:
                12px 13px;

            border:
                1px solid
                #d2d9df;

            border-radius: 8px;

            font-size: 14px;

            color: #263f5d;

            background: white;

            transition:
                border-color 0.2s ease,
                box-shadow 0.2s ease;

        }


        .form-group input::placeholder {

            color: #9aa7b5;

        }


        .form-group input:hover,
        .form-group select:hover {

            border-color:
                #b9c6d2;

        }


        .form-group input:focus,
        .form-group select:focus {

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
           FIELD HINT
           ======================================== */

        .field-hint {

            margin-top: 6px;

            font-size: 12px;

            color: #8b9aad;

        }



        /* ========================================
           ACTIONS
           ======================================== */

        .actions {

            display: flex;

            align-items: center;

            gap: 10px;

            margin-top: 28px;

            padding-top: 22px;

            border-top:
                1px solid
                #e5e9ed;

        }



        /* ========================================
           BUTTON
           ======================================== */

        .button {

            display: inline-block;

            padding:
                11px 18px;

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

            font-weight: 600;

            transition:
                opacity 0.2s ease,
                transform 0.2s ease,
                box-shadow 0.2s ease;

        }


        .button:hover {

            opacity: 0.95;

            transform:
                translateY(-1px);

            box-shadow:
                0 4px 10px
                rgba(
                    36,
                    59,
                    100,
                    0.18
                );

        }


        .button-success {

            background:
                linear-gradient(
                    135deg,
                    #198754,
                    #208e91
                );

        }


        .button-secondary {

            background: #eef1f4;

            color: #52677f;

            border:
                1px solid
                #d8dfe5;

        }


        .button-secondary:hover {

            background: #e4e9ed;

            color: #263f5d;

            box-shadow: none;

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


            .card {

                padding: 22px;

            }


            .actions {

                flex-direction: column;

                align-items: stretch;

            }


            .actions .button {

                width: 100%;

                text-align: center;

            }

        }

    </style>

</head>


<body>


<div class="page">


    <!-- ====================================
         SIDEBAR
         ==================================== -->

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



    <!-- ====================================
         MAIN CONTENT
         ==================================== -->

    <main class="main">


        <!-- PAGE HEADER -->

        <div class="page-header">


            <div>

                <h2 class="page-title">
                    Tambah Menu
                </h2>


                <div class="page-description">

                    Tambahkan menu baru
                    ke daftar restoran.

                </div>

            </div>


            <div class="date-box">

                <?= date('d F Y') ?>

            </div>


        </div>



        <!-- =================================
             FORM CARD
             ================================= -->

        <div class="card">


            <h2 class="card-title">
                Informasi Menu
            </h2>


            <p class="card-description">

                Isi informasi menu yang
                ingin ditambahkan.

            </p>



            <!-- INFO -->

            <div class="info">

                <strong>
                    Catatan:
                </strong>

                Status ketersediaan menu akan
                ditentukan secara otomatis
                berdasarkan jumlah stok.

                Jika stok lebih dari 0,
                menu akan berstatus
                <strong>Tersedia</strong>.
                Jika stok 0,
                menu akan berstatus
                <strong>Habis</strong>.

            </div>



            <!-- FORM -->

            <form
                method="POST"
                action="tambah_menu.php"
            >


                <!-- NAMA MENU -->

                <div class="form-group">


                    <label for="nama_menu">

                        Nama Menu

                        <span class="required">
                            *
                        </span>

                    </label>


                    <input
                        type="text"
                        id="nama_menu"
                        name="nama_menu"
                        placeholder="Contoh: Nasi Goreng"
                        autocomplete="off"
                        required
                    >


                    <div class="field-hint">

                        Masukkan nama menu
                        yang akan ditampilkan
                        kepada pelanggan.

                    </div>


                </div>



                <!-- KATEGORI -->

                <div class="form-group">


                    <label for="kategori">

                        Kategori

                        <span class="required">
                            *
                        </span>

                    </label>


                    <select
                        id="kategori"
                        name="kategori"
                        required
                    >


                        <option value="">
                            -- Pilih Kategori --
                        </option>


                        <option value="Makanan">
                            Makanan
                        </option>


                        <option value="Minuman">
                            Minuman
                        </option>


                    </select>


                </div>



                <!-- HARGA -->

                <div class="form-group">


                    <label for="harga">

                        Harga

                        <span class="required">
                            *
                        </span>

                    </label>


                    <input
                        type="number"
                        id="harga"
                        name="harga"
                        min="0"
                        step="0.01"
                        placeholder="Contoh: 15000"
                        required
                    >


                    <div class="field-hint">

                        Masukkan harga dalam rupiah.

                    </div>


                </div>



                <!-- STOK -->

                <div class="form-group">


                    <label for="stok_menu">

                        Stok

                        <span class="required">
                            *
                        </span>

                    </label>


                    <input
                        type="number"
                        id="stok_menu"
                        name="stok_menu"
                        min="0"
                        value="0"
                        required
                    >


                    <div class="field-hint">

                        Masukkan jumlah stok
                        awal menu.

                    </div>


                </div>



                <!-- BUTTON -->

                <div class="actions">


                    <button
                        type="submit"
                        class="button button-success"
                    >

                        Tambah Menu

                    </button>


                    <a
                        href="menu.php"
                        class="button button-secondary"
                    >

                        Kembali

                    </a>


                </div>


            </form>


        </div>


    </main>


</div>


</body>

</html>