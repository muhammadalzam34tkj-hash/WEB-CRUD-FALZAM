<?php
$host = "localhost";
$user = "user_kata";
$pass = "Password123!";
$db   = "db_kata";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>

