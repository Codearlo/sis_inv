<?php
require_once '../config/database.php';
require_once '../app/Infrastructure/Auth/AuthService.php';

$auth = new AuthService($pdo);
$auth->logout();

header("Location: login.php");
exit;
