<?php
session_start();
require_once "koneksi.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$stmt = mysqli_prepare(
    $conn,
    "SELECT *
     FROM orders
     WHERE user_id = ?
     ORDER BY tanggal_pesan DESC"
);

mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pesanan Saya</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

<header>
    <div class="logo">E-Commerce Jasa</div>

    <nav>
        <a href="index.php">Beranda</a>
        <a href="services.php">Katalog</a>
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
                <th>Metode Pembayaran</th>
                <th>Status Pembayaran</th>
                <th>Tanggal</th>
                <th>Aksi</th>
            </tr>

            <?php if (mysqli_num_rows($result) > 0): ?>

                <?php while ($order = mysqli_fetch_assoc($result)): ?>

                    <tr>
                        <td><?= $order['id'] ?></td>

                        <td>
                            <?= htmlspecialchars($order['nama_layanan']) ?>
                        </td>

                        <td>
                            Rp <?= number_format($order['harga'], 0, ',', '.') ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($order['status']) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($order['metode_pembayaran'] ?? '-') ?>
                        </td>

                        <td>
                            <?php
                            $statusBayar = $order['status_pembayaran'];

                            if ($statusBayar === 'Menunggu Pembayaran Tunai') {
                                echo "Menunggu Pembayaran Tunai";
                            } elseif ($statusBayar === 'Menunggu Verifikasi') {
                                echo "Menunggu Verifikasi QRIS";
                            } else {
                                echo htmlspecialchars($statusBayar);
                            }
                            ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($order['tanggal_pesan']) ?>
                        </td>

                        <td>
                            <?php if ($statusBayar === 'Belum Dibayar'): ?>

                                <a href="payment.php?id=<?= $order['id'] ?>">
                                    Bayar
                                </a>

                            <?php elseif ($statusBayar === 'Menunggu Pembayaran Tunai'): ?>

                                Menunggu Penyedia

                            <?php elseif ($statusBayar === 'Menunggu Verifikasi'): ?>

                                Menunggu Admin

                            <?php elseif ($statusBayar === 'Lunas'): ?>

                                <strong>Lunas</strong>

                            <?php else: ?>

                                -

                            <?php endif; ?>
                        </td>
                    </tr>

                <?php endwhile; ?>

            <?php else: ?>

                <tr>
                    <td colspan="8" style="text-align:center;">
                        Belum ada pesanan.
                    </td>
                </tr>

            <?php endif; ?>

        </table>

    </div>

</main>

</body>
</html>
