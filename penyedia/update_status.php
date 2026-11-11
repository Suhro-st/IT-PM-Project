
<?php
session_start();
require_once __DIR__ . "/../koneksi.php";

if (
    !isset($_SESSION["user_id"]) ||
    ($_SESSION["role"] ?? "") !== "penyedia"
) {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: dashboard.php");
    exit;
}

$order_id = (int) ($_POST["order_id"] ?? 0);
$status = $_POST["status"] ?? "";

$status_valid = ["Menunggu", "Diproses", "Selesai"];

if ($order_id <= 0 || !in_array($status, $status_valid, true)) {
    die("Data pesanan tidak valid.");
}

$stmt = mysqli_prepare(
    $conn,
    "UPDATE orders o
     JOIN services s ON o.service_id = s.id
     SET o.status = ?
     WHERE o.id = ? AND s.user_id = ?"
);

mysqli_stmt_bind_param(
    $stmt,
    "sii",
    $status,
    $order_id,
    $_SESSION["user_id"]
);

if (mysqli_stmt_execute($stmt)) {
    header("Location: dashboard.php");
    exit;
}

die("Status pesanan gagal diperbarui.");
?>
