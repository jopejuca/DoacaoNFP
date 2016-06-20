DROP TABLE IF EXISTS `donations`;
CREATE TABLE `donations` (
  `OngId` int(11) NOT NULL,
  `Code` varchar(44) NOT NULL,
  `Status` int(2) NOT NULL,
  `Date` int(11) NOT NULL,
  `Ip` varchar(20) NOT NULL,
  PRIMARY KEY (`Code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

