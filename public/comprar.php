<?php
require_once '../config/database.php';
require_once '../app/Infrastructure/Auth/AuthService.php';
require_once '../app/Compras/UseCase/GetCompras.php'; // Nuevo Use Case

$auth = new AuthService($pdo);
if (!$auth->estaAutenticado()) {
    header("Location: login.php");
    exit;
}

// Marcador para el sidebar
$active_page = 'comprar';

// Obtener todas las compras
$getCompras = new App\Compras\UseCase\GetCompras($pdo);
$compras = $getCompras->execute();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Compras - Inventario</title>
    <link rel="stylesheet" href="css/sidebar_styles.css?v=1.6">
    <link rel="stylesheet" href="css/comprar_styles.css?v=1.1"> </head>
<body>
    <?php require_once 'parts/sidebar.php'; ?>

    <div class="compras_main-content">
        <header class="compras_header">
            <h1>Gestión de Compras</h1>
            <a href="nueva-compra.php" class="compras_button-new">Nueva Compra</a>
        </header>

        <div class="compras_table-container">
            <table>
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Proveedor</th>
                        <th>Fecha Compra</th>
                        <th>Cantidad</th>
                        <th>Precio Unit.</th>
                        <th>Total</th>
                        <th>Pagado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($compras)): ?>
                        <?php foreach ($compras as $compra): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($compra['producto_nombre']); ?></td>
                                <td><?php echo htmlspecialchars($compra['proveedor']); ?></td>
                                <td><?php echo htmlspecialchars($compra['fecha_compra']); ?></td>
                                <td><?php echo htmlspecialchars($compra['cantidad']); ?></td>
                                <td>$<?php echo htmlspecialchars(number_format($compra['precio_unitario'], 2)); ?></td>
                                <td>$<?php echo htmlspecialchars(number_format($compra['cantidad'] * $compra['precio_unitario'], 2)); ?></td>
                                <td>
                                    <span class="compras_status <?php echo $compra['pagado'] ? 'paid' : 'pending'; ?>">
                                        <?php echo $compra['pagado'] ? 'Sí' : 'No'; ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7">No hay compras registradas. ¡Añade una nueva!</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>