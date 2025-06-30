<?php
namespace App\Pedidos\UseCase;

class RegistrarPedido {
    private $pdo;

    public function __construct(\PDO $pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Registra un nuevo pedido en la base de datos para un negocio específico.
     *
     * @param array $datos Los datos del formulario del pedido.
     * @param int $negocioId ID del negocio al que pertenece el pedido
     * @return bool True si el registro fue exitoso, false en caso contrario.
     */
    public function ejecutar(array $datos, int $negocioId): bool {
        $this->pdo->beginTransaction();

        try {
            // 1. Verificar si el producto ya existe en este negocio o crearlo.
            $stmt = $this->pdo->prepare("SELECT id FROM productos WHERE nombre = ? AND negocio_id = ?");
            $stmt->execute([$datos['producto'], $negocioId]);
            $producto = $stmt->fetch();

            if (!$producto) {
                // Insertar nuevo producto si no existe en este negocio
                $stmt = $this->pdo->prepare("INSERT INTO productos (nombre, negocio_id) VALUES (?, ?)");
                $stmt->execute([$datos['producto'], $negocioId]);
                $producto_id = $this->pdo->lastInsertId();
            } else {
                $producto_id = $producto['id'];
            }

            // 2. Calcular el número de compra para ese producto en ese día en este negocio.
            $stmt = $this->pdo->prepare(
                "SELECT COUNT(*) as total_hoy FROM pedidos WHERE producto_id = ? AND fecha_compra = ? AND negocio_id = ?"
            );
            $stmt->execute([$producto_id, $datos['fecha_compra'], $negocioId]);
            $numero_compra_dia = $stmt->fetchColumn() + 1;

            // 3. Insertar el nuevo pedido en la tabla `pedidos`.
            $sql = "INSERT INTO pedidos 
                        (negocio_id, producto_id, proveedor, fecha_compra, numero_compra_dia, fecha_recibido, 
                         precio_unitario, cantidad, estado_pago, cuenta_id) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $this->pdo->prepare($sql);
            
            $stmt->execute([
                $negocioId,
                $producto_id,
                $datos['proveedor'],
                $datos['fecha_compra'],
                $numero_compra_dia,
                empty($datos['fecha_recibido']) ? null : $datos['fecha_recibido'],
                $datos['precio_unitario'],
                $datos['cantidad'],
                $datos['estado_pago'],
                $datos['cuenta_id'] // Será null si no es una deuda
            ]);

            $this->pdo->commit();
            return true;

        } catch (\PDOException $e) {
            $this->pdo->rollBack();
            // En una aplicación real, se debería registrar el error: error_log($e->getMessage());
            return false;
        }
    }
}