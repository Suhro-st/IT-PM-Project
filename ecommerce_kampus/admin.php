<?php
session_start();
require_once "koneksi.php";

if (
    !isset($_SESSION['user_id']) ||
    $_SESSION['role'] !== 'admin'
) {
    die("Akses ditolak. Halaman ini hanya untuk admin.");
}

$total_users = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM users")
)['total'];

$total_services = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM services")
)['total'];

$total_orders = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM orders")
)['total'];

$result = mysqli_query(
    $conn,
    "SELECT orders.*, users.nama AS nama_pembeli
     FROM orders
     JOIN users ON orders.user_id = users.id
     ORDER BY orders.tanggal_pesan DESC"
);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <div class="logo">Admin E-Commerce Jasa</div>
    <nav>
        <a href="index.php">Beranda</a>
        <a href="manage.php">Kelola Jasa</a>
        <a href="logout.php">Logout</a>
    </nav>
</header>

<main class="container">
    <h1 class="section-title">Dashboard Admin</h1>

    <div class="stats">
        <div class="stat-card">
            <h3>Total Pengguna</h3>
            <h2><?= $total_users ?></h2>
        </div>

        <div class="stat-card">
            <h3>Total Jasa</h3>
            <h2><?= $total_services ?></h2>
        </div>

        <div class="stat-card">
            <h3>Total Pesanan</h3>
            <h2><?= $total_orders ?></h2>
        </div>
    </div>

    <h2>Daftar Pesanan</h2>

    <div class="table-wrapper">
        <table>
            <tr>
                <th>ID</th>
                <th>Pembeli</th>
                <th>Jasa</th>
                <th>Harga</th>
                <th>Status Pesanan</th>
                <th>Status Pembayaran</th>
            </tr>

            <?php while ($order = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?= $order['id'] ?></td>
                    <td><?= htmlspecialchars($order['nama_pembeli']) ?></td>
                    <td><?= htmlspecialchars($order['nama_layanan']) ?></td>
                    <td>
                        Rp <?= number_format($order['harga'], 0, ',', '.') ?>
                    </td>
                    <td><?= htmlspecialchars($order['status']) ?></td>
                    <td>
                        <?= htmlspecialchars($order['status_pembayaran']) ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>
</main>

</body>
</html>
