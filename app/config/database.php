<?php

$host = '127.0.0.1';
$db = 'reserva_laboratorios';
$user = 'root';
$pass = 'admin';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

try {
    $bancoDados = new PDO($dsn, $user, $pass);
} catch (\PDOException $e) {
    exit("Erro na conexão com banco de dados!");
}


?>
