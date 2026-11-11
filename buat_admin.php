
<?php
require_once "koneksi.php";

$nama = "Admin";
$email = "admin@gmail.com";
$password = password_hash("admin123", PASSWORD_DEFAULT);
$role = "admin";

$stmt = mysqli_prepare(
    $conn,
    "SELECT id FROM users WHERE email = ?"
);
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) > 0) {
    echo "Email sudah terdaftar. Periksa role akun tersebut.";
} else {
    $stmt = mysqli_prepare(
        $conn,
        "INSERT INTO users (nama, email, password, role)
         VALUES (?, ?, ?, ?)"
    );

    mysqli_stmt_bind_param(
        $stmt, "ssss", $nama, $email, $password, $role
    );

    if (mysqli_stmt_execute($stmt)) {
        echo "Akun admin berhasil dibuat!";
    } else {
        echo "Gagal: " . mysqli_stmt_error($stmt);
    }
}
?>
