<?php
session_start();
require_once "koneksi.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    die("ID jasa tidak valid.");
}

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

if ($service['user_id'] == $_SESSION['user_id']) {
    die("Kamu tidak dapat memesan jasa milik sendiri.");
}

$error = "";

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

        header("Location: payment.php?id=" . $order_id);
        exit;
    }

    $error = "Pesanan gagal dibuat.";
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

<div class="form-container">

    <h2>Checkout Pesanan</h2>

    <p>Periksa kembali jasa yang ingin kamu pesan.</p>

    <hr>

    <label>Nama Jasa</label>
    <p>
        <strong>
            <?= htmlspecialchars($service['nama_layanan']) ?>
        </strong>
    </p>

    <label>Kategori</label>
    <p>
        <?= htmlspecialchars($service['kategori']) ?>
    </p>

    <label>Deskripsi</label>
    <p>
        <?= htmlspecialchars($service['deskripsi']) ?>
    </p>

    <label>Total Harga</label>
    <h2 class="price">
        Rp <?= number_format($service['harga'], 0, ',', '.') ?>
    </h2>

    <?php if ($error): ?>
        <div class="alert">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <button type="submit" class="btn full">
            Buat Pesanan
        </button>
    </form>

    <p style="text-align:center; margin-top:20px;">
        <a href="detail.php?id=<?= $service['id'] ?>">
            Kembali ke Detail Jasa
        </a>
    </p>

</div>

</body>
</html>
