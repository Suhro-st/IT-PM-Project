<?php
$hostname = "localhost";
$username = "root";
$password = "";
$database = "ecommerce_kampus";

$conn = mysqli_connect(
    $hostname,
    $username,
    $password,
    $database
);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>
