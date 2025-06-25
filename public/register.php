<?php
require_once '../config/database.php';

$mensaje = '';
$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $contrasena = $_POST['contrasena'];
    $confirmacion = $_POST['confirmar_contrasena'];

    // Validaciones básicas
    if (empty($nombre) || empty($email) || empty($contrasena)) {
        $errores[] = "Todos los campos son obligatorios.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "El correo no es válido.";
    }

    if ($contrasena !== $confirmacion) {
        $errores[] = "Las contraseñas no coinciden.";
    }

    if (strlen($contrasena) < 6) {
        $errores[] = "La contraseña debe tener al menos 6 caracteres.";
    }

    // Si no hay errores, continuar
    if (empty($errores)) {
        $hash = password_hash($contrasena, PASSWORD_BCRYPT);

        try {
            $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, contrasena) VALUES (?, ?, ?)");
            $stmt->execute([$nombre, $email, $hash]);
            $mensaje = "Usuario registrado correctamente. <a href='login.php'>Iniciar sesión</a>";
        } catch (PDOException $e) {
            if ($e->errorInfo[1] == 1062) {
                $errores[] = "El correo ya está registrado.";
            } else {
                $errores[] = "Error al registrar: " . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Registro de Usuario</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
    <h2>Crear Cuenta</h2>

    <?php foreach ($errores as $error): ?>
        <p style="color:red;"><?php echo $error; ?></p>
    <?php endforeach; ?>

    <?php if ($mensaje): ?>
        <p style="color:green;"><?php echo $mensaje; ?></p>
    <?php endif; ?>

    <form method="POST">
        <input type="text" name="nombre" placeholder="Nombre completo" required><br>
        <input type="email" name="email" placeholder="Correo electrónico" required><br>
        <input type="password" name="contrasena" placeholder="Contraseña" required><br>
        <input type="password" name="confirmar_contrasena" placeholder="Confirmar contraseña" required><br>
        <button type="submit">Registrar</button>
    </form>

    <p>¿Ya tienes cuenta? <a href="login.php">Inicia sesión aquí</a></p>
</body>
</html>
