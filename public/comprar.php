<?php
require_once '../config/database.php';
require_once '../app/UseCase/RegistrarCompra.php';
require_once '../app/Infrastructure/Auth/AuthService.php';

$auth = new AuthService($pdo);
if (!$auth->estaAutenticado()) {
    header("Location: login.php");
    exit;
}

$mensaje = '';
$tipo_mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = [
        'producto' => $_POST['producto'],
        'proveedor' => $_POST['proveedor'],
        'fecha_compra' => $_POST['fecha_compra'],
        'fecha_recibido' => $_POST['fecha_recibido'],
        'precio_unitario' => $_POST['precio_unitario'],
        'cantidad' => $_POST['cantidad'],
        'pagado' => isset($_POST['pagado']) ? 1 : 0
    ];

    $registrador = new RegistrarCompra($pdo);
    if ($registrador->ejecutar($datos)) {
        $mensaje = "Compra registrada correctamente.";
        $tipo_mensaje = "success";
    } else {
        $mensaje = "Error al registrar la compra.";
        $tipo_mensaje = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Compra - Inventario</title>
    <link rel="stylesheet" href="css/sidebar_styles.css?v=1.5">
    <link rel="stylesheet" href="css/comprar_styles.css?v=1.0">
</head>
<body>
    <?php require_once 'parts/sidebar.php'; ?>

    <div class="comprar_main-content">
        <header>
            <h1>Registrar Nueva Compra</h1>
        </header>

        <div class="comprar_form-container">
            <?php if ($mensaje): ?>
                <div class="comprar_message <?php echo $tipo_mensaje; ?>"><?php echo $mensaje; ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="comprar_form-group">
                    <label for="producto">Nombre del producto</label>
                    <input type="text" id="producto" name="producto" placeholder="Ej: Teclado Mecánico RGB" required>
                </div>

                <div class="comprar_form-group">
                    <label for="proveedor">Proveedor</label>
                    <input type="text" id="proveedor" name="proveedor" placeholder="Ej: AliExpress, Amazon, etc." required>
                </div>
                
                <div class="comprar_form-group">
                    <label for="fecha_compra">Fecha de compra</label>
                    <input type="date" id="fecha_compra" name="fecha_compra" required>
                </div>

                <div class="comprar_form-group">
                    <label for="fecha_recibido">Fecha de recepción</label>
                    <input type="date" id="fecha_recibido" name="fecha_recibido" required>
                </div>

                <div class="comprar_form-group">
                    <label for="precio_unitario">Precio por unidad</label>
                    <input type="number" id="precio_unitario" name="precio_unitario" step="0.01" placeholder="Ej: 45.50" required>
                </div>

                <div class="comprar_form-group">
                    <label for="cantidad">Cantidad</label>
                    <input type="number" id="cantidad" name="cantidad" placeholder="Ej: 10" required>
                </div>
                
                <div class="comprar_checkbox-group">
                    <input type="checkbox" id="pagado" name="pagado">
                    <label for="pagado">Marcar como pagado</label>
                </div>
                
                <button type="submit" class="comprar_button">Registrar compra</button>
            </form>
        </div>
    </div>
</body>
</html>