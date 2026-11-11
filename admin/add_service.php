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

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $penyedia_id = (int) ($_POST["penyedia_id"] ?? 0);
    $nama_layanan = trim($_POST["nama_layanan"] ?? "");
    $kategori = trim($_POST["kategori"] ?? "");
    $deskripsi = trim($_POST["deskripsi"] ?? "");
    $harga = filter_var($_POST["harga"] ?? null, FILTER_VALIDATE_INT);
    $status = $_POST["status"] ?? "Aktif";

    if (
        $penyedia_id <= 0 ||
        $nama_layanan === "" ||
        $kategori === "" ||
        $deskripsi === "" ||
        $harga === false ||
        $harga === null ||
        $harga < 0 ||
        !in_array($status, ["Aktif", "Nonaktif"], true)
    ) {
        $error = "Periksa kembali semua data.";
    } else {
        $cek = mysqli_prepare(
            $conn,
            "SELECT id FROM users WHERE id = ? AND role = 'penyedia'"
        );
        mysqli_stmt_bind_param($cek, "i", $penyedia_id);
        mysqli_stmt_execute($cek);
        $hasil = mysqli_stmt_get_result($cek);

        if (mysqli_num_rows($hasil) === 0) {
            $error = "Penyedia jasa tidak ditemukan.";
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
                $penyedia_id,
                $nama_layanan,
                $kategori,
                $deskripsi,
                $harga,
                $status
            );

            if (mysqli_stmt_execute($stmt)) {
                $success = "Jasa berhasil ditambahkan.";
            } else {
                $error = "Jasa gagal ditambahkan.";
            }
        }
    }
}

$penyedia_list = mysqli_query(
    $conn,
    "SELECT id, nama FROM users
     WHERE role = 'penyedia'
     ORDER BY nama ASC"
);
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
    <div class="logo">E-Commerce Jasa</div>
    <nav>
        <a href="dashboard.php">Dashboard</a>
        <a href="manage.php">Kelola Jasa</a>
        <a href="../index.php">Beranda</a>
        <a href="../logout.php">Logout</a>
    </nav>
</header>

<main class="container">
    <h1 class="section-title">Tambah Jasa</h1>

    <?php if ($error !== ""): ?>
        <div class="alert"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($success !== ""): ?>
        <div class="success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <div class="form-container">
        <?php if (mysqli_num_rows($penyedia_list) === 0): ?>
            <p>Belum ada akun penyedia. Minta penyedia mendaftar terlebih dahulu.</p>
        <?php else: ?>
            <form method="POST">
                <label>Pilih Penyedia Jasa</label>
                <select name="penyedia_id" required>
                    <option value="">-- Pilih Penyedia --</option>
                    <?php while ($p = mysqli_fetch_assoc($penyedia_list)): ?>
                        <option value="<?= (int) $p["id"] ?>">
                            <?= htmlspecialchars($p["nama"]) ?>
                        </option>
                    <?php endwhile; ?>
                </select>

                <label>Nama Jasa</label>
                <input type="text" name="nama_layanan" required>

                <label>Kategori</label>
                <input type="text" name="kategori" required>

                <label>Deskripsi</label>
                <textarea name="deskripsi" rows="5" required></textarea>

                <label>Harga (Rp)</label>
                <input type="number" name="harga" min="0" required>

                <label>Status</label>
                <select name="status">
                    <option value="Aktif">Aktif</option>
                    <option value="Nonaktif">Nonaktif</option>
                </select>

                <button type="submit" class="btn full">Tambah Jasa</button>
            </form>
        <?php endif; ?>
    </div>
</main>
</body>
</html>
