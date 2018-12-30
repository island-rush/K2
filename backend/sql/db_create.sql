-- Database Creation for K2.5

DROP DATABASE IF EXISTS islandRushDB2;
CREATE DATABASE islandRushDB2;
USE islandRushDB2;
SET SQL_SAFE_UPDATES = 0;

CREATE TABLE IF NOT EXISTS `games`(
  `gameId` int(3) NOT NULL AUTO_INCREMENT,
  `gameSection` varchar(5) NOT NULL,
  `gameInstructor` varchar(32) NOT NULL,
  `gameAdminPassword` varchar(32) NOT NULL DEFAULT 'c4a276e907f10b988d593fcd573a3cba',
  `gameActive` int(1) NOT NULL DEFAULT 0,
  `gameRedJoined` int(1) NOT NULL DEFAULT 0,
  `gameBlueJoined` int(1) NOT NULL DEFAULT 0,
  `gameCurrentTeam`  varchar(4) NOT NULL DEFAULT 'Blue',
  `gameTurn` int(4) NOT NULL DEFAULT 0, 
  `gamePhase`  int(1) NOT NULL DEFAULT 0,
  `gameRedRpoints` int(4) NOT NULL DEFAULT 10,
  `gameBlueRpoints` int(4) NOT NULL DEFAULT 60,
  `gameRedHpoints` int(3) NOT NULL DEFAULT 0,
  `gameBlueHpoints` int(3) NOT NULL DEFAULT 0,
  `gameBattleSection` varchar(14) NOT NULL DEFAULT 'none',
  `gameBattleSubSection` varchar(20) NOT NULL DEFAULT 'choosing_pieces',
  `gameBattleTurn` int(3) NOT NULL DEFAULT 0,
  `gameBattleLastRoll` int(1) NOT NULL DEFAULT 1,
  `gameBattleLastMessage` varchar(80) DEFAULT 'DEFAULT LAST MESSAGE',
  `gameBattlePosSelected` int(4) NOT NULL DEFAULT -1,
  `gameIsland1` varchar(5) NOT NULL DEFAULT 'Red',
  `gameIsland2` varchar(5) NOT NULL DEFAULT 'Red',
  `gameIsland3` varchar(5) NOT NULL DEFAULT 'Red',
  `gameIsland4` varchar(5) NOT NULL DEFAULT 'Red',
  `gameIsland5` varchar(5) NOT NULL DEFAULT 'Red',
  `gameIsland6` varchar(5) NOT NULL DEFAULT 'Red',
  `gameIsland7` varchar(5) NOT NULL DEFAULT 'Red',
  `gameIsland8` varchar(5) NOT NULL DEFAULT 'Red',
  `gameIsland9` varchar(5) NOT NULL DEFAULT 'Red',
  `gameIsland10` varchar(5) NOT NULL DEFAULT 'Red',
  `gameIsland11` varchar(5) NOT NULL DEFAULT 'Red',
  `gameIsland12` varchar(5) NOT NULL DEFAULT 'Red',
  `gameIsland13` varchar(5) NOT NULL DEFAULT 'Red',
  `gameIsland14` varchar(5) NOT NULL DEFAULT 'Blue',
    PRIMARY KEY(`gameId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

INSERT INTO `games` (gameSection, gameInstructor, gameAdminPassword, gameActive) VALUES ('M1A1', 'Adolph', 	'5f4dcc3b5aa765d61d8327deb882cf99', 1);

CREATE TABLE IF NOT EXISTS `units`(
	`unitId` int(2) NOT NULL ,
    `unitName` varchar(25) NOT NULL,
    `unitTerrain` varchar(8) NOT NULL,
    `unitMoves` int(2) NOT NULL,
    `unitCost` int(2) NOT NULL,
    PRIMARY KEY(`unitId`)
);
INSERT INTO `units` VALUES (0, 'Transport', 'water', 2, 8);
INSERT INTO `units` VALUES (1, 'Submarine', 'water', 2, 8);
INSERT INTO `units` VALUES (2, 'Destroyer', 'water', 2, 10);
INSERT INTO `units` VALUES (3, 'AircraftCarrier', 'water', 2, 15);
INSERT INTO `units` VALUES (4, 'ArmyCompany', 'land', 1, 4);
INSERT INTO `units` VALUES (5, 'ArtilleryBattery', 'land', 1, 5);
INSERT INTO `units` VALUES (6, 'TankPlatoon', 'land', 1, 6);
INSERT INTO `units` VALUES (7, 'MarinePlatoon', 'land', 1, 5);
INSERT INTO `units` VALUES (8, 'MarineConvoy', 'land', 2, 8);
INSERT INTO `units` VALUES (9, 'AttackHelo', 'air', 3, 7);
INSERT INTO `units` VALUES (10, 'SAM', 'land', 1, 8);
INSERT INTO `units` VALUES (11, 'FighterSquadron', 'air', 4, 12);
INSERT INTO `units` VALUES (12, 'BomberSquadron', 'air', 6, 12);
INSERT INTO `units` VALUES (13, 'StealthBomberSquadron', 'air', 5, 15);
INSERT INTO `units` VALUES (14, 'Tanker', 'air', 5, 11);
INSERT INTO `units` VALUES (15, 'LandBasedSeaMissile', 'missile', 0, 10);

CREATE TABLE IF NOT EXISTS `placements`(
	`placementId` int(8) NOT NULL AUTO_INCREMENT,
    `placementGameId` int(3) NOT NULL,
    `placementUnitId` int(2) NOT NULL,
    `placementTeamId` varchar(5) NOT NULL,
	`placementContainerId` int(8) NOT NULL DEFAULT -1,
    `placementCurrentMoves` int(2) NOT NULL,
    `placementPositionId` int(3) NOT NULL,
    `placementBattleUsed` int(1) NOT NULL DEFAULT 0,
    PRIMARY KEY(`placementId`),
    FOREIGN KEY (placementUnitId) REFERENCES units(unitId),
    FOREIGN KEY (placementGameId) REFERENCES games(gameId)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `movements`(
	`movementId` int(16) NOT NULL AUTO_INCREMENT,
    `movementGameId` int(5) NOT NULL,
    `movementFromPosition` int(3) NOT NULL,
    `movementFromContainer` int(8),
    `movementNowPlacement` int(8) NOT NULL,
    PRIMARY KEY(`movementId`),
    FOREIGN KEY (movementGameId) REFERENCES games(gameId)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `battlePieces`(
	`battlePieceId` int(8) NOT NULL,
    `battleGameId` int(3) NOT NULL,
	`battlePieceState` int(1) NOT NULL,
    `battlePieceWasHit` int(1) NOT NULL DEFAULT 0,
    PRIMARY KEY(`battlePieceId`)
);

CREATE TABLE IF NOT EXISTS `updates`(
	`updateId` int(16) NOT NULL AUTO_INCREMENT,
	`updateGameId` int(5) NOT NULL,
	`updateType` varchar(15), -- phaseChange,
	`updatePlacementId` int(8) DEFAULT 0,
	`updateNewPositionId` int(3) DEFAULT 0,
	`updateNewContainerId` int(8) DEFAULT 0,
    `updateHTML` varchar(16000) DEFAULT 'defaultString',
	PRIMARY KEY(`updateId`)
 ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;
 
CREATE TABLE IF NOT EXISTS `newsAlerts`(
  `newsId` int(8) NOT NULL AUTO_INCREMENT,
  `newsGameId` int(3) NOT NULL,
  `newsOrder` int(3) NOT NULL,
  `newsTeam` varchar(10) NOT NULL DEFAULT 'nothing',
  `newsPieces` varchar(350) NOT NULL DEFAULT 'nothing',
  `newsEffect` varchar(20) NOT NULL,
  `newsRollValue` varchar(2) NOT NULL DEFAULT 0,
  `newsZone` int(5) NOT NULL DEFAULT 666,
  `newsLength` int(10) NOT NULL DEFAULT 1,
  `newsHumanitarian` int(1) NOT NULL DEFAULT 0,
  `newsText` varchar(200) NOT NULL DEFAULT 'default string',
  `newsEffectText` varchar(200) NOT NULL DEFAULT 'default string',
  `newsActivated` int(1) NOT NULL DEFAULT 0,
  PRIMARY KEY(`newsId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;
