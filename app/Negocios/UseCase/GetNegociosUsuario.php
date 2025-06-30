<?php
namespace App\Negocios\UseCase;

class GetNegociosUsuario {
    private $pdo;

    public function __construct(\PDO $pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Obtiene todos los negocios de un usuario
     *
     * @param int $usuarioId ID del usuario
     * @return array Lista de negocios del usuario
     */
    public function ejecutar(int $usuarioId): array {
        try {
            $stmt = $this->pdo->prepare("
                SELECT 
                    n.*,
                    un.rol,
                    un.fecha_union,
                    (SELECT COUNT(*) FROM usuarios_negocios WHERE negocio_id = n.id AND activo = 1) as total_miembros
                FROM negocios n
                JOIN usuarios_negocios un ON n.id = un.negocio_id
                WHERE un.usuario_id = ? AND un.activo = 1 AND n.activo = 1
                ORDER BY n.fecha_creacion ASC
            ");
            $stmt->execute([$usuarioId]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);

        } catch (\PDOException $e) {
            return [];
        }
    }

    /**
     * Verifica si un usuario puede crear más negocios
     *
     * @param int $usuarioId ID del usuario
     * @return bool True si puede crear más negocios
     */
    public function puedeCrearMasNegocios(int $usuarioId): bool {
        try {
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) as total 
                FROM usuarios_negocios 
                WHERE usuario_id = ? AND activo = 1
            ");
            $stmt->execute([$usuarioId]);
            $total = $stmt->fetchColumn();
            
            return $total < 2;

        } catch (\PDOException $e) {
            return false;
        }
    }
}