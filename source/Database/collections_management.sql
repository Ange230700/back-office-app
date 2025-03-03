SET
  SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";

START TRANSACTION;

SET
  time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;

/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;

/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;

/*!40101 SET NAMES utf8mb4 */;

DROP TABLE IF EXISTS `Collected_waste`;

DROP TABLE IF EXISTS `Volunteer_Collection`;

DROP TABLE IF EXISTS `Volunteer`;

DROP TABLE IF EXISTS `CollectionEvent`;

CREATE TABLE
  `Volunteer` (
    `volunteer_id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `username` VARCHAR(100) NOT NULL,
    `email` VARCHAR(100) NOT NULL UNIQUE KEY,
    `password` VARCHAR(255) NOT NULL,
    `role` ENUM ('admin', 'participant') NOT NULL,
    INDEX `idx_username` (`username`)
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

CREATE TABLE
  `CollectionEvent` (
    `collection_id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `collection_date` DATE NOT NULL,
    `collection_place` VARCHAR(255) NOT NULL,
    INDEX `idx_collection_date` (`collection_date`)
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

CREATE TABLE
  `Volunteer_Collection` (
    `id_volunteer` INT DEFAULT NULL,
    `id_collection` INT DEFAULT NULL,
    CONSTRAINT `volunteer_collection_ibfk_1` FOREIGN KEY (`id_volunteer`) REFERENCES `Volunteer` (`volunteer_id`) ON DELETE CASCADE,
    CONSTRAINT `volunteer_collection_ibfk_2` FOREIGN KEY (`id_collection`) REFERENCES `CollectionEvent` (`collection_id`) ON DELETE CASCADE,
    PRIMARY KEY (`id_volunteer`, `id_collection`)
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

CREATE TABLE
  `Collected_waste` (
    `id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `id_collection` INT DEFAULT NULL,
    `waste_type` VARCHAR(50) NOT NULL,
    `quantity_kg` FLOAT NOT NULL,
    CONSTRAINT `collected_waste_ibfk_1` FOREIGN KEY (`id_collection`) REFERENCES `CollectionEvent` (`collection_id`) ON DELETE CASCADE
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;

/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;

/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;