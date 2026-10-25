<?php
session_start();
require_once __DIR__ . "/../koneksi.php";

// Periksa login admin
if (
    !isset($_SESSION['user_id']) ||
    !isset($_SESSION['role']) ||
    $_SESSION['role'] !== 'admin'
) {
    header("Location: login_admin.php");
    exit;
}

// Menghitung total pengguna
$query_pengguna = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total FROM users WHERE role = 'user'"
);
$total_pengguna = mysqli_fetch_assoc($query_pengguna)['total'];

// Menghitung total jasa
$query_jasa = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total FROM services"
);
$total_jasa = mysqli_fetch_assoc($query_jasa)['total'];

// Menghitung total pesanan
$query_pesanan = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total FROM orders"
);
$total_pesanan = mysqli_fetch_assoc($query_pesanan)['total'];

// Mengambil daftar pesanan
$query = "
    SELECT
        orders.id,
        users.nama,
        orders.nama_layanan,
        orders.harga,
        orders.status,
        orders.status_pembayaran
    FROM orders
    LEFT JOIN users ON orders.user_id = users.id
    ORDER BY orders.id DESC
";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<header>
    <div class="logo">Admin E-Commerce Jasa</div>

<nav>
    <a href="dashboard.php">Dashboard</a>
    <a href="manage.php">Kelola Jasa</a>
    <a href="add_services.php">Tambah Jasa</a>
    <a href="../index.php">Beranda</a>
    <a href="../logout.php">Logout</a>
</nav>
</header>

<div class="container">

    <h1 class="section-title">Dashboard Admin</h1>

    <p>
        Selamat datang,
        <strong><?= htmlspecialchars($_SESSION['nama']) ?></strong>!
    </p>

    <!-- Statistik -->
    <div class="stats">

        <div class="stat-card">
            <h3>Total Pengguna</h3>
            <h2><?= $total_pengguna ?></h2>
        </div>

        <div class="stat-card">
            <h3>Total Jasa</h3>
            <h2><?= $total_jasa ?></h2>
        </div>

        <div class="stat-card">
            <h3>Total Pesanan</h3>
            <h2><?= $total_pesanan ?></h2>
        </div>

    </div>

    <!-- Tabel Pesanan -->
    <h2 class="section-title">Daftar Pesanan</h2>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Pembeli</th>
                    <th>Jasa</th>
                    <th>Harga</th>
                    <th>Status Pesanan</th>
                    <th>Status Pembayaran</th>
                </tr>
            </thead>

            <tbody>
                <?php if ($result && mysqli_num_rows($result) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td>
                                <?= htmlspecialchars($row['nama'] ?? 'Pengguna tidak ditemukan') ?>
                            </td>
                            <td>
                                <?= htmlspecialchars($row['nama_layanan']) ?>
                            </td>
                            <td>
                                Rp <?= number_format($row['harga'], 0, ',', '.') ?>
                            </td>
                            <td>
                                <?= htmlspecialchars($row['status']) ?>
                            </td>
                            <td>
                                <?= htmlspecialchars($row['status_pembayaran']) ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align:center;">
                            Belum ada pesanan.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>

<footer>
    <p>&copy; 2026 E-Commerce Jasa Kampus</p>
</footer>

</body>
</html>
