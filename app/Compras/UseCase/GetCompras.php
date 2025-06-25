<?php
namespace App\Compras\UseCase;

class GetCompras {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function execute(): array {
        try {
            $stmt = $this->pdo->query("
                SELECT 
                    l.*, 
                    p.nombre as producto_nombre 
                FROM lotes l
                JOIN productos p ON l.producto_id = p.id
                ORDER BY l.fecha_compra DESC
            ");
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            // Manejo de errores
            return [];
        }
    }
}