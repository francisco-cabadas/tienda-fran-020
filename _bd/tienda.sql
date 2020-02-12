-- phpMyAdmin SQL Dump
-- version 4.9.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 12-02-2020 a las 09:49:20
-- Versión del servidor: 10.4.8-MariaDB
-- Versión de PHP: 7.3.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `tienda`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cliente`
--

CREATE TABLE `cliente` (
  `id` int(11) NOT NULL,
  `email` varchar(60) COLLATE utf8_spanish_ci NOT NULL,
  `contrasenna` varchar(80) COLLATE utf8_spanish_ci NOT NULL,
  `codigoCookie` varchar(50) COLLATE utf8_spanish_ci DEFAULT NULL,
  `nombre` varchar(50) COLLATE utf8_spanish_ci NOT NULL,
  `direccion` varchar(100) COLLATE utf8_spanish_ci DEFAULT NULL,
  `telefono` varchar(20) COLLATE utf8_spanish_ci DEFAULT NULL,
  `registrado` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `cliente`
--

INSERT INTO `cliente` (`id`, `email`, `contrasenna`, `codigoCookie`, `nombre`, `direccion`, `telefono`, `registrado`) VALUES
(1, 'jlopez@gmail.com', 'j', 'J4PTyau8zvK1EfrwLANAn7dT81tWRtyl', 'José', 'Calle Álvaro de Bazán, 16', NULL, 0),
(2, 'mgarcia@gmail.com', 'm', NULL, 'María', 'Calle Sánchez Morate, 10', NULL, 0),
(3, 'jfernandez@gmail.com', '1234', 'MPkkCyPr5filHF0VuWilH4v7J6zJuupy', 'Juanito', 'Calle del Tejo, 20', NULL, 0),
(4, 'cliente@gmail.com', '1234', NULL, 'Alfonso', 'Avda. Real de Pinto', '621907133', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `linea`
--

CREATE TABLE `linea` (
  `pedido_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `unidades` int(11) NOT NULL,
  `precioUnitario` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `linea`
--

INSERT INTO `linea` (`pedido_id`, `producto_id`, `unidades`, `precioUnitario`) VALUES
(1, 1, 3, NULL),
(1, 2, 1, NULL),
(1, 3, 3, NULL),
(1, 10, 5, NULL),
(2, 9, 2, NULL),
(2, 17, 1, NULL),
(3, 5, 6, NULL),
(3, 7, 8, NULL),
(3, 8, 1, NULL),
(3, 12, 1, NULL),
(4, 16, 1, NULL),
(4, 19, 1, NULL),
(4, 20, 2, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedido`
--

CREATE TABLE `pedido` (
  `id` int(11) NOT NULL,
  `cliente_id` int(11) NOT NULL,
  `direccionEnvio` varchar(100) COLLATE utf8_spanish_ci DEFAULT NULL,
  `fechaConfirmacion` datetime DEFAULT NULL,
  `codigo_pedido` varchar(8) COLLATE utf8_spanish_ci NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `pedido`
--

INSERT INTO `pedido` (`id`, `cliente_id`, `direccionEnvio`, `fechaConfirmacion`, `codigo_pedido`) VALUES
(1, 1, 'Calle Gabriel y Galán, 3, Getafe, Madrid', '2020-02-03 14:25:20', '12b30A9d'),
(2, 2, 'Calle Arquitectos, 14, Getafe, Madrid', '2019-11-08 21:51:43', '0'),
(3, 3, 'Calle Pedro Almodóvar, 3, Getafe, Madrid', '2020-01-30 09:13:39', '0'),
(4, 4, 'Calle los Arcos, 30, Getafe, Madrid', '2020-02-02 16:41:23', '0'),
(5, 2, 'Calle Teruel, 13, Getafe, Madrid', '2019-12-26 04:44:13', '0');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto`
--

CREATE TABLE `producto` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `descripcion` varchar(5000) COLLATE utf8_spanish_ci NOT NULL,
  `precio` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `producto`
--

INSERT INTO `producto` (`id`, `nombre`, `descripcion`, `precio`) VALUES
(1, 'Cafetera', 'Modelo 5000, con doble filtro de partículas.', '9.00'),
(2, 'Roomba', 'Sube y baja escaleras, último modelo.', '150.00'),
(3, 'Atun claro en aceite oliva', 'Pack de 6 latas', '4.00'),
(4, 'Fuet espetec extra', 'Pieza de 240gr', '2.05'),
(5, 'Tomate frito', 'Pack de 3 botes', '1.01'),
(6, 'Bacon taquitos', 'Pack 2x125gr', '1.95'),
(7, 'Macarron pasta', 'Paquete de 1kg', '0.76'),
(8, 'Nata ligera para cocinar', 'Pack de 3 botes, 200ml/unidad', '1.32'),
(9, 'Jamón cocido extra', 'Lonchas finas - Pack 2x225gr', '3.08'),
(10, 'Caldo líquido de pollo', 'Pack 2x1L', '1.64'),
(11, 'Tomate natural triturado', 'Bote de 400gr', '0.45'),
(12, 'Arroz redondo', 'Paquete de 1kg', '0.79'),
(13, 'Pan de molde', 'Blanco sin corteza - 450gr', '1.09'),
(14, 'Aceite girasol', 'Botella de 1L', '0.99'),
(15, 'Queso rallado', 'Paquete de tiras de mozzarella - 200gr', '1.20'),
(16, 'Sony a7sii', 'Cuerpo de cámara mirrorless gama ISO', '2049.50'),
(17, 'Nintendo Switch', 'Consola color Azul Neón/Rojo Neón (Modelo 2019)', '315.99'),
(18, 'Echo Dot (3.ª generación)', 'Altavoz inteligente con Alexa, tela de color antracita', '40.01'),
(19, 'Diablo X-One Gaming', 'Silla de Oficina Diseño Ergonomico Mecanismo de Inclinación Cojin Lumbar y Almohada Cuero Sintético (Negro-Rojo)', '159.99'),
(20, 'Teclado Gaming', 'TedGem Teclado Gaming, Teclado USB, Teclado Gaming PS4 LED Retroiluminado con Cable USB, Teclado para PC / Laptop / PS4 / Xbox One (Teclados Español, Negro)', '159.99');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `cliente`
--
ALTER TABLE `cliente`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `linea`
--
ALTER TABLE `linea`
  ADD PRIMARY KEY (`pedido_id`,`producto_id`),
  ADD KEY `producto_id` (`producto_id`);

--
-- Indices de la tabla `pedido`
--
ALTER TABLE `pedido`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cliente_id` (`cliente_id`);

--
-- Indices de la tabla `producto`
--
ALTER TABLE `producto`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `cliente`
--
ALTER TABLE `cliente`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `pedido`
--
ALTER TABLE `pedido`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `producto`
--
ALTER TABLE `producto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `linea`
--
ALTER TABLE `linea`
  ADD CONSTRAINT `linea_ibfk_2` FOREIGN KEY (`producto_id`) REFERENCES `producto` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `linea_ibfk_3` FOREIGN KEY (`pedido_id`) REFERENCES `pedido` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `pedido`
--
ALTER TABLE `pedido`
  ADD CONSTRAINT `pedido_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `cliente` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
