-- Access and Update the Database K2.5

-- -----------------------------------------------------------------------------

USE islandRushDB2;
SET SQL_SAFE_UPDATES = 0;

SELECT * FROM GAMES WHERE gameId = 1;

SELECT gameBattleTurn FROM games WHERE gameId = 1;

SELECT * FROM battlePieces;

SELECT * FROM battlePieces NATURAL JOIN placements WHERE battleGameId = 1 AND placementId = battlePieceId;

SELECT * FROM placements;

SELECT placementId, unitMoves FROM (SELECT placementId, placementUnitId FROM placements WHERE placementGameId = 1 AND placementTeamId = 'Blue') a NATURAL JOIN (SELECT unitId, unitMoves FROM units) b WHERE placementUnitId = unitId;

SELECT placementId, placementUnitId FROM placements WHERE placementGameId = 1 AND placementPositionId = 16 AND placementTeamId != 'Blue' AND placementUnitId != 4 AND placementUnitId != 5 AND placementUnitId != 6 AND placementUnitId != 7 AND placementUnitId != 8 AND placementUnitId != 15;

SELECT placementId, placementUnitId FROM placements WHERE placementGameId = 1 AND placementPositionId = 16 AND placementTeamId != 'Blue';


SELECT * FROM placements WHERE placementPositionId = 66;

SELECT battlePieceId FROM battlePieces RIGHT JOIN placements ON battlePieceId WHERE battlePieceId = placementId AND battleGameId = 1 AND battlePieceState = 3;

SELECT placementUnitId, battlePieceState FROM battlePieces RIGHT JOIN placements ON battlePieceId WHERE battlePieceId = placementId AND battleGameId = 1 AND (battlePieceState = 3 or battlePieceState = 4) ORDER BY battlePieceState DESC;



SELECT b.placementId, b.placementUnitId, b.placementCurrentMoves FROM (SELECT placementPositionId FROM placements WHERE placementGameId = 1 AND placementTeamId = 'Blue' AND placementUnitId = 14) a JOIN (SELECT placementId, placementPositionId, placementUnitId, placementCurrentMoves FROM placements WHERE placementGameId = 1 AND placementTeamId = 'Blue' AND (placementUnitId = 11 OR placementUnitId = 12 OR placementUnitId = 13)) b USING(placementPositionId) WHERE a.placementPositionId = b.placementPositionId;









