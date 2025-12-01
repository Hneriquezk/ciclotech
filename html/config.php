<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "ciclotech";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

// Definir charset para evitar problemas com acentos
$conn->set_charset("utf8mb4");
?>