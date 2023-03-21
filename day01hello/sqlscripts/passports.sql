CREATE TABLE `passports` (
  `id` int(11) AUTO_INCREMENT NOT NULL PRIMARY KEY,
  `passportNo` varchar(20) NOT NULL UNIQUE,
  `photoFilePath` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

