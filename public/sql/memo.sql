-- phpMyAdmin SQL Dump
-- version 4.6.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: 2019-05-25 07:54:51
-- 服务器版本： 5.7.14
-- PHP Version: 7.0.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `smartz`
--

-- --------------------------------------------------------

--
-- 表的结构 `memo`
--

CREATE TABLE `memo` (
  `id` int(10) UNSIGNED NOT NULL,
  `uid` int(10) UNSIGNED NOT NULL COMMENT '用户ID',
  `type` tinyint(3) UNSIGNED NOT NULL DEFAULT '1' COMMENT '类型',
  `content` varchar(500) COLLATE utf8_unicode_ci NOT NULL COMMENT '内容',
  `is_top` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '是否置顶',
  `status` tinyint(3) UNSIGNED NOT NULL DEFAULT '1' COMMENT '状态（1：等待提示；0：已提示）',
  `deadline` int(10) UNSIGNED NOT NULL COMMENT '提醒时间',
  `gmt_create` int(10) UNSIGNED NOT NULL COMMENT '创建时间',
  `gmt_modified` int(10) UNSIGNED NOT NULL COMMENT '更新时间'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `memo`
--
ALTER TABLE `memo`
  ADD PRIMARY KEY (`id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `memo`
--
ALTER TABLE `memo`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=115;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
