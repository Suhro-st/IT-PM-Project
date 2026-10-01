<?php
session_start();
require_once "koneksi.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    die("ID pesanan tidak valid.");
}

$stmt = mysqli_prepare(
    $conn,
    "SELECT * FROM orders
     WHERE id = ? AND user_id = ?"
);

mysqli_stmt_bind_param(
    $stmt,
    "ii",
    $id,
    $_SESSION['user_id']
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$order = mysqli_fetch_assoc($result);

if (!$order) {
    die("Pesanan tidak ditemukan.");
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $metode = $_POST['metode'] ?? '';

    $metode_valid = [
        'Transfer Bank',
        'E-Wallet',
        'Tunai'
    ];

    if (!in_array($metode, $metode_valid, true)) {
        $error = "Metode pembayaran tidak valid.";
    } elseif ($order['status_pembayaran'] !== 'Belum Dibayar') {
        $error = "Pesanan ini sudah memiliki status pembayaran.";
    } else {
        $status_pembayaran = 'Menunggu Verifikasi';

        $stmt = mysqli_prepare(
            $conn,
            "UPDATE orders
             SET metode_pembayaran = ?,
                 status_pembayaran = ?
             WHERE id = ?
             AND user_id = ?
             AND status_pembayaran = 'Belum Dibayar'"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "ssii",
            $metode,
            $status_pembayaran,
            $id,
            $_SESSION['user_id']
        );

        if (mysqli_stmt_execute($stmt) &&
            mysqli_stmt_affected_rows($stmt) === 1) {
            header("Location: order_status.php");
            exit;
        }

        $error = "Pembayaran gagal diproses. Silakan coba lagi.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Pesanan</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="form-container">

    <h2>Pembayaran Pesanan</h2>

    <p>Silakan periksa pesanan dan pilih metode pembayaran.</p>

    <hr>

    <label>Nama Jasa</label>
    <p>
        <strong>
            <?= htmlspecialchars($order['nama_layanan']) ?>
        </strong>
    </p>

    <label>Total Pembayaran</label>
    <h2 class="price">
        Rp <?= number_format($order['harga'], 0, ',', '.') ?>
    </h2>

    <label>Status Pembayaran</label>
    <p>
        <?= htmlspecialchars($order['status_pembayaran']) ?>
    </p>

    <?php if ($error): ?>
        <div class="alert">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <?php if ($order['status_pembayaran'] === 'Belum Dibayar'): ?>

        <form method="POST">

            <label for="metode">Metode Pembayaran</label>

            <select name="metode" id="metode" required>
                <option value="">Pilih Metode Pembayaran</option>
                <option value="Transfer Bank">Transfer Bank</option>
                <option value="E-Wallet">E-Wallet</option>
                <option value="Tunai">Tunai</option>
            </select>

            <button type="submit" class="btn full">
                Konfirmasi Pembayaran
            </button>

        </form>

    <?php else: ?>

        <div class="success">
            Pembayaran sudah dikirim untuk verifikasi.
        </div>

    <?php endif; ?>

    <p style="text-align:center; margin-top:20px;">
        <a href="order_status.php">
            Lihat Status Pesanan
        </a>
    </p>

</div>

</body>
</html>
