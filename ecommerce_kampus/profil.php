<?php
session_start();
require_once "koneksi.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$stmt = mysqli_prepare(
    $conn,
    "SELECT nama, email, role, created_at
     FROM users
     WHERE id = ?"
);

mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

if (!$user) {
    die("Pengguna tidak ditemukan.");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Profil Pengguna</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <div class="logo">E-Commerce Jasa</div>
    <nav>
        <a href="index.php">Beranda</a>
        <a href="services.php">Katalog</a>
        <a href="add_service.php">Tambah Jasa</a>
        <a href="order_status.php">Pesanan</a>
        <a href="logout.php">Logout</a>
    </nav>
</header>

<div class="form-container">
    <h2>Profil Saya</h2>

    <p><strong>Nama:</strong>
        <?= htmlspecialchars($user['nama']) ?>
    </p>

    <p><strong>Email:</strong>
        <?= htmlspecialchars($user['email']) ?>
    </p>

    <p><strong>Role:</strong>
        <?= htmlspecialchars($user['role']) ?>
    </p>

    <p><strong>Terdaftar:</strong>
        <?= htmlspecialchars($user['created_at']) ?>
    </p>

    <a href="index.php" class="btn">Kembali ke Beranda</a>
</div>

</body>
</html>
