<?php
require_once '../config/database.php';
require_once '../app/Infrastructure/Auth/AuthService.php';
require_once '../app/Dashboard/UseCase/GetDashboardData.php';

// Iniciar el namespace para poder instanciar la clase del UseCase
use App\Dashboard\UseCase\GetDashboardData;

$auth = new AuthService($pdo);

if (!$auth->estaAutenticado()) {
    header("Location: login.php");
    exit;
}

// Instanciar y ejecutar el caso de uso
$getDashboardData = new GetDashboardData($pdo);
$dashboardData = $getDashboardData->execute();

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Inventario</title>
    <link rel="stylesheet" href="css/sidebar_styles.css?v=1.3">
    <link rel="stylesheet" href="css/dashboard_styles.css?v=1.3">
</head>
<body>
    <?php require_once 'parts/sidebar.php'; ?>

    <div class="dashboard_main-content">
        <header>
            <h1>Dashboard</h1>
        </header>

        <div class="dashboard_cards-container">
            <div class="dashboard_card">
                <h3>Total de Productos Únicos</h3>
                <p><?php echo htmlspecialchars($dashboardData['total_productos'] ?? '0'); ?></p>
            </div>
            <div class="dashboard_card">
                <h3>Unidades en Stock</h3>
                <p><?php echo htmlspecialchars($dashboardData['total_stock'] ?? '0'); ?></p>
            </div>
            <div class="dashboard_card">
                <h3>Productos con Bajo Stock</h3>
                <p><?php echo count($dashboardData['productos_bajo_stock'] ?? []); ?></p>
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
                            <th>Precio Unitario</th>
                            <th>Fecha de Compra</th>
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
                <h2>Productos con Bajo Stock (<5 unidades)</h2>
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
    </div>

    <script src="js/sidebar.js" defer></script>
</body>
</html>