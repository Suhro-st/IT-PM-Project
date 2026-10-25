<?php
session_start();
require_once "koneksi.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nama = trim($_POST['nama']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if ($nama === "" || $email === "" || $password === "") {
        $error = "Semua kolom wajib diisi.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format email tidak valid.";
    } elseif (strlen($password) < 6) {
        $error = "Password minimal 6 karakter.";
    } else {
        $stmt = mysqli_prepare(
            $conn,
            "SELECT id FROM users WHERE email = ?"
        );

        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            $error = "Email sudah terdaftar.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);

            $stmt = mysqli_prepare(
                $conn,
                "INSERT INTO users (nama, email, password)
                 VALUES (?, ?, ?)"
            );

            mysqli_stmt_bind_param(
                $stmt,
                "sss",
                $nama,
                $email,
                $hash
            );

            if (mysqli_stmt_execute($stmt)) {
                header("Location: login.php?daftar=berhasil");
                exit;
            } else {
                $error = "Registrasi gagal.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi - E-Commerce Jasa</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="form-container">

    <h2>Daftar Akun</h2>

    <p style="text-align:center;">
        Buat akun E-Commerce Jasa Kampus
    </p>

    <?php if ($error): ?>
        <div class="alert">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST">

        <label for="nama">Nama Lengkap</label>
        <input
            type="text"
            id="nama"
            name="nama"
            placeholder="Masukkan nama lengkap"
            required
        >

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
            placeholder="Minimal 6 karakter"
            minlength="6"
            required
        >

        <button type="submit" class="btn full">
            Daftar
        </button>

    </form>

    <p style="text-align:center; margin-top:20px;">
        Sudah punya akun?
        <a href="login.php">Login</a>
    </p>

    <p style="text-align:center;">
        <a href="index.php">Kembali ke Beranda</a>
    </p>

</div>

</body>
</html>
