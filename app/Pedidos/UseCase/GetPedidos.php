<?php
namespace App\Pedidos\UseCase;

class GetPedidos {
    private $pdo;

    public function __construct(\PDO $pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Obtiene una lista completa de todos los pedidos de un negocio específico
     *
     * @param int $negocioId ID del negocio
     * @return array Un array con todos los pedidos del negocio.
     */
    public function execute(int $negocioId): array {
        try {
            // Consulta SQL que une las tres tablas filtrando por negocio
            $sql = "
                SELECT 
                    p.id,
                    p.fecha_compra,
                    p.numero_compra_dia,
                    p.proveedor,
                    p.cantidad,
                    p.precio_unitario,
                    p.estado_pago,
                    p.fecha_recibido,
                    prod.nombre as producto_nombre,
                    cf.nombre_cuenta 
                FROM 
                    pedidos p
                JOIN 
                    productos prod ON p.producto_id = prod.id
                LEFT JOIN 
                    cuentas_financieras cf ON p.cuenta_id = cf.id
                WHERE 
                    p.negocio_id = ?
                ORDER BY 
                    p.fecha_compra DESC, p.id DESC
            ";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$negocioId]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);

        } catch (\PDOException $e) {
            // En una aplicación real, se registraría el error.
            return [];
        }
    }
}