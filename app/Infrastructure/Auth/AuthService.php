<?php
class AuthService {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function login($email, $password) {
        $stmt = $this->pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        $usuario = $stmt->fetch();

        if ($usuario && password_verify($password, $usuario['contrasena'])) {
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nombre'] = $usuario['nombre'];
            
            // Verificar si el usuario tiene negocios
            $this->verificarNegociosUsuario($usuario['id']);
            
            return true;
        }

        return false;
    }

    private function verificarNegociosUsuario($usuarioId) {
        // Obtener negocios del usuario
        $stmt = $this->pdo->prepare("
            SELECT n.*, un.rol 
            FROM negocios n
            JOIN usuarios_negocios un ON n.id = un.negocio_id
            WHERE un.usuario_id = ? AND un.activo = 1
            ORDER BY n.fecha_creacion ASC
        ");
        $stmt->execute([$usuarioId]);
        $negocios = $stmt->fetchAll();

        $_SESSION['negocios_usuario'] = $negocios;
        
        if (empty($negocios)) {
            // Usuario sin negocios - necesita onboarding
            $_SESSION['necesita_onboarding'] = true;
        } else {
            // Establecer el primer negocio como activo si no hay uno seleccionado
            if (!isset($_SESSION['negocio_activo_id'])) {
                $_SESSION['negocio_activo_id'] = $negocios[0]['id'];
                $_SESSION['negocio_activo'] = $negocios[0];
            }
            $_SESSION['necesita_onboarding'] = false;
        }
    }

    public function necesitaOnboarding() {
        return isset($_SESSION['necesita_onboarding']) && $_SESSION['necesita_onboarding'];
    }

    public function getNegociosUsuario() {
        return $_SESSION['negocios_usuario'] ?? [];
    }

    public function getNegocioActivo() {
        return $_SESSION['negocio_activo'] ?? null;
    }

    public function cambiarNegocioActivo($negocioId) {
        $negocios = $this->getNegociosUsuario();
        foreach ($negocios as $negocio) {
            if ($negocio['id'] == $negocioId) {
                $_SESSION['negocio_activo_id'] = $negocioId;
                $_SESSION['negocio_activo'] = $negocio;
                return true;
            }
        }
        return false;
    }

    public function logout() {
        session_destroy();
    }

    public function estaAutenticado() {
        return isset($_SESSION['usuario_id']);
    }

    public function getUsuarioId() {
        return $_SESSION['usuario_id'] ?? null;
    }

    public function getNombreUsuario() {
        return $_SESSION['usuario_nombre'] ?? null;
    }
}