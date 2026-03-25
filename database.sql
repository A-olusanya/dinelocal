Warning: A partial dump from a server that has GTIDs will by default include the GTIDs of all transactions, even those that changed suppressed parts of the database. If you don't want to restore GTIDs, pass --set-gtid-purged=OFF. To make a complete dump, pass --all-databases --triggers --routines --events. 
Warning: A dump from a server that has GTIDs enabled will by default include the GTIDs of all transactions, even those that were executed during its extraction and might not be represented in the dumped data. This might result in an inconsistent data dump. 
In order to ensure a consistent backup of the database, pass --single-transaction or --lock-all-tables or --source-data. 
-- MySQL dump 10.13  Distrib 9.6.0, for macos26.3 (arm64)
--
-- Host: localhost    Database: dinelocal
-- ------------------------------------------------------
-- Server version	9.6.0

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
SET @MYSQLDUMP_TEMP_LOG_BIN = @@SESSION.SQL_LOG_BIN;
SET @@SESSION.SQL_LOG_BIN= 0;

--
-- GTID state at the beginning of the backup 
--

SET @@GLOBAL.GTID_PURGED=/*!80000 '+'*/ '9d9f0358-27d9-11f1-ac74-ef992f221d2c:1-35';

--
-- Table structure for table `admins`
--

DROP TABLE IF EXISTS `admins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `admins` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `role` enum('super_admin','menu_manager','reservations_manager') COLLATE utf8mb4_unicode_ci DEFAULT 'super_admin',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admins`
--

LOCK TABLES `admins` WRITE;
/*!40000 ALTER TABLE `admins` DISABLE KEYS */;
INSERT INTO `admins` VALUES (1,'admin','admin@dinelocal.ca','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','2026-03-24 23:32:15','super_admin'),(2,'olusanya','olusanya.ay@northeastern.edu','$2y$12$Yj6bXF5Lt7n17rEH7fGBkuKl1oW2sgoQAkQ3H1EfK6OC/qphviF1e','2026-03-25 03:26:24','super_admin'),(3,'ambalavatta','ambalavattakottayi.a@northeastern.edu','$2y$12$GhEdP5jc0tG0W6lxJJVYP.gYHRH1pm4KB5914zSVOfKff//JlThOK','2026-03-25 03:26:24','super_admin');
/*!40000 ALTER TABLE `admins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `menu_items`
--

DROP TABLE IF EXISTS `menu_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `menu_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` decimal(8,2) NOT NULL,
  `category` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_available` tinyint(1) DEFAULT '1',
  `is_featured` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `menu_items`
--

LOCK TABLES `menu_items` WRITE;
/*!40000 ALTER TABLE `menu_items` DISABLE KEYS */;
INSERT INTO `menu_items` VALUES (1,'Wood-Fired Flatbread','Ontario prosciutto, fig jam, arugula, shaved parmesan, aged balsamic drizzle.',24.00,'Starters','https://images.unsplash.com/photo-1513104890138-7c749659a591?w=600&h=400&fit=crop',1,1,'2026-03-24 23:32:15','2026-03-25 03:12:22'),(2,'Butternut Bisque','Roasted Ontario butternut squash, crème fraîche, toasted pumpkin seeds, chive oil.',14.00,'Starters','https://images.unsplash.com/photo-1547592180-85f173990554?w=600&h=400&fit=crop',1,0,'2026-03-24 23:32:15','2026-03-25 03:12:25'),(3,'Charcuterie Board','Locally cured meats, artisan cheeses, house pickles, honeycomb, grain mustard, crostini.',32.00,'Starters','https://images.unsplash.com/photo-1555243896-c709bfa0b564?w=600&h=400&fit=crop',1,1,'2026-03-24 23:32:15','2026-03-25 03:12:25'),(4,'Heirloom Tomato Salad','Vine-ripened heirlooms, burrata, basil oil, sea salt, 10-year aged balsamic.',18.00,'Starters','https://images.unsplash.com/photo-1592417817098-8fd3d9eb14a5?w=600&h=400&fit=crop',1,0,'2026-03-24 23:32:15','2026-03-25 03:12:25'),(5,'Ontario Beef Striploin','Dry-aged 28 days. Roasted marrow, heirloom carrots, red wine reduction, rosemary squash.',42.00,'Mains','https://images.unsplash.com/photo-1558030006-450675393462?w=600&h=400&fit=crop',1,1,'2026-03-24 23:32:15','2026-03-25 03:12:25'),(6,'Wild Mushroom Tagliatelle','Hand-cut pasta, foraged Ontario mushrooms, truffle cream, aged pecorino romano.',28.00,'Mains','https://images.unsplash.com/photo-1563379926898-05f4575a45d8?w=600&h=400&fit=crop',1,1,'2026-03-24 23:32:15','2026-03-25 03:12:25'),(7,'Pan-Seared Salmon','Atlantic salmon, saffron risotto, crispy capers, lemon beurre blanc, micro herbs.',38.00,'Mains','https://images.unsplash.com/photo-1467003909585-2f8a72700288?w=600&h=400&fit=crop',1,0,'2026-03-24 23:32:15','2026-03-25 03:12:25'),(8,'Duck Confit','Slow-cooked Brant County duck, Puy lentils, cherry jus, wilted greens.',36.00,'Mains','https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=600&h=400&fit=crop',1,0,'2026-03-24 23:32:15','2026-03-25 03:12:25'),(9,'Roasted Vegetable Tart','Seasonal Ontario vegetables, goat cheese, fresh herbs, puff pastry.',26.00,'Mains','https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?w=600&h=400&fit=crop',1,0,'2026-03-24 23:32:15','2026-03-25 03:12:25'),(10,'Dark Cocoa Ganache','70% Peruvian cocoa, salted caramel, maple honey gelato, vanilla tuile.',14.00,'Desserts','https://images.unsplash.com/photo-1578985545062-69928b1d9587?w=600&h=400&fit=crop',1,1,'2026-03-24 23:32:15','2026-03-25 03:12:25'),(11,'Crème Brûlée','Classic French vanilla custard, torched sugar, Ontario berry compote.',12.00,'Desserts','https://images.unsplash.com/photo-1470124182917-cc6e71b22ecc?w=600&h=400&fit=crop',1,0,'2026-03-24 23:32:15','2026-03-25 03:12:25'),(12,'Sticky Toffee Pudding','Medjool date cake, warm toffee sauce, Kawartha Dairy vanilla ice cream.',13.00,'Desserts','https://images.unsplash.com/photo-1551024601-bec78aea704b?w=600&h=400&fit=crop',1,0,'2026-03-24 23:32:15','2026-03-25 03:12:25'),(13,'Ontario Red Wine','Curated selection from Prince Edward County and Niagara. 6oz pour.',16.00,'Drinks','https://images.unsplash.com/photo-1510812431401-41d2bd2722f3?w=600&h=400&fit=crop',1,0,'2026-03-24 23:32:15','2026-03-25 03:12:25'),(14,'Craft Beer Flight','Four 4oz pours of rotating Ontario craft beers.',18.00,'Drinks','https://images.unsplash.com/photo-1535958636474-b021ee887b13?w=600&h=400&fit=crop',1,0,'2026-03-24 23:32:15','2026-03-25 03:12:25'),(15,'Seasonal Mocktail','House-made shrubs, local herbs, sparkling water. Rotating monthly.',9.00,'Drinks','https://images.unsplash.com/photo-1544145945-f90425340c7e?w=600&h=400&fit=crop',1,0,'2026-03-24 23:32:15','2026-03-25 03:12:25');
/*!40000 ALTER TABLE `menu_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_requests`
--

DROP TABLE IF EXISTS `password_reset_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_requests` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('pending','resolved') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `temp_password` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `requested_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `resolved_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `password_reset_requests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_requests`
--

LOCK TABLES `password_reset_requests` WRITE;
/*!40000 ALTER TABLE `password_reset_requests` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reservations`
--

DROP TABLE IF EXISTS `reservations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reservations` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `full_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guests` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` date NOT NULL,
  `time` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `special` text COLLATE utf8mb4_unicode_ci,
  `status` enum('pending','confirmed','cancelled') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `reservations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reservations`
--

LOCK TABLES `reservations` WRITE;
/*!40000 ALTER TABLE `reservations` DISABLE KEYS */;
INSERT INTO `reservations` VALUES (1,NULL,'Ayomide','ayomide.olusanyaemmanuel@gmail.com','4 Guests','2026-03-27','7:00 PM','','confirmed','2026-03-25 03:58:14'),(2,1,'Ayomi Olusanya','ayomide.olusanyaemmanuel@gmail.com','2 Guests','2026-03-31','1:00 PM','','pending','2026-03-25 04:28:25'),(3,1,'Ayomi Olusanya','ayomide.olusanyaemmanuel@gmail.com','3 Guests','2026-03-27','1:00 PM','for give away','pending','2026-03-25 05:02:39'),(4,2,'Oluwatobiloba Precious Ogunro','Precioustobiloba0@gmail.com','3 Guests','2026-03-27','6:00 PM','','pending','2026-03-25 05:27:49');
/*!40000 ALTER TABLE `reservations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dietary` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `force_password_change` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Ayomi Olusanya','ayomide.olusanyaemmanuel@gmail.com','$2y$12$3eWutg/uXh3f3BIWopDXY.jq23Fqo0x1WJepqvRtXbgUa.bP8eZ8K','4383476546','','2026-03-25 03:49:51',0),(2,'Oluwatobiloba Precious Ogunro','Precioustobiloba0@gmail.com','$2y$12$sV8xqQV3dhUO4hxQ8PYivOaBwtQJb4E4IKQLib.0n9tOCZxGiwLK.','','','2026-03-25 05:27:13',0);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
SET @@SESSION.SQL_LOG_BIN = @MYSQLDUMP_TEMP_LOG_BIN;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-03-25  0:44:20
