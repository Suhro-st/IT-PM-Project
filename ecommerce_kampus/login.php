<?php
session_start();
require_once "koneksi.php";

$error = "";

// Proses login
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if ($email === "" || $password === "") {
        $error = "Email dan password wajib diisi.";
    } else {
        // Mencari pengguna berdasarkan email
        $stmt = mysqli_prepare(
            $conn,
            "SELECT id, nama, email, password
             FROM users
             WHERE email = ?"
        );

        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);

        // Memeriksa password
        if ($user && password_verify($password, $user['password'])) {

            // Membuat sesi login
            session_regenerate_id(true);

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nama'] = $user['nama'];

            // Masuk ke halaman utama
            header("Location: index.php");
            exit;

        } else {
            $error = "Email atau password salah.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - E-Commerce Jasa Kampus</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="form-container">

    <h2>Login</h2>

    <p style="text-align:center;">
        Masuk ke E-Commerce Jasa Kampus
    </p>

    <?php if (isset($_GET['daftar']) && $_GET['daftar'] === 'berhasil'): ?>
        <div class="success">
            Registrasi berhasil. Silakan login.
        </div>
    <?php endif; ?>

    <?php if ($error !== ""): ?>
        <div class="alert">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST">

        <label for="email">Email</label>
        <input
            type="email"
            id="email"
            name="email"
            placeholder="Masukkan email"
            required
        >

        <label for="password">Password</label>
        <input
            type="password"
            id="password"
            name="password"
            placeholder="Masukkan password"
            required
        >

        <button type="submit" class="btn full">
            Login
        </button>

    </form>

    <p style="text-align:center; margin-top:20px;">
        Belum punya akun?
        <a href="register.php">Daftar</a>
    </p>

    <p style="text-align:center;">
        <a href="index.php">Kembali ke Beranda</a>
    </p>

</div>

</body>
</html>
