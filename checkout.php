<?php
session_start();
require_once "koneksi.php";

// Periksa login pelanggan
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Ambil ID jasa
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    die("ID jasa tidak valid.");
}

// Ambil data jasa yang masih aktif
$stmt = mysqli_prepare(
    $conn,
    "SELECT * FROM services
     WHERE id = ? AND status = 'Aktif'"
);

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$service = mysqli_fetch_assoc($result);

if (!$service) {
    die("Jasa tidak ditemukan.");
}

// Pelanggan tidak boleh memesan jasa sendiri
if ($service['user_id'] == $_SESSION['user_id']) {
    die("Kamu tidak dapat memesan jasa milik sendiri.");
}

$error = "";

// Proses membuat pesanan
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $user_id = $_SESSION['user_id'];
    $service_id = $service['id'];
    $nama_layanan = $service['nama_layanan'];
    $harga = $service['harga'];

    $stmt = mysqli_prepare(
        $conn,
        "INSERT INTO orders
         (user_id, service_id, nama_layanan, harga)
         VALUES (?, ?, ?, ?)"
    );

    mysqli_stmt_bind_param(
        $stmt,
        "iisd",
        $user_id,
        $service_id,
        $nama_layanan,
        $harga
    );

    if (mysqli_stmt_execute($stmt)) {

        $order_id = mysqli_insert_id($conn);

        // Lanjut ke halaman pembayaran
        header("Location: payment.php?id=" . $order_id);
        exit;

    } else {
        $error = "Pesanan gagal dibuat. Silakan coba lagi.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout Pesanan</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

<div class="checkout-container">

    <div class="checkout-card">

        <h1>Checkout Pesanan</h1>

        <p class="checkout-subtitle">
            Periksa kembali jasa yang ingin kamu pesan.
        </p>

        <div class="detail-checkout">

            <div class="detail-row">
                <span>Nama Jasa</span>
                <strong>
                    <?= htmlspecialchars($service['nama_layanan']) ?>
                </strong>
            </div>

            <div class="detail-row">
                <span>Kategori</span>
                <strong>
                    <?= htmlspecialchars($service['kategori']) ?>
                </strong>
            </div>

            <div class="detail-row">
                <span>Deskripsi</span>
                <strong class="deskripsi">
                    <?= htmlspecialchars($service['deskripsi']) ?>
                </strong>
            </div>

            <div class="detail-row total-row">
                <span>Total Harga</span>
                <strong class="harga">
                    Rp <?= number_format($service['harga'], 0, ',', '.') ?>
                </strong>
            </div>

        </div>

        <?php if ($error): ?>
            <div class="alert">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <!-- Tombol Buat Pesanan berada di dalam kotak -->
        <form method="POST">

            <button type="submit" class="btn full">
                Buat Pesanan
            </button>

        </form>

        <a href="detail.php?id=<?= (int) $service['id'] ?>"
           class="checkout-back">
            Kembali ke Detail Jasa
        </a>

    </div>

</div>

</body>
</html>
