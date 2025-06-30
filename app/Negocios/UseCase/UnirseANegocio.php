<?php
namespace App\Negocios\UseCase;

class UnirseANegocio {
    private $pdo;

    public function __construct(\PDO $pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Une un usuario a un negocio existente usando código de invitación
     *
     * @param int $usuarioId ID del usuario
     * @param string $codigoInvitacion Código de invitación del negocio
     * @return array Resultado con éxito y datos
     */
    public function ejecutar(int $usuarioId, string $codigoInvitacion): array {
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
                throw new \Exception('No puedes unirte a más de 2 negocios');
            }

            // Buscar el negocio por código de invitación
            $stmt = $this->pdo->prepare("
                SELECT * FROM negocios 
                WHERE codigo_invitacion = ? AND activo = 1
            ");
            $stmt->execute([$codigoInvitacion]);
            $negocio = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$negocio) {
                throw new \Exception('Código de invitación inválido o negocio no encontrado');
            }

            // Verificar que el usuario no esté ya en este negocio
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) as total 
                FROM usuarios_negocios 
                WHERE usuario_id = ? AND negocio_id = ?
            ");
            $stmt->execute([$usuarioId, $negocio['id']]);
            $yaEsMiembro = $stmt->fetchColumn();

            if ($yaEsMiembro > 0) {
                throw new \Exception('Ya eres miembro de este negocio');
            }

            // Unir usuario al negocio como colaborador
            $stmt = $this->pdo->prepare("
                INSERT INTO usuarios_negocios (usuario_id, negocio_id, rol) 
                VALUES (?, ?, 'colaborador')
            ");
            $stmt->execute([$usuarioId, $negocio['id']]);

            $this->pdo->commit();

            return [
                'exito' => true,
                'negocio' => $negocio,
                'mensaje' => 'Te has unido al negocio exitosamente'
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