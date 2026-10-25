<?php
session_start();
require_once "koneksi.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$stmt = mysqli_prepare(
    $conn,
    "SELECT orders.*, services.kategori
     FROM orders
     LEFT JOIN services ON orders.service_id = services.id
     WHERE orders.user_id = ?
     ORDER BY orders.tanggal_pesan DESC"
);

mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Status Pesanan</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <div class="logo">E-Commerce Jasa</div>
    <nav>
        <a href="index.php">Beranda</a>
        <a href="services.php">Katalog</a>
        <a href="add_service.php">Tambah Jasa</a>
        <a href="profile.php">Profil</a>
        <a href="logout.php">Logout</a>
    </nav>
</header>

<main class="container">
    <h1 class="section-title">Pesanan Saya</h1>

    <div class="table-wrapper">
        <table>
            <tr>
                <th>ID</th>
                <th>Nama Jasa</th>
                <th>Harga</th>
                <th>Status Pesanan</th>
                <th>Pembayaran</th>
                <th>Tanggal</th>
                <th>Aksi</th>
            </tr>

            <?php while ($order = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?= $order['id'] ?></td>
                    <td><?= htmlspecialchars($order['nama_layanan']) ?></td>
                    <td>
                        Rp <?= number_format($order['harga'], 0, ',', '.') ?>
                    </td>
                    <td><?= htmlspecialchars($order['status']) ?></td>
                    <td>
                        <?= htmlspecialchars($order['status_pembayaran']) ?>
                    </td>
                    <td><?= htmlspecialchars($order['tanggal_pesan']) ?></td>
                    <td>
                        <?php if ($order['status_pembayaran'] === 'Belum Dibayar'): ?>
                            <a href="payment.php?id=<?= $order['id'] ?>">
                                Bayar
                            </a>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>
</main>

</body>
</html>
