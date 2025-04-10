<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "barbearia_do_banco";

// Cria a conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Checa a conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}
?>