<?php
require_once '../config/database.php';
require_once '../app/Infrastructure/Auth/AuthService.php';
// Este UseCase se creará en un paso posterior
// require_once '../app/Pedidos/UseCase/GetPedidos.php'; 

$auth = new AuthService($pdo);
if (!$auth->estaAutenticado()) {
    header("Location: login.php");
    exit;
}

// Marcador para que el sidebar sepa qué enlace resaltar
$active_page = 'pedidos';

// --- Lógica para obtener los pedidos (se implementará más adelante) ---
// Por ahora, usamos un array vacío para que la página no de error.
$pedidos = [];
/*
$getPedidos = new App\Pedidos\UseCase\GetPedidos($pdo);
$pedidos = $getPedidos->execute();
*/
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Pedidos - Inventario</title>
    <link rel="stylesheet" href="css/sidebar_styles.css?v=1.7">
    <link rel="stylesheet" href="css/pedidos_styles.css?v=1.0"> </head>
    <link rel="stylesheet" href="css/styles.css?">
<body>
    <?php require_once 'parts/sidebar.php'; ?>

    <div class="pedidos_main-content">
        <header class="pedidos_header">
            <h1>Gestión de Pedidos</h1>
            <a href="nuevo-pedido.php" class="pedidos_button-new">Nuevo Pedido</a>
        </header>

        <div class="pedidos_table-container">
            <table>
                <thead>
                    <tr>
                        <th>Lote Único</th>
                        <th>Producto</th>
                        <th>Proveedor</th>
                        <th>Fecha Compra</th>
                        <th>Cantidad</th>
                        <th>Precio Unit.</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th>Cuenta</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($pedidos)): ?>
                        <?php foreach ($pedidos as $pedido): ?>
                            <tr>
                                <td><?php echo htmlspecialchars(strtoupper(substr($pedido['producto_nombre'], 0, 3))) . '-' . htmlspecialchars($pedido['fecha_compra']) . '-' . htmlspecialchars($pedido['numero_compra_dia']); ?></td>
                                <td><?php echo htmlspecialchars($pedido['producto_nombre']); ?></td>
                                <td><?php echo htmlspecialchars($pedido['proveedor']); ?></td>
                                <td><?php echo htmlspecialchars($pedido['fecha_compra']); ?></td>
                                <td><?php echo htmlspecialchars($pedido['cantidad']); ?></td>
                                <td>$<?php echo htmlspecialchars(number_format($pedido['precio_unitario'], 2)); ?></td>
                                <td>$<?php echo htmlspecialchars(number_format($pedido['cantidad'] * $pedido['precio_unitario'], 2)); ?></td>
                                <td>
                                    <span class="pedidos_status <?php echo $pedido['estado_pago'] === 'pagado' ? 'paid' : 'debt'; ?>">
                                        <?php echo ucfirst($pedido['estado_pago']); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($pedido['nombre_cuenta'] ?? 'N/A'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9">No hay pedidos registrados. ¡Añade uno nuevo para empezar!</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>