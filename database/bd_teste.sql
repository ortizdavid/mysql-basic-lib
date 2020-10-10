-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Oct 10, 2020 at 04:25 AM
-- Server version: 5.7.31
-- PHP Version: 7.4.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bd_teste`
--

DELIMITER $$
--
-- Procedures
--
DROP PROCEDURE IF EXISTS `proc_contactos_cliente`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `proc_contactos_cliente` (IN `id` INT)  BEGIN
	SELECT telefone, email 
    FROM tb_cliente 
    WHERE id_cliente = id;
    		
END$$

DROP PROCEDURE IF EXISTS `proc_nome_altura`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `proc_nome_altura` ()  BEGIN
	SELECT nome, altura FROM tb_cliente;	
END$$

--
-- Functions
--
DROP FUNCTION IF EXISTS `fun_ola_mundo`$$
CREATE DEFINER=`root`@`localhost` FUNCTION `fun_ola_mundo` () RETURNS VARCHAR(12) CHARSET latin1 BEGIN
    
	return "Ola Mundo";
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `tb_cliente`
--

DROP TABLE IF EXISTS `tb_cliente`;
CREATE TABLE IF NOT EXISTS `tb_cliente` (
  `id_cliente` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `sexo` enum('Feminino','Masculino') DEFAULT NULL,
  `data_nasc` date NOT NULL,
  `altura` double DEFAULT NULL,
  PRIMARY KEY (`id_cliente`),
  UNIQUE KEY `nome` (`nome`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tb_contacto`
--

DROP TABLE IF EXISTS `tb_contacto`;
CREATE TABLE IF NOT EXISTS `tb_contacto` (
  `id_contacto` int(11) NOT NULL AUTO_INCREMENT,
  `id_cliente` int(11) NOT NULL,
  `telefone` int(9) NOT NULL,
  `email` varchar(100) NOT NULL,
  PRIMARY KEY (`id_contacto`),
  UNIQUE KEY `telefone` (`telefone`),
  UNIQUE KEY `email` (`email`),
  KEY `fk_cliente_contacto` (`id_cliente`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Triggers `tb_contacto`
--
DROP TRIGGER IF EXISTS `tr_teste`;
DELIMITER $$
CREATE TRIGGER `tr_teste` AFTER DELETE ON `tb_contacto` FOR EACH ROW BEGIN 


END
$$
DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
