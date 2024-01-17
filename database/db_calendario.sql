/*
 Navicat Premium Data Transfer

 Source Server         : localhost
 Source Server Type    : MySQL
 Source Server Version : 100427 (10.4.27-MariaDB)
 Source Host           : /Applications/XAMPP/xamppfiles/var/mysql/mysql.sock:3306
 Source Schema         : db_calendario

 Target Server Type    : MySQL
 Target Server Version : 100427 (10.4.27-MariaDB)
 File Encoding         : 65001

 Date: 17/01/2024 14:35:38
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for tbl_eventos
-- ----------------------------
DROP TABLE IF EXISTS `tbl_eventos`;
CREATE TABLE `tbl_eventos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fecha_inicio` datetime DEFAULT NULL,
  `fecha_fin` datetime DEFAULT NULL,
  `nombre_evento` varchar(255) DEFAULT NULL,
  `estatus` int(11) DEFAULT NULL,
  `cantidad_grup` int(11) DEFAULT NULL,
  `min_grupo` int(11) DEFAULT NULL,
  `num_part` int(11) DEFAULT NULL,
  `hora_inicio` time DEFAULT NULL,
  `hora_fin` time DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Table structure for tbl_fechas
-- ----------------------------
DROP TABLE IF EXISTS `tbl_fechas`;
CREATE TABLE `tbl_fechas` (
  `id_fecha` int(11) NOT NULL AUTO_INCREMENT,
  `id_evento` int(11) DEFAULT NULL,
  `dia_semana` varchar(9) NOT NULL,
  `dia_mes` int(11) DEFAULT NULL,
  `mes` varchar(20) DEFAULT NULL,
  `anio` int(11) DEFAULT NULL,
  `disponibilidad` int(11) NOT NULL,
  `fecha` datetime DEFAULT NULL,
  PRIMARY KEY (`id_fecha`),
  KEY `id_evento` (`id_evento`),
  CONSTRAINT `tbl_fechas_ibfk_1` FOREIGN KEY (`id_evento`) REFERENCES `tbl_eventos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=96 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Table structure for tbl_horarios
-- ----------------------------
DROP TABLE IF EXISTS `tbl_horarios`;
CREATE TABLE `tbl_horarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_evento` int(11) DEFAULT NULL,
  `id_fecha` int(11) DEFAULT NULL,
  `hora_inicio` time DEFAULT NULL,
  `hora_fin` time DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_fecha` (`id_fecha`),
  KEY `id_evento` (`id_evento`),
  CONSTRAINT `tbl_horarios_ibfk_1` FOREIGN KEY (`id_fecha`) REFERENCES `tbl_fechas` (`id_fecha`),
  CONSTRAINT `tbl_horarios_ibfk_2` FOREIGN KEY (`id_evento`) REFERENCES `tbl_eventos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Table structure for tbl_participantes
-- ----------------------------
DROP TABLE IF EXISTS `tbl_participantes`;
CREATE TABLE `tbl_participantes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) DEFAULT NULL,
  `id_evento` int(11) NOT NULL,
  `id_fecha` int(11) NOT NULL,
  `id_horario` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`,`id_evento`,`id_fecha`) USING BTREE,
  KEY `id_evento` (`id_evento`),
  KEY `id_fecha` (`id_fecha`),
  KEY `id_horario` (`id_horario`),
  CONSTRAINT `tbl_participantes_ibfk_1` FOREIGN KEY (`id_evento`) REFERENCES `tbl_eventos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `tbl_participantes_ibfk_2` FOREIGN KEY (`id_fecha`) REFERENCES `tbl_fechas` (`id_fecha`),
  CONSTRAINT `tbl_participantes_ibfk_3` FOREIGN KEY (`id_horario`) REFERENCES `tbl_horarios` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

SET FOREIGN_KEY_CHECKS = 1;
