-- Migracion: paquetes turisticos y servicios adicionales
-- Ejecutar en la base de datos travelnow1

CREATE TABLE IF NOT EXISTS `paquete_turistico` (
  `Id_paquete` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(300) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `precio_base` int(11) NOT NULL DEFAULT 0,
  `dias` int(11) NOT NULL DEFAULT 1,
  `imagen` varchar(255) NOT NULL DEFAULT 'paquete.jpg',
  `estado` varchar(50) NOT NULL DEFAULT 'activo',
  PRIMARY KEY (`Id_paquete`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `paquete_componente` (
  `Id_componente` int(11) NOT NULL AUTO_INCREMENT,
  `Id_paquete` int(11) NOT NULL,
  `tipo` varchar(50) NOT NULL DEFAULT 'otro',
  `titulo` varchar(300) NOT NULL,
  `descripcion` varchar(500) DEFAULT NULL,
  `id_habitacion` int(11) DEFAULT NULL,
  `precio_componente` int(11) NOT NULL DEFAULT 0,
  `orden` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`Id_componente`),
  KEY `idx_paquete` (`Id_paquete`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `servicio_adicional` (
  `Id_servicio` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(200) NOT NULL,
  `precio` int(11) NOT NULL DEFAULT 0,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`Id_servicio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `reserva_paquete` (
  `Id_reserva_paquete` int(11) NOT NULL AUTO_INCREMENT,
  `Id_paquete` int(11) NOT NULL,
  `Id_user` int(11) NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `servicios_extra` text DEFAULT NULL,
  `monto_paquete` int(11) NOT NULL DEFAULT 0,
  `monto_extras` int(11) NOT NULL DEFAULT 0,
  `monto_total` int(11) NOT NULL DEFAULT 0,
  `estado_reserva` varchar(50) NOT NULL DEFAULT 'pendiente',
  `metodo_pago` varchar(100) NOT NULL DEFAULT 'por definir',
  PRIMARY KEY (`Id_reserva_paquete`),
  KEY `idx_paquete_user` (`Id_paquete`, `Id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Si la columna ya existe, omitir estos ALTER manualmente.
ALTER TABLE `pago`
  ADD COLUMN `Id_reserva_paquete` int(11) DEFAULT NULL AFTER `Id_reserva`;

ALTER TABLE `pago`
  MODIFY `Id_reserva` int(11) DEFAULT NULL;

INSERT INTO `servicio_adicional` (`nombre`, `precio`, `activo`) VALUES
('Transporte aeropuerto', 80000, 1),
('Guia bilingue', 120000, 1),
('Cena romantica', 150000, 1),
('Seguro de viaje', 45000, 1)
ON DUPLICATE KEY UPDATE nombre = VALUES(nombre);
