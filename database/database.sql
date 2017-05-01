-- phpMyAdmin SQL Dump
-- version 4.0.10.12
-- http://www.phpmyadmin.net
--
-- Хост: pshacker.mysql.ukraine.com.ua
-- Время создания: Мар 23 2017 г., 09:59
-- Версия сервера: 5.6.27-75.0-log
-- Версия PHP: 5.6.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- База данных: `pshacker_hoots`
--

-- --------------------------------------------------------

--
-- Структура таблицы `tbl_company_photo`
--

CREATE TABLE IF NOT EXISTS `tbl_company_photo` (
  `col_id` int(10) NOT NULL AUTO_INCREMENT,
  `col_company_id` int(7) NOT NULL,
  `col_photo_url` varchar(255) NOT NULL,
  `col_date` datetime NOT NULL,
  `col_title` varchar(255) NOT NULL,
  `col_description` varchar(255) NOT NULL,
  PRIMARY KEY (`col_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `tbl_contests`
--

CREATE TABLE IF NOT EXISTS `tbl_contests` (
  `col_id` int(8) NOT NULL AUTO_INCREMENT,
  `col_title` varchar(255) NOT NULL,
  `col_about` text NOT NULL,
  `col_header_photo` varchar(255) NOT NULL,
  `col_logo` varchar(255) NOT NULL,
  `col_date_start` datetime NOT NULL,
  `col_date_end` datetime NOT NULL,
  `col_company_id` int(7) NOT NULL,
  `col_flag` int(1) NOT NULL,
  PRIMARY KEY (`col_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `tbl_error`
--

CREATE TABLE IF NOT EXISTS `tbl_error` (
  `col_ip` varchar(20) NOT NULL,
  `col_date` datetime NOT NULL,
  `col_number` int(1) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `tbl_feeds`
--

CREATE TABLE IF NOT EXISTS `tbl_feeds` (
  `col_id` int(10) NOT NULL AUTO_INCREMENT,
  `col_profile_id` int(7) NOT NULL,
  `col_date` date NOT NULL,
  `col_text` text NOT NULL,
  `col_img` varchar(255) NOT NULL,
  `col_link` varchar(255) NOT NULL,
  `col_flag` int(1) NOT NULL,
  PRIMARY KEY (`col_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `tbl_likes`
--

CREATE TABLE IF NOT EXISTS `tbl_likes` (
  `col_id` int(10) NOT NULL AUTO_INCREMENT,
  `col_contest_id` int(7) NOT NULL,
  `col_author_id` int(7) NOT NULL,
  `col_user_id` int(7) NOT NULL,
  `col_company_id` int(7) NOT NULL,
  `col_date` datetime NOT NULL,
  PRIMARY KEY (`col_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `tbl_lostpass`
--

CREATE TABLE IF NOT EXISTS `tbl_lostpass` (
  `col_email` varchar(50) DEFAULT NULL,
  `col_uniq_id` varchar(50) NOT NULL,
  `col_date` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `tbl_messages`
--

CREATE TABLE IF NOT EXISTS `tbl_messages` (
  `col_id` int(9) NOT NULL AUTO_INCREMENT,
  `col_from_id` int(11) DEFAULT NULL,
  `col_to_id` int(11) DEFAULT NULL,
  `col_date` datetime DEFAULT NULL,
  `col_text` text NOT NULL,
  `col_flag_from` tinyint(1) DEFAULT NULL,
  `col_flag_to` tinyint(1) DEFAULT NULL,
  `col_flag_new` tinyint(1) DEFAULT NULL,
  `col_dialog_id` varchar(23) DEFAULT NULL,
  PRIMARY KEY (`col_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `tbl_photo`
--

CREATE TABLE IF NOT EXISTS `tbl_photo` (
  `col_id` int(10) NOT NULL AUTO_INCREMENT,
  `col_contest_id` int(8) NOT NULL,
  `col_user_id` int(10) NOT NULL,
  `col_photo_url` varchar(255) NOT NULL,
  PRIMARY KEY (`col_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `tbl_prizes`
--

CREATE TABLE IF NOT EXISTS `tbl_prizes` (
  `col_id` int(9) NOT NULL AUTO_INCREMENT,
  `col_title` varchar(14) NOT NULL,
  `col_description` varchar(20) NOT NULL,
  `col_img` varchar(255) NOT NULL,
  `col_start_winners` int(3) NOT NULL,
  `col_end_winners` int(3) NOT NULL,
  `col_type` int(1) NOT NULL,
  `col_contest_id` int(8) NOT NULL,
  `col_company_id` int(7) NOT NULL,
  PRIMARY KEY (`col_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `tbl_subscriptions`
--

CREATE TABLE IF NOT EXISTS `tbl_subscriptions` (
  `col_id` int(10) NOT NULL AUTO_INCREMENT,
  `company_id` int(7) NOT NULL,
  `user_id` int(7) NOT NULL,
  PRIMARY KEY (`col_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `tbl_users`
--

CREATE TABLE IF NOT EXISTS `tbl_users` (
  `col_id` int(7) NOT NULL AUTO_INCREMENT,
  `col_email` varchar(50) NOT NULL,
  `col_password` varchar(100) NOT NULL,
  `col_company_name` varchar(255) NOT NULL,
  `col_username` varchar(255) NOT NULL,
  `col_lastname` varchar(255) NOT NULL,
  `col_date` date NOT NULL,
  `col_about` text NOT NULL,
  `col_header_photo` varchar(255) NOT NULL,
  `col_avatar` varchar(255) NOT NULL,
  `col_link` varchar(255) NOT NULL,
  `col_location` varchar(255) NOT NULL,
  `col_uid` varchar(100) NOT NULL,
  PRIMARY KEY (`col_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `tbl_wins`
--

CREATE TABLE IF NOT EXISTS `tbl_wins` (
  `col_id` int(7) NOT NULL AUTO_INCREMENT,
  `col_user_id` int(7) NOT NULL,
  `col_contest_id` int(8) NOT NULL,
  `col_prize_id` int(9) NOT NULL,
  `col_rating` int(3) NOT NULL,
  `col_type` int(1) NOT NULL,
  PRIMARY KEY (`col_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
