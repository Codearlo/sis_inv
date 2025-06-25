<?php
require_once '../config/database.php';
require_once '../app/Infrastructure/Auth/AuthService.php';

$auth = new AuthService($pdo);

if (!$auth->estaAutenticado()) {
    header("Location: login.php");
    exit;
}

// Redirigir al dashboard una vez autenticado
header("Location: dashboard.php");
exit;