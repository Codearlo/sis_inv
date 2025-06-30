<?php
namespace App\Negocios\UseCase;

class CrearNegocio {
    private $pdo;

    public function __construct(\PDO $pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Crea un nuevo negocio para el usuario
     *
     * @param int $usuarioId ID del usuario propietario
     * @param string $nombre Nombre del negocio
     * @param string|null $descripcion Descripción opcional
     * @return array Resultado con éxito y datos del negocio creado
     */
    public function ejecutar(int $usuarioId, string $nombre, ?string $descripcion = null): array {
        $this->pdo->beginTransaction();

        try {
            // Verificar que el usuario no tenga ya 2 negocios
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) as total 
                FROM usuarios_negocios 
                WHERE usuario_id = ? AND activo = 1
            ");
            $stmt->execute([$usuarioId]);
            $totalNegocios = $stmt->fetchColumn();

            if ($totalNegocios >= 2) {
                throw new \Exception('No puedes tener más de 2 negocios');
            }

            // Crear el negocio
            $stmt = $this->pdo->prepare("
                INSERT INTO negocios (nombre, descripcion, propietario_id) 
                VALUES (?, ?, ?)
            ");
            $stmt->execute([$nombre, $descripcion, $usuarioId]);
            $negocioId = $this->pdo->lastInsertId();

            // Relacionar usuario con el negocio como propietario
            $stmt = $this->pdo->prepare("
                INSERT INTO usuarios_negocios (usuario_id, negocio_id, rol) 
                VALUES (?, ?, 'propietario')
            ");
            $stmt->execute([$usuarioId, $negocioId]);

            // Obtener el negocio creado con su código de invitación
            $stmt = $this->pdo->prepare("
                SELECT * FROM negocios WHERE id = ?
            ");
            $stmt->execute([$negocioId]);
            $negocio = $stmt->fetch(\PDO::FETCH_ASSOC);

            $this->pdo->commit();

            return [
                'exito' => true,
                'negocio' => $negocio,
                'mensaje' => 'Negocio creado exitosamente'
            ];

        } catch (\Exception $e) {
            $this->pdo->rollBack();
            return [
                'exito' => false,
                'mensaje' => $e->getMessage()
            ];
        }
    }
}