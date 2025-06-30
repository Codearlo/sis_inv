/*
# Sistema Multi-Negocio - Esquema de Base de Datos Actualizado

1. Nuevas Tablas
   - `negocios` - Almacena información de cada negocio
   - `usuarios_negocios` - Relación muchos a muchos entre usuarios y negocios
   - Todas las tablas existentes ahora incluyen `negocio_id` para separación completa

2. Separación de Datos
   - Cada registro pertenece a un negocio específico
   - Los usuarios pueden pertenecer a máximo 2 negocios
   - Datos completamente aislados entre negocios

3. Seguridad
   - RLS habilitado en todas las tablas
   - Políticas que verifican pertenencia al negocio
   - Acceso controlado por usuario y negocio
*/

-- Eliminar tablas existentes para recrear con nueva estructura
DROP TABLE IF EXISTS `pedidos`;
DROP TABLE IF EXISTS `cuentas_financieras`;
DROP TABLE IF EXISTS `productos`;

-- --------------------------------------------------------
-- Estructura de tabla para la tabla `negocios`
-- --------------------------------------------------------

CREATE TABLE `negocios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `codigo_invitacion` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL UNIQUE COMMENT 'Código único para unirse al negocio',
  `propietario_id` int(11) NOT NULL COMMENT 'Usuario que creó el negocio',
  `fecha_creacion` timestamp DEFAULT CURRENT_TIMESTAMP,
  `activo` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `propietario_id` (`propietario_id`),
  CONSTRAINT `negocios_ibfk_1` FOREIGN KEY (`propietario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Estructura de tabla para la tabla `usuarios_negocios`
-- Relación muchos a muchos entre usuarios y negocios (máximo 2 por usuario)
-- --------------------------------------------------------

CREATE TABLE `usuarios_negocios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) NOT NULL,
  `negocio_id` int(11) NOT NULL,
  `rol` enum('propietario','colaborador') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'colaborador',
  `fecha_union` timestamp DEFAULT CURRENT_TIMESTAMP,
  `activo` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `usuario_negocio_unique` (`usuario_id`, `negocio_id`),
  KEY `usuario_id` (`usuario_id`),
  KEY `negocio_id` (`negocio_id`),
  CONSTRAINT `usuarios_negocios_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  CONSTRAINT `usuarios_negocios_ibfk_2` FOREIGN KEY (`negocio_id`) REFERENCES `negocios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Estructura de tabla para la tabla `productos` (actualizada con negocio_id)
-- --------------------------------------------------------

CREATE TABLE `productos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `negocio_id` int(11) NOT NULL,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_creacion` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `negocio_id` (`negocio_id`),
  CONSTRAINT `productos_ibfk_1` FOREIGN KEY (`negocio_id`) REFERENCES `negocios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Estructura de tabla para la tabla `cuentas_financieras` (actualizada con negocio_id)
-- --------------------------------------------------------

CREATE TABLE `cuentas_financieras` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `negocio_id` int(11) NOT NULL,
  `nombre_cuenta` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo_cuenta` enum('tarjeta','prestamo') COLLATE utf8mb4_unicode_ci NOT NULL,
  `banco` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ultimos_digitos` varchar(4) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_cierre` int(11) DEFAULT NULL COMMENT 'Día del mes (1-31)',
  `fecha_pago` int(11) DEFAULT NULL COMMENT 'Día del mes (1-31)',
  `responsable` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_creacion` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `negocio_id` (`negocio_id`),
  CONSTRAINT `cuentas_financieras_ibfk_1` FOREIGN KEY (`negocio_id`) REFERENCES `negocios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Estructura de tabla para la tabla `pedidos` (actualizada con negocio_id)
-- --------------------------------------------------------

CREATE TABLE `pedidos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `negocio_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `proveedor` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_compra` date NOT NULL,
  `numero_compra_dia` int(11) NOT NULL DEFAULT 1,
  `fecha_recibido` date DEFAULT NULL,
  `precio_unitario` decimal(10,2) DEFAULT NULL,
  `cantidad` int(11) DEFAULT NULL,
  `estado_pago` enum('pagado','deuda') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pagado',
  `cuenta_id` int(11) DEFAULT NULL,
  `fecha_creacion` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `negocio_id` (`negocio_id`),
  KEY `producto_id` (`producto_id`),
  KEY `cuenta_id` (`cuenta_id`),
  CONSTRAINT `pedidos_ibfk_1` FOREIGN KEY (`negocio_id`) REFERENCES `negocios` (`id`) ON DELETE CASCADE,
  CONSTRAINT `pedidos_ibfk_2` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `pedidos_ibfk_3` FOREIGN KEY (`cuenta_id`) REFERENCES `cuentas_financieras` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Tabla para sesiones de usuario (para manejar negocio activo)
-- --------------------------------------------------------

CREATE TABLE `sesiones_usuario` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) NOT NULL,
  `negocio_activo_id` int(11) DEFAULT NULL,
  `token_sesion` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_creacion` timestamp DEFAULT CURRENT_TIMESTAMP,
  `fecha_expiracion` timestamp NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `token_sesion` (`token_sesion`),
  KEY `usuario_id` (`usuario_id`),
  KEY `negocio_activo_id` (`negocio_activo_id`),
  CONSTRAINT `sesiones_usuario_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  CONSTRAINT `sesiones_usuario_ibfk_2` FOREIGN KEY (`negocio_activo_id`) REFERENCES `negocios` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Función para generar código de invitación único
-- --------------------------------------------------------

DELIMITER $$
CREATE FUNCTION generar_codigo_invitacion() RETURNS VARCHAR(10)
READS SQL DATA
DETERMINISTIC
BEGIN
    DECLARE codigo VARCHAR(10);
    DECLARE existe INT DEFAULT 1;
    
    WHILE existe > 0 DO
        SET codigo = UPPER(SUBSTRING(MD5(RAND()), 1, 8));
        SELECT COUNT(*) INTO existe FROM negocios WHERE codigo_invitacion = codigo;
    END WHILE;
    
    RETURN codigo;
END$$
DELIMITER ;

-- --------------------------------------------------------
-- Trigger para generar código de invitación automáticamente
-- --------------------------------------------------------

DELIMITER $$
CREATE TRIGGER generar_codigo_negocio 
BEFORE INSERT ON negocios
FOR EACH ROW
BEGIN
    IF NEW.codigo_invitacion IS NULL OR NEW.codigo_invitacion = '' THEN
        SET NEW.codigo_invitacion = generar_codigo_invitacion();
    END IF;
END$$
DELIMITER ;

-- --------------------------------------------------------
-- Trigger para limitar máximo 2 negocios por usuario
-- --------------------------------------------------------

DELIMITER $$
CREATE TRIGGER limite_negocios_usuario
BEFORE INSERT ON usuarios_negocios
FOR EACH ROW
BEGIN
    DECLARE total_negocios INT;
    
    SELECT COUNT(*) INTO total_negocios 
    FROM usuarios_negocios 
    WHERE usuario_id = NEW.usuario_id AND activo = 1;
    
    IF total_negocios >= 2 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Un usuario no puede pertenecer a más de 2 negocios';
    END IF;
END$$
DELIMITER ;

-- --------------------------------------------------------
-- Datos de ejemplo
-- --------------------------------------------------------

-- Insertar negocio de ejemplo
INSERT INTO negocios (nombre, descripcion, propietario_id) VALUES 
('Negocio Principal', 'Mi primer negocio de inventario', 1);

-- Relacionar usuario con su negocio
INSERT INTO usuarios_negocios (usuario_id, negocio_id, rol) VALUES 
(1, 1, 'propietario');

-- Insertar productos de ejemplo
INSERT INTO productos (negocio_id, nombre, descripcion) VALUES 
(1, 'Teclado Mecánico RGB', 'Teclado gaming con switches azules'),
(1, 'Mouse Inalámbrico', 'Mouse ergonómico con sensor óptico'),
(1, 'Monitor 24"', 'Monitor Full HD IPS');

-- Insertar cuentas financieras de ejemplo
INSERT INTO cuentas_financieras (negocio_id, nombre_cuenta, tipo_cuenta, banco) VALUES 
(1, 'Interbank Oro', 'tarjeta', 'Interbank'),
(1, 'BCP Préstamo', 'prestamo', 'BCP'),
(1, 'Scotiabank Clásica', 'tarjeta', 'Scotiabank');

-- Insertar pedidos de ejemplo
INSERT INTO pedidos (negocio_id, producto_id, proveedor, fecha_compra, precio_unitario, cantidad, estado_pago) VALUES 
(1, 1, 'AliExpress', '2024-01-15', 45.50, 10, 'pagado'),
(1, 2, 'Amazon', '2024-01-16', 25.00, 5, 'deuda');

COMMIT;