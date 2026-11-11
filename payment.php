<?php
session_start();
require_once __DIR__ . "/koneksi.php";

// Pastikan pelanggan sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Ambil ID pesanan dari URL
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    die("ID pesanan tidak valid.");
}

// Ambil data pesanan milik pelanggan
$stmt = mysqli_prepare(
    $conn,
    "SELECT * FROM orders WHERE id = ? AND user_id = ?"
);

mysqli_stmt_bind_param($stmt, "ii", $id, $_SESSION['user_id']);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$order = mysqli_fetch_assoc($result);

if (!$order) {
    die("Pesanan tidak ditemukan.");
}

$error = "";

// Proses pembayaran
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $metode = $_POST['metode'] ?? '';

    // Validasi metode pembayaran
    if (!in_array($metode, ['QRIS', 'Tunai'], true)) {
        $error = "Silakan pilih metode pembayaran.";
    } elseif ($order['status_pembayaran'] !== 'Belum Dibayar') {
        $error = "Pesanan ini sudah memiliki status pembayaran.";
    } else {

        // Tentukan status berdasarkan metode pembayaran
        if ($metode === 'QRIS') {
            $status_pembayaran = 'Menunggu Verifikasi';
        } else {
            $status_pembayaran = 'Menunggu Pembayaran Tunai';
        }

        // Simpan metode dan status pembayaran
        $stmt = mysqli_prepare(
            $conn,
            "UPDATE orders
             SET metode_pembayaran = ?, status_pembayaran = ?
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

        if (
            mysqli_stmt_execute($stmt) &&
            mysqli_stmt_affected_rows($stmt) === 1
        ) {
            header("Location: order_status.php");
            exit;
        } else {
            $error = "Pembayaran gagal diproses. Silakan coba lagi.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran - E-Commerce Jasa</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

<header>
    <div class="logo">E-Commerce Jasa</div>

    <nav>
        <a href="index.php">Beranda</a>
        <a href="order_status.php">Pesanan Saya</a>
    </nav>
</header>

<main class="container">

    <h1 class="section-title">Pembayaran Pesanan</h1>

    <div class="form-container">

        <h2>Detail Pesanan</h2>

        <p>
            <strong>Nama Jasa:</strong>
            <?= htmlspecialchars($order['nama_layanan']) ?>
        </p>

        <p>
            <strong>Total Harga:</strong>
            Rp <?= number_format((float)$order['harga'], 0, ',', '.') ?>
        </p>

        <?php if ($error !== ""): ?>
            <div class="alert">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST">

            <label for="metode">Metode Pembayaran</label>

            <select
                name="metode"
                id="metode"
                required
                onchange="tampilkanQRIS()"
            >
                <option value="">Pilih Pembayaran</option>
                <option value="QRIS">QRIS</option>
                <option value="Tunai">Tunai</option>
            </select>

            <!-- Tampilan QRIS -->
            <div
                id="qris-box"
                style="display:none; text-align:center; margin-top:20px;"
            >

                <h3>Scan QRIS untuk Membayar</h3>

                <img
                    src="images/qris.jpg"
                    alt="QRIS Pembayaran"
                    style="width:220px; max-width:100%;"
                >

                <p>
                    Silakan scan QRIS dan lakukan pembayaran.
                </p>

                <p>
                    Setelah membayar, admin akan memverifikasi pembayaranmu.
                </p>

            </div>

            <!-- Informasi pembayaran tunai -->
            <div
                id="tunai-box"
                style="display:none; margin-top:20px;"
            >
                <p>
                    Pembayaran dilakukan secara tunai.
                    Silakan lakukan pembayaran sesuai kesepakatan.
                    Admin akan mengonfirmasi setelah menerima pembayaran.
                </p>
            </div>

            <button type="submit" class="btn full">
                Konfirmasi Pembayaran
            </button>

        </form>

        <p style="text-align:center; margin-top:20px;">
            <a href="order_status.php">Kembali ke Pesanan Saya</a>
        </p>

    </div>

</main>

<script>
function tampilkanQRIS() {
    const metode = document.getElementById("metode").value;

    const qris = document.getElementById("qris-box");
    const tunai = document.getElementById("tunai-box");

    qris.style.display = metode === "QRIS" ? "block" : "none";
    tunai.style.display = metode === "Tunai" ? "block" : "none";
}
</script>

</body>
</html>
