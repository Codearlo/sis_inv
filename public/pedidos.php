<?php
require_once '../config/database.php';
require_once '../app/Infrastructure/Auth/AuthService.php';
require_once '../app/Pedidos/UseCase/GetPedidos.php'; 

use App\Pedidos\UseCase\GetPedidos;

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

// Marcador para que el sidebar sepa qué enlace resaltar
$active_page = 'pedidos';

$negocioActivo = $auth->getNegocioActivo();
if (!$negocioActivo) {
    header("Location: onboarding.php");
    exit;
}

// Obtener pedidos del negocio activo
$getPedidos = new GetPedidos($pdo);
$pedidos = $getPedidos->execute($negocioActivo['id']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Pedidos - <?php echo htmlspecialchars($negocioActivo['nombre']); ?></title>
    <link rel="stylesheet" href="css/sidebar_styles.css?v=1.9">
    <link rel="stylesheet" href="css/pedidos_styles.css?v=1.1">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <?php require_once 'parts/sidebar.php'; ?>

    <div class="pedidos_main-content">
        <header class="pedidos_header">
            <div>
                <h1>Gestión de Pedidos</h1>
                <p style="color: #6B7280; margin-top: 5px;">
                    Negocio: <?php echo htmlspecialchars($negocioActivo['nombre']); ?>
                </p>
            </div>
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
                            <td colspan="9" style="text-align: center; padding: 40px; color: #6B7280;">
                                <div style="display: flex; flex-direction: column; align-items: center;">
                                    <div style="font-size: 48px; margin-bottom: 16px;">📦</div>
                                    <h3 style="margin-bottom: 8px; color: #374151;">No hay pedidos registrados</h3>
                                    <p style="margin-bottom: 20px;">Este negocio aún no tiene pedidos. ¡Añade uno nuevo para empezar!</p>
                                    <a href="nuevo-pedido.php" style="background: #111827; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 500;">
                                        Crear Primer Pedido
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>