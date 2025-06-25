<?php
require_once '../config/database.php';
require_once '../app/UseCase/RegistrarCompra.php';

$mensaje = '';

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
    } else {
        $mensaje = "Error al registrar la compra.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Registrar Compra</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
<div class="container">
    <h2>Registrar Compra</h2>

    <?php if ($mensaje): ?>
        <div class="message success"><?php echo $mensaje; ?></div>
    <?php endif; ?>

    <form method="POST">
        <input type="text" name="producto" placeholder="Nombre del producto" required>
        <input type="text" name="proveedor" placeholder="Proveedor (AliExpress, Temu, etc)" required>
        <label>Fecha de compra:</label>
        <input type="date" name="fecha_compra" required>
        <label>Fecha de recepción:</label>
        <input type="date" name="fecha_recibido" required>
        <input type="number" name="precio_unitario" step="0.01" placeholder="Precio por unidad" required>
        <input type="number" name="cantidad" placeholder="Cantidad" required>
        <label><input type="checkbox" name="pagado"> Pagado</label><br><br>
        <button type="submit">Registrar compra</button>
    </form>
</div>
</body>
</html>
