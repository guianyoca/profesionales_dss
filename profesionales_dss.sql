-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 15-04-2024 a las 17:48:30
-- Versión del servidor: 10.4.22-MariaDB
-- Versión de PHP: 8.1.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `profesionales_dss`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asistencias`
--

CREATE TABLE `asistencias` (
  `idasistencia` int(11) NOT NULL,
  `idusuario` int(11) NOT NULL,
  `fecha_ingreso` date NOT NULL,
  `hora_ingreso` time NOT NULL,
  `fecha_salida` date NOT NULL,
  `hora_salida` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `asistencias`
--

INSERT INTO `asistencias` (`idasistencia`, `idusuario`, `fecha_ingreso`, `hora_ingreso`, `fecha_salida`, `hora_salida`) VALUES
(41, 12, '2024-04-09', '11:51:30', '2024-04-09', '13:49:59'),
(42, 9, '2024-04-09', '11:53:38', '2024-04-09', '14:01:58'),
(43, 14, '2024-04-09', '12:10:13', '2024-04-09', '13:47:44'),
(44, 12, '2024-04-09', '13:03:09', '2024-04-09', '13:49:59'),
(45, 13, '2024-04-09', '14:01:19', '2024-04-09', '20:10:48'),
(46, 18, '2024-04-09', '14:30:20', '2024-04-09', '20:00:39'),
(47, 15, '2024-04-09', '14:34:15', '2024-04-09', '20:39:42'),
(48, 16, '2024-04-09', '14:56:39', '2024-04-09', '20:10:32'),
(49, 9, '2024-04-10', '07:49:52', '2024-04-10', '13:53:03'),
(50, 14, '2024-04-10', '08:00:27', '2024-04-10', '13:52:20'),
(51, 15, '2024-04-10', '14:02:04', '2024-04-10', '20:00:40'),
(52, 16, '2024-04-10', '14:57:21', '2024-04-10', '20:00:38'),
(53, 14, '2024-04-11', '07:55:20', '2024-04-11', '13:49:15'),
(54, 9, '2024-04-11', '07:57:37', '2024-04-11', '13:55:55'),
(55, 12, '2024-04-11', '08:00:07', '2024-04-11', '13:56:57'),
(57, 9, '2024-04-11', '09:01:34', '2024-04-11', '13:55:55'),
(58, 13, '2024-04-11', '13:58:13', '2024-04-11', '20:08:43'),
(59, 13, '2024-04-11', '13:58:19', '2024-04-11', '20:08:43'),
(60, 18, '2024-04-11', '14:06:59', '2024-04-11', '20:01:04'),
(61, 15, '2024-04-11', '14:10:57', '2024-04-11', '20:01:03'),
(62, 16, '2024-04-11', '14:58:26', '2024-04-11', '20:08:24'),
(63, 9, '2024-04-12', '07:50:09', '2024-04-12', '14:00:05'),
(64, 14, '2024-04-12', '08:02:32', '2024-04-12', '14:00:10'),
(65, 12, '2024-04-12', '08:07:58', '2024-04-12', '13:56:45'),
(66, 13, '2024-04-12', '14:07:17', '2024-04-12', '19:59:25'),
(67, 15, '2024-04-12', '14:11:30', '2024-04-12', '20:05:06'),
(68, 16, '2024-04-12', '15:01:45', '2024-04-12', '19:58:28'),
(69, 12, '2024-04-15', '10:16:40', '0000-00-00', '00:00:00'),
(70, 14, '2024-04-15', '10:37:46', '0000-00-00', '00:00:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuracion`
--

CREATE TABLE `configuracion` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `telefono` varchar(15) COLLATE utf8_spanish_ci NOT NULL,
  `email` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `direccion` text COLLATE utf8_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `configuracion`
--

INSERT INTO `configuracion` (`id`, `nombre`, `telefono`, `email`, `direccion`) VALUES
(1, 'Sistemas Free', '98745698', 'ana.info1999@gamil.com', 'Trujillo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_permisos`
--

CREATE TABLE `detalle_permisos` (
  `id` int(11) NOT NULL,
  `id_permiso` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historias_clinicas`
--

CREATE TABLE `historias_clinicas` (
  `idhistoria` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `dni` varchar(50) NOT NULL,
  `fecha_nacimiento` date NOT NULL,
  `telefono` varchar(50) NOT NULL,
  `texto` text NOT NULL,
  `usuario_carga` varchar(50) NOT NULL,
  `fecha_carga` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `historias_clinicas`
--

INSERT INTO `historias_clinicas` (`idhistoria`, `nombre`, `dni`, `fecha_nacimiento`, `telefono`, `texto`, `usuario_carga`, `fecha_carga`) VALUES
(3, 'GUILLERMO', '36034573', '0001-01-01', '264588', '<p><span style=\"font-size: 18pt;\">Su primera visita fue fecha 7/8/23 es <strong>POLICIA EN ACTIVIDAD&nbsp;</strong></span></p>\r\n<p><span style=\"font-size: 24pt;\"><strong><span style=\"text-decoration: underline;\">Su Diagnostico:</span> <span style=\"color: #e03e2d;\">Fractura de tibia</span></strong></span></p>\r\n<p>&nbsp;</p>\r\n<p><span style=\"font-size: 24pt;\"><strong><span style=\"color: #e03e2d;\"><span style=\"font-family: \'comic sans ms\', sans-serif;\"><span style=\"color: #ecf0f1; background-color: #2dc26b;\">Su alta fue:</span> <span style=\"color: #b96ad9;\">15/9/23????</span></span></span></strong></span></p>', '9', '2024-04-12 12:36:49'),
(5, 'GUILLERMO', '36034573', '0001-01-01', '264588', '<p><span style=\"font-size: 18pt;\">Su primera visita fue fecha 7/8/23 es <strong>POLICIA EN ACTIVIDAD&nbsp;</strong></span></p>\r\n<p><span style=\"font-size: 24pt;\"><strong><span style=\"text-decoration: underline;\">Su Diagnostico:</span> <span style=\"color: #e03e2d;\">Fractura de tibia</span></strong></span></p>\r\n<p>&nbsp;</p>\r\n<p><span style=\"font-size: 24pt;\"><strong><span style=\"color: #e03e2d;\"><span style=\"font-family: \'comic sans ms\', sans-serif;\"><span style=\"color: #ecf0f1; background-color: #2dc26b;\">Su alta fue:</span> <span style=\"color: #2dc26b;\">15/9/23????</span></span></span></strong></span></p>', '9', '2024-04-12 13:21:05'),
(6, 'Milagros Montaña', '45635165', '2004-03-11', '2645288971', '<ul>\r\n<li>Sobrepeso</li>\r\n<li>Peso: 88.5 kg.&nbsp; Altura 1.67&nbsp;</li>\r\n</ul>\r\n<p>&nbsp;</p>', '14', '2024-04-15 10:41:37'),
(7, 'Quintero Valentina', '49281059', '2009-02-02', '2644121347', '<p><strong>Edad 15 a&ntilde;os.</strong></p>\r\n<p><strong>Estado Civil: </strong>Soltera.<br>No esta de novia ni tiene ningun tipo de relacion.&nbsp;</p>\r\n<p><strong>Diagnostico actual:</strong> <em><strong>Miastenia gravis</strong></em>, donde la&nbsp;enfermedad es ocasionada por una interrupci&oacute;n en la comunicaci&oacute;n entre los nervios y los m&uacute;sculos.</p>\r\n<div class=\"m6vS6b PZPZlf\" data-attrid=\"kc:/medicine/disease:long description\">Los s&iacute;ntomas incluyen debilidad en los m&uacute;sculos de los brazos y las piernas, visi&oacute;n doble y dificultades para hablar y masticar.</div>\r\n<div class=\"m6vS6b PZPZlf\" data-attrid=\"kc:/medicine/disease:long description\">&nbsp;</div>\r\n<div class=\"m6vS6b PZPZlf\" data-attrid=\"kc:/medicine/disease:long description\">La enfermedad fue diagnosticada en marzo del 2022.&nbsp;</div>\r\n<div class=\"m6vS6b PZPZlf\" data-attrid=\"kc:/medicine/disease:long description\">&nbsp;</div>\r\n<div class=\"m6vS6b PZPZlf\" data-attrid=\"kc:/medicine/disease:long description\">Comenzo tratamiento psicologico en abril del 2024 teniendo en cuenta que a principio de a&ntilde;o tuvo una crisis de la enfermedad por lo cual se recomendo terapia psicologica para acompa&ntilde;ar psiquicamente y emocionalmente a la paciente con el proceso de su enfermedad.&nbsp;</div>\r\n<div class=\"m6vS6b PZPZlf\" data-attrid=\"kc:/medicine/disease:long description\">&nbsp;</div>\r\n<div class=\"m6vS6b PZPZlf\" data-attrid=\"kc:/medicine/disease:long description\">No puede realizar actividad fisica por el momento.&nbsp;</div>\r\n<div class=\"m6vS6b PZPZlf\" data-attrid=\"kc:/medicine/disease:long description\">&nbsp;</div>\r\n<div class=\"m6vS6b PZPZlf\" data-attrid=\"kc:/medicine/disease:long description\"><strong>Convive: </strong>Padres y 2 hermanas, es la menor de la familia.</div>\r\n<div class=\"m6vS6b PZPZlf\" data-attrid=\"kc:/medicine/disease:long description\">El padre es retirado de la policia, la mam&aacute; ama de casa.&nbsp;</div>\r\n<div class=\"m6vS6b PZPZlf\" data-attrid=\"kc:/medicine/disease:long description\">&nbsp;</div>\r\n<div class=\"m6vS6b PZPZlf\" data-attrid=\"kc:/medicine/disease:long description\">&nbsp;</div>\r\n<p>&nbsp;</p>', '12', '2024-04-15 11:46:48');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pacientes`
--

CREATE TABLE `pacientes` (
  `idpaciente` int(11) NOT NULL,
  `nombre` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `dni` varchar(15) COLLATE utf8_spanish_ci NOT NULL,
  `fecha` date NOT NULL DEFAULT current_timestamp(),
  `hora` time NOT NULL DEFAULT current_timestamp(),
  `usuario_carga` varchar(100) COLLATE utf8_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `pacientes`
--

INSERT INTO `pacientes` (`idpaciente`, `nombre`, `dni`, `fecha`, `hora`, `usuario_carga`) VALUES
(14, 'Tapia Maximiliano', '30371808', '2024-04-09', '12:08:25', '9'),
(17, 'Olivera Julio ', '176500424', '2024-04-09', '12:34:43', '14'),
(18, 'Recabarren Isabel', '14417485', '2024-04-09', '12:37:49', '14'),
(19, 'Jofre Delfina', '47582985', '2024-04-09', '12:38:20', '14'),
(20, 'Ligorria Julieta', '55440726', '2024-04-09', '13:07:09', '9'),
(21, 'Quintero Valentina', '49281059', '2024-04-09', '13:08:31', '12'),
(22, 'RODRIGUEZ GONZALO ', '37742885', '2024-04-09', '14:04:44', '13'),
(24, 'PEREZ CATALINA', '5283924', '2024-04-09', '14:38:38', '13'),
(25, 'OROPEL MARTIN ', '25649577', '2024-04-09', '14:50:55', '13'),
(26, 'Guzman Adrian ', '34818269', '2024-04-09', '14:52:44', '18'),
(27, 'Villegas Ruben ', '32750440', '2024-04-09', '15:26:46', '18'),
(28, 'FLORES FRANCO', '38218273', '2024-04-09', '15:28:21', '16'),
(29, 'Molina Felipe', '7936768', '2024-04-09', '15:54:24', '13'),
(30, 'ALVAREZ ROSA', '92462499', '2024-04-09', '15:54:31', '16'),
(31, 'AGUADO CARLOS ', '10030906', '2024-04-09', '17:00:38', '13'),
(32, 'CASTRO DARIO', '29690709', '2024-04-09', '17:00:54', '16'),
(33, 'MENSEGUEZ DAVID', '23057206', '2024-04-09', '17:01:04', '13'),
(34, 'ZARZUELO CARLA ', '37647918', '2024-04-09', '17:09:09', '13'),
(35, 'Aguirre Marisa', '28967888', '2024-04-09', '17:10:42', '18'),
(36, 'AHUMADA MIGUEL', '36251753', '2024-04-09', '17:19:24', '16'),
(37, 'Bondanza Maira', '33759176', '2024-04-09', '17:48:38', '18'),
(38, 'CRISTINA VIVARES', '12621137', '2024-04-09', '17:58:56', '13'),
(39, 'POLIZOTO MATEO', '53552438', '2024-04-09', '18:41:58', '13'),
(40, 'GONZALEZ PATRICIA', '21357888', '2024-04-09', '18:43:23', '13'),
(41, 'VERA, WALTER ALFREDO', '26790184', '2024-04-09', '20:35:21', '15'),
(42, 'TRIGO, PRISCILLA BELEN', '43281347', '2024-04-09', '20:36:27', '15'),
(43, 'SERVANT, MARTINA', '46933018', '2024-04-09', '20:37:28', '15'),
(44, 'COSTA, JUAN RAMON', '35922407', '2024-04-09', '20:38:08', '15'),
(45, 'TOMÁS, VANESA SOLEDAD', '32750553', '2024-04-09', '20:39:22', '15'),
(46, 'caballero Claudia', '22358924', '2024-04-10', '08:57:26', '9'),
(47, 'Castro Gabriela', '27043834', '2024-04-10', '08:58:28', '9'),
(48, 'Ibaceta Daniela', '26790279', '2024-04-10', '08:59:18', '9'),
(49, 'Vega Walter ', '38590442', '2024-04-10', '08:59:46', '9'),
(50, 'Reta Jorge', '16183463', '2024-04-10', '09:32:13', '9'),
(51, 'Bazan Antonio', '10684461', '2024-04-10', '09:32:35', '9'),
(52, 'Herrera Eves', '6039994', '2024-04-10', '09:48:54', '9'),
(53, 'Tejada Mariano', '31523698', '2024-04-10', '10:24:03', '9'),
(54, 'Torres Pablo', '20897741', '2024-04-10', '11:06:53', '9'),
(55, 'Martinez Ana ', '20655165', '2024-04-10', '11:23:14', '9'),
(56, 'Tapia Maximiliano', '30371808', '2024-04-10', '11:28:35', '9'),
(57, 'Torres Carlos', '21742777', '2024-04-10', '12:10:19', '14'),
(58, 'Gonzales Priscila', '35851376', '2024-04-10', '12:25:55', '9'),
(59, 'ligorria Julieta', '55440726', '2024-04-10', '12:55:29', '9'),
(60, 'VILLAFAÑE FERNANDA', '24971755', '2024-04-10', '15:17:35', '16'),
(61, 'MARTIN MIGUEL', '18665160', '2024-04-10', '15:52:34', '16'),
(62, 'MENSEGUEZ DARIO', '23057206', '2024-04-10', '16:17:27', '16'),
(63, 'FLORES FRANCO', '38218273', '2024-04-10', '16:26:24', '16'),
(64, 'ESPEJO MIRIAM', '16384438', '2024-04-10', '16:46:31', '16'),
(65, 'MALLEA MARCOS', '22064473', '2024-04-10', '17:00:49', '16'),
(66, 'MERCADO VIVIANA', '23735154', '2024-04-10', '17:01:20', '16'),
(67, 'ZARZUELO AGUSTIN', '43340253', '2024-04-10', '19:52:04', '16'),
(68, 'COSTA, FLORENCIA LORENA', '37646693', '2024-04-10', '19:54:52', '15'),
(69, 'CABELLO LUCAS EMILIANO', '34915840', '2024-04-10', '19:55:57', '15'),
(70, 'MORALES JOSUÉ', '35510584', '2024-04-10', '19:56:59', '15'),
(71, 'MORENO NATALIA', '29911923', '2024-04-10', '19:58:22', '15'),
(72, 'Morales Juan Pablo', '31366435', '2024-04-11', '08:23:51', '14'),
(73, 'Caballero Claudia', '22358924', '2024-04-11', '08:25:59', '9'),
(74, 'Ibaceta Daniela', '26790279', '2024-04-11', '09:02:21', '9'),
(75, 'Castro Gacriela', '27043834', '2024-04-11', '09:02:50', '9'),
(76, 'Alamo Eva', '5745485', '2024-04-11', '09:36:23', '9'),
(77, 'Osores Mabel', '11296524', '2024-04-11', '09:36:57', '9'),
(78, 'Urbano Ruben', '25939725', '2024-04-11', '09:37:33', '12'),
(79, 'Mercado Nancy', '20130355', '2024-04-11', '09:48:38', '14'),
(80, 'Alamo Eva ', '5745585', '2024-04-11', '10:19:56', '14'),
(81, 'Funes Rocio', '46258849', '2024-04-11', '10:26:11', '12'),
(82, 'Tejada Mariana', '31523698', '2024-04-11', '10:29:23', '9'),
(83, 'Torres Pablo', '20897741', '2024-04-11', '10:30:57', '9'),
(84, 'Vaga Walter', '38590442', '2024-04-11', '10:31:23', '9'),
(85, 'Carolina Muggiani', '26678703', '2024-04-11', '11:06:36', '12'),
(86, 'Tejada Maira ', '46803801', '2024-04-11', '11:19:07', '14'),
(87, 'Montaña Jorge', '10999679', '2024-04-11', '11:23:55', '9'),
(88, 'Martinez Ana', '20655165', '2024-04-11', '11:24:28', '9'),
(89, 'Herrera Eves', '6039994', '2024-04-11', '11:25:27', '9'),
(90, 'Tapia Maximiliano', '30371808', '2024-04-11', '11:43:11', '9'),
(91, 'Jose Aguirre', '35736970', '2024-04-11', '11:56:44', '12'),
(92, 'Cespedes Julieta', '44766737', '2024-04-11', '12:35:21', '12');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permisos`
--

CREATE TABLE `permisos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `permisos`
--

INSERT INTO `permisos` (`id`, `nombre`) VALUES
(1, 'configuración'),
(2, 'usuarios'),
(3, 'clientes'),
(4, 'productos'),
(5, 'ventas'),
(6, 'nueva_venta'),
(7, 'tipos'),
(8, 'presentacion'),
(9, 'laboratorios');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `turnos`
--

CREATE TABLE `turnos` (
  `idturno` int(11) NOT NULL,
  `nombre` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `dni` varchar(15) COLLATE utf8_spanish_ci NOT NULL,
  `fecha` date NOT NULL DEFAULT current_timestamp(),
  `hora` time NOT NULL DEFAULT current_timestamp(),
  `estado` int(10) NOT NULL DEFAULT 0 COMMENT '0=Espera\r\n1=Presente\r\n2=Ausente',
  `usuario_carga` varchar(100) COLLATE utf8_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `turnos`
--

INSERT INTO `turnos` (`idturno`, `nombre`, `dni`, `fecha`, `hora`, `estado`, `usuario_carga`) VALUES
(14, 'Tapia Maximiliano', '30371808', '2024-04-09', '12:08:25', 1, '9'),
(17, 'Olivera Julio ', '176500424', '2024-04-09', '12:34:43', 1, '14'),
(18, 'Recabarren Isabel', '14417485', '2024-04-09', '12:37:49', 1, '14'),
(19, 'Jofre Delfina', '47582985', '2024-04-09', '12:38:20', 1, '14'),
(20, 'Ligorria Julieta', '55440726', '2024-04-09', '13:07:09', 1, '9'),
(21, 'Quintero Valentina', '49281059', '2024-04-09', '13:08:31', 1, '12'),
(22, 'RODRIGUEZ GONZALO ', '37742885', '2024-04-09', '14:04:44', 1, '13'),
(24, 'PEREZ CATALINA', '5283924', '2024-04-09', '14:38:38', 1, '13'),
(25, 'OROPEL MARTIN ', '25649577', '2024-04-09', '14:50:55', 1, '13'),
(26, 'Guzman Adrian ', '34818269', '2024-04-09', '14:52:44', 1, '18'),
(27, 'Villegas Ruben ', '32750440', '2024-04-09', '15:26:46', 1, '18'),
(28, 'FLORES FRANCO', '38218273', '2024-04-09', '15:28:21', 1, '16'),
(29, 'Molina Felipe', '7936768', '2024-04-09', '15:54:24', 1, '13'),
(30, 'ALVAREZ ROSA', '92462499', '2024-04-09', '15:54:31', 1, '16'),
(31, 'AGUADO CARLOS ', '10030906', '2024-04-09', '17:00:38', 1, '13'),
(32, 'CASTRO DARIO', '29690709', '2024-04-09', '17:00:54', 1, '16'),
(33, 'MENSEGUEZ DAVID', '23057206', '2024-04-09', '17:01:04', 1, '13'),
(34, 'ZARZUELO CARLA ', '37647918', '2024-04-09', '17:09:09', 1, '13'),
(35, 'Aguirre Marisa', '28967888', '2024-04-09', '17:10:42', 1, '18'),
(36, 'AHUMADA MIGUEL', '36251753', '2024-04-09', '17:19:24', 1, '16'),
(37, 'Bondanza Maira', '33759176', '2024-04-09', '17:48:38', 1, '18'),
(38, 'CRISTINA VIVARES', '12621137', '2024-04-09', '17:58:56', 1, '13'),
(39, 'POLIZOTO MATEO', '53552438', '2024-04-09', '18:41:58', 1, '13'),
(40, 'GONZALEZ PATRICIA', '21357888', '2024-04-09', '18:43:23', 1, '13'),
(41, 'VERA, WALTER ALFREDO', '26790184', '2024-04-09', '20:35:21', 1, '15'),
(42, 'TRIGO, PRISCILLA BELEN', '43281347', '2024-04-09', '20:36:27', 1, '15'),
(43, 'SERVANT, MARTINA', '46933018', '2024-04-09', '20:37:28', 1, '15'),
(44, 'COSTA, JUAN RAMON', '35922407', '2024-04-09', '20:38:08', 1, '15'),
(45, 'TOMÁS, VANESA SOLEDAD', '32750553', '2024-04-09', '20:39:22', 1, '15'),
(46, 'caballero Claudia', '22358924', '2024-04-10', '08:57:26', 1, '9'),
(47, 'Castro Gabriela', '27043834', '2024-04-10', '08:58:28', 1, '9'),
(48, 'Ibaceta Daniela', '26790279', '2024-04-10', '08:59:18', 1, '9'),
(49, 'Vega Walter ', '38590442', '2024-04-10', '08:59:46', 1, '9'),
(50, 'Reta Jorge', '16183463', '2024-04-10', '09:32:13', 1, '9'),
(51, 'Bazan Antonio', '10684461', '2024-04-10', '09:32:35', 1, '9'),
(52, 'Herrera Eves', '6039994', '2024-04-10', '09:48:54', 1, '9'),
(53, 'Tejada Mariano', '31523698', '2024-04-10', '10:24:03', 1, '9'),
(54, 'Torres Pablo', '20897741', '2024-04-10', '11:06:53', 1, '9'),
(55, 'Martinez Ana ', '20655165', '2024-04-10', '11:23:14', 1, '9'),
(56, 'Tapia Maximiliano', '30371808', '2024-04-10', '11:28:35', 1, '9'),
(57, 'Torres Carlos', '21742777', '2024-04-10', '12:10:19', 1, '14'),
(58, 'Gonzales Priscila', '35851376', '2024-04-10', '12:25:55', 1, '9'),
(59, 'ligorria Julieta', '55440726', '2024-04-10', '12:55:29', 1, '9'),
(60, 'VILLAFAÑE FERNANDA', '24971755', '2024-04-10', '15:17:35', 1, '16'),
(61, 'MARTIN MIGUEL', '18665160', '2024-04-10', '15:52:34', 1, '16'),
(62, 'MENSEGUEZ DARIO', '23057206', '2024-04-10', '16:17:27', 1, '16'),
(63, 'FLORES FRANCO', '38218273', '2024-04-10', '16:26:24', 1, '16'),
(64, 'ESPEJO MIRIAM', '16384438', '2024-04-10', '16:46:31', 1, '16'),
(65, 'MALLEA MARCOS', '22064473', '2024-04-10', '17:00:49', 1, '16'),
(66, 'MERCADO VIVIANA', '23735154', '2024-04-10', '17:01:20', 1, '16'),
(67, 'ZARZUELO AGUSTIN', '43340253', '2024-04-10', '19:52:04', 1, '16'),
(68, 'COSTA, FLORENCIA LORENA', '37646693', '2024-04-10', '19:54:52', 1, '15'),
(69, 'CABELLO LUCAS EMILIANO', '34915840', '2024-04-10', '19:55:57', 1, '15'),
(70, 'MORALES JOSUÉ', '35510584', '2024-04-10', '19:56:59', 1, '15'),
(71, 'MORENO NATALIA', '29911923', '2024-04-10', '19:58:22', 1, '15'),
(72, 'Morales Juan Pablo', '31366435', '2024-04-11', '08:23:51', 1, '14'),
(73, 'Caballero Claudia', '22358924', '2024-04-11', '08:25:59', 1, '9'),
(74, 'Ibaceta Daniela', '26790279', '2024-04-11', '09:02:21', 1, '9'),
(75, 'Castro Gacriela', '27043834', '2024-04-11', '09:02:50', 1, '9'),
(76, 'Alamo Eva', '5745485', '2024-04-11', '09:36:23', 1, '9'),
(77, 'Osores Mabel', '11296524', '2024-04-11', '09:36:57', 1, '9'),
(78, 'Urbano Ruben', '25939725', '2024-04-11', '09:37:33', 1, '12'),
(79, 'Mercado Nancy', '20130355', '2024-04-11', '09:48:38', 1, '14'),
(80, 'Alamo Eva ', '5745585', '2024-04-11', '10:19:56', 1, '14'),
(81, 'Funes Rocio', '46258849', '2024-04-11', '10:26:11', 1, '12'),
(82, 'Tejada Mariana', '31523698', '2024-04-11', '10:29:23', 1, '9'),
(83, 'Torres Pablo', '20897741', '2024-04-11', '10:30:57', 1, '9'),
(84, 'Vaga Walter', '38590442', '2024-04-11', '10:31:23', 1, '9'),
(85, 'Carolina Muggiani', '26678703', '2024-04-11', '11:06:36', 1, '12'),
(86, 'Tejada Maira ', '46803801', '2024-04-11', '11:19:07', 1, '14'),
(87, 'Montaña Jorge', '10999679', '2024-04-11', '11:23:55', 1, '9'),
(88, 'Martinez Ana', '20655165', '2024-04-11', '11:24:28', 1, '9'),
(89, 'Herrera Eves', '6039994', '2024-04-11', '11:25:27', 1, '9'),
(90, 'Tapia Maximiliano', '30371808', '2024-04-11', '11:43:11', 1, '9'),
(91, 'Jose Aguirre', '35736970', '2024-04-11', '11:56:44', 1, '12'),
(92, 'Gonzalez Hector', '11567505', '2024-04-11', '12:00:00', 2, 'Mariana Lezcano'),
(93, 'Sanchez Nancy ', '12893257', '2024-04-11', '08:00:00', 2, 'Mariana Lezcano'),
(94, 'Illanes Agustina', '44991415', '2024-04-11', '11:00:00', 2, 'Mariana Lezcano'),
(95, 'Tejada Mariano', '31523698', '2024-04-11', '10:00:00', 1, 'Mariana Lezcano'),
(96, 'Alejandro Lopez', '28538742', '2024-04-11', '12:00:00', 2, 'Mariana Lezcano'),
(97, 'Recabarren Isabela', '14417485', '2024-04-11', '10:00:00', 1, 'Mariana Lezcano'),
(98, 'Guerra Lourdes', '50347430', '2024-04-11', '12:00:00', 2, 'Paula Lescano'),
(99, 'Andino Fabián ', '25118755', '2024-04-11', '13:00:00', 2, 'Paula Lescano'),
(100, 'Ligorria Julieta', '55440726', '2024-04-11', '13:00:00', 1, 'Mariana Lezcano'),
(101, 'Ligorria Julieta', '55440726', '2024-04-11', '13:00:00', 1, 'Mariana Lezcano'),
(102, 'Bazan Antonio', '10684461', '2024-04-11', '10:00:00', 1, 'Mariana Lezcano'),
(103, 'Martinez Ana', '20655165', '2024-04-11', '11:00:00', 1, 'Mariana Lezcano'),
(104, 'Herrera Eves', '6039994', '2024-04-11', '09:00:00', 1, 'Mariana Lezcano'),
(105, 'Osores Mabel', '11295524', '2024-04-11', '09:00:00', 1, 'Mariana Lezcano'),
(106, 'Gonzalez Priscila', '35851376', '2024-04-11', '08:00:00', 1, 'Mariana Lezcano'),
(107, 'Martinez Victoria', '24276604', '2024-04-11', '08:00:00', 2, 'Mariana Lezcano'),
(108, 'Reta Jorge', '16183403', '2024-04-11', '13:00:00', 2, 'Mariana Lezcano'),
(109, 'Adaro Jose', '12069909', '2024-04-11', '09:00:00', 2, 'Mariana Lezcano'),
(110, 'Alamo Eva', '5745585', '2024-04-11', '09:00:00', 1, 'Mariana Lezcano'),
(111, 'Torres Pablo', '20897741', '2024-04-11', '09:00:00', 1, 'Mariana Lezcano'),
(112, 'Caballero Claudia', '22358924', '2024-04-11', '08:00:00', 1, 'Mariana Lezcano'),
(113, 'Vega Walter', '38590442', '2024-04-11', '10:00:00', 1, 'Mariana Lezcano'),
(114, 'Vildoso Patricia', '22659790', '2024-04-11', '10:00:00', 2, 'Mariana Lezcano'),
(116, 'guillermo', '3603473', '2024-04-11', '14:00:00', 2, 'Candela Pelaez'),
(117, 'Cespedes Julieta ', '44766737', '2024-04-11', '13:00:00', 1, 'Candela Pelaez'),
(118, 'Guajardo Rodrigo Jesus', '34917107', '2024-04-12', '09:00:00', 1, 'Candela Pelaez'),
(119, 'Herrera Nancy', '25319811', '2024-04-12', '10:00:00', 1, 'Candela Pelaez'),
(120, 'Trigo Eithan', '55364452', '2024-04-12', '11:00:00', 2, 'Candela Pelaez'),
(121, 'Tejada Mayra', '46803801', '2024-04-12', '11:40:00', 1, 'Candela Pelaez'),
(122, 'Paez Guadalupe', '47891626', '2024-04-12', '12:45:00', 1, 'Candela Pelaez'),
(123, 'RAUL ALGAÑARAZ', '23735743', '2024-04-11', '14:12:00', 1, 'Silvana Merino'),
(124, 'SORIA ANA PAULA', '46259776', '2024-04-11', '14:05:00', 1, 'Erica Garrido'),
(125, 'RODRIGUEZ GONZALEZ', '37742885', '2024-04-11', '14:10:00', 1, 'Erica Garrido'),
(126, 'BURGOA CLAUDIO', '36254577', '2024-04-11', '14:24:00', 1, 'Erica Garrido'),
(127, 'OROPEL MARTIN ', '25649577', '2024-04-11', '14:00:00', 1, 'Erica Garrido'),
(128, 'OYOLA HECTOR ', '22011692', '2024-04-11', '14:00:00', 2, 'Erica Garrido'),
(129, 'ROMERO CARLOS ', '35852707', '2024-04-11', '15:00:00', 1, 'Silvana Merino'),
(130, 'AGUADO CARLOS', '10030906', '2024-04-11', '16:00:00', 0, 'Erica Garrido'),
(131, 'AVILA ISABEL', '20131255', '2024-04-11', '15:30:00', 1, 'Silvana Merino'),
(132, 'BRAVO JORGE ', '38082312', '2024-04-11', '15:07:00', 0, 'Erica Garrido'),
(133, 'CHANKAY IRENE', '28492256', '2024-04-11', '16:00:00', 2, 'Silvana Merino'),
(134, 'MENSEGUEZ DAVID ', '23057206', '2024-04-11', '16:00:00', 2, 'Erica Garrido'),
(135, 'ESPEJO MIRIAM ', '16384438', '2024-04-11', '17:00:00', 0, 'Erica Garrido'),
(136, 'TEJADA KEILA', '46726300', '2024-04-11', '17:00:00', 1, 'Silvana Merino'),
(137, 'GALLARDO VANESA', '37507519', '2024-04-11', '17:30:00', 2, 'Silvana Merino'),
(138, 'VILLAFAÑE FERNANDA', '-24971755', '2024-04-11', '15:24:00', 2, 'Araceli Tirado'),
(139, 'PEREZ CATALINA', '5283924', '2024-04-11', '15:00:00', 2, 'Araceli Tirado'),
(140, 'ALVAREZ ROSA', '32462499', '2024-04-11', '15:00:00', 1, 'Araceli Tirado'),
(141, 'FLORES FRANCO', '38218273', '2024-04-11', '15:00:00', 1, 'Araceli Tirado'),
(142, 'MALLEA MARCOS', '22064473', '2024-04-11', '15:00:00', 2, 'Araceli Tirado'),
(143, 'ZARZUELO AGUSTIN', '43340253', '2024-04-11', '15:00:00', 1, 'Araceli Tirado'),
(144, 'RAMOS ESTELA', '10530809', '2024-04-11', '15:00:00', 1, 'Araceli Tirado'),
(145, 'CASTRO DARIO', '29690709', '2024-04-11', '17:00:00', 1, 'Araceli Tirado'),
(146, 'MOLINA FELIPE', '7936768', '2024-04-11', '17:00:00', 2, 'Erica Garrido'),
(147, 'GONZALEZ PATRICIA ', '21357888', '2024-04-11', '17:00:00', 2, 'Erica Garrido'),
(148, 'ZARZUELO CARLA', '37647918', '2024-04-11', '17:00:00', 2, 'Erica Garrido'),
(149, 'POLIZOTO MATEO', '53552438', '2024-04-11', '18:00:00', 2, 'Erica Garrido'),
(150, 'QUINTERO EMILIANO', '35148338', '2024-04-09', '14:00:00', 0, 'Silvana Merino'),
(151, 'LOPEZ MARIO', '11519056', '2024-04-11', '18:00:00', 1, 'Erica Garrido'),
(152, 'VEDOYA MONICA', '23977010', '2024-04-11', '18:00:00', 1, 'Silvana Merino'),
(153, 'AHUMADA MIGUEL', '36251753', '2024-04-11', '17:00:00', 1, 'Araceli Tirado'),
(154, 'AHUMADA MIGUEL', '36251753', '2024-04-11', '17:00:00', 1, 'Araceli Tirado'),
(155, 'FURINI FRANCO', '39376985', '2024-04-11', '14:00:00', 1, 'Santiago Salinas'),
(156, 'RIVEROS LUNA MARCOS GERMAN', '48596701', '2024-04-11', '15:20:00', 1, 'Santiago Salinas'),
(157, 'MORENO ESTELA', '6555260', '2024-04-11', '16:30:00', 1, 'Santiago Salinas'),
(158, 'VALLEJO, NORA', '13608021', '2024-04-11', '17:30:00', 2, 'Santiago Salinas'),
(159, 'ALANIZ VERONICA', '33614086', '2024-04-11', '18:30:00', 2, 'Santiago Salinas'),
(160, 'VILLAFAÑE CRISTIAN ', '27617304', '2024-04-11', '18:49:00', 1, 'Silvana Merino'),
(161, 'ABALLAY FABIAN OSCAR', '34195135', '2024-04-11', '19:00:00', 1, 'Santiago Salinas'),
(162, 'Cballero Claudia', '22358924', '2024-04-12', '07:58:00', 1, 'Mariana Lezcano'),
(163, 'Rivero Gonzalo', '46806338', '2024-04-12', '08:30:00', 2, 'Paula Lescano'),
(164, 'Riveros Enzo', '50735412', '2024-04-12', '09:00:00', 2, 'Paula Lescano'),
(165, 'Peralta Viviana', '25936074', '2024-04-12', '09:00:00', 2, 'Paula Lescano'),
(166, 'Frias Delfina', '47891470', '2024-04-12', '13:00:00', 1, 'Paula Lescano'),
(167, 'Castro Gabriela', '27043834', '2024-04-12', '09:21:00', 1, 'Mariana Lezcano'),
(168, 'Carolina Mugnani', '26698703', '2024-04-12', '11:00:00', 1, 'Paula Lescano'),
(169, 'Herrera eves', '6039994', '2024-04-12', '10:02:00', 1, 'Mariana Lezcano'),
(170, 'Vildoso Patricia', '22659790', '2024-04-12', '10:03:00', 1, 'Mariana Lezcano'),
(171, 'Vildoso Patricia', '22659790', '2024-04-12', '10:03:00', 1, 'Mariana Lezcano'),
(172, 'Bazan Antonio', '10684461', '2024-04-12', '10:04:00', 1, 'Mariana Lezcano'),
(173, 'Torres Pablo', '31523698', '2024-04-12', '10:11:00', 1, 'Mariana Lezcano'),
(174, 'Martinez Ana', '20655165', '2024-04-12', '11:33:00', 1, 'Mariana Lezcano'),
(175, 'Tapia maximiliano', '30371808', '2024-04-12', '10:37:00', 1, 'Mariana Lezcano'),
(176, 'Sergio Quiroga ', '20132823', '2024-04-12', '11:30:00', 1, 'Paula Lescano'),
(177, 'SAnchez Fernanda', '45636759', '2024-04-15', '10:00:00', 2, 'Candela Pelaez'),
(178, 'Montaña Milagros', '45635165', '2024-04-15', '11:00:00', 1, 'Candela Pelaez'),
(179, 'Reyes Umma', '51004936', '2024-04-15', '12:00:00', 0, 'Candela Pelaez'),
(180, 'Carrizo Nahiara', '55137134', '2024-04-15', '12:45:00', 0, 'Candela Pelaez'),
(181, 'Tejada Mariano', '31523698', '2024-04-12', '10:21:00', 1, 'Mariana Lezcano'),
(182, 'Andrea Muggiani', '26678703', '2024-04-12', '12:35:00', 1, 'Paula Lescano'),
(183, 'Ligorria Julieta', '55440726', '2024-04-12', '13:02:00', 1, 'Mariana Lezcano'),
(184, 'Miliana Gonzalez', '16115592', '2024-04-15', '09:00:00', 2, 'Candela Pelaez'),
(186, 'BURGOA CLAUDIO', '36254877', '2024-04-12', '14:00:00', 1, 'Erica Garrido'),
(187, 'RODRIGUEZ GONZALO', '37742885', '2024-04-12', '14:01:00', 1, 'Erica Garrido'),
(188, 'SORIA ANA', '46259776', '2024-04-12', '14:00:00', 1, 'Erica Garrido'),
(189, 'MERCADO, VIVIANA', '23735154', '2024-04-12', '17:00:00', 2, 'Araceli Tirado'),
(190, 'VILLAFAÑE, FERNANDA', '24971755', '2024-04-12', '16:00:00', 2, 'Araceli Tirado'),
(191, 'ALVAREZ, ROSA', '92462499', '2024-04-12', '15:00:00', 2, 'Araceli Tirado'),
(192, 'FLORES, FRANCO', '38218273', '2024-04-12', '15:00:00', 2, 'Araceli Tirado'),
(193, 'MARTIN,MIGUEL', '18665160', '2024-04-12', '16:00:00', 2, 'Araceli Tirado'),
(194, 'MALLEA, MARCOS', '22064473', '2024-04-12', '17:00:00', 2, 'Araceli Tirado'),
(195, 'VAZQUES ALEJANDRO', '28263010', '2024-04-12', '14:20:00', 1, 'Santiago Salinas'),
(196, 'VIDAL EMILIA MACARENA', '44317174', '2024-04-12', '15:30:00', 1, 'Santiago Salinas'),
(197, 'LUNA JOHANA NOEMI', '36250198', '2024-04-12', '16:40:00', 2, 'Santiago Salinas'),
(198, 'RODRIGUEZ, MIGUEL', '36251753', '2024-04-12', '17:00:00', 1, 'Araceli Tirado'),
(199, 'ZARZUELO, AGUSTIN', '43340253', '2024-04-12', '19:00:00', 1, 'Araceli Tirado'),
(200, 'RAMOS, ESTELA', '10530809', '2024-04-12', '19:00:00', 0, 'Araceli Tirado'),
(201, 'OROPEL MARTIN ', '25649577', '2024-04-12', '15:03:00', 2, 'Erica Garrido'),
(202, 'AGUADO CARLOS', '10030906', '2024-04-12', '16:01:00', 1, 'Erica Garrido'),
(203, 'MENSEGUEZ DAVID', '23057206', '2024-04-12', '16:00:00', 2, 'Erica Garrido'),
(204, 'ESPEJO MIRIAM ', '16384438', '2024-04-12', '17:01:00', 2, 'Erica Garrido'),
(205, 'MOLINA FELIPE', '7936768', '2024-04-12', '17:00:00', 1, 'Erica Garrido'),
(206, 'POLIZOTO MATEO', '53552438', '2024-04-12', '18:00:00', 2, 'Erica Garrido'),
(207, 'ZARZUELO CARLA ', '37647918', '2024-04-12', '17:00:00', 2, 'Erica Garrido'),
(208, 'GONZALEZ PATRICIA ', '21357888', '2024-04-12', '18:01:00', 1, 'Erica Garrido'),
(209, 'CASTRO DARIO', '46484663', '2024-04-12', '17:00:00', 2, 'Erica Garrido'),
(210, 'AVILA NICOLAS', '18813444', '2024-04-12', '15:03:00', 2, 'Erica Garrido'),
(211, 'BRAVO JORGE ', '38082312', '2024-04-12', '14:15:00', 2, 'Erica Garrido'),
(212, 'LOPÈZ MARIO ALBERTO', '11519056', '2024-04-12', '18:15:00', 1, 'Santiago Salinas'),
(213, 'NUÑEZ KARINA MAGDALENA', '34916387', '2024-04-12', '19:00:00', 1, 'Santiago Salinas'),
(214, 'Guajardo Rodrigo Jesus', '34917107', '2024-04-16', '08:30:00', 0, 'Candela Pelaez'),
(215, 'Ruarte Alicia', '16643163', '2024-04-16', '09:30:00', 0, 'Candela Pelaez'),
(216, 'Jofre Delfina', '47285793', '2024-04-16', '10:30:00', 0, 'Candela Pelaez'),
(217, 'Aroyo Valentino', '49849090', '2024-04-16', '12:00:00', 0, 'Candela Pelaez'),
(218, 'Quintero Valentina', '49281059', '2024-04-16', '13:00:00', 0, 'Candela Pelaez'),
(219, 'Molla Analia ', '22098221', '2024-04-17', '08:15:00', 0, 'Candela Pelaez'),
(220, 'Perez Tamara', '39011476', '2024-04-17', '09:15:00', 0, 'Candela Pelaez'),
(221, 'Arguello Mia ', '46806102', '2024-04-17', '10:30:00', 0, 'Candela Pelaez'),
(222, 'Elizondo Sofia ', '55828135', '2024-04-17', '12:15:00', 0, 'Candela Pelaez'),
(223, 'Servant Valentina', '48597052', '2024-04-17', '13:00:00', 0, 'Candela Pelaez'),
(224, 'Montaña Milagros', '45635165', '2024-04-15', '10:00:00', 1, 'Paula Lescano'),
(225, 'Trigo Gustavo', '28412984', '2024-04-15', '08:30:00', 2, 'Paula Lescano'),
(226, 'Iturrieta Norma', '17592156', '2024-04-15', '11:00:00', 0, 'Paula Lescano'),
(227, 'Suarez Luciana', '49351148', '2024-04-15', '12:00:00', 2, 'Paula Lescano'),
(228, 'Sieber Matias ', '34195355', '2024-04-15', '13:00:00', 0, 'Paula Lescano');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `idusuario` int(11) NOT NULL,
  `nombre` varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  `usuario` varchar(20) COLLATE utf8_spanish_ci NOT NULL,
  `clave` varchar(50) COLLATE utf8_spanish_ci NOT NULL,
  `rol` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`idusuario`, `nombre`, `usuario`, `clave`, `rol`) VALUES
(1, 'JEFE', 'jefe', '4e5046fc8d6a97d18a5f54beaed54dea', 1),
(9, 'Mariana Lezcano', 'mariana', '7171e95248ff768e1ebee3edde01ea7a', 2),
(12, 'Candela Pelaez', 'candela', 'b44182379bf9fae976e6ae5996e13cd8', 2),
(13, 'Erica Garrido', 'erica', '60495b4e033e9f60b32a6607b587aadd', 2),
(14, 'Paula Lescano', 'paula', '3e10f9cfe8030470e507965881025ab8', 2),
(15, 'Santiago Salinas', 'santi', '130f1a8e9e102707f3f91b010f151b0b', 2),
(16, 'Araceli Tirado', 'araceli', '0f2c9a93eea6f38fabb3acb1c31488c6', 2),
(17, 'Sabrina Olivieri', 'sabrina', 'ff450ba01b0ca2695d62525505dd80eb', 2),
(18, 'Silvana Merino', 'silvana', 'c783eed3cfc1c978fe76e15af007e0d0', 2);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `asistencias`
--
ALTER TABLE `asistencias`
  ADD PRIMARY KEY (`idasistencia`);

--
-- Indices de la tabla `configuracion`
--
ALTER TABLE `configuracion`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `detalle_permisos`
--
ALTER TABLE `detalle_permisos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_permiso` (`id_permiso`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `historias_clinicas`
--
ALTER TABLE `historias_clinicas`
  ADD PRIMARY KEY (`idhistoria`);

--
-- Indices de la tabla `pacientes`
--
ALTER TABLE `pacientes`
  ADD PRIMARY KEY (`idpaciente`);

--
-- Indices de la tabla `permisos`
--
ALTER TABLE `permisos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `turnos`
--
ALTER TABLE `turnos`
  ADD PRIMARY KEY (`idturno`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`idusuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `asistencias`
--
ALTER TABLE `asistencias`
  MODIFY `idasistencia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT de la tabla `configuracion`
--
ALTER TABLE `configuracion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `detalle_permisos`
--
ALTER TABLE `detalle_permisos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `historias_clinicas`
--
ALTER TABLE `historias_clinicas`
  MODIFY `idhistoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `pacientes`
--
ALTER TABLE `pacientes`
  MODIFY `idpaciente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=93;

--
-- AUTO_INCREMENT de la tabla `permisos`
--
ALTER TABLE `permisos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `turnos`
--
ALTER TABLE `turnos`
  MODIFY `idturno` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=229;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `idusuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `detalle_permisos`
--
ALTER TABLE `detalle_permisos`
  ADD CONSTRAINT `detalle_permisos_ibfk_1` FOREIGN KEY (`id_permiso`) REFERENCES `permisos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `detalle_permisos_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`idusuario`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
