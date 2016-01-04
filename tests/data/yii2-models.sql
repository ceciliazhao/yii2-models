-- phpMyAdmin SQL Dump
-- version 4.5.3.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: 2016-01-04 18:15:40
-- 服务器版本： 5.7.9
-- PHP Version: 5.6.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+08:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `yii2-models`
--
CREATE DATABASE IF NOT EXISTS `yii2-models` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `yii2-models`;

-- --------------------------------------------------------

--
-- 表的结构 `user`
--
-- 创建时间： 2016-01-04 08:20:50
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `guid` varchar(36) NOT NULL,
  `id` int(14) UNSIGNED NOT NULL DEFAULT '0',
  `pass_hash` varchar(80) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `create_time` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
  `update_time` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
  `ip_1` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `ip_2` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `ip_3` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `ip_4` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `ip_type` tinyint(3) UNSIGNED NOT NULL DEFAULT '4',
  `auth_key` varchar(255) NOT NULL,
  `access_token` varchar(255) NOT NULL,
  `status` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `source` varchar(255) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `user_email`
--
-- 创建时间： 2016-01-04 08:21:06
-- 最后更新： 2016-01-03 03:14:42
--

DROP TABLE IF EXISTS `user_email`;
CREATE TABLE `user_email` (
  `guid` varchar(36) NOT NULL,
  `user_guid` varchar(36) NOT NULL,
  `id` varchar(8) NOT NULL,
  `email` varchar(255) NOT NULL,
  `create_time` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
  `update_time` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
  `confirmed` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `confirm_time` datetime NOT NULL DEFAULT '1970-01-01 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`guid`),
  ADD UNIQUE KEY `user_id_unique` (`id`),
  ADD UNIQUE KEY `user_auth_key_unique` (`auth_key`) USING BTREE,
  ADD UNIQUE KEY `user_access_token_unique` (`access_token`);

--
-- Indexes for table `user_email`
--
ALTER TABLE `user_email`
  ADD PRIMARY KEY (`guid`),
  ADD UNIQUE KEY `user_email_id_unique` (`user_guid`,`id`);

--
-- 限制导出的表
--

--
-- 限制表 `user_email`
--
ALTER TABLE `user_email`
  ADD CONSTRAINT `user_guid_fkey` FOREIGN KEY (`user_guid`) REFERENCES `user` (`guid`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
