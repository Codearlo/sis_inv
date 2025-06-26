<?php
require_once '../config/database.php';
require_once '../app/Infrastructure/Auth/AuthService.php';
require_once '../app/Dashboard/UseCase/GetDashboardData.php';

use App\Dashboard\UseCase\GetDashboardData;

$auth = new AuthService($pdo);
if (!$auth->estaAutenticado()) {
    header("Location: login.php");
    exit;
}

// Marcador para que el sidebar sepa qué enlace resaltar
$active_page = 'dashboard';

$getDashboardData = new GetDashboardData($pdo);
$dashboardData = $getDashboardData->execute();

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Inventario</title>
    <link rel="stylesheet" href="css/sidebar_styles.css?v=1.8">
    <link rel="stylesheet" href="css/dashboard_styles.css?v=1.7">
</head>
<body>
    <?php require_once 'parts/sidebar.php'; ?>

    <div class="dashboard_main-content">
        <header class="dashboard_header">
            <div class="dashboard_header-actions">
                </div>
        </header>

        <div class="dashboard_stats-container">
            <h2>Tus Estadísticas</h2>
            <div class="dashboard_cards-container">
                <div class="dashboard_card">
                    <h3>Total de Productos</h3>
                    <p><?php echo htmlspecialchars($dashboardData['total_productos'] ?? '0'); ?></p>
                </div>
                <div class="dashboard_card">
                    <h3>Unidades en Stock</h3>
                    <p><?php echo htmlspecialchars($dashboardData['total_stock'] ?? '0'); ?></p>
                </div>
                <div class="dashboard_card">
                    <h3>Bajo Stock</h3>
                    <p><?php echo count($dashboardData['productos_bajo_stock'] ?? []); ?></p>
                </div>
            </div>
        </div>


        <div class="dashboard_tables-container">
            <div class="dashboard_table-wrapper">
                <h2>Compras Recientes</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Precio</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($dashboardData['compras_recientes'])): ?>
                            <?php foreach ($dashboardData['compras_recientes'] as $compra): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($compra['nombre']); ?></td>
                                    <td><?php echo htmlspecialchars($compra['cantidad']); ?></td>
                                    <td>$<?php echo htmlspecialchars(number_format($compra['precio_unitario'], 2)); ?></td>
                                    <td><?php echo htmlspecialchars($compra['fecha_compra']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4">No hay compras recientes.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="dashboard_table-wrapper">
                <h2>Productos con Bajo Stock (&lt;5)</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Stock Actual</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($dashboardData['productos_bajo_stock'])): ?>
                            <?php foreach ($dashboardData['productos_bajo_stock'] as $producto): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($producto['nombre']); ?></td>
                                    <td class="dashboard_low-stock"><?php echo htmlspecialchars($producto['stock_actual']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="2">No hay productos con bajo stock.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="dashboard_promo-card">
             <div class="promo_text">
                <h3>¡Aprende aún más!</h3>
                <p>Desbloquea funciones premium y lleva el control de tu inventario al siguiente nivel.</p>
                <a href="#" class="promo_button">Volverse Premium</a>
            </div>
            <div class="promo_icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="feather feather-zap"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
            </div>
        </div>

    </div>
    
    </body>
</html>