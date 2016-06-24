SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for donations
-- ----------------------------
DROP TABLE IF EXISTS `donations`;
CREATE TABLE `donations` (
  `OngId` int(11) NOT NULL,
  `Code` varchar(55) NOT NULL,
  `Status` int(2) NOT NULL,
  `Date` int(11) NOT NULL,
  `Ip` varchar(20) NOT NULL,
  `Message` varchar(255) NOT NULL,
  `RemoteStatus` int(11) NOT NULL,
  PRIMARY KEY (`Code`),
  KEY `OngId` (`OngId`),
  CONSTRAINT `OngId` FOREIGN KEY (`OngId`) REFERENCES `ongs` (`Id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for messages
-- ----------------------------
DROP TABLE IF EXISTS `messages`;
CREATE TABLE `messages` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `Author` varchar(255) NOT NULL,
  `Destination` int(11) NOT NULL,
  `Date` int(11) NOT NULL,
  `Message` text NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ongs
-- ----------------------------
DROP TABLE IF EXISTS `ongs`;
CREATE TABLE `ongs` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `Name` text NOT NULL,
  `CNPJ` varchar(255) NOT NULL,
  `Website` text NOT NULL,
  `Valid` int(11) NOT NULL DEFAULT '0',
  `Password` text NOT NULL,
  `Email` text NOT NULL,
  `CPF` varchar(255) NOT NULL,
  `RemotePassword` text NOT NULL,
  `Address` text NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
