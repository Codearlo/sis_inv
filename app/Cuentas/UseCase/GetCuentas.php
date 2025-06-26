<?php
namespace App\Cuentas\UseCase;

class GetCuentas {
    private $pdo;

    public function __construct(\PDO $pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Ejecuta la consulta para obtener todas las cuentas financieras.
     *
     * @return array Un array de cuentas financieras.
     */
    public function execute(): array {
        try {
            $stmt = $this->pdo->query("
                SELECT id, nombre_cuenta, tipo_cuenta 
                FROM cuentas_financieras 
                ORDER BY nombre_cuenta ASC
            ");
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            // En una aplicación real, aquí se registraría el error.
            // Por ahora, devolvemos un array vacío en caso de fallo.
            return [];
        }
    }
}