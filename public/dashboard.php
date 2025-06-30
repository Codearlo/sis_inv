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

// Verificar si necesita onboarding
if ($auth->necesitaOnboarding()) {
    header("Location: onboarding.php");
    exit;
}

// Marcador para que el sidebar sepa qué enlace resaltar
$active_page = 'dashboard';

$negocioActivo = $auth->getNegocioActivo();
if (!$negocioActivo) {
    header("Location: onboarding.php");
    exit;
}

$getDashboardData = new GetDashboardData($pdo);
$dashboardData = $getDashboardData->execute($negocioActivo['id']);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - <?php echo htmlspecialchars($negocioActivo['nombre']); ?></title>
    <link rel="stylesheet" href="css/sidebar_styles.css?v=1.9">
    <link rel="stylesheet" href="css/dashboard_styles.css?v=1.8">
</head>
<body>
    <?php require_once 'parts/sidebar.php'; ?>

    <div class="dashboard_main-content">
        <header class="dashboard_header">
            <div class="dashboard_welcome">
                <h1>Dashboard - <?php echo htmlspecialchars($negocioActivo['nombre']); ?></h1>
                <?php if ($negocioActivo['descripcion']): ?>
                    <p style="color: #6B7280; margin-top: 5px;"><?php echo htmlspecialchars($negocioActivo['descripcion']); ?></p>
                <?php endif; ?>
                <div style="margin-top: 10px;">
                    <span style="background: #F3F4F6; color: #374151; padding: 4px 8px; border-radius: 6px; font-size: 0.8rem;">
                        Código: <?php echo htmlspecialchars($negocioActivo['codigo_invitacion']); ?>
                    </span>
                </div>
            </div>
        </header>

        <div class="dashboard_stats-container">
            <h2>Estadísticas del Negocio</h2>
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
                <h2>Productos con Bajo Stock (<5)</h2>
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
                <h3>¡Gestiona múltiples negocios!</h3>
                <p>Puedes crear hasta 2 negocios independientes o unirte a otros usando códigos de invitación.</p>
                <a href="gestionar-negocios.php" class="promo_button">Gestionar Negocios</a>
            </div>
            <div class="promo_icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18"/><path d="M5 21V7l8-4v18"/><path d="M19 21V11l-6-4"/></svg>
            </div>
        </div>

    </div>
    
    </body>
</html>