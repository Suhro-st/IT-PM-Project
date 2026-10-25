<?php
session_start();
require_once "koneksi.php";

$keyword = trim($_GET['cari'] ?? '');

if ($keyword !== '') {
    $search = "%$keyword%";

    $stmt = mysqli_prepare(
        $conn,
        "SELECT services.*, users.nama
         FROM services
         JOIN users ON services.user_id = users.id
         WHERE services.status = 'Aktif'
         AND (
             services.nama_layanan LIKE ?
             OR services.kategori LIKE ?
         )
         ORDER BY services.id DESC"
    );

    mysqli_stmt_bind_param($stmt, "ss", $search, $search);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
} else {
    $result = mysqli_query(
        $conn,
        "SELECT services.*, users.nama
         FROM services
         JOIN users ON services.user_id = users.id
         WHERE services.status = 'Aktif'
         ORDER BY services.id DESC"
    );
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Katalog Jasa</title>
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
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="login.php">Login</a>
        <?php endif; ?>
    </nav>
</header>

<main class="container">
    <h1 class="section-title">Katalog Jasa</h1>

    <form method="GET" class="search-form">
        <input
            type="text"
            name="cari"
            placeholder="Cari nama atau kategori jasa..."
            value="<?= htmlspecialchars($keyword) ?>"
        >
        <button type="submit" class="btn">Cari</button>
    </form>

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
                    <p>
                        Penyedia:
                        <?= htmlspecialchars($service['nama']) ?>
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
            <p>Jasa tidak ditemukan.</p>
        <?php endif; ?>
    </div>
</main>

</body>
</html>
