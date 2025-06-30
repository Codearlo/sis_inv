<?php
namespace App\Dashboard\UseCase;

class GetDashboardData {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function execute(int $negocioId): array {
        try {
            // Consulta para obtener el total de productos del negocio
            $totalProductosStmt = $this->pdo->prepare("
                SELECT COUNT(id) as total 
                FROM productos 
                WHERE negocio_id = ?
            ");
            $totalProductosStmt->execute([$negocioId]);
            $totalProductos = $totalProductosStmt->fetch(\PDO::FETCH_ASSOC)['total'];

            // Consulta para obtener la cantidad total de items en stock del negocio
            $totalStockStmt = $this->pdo->prepare("
                SELECT SUM(cantidad) as total 
                FROM pedidos 
                WHERE negocio_id = ?
            ");
            $totalStockStmt->execute([$negocioId]);
            $totalStock = $totalStockStmt->fetch(\PDO::FETCH_ASSOC)['total'];

            // Consulta para obtener los productos con bajo stock del negocio
            $lowStockStmt = $this->pdo->prepare("
                SELECT p.nombre, SUM(pe.cantidad) as stock_actual
                FROM productos p
                JOIN pedidos pe ON p.id = pe.producto_id
                WHERE p.negocio_id = ?
                GROUP BY p.id
                HAVING stock_actual < 5
                ORDER BY stock_actual ASC
            ");
            $lowStockStmt->execute([$negocioId]);
            $productosBajoStock = $lowStockStmt->fetchAll(\PDO::FETCH_ASSOC);

            // Consulta para obtener las compras recientes del negocio
            $comprasRecientesStmt = $this->pdo->prepare("
                SELECT p.nombre, pe.cantidad, pe.precio_unitario, pe.fecha_compra
                FROM pedidos pe
                JOIN productos p ON pe.producto_id = p.id
                WHERE pe.negocio_id = ?
                ORDER BY pe.fecha_compra DESC
                LIMIT 5
            ");
            $comprasRecientesStmt->execute([$negocioId]);
            $comprasRecientes = $comprasRecientesStmt->fetchAll(\PDO::FETCH_ASSOC);

            return [
                'total_productos' => $totalProductos,
                'total_stock' => $totalStock ?: 0,
                'productos_bajo_stock' => $productosBajoStock,
                'compras_recientes' => $comprasRecientes
            ];
        } catch (\PDOException $e) {
            return [
                'error' => 'Error al obtener los datos del dashboard.'
            ];
        }
    }
}