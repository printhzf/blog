-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- 主机： 127.0.0.1:3306
-- 生成日期： 2019-07-02 07:41:20
-- 服务器版本： 5.7.23
-- PHP 版本： 7.2.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 数据库： `forum`
--

-- --------------------------------------------------------

--
-- 表的结构 `categories`
--

DROP TABLE IF EXISTS `categories`;
CREATE TABLE IF NOT EXISTS `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) COLLATE utf8_unicode_ci NOT NULL COMMENT '板块名称',
  `sort` int(11) NOT NULL COMMENT '版块目排序',
  `status` tinyint(4) NOT NULL COMMENT '状态（1：启用；0：禁用）',
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT '描述',
  `create_time` int(10) NOT NULL,
  `update_time` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='文章版块表';

--
-- 插入之前先把表清空（truncate） `categories`
--

TRUNCATE TABLE `categories`;
--
-- 转存表中的数据 `categories`
--

INSERT INTO `categories` (`id`, `name`, `sort`, `status`, `description`, `create_time`, `update_time`) VALUES
(1, '技术类', 0, 0, '记录开发过程中遇到的问题与解决方案', 0, 0),
(2, '生活类', 0, 0, '记录生活中遇到的趣事与亮点', 0, 0);

-- --------------------------------------------------------

--
-- 表的结构 `collect`
--

DROP TABLE IF EXISTS `collect`;
CREATE TABLE IF NOT EXISTS `collect` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(11) UNSIGNED NOT NULL,
  `topic_id` int(11) UNSIGNED NOT NULL,
  `gmt_create` int(10) NOT NULL COMMENT '创建时间',
  `gmt_modified` int(10) NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=25 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='收藏表';

--
-- 插入之前先把表清空（truncate） `collect`
--

TRUNCATE TABLE `collect`;
--
-- 转存表中的数据 `collect`
--

INSERT INTO `collect` (`id`, `user_id`, `topic_id`, `gmt_create`, `gmt_modified`) VALUES
(24, 2, 2, 1547038530, 1547038530),
(23, 2, 1, 1544622740, 1544622740);

-- --------------------------------------------------------

--
-- 表的结构 `comment`
--

DROP TABLE IF EXISTS `comment`;
CREATE TABLE IF NOT EXISTS `comment` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(11) UNSIGNED NOT NULL,
  `topic_id` int(11) UNSIGNED NOT NULL,
  `content` text COLLATE utf8_unicode_ci NOT NULL COMMENT '评论内容',
  `rely_id` int(11) UNSIGNED NOT NULL COMMENT '回复ID（用于叠楼）',
  `status` tinyint(4) UNSIGNED NOT NULL DEFAULT '1' COMMENT '状态（1：显示；0：隐藏）',
  `gmt_create` int(10) UNSIGNED NOT NULL,
  `update_time` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='评论表';

--
-- 插入之前先把表清空（truncate） `comment`
--

TRUNCATE TABLE `comment`;
-- --------------------------------------------------------

--
-- 表的结构 `illegal`
--

DROP TABLE IF EXISTS `illegal`;
CREATE TABLE IF NOT EXISTS `illegal` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `word` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `type` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- 插入之前先把表清空（truncate） `illegal`
--

TRUNCATE TABLE `illegal`;
-- --------------------------------------------------------

--
-- 表的结构 `like`
--

DROP TABLE IF EXISTS `like`;
CREATE TABLE IF NOT EXISTS `like` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `topic_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='用户点赞表';

--
-- 插入之前先把表清空（truncate） `like`
--

TRUNCATE TABLE `like`;
-- --------------------------------------------------------

--
-- 表的结构 `memo`
--

DROP TABLE IF EXISTS `memo`;
CREATE TABLE IF NOT EXISTS `memo` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` int(10) UNSIGNED NOT NULL COMMENT '用户ID',
  `type` tinyint(3) UNSIGNED NOT NULL DEFAULT '1' COMMENT '类型',
  `content` varchar(500) COLLATE utf8_unicode_ci NOT NULL COMMENT '内容',
  `is_top` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '是否置顶',
  `status` tinyint(3) UNSIGNED NOT NULL DEFAULT '1' COMMENT '状态（1：等待提示；0：已提示）',
  `deadline` int(10) UNSIGNED NOT NULL COMMENT '提醒时间',
  `gmt_create` int(10) UNSIGNED NOT NULL COMMENT '创建时间',
  `gmt_modified` int(10) UNSIGNED NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=115 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- 插入之前先把表清空（truncate） `memo`
--

TRUNCATE TABLE `memo`;
-- --------------------------------------------------------

--
-- 表的结构 `notice`
--

DROP TABLE IF EXISTS `notice`;
CREATE TABLE IF NOT EXISTS `notice` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL COMMENT '通知者ID',
  `topic_id` int(10) UNSIGNED NOT NULL COMMENT '帖子标题',
  `reply_id` int(10) UNSIGNED NOT NULL COMMENT '回复ID',
  `reply_content` varchar(500) COLLATE utf8_unicode_ci NOT NULL COMMENT '回复内容',
  `gmt_create` int(10) UNSIGNED NOT NULL COMMENT '创建时间',
  `gmt_modified` int(10) UNSIGNED NOT NULL COMMENT '更新时间'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='用户消息推送表';

--
-- 插入之前先把表清空（truncate） `notice`
--

TRUNCATE TABLE `notice`;
-- --------------------------------------------------------

--
-- 表的结构 `reply`
--

DROP TABLE IF EXISTS `reply`;
CREATE TABLE IF NOT EXISTS `reply` (
  `id` int(10) UNSIGNED NOT NULL COMMENT '回复ID',
  `user_id` int(10) UNSIGNED NOT NULL COMMENT '评论者ID',
  `topic_id` int(10) UNSIGNED NOT NULL COMMENT '帖子ID',
  `topic_title` varchar(100) COLLATE utf8_unicode_ci NOT NULL COMMENT '帖子标题',
  `reply_pid` int(10) UNSIGNED NOT NULL COMMENT '回复父ID',
  `reply_content` varchar(500) COLLATE utf8_unicode_ci NOT NULL COMMENT '回复内容',
  `gmt_create` int(10) UNSIGNED NOT NULL COMMENT '创建时间',
  `gmt_modified` int(10) UNSIGNED NOT NULL COMMENT '更新时间'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='用户回复表';

--
-- 插入之前先把表清空（truncate） `reply`
--

TRUNCATE TABLE `reply`;
-- --------------------------------------------------------

--
-- 表的结构 `think_auth_group`
--

DROP TABLE IF EXISTS `think_auth_group`;
CREATE TABLE IF NOT EXISTS `think_auth_group` (
  `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` char(100) NOT NULL DEFAULT '' COMMENT '用户组名称',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态：为1正常，为0禁用',
  `rules` char(80) NOT NULL DEFAULT '' COMMENT '用户组对应的权限id（控制器方法的id）',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8 COMMENT='角色权限表';

--
-- 插入之前先把表清空（truncate） `think_auth_group`
--

TRUNCATE TABLE `think_auth_group`;
--
-- 转存表中的数据 `think_auth_group`
--

INSERT INTO `think_auth_group` (`id`, `title`, `status`, `rules`) VALUES
(1, '超级管理员', 1, '1,2'),
(2, '普通管理员', 1, '1,2'),
(15, 'test', 1, '1,2');

-- --------------------------------------------------------

--
-- 表的结构 `think_auth_group_access`
--

DROP TABLE IF EXISTS `think_auth_group_access`;
CREATE TABLE IF NOT EXISTS `think_auth_group_access` (
  `uid` mediumint(8) UNSIGNED NOT NULL COMMENT '用户ID',
  `group_id` mediumint(8) UNSIGNED NOT NULL COMMENT '用户组ID',
  UNIQUE KEY `uid_group_id` (`uid`,`group_id`),
  KEY `uid` (`uid`),
  KEY `group_id` (`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='角色表';

--
-- 插入之前先把表清空（truncate） `think_auth_group_access`
--

TRUNCATE TABLE `think_auth_group_access`;
--
-- 转存表中的数据 `think_auth_group_access`
--

INSERT INTO `think_auth_group_access` (`uid`, `group_id`) VALUES
(1, 1),
(2, 2),
(4, 1),
(5, 2),
(6, 2),
(22, 1);

-- --------------------------------------------------------

--
-- 表的结构 `think_auth_rule`
--

DROP TABLE IF EXISTS `think_auth_rule`;
CREATE TABLE IF NOT EXISTS `think_auth_rule` (
  `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` char(80) NOT NULL DEFAULT '' COMMENT '控制器下的方法名',
  `title` char(20) NOT NULL DEFAULT '' COMMENT '方法中文名',
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '认证方式，1为实时认证；2为登录认证。',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态：为1正常，为0禁用',
  `condition` char(100) NOT NULL DEFAULT '' COMMENT '规则表达式，为空表示存在就验证，不为空表示按照条件验证',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='权限表';

--
-- 插入之前先把表清空（truncate） `think_auth_rule`
--

TRUNCATE TABLE `think_auth_rule`;
--
-- 转存表中的数据 `think_auth_rule`
--

INSERT INTO `think_auth_rule` (`id`, `name`, `title`, `type`, `status`, `condition`) VALUES
(1, '/cms/User/UserList', '用户列表', 1, 1, ''),
(2, '/cms/Tool/uploadImg', '图片上传', 1, 1, '');

-- --------------------------------------------------------

--
-- 表的结构 `topic`
--

DROP TABLE IF EXISTS `topic`;
CREATE TABLE IF NOT EXISTS `topic` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT '帖子标题',
  `title_img` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '标题图片',
  `brief` char(30) COLLATE utf8_unicode_ci NOT NULL COMMENT '帖子简介',
  `body` text COLLATE utf8_unicode_ci NOT NULL COMMENT '帖子内容',
  `is_top` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否置顶',
  `is_hot` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否热门',
  `user_id` int(11) UNSIGNED NOT NULL COMMENT '用户ID',
  `category_id` int(11) UNSIGNED NOT NULL COMMENT '分类ID',
  `tag` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '标签',
  `title_status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '显示状态（1：显示；0：隐藏）',
  `view_count` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '查看总数',
  `reply_count` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '回复数量',
  `last_reply_user_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '最后回复的用户ID',
  `gmt_create` int(10) UNSIGNED NOT NULL COMMENT '创建时间',
  `gmt_modified` int(10) UNSIGNED NOT NULL COMMENT '更新时间',
  `order` int(11) NOT NULL DEFAULT '0' COMMENT '用于排序',
  `excerpt` text COLLATE utf8_unicode_ci COMMENT '用于SEO优化',
  `slug` int(11) DEFAULT NULL COMMENT 'SEO友好URL',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='帖子表';

--
-- 插入之前先把表清空（truncate） `topic`
--

TRUNCATE TABLE `topic`;
-- --------------------------------------------------------

--
-- 表的结构 `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `gender` tinyint(3) UNSIGNED NOT NULL DEFAULT '2' COMMENT '性别（0代表女，1代表男，2代表保密）',
  `email` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `head_img` varchar(100) COLLATE utf8_unicode_ci DEFAULT '0' COMMENT '用户头像',
  `userip` int(10) NOT NULL COMMENT '注册IP',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态（0-冻结，1-正常）',
  `notification_count` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '未读通知数量',
  `gmt_create` int(10) DEFAULT '0' COMMENT '创建时间',
  `gmt_modified` int(10) DEFAULT '0' COMMENT '更新时间',
  `last_login_ip` int(10) DEFAULT '0' COMMENT '上次登录IP',
  `last_login_time` int(10) DEFAULT '0' COMMENT '上次登录时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=25 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='用户表';

--
-- 插入之前先把表清空（truncate） `user`
--

TRUNCATE TABLE `user`;
--
-- 转存表中的数据 `user`
--

INSERT INTO `user` (`id`, `username`, `password`, `gender`, `email`, `head_img`, `userip`, `status`, `notification_count`, `gmt_create`, `gmt_modified`, `last_login_ip`, `last_login_time`) VALUES
(1, 'printhzf', '1', 2, '111@qq.com', '0', 0, 1, 0, 0, 0, 0, 0),
(2, '2', 'c81e728d9d4c2f636f067f89cc14862c', 1, '222@qq.com', '0', 0, 1, 0, 0, 1547965311, 2130706433, 1547965311),
(4, 'test', 'e10adc3949ba59abbe56e057f20f883e', 2, 'test@qq.com', '0', 0, 1, 0, 0, 0, 0, 0),
(5, 'test2', 'e10adc3949ba59abbe56e057f20f883e', 2, 'test2@qq.com', '0', 0, 1, 0, 0, 0, 0, 0),
(6, 'test3', 'e10adc3949ba59abbe56e057f20f883e', 2, 'test3@qq.com', '0', 0, 1, 0, 0, 0, 0, 0),
(22, '3', '3', 2, '3', '0', 0, 1, 0, 0, 0, 0, 0),
(24, '0', '0', 2, 'cfcd208495d565ef66e7dff9f98764da', '0', 2130706433, 1, 0, 1542185924, 1542185924, 0, 0);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
