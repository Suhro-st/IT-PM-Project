<?php
session_start();
require_once "koneksi.php";

$result = mysqli_query(
    $conn,
    "SELECT services.*, users.nama
     FROM services
     JOIN users ON services.user_id = users.id
     WHERE services.status = 'Aktif'
     ORDER BY services.id DESC
     LIMIT 6"
);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>E-Commerce Jasa</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <div class="logo">E-Commerce Jasa</div>

    <nav>
        <a href="index.php">Beranda</a>
        <a href="services.php">Katalog Jasa</a>

        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="add_service.php">Tambah Jasa</a>
            <a href="order_status.php">Pesanan</a>
            <a href="profile.php">Profil</a>

            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <a href="admin.php">Admin</a>
            <?php endif; ?>

            <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="login.php">Login</a>
            <a href="register.php" class="btn-nav">Daftar</a>
        <?php endif; ?>
    </nav>
</header>

<section class="hero">
    <div class="hero-content">
        <h1>E-Commerce Jasa Kampus</h1>
        <p>
            Temukan dan tawarkan berbagai jasa
            dengan mudah di lingkungan kampus.
        </p>
        <a href="services.php" class="btn">Lihat Jasa</a>
    </div>
</section>

<main class="container">
    <h2 class="section-title">Jasa Terbaru</h2>

    <div class="service-grid">
        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while ($service = mysqli_fetch_assoc($result)): ?>
                <div class="card">
                    <div class="card-icon">🛠️</div>

                    <h3>
                        <?= htmlspecialchars($service['nama_layanan']) ?>
                    </h3>

                    <p class="category">
                        <?= htmlspecialchars($service['kategori']) ?>
                    </p>

                    <p>
                        <?= htmlspecialchars(
                            substr($service['deskripsi'], 0, 100)
                        ) ?>...
                    </p>

                    <h3 class="price">
                        Rp <?= number_format($service['harga'], 0, ',', '.') ?>
                    </h3>

                    <a
                        href="detail.php?id=<?= $service['id'] ?>"
                        class="btn"
                    >
                        Lihat Detail
                    </a>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>Belum ada jasa yang tersedia.</p>
        <?php endif; ?>
    </div>
</main>

<footer>
    <p>&copy; 2026 E-Commerce Jasa Kampus</p>
</footer>

</body>
</html>
