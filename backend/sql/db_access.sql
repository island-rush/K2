-- Access and Update the Database K2.5

-- -----------------------------------------------------------------------------

USE islandRushDB2;
SET SQL_SAFE_UPDATES = 0;

SELECT * FROM GAMES WHERE gameId = 1;

SELECT * FROM battlePieces NATURAL JOIN placements WHERE battleGameId = 1 AND placementId = battlePieceId;

SELECT * FROM placements;


SELECT placementId, placementUnitId FROM placements WHERE placementGameId = 1 AND placementPositionId = 16 AND placementTeamId != 'Blue' AND placementUnitId != 4 AND placementUnitId != 5 AND placementUnitId != 6 AND placementUnitId != 7 AND placementUnitId != 8 AND placementUnitId != 15;

SELECT placementId, placementUnitId FROM placements WHERE placementGameId = 1 AND placementPositionId = 16 AND placementTeamId != 'Blue';