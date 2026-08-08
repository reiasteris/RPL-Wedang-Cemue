<?php

session_start();

if (!isset($_SESSION['id_pegawai'])) {
    header("Location: auth/login.php");
    exit;
}

switch ($_SESSION['role']) {

    case 'pelayan':
        header("Location: pelayan/index.php");
        break;

    case 'koki':
        header("Location: koki/index.php");
        break;

    case 'kasir':
        header("Location: kasir/index.php");
        break;

    case 'pemilik':
        header("Location: pemilik/index.php");
        break;

    default:
        session_destroy();
        header("Location: auth/login.php?error=invalid");
        break;
}

exit;