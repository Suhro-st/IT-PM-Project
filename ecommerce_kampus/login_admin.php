<?php
session_start();
require_once "koneksi.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if ($email === "" || $password === "") {
        $error = "Email dan password wajib diisi.";
    } else {
        $stmt = mysqli_prepare(
            $conn,
            "SELECT id, nama, email, password, role
             FROM users
             WHERE email = ? AND role = 'admin'"
        );

        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $admin = mysqli_fetch_assoc($result);

        if ($admin && password_verify($password, $admin['password'])) {
            session_regenerate_id(true);

            $_SESSION['user_id'] = $admin['id'];
            $_SESSION['nama'] = $admin['nama'];
            $_SESSION['role'] = $admin['role'];

            header("Location: admin/dashboard.php");
            exit;
        } else {
            $error = "Email atau password admin salah.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="form-container">

    <h2>Login Admin</h2>

    <p style="text-align:center;">
        E-Commerce Jasa Kampus
    </p>

    <?php if ($error !== ""): ?>
        <div class="alert">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST">

        <label for="email">Email Admin</label>
        <input
            type="email"
            id="email"
            name="email"
            placeholder="Masukkan email admin"
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
            Login Admin
        </button>

    </form>

    <p style="text-align:center; margin-top:20px;">
        <a href="index.php">Kembali ke Beranda</a>
    </p>

</div>

</body>
</html>
