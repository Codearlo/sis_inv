<?php
require_once '../config/database.php';
require_once '../app/Infrastructure/Auth/AuthService.php';
// Estos UseCases se crearán en pasos posteriores
// require_once '../app/Pedidos/UseCase/RegistrarPedido.php';
// require_once '../app/Cuentas/UseCase/GetCuentas.php';

$auth = new AuthService($pdo);
if (!$auth->estaAutenticado()) {
    header("Location: login.php");
    exit;
}

$active_page = 'pedidos';
$mensaje = '';
$tipo_mensaje = '';

// --- Lógica para obtener las cuentas financieras (se implementará más adelante) ---
$cuentas = [];
/*
$getCuentas = new App\Cuentas\UseCase\GetCuentas($pdo);
$cuentas = $getCuentas->execute();
*/


// --- Lógica para registrar el pedido ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    /*
    $datos = [
        'producto' => $_POST['producto'],
        'proveedor' => $_POST['proveedor'],
        'fecha_compra' => $_POST['fecha_compra'],
        'fecha_recibido' => $_POST['fecha_recibido'],
        'precio_unitario' => $_POST['precio_unitario'],
        'cantidad' => $_POST['cantidad'],
        'estado_pago' => $_POST['estado_pago'],
        'cuenta_id' => $_POST['estado_pago'] === 'deuda' ? $_POST['cuenta_id'] : null,
    ];

    $registrador = new App\Pedidos\UseCase\RegistrarPedido($pdo);
    if ($registrador->ejecutar($datos)) {
        $mensaje = "Pedido registrado con éxito. <a href='pedidos.php'>Volver al listado</a>";
        $tipo_mensaje = "success";
    } else {
        $mensaje = "Error al registrar el pedido.";
        $tipo_mensaje = "error";
    }
    */
    $mensaje = "La lógica de registro aún no está implementada.";
    $tipo_mensaje = "error";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nuevo Pedido - Inventario</title>
    <link rel="stylesheet" href="css/sidebar_styles.css?v=1.7">
    <link rel="stylesheet" href="css/form_styles.css?v=1.1">
</head>
<body>
    <?php require_once 'parts/sidebar.php'; ?>

    <div class="form_main-content">
        <header>
            <h1>Registrar Nuevo Pedido</h1>
        </header>

        <div class="form_container">
            <?php if ($mensaje): ?>
                <div class="form_message <?php echo $tipo_mensaje; ?>"><?php echo $mensaje; ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form_group">
                    <label for="producto">Nombre del producto</label>
                    <input type="text" id="producto" name="producto" placeholder="Ej: Teclado Mecánico RGB" required>
                </div>

                <div class="form_group">
                    <label for="proveedor">Proveedor</label>
                    <input type="text" id="proveedor" name="proveedor" placeholder="Ej: AliExpress, Amazon, etc." required>
                </div>
                
                <div class="form_group">
                    <label for="fecha_compra">Fecha de compra</label>
                    <input type="date" id="fecha_compra" name="fecha_compra" required>
                </div>

                <div class="form_group">
                    <label for="fecha_recibido">Fecha de recepción (opcional)</label>
                    <input type="date" id="fecha_recibido" name="fecha_recibido">
                </div>

                <div class="form_group">
                    <label for="precio_unitario">Precio por unidad</label>
                    <input type="number" id="precio_unitario" name="precio_unitario" step="0.01" placeholder="Ej: 45.50" required>
                </div>

                <div class="form_group">
                    <label for="cantidad">Cantidad</label>
                    <input type="number" id="cantidad" name="cantidad" placeholder="Ej: 10" required>
                </div>
                
                <div class="form_group">
                    <label>Estado del Pago</label>
                    <div class="form_radio-group">
                        <label class="form_radio-label">
                            <input type="radio" name="estado_pago" value="pagado" checked> Pagado Completo
                        </label>
                        <label class="form_radio-label">
                            <input type="radio" name="estado_pago" value="deuda"> Deuda
                        </label>
                    </div>
                </div>

                <div id="deuda_details" class="form_group" style="display: none;">
                    <label for="cuenta_id">Cuenta financiera asociada</label>
                    <select id="cuenta_id" name="cuenta_id" class="form_select">
                        <option value="">-- Seleccione una cuenta --</option>
                        <?php foreach($cuentas as $cuenta): ?>
                            <option value="<?php echo $cuenta['id']; ?>">
                                <?php echo htmlspecialchars($cuenta['nombre_cuenta']); ?>
                            </option>
                        <?php endforeach; ?>
                         <option value="1">Interbank Oro (Ejemplo)</option>
                        <option value="2">BCP Préstamo (Ejemplo)</option>
                    </select>
                </div>
                
                <button type="submit" class="form_button">Registrar Pedido</button>
            </form>
        </div>
    </div>

    <script>
        // Script para mostrar/ocultar los detalles de la deuda
        document.querySelectorAll('input[name="estado_pago"]').forEach(elem => {
            elem.addEventListener("change", function(event) {
                const isDeuda = event.target.value === 'deuda';
                const deudaDetails = document.getElementById('deuda_details');
                const cuentaSelect = document.getElementById('cuenta_id');
                
                deudaDetails.style.display = isDeuda ? 'block' : 'none';
                cuentaSelect.required = isDeuda;
            });
        });
    </script>
</body>
</html>