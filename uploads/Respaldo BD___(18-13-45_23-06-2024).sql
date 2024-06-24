SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
--
-- Database: `inventario`
--


SET FOREIGN_KEY_CHECKS=0;


CREATE TABLE `articulos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `serial_fabrica` varchar(20) NOT NULL,
  `descripcion` varchar(255) NOT NULL,
  `fabricante` varchar(255) NOT NULL,
  `ubicacion` int(11) NOT NULL,
  `monto_valor` varchar(20) DEFAULT NULL,
  `esta_retirado` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `Divisiones` (`ubicacion`),
  CONSTRAINT `Divisiones` FOREIGN KEY (`ubicacion`) REFERENCES `divisiones` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


INSERT INTO articulos VALUES
("1","00000001","CAFETERA","OSTER","1","0,00","0"),
("2","00000002","ROUTER","TP-LINK","7","0,00","0"),
("3","00000003","BOLIGRAFO","Sharpie","7","0,00","0"),
("4","00000004","ESCRITORIO","GENERICO","7","0,00","0"),
("5","00000005","ARCHIVADOR COLOR HAYA","GENERICO","1","0,00","0"),
("6","00000006","SILLA OFICINA","GENERICO","1","0,00","0"),
("7","00000007","PIZARRA ACRILICA","GENERICO","1","0,00","0"),
("8","00000008","SILLA OFICINA","GENERICO","1","0,00","0"),
("9","00000008","MONITOR","LG","1","0,00","0"),
("10","1000000","BOLIGRAFO","Sharpie","1","0,00","0"),
("11","00200003","SILLA OFICINA","GENERICO","6","0,00","0"),
("12","002220001","VIDEO BEAM","EPSON","6","0,00","0"),
("13","11100008","MONITOR","AOC","6","0,00","0"),
("14","111223001","VIDEO BEAM","EPSON","6","0,00","0"),
("15","00000228","BOLIGRAFO","Sharpie","2","0,00","0"),
("16","00004444","BOLIGRAFO","Sharpie","1","0,00","0"),
("17","00234001","SILLA OFICINA","GENERICO","1","0,00","0"),
("18","02222308","ESCRITORIO","GENERICO","2","0,00","0"),
("19","29300008","MONITOR","LG","2","0,00","0");




CREATE TABLE `divisiones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_division` varchar(255) NOT NULL,
  `direccion` varchar(40) NOT NULL,
  `municipio` varchar(40) NOT NULL,
  `es_destino_retiro` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


INSERT INTO divisiones VALUES
("1","DESPACHO DE LA DIRECCION","EDIFICIO ADMINISTRATIVO","ANGOSTURA DEL ORINOCO","0"),
("2","OFICINA DE ADMINISTRACION Y GESTION INTERNA","EDIFICIO ADMINISTRATIVO","ANGOSTURA DEL ORINOCO","0"),
("3","UNIDAD DE PROYECTO DE INVESTIGACIÓN Y TECNOLOGÍA","EDIFICIO ADMINISTRATIVO","ANGOSTURA DEL ORINOCO","0"),
("4","DEPARTAMENTO DE SEGURIDAD Y SISTEMAS","EDIFICIO ADMINISTRATIVO","ANGOSTURA DEL ORINOCO","0"),
("5","DEPARTAMENTO DE DESARROLLO Y MANTENIMIENTO DE SISTEMAS","EDIFICIO ADMINISTRATIVO","ANGOSTURA DEL ORINOCO","0"),
("6","ADMINISTRACION DE BASE DE DATOS","EDIFICIO ADMINISTRATIVO","ANGOSTURA DEL ORINOCO","0"),
("7","DEPARTAMENTO DE SOPORTE TECNICO","EDIFICIO ADMINISTRATIVO","ANGOSTURA DEL ORINOCO","0"),
("8","DEPARTAMENTO DE ASISTENCIA AL USUARIO","EDIFICIO ADMINISTRATIVO","ANGOSTURA DEL ORINOCO","0"),
("9","DEPARTAMENTO DE REDES Y TELECOMUNICACIONES","EDIFICIO ADMINISTRATIVO","ANGOSTURA DEL ORINOCO","0"),
("10","DEPARTAMENTO DE SEGURIDAD Y CONTROL","EDIFICIO ADMINISTRATIVO","ANGOSTURA DEL ORINOCO","0"),
("11","DEPARTAMENTO DE DESARROLLO Y PRODUCCION","EDIFICIO ADMINISTRATIVO","ANGOSTURA DEL ORINOCO","0"),
("12","DEPARTAMENTO DE ANALISIS Y DISEÑO","EDIFICIO ADMINISTRATIVO","ANGOSTURA DEL ORINOCO","0"),
("14","DEPOSITO DE BIENES POR DESINCORPORAR 2","ZONA INDUSTRIAL LA SABANITA","ANGOSTURA DEL ORINOCO","1");




CREATE TABLE `historial_operaciones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fecha_operacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `observaciones` varchar(255) NOT NULL,
  `tipo_operacion` varchar(255) NOT NULL,
  `destino` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `Destino` (`destino`),
  CONSTRAINT `Destino` FOREIGN KEY (`destino`) REFERENCES `divisiones` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


INSERT INTO historial_operaciones VALUES
("1","2024-04-22 19:47:09","Incorporación","Registro","2"),
("2","2024-04-22 19:48:59","Incorporación","Registro","2"),
("3","2024-04-22 19:52:24","Incorporación","Registro","2"),
("4","2024-04-22 19:52:46","Incorporación","Registro","2"),
("5","2024-04-22 19:53:00","Incorporación","Registro","2"),
("6","2024-04-22 19:53:23","Incorporación","Registro","2"),
("7","2024-04-22 19:53:50","Incorporación","Registro","2"),
("8","2024-04-22 19:54:13","Incorporación","Registro","2"),
("9","2024-04-22 19:57:29","Incorporación","Registro","2"),
("10","2024-04-22 19:58:05","Incorporación","Registro","2"),
("11","2024-04-22 19:58:26","Incorporación","Registro","2"),
("12","2024-04-22 19:59:30","Incorporación","Registro","2"),
("13","2024-04-22 20:00:15","Incorporación","Registro","2"),
("14","2024-04-22 20:01:01","Incorporación","Registro","2"),
("15","2024-04-22 20:01:25","Incorporación","Registro","2"),
("16","2024-04-22 20:01:41","Incorporación","Registro","2"),
("17","2024-04-22 20:02:06","Incorporación","Registro","2"),
("18","2024-04-22 20:02:32","Incorporación","Registro","2"),
("19","2024-04-22 20:02:58","Incorporación","Registro","2"),
("20","2024-05-17 08:27:40","INSERVIBILIDAD","Traspaso","7"),
("21","2024-05-17 08:37:54","Ninguna","Traspaso","1"),
("22","2024-05-17 08:40:34","Ninguna","Traspaso","1"),
("23","2024-05-17 08:40:52","Ninguna","Traspaso","6"),
("24","2024-05-27 04:22:35","Ninguna","Traspaso Temporal","1"),
("25","2024-06-12 12:54:50","Ninguna","Retorno","2"),
("26","2024-06-12 12:55:35","Ninguna","Retorno","2"),
("27","2024-06-12 12:57:13","Ninguna","Retorno","2"),
("28","2024-06-12 12:57:17","Ninguna","Retorno","2"),
("29","2024-06-12 12:57:47","Ninguna","Retorno","2"),
("30","2024-06-12 13:02:36","Ninguna","Retorno","2"),
("31","2024-06-12 13:02:49","Ninguna","Retorno","2"),
("32","2024-06-12 13:03:20","Ninguna","Retorno","2");




CREATE TABLE `historial_operaciones_articulos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_operacion` int(11) NOT NULL,
  `id_articulo` int(11) NOT NULL,
  `origen` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_operacion` (`id_operacion`),
  KEY `id_articulo` (`id_articulo`),
  KEY `origen` (`origen`),
  CONSTRAINT `historial_operaciones_articulos_ibfk_1` FOREIGN KEY (`id_operacion`) REFERENCES `historial_operaciones` (`id`),
  CONSTRAINT `historial_operaciones_articulos_ibfk_2` FOREIGN KEY (`id_articulo`) REFERENCES `articulos` (`id`),
  CONSTRAINT `origen` FOREIGN KEY (`origen`) REFERENCES `divisiones` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


INSERT INTO historial_operaciones_articulos VALUES
("1","1","1","2"),
("2","2","2","2"),
("3","3","3","2"),
("4","4","4","2"),
("5","5","5","2"),
("6","6","6","2"),
("7","7","7","2"),
("8","8","8","2"),
("9","9","9","2"),
("10","10","10","2"),
("11","11","11","2"),
("12","12","12","2"),
("13","13","13","2"),
("14","14","14","2"),
("15","15","15","2"),
("16","16","16","2"),
("17","17","17","2"),
("18","18","18","2"),
("19","19","19","2"),
("20","20","2","2"),
("21","20","3","2"),
("22","20","4","2"),
("23","21","1","2"),
("24","21","5","2"),
("25","21","6","2"),
("26","22","7","2"),
("27","22","8","2"),
("28","22","9","2"),
("29","22","10","2"),
("30","23","11","2"),
("31","23","12","2"),
("32","23","13","2"),
("33","23","14","2"),
("34","24","15","2"),
("35","24","16","2"),
("36","24","17","2");




CREATE TABLE `historial_respaldos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fecha_realizado` timestamp NOT NULL DEFAULT current_timestamp(),
  `realizado_por` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `Realizado por` (`realizado_por`),
  CONSTRAINT `Realizado por` FOREIGN KEY (`realizado_por`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


INSERT INTO historial_respaldos VALUES
("1","2024-04-19 09:21:29","1"),
("8","2024-04-22 20:16:32","1"),
("9","2024-05-30 11:56:19","1"),
("10","2024-06-23 08:33:09","1"),
("11","2024-06-23 08:42:28","1"),
("12","2024-06-23 08:45:07","1"),
("13","2024-06-23 08:45:09","1"),
("14","2024-06-23 14:05:32","1"),
("15","2024-06-23 14:05:43","1");




CREATE TABLE `modelo_articulo` (
  `id_articulo` int(11) NOT NULL,
  `nombre_modelo` varchar(255) NOT NULL,
  PRIMARY KEY (`id_articulo`),
  CONSTRAINT `Articulo-Modelo` FOREIGN KEY (`id_articulo`) REFERENCES `articulos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


INSERT INTO modelo_articulo VALUES
("2","EJEMPLO"),
("9","19M38"),
("12","PL-118"),
("13","A-111"),
("14","PL-118"),
("19","19M38");




CREATE TABLE `nro_identificacion_articulo` (
  `id_articulo` int(11) NOT NULL,
  `n_identificacion` varchar(20) NOT NULL,
  PRIMARY KEY (`id_articulo`),
  CONSTRAINT `Articulo-Identificación` FOREIGN KEY (`id_articulo`) REFERENCES `articulos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


INSERT INTO nro_identificacion_articulo VALUES
("1","000-000-000"),
("2","000-000-001"),
("3","000-000-002"),
("7","000-000-003"),
("9","000-000-010"),
("11","000-000-110"),
("12","000-000-111"),
("13","020-020-000"),
("14","333-000-001"),
("17","999-010-002"),
("19","234-000-110");




CREATE TABLE `traspasos_temporales` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `articulo_id` int(11) NOT NULL,
  `fecha_de_retorno` timestamp NOT NULL DEFAULT current_timestamp(),
  `id_operacion` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `serial` (`articulo_id`),
  KEY `id_operacion` (`id_operacion`),
  CONSTRAINT `articulo` FOREIGN KEY (`articulo_id`) REFERENCES `articulos` (`id`),
  CONSTRAINT `traspasos_temporales_ibfk_1` FOREIGN KEY (`id_operacion`) REFERENCES `historial_operaciones` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


INSERT INTO traspasos_temporales VALUES
("2","16","2024-06-01 11:59:59","24"),
("3","17","2024-06-01 11:59:59","24");




CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_usuario` varchar(40) NOT NULL,
  `contrasena` varchar(255) NOT NULL,
  `rol` varchar(255) NOT NULL,
  `pregunta_recup` varchar(255) NOT NULL,
  `respuesta_recup` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `usuario` (`nombre_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


INSERT INTO usuarios VALUES
("1","admin","21232f297a57a5a743894a0e4a801fc3","admin","admin","21232f297a57a5a743894a0e4a801fc3"),
("2","usuario1","2fb6c8d2f3842a5ceaa9bf320e649ff0","admin","user","ee11cbb19052e40b07aac0ca060c23ee"),
("3","usuario2","827ccb0eea8a706c4c34a16891f84e7b","usuario","¿Cual es tu libro favorito?","b634fe7f72fa0a3c37309525b5f2061f");



SET FOREIGN_KEY_CHECKS=1;


/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;