<?php
session_start();
require_once __DIR__ . "/../koneksi.php";

// Cek apakah yang login adalah admin
if (
    !isset($_SESSION['user_id']) ||
    !isset($_SESSION['role']) ||
    $_SESSION['role'] !== 'admin'
) {
    header("Location: ../login_admin.php");
    exit;
}

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nama_layanan = trim($_POST['nama_layanan'] ?? '');
    $kategori = trim($_POST['kategori'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $harga = $_POST['harga'] ?? '';
    $status = $_POST['status'] ?? 'Aktif';

    if (
        $nama_layanan === '' ||
        $kategori === '' ||
        $deskripsi === '' ||
        $harga === ''
    ) {
        $error = "Semua data harus diisi.";
    } else {

        $stmt = mysqli_prepare(
            $conn,
            "INSERT INTO services
            (user_id, nama_layanan, kategori, deskripsi, harga, status)
            VALUES (?, ?, ?, ?, ?, ?)"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "isssis",
            $_SESSION['user_id'],
            $nama_layanan,
            $kategori,
            $deskripsi,
            $harga,
            $status
        );

        if (mysqli_stmt_execute($stmt)) {
            $success = "Jasa berhasil ditambahkan.";
        } else {
            $error = "Jasa gagal ditambahkan: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Tambah Jasa</title>

    <link rel="stylesheet" href="../style.css">
</head>

<body>

<header>

    <div class="logo">
        E-Commerce Jasa
    </div>

    <nav>
        <a href="dashboard.php">Dashboard</a>
        <a href="manage.php">Kelola Jasa</a>
        <a href="../index.php">Beranda</a>
        <a href="../logout.php">Logout</a>
    </nav>

</header>

<main class="container">

    <h1 class="section-title">
        Tambah Jasa
    </h1>

    <?php if ($error): ?>

        <div class="alert">
            <?= htmlspecialchars($error) ?>
        </div>

    <?php endif; ?>

    <?php if ($success): ?>

        <div class="success">
            <?= htmlspecialchars($success) ?>
        </div>

    <?php endif; ?>

    <div class="form-container">

        <form method="POST">

            <label>Nama Jasa</label>

            <input
                type="text"
                name="nama_layanan"
                placeholder="Contoh: Pembuatan Website"
                required
            >

            <label>Kategori</label>

            <input
                type="text"
                name="kategori"
                placeholder="Contoh: Pemrograman"
                required
            >

            <label>Deskripsi</label>

            <textarea
                name="deskripsi"
                rows="5"
                placeholder="Masukkan deskripsi jasa"
                required
            ></textarea>

            <label>Harga</label>

            <input
                type="number"
                name="harga"
                placeholder="Contoh: 150000"
                min="0"
                required
            >

            <label>Status</label>

            <select name="status">

                <option value="Aktif">
                    Aktif
                </option>

                <option value="Nonaktif">
                    Nonaktif
                </option>

            </select>

            <button
                type="submit"
                class="btn full"
            >
                Tambah Jasa
            </button>

        </form>

    </div>

</main>

<footer>

    <p>
        &copy; 2026 E-Commerce Jasa Kampus
    </p>

</footer>

</body>
</html>
