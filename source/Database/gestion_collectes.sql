SET
  SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";

START TRANSACTION;

SET
  time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;

/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;

/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;

/*!40101 SET NAMES utf8mb4 */;

DROP TABLE IF EXISTS `dechets_collectes`;

DROP TABLE IF EXISTS `benevoles_collectes`;

DROP TABLE IF EXISTS `benevoles`;

DROP TABLE IF EXISTS `collectes`;

CREATE TABLE
  `benevoles` (
    `id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `nom` VARCHAR(100) NOT NULL,
    `email` VARCHAR(100) NOT NULL UNIQUE KEY,
    `mot_de_passe` VARCHAR(255) NOT NULL,
    `role` ENUM ('admin', 'participant') NOT NULL,
    INDEX `idx_nom` (`nom`)
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

CREATE TABLE
  `collectes` (
    `id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `date_collecte` DATE NOT NULL,
    `lieu` VARCHAR(255) NOT NULL,
    INDEX `idx_collecte_date` (`date_collecte`)
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

CREATE TABLE
  `benevoles_collectes` (
    `id_benevole` INT DEFAULT NULL,
    `id_collecte` INT DEFAULT NULL,
    CONSTRAINT `benevoles_collectes_ibfk_1` FOREIGN KEY (`id_benevole`) REFERENCES `benevoles` (`id`) ON DELETE CASCADE,
    CONSTRAINT `benevoles_collectes_ibfk_2` FOREIGN KEY (`id_collecte`) REFERENCES `collectes` (`id`) ON DELETE CASCADE,
    PRIMARY KEY (`id_benevole`, `id_collecte`)
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

CREATE TABLE
  `dechets_collectes` (
    `id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `id_collecte` INT DEFAULT NULL,
    `type_dechet` VARCHAR(50) NOT NULL,
    `quantite_kg` FLOAT NOT NULL,
    CONSTRAINT `dechets_collectes_ibfk_1` FOREIGN KEY (`id_collecte`) REFERENCES `collectes` (`id`) ON DELETE CASCADE
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;

/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;

/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;