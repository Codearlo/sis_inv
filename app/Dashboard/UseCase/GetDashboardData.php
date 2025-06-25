<?php
namespace App\Dashboard\UseCase;

class GetDashboardData {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function execute(): array {
        try {
            // Consulta para obtener el total de productos
            $totalProductosStmt = $this->pdo->query("SELECT COUNT(id) as total FROM productos");
            $totalProductos = $totalProductosStmt->fetch(\PDO::FETCH_ASSOC)['total'];

            // Consulta para obtener la cantidad total de items en stock (sumando las cantidades de todos los lotes)
            $totalStockStmt = $this->pdo->query("SELECT SUM(cantidad) as total FROM lotes");
            $totalStock = $totalStockStmt->fetch(\PDO::FETCH_ASSOC)['total'];

            // Consulta para obtener los productos con bajo stock (ej: menos de 5 unidades)
            $lowStockStmt = $this->pdo->prepare("
                SELECT p.nombre, SUM(l.cantidad) as stock_actual
                FROM productos p
                JOIN lotes l ON p.id = l.producto_id
                GROUP BY p.id
                HAVING stock_actual < 5
                ORDER BY stock_actual ASC
            ");
            $lowStockStmt->execute();
            $productosBajoStock = $lowStockStmt->fetchAll(\PDO::FETCH_ASSOC);

            // Consulta para obtener las compras recientes
            $comprasRecientesStmt = $this->pdo->query("
                SELECT p.nombre, l.cantidad, l.precio_unitario, l.fecha_compra
                FROM lotes l
                JOIN productos p ON l.producto_id = p.id
                ORDER BY l.fecha_compra DESC
                LIMIT 5
            ");
            $comprasRecientes = $comprasRecientesStmt->fetchAll(\PDO::FETCH_ASSOC);


            return [
                'total_productos' => $totalProductos,
                'total_stock' => $totalStock ?: 0,
                'productos_bajo_stock' => $productosBajoStock,
                'compras_recientes' => $comprasRecientes
            ];
        } catch (\PDOException $e) {
            // En una aplicación real, aquí manejarías el error de forma más robusta (logs, etc.)
            return [
                'error' => 'Error al obtener los datos del dashboard.'
            ];
        }
    }
}