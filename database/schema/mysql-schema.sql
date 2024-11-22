/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `accounts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `account_number` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `achievement`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `achievement` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `desc` varchar(255) NOT NULL,
  `fa_icon` text DEFAULT NULL,
  `tier` varchar(255) NOT NULL,
  `has_page` tinyint(1) NOT NULL,
  `page_name` varchar(255) DEFAULT NULL,
  `page_content` text DEFAULT NULL,
  `is_archived` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `achievement_page_name_unique` (`page_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `achievements_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `achievements_users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `achievement_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `alerted` tinyint(1) NOT NULL DEFAULT 0,
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `activities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `activities` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `event_id` int(11) DEFAULT NULL,
  `price` double DEFAULT NULL,
  `no_show_fee` float NOT NULL DEFAULT 0,
  `participants` int(11) NOT NULL DEFAULT -1,
  `hide_participants` tinyint(1) NOT NULL DEFAULT 0,
  `attendees` int(11) DEFAULT NULL,
  `registration_start` int(11) NOT NULL,
  `registration_end` int(11) NOT NULL,
  `deregistration_end` int(11) NOT NULL,
  `closed` tinyint(1) NOT NULL DEFAULT 0,
  `closed_account` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `comment` varchar(255) DEFAULT NULL,
  `redirect_url` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `activities_event_id_index` (`event_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `activities_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `activities_users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `activity_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `is_present` tinyint(1) NOT NULL DEFAULT 0,
  `committees_activities_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `backup` tinyint(1) NOT NULL DEFAULT 0,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `activities_users_user_id_index` (`user_id`),
  KEY `activities_users_activity_id_index` (`activity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `addresses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `addresses` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `street` varchar(255) NOT NULL,
  `number` varchar(255) NOT NULL,
  `zipcode` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `country` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `alias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `alias` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `alias` varchar(255) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `destination` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `announcements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `announcements` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `display_from` datetime NOT NULL,
  `display_till` datetime NOT NULL,
  `show_guests` tinyint(1) NOT NULL DEFAULT 0,
  `show_users` tinyint(1) NOT NULL DEFAULT 0,
  `show_members` tinyint(1) NOT NULL DEFAULT 1,
  `show_only_homepage` tinyint(1) NOT NULL DEFAULT 1,
  `show_only_new` tinyint(1) NOT NULL DEFAULT 0,
  `show_only_firstyear` tinyint(1) NOT NULL DEFAULT 0,
  `show_only_active` tinyint(1) NOT NULL DEFAULT 0,
  `show_as_popup` tinyint(1) NOT NULL DEFAULT 0,
  `show_style` tinyint(4) NOT NULL DEFAULT 0,
  `is_dismissable` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `bankaccounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bankaccounts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `iban` varchar(255) NOT NULL,
  `bic` varchar(255) NOT NULL,
  `machtigingid` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `codex_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `codex_category` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `codex_codex_song`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `codex_codex_song` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `codex` int(10) unsigned NOT NULL,
  `song` int(10) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `codex_codex_text`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `codex_codex_text` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `codex` int(10) unsigned NOT NULL,
  `text_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `codex_codices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `codex_codices` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `export` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `codex_songs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `codex_songs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` bigint(20) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `artist` varchar(255) DEFAULT NULL,
  `lyrics` longtext NOT NULL,
  `youtube` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `codex_text_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `codex_text_types` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `codex_texts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `codex_texts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `type_id` int(10) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `text` longtext NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `committees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `committees` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `public` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `image_id` int(11) DEFAULT NULL,
  `allow_anonymous_email` tinyint(1) NOT NULL DEFAULT 0,
  `is_society` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `committees_activities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `committees_activities` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `activity_id` int(11) NOT NULL,
  `committee_id` int(11) NOT NULL,
  `amount` int(11) NOT NULL,
  `notification_sent` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `committees_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `committees_users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `committee_id` int(11) NOT NULL,
  `role` varchar(255) DEFAULT NULL,
  `edition` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `companies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `companies` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `excerpt` text NOT NULL,
  `description` text NOT NULL,
  `image_id` int(11) NOT NULL,
  `on_carreer_page` tinyint(1) NOT NULL,
  `in_logo_bar` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `on_membercard` tinyint(1) NOT NULL DEFAULT 0,
  `membercard_excerpt` text DEFAULT NULL,
  `membercard_long` text DEFAULT NULL,
  `sort` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `dinnerform_orderline`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `dinnerform_orderline` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `dinnerform_id` int(11) NOT NULL,
  `description` text NOT NULL,
  `price` double NOT NULL,
  `helper` tinyint(1) NOT NULL,
  `closed` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `dinnerforms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `dinnerforms` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `restaurant` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `start` datetime NOT NULL,
  `end` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `event_id` int(11) DEFAULT NULL,
  `helper_discount` double NOT NULL,
  `regular_discount` double NOT NULL,
  `closed` tinyint(1) NOT NULL,
  `visible_home_page` tinyint(1) NOT NULL DEFAULT 1,
  `ordered_by_user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `dmx_channels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `dmx_channels` (
  `id` int(11) NOT NULL,
  `name` text NOT NULL,
  `special_function` char(10) NOT NULL DEFAULT 'none'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `dmx_fixtures`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `dmx_fixtures` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `channel_start` int(11) NOT NULL,
  `channel_end` int(11) NOT NULL,
  `follow_timetable` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `dmx_overrides`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `dmx_overrides` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fixtures` varchar(255) NOT NULL,
  `color` varchar(255) NOT NULL,
  `start` varchar(255) NOT NULL,
  `end` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `emails`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `emails` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `sender_name` varchar(255) NOT NULL,
  `sender_address` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `to_user` tinyint(1) NOT NULL DEFAULT 0,
  `to_member` tinyint(1) NOT NULL DEFAULT 0,
  `to_pending` tinyint(1) NOT NULL DEFAULT 0,
  `to_list` tinyint(1) NOT NULL DEFAULT 0,
  `to_event` tinyint(1) NOT NULL,
  `to_backup` tinyint(1) NOT NULL DEFAULT 0,
  `to_active` tinyint(1) NOT NULL DEFAULT 0,
  `sent_to` int(11) DEFAULT NULL,
  `sent` tinyint(1) NOT NULL DEFAULT 0,
  `ready` tinyint(1) NOT NULL DEFAULT 0,
  `time` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `emails_events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `emails_events` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `emails_files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `emails_files` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email_id` int(11) NOT NULL,
  `file_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `emails_lists`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `emails_lists` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email_id` int(11) NOT NULL,
  `list_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `event_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `event_categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `icon` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `event_newsitem`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `event_newsitem` (
  `newsitem_id` int(10) unsigned NOT NULL,
  `event_id` int(10) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `events` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `category_id` int(10) unsigned DEFAULT NULL,
  `is_external` tinyint(1) NOT NULL DEFAULT 0,
  `start` int(11) NOT NULL,
  `end` int(11) NOT NULL,
  `publication` int(11) DEFAULT NULL,
  `unique_users_count` int(10) unsigned NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `update_sequence` int(11) NOT NULL DEFAULT 0,
  `location` varchar(255) NOT NULL,
  `maps_location` varchar(255) DEFAULT NULL,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `involves_food` tinyint(1) NOT NULL DEFAULT 0,
  `secret` tinyint(1) NOT NULL DEFAULT 0,
  `force_calendar_sync` tinyint(1) NOT NULL DEFAULT 0,
  `image_id` int(11) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `committee_id` int(11) DEFAULT NULL,
  `summary` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `events_start_index` (`start`),
  KEY `events_end_index` (`end`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) DEFAULT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `feedback`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `feedback` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `feedback_category_id` bigint(20) NOT NULL DEFAULT 1,
  `feedback` text NOT NULL,
  `reviewed` tinyint(1) NOT NULL DEFAULT 0,
  `accepted` tinyint(1) DEFAULT NULL,
  `reply` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `feedback_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `feedback_categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `review` tinyint(1) NOT NULL,
  `reviewer_id` int(11) DEFAULT NULL,
  `show_publisher` tinyint(1) NOT NULL DEFAULT 0,
  `can_reply` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `feedback_votes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `feedback_votes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `feedback_id` int(11) NOT NULL,
  `vote` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `files` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `filename` varchar(255) NOT NULL,
  `mime` varchar(255) NOT NULL,
  `original_filename` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `hash` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `flickr_albums`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `flickr_albums` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `thumb` varchar(255) NOT NULL,
  `date_create` int(11) NOT NULL,
  `date_update` int(11) NOT NULL,
  `date_taken` int(11) DEFAULT NULL,
  `event_id` int(11) DEFAULT NULL,
  `private` tinyint(1) NOT NULL DEFAULT 0,
  `migrated` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `flickr_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `flickr_items` (
  `url` varchar(255) NOT NULL,
  `thumb` varchar(255) NOT NULL,
  `album_id` varchar(255) NOT NULL,
  `id` varchar(255) NOT NULL,
  `date_taken` int(11) NOT NULL,
  `private` tinyint(1) NOT NULL DEFAULT 0,
  `migrated` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`url`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `flickr_likes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `flickr_likes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `photo_id` bigint(20) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `migrated` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `hashmap`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `hashmap` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) NOT NULL,
  `subkey` varchar(255) DEFAULT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `headerimages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `headerimages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `credit_id` int(11) DEFAULT NULL,
  `image_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `joboffers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `joboffers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL,
  `title` text NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `redirect_url` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_reserved_at_index` (`queue`,`reserved_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `leaderboards`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `leaderboards` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `committee_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `featured` tinyint(1) NOT NULL,
  `description` varchar(255) NOT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `points_name` varchar(255) NOT NULL DEFAULT 'points',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `leaderboards_entries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `leaderboards_entries` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `leaderboard_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `points` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `mailinglists`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mailinglists` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `is_member_only` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `members`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `members` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `proto_username` text NOT NULL,
  `membership_form_id` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `is_primary_at_another_association` tinyint(1) NOT NULL DEFAULT 0,
  `until` bigint(20) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `card_printed_on` date DEFAULT NULL,
  `omnomcom_sound_id` int(11) DEFAULT NULL,
  `membership_type` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `menuitems`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `menuitems` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent` int(11) DEFAULT NULL,
  `menuname` varchar(255) NOT NULL,
  `url` varchar(255) DEFAULT NULL,
  `page_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `order` int(11) NOT NULL,
  `is_member_only` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `model_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_permissions` (
  `permission_id` int(10) unsigned NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` int(11) NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `model_has_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_roles` (
  `role_id` int(10) unsigned NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` int(11) NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `mollie_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mollie_transactions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` varchar(255) NOT NULL,
  `mollie_id` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `amount` double NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `payment_url` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `narrowcasting`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `narrowcasting` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `image_id` int(11) DEFAULT NULL,
  `campaign_start` int(11) NOT NULL,
  `campaign_end` int(11) NOT NULL,
  `slide_duration` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `youtube_id` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `newsitems`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `newsitems` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` longtext NOT NULL,
  `featured_image_id` int(11) DEFAULT NULL,
  `published_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `is_weekly` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `oauth_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `oauth_access_tokens` (
  `id` varchar(100) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `client_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `scopes` text DEFAULT NULL,
  `revoked` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `oauth_access_tokens_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `oauth_auth_codes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `oauth_auth_codes` (
  `id` varchar(100) NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `client_id` bigint(20) unsigned NOT NULL,
  `scopes` text DEFAULT NULL,
  `revoked` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `oauth_auth_codes_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `oauth_clients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `oauth_clients` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `secret` varchar(100) DEFAULT NULL,
  `provider` varchar(255) DEFAULT NULL,
  `redirect` text NOT NULL,
  `personal_access_client` tinyint(1) NOT NULL,
  `password_client` tinyint(1) NOT NULL,
  `revoked` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `oauth_clients_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `oauth_personal_access_clients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `oauth_personal_access_clients` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `oauth_refresh_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `oauth_refresh_tokens` (
  `id` varchar(100) NOT NULL,
  `access_token_id` varchar(100) NOT NULL,
  `revoked` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `oauth_refresh_tokens_access_token_id_index` (`access_token_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `orderlines`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orderlines` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `cashier_id` int(11) DEFAULT NULL,
  `product_id` int(11) NOT NULL,
  `original_unit_price` double NOT NULL,
  `units` int(11) NOT NULL,
  `total_price` double NOT NULL,
  `authenticated_by` text NOT NULL,
  `payed_with_cash` datetime DEFAULT NULL,
  `payed_with_bank_card` datetime DEFAULT NULL,
  `payed_with_mollie` int(11) DEFAULT NULL,
  `payed_with_withdrawal` int(11) DEFAULT NULL,
  `payed_with_loss` tinyint(1) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `orderlines_user_id_index` (`user_id`),
  KEY `orderlines_created_at_index` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `content` longtext NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `is_member_only` tinyint(1) NOT NULL DEFAULT 0,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `featured_image_id` int(11) DEFAULT NULL,
  `show_attachments` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pages_files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pages_files` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `page_id` int(10) unsigned NOT NULL,
  `file_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `pages_files_page_id_foreign` (`page_id`),
  KEY `pages_files_file_id_foreign` (`file_id`),
  CONSTRAINT `pages_files_file_id_foreign` FOREIGN KEY (`file_id`) REFERENCES `files` (`id`),
  CONSTRAINT `pages_files_page_id_foreign` FOREIGN KEY (`page_id`) REFERENCES `pages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_resets` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `valid_to` int(11) NOT NULL,
  KEY `password_resets_email_index` (`email`),
  KEY `password_resets_token_index` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `passwordstore`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `passwordstore` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `permission_id` int(11) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `username` text DEFAULT NULL,
  `password` text DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `permission_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permission_role` (
  `permission_id` int(10) unsigned NOT NULL,
  `role_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `permission_role_role_id_foreign` (`role_id`),
  CONSTRAINT `permission_role_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `permission_role_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permissions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `display_name` varchar(255) DEFAULT NULL,
  `guard_name` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_unique` (`name`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `photo_albums`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `photo_albums` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `name` char(255) NOT NULL,
  `date_create` int(11) NOT NULL,
  `date_taken` int(11) NOT NULL,
  `thumb_id` int(11) NOT NULL,
  `event_id` int(11) DEFAULT NULL,
  `private` tinyint(1) NOT NULL,
  `published` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `photo_likes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `photo_likes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `photo_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `photos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `photos` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `file_id` int(11) NOT NULL,
  `album_id` int(11) NOT NULL,
  `date_taken` int(11) NOT NULL,
  `private` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `playedvideos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `playedvideos` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `video_id` varchar(255) NOT NULL,
  `video_title` varchar(255) NOT NULL,
  `spotify_id` varchar(50) DEFAULT NULL,
  `spotify_name` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `product_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `product_wallstreet_drink`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_wallstreet_drink` (
  `wallstreet_drink_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `products` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `account_id` int(11) NOT NULL,
  `image_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `price` double NOT NULL,
  `calories` int(11) NOT NULL DEFAULT 0,
  `supplier_id` text DEFAULT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `preferred_stock` int(11) NOT NULL DEFAULT 0,
  `max_stock` int(11) NOT NULL DEFAULT 0,
  `supplier_collo` int(11) NOT NULL DEFAULT 0,
  `is_visible` tinyint(1) NOT NULL DEFAULT 0,
  `is_alcoholic` tinyint(1) NOT NULL DEFAULT 0,
  `is_visible_when_no_stock` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `products_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `products_categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `qrauth_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `qrauth_requests` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `auth_token` text NOT NULL,
  `qr_token` text NOT NULL,
  `description` text NOT NULL,
  `approved_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `rfid`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rfid` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `card_id` varchar(255) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `role_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_user` (
  `user_id` int(10) unsigned NOT NULL,
  `role_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`user_id`,`role_id`),
  KEY `role_user_role_id_foreign` (`role_id`),
  CONSTRAINT `role_user_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `role_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `display_name` varchar(255) DEFAULT NULL,
  `guard_name` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_unique` (`name`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` text NOT NULL,
  `last_activity` int(11) NOT NULL,
  UNIQUE KEY `sessions_id_unique` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `short_url`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `short_url` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `target` varchar(255) NOT NULL,
  `clicks` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `short_url_url_unique` (`url`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `soundboard_sounds`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `soundboard_sounds` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `file_id` int(11) NOT NULL,
  `hidden` tinyint(1) NOT NULL,
  `name` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `stock_mutations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `stock_mutations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `before` int(11) NOT NULL,
  `after` int(11) NOT NULL,
  `is_bulk` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tempadmins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tempadmins` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `start_at` datetime NOT NULL,
  `end_at` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ticket_purchases`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ticket_purchases` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ticket_id` int(11) NOT NULL,
  `orderline_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `barcode` varchar(255) NOT NULL,
  `scanned` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `payment_complete` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tickets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tickets` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `event_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `members_only` tinyint(1) NOT NULL,
  `available_from` int(11) NOT NULL,
  `available_to` int(11) NOT NULL,
  `is_prepaid` tinyint(1) NOT NULL DEFAULT 0,
  `show_participants` tinyint(1) NOT NULL DEFAULT 0,
  `has_buy_limit` tinyint(1) NOT NULL DEFAULT 0,
  `buy_limit` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tokens` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tokens_token_unique` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `user_welcome`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_welcome` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `calling_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `image_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `diet` text DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `phone_visible` tinyint(1) NOT NULL DEFAULT 0,
  `address_visible` tinyint(1) NOT NULL DEFAULT 0,
  `receive_sms` tinyint(1) NOT NULL DEFAULT 0,
  `keep_protube_history` tinyint(1) NOT NULL DEFAULT 1,
  `show_birthday` tinyint(1) NOT NULL DEFAULT 1,
  `show_achievements` tinyint(1) NOT NULL DEFAULT 1,
  `profile_in_almanac` tinyint(1) NOT NULL DEFAULT 1,
  `show_omnomcom_total` tinyint(1) NOT NULL DEFAULT 0,
  `show_omnomcom_calories` tinyint(1) NOT NULL DEFAULT 0,
  `keep_omnomcom_history` tinyint(1) NOT NULL DEFAULT 1,
  `disable_omnomcom` tinyint(1) NOT NULL DEFAULT 0,
  `theme` int(11) NOT NULL,
  `pref_calendar_alarm` tinyint(1) DEFAULT NULL,
  `pref_calendar_relevant_only` tinyint(1) NOT NULL DEFAULT 0,
  `utwente_username` varchar(255) DEFAULT NULL,
  `edu_username` varchar(255) DEFAULT NULL,
  `utwente_department` text DEFAULT NULL,
  `did_study_create` tinyint(1) NOT NULL DEFAULT 0,
  `did_study_itech` tinyint(1) NOT NULL DEFAULT 0,
  `tfa_totp_key` varchar(255) DEFAULT NULL,
  `signed_nda` tinyint(1) NOT NULL DEFAULT 0,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `personal_key` varchar(64) DEFAULT NULL,
  `discord_id` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `users_mailinglists`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users_mailinglists` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `list_id` int(11) NOT NULL,
  `user_id` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ut_accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ut_accounts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `member_id` bigint(20) unsigned NOT NULL,
  `number` varchar(255) NOT NULL,
  `mail` varchar(255) NOT NULL,
  `department` varchar(255) DEFAULT NULL,
  `givenname` varchar(255) NOT NULL,
  `surname` varchar(255) NOT NULL,
  `found` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `videos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `videos` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `event_id` int(11) DEFAULT NULL,
  `youtube_id` varchar(255) NOT NULL,
  `youtube_title` varchar(255) NOT NULL,
  `youtube_length` varchar(255) NOT NULL,
  `youtube_user_id` varchar(255) NOT NULL,
  `youtube_user_name` varchar(255) NOT NULL,
  `youtube_thumb_url` varchar(255) NOT NULL,
  `video_date` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `wallstreet_drink`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wallstreet_drink` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `end_time` bigint(20) NOT NULL,
  `start_time` bigint(20) NOT NULL,
  `price_increase` double NOT NULL,
  `price_decrease` double NOT NULL,
  `minimum_price` double NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `random_events_chance` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `wallstreet_drink_event`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wallstreet_drink_event` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `wallstreet_drink_id` int(10) unsigned NOT NULL,
  `wallstreet_drink_events_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `wallstreet_drink_event_product`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wallstreet_drink_event_product` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `wallstreet_drink_event_id` int(10) unsigned NOT NULL,
  `product_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `wallstreet_drink_events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wallstreet_drink_events` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `percentage` int(11) NOT NULL,
  `description` text DEFAULT NULL,
  `image_id` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `wallstreet_drink_prices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wallstreet_drink_prices` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `wallstreet_drink_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned NOT NULL,
  `price` double NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `withdrawals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `withdrawals` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `closed` tinyint(1) NOT NULL DEFAULT 0,
  `total_users_associated` int(11) NOT NULL DEFAULT 0,
  `total_orderlines_associated` int(11) NOT NULL DEFAULT 0,
  `sum_associated_orderlines` decimal(10,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `withdrawals_failed`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `withdrawals_failed` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `withdrawal_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `correction_orderline_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (1,'2014_10_12_000000_create_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (2,'2014_10_12_100000_create_password_resets_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (3,'2016_03_09_221221_initialmigration',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (4,'2016_03_09_233319_entrust_setup_tables',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (5,'2016_03_16_190241_activities',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (6,'2016_03_16_193518_committees',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (7,'2016_03_19_205050_alterevents',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (8,'2016_03_20_145717_makeactivityeventidnullable',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (9,'2016_03_20_153401_updateactivity',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (10,'2016_03_30_213121_droputwentes',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (11,'2016_04_10_155223_create_files_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (12,'2016_04_10_160233_add_image_to_committee',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (13,'2016_05_04_223511_add_narrowcasting_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (14,'2016_05_04_232550_create_quote_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (15,'2016_05_15_213137_update_study_users_table_time_as_int',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (16,'2016_05_15_232440_update_committee_membership_date_to_int',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (17,'2016_05_17_205004_add_study_type',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (18,'2016_05_25_192649_convert_events_activities_datetime_to_int',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (19,'2016_05_25_235837_user_profile_picture_column',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (20,'2016_05_26_000653_user_proto_username',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (21,'2016_06_01_000001_create_oauth_auth_codes_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (22,'2016_06_01_000002_create_oauth_access_tokens_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (23,'2016_06_01_000003_create_oauth_refresh_tokens_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (24,'2016_06_01_000004_create_oauth_clients_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (25,'2016_06_01_000005_create_oauth_personal_access_clients_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (26,'2016_06_01_164325_update_quote_type_to_text',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (27,'2016_06_08_185109_create_achievement_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (28,'2016_06_08_202555_edit_activity_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (29,'2016_06_09_114034_move_secret_to_event',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (30,'2016_06_13_194056_new_migration_program_changes',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (31,'2016_06_15_201612_add_image_to_event',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (32,'2016_06_15_214834_update_image_with_hash',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (33,'2016_06_16_224512_two_factor_authentication_tables',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (34,'2016_06_17_233542_move_totp_to_user',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (35,'2016_06_17_233552_add_ubikey_to_user',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (36,'2016_06_21_212547_registration_migration',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (37,'2016_06_22_170307_introducing_softdelete',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (38,'2016_06_22_211743_default_membership_types',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (39,'2016_06_23_112958_omnomcom_migration',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (40,'2016_06_29_211636_update_achievement_img_id_nullable',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (41,'2016_06_29_214555_create_pages_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (42,'2016_06_29_214732_create_menuitems_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (43,'2016_07_04_151755_add_is_member_only_to_pages',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (44,'2016_07_04_175731_add_softdeletes_to_pages',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (45,'2016_07_05_115709_allow_null_user_orderline',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (46,'2016_07_05_151741_make_page_slug_unique',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (47,'2016_07_05_164636_add_featured_image_id_to_pages',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (48,'2016_07_06_155157_make_linking_table_for_files_to_pages',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (49,'2016_07_06_182122_add_rfid_cards',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (50,'2016_07_06_210159_drop_categorie_display',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (51,'2016_07_07_131558_allow_null_menuitems_parent',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (52,'2016_07_07_131816_add_order_to_menuitems',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (53,'2016_07_07_171636_add_member_only_to_menuitems',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (54,'2016_07_13_204444_add_last_membercard_printed',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (55,'2016_07_15_124759_add_old_name_to_activity',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (56,'2016_07_17_145915_add_print_file',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (57,'2016_08_10_152013_create_users_tokens_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (58,'2016_08_17_134114_add_company_profiles',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (59,'2016_08_20_182010_remove_proto_username',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (60,'2016_08_30_193936_create_alias_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (61,'2016_09_04_185820_associate_helping_with_events',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (62,'2016_09_05_141650_add_tempadmin_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (63,'2016_09_08_195023_create_playedvideos_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (64,'2016_09_09_170006_add_email_list_functionality',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (65,'2016_09_11_144751_make_emails_tables',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (66,'2016_09_11_201106_add_newsletter_summary_to_events',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (67,'2016_09_15_213445_make_withdrawals_closable',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (68,'2016_09_21_144940_create_pastries_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (69,'2016_09_22_011617_create__table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (70,'2016_09_22_014934_create_failed_jobs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (71,'2016_10_03_192127_create_user_welcome_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (72,'2016_10_03_200454_make_pages_studies_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (73,'2016_10_03_203327_update_users_table_privacy_name',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (74,'2016_10_03_214359_remove_biography',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (75,'2016_10_03_222908_remove_address_primary',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (76,'2016_10_10_205959_create_passwordentry_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (77,'2016_10_25_145226_add_wizard_boolean_to_users',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (78,'2016_10_25_165147_add_membercard_attributes_to_companies',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (79,'2016_10_25_174847_add_joboffers_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (80,'2016_10_25_220503_update_password_reset_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (81,'2016_10_27_145355_add_video_support_narrowcasting',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (82,'2016_11_18_104215_activity_participants_not_nullable',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (83,'2016_11_28_201203_updateAchievementTable',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (84,'2016_12_02_144039_rename_protomail_to_protousername',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (85,'2016_12_05_211332_add_sort_field_to_companies_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (86,'2016_12_05_215753_add_flickr_albums_and_items',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (87,'2016_12_07_124414_add_dates_to_flickr',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (88,'2016_12_30_200712_add_newsletter_field_to_events',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (89,'2017_01_09_195645_remove_obsolete_tool',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (90,'2017_01_09_212926_add_event_tickets_tables',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (91,'2017_02_06_183022_change_datatype_page_content',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (92,'2017_02_07_154009_create_newsitems_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (93,'2017_02_13_221519_add_mollie_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (94,'2017_02_21_184216_remove_printed_file_column',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (95,'2017_02_23_002809_remove_softdelete_bank',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (96,'2017_03_02_002316_remove_primary_member_column',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (97,'2017_03_09_231235_add_deposit_to_activities',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (98,'2017_03_13_193009_create_quotes_likes_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (99,'2017_03_16_132526_additional_flickr_data',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (100,'2017_03_22_211433_add_event_destination_to_email',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (101,'2017_03_27_200526_albumprivatable',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (102,'2017_04_03_174933_add_diet_to_user',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (103,'2017_04_03_213210_create_config_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (104,'2017_04_20_152043_add_external_column_events',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (105,'2017_05_15_152220_add_prepaid_option_to_tickets',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (106,'2017_05_15_155216_add_payment_url_to_mollie',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (107,'2017_06_12_223703_remove_wizard_column_from_users',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (108,'2017_06_19_212644_add_product_tier_to_category',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (109,'2017_06_26_213129_productdefaultrank',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (110,'2017_06_26_221506_achievementisprize',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (111,'2017_07_03_220332_flickr_add_private_flag',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (112,'2017_07_04_132604_emails_add_active_member_destination',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (113,'2017_07_04_205456_hashmap_store_larger_text',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (114,'2017_09_15_200945_remove_yubikey_column',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (115,'2017_09_22_134929_add_attachment_button_to_pages',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (116,'2017_09_25_223718_remove_unique_page_slug_index',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (117,'2017_09_29_131354_add_anonymous_email_to_committee',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (118,'2017_10_09_102828_add_api_key_to_user',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (119,'2017_10_09_201751_add_radio_station',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (120,'2017_10_09_223627_add_displays',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (121,'2017_10_16_102218_playedvideos_add_spotifyid',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (122,'2017_10_22_154136_update_sessions_for_laravel52',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (123,'2017_10_22_171848_update_jobs_for_laravel53',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (124,'2017_10_23_193905_sessions_in_cookies_only',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (125,'2017_10_23_220112_sessions_back_in_database',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (126,'2017_10_28_100412_add_institution_column_to_user_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (127,'2017_10_28_132818_add_protube_privacy_to_user',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (128,'2017_10_30_215235_add_qrauth_requests_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (129,'2017_11_06_104406_add_present_table_to_activity_participation',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (130,'2017_11_06_220427_users_add_nda_field',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (131,'2017_11_21_210700_add_soundboard_sounds_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (132,'2017_11_28_191955_create_dmx_manager',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (133,'2017_12_05_215829_create_photolikes_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (134,'2017_12_06_174219_extend_dmx_manager',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (135,'2017_12_19_180911_add_study_fields_to_user',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (136,'2018_01_09_205312_drop_nationality_gender_studyhistory',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (137,'2018_02_27_155857_move_settings_to_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (138,'2018_02_27_164436_add_force_sync_for_events',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (139,'2018_03_20_193426_add_helper_reminder_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (140,'2018_05_01_180557_add_birthday_optout',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (141,'2018_06_05_183351_add_email_event_pivot',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (142,'2018_06_05_213518_add_supplier_id_to_product',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (143,'2018_06_12_221653_add_closed_account_to_activities',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (144,'2018_06_26_204526_add_description_to_orderlines',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (145,'2018_06_26_212834_add_omnomcom_total_user_pref',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (146,'2018_06_28_152040_add_failed_withdrawal_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (147,'2018_07_03_183750_add_video_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (148,'2018_09_10_221132_add_user_has_studied_itech',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (149,'2018_09_17_145620_add_announcements_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (150,'2018_10_29_091537_fix_for_github_817_819_820',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (151,'2018_10_29_114355_add_header_images',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (152,'2018_11_14_165117_add_calories_to_products',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (153,'2018_11_14_172300_add_omnomcom_show_calories_to_users',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (154,'2018_11_27_224536_rename_is_prize_to_exclude_from_all_achievements',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (155,'2019_03_19_204609_add_notification_sent_to_committees_activities',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (156,'2019_04_02_212223_add_theme_to_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (157,'2019_04_11_164711_add_omnomcom_block_to_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (158,'2019_06_04_202230_archive_achievements',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (159,'2019_06_04_210748_order_omnomcom',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (160,'2019_07_23_195454_add_pin_to_omnomcom',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (161,'2019_10_22_181949_add_featured_to_event',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (162,'2019_11_24_093900_add_short_url',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (163,'2019_11_29_103335_create_dinnerforms_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (164,'2019_12_17_214522_orderline_paid_audit',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (165,'2019_12_27_220910_create_new_photo_tables',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (166,'2020_02_11_203742_create_societies_column_for_committees',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (167,'2020_02_11_204426_create_good_ideas_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (168,'2020_02_11_204901_create_good_idea_votes_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (169,'2020_06_30_210405_add_profile_in_almanac_to_users',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (170,'2020_07_30_142212_add_membership_form_to_members_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (171,'2020_10_27_212616_add_redirect_url_to_joboffers',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (172,'2020_12_15_210106_create_leaderboards_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (173,'2021_02_10_163004_change_user_theme_to_enum',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (174,'2021_03_06_201232_add_url_to_achievement_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (175,'2021_04_25_140954_change_membership_type',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (176,'2021_04_25_205806_add_pet_membership_type',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (177,'2021_04_26_203140_add_featured_to_leaderboards_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (178,'2021_06_06_183111_create_permission_tables',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (179,'2021_07_11_180444_create_event_categories_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (180,'2021_07_11_181335_add_category_to_events',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (181,'2021_10_03_170304_add_alerted_column_to_achievements_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (182,'2021_12_10_220655_add_is_pending_row_to_emails_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (183,'2022_03_21_161021_add_hide_participants_to_activity',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (184,'2022_05_19_120628_add_to_backup_row_to_emails',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (185,'2022_07_18_010010_create_dinner_orderline_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (186,'2022_09_04_214929_add_events_update_sequency_field',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (187,'2022_09_13_215305_drop_bank_is_first_column',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (188,'2022_09_25_214738_add_visibility_to_dinnerforms',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (189,'2022_10_03_160141_dinnerform_int_to_float',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (190,'2022_10_20_153408_add_publish_time_to_events',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (191,'2022_10_24_152612_add_discount_to_dinnerforms',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (192,'2022_11_11_122455_add_provider_column_to_oauth_clients_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (193,'2022_11_13_161109_add_payed_with_loss_to_orderlines',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (194,'2022_11_18_204950_add_feedback_functionality',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (195,'2022_11_30_175546_add_attendees_to_activities',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (196,'2022_23_10_164620_add_uuid_to_failed_jobs',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (197,'2023_01_06_212940_add_pkce_support_to_oauth_clients',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (198,'2023_01_20_211900_add_member_until_to_member',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (199,'2023_01_31_000437_add_wallstreet_drink',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (200,'2023_02_12_133147_add_custom_omnomcom_sound_to_members',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (201,'2023_03_30_162439_add_discord_id_column_to_users',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (202,'2023_04_23_231630_add_show_participants_to_ticket',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (203,'2023_05_08_143425_add_userid_to_dinnerform',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (204,'2023_05_09_212116_create_stock_mutations',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (205,'2023_05_30_101828_add_redirect_link_to_activities',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (206,'2023_06_25_223210_add_maps_location_to_events_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (207,'2023_08_16_231824_add_is_weekly_to_news',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (208,'2023_08_16_232939_many_to_many_between_events_and_newsitems',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (209,'2023_08_28_160325_add_description_to_achievement_users',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (210,'2023_09_09_195628_add_codex_tool',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (211,'2023_10_05_114449_add_show_publisher_to_feedback_category',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (212,'2023_11_23_120900_add_is_active_to_committees',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (213,'2023_11_30_214933_drop_displays_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (214,'2023_11_30_215014_drop_radio_stations_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (215,'2023_12_07_173710_add_custom_wallstreet_events',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (216,'2024_01_25_223150_remove_committees_helper_reminders_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (217,'2024_02_04_215138_add_random_minutes_to_wallstreet_events',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (218,'2024_02_16_093651_add_active_to_wallstreet_drink_events',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (219,'2024_02_21_154834_add_buy_limit_to_tickets',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (220,'2024_03_12_115002_add_users_count_to_events',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (221,'2024_05_09_011413_alter_codices',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (222,'2024_08_31_214750_add_membership_type_to_members',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (223,'2024_09_01_230659_create_ut_accounts_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (224,'2024_09_04_015210_add_primary_to_members',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (225,'2024_10_19_175537_cleanup_membership_system',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (226,'2024_10_26_224952_add_total_and_sum_to_withdrawals',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (227,'2024_11_03_180047_add_indexes_to_orderlines',1);
