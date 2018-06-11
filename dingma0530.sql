-- phpMyAdmin SQL Dump
-- version 3.5.0-beta1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: May 30, 2018 at 10:42 AM
-- Server version: 5.7.22
-- PHP Version: 7.2.4

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `dingma`
--

-- --------------------------------------------------------

--
-- Table structure for table `dm_activity`
--

CREATE TABLE IF NOT EXISTS `dm_activity` (
  `id` int(20) unsigned NOT NULL AUTO_INCREMENT,
  `test_id` int(20) NOT NULL,
  `user_id` varchar(29) NOT NULL,
  `user_name` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `province` varchar(20) NOT NULL,
  `city` varchar(20) NOT NULL,
  `area` varchar(20) NOT NULL,
  `address` varchar(100) NOT NULL,
  `room_number` int(2) NOT NULL,
  `order_time` int(20) NOT NULL,
  `info` text NOT NULL,
  `status` int(2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=108 ;

--
-- Dumping data for table `dm_activity`
--

INSERT INTO `dm_activity` (`id`, `test_id`, `user_id`, `user_name`, `phone`, `province`, `city`, `area`, `address`, `room_number`, `order_time`, `info`, `status`) VALUES
(106, 2, 'ofhul5KXzYSrV_vwsVWVqV-0ibaM', '', '', '', '', '', '', 0, 0, '', 3),
(107, 1, 'ofhul5KXzYSrV_vwsVWVqV-0ibaM', '', '', '', '', '', '', 0, 0, '', 1);

-- --------------------------------------------------------

--
-- Table structure for table `dm_admin`
--

CREATE TABLE IF NOT EXISTS `dm_admin` (
  `id` int(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(40) NOT NULL,
  `password` varchar(40) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `dm_admin`
--

INSERT INTO `dm_admin` (`id`, `name`, `password`) VALUES
(1, 'dingma', '9300b6c718be229a26a115057e85ce21');

-- --------------------------------------------------------

--
-- Table structure for table `dm_customer`
--

CREATE TABLE IF NOT EXISTS `dm_customer` (
  `id` int(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` varchar(29) NOT NULL,
  `content` text NOT NULL,
  `create_time` int(11) NOT NULL,
  `is_read` int(1) unsigned NOT NULL,
  `reply` text NOT NULL,
  `reply_time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

--
-- Dumping data for table `dm_customer`
--

INSERT INTO `dm_customer` (`id`, `user_id`, `content`, `create_time`, `is_read`, `reply`, `reply_time`) VALUES
(1, '1', 'dsalkfhasasdvjkba;jvwqrjobq;vqdsalkfhasasdvjkba;jvwqrjobq;vqdsalkfhasasdvjkba;jvwqrjobq;vqdsalkfhasasdvjkba;jvwqrjobq;vq', 1525264578, 0, '', 0),
(2, '1', 'dsalkfhasasdvjkba;jvwqrjobq;vqdsalkfhasasdvjkba;jvwqrjobq;vqdsalkfhasasdvjkba;jvwqrjobq;vqdsalkfhasasdvjkba;jvwqrjobq;vq', 1525294578, 0, 'hellowqefqwesdsalkfhasasdvjkba;jvwqrjobq;vqdsalkfhasasdvjkba;jvwqrjobq;vqdsalkfhasasdvjkba;jvwqrjobq;vqdsalkfhasasdvjkba;jvwqrjobq;vqdsalkfhasasdvjkba;jvwqrjobq;vqdsalkfhasasdvjkba;jvwqrjobq;vqdsalkfhasasdvjkba;jvwqrjobq;vqdsalkfhasasdvjkba;jvwqrjobq;vqdsalkfhasasdvjkba;jvwqrjobq;vqdsalkfhasasdvjkba;jvwqrjobq;vqdsalkfhasasdvjkba;jvwqrjobq;vqdsalkfhasasdvjkba;jvwqrjobq;vq', 0),
(3, '1', '1', 1525424647, 0, '', 0),
(4, '1', '我们说中文好不我们说中文好不我们说中文好不我们说中文好不我们说中文好不', 1525424704, 0, '', 0),
(5, '1', '我们说中文好不我们说中文好不我们说中文好不我们说中文好不我们说中文好不我们说中文好不我们说中文好不我们说中文好不我们说中文好不我们说中文好不我们说中文好不我们说中文好不我们说中文好不我们说中文好不我们说中文好不我们说中文好不我们说中文好不我们说中文好不我们说中文好不我们说中文好不', 1525424736, 0, '', 0),
(6, '1', '132512351235123', 1525424884, 0, '', 0),
(7, '1', '5131325`3523', 1525424978, 0, '1251', 1525437751);

-- --------------------------------------------------------

--
-- Table structure for table `dm_friendhelp`
--

CREATE TABLE IF NOT EXISTS `dm_friendhelp` (
  `id` int(40) unsigned NOT NULL AUTO_INCREMENT,
  `activity_id` int(11) NOT NULL,
  `count` int(11) NOT NULL,
  `friend_ids` varchar(800) NOT NULL,
  `is_complete` tinyint(1) NOT NULL,
  `creat_time` int(20) NOT NULL,
  `finish_time` int(20) NOT NULL,
  `user_id` varchar(29) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=13 ;

--
-- Dumping data for table `dm_friendhelp`
--

INSERT INTO `dm_friendhelp` (`id`, `activity_id`, `count`, `friend_ids`, `is_complete`, `creat_time`, `finish_time`, `user_id`) VALUES
(11, 106, 1, '', 0, 1527661575, 0, 'ofhul5KXzYSrV_vwsVWVqV-0ibaM'),
(12, 107, 0, '', 0, 1527661733, 0, 'ofhul5KXzYSrV_vwsVWVqV-0ibaM');

-- --------------------------------------------------------

--
-- Table structure for table `dm_index`
--

CREATE TABLE IF NOT EXISTS `dm_index` (
  `id` int(20) unsigned NOT NULL AUTO_INCREMENT,
  `machine` int(10) NOT NULL,
  `cost` int(10) NOT NULL,
  `family` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `dm_index`
--

INSERT INTO `dm_index` (`id`, `machine`, `cost`, `family`) VALUES
(1, 2212, 1211, 1254);

-- --------------------------------------------------------

--
-- Table structure for table `dm_isread`
--

CREATE TABLE IF NOT EXISTS `dm_isread` (
  `id` int(20) unsigned NOT NULL AUTO_INCREMENT,
  `time` int(20) unsigned NOT NULL,
  `uid` varchar(29) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `dm_isread`
--

INSERT INTO `dm_isread` (`id`, `time`, `uid`) VALUES
(1, 1515444498, '1');

-- --------------------------------------------------------

--
-- Table structure for table `dm_logistical`
--

CREATE TABLE IF NOT EXISTS `dm_logistical` (
  `id` int(20) unsigned NOT NULL AUTO_INCREMENT,
  `test_id` int(20) NOT NULL,
  `user_id` varchar(29) NOT NULL,
  `activity_id` int(11) NOT NULL,
  `send_id` varchar(40) NOT NULL DEFAULT '0',
  `update_time` int(20) NOT NULL,
  `msg` varchar(300) NOT NULL DEFAULT '暂无物流信息',
  `status` int(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `dm_logistical`
--

INSERT INTO `dm_logistical` (`id`, `test_id`, `user_id`, `activity_id`, `send_id`, `update_time`, `msg`, `status`) VALUES
(1, 2, '1', 7, '0', 0, '暂无物流信息', 0),
(2, 1, '1', 11, '0', 0, '暂无物流信息', 0);

-- --------------------------------------------------------

--
-- Table structure for table `dm_notice`
--

CREATE TABLE IF NOT EXISTS `dm_notice` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` text NOT NULL,
  `content` text NOT NULL,
  `ctime` int(20) NOT NULL,
  `status` int(1) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `dm_notice`
--

INSERT INTO `dm_notice` (`id`, `title`, `content`, `ctime`, `status`) VALUES
(1, '我是标题哈哈哈哈', '我是标题哈哈哈哈我是标题哈哈哈哈我是标题哈哈哈哈我是标题哈哈哈哈我是标题哈哈哈哈我是标题哈哈哈哈我是标题哈哈哈哈', 1525432033, 1),
(2, '1111111', '112221122211222112221122211222fasdgdga中文112221122211222112221122211222fasdgdga中文112221122211222112221122211222fasdgdga中文112221122211222112221122211222fasdgdga中文', 0, 1),
(3, '1111', '3125124545234', 1525438628, 1);

-- --------------------------------------------------------

--
-- Table structure for table `dm_result`
--

CREATE TABLE IF NOT EXISTS `dm_result` (
  `id` int(20) unsigned NOT NULL AUTO_INCREMENT,
  `test_id` int(20) NOT NULL,
  `user_id` varchar(29) NOT NULL,
  `submit_rooms` varchar(200) NOT NULL,
  `decorate_time` int(20) NOT NULL,
  `upload_img_url` text NOT NULL,
  `message` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `dm_test`
--

CREATE TABLE IF NOT EXISTS `dm_test` (
  `id` int(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `video_url` text NOT NULL,
  `cover_img_url` text NOT NULL,
  `info` text NOT NULL,
  `status` int(1) NOT NULL DEFAULT '1',
  `detail_img_url` text NOT NULL,
  `friend_count` int(3) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

--
-- Dumping data for table `dm_test`
--

INSERT INTO `dm_test` (`id`, `name`, `video_url`, `cover_img_url`, `info`, `status`, `detail_img_url`, `friend_count`) VALUES
(1, '臭氧检测', 'http://player.youku.com/player.php/sid/XMzE3NjUwMTc0OA==/v.swf', '1.jpg', '', 1, '1.png', 1),
(2, '电磁辐射检测', 'http://player.youku.com/player.php/sid/XMzE3NjUwMTc0OA==/v.swf', '2.jpg', '', 1, '1.png', 1),
(3, '放射性检测', 'http://player.youku.com/player.php/sid/XMzE3NjUwMTc0OA==/v.swf', '3.jpg', '', 1, '1.png', 1),
(4, '化妆品检测', 'http://player.youku.com/player.php/sid/XMzE3NjUwMTc0OA==/v.swf', '4.jpg', '', 1, '1.png', 1),
(5, '甲醛检测', 'http://player.youku.com/player.php/sid/XMzE3NjUwMTc0OA==/v.swf', '5.jpg', '', 1, '1.png', 1),
(6, '牛奶检测', 'http://player.youku.com/player.php/sid/XMzE3NjUwMTc0OA==/v.swf', '6.jpg', '', 1, '1.png', 1),
(7, '瘦肉精检测', 'http://player.youku.com/player.php/sid/XMzE3NjUwMTc0OA==/v.swf', '7.jpg', '', 0, '1.png', 1);

-- --------------------------------------------------------

--
-- Table structure for table `dm_user`
--

CREATE TABLE IF NOT EXISTS `dm_user` (
  `id` int(20) unsigned NOT NULL AUTO_INCREMENT,
  `nick_name` varchar(40) NOT NULL DEFAULT '游客',
  `avatarUrl` text NOT NULL,
  `gender` varchar(3) NOT NULL,
  `city` varchar(10) NOT NULL,
  `province` varchar(10) NOT NULL,
  `country` varchar(10) NOT NULL,
  `language` varchar(10) NOT NULL,
  `union_id` text NOT NULL,
  `session_key` text NOT NULL,
  `open_id` varchar(29) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

--
-- Dumping data for table `dm_user`
--

INSERT INTO `dm_user` (`id`, `nick_name`, `avatarUrl`, `gender`, `city`, `province`, `country`, `language`, `union_id`, `session_key`, `open_id`) VALUES
(7, 'MILLER', 'https://wx.qlogo.cn/mmopen/vi_32/SVLQP1rX7GS6Lict0qZAteXM7rbfmFETrN8uYiaxl31JuFcZ5CYr8aNzWQXC0wFJ5rhx8u2ezOZV3a0EcNFmtp5w/132', '1', '', '', 'Andorra', 'zh_CN', '0', 'CkYVk1RtPKXbTJfFWNxBaw==', 'ofhul5KXzYSrV_vwsVWVqV-0ibaM');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
