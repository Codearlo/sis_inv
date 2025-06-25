<?php
class RegistrarCompra {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function ejecutar(array $datos): bool {
        try {
            // Verificar si el producto ya existe
            $stmt = $this->pdo->prepare("SELECT id FROM productos WHERE nombre = ?");
            $stmt->execute([$datos['producto']]);
            $producto = $stmt->fetch();

            if (!$producto) {
                // Insertar nuevo producto
                $stmt = $this->pdo->prepare("INSERT INTO productos (nombre) VALUES (?)");
                $stmt->execute([$datos['producto']]);
                $producto_id = $this->pdo->lastInsertId();
            } else {
                $producto_id = $producto['id'];
            }

            // Insertar lote
            $stmt = $this->pdo->prepare("INSERT INTO lotes 
                (producto_id, proveedor, fecha_compra, fecha_recibido, precio_unitario, cantidad, pagado) 
                VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $producto_id,
                $datos['proveedor'],
                $datos['fecha_compra'],
                $datos['fecha_recibido'],
                $datos['precio_unitario'],
                $datos['cantidad'],
                $datos['pagado']
            ]);

            return true;
        } catch (PDOException $e) {
            return false;
        }
    }
}
