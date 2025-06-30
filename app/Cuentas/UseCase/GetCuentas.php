<?php
namespace App\Cuentas\UseCase;

class GetCuentas {
    private $pdo;

    public function __construct(\PDO $pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Ejecuta la consulta para obtener todas las cuentas financieras de un negocio específico.
     *
     * @param int $negocioId ID del negocio
     * @return array Un array de cuentas financieras del negocio.
     */
    public function execute(int $negocioId): array {
        try {
            $stmt = $this->pdo->prepare("
                SELECT id, nombre_cuenta, tipo_cuenta, banco
                FROM cuentas_financieras 
                WHERE negocio_id = ?
                ORDER BY nombre_cuenta ASC
            ");
            $stmt->execute([$negocioId]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            // En una aplicación real, aquí se registraría el error.
            // Por ahora, devolvemos un array vacío en caso de fallo.
            return [];
        }
    }
}