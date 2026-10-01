<?php
session_start();
require_once "koneksi.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nama = trim($_POST['nama_layanan']);
    $kategori = trim($_POST['kategori']);
    $deskripsi = trim($_POST['deskripsi']);
    $harga = filter_var($_POST['harga'], FILTER_VALIDATE_FLOAT);

    if ($nama === "" || $kategori === "" || $deskripsi === "") {
        $error = "Semua kolom wajib diisi.";
    } elseif ($harga === false || $harga <= 0) {
        $error = "Harga harus berupa angka lebih dari 0.";
    } else {
        $user_id = $_SESSION['user_id'];

        $stmt = mysqli_prepare(
            $conn,
            "INSERT INTO services
             (user_id, nama_layanan, kategori, deskripsi, harga)
             VALUES (?, ?, ?, ?, ?)"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "isssd",
            $user_id,
            $nama,
            $kategori,
            $deskripsi,
            $harga
        );

        if (mysqli_stmt_execute($stmt)) {
            header("Location: services.php");
            exit;
        } else {
            $error = "Gagal menambahkan jasa.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Jasa</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <div class="logo">E-Commerce Jasa</div>
    <nav>
        <a href="index.php">Beranda</a>
        <a href="services.php">Katalog</a>
        <a href="order_status.php">Pesanan</a>
        <a href="profile.php">Profil</a>
    </nav>
</header>

<div class="form-container">
    <h2>Tambah Jasa</h2>

    <?php if ($error): ?>
        <div class="alert"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">
        <label>Nama Jasa</label>
        <input type="text" name="nama_layanan" required>

        <label>Kategori</label>
        <select name="kategori" required>
            <option value="">Pilih kategori</option>
            <option value="Desain Grafis">Desain Grafis</option>
            <option value="Pemrograman">Pemrograman</option>
            <option value="Pengetikan">Pengetikan</option>
            <option value="Multimedia">Multimedia</option>
            <option value="Pendidikan">Pendidikan</option>
            <option value="Lainnya">Lainnya</option>
        </select>

        <label>Deskripsi</label>
        <textarea name="deskripsi" rows="5" required></textarea>

        <label>Harga (Rp)</label>
        <input type="number" name="harga" min="1" step="1" required>

        <button type="submit" class="btn full">Simpan Jasa</button>
    </form>
</div>

</body>
</html>
