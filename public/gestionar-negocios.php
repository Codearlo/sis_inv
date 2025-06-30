<?php
require_once '../config/database.php';
require_once '../app/Infrastructure/Auth/AuthService.php';
require_once '../app/Negocios/UseCase/GetNegociosUsuario.php';
require_once '../app/Negocios/UseCase/CrearNegocio.php';

use App\Negocios\UseCase\GetNegociosUsuario;
use App\Negocios\UseCase\CrearNegocio;

$auth = new AuthService($pdo);
if (!$auth->estaAutenticado()) {
    header("Location: login.php");
    exit;
}

if ($auth->necesitaOnboarding()) {
    header("Location: onboarding.php");
    exit;
}

$active_page = 'negocios';
$usuarioId = $auth->getUsuarioId();
$negocioActivo = $auth->getNegocioActivo();

$getNegociosUsuario = new GetNegociosUsuario($pdo);
$negocios = $getNegociosUsuario->ejecutar($usuarioId);
$puedeCrearMas = $getNegociosUsuario->puedeCrearMasNegocios($usuarioId);

$mensaje = '';
$tipoMensaje = '';

// Procesar cambio de negocio activo
if (isset($_POST['cambiar_negocio'])) {
    $nuevoNegocioId = $_POST['negocio_id'];
    if ($auth->cambiarNegocioActivo($nuevoNegocioId)) {
        header("Location: dashboard.php");
        exit;
    } else {
        $mensaje = 'Error al cambiar de negocio';
        $tipoMensaje = 'error';
    }
}

// Procesar creación de nuevo negocio
if (isset($_POST['crear_negocio'])) {
    $crearNegocio = new CrearNegocio($pdo);
    $resultado = $crearNegocio->ejecutar(
        $usuarioId,
        $_POST['nombre_negocio'],
        $_POST['descripcion_negocio'] ?? null
    );
    
    if ($resultado['exito']) {
        $mensaje = $resultado['mensaje'];
        $tipoMensaje = 'success';
        // Actualizar lista de negocios
        $negocios = $getNegociosUsuario->ejecutar($usuarioId);
        $puedeCrearMas = $getNegociosUsuario->puedeCrearMasNegocios($usuarioId);
    } else {
        $mensaje = $resultado['mensaje'];
        $tipoMensaje = 'error';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestionar Negocios - Inventario</title>
    <link rel="stylesheet" href="css/sidebar_styles.css?v=1.9">
    <link rel="stylesheet" href="css/form_styles.css?v=1.3">
    <link rel="stylesheet" href="css/styles.css">
    <style>
        .negocios-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .negocio-card {
            background: white;
            border-radius: 16px;
            padding: 25px;
            border: 2px solid #E5E7EB;
            transition: all 0.3s ease;
        }
        
        .negocio-card.activo {
            border-color: #111827;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .negocio-header {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .negocio-icon {
            width: 50px;
            height: 50px;
            background: #111827;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
            margin-right: 15px;
        }
        
        .negocio-info h3 {
            margin: 0;
            color: #111827;
            font-size: 1.2rem;
        }
        
        .negocio-rol {
            background: #F3F4F6;
            color: #374151;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
            margin-top: 5px;
            display: inline-block;
        }
        
        .negocio-rol.propietario {
            background: #FEE2E2;
            color: #991B1B;
        }
        
        .negocio-descripcion {
            color: #6B7280;
            margin-bottom: 15px;
            font-size: 0.9rem;
        }
        
        .negocio-detalles {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 20px;
            font-size: 0.85rem;
        }
        
        .detalle-item {
            background: #F9FAFB;
            padding: 10px;
            border-radius: 8px;
        }
        
        .detalle-label {
            color: #6B7280;
            font-weight: 500;
        }
        
        .detalle-valor {
            color: #111827;
            font-weight: 600;
        }
        
        .negocio-acciones {
            display: flex;
            gap: 10px;
        }
        
        .btn-cambiar {
            flex: 1;
            background: #111827;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            transition: background 0.2s;
        }
        
        .btn-cambiar:hover {
            background: #374151;
        }
        
        .btn-cambiar:disabled {
            background: #9CA3AF;
            cursor: not-allowed;
        }
        
        .crear-negocio-section {
            background: white;
            border-radius: 16px;
            padding: 25px;
            border: 2px dashed #D1D5DB;
            text-align: center;
            margin-bottom: 30px;
        }
        
        .info-section {
            background: #EFF6FF;
            border: 1px solid #DBEAFE;
            border-radius: 12px;
            padding: 20px;
        }
        
        .info-section h3 {
            color: #1E40AF;
            margin-bottom: 10px;
        }
        
        .info-section ul {
            color: #1E40AF;
            margin: 0;
            padding-left: 20px;
        }
        
        .info-section li {
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <?php require_once 'parts/sidebar.php'; ?>

    <div class="form_main-content">
        <header>
            <h1>Gestión de Negocios</h1>
            <p style="color: #6B7280; margin-top: 5px;">Administra hasta 2 negocios independientes con datos separados</p>
        </header>

        <?php if ($mensaje): ?>
            <div class="form_message <?php echo $tipoMensaje; ?>" style="margin-bottom: 30px;">
                <?php echo htmlspecialchars($mensaje); ?>
            </div>
        <?php endif; ?>

        <!-- Crear Nuevo Negocio -->
        <?php if ($puedeCrearMas): ?>
            <div class="crear-negocio-section">
                <h3 style="margin-bottom: 15px; color: #111827;">Crear Nuevo Negocio</h3>
                <p style="color: #6B7280; margin-bottom: 20px;">Puedes tener hasta 2 negocios con datos completamente separados</p>
                
                <form method="POST" style="max-width: 400px; margin: 0 auto;">
                    <div class="form_group">
                        <input type="text" name="nombre_negocio" placeholder="Nombre del negocio" required>
                    </div>
                    <div class="form_group">
                        <textarea name="descripcion_negocio" placeholder="Descripción (opcional)" rows="2" 
                                  style="width: 100%; padding: 12px; border: 1px solid #D1D5DB; border-radius: 8px; resize: vertical;"></textarea>
                    </div>
                    <button type="submit" name="crear_negocio" class="form_button">Crear Negocio</button>
                </form>
            </div>
        <?php endif; ?>

        <!-- Lista de Negocios -->
        <div class="negocios-grid">
            <?php foreach ($negocios as $negocio): ?>
                <div class="negocio-card <?php echo ($negocioActivo && $negocioActivo['id'] == $negocio['id']) ? 'activo' : ''; ?>">
                    <div class="negocio-header">
                        <div class="negocio-icon">🏢</div>
                        <div class="negocio-info">
                            <h3><?php echo htmlspecialchars($negocio['nombre']); ?></h3>
                            <span class="negocio-rol <?php echo $negocio['rol']; ?>">
                                <?php echo ucfirst($negocio['rol']); ?>
                            </span>
                            <?php if ($negocioActivo && $negocioActivo['id'] == $negocio['id']): ?>
                                <span class="negocio-rol" style="background: #D1FAE5; color: #065F46; margin-left: 5px;">Activo</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if ($negocio['descripcion']): ?>
                        <div class="negocio-descripcion">
                            <?php echo htmlspecialchars($negocio['descripcion']); ?>
                        </div>
                    <?php endif; ?>

                    <div class="negocio-detalles">
                        <div class="detalle-item">
                            <div class="detalle-label">Código de Invitación</div>
                            <div class="detalle-valor"><?php echo htmlspecialchars($negocio['codigo_invitacion']); ?></div>
                        </div>
                        <div class="detalle-item">
                            <div class="detalle-label">Miembros</div>
                            <div class="detalle-valor"><?php echo $negocio['total_miembros']; ?> persona<?php echo $negocio['total_miembros'] != 1 ? 's' : ''; ?></div>
                        </div>
                        <div class="detalle-item">
                            <div class="detalle-label">Creado</div>
                            <div class="detalle-valor"><?php echo date('d/m/Y', strtotime($negocio['fecha_creacion'])); ?></div>
                        </div>
                        <div class="detalle-item">
                            <div class="detalle-label">Te uniste</div>
                            <div class="detalle-valor"><?php echo date('d/m/Y', strtotime($negocio['fecha_union'])); ?></div>
                        </div>
                    </div>

                    <div class="negocio-acciones">
                        <?php if (!$negocioActivo || $negocioActivo['id'] != $negocio['id']): ?>
                            <form method="POST" style="flex: 1;">
                                <input type="hidden" name="negocio_id" value="<?php echo $negocio['id']; ?>">
                                <button type="submit" name="cambiar_negocio" class="btn-cambiar">
                                    Cambiar a este negocio
                                </button>
                            </form>
                        <?php else: ?>
                            <button class="btn-cambiar" disabled>Negocio Activo</button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Información -->
        <div class="info-section">
            <h3>Información sobre Negocios Múltiples</h3>
            <ul>
                <li>Puedes tener hasta 2 negocios por cuenta</li>
                <li>Cada negocio mantiene sus datos completamente separados</li>
                <li>Los productos, pedidos y cuentas son independientes entre negocios</li>
                <li>Puedes cambiar entre negocios en cualquier momento</li>
                <li>Comparte el código de invitación para que otros se unan a tu negocio</li>
                <li>Solo el propietario puede ver el código de invitación completo</li>
            </ul>
        </div>
    </div>
</body>
</html>