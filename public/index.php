<?php
require_once '../config/database.php';
require_once '../app/Infrastructure/Auth/AuthService.php';

$auth = new AuthService($pdo);

if (!$auth->estaAutenticado()) {
    header("Location: login.php");
    exit;
}

echo "<h1>Bienvenido a la plataforma</h1>";
echo "<a href='logout.php'>Cerrar sesión</a>";
