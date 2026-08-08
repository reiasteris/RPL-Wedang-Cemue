<?php

session_start();

require_once "../config/database.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: login.php");
    exit;
}

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if ($username === '' || $password === '') {
    header("Location: login.php?error=empty");
    exit;
}

/*
 * Cari user berdasarkan username.
 */
$sql = "SELECT * FROM pegawai WHERE username = ? LIMIT 1";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    header("Location: login.php?error=invalid");
    exit;
}

$pegawai = $result->fetch_assoc();

/*
 * Verifikasi password.
 */
if (!password_verify($password, $pegawai['password'])) {
    header("Location: login.php?error=invalid");
    exit;
}

/*
 * Login berhasil.
 */
session_regenerate_id(true);

$_SESSION['id_pegawai'] = $pegawai['id_pegawai'];
$_SESSION['nama_pegawai'] = $pegawai['nama_pegawai'];
$_SESSION['role'] = $pegawai['role'];

/*
 * Redirect berdasarkan role.
 */
switch ($pegawai['role']) {

    case 'pelayan':
        header("Location: ../pelayan/index.php");
        break;

    case 'koki':
        header("Location: ../koki/index.php");
        break;

    case 'kasir':
        header("Location: ../kasir/index.php");
        break;

    case 'pemilik':
        header("Location: ../pemilik/index.php");
        break;

    default:
        session_destroy();
        die("Role pengguna tidak dikenali.");
}

exit;