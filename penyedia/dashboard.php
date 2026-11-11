
<?php
session_start();
require_once __DIR__ . "/../koneksi.php";

// Cek login penyedia
if (
    !isset($_SESSION["user_id"]) ||
    ($_SESSION["role"] ?? "") !== "penyedia"
) {
    header("Location: ../login.php");
    exit;
}

$penyedia_id = (int) $_SESSION["user_id"];

// Menghitung jumlah jasa milik penyedia
$stmt = mysqli_prepare(
    $conn,
    "SELECT COUNT(*) AS total
     FROM services
     WHERE user_id = ?"
);
mysqli_stmt_bind_param($stmt, "i", $penyedia_id);
mysqli_stmt_execute($stmt);
$total_jasa = mysqli_fetch_assoc(
    mysqli_stmt_get_result($stmt)
)["total"];

// Menghitung jumlah pesanan
$stmt = mysqli_prepare(
    $conn,
    "SELECT COUNT(*) AS total
     FROM orders o
     JOIN services s ON o.service_id = s.id
     WHERE s.user_id = ?"
);
mysqli_stmt_bind_param($stmt, "i", $penyedia_id);
mysqli_stmt_execute($stmt);
$total_pesanan = mysqli_fetch_assoc(
    mysqli_stmt_get_result($stmt)
)["total"];

// Menghitung pesanan selesai
$stmt = mysqli_prepare(
    $conn,
    "SELECT COUNT(*) AS total
     FROM orders o
     JOIN services s ON o.service_id = s.id
     WHERE s.user_id = ? AND o.status = 'Selesai'"
);
mysqli_stmt_bind_param($stmt, "i", $penyedia_id);
mysqli_stmt_execute($stmt);
$total_selesai = mysqli_fetch_assoc(
    mysqli_stmt_get_result($stmt)
)["total"];

// Mengambil pesanan khusus milik penyedia
$stmt = mysqli_prepare(
    $conn,
    "SELECT o.id, o.nama_layanan, o.harga, o.status,
            o.status_pembayaran, o.tanggal_pesan,
            u.nama AS nama_pelanggan
     FROM orders o
     JOIN services s ON o.service_id = s.id
     JOIN users u ON o.user_id = u.id
     WHERE s.user_id = ?
     ORDER BY o.id DESC"
);

mysqli_stmt_bind_param($stmt, "i", $penyedia_id);
mysqli_stmt_execute($stmt);
$pesanan = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Penyedia Jasa</title>
    <link rel="stylesheet" href="../style.css">

    <style>
        .provider-welcome {
            margin-bottom: 25px;
        }

        .provider-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin: 25px 0;
        }

        .provider-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 3px 12px rgba(0,0,0,0.07);
        }

        .provider-card h3 {
            color: #555;
            margin-bottom: 12px;
        }

        .provider-card .number {
            font-size: 30px;
            font-weight: bold;
            color: #164e9a;
        }

        .provider-table {
            background: white;
            padding: 20px;
            border-radius: 12px;
            margin-top: 25px;
        }

        .provider-table table {
            width: 100%;
            border-collapse: collapse;
        }

        .provider-table th,
        .provider-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        .provider-table select {
            margin-bottom: 8px;
        }

        .empty-message {
            background: white;
            padding: 25px;
            border-radius: 10px;
            text-align: center;
        }

        @media (max-width: 700px) {
            .provider-stats {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

<header>
    <a href="../index.php" class="logo">
    <img src="../images/logo.png" alt="Logo">
    <span>Penyedia E-Commerce Jasa</span>
</a>

    <nav>
        <a href="dashboard.php">Dashboard</a>
        <a href="../logout.php">Logout</a>
    </nav>
</header>

<main class="container">

    <div class="provider-welcome">
        <h1 class="section-title">
            Selamat Datang,
            <?= htmlspecialchars($_SESSION["nama"] ?? "Penyedia") ?>
        </h1>

        <p>
            Kelola jasa dan pantau pesanan pelanggan melalui dashboard ini.
        </p>
    </div>

    <!-- Statistik -->
    <div class="provider-stats">

        <div class="provider-card">
            <h3>Jasa Saya</h3>
            <div class="number">
                <?= (int) $total_jasa ?>
            </div>
        </div>

        <div class="provider-card">
            <h3>Total Pesanan</h3>
            <div class="number">
                <?= (int) $total_pesanan ?>
            </div>
        </div>

        <div class="provider-card">
            <h3>Pesanan Selesai</h3>
            <div class="number">
                <?= (int) $total_selesai ?>
            </div>
        </div>

    </div>

    <!-- Daftar Pesanan -->
    <h2>Pesanan Masuk</h2>

    <div class="provider-table">

        <?php if (mysqli_num_rows($pesanan) > 0): ?>

            <div style="overflow-x:auto">
                <table>

                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Pelanggan</th>
                            <th>Nama Jasa</th>
                            <th>Harga</th>
                            <th>Pembayaran</th>
                            <th>Status Pesanan</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php while ($p = mysqli_fetch_assoc($pesanan)): ?>

                        <tr>
                            <td><?= (int) $p["id"] ?></td>

                            <td>
                                <?= htmlspecialchars($p["nama_pelanggan"]) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($p["nama_layanan"]) ?>
                            </td>

                            <td>
                                Rp <?= number_format(
                                    (float) $p["harga"], 0, ",", "."
                                ) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars(
                                    $p["status_pembayaran"] ?? "-"
                                ) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($p["status"] ?? "Menunggu") ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($p["tanggal_pesan"] ?? "-") ?>
                            </td>

                            <td>
                                <form method="POST" action="update_status.php">

                                    <input type="hidden"
                                           name="order_id"
                                           value="<?= (int) $p["id"] ?>">

                                    <select name="status" required>
                                        <option value="Menunggu"
                                            <?= ($p["status"] ?? "") === "Menunggu" ? "selected" : "" ?>>
                                            Menunggu
                                        </option>

                                        <option value="Diproses"
                                            <?= ($p["status"] ?? "") === "Diproses" ? "selected" : "" ?>>
                                            Diproses
                                        </option>

                                        <option value="Selesai"
                                            <?= ($p["status"] ?? "") === "Selesai" ? "selected" : "" ?>>
                                            Selesai
                                        </option>
                                    </select>

                                    <button type="submit" class="btn">
                                        Simpan
                                    </button>

                                </form>
                            </td>
                        </tr>

                    <?php endwhile; ?>

                    </tbody>

                </table>
            </div>

        <?php else: ?>

            <div class="empty-message">
                <h3>Belum Ada Pesanan</h3>
                <p>Pesanan pelanggan untuk jasa kamu akan muncul di sini.</p>
            </div>

        <?php endif; ?>

    </div>

</main>

</body>
</html>
