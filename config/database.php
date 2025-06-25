<?php
$host = 'localhost';
$db = 'u347334547_sis_inv';
$user = 'u347334547_sis_use';
$pass = 'CH7322a#';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
