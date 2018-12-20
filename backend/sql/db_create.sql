-- Database Creation for K2.5


-- -----------------------------------------------------------------------------
DROP DATABASE IF EXISTS islandRushDB2;
CREATE DATABASE islandRushDB2;
USE islandRushDB2;

SET SQL_SAFE_UPDATES = 0;
-- -----------------------------------------------------------------------------



-- Table of Games
CREATE TABLE IF NOT EXISTS `games`(
  `gameId` int(5) NOT NULL AUTO_INCREMENT,
  
  `gameSection` varchar(10) NOT NULL,  -- 'M1A', 'T7C'
  `gameInstructor` varchar(50) NOT NULL,  -- "Lastname"
  `gameAdminPassword` varchar(50) NOT NULL DEFAULT 'c4a276e907f10b988d593fcd573a3cba',  -- "password"
  
  `gameActive` int(1) NOT NULL DEFAULT 0, -- 1 or 0
  
  `gameRedJoined` int(1) NOT NULL DEFAULT 0, -- 0 or 1 (1 = joined)
  `gameBlueJoined` int(1) NOT NULL DEFAULT 0,
  
  `gameCurrentTeam`  varchar(5) NOT NULL DEFAULT 'Blue', -- 'Red' or 'Blue'
  `gameTurn` int(4) NOT NULL DEFAULT 0, -- 0, 1, 2, 3...
  `gamePhase`  int(1) NOT NULL DEFAULT 1, --  1 = news, 2 = reinforcements...
  
  `gameRedRpoints` int(5) NOT NULL DEFAULT 10,
  `gameBlueRpoints` int(5) NOT NULL DEFAULT 60,
  `gameRedHpoints` int(5) NOT NULL DEFAULT 0,
  `gameBlueHpoints` int(5) NOT NULL DEFAULT 0,

  `gameBattleSection` varchar(20) NOT NULL DEFAULT 'none',  -- "none" (no popup), "attack", "counter", "askRepeat"......"selectPos", "selectPieces"?
  `gameBattleSubSection` varchar(20) NOT NULL DEFAULT 'choosing_pieces', -- "choosing_pieces", "attacked_popup", "defense_popup"
  `gameBattleTurn` int(3) NOT NULL DEFAULT 0,  -- put in to kick out aircraft after 2 turns
  `gameBattleLastRoll` int(1) NOT NULL DEFAULT 1, -- 1 for default (or no roll to display anymore/reset), 1-6 for roll
  `gameBattleLastMessage` varchar(50) DEFAULT '', -- used for explaining what happened "red killed blue's fighter with fighter" ex...
  `gameBattlePosSelected` int(8) NOT NULL DEFAULT 999999, -- positionId chosen by attacker (999999 default)
  
  `gameIsland1` varchar(10) NOT NULL DEFAULT 'Red',
  `gameIsland2` varchar(10) NOT NULL DEFAULT 'Red',
  `gameIsland3` varchar(10) NOT NULL DEFAULT 'Red',
  `gameIsland4` varchar(10) NOT NULL DEFAULT 'Red',
  `gameIsland5` varchar(10) NOT NULL DEFAULT 'Red',
  `gameIsland6` varchar(10) NOT NULL DEFAULT 'Red',
  `gameIsland7` varchar(10) NOT NULL DEFAULT 'Red',
  `gameIsland8` varchar(10) NOT NULL DEFAULT 'Red',
  `gameIsland9` varchar(10) NOT NULL DEFAULT 'Red',
  `gameIsland10` varchar(10) NOT NULL DEFAULT 'Red',
  `gameIsland11` varchar(10) NOT NULL DEFAULT 'Red',
  `gameIsland12` varchar(10) NOT NULL DEFAULT 'Red',
  `gameIsland13` varchar(10) NOT NULL DEFAULT 'Red',
  `gameIsland14` varchar(10) NOT NULL DEFAULT 'Blue',
    PRIMARY KEY(`gameId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;



-- Insert games into the database
INSERT INTO `games` (gameSection, gameInstructor, gameAdminPassword, gameActive) VALUES ('M1A1', 'Adolph', 	'5f4dcc3b5aa765d61d8327deb882cf99', 1);
INSERT INTO `games` (gameSection, gameInstructor, gameAdminPassword, gameActive) VALUES ('M3A1', 'Kulp', 	'5f4dcc3b5aa765d61d8327deb882cf99', 1);
INSERT INTO `games` (gameSection, gameInstructor, gameAdminPassword, gameActive) VALUES ('M1A1', 'test', 	'5f4dcc3b5aa765d61d8327deb882cf99', 1);
INSERT INTO `games` (gameSection, gameInstructor, gameAdminPassword, gameActive) VALUES ('M2A1', 'test', 	'5f4dcc3b5aa765d61d8327deb882cf99', 1);
INSERT INTO `games` (gameSection, gameInstructor, gameAdminPassword, gameActive) VALUES ('M3A1', 'test', 	'5f4dcc3b5aa765d61d8327deb882cf99', 1);
INSERT INTO `games` (gameSection, gameInstructor, gameAdminPassword, gameActive) VALUES ('M4A1', 'test', 	'5f4dcc3b5aa765d61d8327deb882cf99', 1);
INSERT INTO `games` (gameSection, gameInstructor, gameAdminPassword, gameActive) VALUES ('M5A1', 'test', 	'5f4dcc3b5aa765d61d8327deb882cf99', 1);
INSERT INTO `games` (gameSection, gameInstructor, gameAdminPassword, gameActive) VALUES ('M6A1', 'test', 	'5f4dcc3b5aa765d61d8327deb882cf99', 1);
INSERT INTO `games` (gameSection, gameInstructor, gameAdminPassword, gameActive) VALUES ('M7A1', 'test', 	'5f4dcc3b5aa765d61d8327deb882cf99', 1);
INSERT INTO `games` (gameSection, gameInstructor, gameAdminPassword, gameActive) VALUES ('T1A1', 'test', 	'5f4dcc3b5aa765d61d8327deb882cf99', 1);
INSERT INTO `games` (gameSection, gameInstructor, gameAdminPassword, gameActive) VALUES ('T2A1', 'test', 	'5f4dcc3b5aa765d61d8327deb882cf99', 1);
INSERT INTO `games` (gameSection, gameInstructor, gameAdminPassword, gameActive) VALUES ('T3A1', 'test', 	'5f4dcc3b5aa765d61d8327deb882cf99', 1);
INSERT INTO `games` (gameSection, gameInstructor, gameAdminPassword, gameActive) VALUES ('T4A1', 'test', 	'5f4dcc3b5aa765d61d8327deb882cf99', 1);
-- REAL GAMES
-- password = 'DFMI2019teacher'
INSERT INTO `games` (gameSection, gameInstructor, gameAdminPassword) VALUES ('M1A1', 'Start', 			'a3890051c3b41a4b3890633eb1340248');  -- 'DFCS2019student'
INSERT INTO `games` (gameSection, gameInstructor, gameAdminPassword) VALUES ('M1A1', 'German', 			'c4a276e907f10b988d593fcd573a3cba');
INSERT INTO `games` (gameSection, gameInstructor, gameAdminPassword) VALUES ('M1A2', 'German', 			'c4a276e907f10b988d593fcd573a3cba');
INSERT INTO `games` (gameSection, gameInstructor, gameAdminPassword) VALUES ('M1B1', 'Grotelueschen', 	'c4a276e907f10b988d593fcd573a3cba');
INSERT INTO `games` (gameSection, gameInstructor, gameAdminPassword) VALUES ('M1B2', 'Grotelueschen', 	'c4a276e907f10b988d593fcd573a3cba');
INSERT INTO `games` (gameSection, gameInstructor, gameAdminPassword) VALUES ('M1C1', 'Smicklas', 		'c4a276e907f10b988d593fcd573a3cba');
INSERT INTO `games` (gameSection, gameInstructor, gameAdminPassword) VALUES ('M1C2', 'Smicklas', 		'c4a276e907f10b988d593fcd573a3cba');
INSERT INTO `games` (gameSection, gameInstructor, gameAdminPassword) VALUES ('M1D1', 'Moore', 			'c4a276e907f10b988d593fcd573a3cba');
INSERT INTO `games` (gameSection, gameInstructor, gameAdminPassword) VALUES ('M1D2', 'Moore', 			'c4a276e907f10b988d593fcd573a3cba');
INSERT INTO `games` (gameSection, gameInstructor, gameAdminPassword) VALUES ('M3A1', 'Burke', 			'c4a276e907f10b988d593fcd573a3cba');
INSERT INTO `games` (gameSection, gameInstructor, gameAdminPassword) VALUES ('M3A2', 'Burke', 			'c4a276e907f10b988d593fcd573a3cba');
INSERT INTO `games` (gameSection, gameInstructor, gameAdminPassword) VALUES ('M3B1', 'Moore', 			'c4a276e907f10b988d593fcd573a3cba');
INSERT INTO `games` (gameSection, gameInstructor, gameAdminPassword) VALUES ('M3B2', 'Moore', 			'c4a276e907f10b988d593fcd573a3cba');
INSERT INTO `games` (gameSection, gameInstructor, gameAdminPassword) VALUES ('M3C1', 'Smicklas',		'c4a276e907f10b988d593fcd573a3cba');
INSERT INTO `games` (gameSection, gameInstructor, gameAdminPassword) VALUES ('M3C2', 'Smicklas',		'c4a276e907f10b988d593fcd573a3cba');
INSERT INTO `games` (gameSection, gameInstructor, gameAdminPassword) VALUES ('M6A1', 'Estrada', 		'c4a276e907f10b988d593fcd573a3cba');
INSERT INTO `games` (gameSection, gameInstructor, gameAdminPassword) VALUES ('M6A2', 'Estrada', 		'c4a276e907f10b988d593fcd573a3cba');
INSERT INTO `games` (gameSection, gameInstructor, gameAdminPassword) VALUES ('M6B1', 'Grotelueschen', 	'c4a276e907f10b988d593fcd573a3cba');
INSERT INTO `games` (gameSection, gameInstructor, gameAdminPassword) VALUES ('M6B2', 'Grotelueschen',	'c4a276e907f10b988d593fcd573a3cba');
INSERT INTO `games` (gameSection, gameInstructor, gameAdminPassword) VALUES ('M6C1', 'German', 			'c4a276e907f10b988d593fcd573a3cba');
INSERT INTO `games` (gameSection, gameInstructor, gameAdminPassword) VALUES ('M6C2', 'German', 			'c4a276e907f10b988d593fcd573a3cba');
INSERT INTO `games` (gameSection, gameInstructor, gameAdminPassword) VALUES ('M6D1', 'Matisek', 		'c4a276e907f10b988d593fcd573a3cba');
INSERT INTO `games` (gameSection, gameInstructor, gameAdminPassword) VALUES ('M6D2', 'Matisek', 		'c4a276e907f10b988d593fcd573a3cba');
INSERT INTO `games` (gameSection, gameInstructor, gameAdminPassword) VALUES ('T1A1', 'McPhilamy', 		'c4a276e907f10b988d593fcd573a3cba');
INSERT INTO `games` (gameSection, gameInstructor, gameAdminPassword) VALUES ('T1A2', 'McPilamy', 		'c4a276e907f10b988d593fcd573a3cba');
INSERT INTO `games` (gameSection, gameInstructor, gameAdminPassword) VALUES ('T1B1', 'Cutchin', 		'c4a276e907f10b988d593fcd573a3cba');
INSERT INTO `games` (gameSection, gameInstructor, gameAdminPassword) VALUES ('T1B2', 'Cutchin', 		'c4a276e907f10b988d593fcd573a3cba');
INSERT INTO `games` (gameSection, gameInstructor, gameAdminPassword) VALUES ('T1C1', 'Davitch', 		'c4a276e907f10b988d593fcd573a3cba');
INSERT INTO `games` (gameSection, gameInstructor, gameAdminPassword) VALUES ('T1C2', 'Davitch', 		'c4a276e907f10b988d593fcd573a3cba');
INSERT INTO `games` (gameSection, gameInstructor, gameAdminPassword) VALUES ('T1D1', 'Fogle', 			'c4a276e907f10b988d593fcd573a3cba');
INSERT INTO `games` (gameSection, gameInstructor, gameAdminPassword) VALUES ('T1D2', 'Fogle', 			'c4a276e907f10b988d593fcd573a3cba');
INSERT INTO `games` (gameSection, gameInstructor, gameAdminPassword) VALUES ('T3A1', 'Swaim', 			'c4a276e907f10b988d593fcd573a3cba');
INSERT INTO `games` (gameSection, gameInstructor, gameAdminPassword) VALUES ('T3A2', 'Swaim', 			'c4a276e907f10b988d593fcd573a3cba');
INSERT INTO `games` (gameSection, gameInstructor, gameAdminPassword) VALUES ('T3B1', 'Nelson', 			'c4a276e907f10b988d593fcd573a3cba');
INSERT INTO `games` (gameSection, gameInstructor, gameAdminPassword) VALUES ('T3B2', 'Nelson', 			'c4a276e907f10b988d593fcd573a3cba');
INSERT INTO `games` (gameSection, gameInstructor, gameAdminPassword) VALUES ('T3C1', 'Davitch', 		'c4a276e907f10b988d593fcd573a3cba');
INSERT INTO `games` (gameSection, gameInstructor, gameAdminPassword) VALUES ('T3C2', 'Davitch', 		'c4a276e907f10b988d593fcd573a3cba');
INSERT INTO `games` (gameSection, gameInstructor, gameAdminPassword) VALUES ('T4A1', 'Fogle', 			'c4a276e907f10b988d593fcd573a3cba');
INSERT INTO `games` (gameSection, gameInstructor, gameAdminPassword) VALUES ('T4A2', 'Fogle', 			'c4a276e907f10b988d593fcd573a3cba');
INSERT INTO `games` (gameSection, gameInstructor, gameAdminPassword) VALUES ('T5A1', 'McPhilamy', 		'c4a276e907f10b988d593fcd573a3cba');
INSERT INTO `games` (gameSection, gameInstructor, gameAdminPassword) VALUES ('T5A2', 'McPhilamy', 		'c4a276e907f10b988d593fcd573a3cba');
INSERT INTO `games` (gameSection, gameInstructor, gameAdminPassword) VALUES ('T5B1', 'Hersch', 			'c4a276e907f10b988d593fcd573a3cba');
INSERT INTO `games` (gameSection, gameInstructor, gameAdminPassword) VALUES ('T5B2', 'Hersch', 			'c4a276e907f10b988d593fcd573a3cba');
INSERT INTO `games` (gameSection, gameInstructor, gameAdminPassword) VALUES ('T6A1', 'Nelson', 			'c4a276e907f10b988d593fcd573a3cba');
INSERT INTO `games` (gameSection, gameInstructor, gameAdminPassword) VALUES ('T6A2', 'Nelson', 			'c4a276e907f10b988d593fcd573a3cba');
INSERT INTO `games` (gameSection, gameInstructor, gameAdminPassword) VALUES ('T6B1', 'Cutchin', 		'c4a276e907f10b988d593fcd573a3cba');
INSERT INTO `games` (gameSection, gameInstructor, gameAdminPassword) VALUES ('T6B2', 'Cutchin', 		'c4a276e907f10b988d593fcd573a3cba');
INSERT INTO `games` (gameSection, gameInstructor, gameAdminPassword) VALUES ('T6C1', 'Araki', 			'c4a276e907f10b988d593fcd573a3cba');
INSERT INTO `games` (gameSection, gameInstructor, gameAdminPassword) VALUES ('T6C2', 'Araki', 			'c4a276e907f10b988d593fcd573a3cba');
INSERT INTO `games` (gameSection, gameInstructor, gameAdminPassword) VALUES ('T6D1', 'Matisek', 		'c4a276e907f10b988d593fcd573a3cba');
INSERT INTO `games` (gameSection, gameInstructor, gameAdminPassword) VALUES ('T6D2', 'Matisek', 		'c4a276e907f10b988d593fcd573a3cba');



-- Table of Units (static)
CREATE TABLE IF NOT EXISTS `units`(
	`unitId` int(5) NOT NULL ,
    `unitName` varchar(30) NOT NULL,
    `unitTerrain` varchar(20) NOT NULL,
    `unitMoves` int(3) NOT NULL,
    `unitCost` int(3) NOT NULL,
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




-- Table of game pieces and where they are in each game
CREATE TABLE IF NOT EXISTS `placements`(
	`placementId` int(16) NOT NULL AUTO_INCREMENT,
    `placementGameId` int(5) NOT NULL,
    `placementUnitId` int(5) NOT NULL,
    `placementTeamId` varchar(10) NOT NULL,  -- "Red" or "Blue"
	`placementContainerId` int(16) NOT NULL,  -- placementId of the container its in (999999 used instead of null)
    `placementCurrentMoves` int(3) NOT NULL,
    `placementPositionId` int(4) NOT NULL,  -- references what spot its in on the board (map is available in resources / gameInfo)
    `placementBattleUsed` int(1) NOT NULL, -- 0 for not yet used, 1 for used
    PRIMARY KEY(`placementId`),
    FOREIGN KEY (placementUnitId) REFERENCES units(unitId),
    FOREIGN KEY (placementGameId) REFERENCES games(gameId)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;



-- Table of Movements
CREATE TABLE IF NOT EXISTS `movements`(
	`movementId` int(16) NOT NULL AUTO_INCREMENT,
    `movementGameId` int(5) NOT NULL,
    `movementTurn` int(5) NOT NULL,  -- need what phase/turn movement was made (only undo current phase/turn)
    `movementPhase` varchar(20) NOT NULL,
    `movementFromPosition` int(4) NOT NULL,
    `movementFromContainer` int(16),
    `movementNowPlacement` int(16) NOT NULL,  -- placement contains current position/container
    `movementCost` int(3) NOT NULL,  -- cost of moves
    PRIMARY KEY(`movementId`),
    FOREIGN KEY (movementGameId) REFERENCES games(gameId)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;




-- Table of pieces involved in battles (duplicate pieces with battle only info)
CREATE TABLE IF NOT EXISTS `battlePieces`(
	`battlePieceId` int(5) NOT NULL,  -- piece must already exist, this refers to the placementId
    `battleGameId` int(5) NOT NULL,
	`battlePieceState` int(4) NOT NULL,  -- "unused_attacker" (0), "used_defender", "selected..." (in battle center), "destroyed?" (this maybe not used, piece will be deleted here and also from real board)
    `battlePieceWasHit` int(1) NOT NULL, -- 0 for false, 1 for true
    PRIMARY KEY(`battlePieceId`)
);




-- Table of board updates to send to other client (piece stuff mostly)
CREATE TABLE IF NOT EXISTS `updates`(
	`updateId` int(16) NOT NULL AUTO_INCREMENT,
	`updateGameId` int(5) NOT NULL,
	`updateValue` int(5) NOT NULL,  -- has the update been processed / changed / null? (0 = not been processed) (1 = processed)  
	`updateTeam` varchar(10),  -- Red, Blue, Spec
	`updateType` varchar(30), -- phaseChange,
	`updatePlacementId` int(4) DEFAULT 0,
	`updateNewPositionId` int(4) DEFAULT 0,
	`updateNewContainerId` int(4) DEFAULT 0,
    `updateNewMoves` int(2) DEFAULT 9,
	`updateNewUnitId` int(4) DEFAULT 16,
    `updateBattlePieceState` int(2) DEFAULT 8,
    `updateBattlePositionSelectedPieces` varchar(16000) DEFAULT 'defaultString',
    `updateBattlePiecesSelected` varchar(16000) DEFAULT 'defaultString',
    `updateIsland` varchar(20) DEFAULT 'special_default15',
    `updateIslandTeam` varchar(10) DEFAULT 'purple',
	PRIMARY KEY(`updateId`)
 ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;
 
 
 
 
 -- Table of news alerts (not yet fully implemented)
CREATE TABLE IF NOT EXISTS `newsAlerts`(
  `newsId` int(5) NOT NULL AUTO_INCREMENT,
  `newsGameId` int(5) NOT NULL,
  `newsOrder` int(5) NOT NULL,  -- what index is this in the list of this game's news alerts
  `newsTeam` varchar(10) NOT NULL DEFAULT 'nothing', -- 'Red', 'Blue', 'All'. Defaults to 'nothing' for effect=nothing
  `newsPieces` varchar(350) NOT NULL DEFAULT 'nothing', -- "{transport: 0, submarine: 1, destroyer: 0, ...}"  a JSON string. -access with  newsPieces->>'$.tank'. Defaults to 'nothing' for effect=nothing
  `newsEffect` varchar(20) NOT NULL, -- 'disable', 'rollDie', 'nothing',  ...
  `newsRollValue` varchar(2) NOT NULL DEFAULT 0, -- {1,2,3,4,5,6} default 0 but it isnt looked at unless effect=rollDie
  `newsZone` int(10) NOT NULL DEFAULT 666, -- {0-54, 101-114, 200} for sea zones 0-54, whole island 1-14, or all zones 200. if effect=nothing, default to 666.
  `newsLength` int(10) NOT NULL DEFAULT 1, -- 1,2,3 (amount of turns the effect lasts)
  `newsHumanitarian` int(2) NOT NULL DEFAULT 0, -- 1 or 0
  `newsText` varchar(200) NOT NULL DEFAULT 'default string', -- the message that displays with the alert
  `newsEffectText` varchar(200) NOT NULL DEFAULT 'default string', -- the message about the action of the effect
  `newsActivated` int(2) NOT NULL DEFAULT 0, -- if the news alert has been 'pulled' (activated) yet. defaults to unused=0.
  PRIMARY KEY(`newsId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;




