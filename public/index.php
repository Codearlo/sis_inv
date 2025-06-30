<?php
require_once '../config/database.php';
require_once '../app/Infrastructure/Auth/AuthService.php';

$auth = new AuthService($pdo);

if (!$auth->estaAutenticado()) {
    header("Location: login.php");
    exit;
}

// Verificar si necesita onboarding
if ($auth->necesitaOnboarding()) {
    header("Location: onboarding.php");
    exit;
}

// Redirigir al dashboard una vez autenticado y configurado
header("Location: dashboard.php");
exit;