<?php
require_once '../config/database.php';
require_once '../app/Infrastructure/Auth/AuthService.php';
require_once '../app/Negocios/UseCase/CrearNegocio.php';
require_once '../app/Negocios/UseCase/UnirseANegocio.php';

use App\Negocios\UseCase\CrearNegocio;
use App\Negocios\UseCase\UnirseANegocio;

$auth = new AuthService($pdo);

// Verificar autenticación
if (!$auth->estaAutenticado()) {
    header("Location: login.php");
    exit;
}

// Si ya tiene negocios, redirigir al dashboard
if (!$auth->necesitaOnboarding()) {
    header("Location: dashboard.php");
    exit;
}

$mensaje = '';
$tipoMensaje = '';
$usuarioId = $auth->getUsuarioId();

// Procesar formularios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['accion'])) {
        if ($_POST['accion'] === 'crear_negocio') {
            $crearNegocio = new CrearNegocio($pdo);
            $resultado = $crearNegocio->ejecutar(
                $usuarioId,
                $_POST['nombre_negocio'],
                $_POST['descripcion_negocio'] ?? null
            );
            
            if ($resultado['exito']) {
                // Actualizar sesión y redirigir
                $_SESSION['necesita_onboarding'] = false;
                $_SESSION['negocio_activo_id'] = $resultado['negocio']['id'];
                $_SESSION['negocio_activo'] = $resultado['negocio'];
                header("Location: dashboard.php");
                exit;
            } else {
                $mensaje = $resultado['mensaje'];
                $tipoMensaje = 'error';
            }
            
        } elseif ($_POST['accion'] === 'unirse_negocio') {
            $unirseNegocio = new UnirseANegocio($pdo);
            $resultado = $unirseNegocio->ejecutar(
                $usuarioId,
                strtoupper(trim($_POST['codigo_invitacion']))
            );
            
            if ($resultado['exito']) {
                // Actualizar sesión y redirigir
                $_SESSION['necesita_onboarding'] = false;
                $_SESSION['negocio_activo_id'] = $resultado['negocio']['id'];
                $_SESSION['negocio_activo'] = $resultado['negocio'];
                header("Location: dashboard.php");
                exit;
            } else {
                $mensaje = $resultado['mensaje'];
                $tipoMensaje = 'error';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Configuración Inicial - Inventario</title>
    <link rel="stylesheet" href="css/form_styles.css?v=1.2">
    <link rel="stylesheet" href="css/styles.css">
    <style>
        .onboarding-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
        }
        
        .onboarding-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            max-width: 600px;
            width: 100%;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        
        .onboarding-header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .onboarding-header h1 {
            color: #1F2937;
            font-size: 2.5rem;
            margin-bottom: 10px;
        }
        
        .onboarding-header p {
            color: #6B7280;
            font-size: 1.1rem;
        }
        
        .option-cards {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .option-card {
            border: 2px solid #E5E7EB;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .option-card:hover {
            border-color: #111827;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        
        .option-card.active {
            border-color: #111827;
            background-color: #F9FAFB;
        }
        
        .option-icon {
            width: 60px;
            height: 60px;
            background: #111827;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            color: white;
            font-size: 24px;
        }
        
        .option-title {
            font-weight: 600;
            color: #1F2937;
            margin-bottom: 8px;
        }
        
        .option-description {
            color: #6B7280;
            font-size: 0.9rem;
        }
        
        .form-section {
            display: none;
            animation: fadeIn 0.3s ease;
        }
        
        .form-section.active {
            display: block;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @media (max-width: 768px) {
            .option-cards {
                grid-template-columns: 1fr;
            }
            
            .onboarding-card {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="onboarding-container">
        <div class="onboarding-card">
            <div class="onboarding-header">
                <h1>¡Bienvenido!</h1>
                <p>Para comenzar, necesitas crear un negocio o unirte a uno existente</p>
            </div>

            <?php if ($mensaje): ?>
                <div class="form_message <?php echo $tipoMensaje; ?>" style="margin-bottom: 30px;">
                    <?php echo htmlspecialchars($mensaje); ?>
                </div>
            <?php endif; ?>

            <!-- Opciones -->
            <div class="option-cards">
                <div class="option-card" onclick="mostrarFormulario('crear')">
                    <div class="option-icon">🏢</div>
                    <div class="option-title">Crear Nuevo Negocio</div>
                    <div class="option-description">Inicia tu propio inventario desde cero</div>
                </div>
                
                <div class="option-card" onclick="mostrarFormulario('unirse')">
                    <div class="option-icon">🤝</div>
                    <div class="option-title">Unirse a Negocio</div>
                    <div class="option-description">Únete a un negocio existente con código</div>
                </div>
            </div>

            <!-- Formulario Crear Negocio -->
            <div id="form-crear" class="form-section">
                <h3 style="margin-bottom: 20px; color: #1F2937;">Crear Nuevo Negocio</h3>
                <form method="POST">
                    <input type="hidden" name="accion" value="crear_negocio">
                    
                    <div class="form_group">
                        <label for="nombre_negocio">Nombre del Negocio</label>
                        <input type="text" id="nombre_negocio" name="nombre_negocio" 
                               placeholder="Ej: Tienda de Electrónicos" required>
                    </div>
                    
                    <div class="form_group">
                        <label for="descripcion_negocio">Descripción (opcional)</label>
                        <textarea id="descripcion_negocio" name="descripcion_negocio" 
                                  placeholder="Describe tu negocio..." rows="3"
                                  style="width: 100%; padding: 12px; border: 1px solid #D1D5DB; border-radius: 8px; resize: vertical;"></textarea>
                    </div>
                    
                    <button type="submit" class="form_button">Crear Mi Negocio</button>
                </form>
            </div>

            <!-- Formulario Unirse a Negocio -->
            <div id="form-unirse" class="form-section">
                <h3 style="margin-bottom: 20px; color: #1F2937;">Unirse a Negocio Existente</h3>
                <form method="POST">
                    <input type="hidden" name="accion" value="unirse_negocio">
                    
                    <div class="form_group">
                        <label for="codigo_invitacion">Código de Invitación</label>
                        <input type="text" id="codigo_invitacion" name="codigo_invitacion" 
                               placeholder="Ej: ABC12345" required maxlength="10"
                               style="text-transform: uppercase;">
                    </div>
                    
                    <div style="background: #F3F4F6; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                        <p style="margin: 0; color: #6B7280; font-size: 0.9rem;">
                            💡 <strong>Tip:</strong> El código de invitación te lo proporciona el propietario del negocio. 
                            Es un código de 8 caracteres único para cada negocio.
                        </p>
                    </div>
                    
                    <button type="submit" class="form_button">Unirse al Negocio</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function mostrarFormulario(tipo) {
            // Remover active de todas las cards
            document.querySelectorAll('.option-card').forEach(card => {
                card.classList.remove('active');
            });
            
            // Ocultar todos los formularios
            document.querySelectorAll('.form-section').forEach(section => {
                section.classList.remove('active');
            });
            
            // Activar la card seleccionada
            event.currentTarget.classList.add('active');
            
            // Mostrar el formulario correspondiente
            document.getElementById('form-' + tipo).classList.add('active');
        }
        
        // Auto-uppercase para código de invitación
        document.getElementById('codigo_invitacion').addEventListener('input', function(e) {
            e.target.value = e.target.value.toUpperCase();
        });
    </script>
</body>
</html>