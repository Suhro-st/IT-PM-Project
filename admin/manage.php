
<?php
session_start();
require_once __DIR__ . "/../koneksi.php";

if (
    !isset($_SESSION["user_id"]) ||
    ($_SESSION["role"] ?? "") !== "admin"
) {
    header("Location: ../login_admin.php");
    exit;
}

// Mengambil data jasa
$jasa = mysqli_query(
    $conn,
    "SELECT s.id, s.nama_layanan, s.kategori, s.harga, s.status,
            u.nama AS nama_penyedia
     FROM services s
     LEFT JOIN users u ON s.user_id = u.id
     ORDER BY s.id DESC"
);

// Mengambil data pesanan
$pesanan = mysqli_query(
    $conn,
    "SELECT o.id, o.nama_layanan, o.harga, o.status,
            o.status_pembayaran, o.metode_pembayaran,
            u.nama AS nama_pelanggan,
            p.nama AS nama_penyedia
     FROM orders o
     JOIN users u ON o.user_id = u.id
     LEFT JOIN services s ON o.service_id = s.id
     LEFT JOIN users p ON s.user_id = p.id
     ORDER BY o.id DESC"
);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Jasa dan Pesanan</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<header>
    <div class="logo">E-Commerce Jasa</div>
    <nav>
        <a href="dashboard.php">Dashboard</a>
        <a href="add_service.php">Tambah Jasa</a>
        <a href="../index.php">Beranda</a>
        <a href="../logout.php">Logout</a>
    </nav>
</header>

<main class="container">

    <h1 class="section-title">Kelola Jasa</h1>

    <p>
        <a class="btn" href="add_service.php">+ Tambah Jasa</a>
    </p>

    <div style="overflow-x:auto">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama Jasa</th>
                    <th>Kategori</th>
                    <th>Penyedia</th>
                    <th>Harga</th>
                    <th>Status</th>
                </tr>
            </thead>

            <tbody>
            <?php while ($j = mysqli_fetch_assoc($jasa)): ?>
                <tr>
                    <td><?= (int) $j["id"] ?></td>
                    <td><?= htmlspecialchars($j["nama_layanan"]) ?></td>
                    <td><?= htmlspecialchars($j["kategori"]) ?></td>
                    <td><?= htmlspecialchars($j["nama_penyedia"] ?? "Belum ditentukan") ?></td>
                    <td>Rp <?= number_format((float) $j["harga"], 0, ",", ".") ?></td>
                    <td><?= htmlspecialchars($j["status"]) ?></td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <h1 class="section-title">Pantau Pesanan</h1>

    <div style="overflow-x:auto">
        <table>
            <thead>
                <tr>
                    <th>ID Pesanan</th>
                    <th>Pelanggan</th>
                    <th>Nama Jasa</th>
                    <th>Penyedia</th>
                    <th>Harga</th>
                    <th>Metode Pembayaran</th>
                    <th>Status Pembayaran</th>
                    <th>Status Pesanan</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>

            <?php while ($o = mysqli_fetch_assoc($pesanan)): ?>
                <tr>
                    <td><?= (int) $o["id"] ?></td>

                    <td>
                        <?= htmlspecialchars($o["nama_pelanggan"]) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($o["nama_layanan"]) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($o["nama_penyedia"] ?? "Belum ditentukan") ?>
                    </td>

                    <td>
                        Rp <?= number_format((float) $o["harga"], 0, ",", ".") ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($o["metode_pembayaran"] ?? "-") ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($o["status_pembayaran"] ?? "-") ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($o["status"] ?? "Menunggu") ?>
                    </td>

                    <td>
                        <?php if (
                            ($o["metode_pembayaran"] ?? "") === "QRIS" &&
                            ($o["status_pembayaran"] ?? "") === "Menunggu Verifikasi"
                        ): ?>

                            <form method="POST" action="konfirmasi_qris.php">
                                <input type="hidden" name="id"
                                       value="<?= (int) $o["id"] ?>">

                                <button type="submit" class="btn"
                                    onclick="return confirm('Apakah pembayaran QRIS sudah diterima?')">
                                    Verifikasi QRIS
                                </button>
                            </form>

                        <?php elseif (
                            ($o["metode_pembayaran"] ?? "") === "Tunai" &&
                            ($o["status_pembayaran"] ?? "") === "Menunggu Pembayaran Tunai"
                        ): ?>

                            <form method="POST" action="konfirmasi_tunai.php">
                                <input type="hidden" name="id"
                                       value="<?= (int) $o["id"] ?>">

                                <button type="submit" class="btn"
                                    onclick="return confirm('Apakah pembayaran Tunai sudah diterima oleh penyedia?')">
                                    Konfirmasi Tunai
                                </button>
                            </form>

                        <?php elseif (
                            ($o["status_pembayaran"] ?? "") === "Lunas"
                        ): ?>

                            <strong>Lunas</strong>

                        <?php else: ?>

                            -

                        <?php endif; ?>
                    </td>
                </tr>

            <?php endwhile; ?>

            </tbody>
        </table>
    </div>

</main>

</body>
</html>
