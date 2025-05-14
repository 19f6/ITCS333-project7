-- Adminer 4.8.1 MySQL 10.6.7-MariaDB-log dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `comments`;
CREATE TABLE `comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `note_id` int(11) NOT NULL,
  `author` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT 'Anonymous',
  `comment` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `note_id` (`note_id`),
  CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`note_id`) REFERENCES `notes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `comments` (`id`, `note_id`, `author`, `comment`, `created_at`) VALUES
(1,	3,	'Anonymous',	'Thanks dr. Noor!',	'2025-05-13 21:09:57'),
(2,	3,	'Anonymous',	'That was helpful!',	'2025-05-13 21:10:29'),
(5,	5,	'Anonymous',	'Thank you!',	'2025-05-14 08:46:22'),
(6,	6,	'Anonymous',	'That note helpful!',	'2025-05-14 12:07:52'),
(7,	4,	'Anonymous',	'Thanks!',	'2025-05-14 12:08:46');

DROP TABLE IF EXISTS `notes`;
CREATE TABLE `notes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `course_code` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `summary` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `notes` (`id`, `course_code`, `title`, `summary`, `content`, `created_at`) VALUES
(3,	'CS113',	'Intro to JAVA',	'Basics of the language',	'We will learn how to write a java code...',	'2025-05-13 18:38:30'),
(4,	'CS113',	'Loops and Conditions in C++',	'If/else and loop structures',	'We covered the if, else, and else if conditional structures. Then moved on to loops: for, while, and do-while. We practiced writing simple programs to calculate sums, factorials, and validate input using loops.',	'2025-05-13 21:33:11'),
(5,	'CS113',	'Introduction to Programming (Week 1)',	'Covered programming fundamentals and tools',	'Today we were introduced to the concept of programming, the role of compilers, and IDE setup. We wrote our first Hello World in C++. We also discussed how programming logic is built using algorithms and flowcharts.',	'2025-05-13 21:34:55'),
(6,	'CS333',	'HTML Basics and Tags',	'Explained the structure of an HTML document',	'This week focused on the basic HTML structure including <!DOCTYPE>, <html>, <head>, and <body>. We practiced creating headings, paragraphs, links, and images. The importance of semantic tags was also introduced.',	'2025-05-14 11:19:09');

-- 2025-05-14 14:12:08
