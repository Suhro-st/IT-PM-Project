<?php
session_start();
require_once "koneksi.php";

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    die("ID jasa tidak valid.");
}

$stmt = mysqli_prepare(
    $conn,
    "SELECT services.*, users.nama
     FROM services
     JOIN users ON services.user_id = users.id
     WHERE services.id = ? AND services.status = 'Aktif'"
);

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$service = mysqli_fetch_assoc($result);

if (!$service) {
    die("Jasa tidak ditemukan.");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Jasa</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <div class="logo">E-Commerce Jasa</div>
    <nav>
        <a href="index.php">Beranda</a>
        <a href="services.php">Katalog Jasa</a>
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="profile.php">Profil</a>
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="login.php">Login</a>
        <?php endif; ?>
    </nav>
</header>

<main class="container">
    <div class="detail-card">
        <div class="card-icon">🛠️</div>

        <h1><?= htmlspecialchars($service['nama_layanan']) ?></h1>

        <p class="category">
            <?= htmlspecialchars($service['kategori']) ?>
        </p>

        <p>
            <?= nl2br(htmlspecialchars($service['deskripsi'])) ?>
        </p>

        <p>
            Penyedia:
            <?= htmlspecialchars($service['nama']) ?>
        </p>

        <h2 class="price">
            Rp <?= number_format($service['harga'], 0, ',', '.') ?>
        </h2>

        <?php if (
            isset($_SESSION['user_id']) &&
            $_SESSION['user_id'] != $service['user_id']
        ): ?>
            <a
                href="checkout.php?id=<?= $service['id'] ?>"
                class="btn"
            >
                Pesan Jasa
            </a>
        <?php elseif (!isset($_SESSION['user_id'])): ?>
            <a href="login.php" class="btn">Login untuk Memesan</a>
        <?php else: ?>
            <p>Ini adalah jasa yang kamu tawarkan.</p>
        <?php endif; ?>

        <br><br>
        <a href="services.php">Kembali ke Katalog</a>
    </div>
</main>

</body>
</html>
