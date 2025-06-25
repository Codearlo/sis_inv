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
            return true;
        }

        return false;
    }

    public function logout() {
        session_destroy();
    }

    public function estaAutenticado() {
        return isset($_SESSION['usuario_id']);
    }
}
