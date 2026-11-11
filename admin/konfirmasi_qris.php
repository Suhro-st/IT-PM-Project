<?php
session_start();
require_once __DIR__."/../koneksi.php";

if (
    !isset($_SESSION['user_id']) ||
    ($_SESSION['role'] ?? '') !== 'admin'
) {
    header("Location: ../login_admin.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: manage.php");
    exit;
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    header("Location: manage.php");
    exit;
}

$stmt = mysqli_prepare(
    $conn,
    "UPDATE orders
     SET status_pembayaran = 'Lunas'
     WHERE id = ?
       AND metode_pembayaran = 'QRIS'
       AND status_pembayaran = 'Menunggu Verifikasi'"
);

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

header("Location: manage.php");
exit;
?>
