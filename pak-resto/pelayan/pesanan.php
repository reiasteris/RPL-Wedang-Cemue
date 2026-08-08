<?php

session_start();

if (!isset($_SESSION['id_pegawai'])) {
    header("Location: ../auth/login.php");
    exit;
}

if ($_SESSION['role'] !== 'pelayan') {
    die("Akses ditolak.");
}

require_once "../config/database.php";

$id_meja = (int) ($_GET['id_meja'] ?? 0);
$jumlah_pelanggan = (int) ($_GET['jumlah_pelanggan'] ?? 0);

if ($id_meja <= 0 || $jumlah_pelanggan <= 0) {
    die("Data meja tidak valid.");
}


/*
 * Ambil informasi meja.
 */
$sql = "
    SELECT *
    FROM meja
    WHERE id_meja = ?
    AND status_meja = 'tersedia'
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_meja);
$stmt->execute();

$meja_result = $stmt->get_result();

if ($meja_result->num_rows !== 1) {
    die("Meja tidak tersedia.");
}

$meja = $meja_result->fetch_assoc();


/*
 * Ambil menu yang tersedia.
 */
$sql_menu = "
    SELECT *
    FROM menu
    WHERE status_ketersediaan = 'tersedia'
    AND stok_menu > 0
    ORDER BY kategori, nama_menu
";

$menu_result = $conn->query($sql_menu);

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Pesanan - Pak Resto</title>

    <style>

        body {
            font-family: Arial, sans-serif;
            background: #f5f6f8;
            margin: 0;
        }

        header {
            background: #243b64;
            color: white;
            padding: 20px 30px;
        }

        .container {
            max-width: 1000px;
            margin: auto;
            padding: 30px;
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        .menu-grid {
            display: grid;
            grid-template-columns:
                repeat(auto-fit, minmax(220px, 1fr));

            gap: 15px;
        }

        .menu-item {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 18px;
        }

        .menu-item h3 {
            margin-top: 0;
        }

        .harga {
            font-weight: bold;
        }

        .stok {
            color: #666;
            font-size: 14px;
        }

        input[type="number"] {
            width: 70px;
            padding: 8px;
            margin-top: 10px;
        }

        .submit-button {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 7px;
            background: #243b64;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }

    </style>

</head>

<body>

<header>

    <h2>PAK RESTO</h2>

    <div>
        Pelayan:
        <?= htmlspecialchars($_SESSION['nama_pegawai']) ?>
    </div>

</header>


<div class="container">

    <div class="card">

        <h2>Pesanan Baru</h2>

        <p>
            Meja:
            <strong>
                <?= htmlspecialchars($meja['nomor_meja']) ?>
            </strong>
        </p>

        <p>
            Jumlah pelanggan:
            <strong>
                <?= $jumlah_pelanggan ?>
            </strong>
        </p>

    </div>


    <form action="simpan_pesanan.php" method="POST">

        <input
            type="hidden"
            name="id_meja"
            value="<?= $id_meja ?>"
        >

        <input
            type="hidden"
            name="jumlah_pelanggan"
            value="<?= $jumlah_pelanggan ?>"
        >


        <div class="card">

            <h2>Pilih Menu</h2>

            <div class="menu-grid">

                <?php while ($menu = $menu_result->fetch_assoc()): ?>

                    <div class="menu-item">

                        <h3>
                            <?= htmlspecialchars($menu['nama_menu']) ?>
                        </h3>

                        <p>
                            <?= htmlspecialchars($menu['kategori']) ?>
                        </p>

                        <p class="harga">
                            Rp <?= number_format(
                                $menu['harga'],
                                0,
                                ',',
                                '.'
                            ) ?>
                        </p>

                        <p class="stok">
                            Stok:
                            <?= $menu['stok_menu'] ?>
                        </p>

                        <label>
                            Jumlah:
                        </label>

                        <input
                            type="number"
                            name="jumlah[<?= $menu['id_menu'] ?>]"
                            min="0"
                            max="<?= $menu['stok_menu'] ?>"
                            value="0"
                        >

                    </div>

                <?php endwhile; ?>

            </div>

        </div>


        <button
            type="submit"
            class="submit-button"
        >
            Simpan Pesanan
        </button>

    </form>

</div>

</body>

</html>