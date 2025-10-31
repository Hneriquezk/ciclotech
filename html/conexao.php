<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "ciclotech";

$conn = new mysqli(hostname: $host, username: $user, password: $pass, database: $db);
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}
$conn->set_charset(charset: "utf8mb4");