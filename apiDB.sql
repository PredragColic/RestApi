-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               10.1.30-MariaDB - mariadb.org binary distribution
-- Server OS:                    Win32
-- HeidiSQL Version:             9.5.0.5196
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Dumping structure for table jwtapi.knjige
CREATE TABLE IF NOT EXISTS `knjige` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `naziv` varchar(50) NOT NULL,
  `autor` varchar(50) DEFAULT NULL,
  `godina_izdavanja` smallint(5) unsigned DEFAULT NULL,
  `jezik` varchar(50) DEFAULT NULL,
  `originalni_jezik` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=latin1;

-- Dumping data for table jwtapi.knjige: ~0 rows (approximately)
/*!40000 ALTER TABLE `knjige` DISABLE KEYS */;
INSERT INTO `knjige` (`id`, `naziv`, `autor`, `godina_izdavanja`, `jezik`, `originalni_jezik`) VALUES
	(1, 'Na drini cuprija', 'Ivo Andric', 2018, 'srpski', 'srpski'),
	(2, 'Sumnjivo lice', 'Branislav Nusic', 2017, 'srpski', 'srpski'),
	(3, 'Sidarta', 'Herman Hese', 2002, 'srpski', 'nemacki'),
	(4, 'Majstor i Margarita', 'Mihail Bulgakov', 2008, 'srpski', 'ruski'),
	(5, 'Stepski vuk', 'Herman Hese', 2010, 'srpski', 'nemacki'),
	(6, 'Fjodor Dostojevski', 'Idiot', 2012, 'srpski', 'ruski'),
	(7, 'Fjodor Dostojevski', 'Braca Karamazovi', 2016, 'srpski', 'ruski'),
	(8, 'Radoje Domanovic', 'Odabrane pripovetke', 2015, 'srpski', 'srpski'),
	(9, 'Ivo Andric', 'Prokleta avlija', 2016, 'srpski', 'srpski'),
	(10, 'Ivo Andric', 'Ex Ponto', 2014, 'srpski', 'srpski'),
	(11, 'John Doe', 'PHP 7', 2016, 'srpski', 'srpski'),
	(12, 'John Doe', 'All about Laravel', 2017, 'srpski', 'srpski'),
	(13, 'John Doe', 'CodeIgniter for beginners', 2013, 'srpski', 'srpski'),
	(14, 'John Doe', 'AngularJS 5', 2017, 'srpski', 'srpski');
/*!40000 ALTER TABLE `knjige` ENABLE KEYS */;

-- Dumping structure for table jwtapi.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `active` tinyint(4) NOT NULL DEFAULT '1',
  `created_on` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

-- Dumping data for table jwtapi.users: ~0 rows (approximately)
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` (`id`, `name`, `email`, `password`, `active`, `created_on`) VALUES
	(1, 'Predrag Colic', 'predragcolic@gmail.com', 'test123', 1, '2018-02-22 23:37:25');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
