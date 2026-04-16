-- Drop existing tables if they exist
DROP TABLE IF EXISTS `offers`;
DROP TABLE IF EXISTS `society`;

-- Create Society table
CREATE TABLE `society` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `domain` varchar(150) DEFAULT NULL,
  `description` longtext DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create Offers table
CREATE TABLE `offers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `society_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` longtext NOT NULL,
  `domain` varchar(150) DEFAULT NULL,
  `salary` decimal(10,2) DEFAULT NULL,
  `contract_type` varchar(100) NOT NULL,
  `location` varchar(150) DEFAULT NULL,
  `experience_level` varchar(100) DEFAULT NULL,
  `expiration_date` date DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `society_id` (`society_id`),
  CONSTRAINT `offers_ibfk_1` FOREIGN KEY (`society_id`) REFERENCES `society` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
