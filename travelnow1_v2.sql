-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 19-04-2026 a las 21:44:59
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `travelnow1`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `descripcion`
--

CREATE TABLE `descripcion` (
  `id_descripcion` int(11) NOT NULL,
  `Id_precio` int(11) NOT NULL,
  `Id_reserva` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empresa`
--

CREATE TABLE `empresa` (
  `id_alojamieto` int(11) NOT NULL,
  `nombre` varchar(300) NOT NULL,
  `ubicacion` varchar(300) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `empresa`
--

INSERT INTO `empresa` (`id_alojamieto`, `nombre`, `ubicacion`) VALUES
(2, 'Hotel Travel Now Demo', 'Medellin, Antioquia'),
(3, 'Hotel Funcional Demo', 'Cali, Valle'),
(5, 'Hotel Ficha Completa', 'Armenia, Quindio'),
(6, 'Hotel Imagen Subida', 'Neiva, Huila'),
(7, 'hotel aleja', 'nose en la casa de aleja');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `habitacion`
--

CREATE TABLE `habitacion` (
  `Id_habitacion` int(11) NOT NULL,
  `Id_empresa` int(11) NOT NULL,
  `tipo_habitacion` int(11) NOT NULL,
  `imagen` varchar(255) NOT NULL DEFAULT 'habitacion1.jpg',
  `descripcion_texto` text DEFAULT NULL,
  `capacidad` int(11) NOT NULL DEFAULT 2,
  `banos` int(11) NOT NULL DEFAULT 1,
  `cupos` int(11) NOT NULL DEFAULT 2,
  `servicios` varchar(500) NOT NULL DEFAULT 'wifi, tv, bano privado'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `habitacion`
--

INSERT INTO `habitacion` (`Id_habitacion`, `Id_empresa`, `tipo_habitacion`, `imagen`, `descripcion_texto`, `capacidad`, `banos`, `cupos`, `servicios`) VALUES
(3, 2, 1, 'habitacion1.jpg', 'Alojamiento publicado en Travel Now con disponibilidad real.', 2, 1, 2, 'wifi, tv, bano privado'),
(4, 2, 2, 'habitacion1.jpg', 'Alojamiento publicado en Travel Now con disponibilidad real.', 2, 1, 2, 'wifi, tv, bano privado'),
(5, 3, 3, 'hotel1.jpg', 'Alojamiento publicado en Travel Now con disponibilidad real.', 2, 1, 2, 'wifi, tv, bano privado'),
(7, 5, 3, 'hotel1.jpg', 'Hotel amplio con vista a la ciudad y zonas comunes activas.', 5, 2, 3, 'wifi, parqueadero, desayuno, piscina'),
(8, 6, 1, 'uploads/propiedad_69e437387ea1b.jpg', 'Propiedad con imagen subida desde formulario.', 2, 1, 1, 'wifi, aire acondicionado'),
(9, 7, 1, 'uploads/propiedad_69e43918686f3.png', 'es la casa de aleja', 2, 1, 2, 'wifi, tv, bano privado');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pago`
--

CREATE TABLE `pago` (
  `Id_pago` int(11) NOT NULL,
  `Id_reserva` int(11) NOT NULL,
  `metodo_pago` varchar(100) NOT NULL DEFAULT 'por definir',
  `monto` int(11) NOT NULL DEFAULT 0,
  `monto_reembolso` int(11) NOT NULL DEFAULT 0,
  `estado_pago` varchar(50) NOT NULL DEFAULT 'pendiente',
  `fecha_pago` datetime DEFAULT NULL,
  `referencia` varchar(100) DEFAULT NULL,
  `detalle_reembolso` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pago`
--

INSERT INTO `pago` (`Id_pago`, `Id_reserva`, `metodo_pago`, `monto`, `monto_reembolso`, `estado_pago`, `fecha_pago`, `referencia`, `detalle_reembolso`) VALUES
(1, 1, 'demo', 120000, 60000, 'reembolsado', '2026-04-18 13:27:26', 'DEMO-1', 'reembolso parcial'),
(2, 2, 'demo', 220000, 0, 'cancelado', NULL, 'DEMO-2', NULL),
(3, 3, 'demo', 120000, 0, 'pagado', '2026-04-18 13:27:26', 'DEMO-3', NULL),
(4, 4, 'tarjeta', 120000, 0, 'pagado', '2026-04-18 14:11:39', 'USR-4', NULL),
(8, 5, 'transferencia', 220000, 0, 'pagado', NULL, 'USR-5', NULL),
(9, 6, 'por definir', 233333, 0, 'pendiente', NULL, 'RES-6', NULL),
(10, 7, 'por definir', 130000, 0, 'pendiente', NULL, 'RES-7', NULL),
(11, 8, 'por definir', 233333, 0, 'pendiente', NULL, 'RES-8', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `persona`
--

CREATE TABLE `persona` (
  `Id_persona` int(11) NOT NULL,
  `Id_rol_user` int(11) NOT NULL,
  `Id_descripcion` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `precio`
--

CREATE TABLE `precio` (
  `Id_precio` int(11) NOT NULL,
  `Id_habitacion` int(11) NOT NULL,
  `precio` int(11) NOT NULL,
  `estado` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `precio`
--

INSERT INTO `precio` (`Id_precio`, `Id_habitacion`, `precio`, `estado`) VALUES
(3, 3, 120000, 'Disponible'),
(4, 4, 220000, 'Disponible'),
(5, 5, 175000, 'Disponible'),
(7, 7, 210000, 'Disponible'),
(8, 8, 130000, 'Disponible'),
(9, 9, 233333, 'Disponible');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reserva`
--

CREATE TABLE `reserva` (
  `Id_reserva` int(11) NOT NULL,
  `fecha_ingreso` datetime NOT NULL,
  `fecha_salida` datetime NOT NULL,
  `tipo_habitacion` varchar(500) NOT NULL,
  `servicio_especial` varchar(500) NOT NULL,
  `estado_reserva` varchar(50) NOT NULL DEFAULT 'pendiente',
  `Id_user` int(11) DEFAULT NULL,
  `id_habitacion` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `reserva`
--

INSERT INTO `reserva` (`Id_reserva`, `fecha_ingreso`, `fecha_salida`, `tipo_habitacion`, `servicio_especial`, `estado_reserva`, `Id_user`, `id_habitacion`) VALUES
(1, '2026-04-20 14:00:00', '2026-04-23 12:00:00', 'Estandar', 'Desayuno incluido', 'cancelada', 108, 3),
(2, '2026-04-25 15:00:00', '2026-04-28 11:00:00', 'Superior', 'Parqueadero', 'rechazada', 108, 4),
(3, '2026-05-02 14:00:00', '2026-05-05 12:00:00', '1', 'Late checkout', 'aprobada', 108, 3),
(4, '2026-04-20 12:00:00', '2026-04-21 12:00:00', '1', 'Sin cruce', 'aprobada', 108, 3),
(5, '2026-04-22 20:00:00', '2026-04-24 11:00:00', '2', 'Pago automatico demo', 'aprobada', 108, 4),
(6, '2026-04-21 20:00:00', '2026-04-30 10:00:00', '1', '88888', 'pendiente', 109, 9),
(7, '2026-04-23 20:00:00', '2026-04-30 10:10:00', '1', 'no', 'pendiente', 109, 8),
(8, '2026-05-01 09:00:00', '2026-05-06 08:09:00', '1', 'desayuno', 'pendiente', 108, 9);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol`
--

CREATE TABLE `rol` (
  `Id_rol` int(11) NOT NULL,
  `rol` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `rol`
--

INSERT INTO `rol` (`Id_rol`, `rol`) VALUES
(1, 'user'),
(2, 'admin');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol_user`
--

CREATE TABLE `rol_user` (
  `id_rol_user` int(11) NOT NULL,
  `Id_rol` int(11) NOT NULL,
  `Id_user` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `rol_user`
--

INSERT INTO `rol_user` (`id_rol_user`, `Id_rol`, `Id_user`) VALUES
(2, 1, 33),
(3, 2, 37),
(8, 1, 84),
(13, 1, 89),
(14, 1, 90),
(15, 1, 91),
(16, 1, 92),
(17, 1, 93),
(20, 1, 96),
(29, 1, 105),
(32, 2, 107),
(33, 1, 108),
(34, 2, 109);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user`
--

CREATE TABLE `user` (
  `Id_user` int(11) NOT NULL,
  `nombre` varchar(300) NOT NULL,
  `apellido` varchar(300) NOT NULL,
  `telefono` varchar(10) NOT NULL,
  `correo` varchar(500) NOT NULL,
  `contrasena` varchar(8) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `user`
--

INSERT INTO `user` (`Id_user`, `nombre`, `apellido`, `telefono`, `correo`, `contrasena`) VALUES
(33, 'Juan ', 'tovar', '3223232323', 'papas@gmail.com', '12345678'),
(37, 'victor', 'rojas', '3124567896', 'vict@gmail.com', '12345678'),
(74, 'Oscar', 'Moreno', '3148953105', 'Oscar@gmail.com', '$2y$10$O'),
(84, 'Blanca', 'Torres', '3126623565', 'blanca@gmail.com', '12345768'),
(89, 'Oscar', 'Moreno', '3126623565', 'Oscar1@gmail.com', '97654321'),
(90, 'zoe', 'Torree', '3124456789', 'zoe@gmail.com', '97654321'),
(91, 'Alejandro', 'Bulla', '3124567890', 'bulla@gmail.com', '12345678'),
(92, 'Luisa', 'Vitola', '3123452314', 'lulu@gmail.com', '43245677'),
(93, 'Camilo', 'Montoya', '3123452314', 'cami@gmail.com', '12345678'),
(96, 'Luis', 'Giraldo', '321456789', 'luis@gmial.com', '11111111'),
(105, 'Juan David', 'Perez', '3116542367', 'juan@gmail.com', '21436587'),
(107, 'Kelly Alejandra', 'Torres', '3206252877', 'Aleja@gmail.com', '12345678'),
(108, 'DemoUser', 'Prueba', '3001112233', 'demouser2026@example.com', '12345678'),
(109, 'DemoAdmin', 'Prueba', '3001112244', 'demoadmin2026@example.com', '12345678');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `descripcion`
--
ALTER TABLE `descripcion`
  ADD PRIMARY KEY (`id_descripcion`),
  ADD KEY `Id_precio` (`Id_precio`),
  ADD KEY `Id_reserva` (`Id_reserva`);

--
-- Indices de la tabla `empresa`
--
ALTER TABLE `empresa`
  ADD PRIMARY KEY (`id_alojamieto`);

--
-- Indices de la tabla `habitacion`
--
ALTER TABLE `habitacion`
  ADD PRIMARY KEY (`Id_habitacion`),
  ADD KEY `Id_empresa` (`Id_empresa`);

--
-- Indices de la tabla `pago`
--
ALTER TABLE `pago`
  ADD PRIMARY KEY (`Id_pago`),
  ADD UNIQUE KEY `uq_pago_reserva` (`Id_reserva`);

--
-- Indices de la tabla `persona`
--
ALTER TABLE `persona`
  ADD PRIMARY KEY (`Id_persona`),
  ADD KEY `Id_rol_user` (`Id_rol_user`),
  ADD KEY `Id_descripcion` (`Id_descripcion`);

--
-- Indices de la tabla `precio`
--
ALTER TABLE `precio`
  ADD PRIMARY KEY (`Id_precio`),
  ADD KEY `Id_habitacion` (`Id_habitacion`);

--
-- Indices de la tabla `reserva`
--
ALTER TABLE `reserva`
  ADD PRIMARY KEY (`Id_reserva`),
  ADD KEY `id_habitacion` (`id_habitacion`),
  ADD KEY `idx_reserva_user` (`Id_user`);

--
-- Indices de la tabla `rol`
--
ALTER TABLE `rol`
  ADD PRIMARY KEY (`Id_rol`);

--
-- Indices de la tabla `rol_user`
--
ALTER TABLE `rol_user`
  ADD PRIMARY KEY (`id_rol_user`),
  ADD KEY `Id_rol` (`Id_rol`),
  ADD KEY `Id_user` (`Id_user`);

--
-- Indices de la tabla `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`Id_user`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `descripcion`
--
ALTER TABLE `descripcion`
  MODIFY `id_descripcion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `empresa`
--
ALTER TABLE `empresa`
  MODIFY `id_alojamieto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `habitacion`
--
ALTER TABLE `habitacion`
  MODIFY `Id_habitacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `pago`
--
ALTER TABLE `pago`
  MODIFY `Id_pago` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `persona`
--
ALTER TABLE `persona`
  MODIFY `Id_persona` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `precio`
--
ALTER TABLE `precio`
  MODIFY `Id_precio` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `reserva`
--
ALTER TABLE `reserva`
  MODIFY `Id_reserva` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `rol`
--
ALTER TABLE `rol`
  MODIFY `Id_rol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `rol_user`
--
ALTER TABLE `rol_user`
  MODIFY `id_rol_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT de la tabla `user`
--
ALTER TABLE `user`
  MODIFY `Id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=110;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `habitacion`
--
ALTER TABLE `habitacion`
  ADD CONSTRAINT `habitacion_ibfk_1` FOREIGN KEY (`Id_empresa`) REFERENCES `empresa` (`id_alojamieto`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `pago`
--
ALTER TABLE `pago`
  ADD CONSTRAINT `fk_pago_reserva` FOREIGN KEY (`Id_reserva`) REFERENCES `reserva` (`Id_reserva`);

--
-- Filtros para la tabla `persona`
--
ALTER TABLE `persona`
  ADD CONSTRAINT `persona_ibfk_1` FOREIGN KEY (`Id_rol_user`) REFERENCES `rol_user` (`id_rol_user`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `persona_ibfk_2` FOREIGN KEY (`Id_descripcion`) REFERENCES `descripcion` (`id_descripcion`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `precio`
--
ALTER TABLE `precio`
  ADD CONSTRAINT `precio_ibfk_1` FOREIGN KEY (`Id_habitacion`) REFERENCES `habitacion` (`Id_habitacion`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `reserva`
--
ALTER TABLE `reserva`
  ADD CONSTRAINT `fk_reserva_user` FOREIGN KEY (`Id_user`) REFERENCES `user` (`Id_user`);

--
-- Filtros para la tabla `rol_user`
--
ALTER TABLE `rol_user`
  ADD CONSTRAINT `rol_user_ibfk_1` FOREIGN KEY (`Id_user`) REFERENCES `user` (`Id_user`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `rol_user_ibfk_2` FOREIGN KEY (`Id_rol`) REFERENCES `rol` (`Id_rol`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
