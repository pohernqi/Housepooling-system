-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               10.4.24-MariaDB - mariadb.org binary distribution
-- Server OS:                    Win64
-- HeidiSQL Version:             11.3.0.6295
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Dumping structure for table housepool.admin
CREATE TABLE IF NOT EXISTS `admin` (
  `username` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(30) NOT NULL,
  `email` varchar(40) NOT NULL,
  `adminId` int(30) NOT NULL AUTO_INCREMENT,
  `name` varchar(40) NOT NULL,
  `pic` blob NOT NULL,
  `reset_token_hash` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`adminId`),
  UNIQUE KEY `reset_token_hash` (`reset_token_hash`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4;

-- Dumping data for table housepool.admin: ~2 rows (approximately)
DELETE FROM `admin`;
/*!40000 ALTER TABLE `admin` DISABLE KEYS */;
INSERT INTO `admin` (`username`, `password`, `phone`, `email`, `adminId`, `name`, `pic`, `reset_token_hash`) VALUES
	('admin_user1', '$2y$10$uQeI5S3GyS2T6xohugxOQ.d4wAhKTeyMOzFpqy9WDVrUxQNTarNQK', '012-4747777', 'admin_user1@gmail.com', 6, 'Admin user 1', _binary 0x696d672f70726f66696c652e706e67, NULL),
	('admin_user2', '$2y$10$exqgJtsEqx.QGtbSirMXRu2xoDKskbg2irzxLdJF.8NhXn0mZHjp6', '012-4848488', 'admin_user2@gmail.com', 7, 'Admin user 2', _binary 0x696d672f70726f66696c652e706e67, NULL);
/*!40000 ALTER TABLE `admin` ENABLE KEYS */;

-- Dumping structure for table housepool.agreement
CREATE TABLE IF NOT EXISTS `agreement` (
  `agreementId` int(30) NOT NULL AUTO_INCREMENT,
  `rental` decimal(6,2) NOT NULL,
  `startdate` date NOT NULL,
  `enddate` date NOT NULL,
  `deposit` decimal(6,2) NOT NULL,
  `status` varchar(10) NOT NULL,
  `paystatus` varchar(20) NOT NULL,
  `tenantId` int(20) NOT NULL,
  `propertyId` int(30) NOT NULL,
  `docs` varchar(200) NOT NULL,
  `receivedDocs` varchar(200) NOT NULL,
  PRIMARY KEY (`agreementId`),
  KEY `sds` (`propertyId`),
  KEY `tenantId` (`tenantId`),
  CONSTRAINT `sds` FOREIGN KEY (`propertyId`) REFERENCES `property` (`propertyId`) ON DELETE CASCADE,
  CONSTRAINT `tenantId` FOREIGN KEY (`tenantId`) REFERENCES `tenant` (`tenantId`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=111 DEFAULT CHARSET=utf8mb4;

-- Dumping data for table housepool.agreement: ~3 rows (approximately)
DELETE FROM `agreement`;
/*!40000 ALTER TABLE `agreement` DISABLE KEYS */;
INSERT INTO `agreement` (`agreementId`, `rental`, `startdate`, `enddate`, `deposit`, `status`, `paystatus`, `tenantId`, `propertyId`, `docs`, `receivedDocs`) VALUES
	(108, 1500.00, '2024-03-01', '2025-02-28', 500.00, 'pending', 'unpaid', 6, 107, 'tenancy-agreement-template-for-lease-of-private-residential-properties-2020.pdf', ''),
	(109, 1200.00, '2024-02-01', '2025-01-31', 500.00, 'pending', 'unpaid', 7, 108, 'tenancy-agreement-for-lease.pdf', ''),
	(110, 1200.00, '2024-02-01', '2025-01-31', 500.00, 'accepted', 'paid', 8, 108, 'tenancy-agreement-for-lease_1.pdf', 'signed_tenancy-agreement-for-lease.pdf');
/*!40000 ALTER TABLE `agreement` ENABLE KEYS */;

-- Dumping structure for table housepool.booking
CREATE TABLE IF NOT EXISTS `booking` (
  `bookingId` int(30) NOT NULL AUTO_INCREMENT,
  `bookingdate` datetime(6) NOT NULL,
  `remarks` varchar(100) NOT NULL,
  `bookingstatus` varchar(20) NOT NULL,
  `searcherId` int(30) NOT NULL,
  `propertyId` int(30) NOT NULL,
  PRIMARY KEY (`bookingId`),
  KEY `booking_ibfk_1` (`propertyId`),
  KEY `sdsd` (`searcherId`),
  CONSTRAINT `booking_ibfk_1` FOREIGN KEY (`propertyId`) REFERENCES `property` (`propertyId`) ON DELETE CASCADE,
  CONSTRAINT `sdsd` FOREIGN KEY (`searcherId`) REFERENCES `searcher` (`searcherId`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4;

-- Dumping data for table housepool.booking: ~2 rows (approximately)
DELETE FROM `booking`;
/*!40000 ALTER TABLE `booking` DISABLE KEYS */;
INSERT INTO `booking` (`bookingId`, `bookingdate`, `remarks`, `bookingstatus`, `searcherId`, `propertyId`) VALUES
	(29, '2024-02-20 08:00:00.000000', 'I want to arrange for viewing.', 'approved', 7, 107),
	(30, '2024-02-17 10:00:00.000000', 'Interested to view. Please call me.', 'rejected', 8, 107);
/*!40000 ALTER TABLE `booking` ENABLE KEYS */;

-- Dumping structure for table housepool.feedback
CREATE TABLE IF NOT EXISTS `feedback` (
  `feedbackId` int(30) NOT NULL AUTO_INCREMENT,
  `adminId` int(30) NOT NULL,
  `feedback` varchar(255) NOT NULL,
  `propertyId` int(30) NOT NULL,
  PRIMARY KEY (`feedbackId`),
  KEY `adminId` (`adminId`),
  KEY `propertyId` (`propertyId`),
  CONSTRAINT `adminId` FOREIGN KEY (`adminId`) REFERENCES `admin` (`adminId`) ON DELETE CASCADE,
  CONSTRAINT `propertyId` FOREIGN KEY (`propertyId`) REFERENCES `property` (`propertyId`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4;

-- Dumping data for table housepool.feedback: ~1 rows (approximately)
DELETE FROM `feedback`;
/*!40000 ALTER TABLE `feedback` DISABLE KEYS */;
INSERT INTO `feedback` (`feedbackId`, `adminId`, `feedback`, `propertyId`) VALUES
	(8, 7, 'Wrong selection. Please change to Apartment type.', 110);
/*!40000 ALTER TABLE `feedback` ENABLE KEYS */;

-- Dumping structure for table housepool.owner
CREATE TABLE IF NOT EXISTS `owner` (
  `username` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(30) NOT NULL,
  `phone` varchar(30) NOT NULL,
  `ownerId` int(6) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  `pic` blob NOT NULL,
  `reset_token_hash` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`ownerId`),
  UNIQUE KEY `reset_token_hash` (`reset_token_hash`)
) ENGINE=InnoDB AUTO_INCREMENT=59 DEFAULT CHARSET=utf8mb4;

-- Dumping data for table housepool.owner: ~2 rows (approximately)
DELETE FROM `owner`;
/*!40000 ALTER TABLE `owner` DISABLE KEYS */;
INSERT INTO `owner` (`username`, `password`, `email`, `phone`, `ownerId`, `name`, `pic`, `reset_token_hash`) VALUES
	('jenny', '$2y$10$XG657fwmKi6N3Vwk8gnudOFq9JcAGyfDCL/0GhKsR2dSpIoxZEtY.', 'jenny@gmail.com', '017-4222223', 51, 'Jenny Foo', _binary 0x75706c6f6164732f363563396331386137303936382e6a7067, NULL),
	('thomas', '$2y$10$1JQ5HZoLJBxYONdhijSesesM7T1N9bET1JEvbkHFXSF235qZwAESG', 'thomas@gmail.com', '018-3337666', 58, 'Thomas Lee', _binary 0x696d672f70726f66696c652e706e67, NULL);
/*!40000 ALTER TABLE `owner` ENABLE KEYS */;

-- Dumping structure for table housepool.property
CREATE TABLE IF NOT EXISTS `property` (
  `propertyId` int(30) NOT NULL AUTO_INCREMENT,
  `propertyname` varchar(70) NOT NULL,
  `location` varchar(40) NOT NULL,
  `address` varchar(200) NOT NULL,
  `description` varchar(200) NOT NULL,
  `propertytype` varchar(30) NOT NULL,
  `rentType` varchar(30) NOT NULL,
  `propertystatus` varchar(20) NOT NULL,
  `pic` blob NOT NULL,
  `ownerId` int(6) NOT NULL,
  `price` decimal(6,2) NOT NULL,
  PRIMARY KEY (`propertyId`),
  KEY `fk` (`ownerId`),
  CONSTRAINT `fk` FOREIGN KEY (`ownerId`) REFERENCES `owner` (`ownerId`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=111 DEFAULT CHARSET=utf8mb4;

-- Dumping data for table housepool.property: ~4 rows (approximately)
DELETE FROM `property`;
/*!40000 ALTER TABLE `property` DISABLE KEYS */;
INSERT INTO `property` (`propertyId`, `propertyname`, `location`, `address`, `description`, `propertytype`, `rentType`, `propertystatus`, `pic`, `ownerId`, `price`) VALUES
	(107, 'Skyview Residence', 'Penang', '11A Jalan Hijau', 'Fully furnished with 4 rooms', 'Condominium', 'Room', 'approved', _binary 0x75706c6f6164732f363563613237343630396339662e6a7067, 58, 2000.00),
	(108, 'Taman Chee Seng', 'CyberJaya', '89, Jalan Rumbai', 'One room for femail only.', 'Terrace', 'Room', 'approved', _binary 0x75706c6f6164732f363563613264643539366564302e706e67, 58, 200.00),
	(109, 'Setia Condo', 'Puchong', '7,Jalan Puchong', 'Fully furnished with 3 rooms', 'Condominium', 'Whole Unit', 'approved', _binary 0x75706c6f6164732f363563613238353163396632652e706e67, 51, 3000.00),
	(110, 'Taman Lembah Maju', 'Jelutong', 'Penang', 'Empty unit without furniture', 'Studio Apartment', 'Whole Unit', 'rejected', _binary 0x75706c6f6164732f363563613333633533653533632e6a7067, 58, 900.00);
/*!40000 ALTER TABLE `property` ENABLE KEYS */;

-- Dumping structure for table housepool.request_role
CREATE TABLE IF NOT EXISTS `request_role` (
  `requestId` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `request_status` enum('pending','approved','rejected','') NOT NULL DEFAULT 'pending',
  `request_date` datetime NOT NULL DEFAULT current_timestamp(),
  `description` varchar(255) NOT NULL,
  PRIMARY KEY (`requestId`),
  KEY `request tenantId` (`userId`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4;

-- Dumping data for table housepool.request_role: ~0 rows (approximately)
DELETE FROM `request_role`;
/*!40000 ALTER TABLE `request_role` DISABLE KEYS */;
/*!40000 ALTER TABLE `request_role` ENABLE KEYS */;

-- Dumping structure for table housepool.review
CREATE TABLE IF NOT EXISTS `review` (
  `reviewId` int(30) NOT NULL AUTO_INCREMENT,
  `reviewdetail` varchar(255) NOT NULL,
  `reviewstar` int(5) NOT NULL,
  `reviewdate` datetime NOT NULL DEFAULT current_timestamp(),
  `propertyId` int(30) NOT NULL,
  `tenantId` int(20) NOT NULL,
  PRIMARY KEY (`reviewId`),
  KEY `foreign key 1` (`propertyId`),
  KEY `foreign key 2` (`tenantId`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4;

-- Dumping data for table housepool.review: ~1 rows (approximately)
DELETE FROM `review`;
/*!40000 ALTER TABLE `review` DISABLE KEYS */;
INSERT INTO `review` (`reviewId`, `reviewdetail`, `reviewstar`, `reviewdate`, `propertyId`, `tenantId`) VALUES
	(12, 'Good location!', 5, '2024-02-12 23:02:17', 108, 8);
/*!40000 ALTER TABLE `review` ENABLE KEYS */;

-- Dumping structure for table housepool.searcher
CREATE TABLE IF NOT EXISTS `searcher` (
  `searcherId` int(30) NOT NULL AUTO_INCREMENT,
  `username` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(40) NOT NULL,
  `name` varchar(40) NOT NULL,
  `phone` varchar(30) NOT NULL,
  `pic` blob NOT NULL,
  `propertyId` int(30) NOT NULL,
  `reset_token_hash` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`searcherId`),
  UNIQUE KEY `reset_token_hash` (`reset_token_hash`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4;

-- Dumping data for table housepool.searcher: ~2 rows (approximately)
DELETE FROM `searcher`;
/*!40000 ALTER TABLE `searcher` DISABLE KEYS */;
INSERT INTO `searcher` (`searcherId`, `username`, `password`, `email`, `name`, `phone`, `pic`, `propertyId`, `reset_token_hash`) VALUES
	(7, 'jeeva1', '$2y$10$CrtwRYhDXA1IHdZpGfeZb.R5bFKRJir55/22Nr5mp7ZyJRm1OMC5S', 'jeeva@gmail.com', 'Jeeva A/L Goyal', '018-2323222', _binary 0x696d672f70726f66696c652e706e67, 0, NULL),
	(8, 'anita', '$2y$10$H8g35TgGrXUzL4pHGoFGCO8XwZpIwKjFv7n2noewGKGuj8eApcUAu', 'anita@gmail.com', 'Anita A/P Rohan', '018-3339999', _binary 0x696d672f70726f66696c652e706e67, 0, NULL);
/*!40000 ALTER TABLE `searcher` ENABLE KEYS */;

-- Dumping structure for table housepool.tenant
CREATE TABLE IF NOT EXISTS `tenant` (
  `username` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(30) NOT NULL,
  `phone` varchar(30) NOT NULL,
  `tenantId` int(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(40) NOT NULL,
  `pic` blob NOT NULL,
  `reset_token_hash` varchar(64) DEFAULT NULL,
  `is_deleted` smallint(6) NOT NULL DEFAULT 0,
  PRIMARY KEY (`tenantId`),
  UNIQUE KEY `reset_token_hash` (`reset_token_hash`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4;

-- Dumping data for table housepool.tenant: ~3 rows (approximately)
DELETE FROM `tenant`;
/*!40000 ALTER TABLE `tenant` DISABLE KEYS */;
INSERT INTO `tenant` (`username`, `password`, `email`, `phone`, `tenantId`, `name`, `pic`, `reset_token_hash`, `is_deleted`) VALUES
	('cheekeong1', '$2y$10$9W7M3cI/jb4hVSPXyysVAud1Zjwy4NXyZH2JFh27vb4EPIN4jYN82', 'cheekeong1@gmail.com', '018-9999111', 6, 'Lim Chee Keong', _binary 0x696d672f70726f66696c652e706e67, NULL, 0),
	('ewehock1', '$2y$10$9.VYJf3k2ud8mLzHxJ4wB.qngXnK2RV4bzQ7fuySq9rSSORW55eeS', 'ewehock1@gmail.com', '017-8888222', 7, 'Ong Ewe Hong', _binary 0x696d672f70726f66696c652e706e67, NULL, 0),
	('huilan', '$2y$10$5T5xK56m5zBLrXu3E5nokeBcGHAasz.lLDjkyuM6yp5WH1JW9Uq.a', 'huilan@gmail.com', '014-2211111', 8, 'Ang Hui Lan', _binary 0x696d672f70726f66696c652e706e67, NULL, 0);
/*!40000 ALTER TABLE `tenant` ENABLE KEYS */;

-- Dumping structure for table housepool.transaction
CREATE TABLE IF NOT EXISTS `transaction` (
  `transactionId` int(30) NOT NULL AUTO_INCREMENT,
  `paymentDate` datetime NOT NULL DEFAULT current_timestamp(),
  `amount` int(10) NOT NULL,
  `paymentType` varchar(50) NOT NULL,
  `transactionStatus` varchar(50) NOT NULL,
  `agreementId` int(30) NOT NULL,
  PRIMARY KEY (`transactionId`),
  KEY `agreementId` (`agreementId`),
  CONSTRAINT `payment foreign key 1` FOREIGN KEY (`agreementId`) REFERENCES `agreement` (`agreementId`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4;

-- Dumping data for table housepool.transaction: ~1 rows (approximately)
DELETE FROM `transaction`;
/*!40000 ALTER TABLE `transaction` DISABLE KEYS */;
INSERT INTO `transaction` (`transactionId`, `paymentDate`, `amount`, `paymentType`, `transactionStatus`, `agreementId`) VALUES
	(15, '2024-02-12 23:01:36', 500, 'Visa', 'paid', 110);
/*!40000 ALTER TABLE `transaction` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
