<?php
require_once '../config/database.php';
require_once '../app/Infrastructure/Auth/AuthService.php';

$auth = new AuthService($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $pass = $_POST['contrasena'];

    if ($auth->login($email, $pass)) {
        header("Location: index.php");
        exit;
    } else {
        $error = "Correo o contraseña incorrectos.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
<h2>Iniciar Sesión</h2>
<?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
<form method="POST">
    <input type="email" name="email" placeholder="Correo" required><br>
    <input type="password" name="contrasena" placeholder="Contraseña" required><br>
    <button type="submit">Entrar</button>
</form>
</body>
</html>
