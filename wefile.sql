-- phpMyAdmin SQL Dump
-- version 4.4.15.10
-- https://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: 2018-10-07 01:49:51
-- 服务器版本： 5.6.37-log
-- PHP Version: 5.4.45

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `wefile`
--

DELIMITER $$
--
-- 存储过程
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `FileClear`()
    MODIFIES SQL DATA
    SQL SECURITY INVOKER
DELETE
FROM file
WHERE RestTime = 0$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetFilePath`(IN `_ID` BIGINT, IN `_PrivateKey` TINYTEXT)
    NO SQL
SELECT CONCAT(ID,'/',Name) AS Path
FROM file
WHERE ID=_ID AND PrivateKey=_PrivateKey$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetInvalidList`()
    NO SQL
SELECT CONCAT(ID,'/',Name) AS FilePath,ID AS FolderPath
FROM file
WHERE RestTime = 0$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetPageNum_PRI`(IN `_key` TINYTEXT)
    NO SQL
SELECT floor(abs(COUNT(*)-1)/5)+1 AS NUM
FROM file
WHERE IsPublic=0 AND PrivateKey=_key AND RestTime > 0$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetPageNum_PUB`(IN `_FName` TINYTEXT)
    READS SQL DATA
SELECT floor(abs(COUNT(*)-1)/5)+1 AS NUM
FROM file
WHERE IsPublic = 1 AND Name LIKE CONCAT('%',_FName,'%') AND RestTime > 0$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetPrivateByKey`(IN `_key` TINYTEXT, IN `_offset` INT)
    NO SQL
SELECT *
FROM file
WHERE IsPublic=0 AND PrivateKey=_key AND RestTime > 0 AND Ready = 1
LIMIT _offset , 5$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetPublicList`(IN `_offset` INT, IN `_FName` TINYTEXT)
    READS SQL DATA
    SQL SECURITY INVOKER
SELECT *
FROM file
WHERE IsPublic = 1 AND Name LIKE CONCAT('%',_FName,'%') AND RestTime > 0 AND Ready = 1
LIMIT _offset , 5$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetThisID`()
    READS SQL DATA
SELECT LAST_INSERT_ID() AS ID$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `IsUniqueKey`(IN `_key` TINYTEXT)
    NO SQL
SELECT (COUNT(*)+1)%2 AS Result
FROM file
WHERE PrivateKey = _key$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `NewFile`(IN `_Name` TEXT, IN `_Size` FLOAT, IN `_Unit` TEXT, IN `_Addition` TINYTEXT, IN `_IsPublic` BOOLEAN, IN `_PrivateKey` TINYTEXT)
    MODIFIES SQL DATA
INSERT 
INTO file(Name,Size,Unit,Addition,IsPublic,PrivateKey) 
VALUES(_Name,_Size,_Unit,_Addition,_IsPublic,_PrivateKey)$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `ResumeIndex`()
    MODIFIES SQL DATA
    SQL SECURITY INVOKER
alter table file auto_increment=1$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SetReady`(IN `_ID` BIGINT)
    NO SQL
UPDATE file
SET Ready = 1
WHERE ID = _ID$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `TimeDEC`()
    MODIFIES SQL DATA
UPDATE file
SET RestTime = RestTime-1
WHERE RestTime > 0 AND Ready = 1$$

DELIMITER ;

-- --------------------------------------------------------

--
-- 表的结构 `file`
--

CREATE TABLE IF NOT EXISTS `file` (
  `ID` bigint(20) unsigned NOT NULL,
  `Name` text NOT NULL,
  `Unit` text NOT NULL,
  `UpTime` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `Size` float NOT NULL,
  `RestTime` int(11) DEFAULT '30',
  `Addition` tinytext,
  `IsPublic` tinyint(1) DEFAULT '1',
  `PrivateKey` tinytext NOT NULL,
  `Ready` tinyint(1) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `file`
--
ALTER TABLE `file`
  ADD PRIMARY KEY (`ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `file`
--
ALTER TABLE `file`
  MODIFY `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT;
DELIMITER $$
--
-- 事件
--
CREATE DEFINER=`root`@`localhost` EVENT `AutoUpdateFileRestTime` ON SCHEDULE EVERY 1 MINUTE STARTS '2018-10-03 16:31:56' ON COMPLETION NOT PRESERVE ENABLE DO CALL TimeDEC()$$

DELIMITER ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
