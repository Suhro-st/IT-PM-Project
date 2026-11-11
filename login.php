<?php
session_start();
require_once "koneksi.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    // Mencari akun berdasarkan email
    $stmt = mysqli_prepare(
        $conn,
        "SELECT id, nama, email, password, role
         FROM users
         WHERE email = ?
         LIMIT 1"
    );

    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    // Memeriksa email dan password
    if ($user && password_verify($password, $user["password"])) {

        // Admin harus menggunakan halaman login admin
        if ($user["role"] === "admin") {

            // Bersihkan sesi sebelumnya
            session_unset();
            session_regenerate_id(true);

            $error = "Akun admin harus login melalui halaman admin.";

        } else {

            // Bersihkan sesi akun sebelumnya
            session_unset();
            session_regenerate_id(true);

            // Simpan sesi akun yang berhasil login
            $_SESSION["user_id"] = (int) $user["id"];
            $_SESSION["nama"] = $user["nama"];
            $_SESSION["role"] = $user["role"];

            // Arahkan sesuai jenis akun
            if ($user["role"] === "penyedia") {

                header("Location: penyedia/dashboard.php");
                exit;

            } elseif ($user["role"] === "user") {

                header("Location: index.php");
                exit;

            } else {

                session_unset();
                session_regenerate_id(true);

                $error = "Jenis akun tidak dikenali.";
            }
        }

    } else {

        $error = "Email atau password salah.";

    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login - E-Commerce Jasa</title>

    <link rel="stylesheet" href="style.css">
</head>

<body>

<header>
    <div class="logo">E-Commerce Jasa</div>

    <nav>
        <a href="index.php">Beranda</a>
    </nav>
</header>

<main class="container">

    <h1 class="section-title">Login</h1>

    <?php if ($error !== ""): ?>
        <div class="alert">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <div class="form-container">

        <form method="POST">

            <label>Email</label>
            <input
                type="email"
                name="email"
                placeholder="Masukkan email"
                required
            >

            <label>Password</label>
            <input
                type="password"
                name="password"
                placeholder="Masukkan password"
                required
            >

            <button type="submit" class="btn full">
                Login
            </button>

        </form>

        <p>
            Belum punya akun?
            <a href="register.php">Daftar</a>
        </p>

        <p>
            <a href="login_admin.php">Login sebagai admin</a>
        </p>

    </div>

</main>

</body>
</html>
