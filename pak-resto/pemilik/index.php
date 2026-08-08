<?php

session_start();


/*
 * Pastikan user sudah login.
 */
if (!isset($_SESSION['id_pegawai'])) {

    header("Location: ../auth/login.php");

    exit;
}


/*
 * Pemilik dan Kasir boleh mengakses
 * laporan pendapatan.
 */
if (
    $_SESSION['role'] !== 'pemilik' &&
    $_SESSION['role'] !== 'kasir'
) {

    die("Akses ditolak.");

}


require_once "../config/database.php";



/*
 * ================================
 * PERIODE LAPORAN
 * ================================
 *
 * Default:
 * bulan berjalan.
 */

$periode =
    $_GET['periode'] ?? 'bulan';



/*
 * ================================
 * TENTUKAN RENTANG TANGGAL
 * ================================
 */

switch ($periode) {


    case 'hari':

        $tanggal_mulai =
            date('Y-m-d');

        $tanggal_selesai =
            date('Y-m-d');

        $label_periode =
            'Hari Ini';

        break;



    case 'minggu':

        $tanggal_mulai =
            date(
                'Y-m-d',
                strtotime(
                    'monday this week'
                )
            );

        $tanggal_selesai =
            date(
                'Y-m-d',
                strtotime(
                    'sunday this week'
                )
            );

        $label_periode =
            'Minggu Ini';

        break;



    case 'tahun':

        $tanggal_mulai =
            date('Y-01-01');

        $tanggal_selesai =
            date('Y-12-31');

        $label_periode =
            'Tahun Ini';

        break;



    case 'bulan':

    default:

        $tanggal_mulai =
            date('Y-m-01');

        $tanggal_selesai =
            date('Y-m-t');

        $label_periode =
            'Bulan Ini';

        break;

}



/*
 * ================================
 * AMBIL DATA PEMBAYARAN
 * ================================
 *
 * Hanya pembayaran yang berhasil
 * yang dihitung sebagai pendapatan.
 */

$sql = "

    SELECT

        pb.id_pembayaran,

        pb.id_pesanan,

        pb.total_bayar,

        pb.metode_bayar,

        pb.waktu_bayar,

        pg.nama_pegawai

    FROM pembayaran pb

    INNER JOIN pegawai pg

        ON pb.id_pegawai =
           pg.id_pegawai

    WHERE
        pb.status_validasi =
        'berhasil'

    AND DATE(pb.waktu_bayar)
        BETWEEN ? AND ?

    ORDER BY
        pb.waktu_bayar DESC

";



$stmt =
    $conn->prepare($sql);



$stmt->bind_param(
    "ss",
    $tanggal_mulai,
    $tanggal_selesai
);



$stmt->execute();



$result =
    $stmt->get_result();



/*
 * ================================
 * HITUNG RINGKASAN
 * ================================
 */

$total_pendapatan = 0;

$jumlah_transaksi = 0;

$transaksi = [];



while (
    $row =
    $result->fetch_assoc()
) {


    $total_pendapatan +=
        (float)
        $row['total_bayar'];


    $jumlah_transaksi++;


    $transaksi[] =
        $row;

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
        Laporan Pendapatan - Pak Resto
    </title>


    <style>

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

    .page {
        min-height: 100vh;
        display: flex;
    }

    /* =========================
       SIDEBAR
       ========================= */

    .sidebar {
        width: 250px;
        min-height: 100vh;
        position: fixed;
        left: 0;
        top: 0;
        bottom: 0;
        padding: 28px 18px;
        background: linear-gradient(
            180deg,
            #243b64 0%,
            #24527a 55%,
            #208e91 100%
        );
        color: white;
        display: flex;
        flex-direction: column;
    }

    .brand {
        padding: 5px 12px 30px;
    }

    .brand-title {
        margin: 0;
        font-size: 25px;
        font-weight: 800;
        letter-spacing: .5px;
    }

    .brand-subtitle {
        margin-top: 7px;
        font-size: 13px;
        color: rgba(255,255,255,.72);
    }

    .sidebar-menu {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .sidebar-link {
        display: block;
        padding: 13px 12px;
        border-radius: 8px;
        color: rgba(255,255,255,.88);
        text-decoration: none;
        font-size: 15px;
        transition: .2s ease;
    }

    .sidebar-link:hover,
    .sidebar-link.active {
        background: rgba(255,255,255,.14);
        color: white;
    }

    .sidebar-link:hover {
        transform: translateX(3px);
    }

    .sidebar-bottom {
        margin-top: auto;
        padding: 18px 12px 5px;
        border-top: 1px solid rgba(255,255,255,.15);
    }

    .user-label {
        font-size: 11px;
        color: rgba(255,255,255,.62);
        margin-bottom: 5px;
        text-transform: uppercase;
        letter-spacing: .5px;
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
        border: 1px solid rgba(255,255,255,.25);
        color: white;
        text-decoration: none;
        text-align: center;
        font-size: 14px;
        transition: .2s ease;
    }

    .logout:hover {
        background: rgba(255,255,255,.12);
    }

    /* =========================
       MAIN
       ========================= */

    .main {
        margin-left: 250px;
        width: calc(100% - 250px);
        min-height: 100vh;
        padding: 35px 42px;
    }

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

    /* =========================
       CARD
       ========================= */

    .card {
        background: white;
        padding: 25px;
        border-radius: 14px;
        margin-bottom: 20px;
        border: 1px solid #e2e8ee;
        box-shadow: 0 4px 16px rgba(31,55,80,.07);
    }

    .card-title {
        margin: 0;
        color: #17345d;
        font-size: 22px;
    }

    .card-description {
        margin: 7px 0 0;
        color: #71839b;
        font-size: 14px;
    }

    /* =========================
       FILTER
       ========================= */

    .filter-card {
        margin-bottom: 18px;
    }

    .filter {
        display: flex;
        gap: 10px;
        align-items: center;
        flex-wrap: wrap;
        margin-top: 18px;
    }

    select {
        min-width: 190px;
        padding: 10px 12px;
        border: 1px solid #d2d9df;
        border-radius: 7px;
        background: white;
        color: #263f5d;
        font-size: 14px;
    }

    select:focus {
        outline: none;
        border-color: #208e91;
        box-shadow: 0 0 0 3px rgba(32,142,145,.12);
    }

    /* =========================
       BUTTON
       ========================= */

    .button {
        display: inline-block;
        padding: 10px 15px;
        border: none;
        border-radius: 7px;
        background: linear-gradient(135deg,#243b64,#24527a);
        color: white;
        text-decoration: none;
        cursor: pointer;
        font-size: 14px;
        transition: .2s ease;
    }

    .button:hover {
        opacity: .94;
        transform: translateY(-1px);
    }

    .button-export {
        background: linear-gradient(135deg,#198754,#208e91);
    }

    /* =========================
       SUMMARY
       ========================= */

    .summary {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 15px;
        margin-bottom: 20px;
    }

    .summary-card {
        position: relative;
        overflow: hidden;
        min-height: 130px;
    }

    .summary-card::after {
        content: "";
        position: absolute;
        width: 85px;
        height: 85px;
        right: -25px;
        bottom: -35px;
        border-radius: 50%;
        background: rgba(32,142,145,.08);
    }

    .summary-card:nth-child(1) {
        border-top: 3px solid #208e91;
    }

    .summary-card:nth-child(2) {
        border-top: 3px solid #198754;
    }

    .summary-card:nth-child(3) {
        border-top: 3px solid #243b64;
    }

    .summary-label {
        color: #71839b;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: .4px;
        margin-bottom: 10px;
    }

    .summary-value {
        color: #17345d;
        font-size: 28px;
        font-weight: 700;
    }

    .summary-note {
        color: #8b9aad;
        font-size: 12px;
        margin-top: 7px;
    }

    /* =========================
       TABLE
       ========================= */

    .table-wrapper {
        overflow-x: auto;
        border: 1px solid #e2e8ee;
        border-radius: 10px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        min-width: 720px;
    }

    th,
    td {
        padding: 14px 15px;
        border-bottom: 1px solid #e2e8ee;
        text-align: left;
    }

    th {
        background: #f1f5f8;
        color: #52677f;
        font-size: 13px;
        font-weight: 700;
    }

    td {
        color: #405871;
        font-size: 14px;
    }

    tbody tr:last-child td {
        border-bottom: none;
    }

    tbody tr:hover {
        background: #f8fbfc;
    }

    .payment-id,
    .order-id {
        font-weight: 700;
        color: #17345d;
    }

    .amount {
        font-weight: 700;
        color: #17345d;
    }

    .method {
        display: inline-block;
        padding: 5px 9px;
        border-radius: 999px;
        background: #e8f5f4;
        color: #147879;
        font-size: 12px;
        font-weight: 700;
    }

    .empty {
        text-align: center;
        color: #777;
        padding: 45px 20px;
    }

    /* =========================
       RESPONSIVE
       ========================= */

    @media (max-width: 900px) {
        .sidebar {
            width: 210px;
        }

        .main {
            margin-left: 210px;
            width: calc(100% - 210px);
            padding: 25px;
        }

        .summary {
            grid-template-columns: 1fr;
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

        .filter {
            align-items: stretch;
        }

        select,
        .filter .button {
            width: 100%;
        }
    }

    </style>


</head>



<body>




<div class="page">

    <aside class="sidebar">

        <div class="brand">
            <h1 class="brand-title">PAK RESTO</h1>
            <div class="brand-subtitle">
                Sistem Manajemen Restoran
            </div>
        </div>

        <nav class="sidebar-menu">

            <a
                href="index.php"
                class="sidebar-link active"
            >
                Laporan Pendapatan
            </a>

            <?php if ($_SESSION['role'] === 'kasir'): ?>

                <a
                    href="../kasir/index.php"
                    class="sidebar-link"
                >
                    Pesanan & Pembayaran
                </a>

            <?php endif; ?>

        </nav>

        <div class="sidebar-bottom">

            <div class="user-label">
                <?= ucfirst(
                    htmlspecialchars($_SESSION['role'])
                ) ?>
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

    <main class="main">

        <div class="page-header">

            <div>
                <h2 class="page-title">
                    Laporan Pendapatan
                </h2>

                <div class="page-description">
                    Pantau transaksi dan pendapatan restoran berdasarkan periode.
                </div>
            </div>

            <div class="date-box">
                <?= date('d F Y') ?>
            </div>

        </div>




    <!-- ==========================
         FILTER PERIODE
         ========================== -->


    <div class="card">


        <h2>

            Periode Laporan

        </h2>



        <form
            method="GET"
            class="filter"
        >


            <select
                name="periode"
            >


                <option
                    value="hari"

                    <?= $periode === 'hari'
                        ? 'selected'
                        : '' ?>
                >

                    Hari Ini

                </option>



                <option
                    value="minggu"

                    <?= $periode === 'minggu'
                        ? 'selected'
                        : '' ?>
                >

                    Minggu Ini

                </option>



                <option
                    value="bulan"

                    <?= $periode === 'bulan'
                        ? 'selected'
                        : '' ?>
                >

                    Bulan Ini

                </option>



                <option
                    value="tahun"

                    <?= $periode === 'tahun'
                        ? 'selected'
                        : '' ?>
                >

                    Tahun Ini

                </option>


            </select>



            <button
                type="submit"
                class="button"
            >

                Tampilkan

            </button>



            <!--
             * Export XLSX
             * menggunakan periode yang
             * sedang dipilih.
             -->

            <a
                href="export_laporan.php?periode=<?= urlencode($periode) ?>"
                class="button button-export"
            >

                Export XLSX

            </a>


        </form>


    </div>



    <!-- ==========================
         RINGKASAN
         ========================== -->


    <div class="summary">



        <div class="card summary-card">

            <div class="summary-label">Periode</div>


            <div class="summary-value"><?= $label_periode ?></div>
            <div class="summary-note">Rentang laporan aktif</div>


        </div>



        <div class="card summary-card">

            <div class="summary-label">Jumlah Transaksi</div>


            <div class="summary-value"><?= $jumlah_transaksi ?></div>
            <div class="summary-note">Pembayaran berhasil pada periode ini</div>


        </div>



        <div class="card summary-card">

            <div class="summary-label">Total Pendapatan</div>


            <div class="summary-value">Rp <?= number_format($total_pendapatan, 0, ',', '.') ?></div>
            <div class="summary-note">Total pembayaran berhasil</div>


        </div>


    </div>



    <!-- ==========================
         DETAIL TRANSAKSI
         ========================== -->


    <div class="card">


        <h2 class="card-title">

            Detail Transaksi

        </h2>

        <p class="card-description" style="margin-bottom:20px;">
            Rincian pembayaran yang berhasil pada periode yang dipilih.
        </p>



        <?php

        if (
            count($transaksi) === 0
        ):

        ?>


            <div class="empty">


                Tidak ada transaksi
                pada periode ini.


            </div>



        <?php

        else:

        ?>


            <div class="table-wrapper">

            <table>


                <thead>


                    <tr>


                        <th>

                            ID Pembayaran

                        </th>


                        <th>

                            ID Pesanan

                        </th>


                        <th>

                            Total

                        </th>


                        <th>

                            Metode

                        </th>


                        <th>

                            Waktu

                        </th>


                        <th>

                            Kasir

                        </th>


                    </tr>


                </thead>



                <tbody>



                    <?php

                    foreach (
                        $transaksi
                        as $row
                    ):

                    ?>


                        <tr>


                            <td class="payment-id">

                                #<?= $row[
                                    'id_pembayaran'
                                ] ?>

                            </td>



                            <td class="order-id">

                                #<?= $row[
                                    'id_pesanan'
                                ] ?>

                            </td>



                            <td class="amount">

                                Rp <?= number_format(
                                    $row['total_bayar'],
                                    0,
                                    ',',
                                    '.'
                                ) ?>

                            </td>



                            <td>

                                <span class="method">
                                    <?= htmlspecialchars(
                                        $row['metode_bayar']
                                    ) ?>
                                </span>

                            </td>



                            <td>


                                <?= $row[
                                    'waktu_bayar'
                                ] ?>


                            </td>



                            <td>


                                <?= htmlspecialchars(
                                    $row[
                                        'nama_pegawai'
                                    ]
                                ) ?>


                            </td>


                        </tr>


                    <?php

                    endforeach;

                    ?>


                </tbody>


            </table>

            </div>


        <?php

        endif;

        ?>


    </div>


    </main>

</div>



</body>


</html>