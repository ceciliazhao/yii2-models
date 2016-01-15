-- phpMyAdmin SQL Dump
-- version 4.5.3.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: 2016-01-15 23:10:59
-- 服务器版本： 5.7.10
-- PHP Version: 5.6.17

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `yii2-models`
--
CREATE DATABASE IF NOT EXISTS `yii2-models` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `yii2-models`;

-- --------------------------------------------------------

--
-- 表的结构 `user`
--
-- 创建时间： 2016-01-11 12:58:55
-- 最后更新： 2016-01-15 14:55:22
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `guid` varchar(36) NOT NULL,
  `id` int(14) UNSIGNED NOT NULL DEFAULT '0',
  `pass_hash` varchar(80) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
  `create_time` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
  `update_time` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
  `ip_1` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `ip_2` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `ip_3` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `ip_4` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `ip_type` tinyint(3) UNSIGNED NOT NULL DEFAULT '4',
  `auth_key` varchar(40) NOT NULL DEFAULT '',
  `access_token` varchar(40) NOT NULL DEFAULT '',
  `password_reset_token` varchar(40) NOT NULL DEFAULT '',
  `status` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `source` varchar(255) NOT NULL DEFAULT '0',
  PRIMARY KEY (`guid`),
  UNIQUE KEY `user_id_unique` (`id`),
  UNIQUE KEY `user_auth_key_unique` (`auth_key`) USING BTREE,
  UNIQUE KEY `user_access_token_unique` (`access_token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `user_additional_account`
--
-- 创建时间： 2016-01-11 17:21:48
-- 最后更新： 2016-01-15 14:55:00
--

DROP TABLE IF EXISTS `user_additional_account`;
CREATE TABLE IF NOT EXISTS `user_additional_account` (
  `guid` varchar(36) NOT NULL,
  `user_guid` varchar(36) NOT NULL,
  `id` varchar(8) NOT NULL,
  `pass_hash` varchar(60) NOT NULL DEFAULT '',
  `enable_login` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `create_time` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
  `update_time` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
  PRIMARY KEY (`guid`),
  UNIQUE KEY `user_email_id_unique` (`user_guid`,`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `user_email`
--
-- 创建时间： 2016-01-14 08:30:22
-- 最后更新： 2016-01-15 14:55:00
--

DROP TABLE IF EXISTS `user_email`;
CREATE TABLE IF NOT EXISTS `user_email` (
  `guid` varchar(36) NOT NULL,
  `user_guid` varchar(36) NOT NULL,
  `id` varchar(8) NOT NULL,
  `email` varchar(255) NOT NULL,
  `type` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `create_time` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
  `update_time` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
  `confirmed` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `confirm_time` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
  `confirm_code` varchar(8) NOT NULL,
  PRIMARY KEY (`guid`),
  UNIQUE KEY `user_email_id_unique` (`user_guid`,`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `user_relation`
--
-- 创建时间： 2016-01-15 11:31:11
-- 最后更新： 2016-01-15 14:55:22
--

DROP TABLE IF EXISTS `user_relation`;
CREATE TABLE IF NOT EXISTS `user_relation` (
  `guid` varchar(36) NOT NULL,
  `user_guid` varchar(36) NOT NULL,
  `remark` varchar(255) NOT NULL DEFAULT '',
  `other_guid` varchar(36) NOT NULL,
  `type` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `favorite` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `ip_1` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `ip_2` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `ip_3` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `ip_4` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `ip_type` tinyint(3) UNSIGNED NOT NULL DEFAULT '4',
  `create_time` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
  `update_time` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
  `groups` text NOT NULL,
  PRIMARY KEY (`guid`),
  UNIQUE KEY `user_other_guid_unique` (`user_guid`,`other_guid`),
  KEY `relation_other_guid_fkey` (`other_guid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `user_relation_group`
--
-- 创建时间： 2016-01-15 13:54:14
-- 最后更新： 2016-01-15 14:55:22
--

DROP TABLE IF EXISTS `user_relation_group`;
CREATE TABLE IF NOT EXISTS `user_relation_group` (
  `guid` varchar(36) NOT NULL,
  `user_guid` varchar(36) NOT NULL,
  `content` varchar(255) NOT NULL DEFAULT '' COMMENT 'group name',
  `update_time` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
  `create_time` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
  PRIMARY KEY (`guid`),
  KEY `relation_group_user_guid_fkey` (`user_guid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 限制导出的表
--

--
-- 限制表 `user_additional_account`
--
ALTER TABLE `user_additional_account`
  ADD CONSTRAINT `user_additional_account_ibfk_1` FOREIGN KEY (`user_guid`) REFERENCES `user` (`guid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- 限制表 `user_email`
--
ALTER TABLE `user_email`
  ADD CONSTRAINT `email_user_guid_fkey` FOREIGN KEY (`user_guid`) REFERENCES `user` (`guid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- 限制表 `user_relation`
--
ALTER TABLE `user_relation`
  ADD CONSTRAINT `relation_other_guid_fkey` FOREIGN KEY (`other_guid`) REFERENCES `user` (`guid`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `relation_user_guid_fkey` FOREIGN KEY (`user_guid`) REFERENCES `user` (`guid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- 限制表 `user_relation_group`
--
ALTER TABLE `user_relation_group`
  ADD CONSTRAINT `relation_group_user_guid_fkey` FOREIGN KEY (`user_guid`) REFERENCES `user` (`guid`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
