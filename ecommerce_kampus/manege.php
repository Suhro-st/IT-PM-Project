<?php
session_start();
require_once __DIR__ . "/../koneksi.php";

if (
    !isset($_SESSION['user_id']) ||
    $_SESSION['role'] !== 'admin'
) {
    die("Akses ditolak.");
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $aksi = $_POST['aksi'] ?? '';

    if ($aksi === 'status_jasa') {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $status = $_POST['status'] ?? '';

        if ($id && in_array($status, ['Aktif', 'Nonaktif'], true)) {
            $stmt = mysqli_prepare(
                $conn,
                "UPDATE services SET status = ? WHERE id = ?"
            );

            mysqli_stmt_bind_param($stmt, "si", $status, $id);
            mysqli_stmt_execute($stmt);
            $message = "Status jasa berhasil diperbarui.";
        }
    }

    if ($aksi === 'status_pesanan') {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $status = $_POST['status'] ?? '';

        $status_valid = [
            'Menunggu',
            'Diproses',
            'Selesai',
            'Dibatalkan'
        ];

        if ($id && in_array($status, $status_valid, true)) {
            $stmt = mysqli_prepare(
                $conn,
                "UPDATE orders SET status = ? WHERE id = ?"
            );

            mysqli_stmt_bind_param($stmt, "si", $status, $id);
            mysqli_stmt_execute($stmt);
            $message = "Status pesanan berhasil diperbarui.";
        }
    }

    if ($aksi === 'status_pembayaran') {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $status = $_POST['status'] ?? '';

        $status_valid = ['Berhasil', 'Ditolak'];

        if ($id && in_array($status, $status_valid, true)) {
            $stmt = mysqli_prepare(
                $conn,
                "UPDATE orders
                 SET status_pembayaran = ?
                 WHERE id = ?
                 AND status_pembayaran = 'Menunggu Verifikasi'"
            );

            mysqli_stmt_bind_param($stmt, "si", $status, $id);
            mysqli_stmt_execute($stmt);
            $message = "Status pembayaran berhasil diperbarui.";
        }
    }
}

$services = mysqli_query(
    $conn,
    "SELECT services.*, users.nama
     FROM services
     JOIN users ON services.user_id = users.id
     ORDER BY services.id DESC"
);

$orders = mysqli_query(
    $conn,
    "SELECT * FROM orders ORDER BY tanggal_pesan DESC"
);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Jasa</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>


<header>
    <div class="logo">E-Commerce Jasa Kampus</div>

    <nav>
        <a href="../index.php">Beranda</a>
        <a href="dashboard.php">Dashboard</a>
        <a href="manage.php">Kelola Jasa</a>
        <a href="../logout.php">Logout</a>
    </nav>
</header>

<main class="container">
    <h1 class="section-title">Kelola Jasa</h1>

    <?php if ($message): ?>
        <div class="success"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <div class="table-wrapper">
        <table>
            <tr>
                <th>ID</th>
                <th>Nama Jasa</th>
                <th>Penyedia</th>
                <th>Harga</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>

            <?php while ($service = mysqli_fetch_assoc($services)): ?>
                <tr>
                    <td><?= $service['id'] ?></td>
                    <td><?= htmlspecialchars($service['nama_layanan']) ?></td>
                    <td><?= htmlspecialchars($service['nama']) ?></td>
                    <td>
                        Rp <?= number_format($service['harga'], 0, ',', '.') ?>
                    </td>
                    <td><?= htmlspecialchars($service['status']) ?></td>
                    <td>
                        <form method="POST" class="inline-form">
                            <input type="hidden" name="aksi" value="status_jasa">
                            <input
                                type="hidden"
                                name="id"
                                value="<?= $service['id'] ?>"
                            >

                            <select name="status">
                                <option value="Aktif"
                                    <?= $service['status'] === 'Aktif' ? 'selected' : '' ?>>
                                    Aktif
                                </option>
                                <option value="Nonaktif"
                                    <?= $service['status'] === 'Nonaktif' ? 'selected' : '' ?>>
                                    Nonaktif
                                </option>
                            </select>

                            <button class="btn small" type="submit">
                                Simpan
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>

    <h1 class="section-title">Kelola Pesanan</h1>

    <div class="table-wrapper">
        <table>
            <tr>
                <th>ID</th>
                <th>Jasa</th>
                <th>Status Pesanan</th>
                <th>Status Pembayaran</th>
                <th>Aksi</th>
            </tr>

            <?php while ($order = mysqli_fetch_assoc($orders)): ?>
                <tr>
                    <td><?= $order['id'] ?></td>
                    <td><?= htmlspecialchars($order['nama_layanan']) ?></td>
                    <td>
                        <form method="POST" class="inline-form">
                            <input type="hidden" name="aksi" value="status_pesanan">
                            <input
                                type="hidden"
                                name="id"
                                value="<?= $order['id'] ?>"
                            >

                            <select name="status">
                                <?php
                                $statuses = [
                                    'Menunggu',
                                    'Diproses',
                                    'Selesai',
                                    'Dibatalkan'
                                ];

                                foreach ($statuses as $status):
                                ?>
                                    <option
                                        value="<?= $status ?>"
                                        <?= $order['status'] === $status ? 'selected' : '' ?>
                                    >
                                        <?= $status ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>

                            <button class="btn small" type="submit">
                                Simpan
                            </button>
                        </form>
                    </td>
                    <td>
                        <?= htmlspecialchars($order['status_pembayaran']) ?>
                    </td>
                    <td>
                        <?php if ($order['status_pembayaran'] === 'Menunggu Verifikasi'): ?>
                            <form method="POST" class="inline-form">
                                <input
                                    type="hidden"
                                    name="aksi"
                                    value="status_pembayaran"
                                >
                                <input
                                    type="hidden"
                                    name="id"
                                    value="<?= $order['id'] ?>"
                                >
                                <select name="status">
                                    <option value="Berhasil">Berhasil</option>
                                    <option value="Ditolak">Ditolak</option>
                                </select>
                                <button class="btn small" type="submit">
                                    Verifikasi
                                </button>
                            </form>
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
