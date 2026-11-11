<?php
session_start();
require_once "koneksi.php";

if (
    !isset($_SESSION['user_id']) ||
    ($_SESSION['role'] ?? '') !== 'penyedia'
) {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: dashboard.php");
    exit;
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    header("Location: dashboard.php");
    exit;
}

$stmt = mysqli_prepare(
    $conn,
    "UPDATE orders
     INNER JOIN services
        ON orders.service_id = services.id
     SET orders.status_pembayaran = 'Lunas'
     WHERE orders.id = ?
       AND services.user_id = ?
       AND orders.metode_pembayaran = 'Tunai'
       AND orders.status_pembayaran = 'Menunggu Pembayaran Tunai'"
);

mysqli_stmt_bind_param(
    $stmt,
    "ii",
    $id,
    $_SESSION['user_id']
);

mysqli_stmt_execute($stmt);

header("Location: dashboard.php");
exit;
?>
