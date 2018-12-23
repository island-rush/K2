-- Access and Update the Database K2.5

-- -----------------------------------------------------------------------------

USE islandRushDB2;
SET SQL_SAFE_UPDATES = 0;














-- UPDATE games SET gameRedRpoints = 1000 WHERE gameId = 1;

SELECT * FROM games WHERE gameId = 1;

-- SELECT * FROM units;

SELECT * FROM placements ORDER BY placementId DESC;

-- SELECT * FROM movements;

-- SELECT * FROM battlePieces;

SELECT * FROM updates order by updateId DESC;

-- SELECT * FROM newsAlerts;

-- SELECT * FROM updates WHERE (updateGameId = 1) AND (updateId > 137) AND (updateTeam = 'Spec') ORDER BY updateId ASC;

-- UPDATE games SET gameRedJoined = 0 WHERE gameId = 1;

-- INSERT INTO updates (updateGameId, updateValue, updateTeam, updateType) VALUES (1, 0, 'Red', 'logout');

-- INSERT INTO updates (updateGameId, updateValue, updateTeam, updateType) VALUES (1, 0, 'Red', 'phaseChange');

-- INSERT INTO placements VALUES ();


INSERT INTO placements (placementGameId, placementUnitId, placementTeamId, placementCurrentMoves, placementPositionId) VALUES(1, 2, 'Red', 1, 1);


-- UPDATE placements SET placementCurrentMoves = 1 WHERE placementId = 95;



