<?php
session_start();
include("../../db.php");
$gameId = $_SESSION['gameId'];
$myTeam = $_SESSION['myTeam'];
if (!isset($_REQUEST['placementId']) || !isset($_REQUEST['positionId']) || !isset($_REQUEST['containerId']) || !$_REQUEST['placementId'] || !$_REQUEST['positionId'] || !$_REQUEST['containerId']) {
    echo "Invalid request.";
    exit;
}
$placementId = (int) $_REQUEST['placementId'];  //piece that was moved
$newPositionId = (int) $_REQUEST['positionId'];  //could be -1
$newContainerId = (int) $_REQUEST['containerId'];  //could be -1
$query = 'SELECT gameActive, gamePhase, gameCurrentTeam, gameBattleSection, gameIsland1, gameIsland2, gameIsland3, gameIsland4, gameIsland5, gameIsland6, gameIsland7, gameIsland8, gameIsland9, gameIsland10, gameIsland11, gameIsland12, gameIsland13, gameIsland14 FROM GAMES WHERE gameId = ?';
$preparedQuery = $db->prepare($query);
$preparedQuery->bind_param("i", $gameId);
$preparedQuery->execute();
$results = $preparedQuery->get_result();
$r = $results->fetch_assoc();
$gamePhase = $r['gamePhase'];
$gameCurrentTeam = $r['gameCurrentTeam'];
$gameBattleSection = $r['gameBattleSection'];
$gameIsland1 = $r['gameIsland1'];
$gameIsland2 = $r['gameIsland2'];
$gameIsland3 = $r['gameIsland3'];
$gameIsland4 = $r['gameIsland4'];
$gameIsland5 = $r['gameIsland5'];
$gameIsland6 = $r['gameIsland6'];
$gameIsland7 = $r['gameIsland7'];
$gameIsland8 = $r['gameIsland8'];
$gameIsland9 = $r['gameIsland9'];
$gameIsland10 = $r['gameIsland10'];
$gameIsland11 = $r['gameIsland11'];
$gameIsland12 = $r['gameIsland12'];
$gameIsland13 = $r['gameIsland13'];
$gameIsland14 = $r['gameIsland14'];
$ownerships = [$gameIsland1, $gameIsland2, $gameIsland3, $gameIsland4, $gameIsland5, $gameIsland6, $gameIsland7, $gameIsland8, $gameIsland9, $gameIsland10, $gameIsland11, $gameIsland12, $gameIsland13, $gameIsland14];
if ($r['gameActive'] != 1) {
    header("location:home.php?err=1");
    exit;
}
if ($myTeam != $gameCurrentTeam) {
    echo "It is not your team's turn";
    exit;
}
if ($gamePhase != 2 && $gamePhase != 3 && $gamePhase != 4) {
    echo "It is not the right phase for this.";
    exit;
}
if ($gameBattleSection != "none") {
    echo "Cannot move during battle";
    exit;
}
$unitTerrains = ['water', 'water', 'water', 'water', 'land', 'land', 'land', 'land', 'land', 'air', 'land', 'air', 'air', 'air', 'air', 'missile'];
$unitNames = ['Transport', 'Submarine', 'Destroyer', 'AircraftCarrier', 'ArmyCompany', 'ArtilleryBattery', 'TankPlatoon', 'MarinePlatoon', 'MarineConvoy', 'AttackHelo', 'SAM', 'FighterSquadron', 'BomberSquadron', 'StealthBomberSquadron', 'Tanker', 'LandBasedSeaMissile'];
$query = 'SELECT placementUnitId, placementTeamId, placementCurrentMoves, placementPositionId, placementContainerId, placementBattleUsed FROM placements WHERE placementId = ?';
$preparedQuery = $db->prepare($query);
$preparedQuery->bind_param("i", $placementId);
$preparedQuery->execute();
$results = $preparedQuery->get_result();
$r = $results->fetch_assoc();
$placementUnitId = $r['placementUnitId'];
$placementTeamId = $r['placementTeamId'];
$placementCurrentMoves = $r['placementCurrentMoves'];
$oldPositionId = $r['placementPositionId'];  //used for distance check
$oldContainerId = $r['placementContainerId'];
$placementBattleUsed = $r['placementBattleUsed'];
$placementUnitTerrain = $unitTerrains[$placementUnitId];
$placementUnitName = $unitNames[$placementUnitId];
if ($myTeam != $placementTeamId) {
    echo "This piece does not belong to you";
    exit;
}
if (($oldPositionId == 118 && $gamePhase != 4) || ($gamePhase == 4 && $oldPositionId != 118)) {
    echo "Can only place Reinforcements in inventory during 'Reinforcement Place' phase.";
    exit;
}
if ($oldPositionId == $newPositionId && $oldContainerId == $newContainerId) {  //do nothing if dropped into same spot
    echo "Moved Piece Into Same Position.";
    exit;
}
if ($placementUnitId != 15 && $placementCurrentMoves == 0) {
    echo "This piece is out of moves.";
    exit;
}
if ($placementUnitId != 15) {
    if ($_SESSION['dist'][$oldPositionId][$newPositionId] != 1 && ($oldPositionId != $newPositionId)) {
        echo "Can only move 1 space at a time.";
        exit;
    }
}
if ($newContainerId != -1) {
    $query = 'SELECT placementUnitId, placementTeamId, placementPositionId FROM placements WHERE placementId = ?';
    $preparedQuery = $db->prepare($query);
    $preparedQuery->bind_param("i", $newContainerId);
    $preparedQuery->execute();
    $results = $preparedQuery->get_result();
    $r = $results->fetch_assoc();
    $newPositionId = $r['placementPositionId'];  //positionId was -1, now we know actual position going to
    $containerUnitId = $r['placementUnitId'];
    $containerTeamId = $r['placementTeamId'];
    if ($containerUnitId != 0 && $containerUnitId != 3) {
        echo "This is not a container piece.";
        exit;
    }
    if ($myTeam != $containerTeamId) {
        echo "This container piece does not belong to you.";  //unlikely they would be able to open this piece up
        exit;
    }
    $containerContents_UnitIds = [];
    $query = 'SELECT placementUnitId FROM placements WHERE placementContainerId = ?';
    $preparedQuery = $db->prepare($query);
    $preparedQuery->bind_param("i", $newContainerId);
    $preparedQuery->execute();
    $results = $preparedQuery->get_result();
    $num_results = $results->num_rows;
    for ($i = 0; $i < $num_results; $i++) {
        $r = $results->fetch_assoc();
        $thisPieceInsideTheContainer_UnitId = $r['placementUnitId'];
        array_push($containerContents_UnitIds, $thisPieceInsideTheContainer_UnitId);
    }
    if ($containerUnitId == 0) {  //Transport
        $people = [4, 7];
        $machines = [5, 6, 8, 9, 10];
        if (sizeof($containerContents_UnitIds) == 3) {
            echo "Transport already filled with troops.";
            exit;
        }
        if (in_array($placementUnitId, $people)) {  //people going in
            if (sizeof($containerContents_UnitIds) == 2) {
                if (in_array($containerContents_UnitIds[0], $machines) || in_array($containerContents_UnitIds[1], $machines)) {
                    echo "This troop can't fit with this combination.";
                    exit;
                }
            }
        } elseif (in_array($placementUnitId, $machines)) {  //machine going in            //needs to have 0, 1 people
            if (sizeof($containerContents_UnitIds) == 2 || (sizeof($containerContents_UnitIds) == 1 && in_array($containerContents_UnitIds[0], $machines))) {
                echo "This piece can't fit with this combination.";
                exit;
            }
        } else {
            echo "This piece not allowed into this container.";
            exit;
        }
    } else {  //AircraftCarrier
        if ($placementUnitId != 11) {
            echo "Must be a fighter to go into Aircraft Carrier.";
            exit;
        }
        if (sizeof($containerContents_UnitIds) > 1) {
            echo "Aircraft Carrier already filled.";
            exit;
        }
    }
} else {  //an actual land or water position
    $query = 'SELECT placementUnitId FROM placements WHERE placementPositionId = ? AND placementTeamId != ?';  //get the other pieces that are there
    $preparedQuery = $db->prepare($query);
    $preparedQuery->bind_param("is", $newPositionId, $myTeam);
    $preparedQuery->execute();
    $results = $preparedQuery->get_result();
    $num_results = $results->num_rows;
    if ($newPositionId == 121 || $newPositionId == 122 || $newPositionId == 123 || $newPositionId == 124) {  //missile position
        if ($placementUnitId != 15) {
            echo "Must be a missile to go here.";
            exit;
        }
        if (($newPositionId == 121 && $myTeam != $gameIsland2) || ($newPositionId == 122 && $myTeam != $gameIsland6) || ($newPositionId == 123 && $myTeam != $gameIsland7) || ($newPositionId == 124 && $myTeam != $gameIsland9)) {
            echo "Need to own the island to place a missile.";
            exit;
        }
        if ($num_results > 0) {
            echo "Missile already at this site.";
            exit;
        }
        if ($oldPositionId != 118) {
            echo "Can only move missiles from 118.";
            exit;
        }
    } else {  //land or water position
        if ($placementUnitId == 15) {
            echo "Missiles only go on missile sites (red squares).";  //missile positions checked above
            exit;
        }
        if ($newPositionId <= 54) {  //water positions
            if ($placementUnitTerrain == "land") {
                echo "This piece cannot go on water.";
                exit;
            }
        } else {  //land position
            if ($placementUnitTerrain == "water") {
                echo "This piece cannot go on land.";
                exit;
            }
        }
        $listEnemyPiecesInPosition_UnitIds = [];  //checking blockade
        for ($i = 0; $i < $num_results; $i++) {
            $r = $results->fetch_assoc();
            $thisPieceInPosition_UnitId = $r['placementUnitId'];
            array_push($listEnemyPiecesInPosition_UnitIds, $thisPieceInPosition_UnitId);
        }
        if ($placementUnitId == 1 && in_array(1, $listEnemyPiecesInPosition_UnitIds)) {  //subs block subs
            echo "Blockaded by another sub.";
            exit;
        }
        if (($placementUnitId == 0 || $placementUnitId == 2 || $placementUnitId == 3) && (in_array(2, $listEnemyPiecesInPosition_UnitIds) || in_array(3, $listEnemyPiecesInPosition_UnitIds))) {
            echo "Blockaded by another ship.";
            exit;
        }
    }
}
if ($gamePhase == 4 && $placementUnitId != 15) {  //Reinforcement Place controls, already checked missile stuff, already checked from 118
    $redPlaceValid = array(55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 0, 13, 21, 20, 19);
    $bluePlaceValid = array(65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 8, 7, 6, 12, 18, 25, 31, 38, 45, 54);
    $airfieldPosition = array(56, 57, 78, 83, 89, 113, 116, 66, 68);
    if (($myTeam == "Red" && !in_array($newPositionId, $redPlaceValid)) || ($myTeam == "Blue" && !in_array($newPositionId, $bluePlaceValid))) {
        echo "Not a valid spot for placement.";
        exit;
    }
    if (($placementUnitId == 11 || $placementUnitId == 12 || $placementUnitId == 13 || $placementUnitId == 14) && !in_array($newPositionId, $airfieldPosition)) {
        echo "Planes must be placed on airfields.";
        exit;
    }
    $query = 'SELECT placementId FROM placements WHERE (placementPositionId = ?) AND (placementTeamId != ?) AND (placementGameId = ?)';
    $query = $db->prepare($query);
    $query->bind_param("isi", $newPositionId, $myTeam, $gameId);
    $query->execute();
    $results = $query->get_result();
    $num_results = $results->num_rows;
    if ($num_results > 0) {
        echo "Enemy is already in this position.";
        exit;
    }
}
$query = 'SELECT newsTeam, newsEffect, newsPieces, newsZone FROM newsAlerts WHERE (newsGameId = ?) AND (newsActivated = 1) AND (newsLength >= 1)';
$query = $db->prepare($query);
$query->bind_param("i", $gameId);
$query->execute();
$results = $query->get_result();
$num_results = $results->num_rows;
for ($i = 0; $i < $num_results; $i++) {
    $r = $results->fetch_assoc();
    $newsTeam = $r['newsTeam'];
    if ($newsTeam == $myTeam || $newsTeam == "All") {
        $newsEffect = $r['newsEffect'];
        if ($newsEffect == "disable") {
            $newsPieces = $r['newsPieces'];
            $newsZone = $r['newsZone'];
            $islandPositions = [[75, 76, 77, 78], [79, 80, 81, 82, 121], [83, 84, 85], [86, 87, 88, 89], [90, 91, 92, 93], [94, 95, 96, 122], [97, 98, 99, 123], [100, 101, 102], [103, 104, 105, 106, 124], [107, 108, 109, 110], [111, 112, 113], [114, 115, 116, 117], [55, 56, 57, 58, 59, 60, 61, 62, 63, 64], [65, 66, 67, 68, 69, 70, 71, 72, 73, 74]];
            if ($newsZone == 200 ||
                ($newsZone == $newPositionId && $newPositionId < 100) ||
                ($newsZone == $oldPositionId && $oldPositionId < 100) ||
                (in_array(($newPositionId), ($islandPositions[$newsZone-101]))) ||
                (in_array(($oldPositionId), ($islandPositions[$newsZone-101]))) ||
                (($newsZone > 1000) && (($newsZone - 1000 == $newPositionId) || ($newsZone - 1000 == $oldPositionId)))) {
                $decoded = json_decode($newsPieces, true);
                if ((int) $decoded[$placementUnitName] == 1) {
                    if ((int) $oldPositionId != 118 && $placementUnitId != 15){  //purchased is exempt (except for missiles)
                        echo "News Alert Prevented the Move.";
                        exit;
                    }
                }
            }
        }
    }
}
$killed = 0;  //allowed to move at this point from all game rules / logic
$thingToEcho = "DEFAULT THING TO ECHO";
if ($gamePhase != 4 && $gamePhase != 1 && ($placementUnitId == 11 || $placementUnitId == 12 || $placementUnitId == 13 || $placementUnitId == 14)) {
    $adjSam = array();
    for ($i = 55; $i < 117; $i++) {  //no container sams on land, skip water check
        if ($_SESSION['dist'][$newPositionId][$i] == 1) {
            array_push($adjSam, $i);
        }
    }
    array_push($adjSam, $newPositionId);
    for ($j = 0; $j < sizeof($adjSam); $j++) {
        $query = 'SELECT placementPositionId FROM placements WHERE (placementPositionId = ?) AND (placementTeamId != ?) AND (placementUnitId = 10) AND (placementGameId = ?)';
        $query = $db->prepare($query);
        $position = $adjSam[$j];
        $query->bind_param("isi", $position, $myTeam, $gameId);
        $query->execute();
        $results = $query->get_result();
        $num_results = $results->num_rows;
        for ($k = 0; $k < $num_results; $k++) {
            $diceRoll = rand(1, 6);
            $thisSam = $results->fetch_assoc();
            $samPosition = (int) $thisSam['placementPositionId'];
            if ($newPositionId == $samPosition || $placementUnitId != 13) {
                if ($diceRoll >= $_SESSION['attack'][10][$placementUnitId]) {
                    $killed = 1;
                    $thingToEcho = "Piece was destroyed by Sam!";
                    break;
                }
            }
        }
    }
}
if ($gamePhase != 4 && $gamePhase != 1 && ($placementUnitId == 0 || $placementUnitId == 2 || $placementUnitId == 3)) {  //missile check
    $missileTargets = [[2, 3, 4, 10, 11, 15, 16], [16, 17, 18, 24, 25, 29, 30, 31], [19, 20, 21, 26, 27, 32, 33, 34], [28, 35, 36, 41, 42]];
    for ($x = 0; $x < 4; $x++) {
        if (in_array($newPositionId, $missileTargets[$x])) {
            $missilePosition = $x + 121;
            $query = 'SELECT placementId FROM placements WHERE placementPositionId = ? AND placementGameId = ? AND placementTeamId != ?';
            $query = $db->prepare($query);
            $query->bind_param("iis", $missilePosition, $gameId, $myTeam);
            $query->execute();
            $results = $query->get_result();
            $num_results = $results->num_rows;
            if ($num_results == 1) {
                $r = $results->fetch_assoc();
                $missilePlacementId = $r['placementId'];
                $randNumber = rand(1, 10);
                if ($randNumber <= 8) {
                    $killed = 1;
                    $thingToEcho = "Piece was destroyed by Missile.";
                    $query = 'DELETE FROM placements WHERE placementId = ?';  //Delete the missile
                    $query = $db->prepare($query);
                    $query->bind_param("i", $missilePlacementId);
                    $query->execute();
                    $updateType = "pieceRemove";
                    $query = 'INSERT INTO updates (updateGameId, updateType, updatePlacementId) VALUES (?, ?, ?)';
                    $query = $db->prepare($query);
                    $query->bind_param("isi", $gameId, $updateType, $missilePlacementId);
                    $query->execute();
                    break;
                }
            }
        }
    }
}
if ($killed == 1) {
    if ($placementUnitId == 0 || $placementUnitId == 3) {  //update pieces inside this piece's container
        $query = 'DELETE FROM placements WHERE placementContainerId = ?';
        $query = $db->prepare($query);
        $query->bind_param("i", $placementId);
        $query->execute();
    }
    $query = 'DELETE FROM placements WHERE placementId = ?';
    $query = $db->prepare($query);
    $query->bind_param("i", $placementId);
    $query->execute();
    $query = 'DELETE FROM movements WHERE movementGameId = ?';
    $query = $db->prepare($query);
    $query->bind_param("i", $gameId);
    $query->execute();
    $updateType = "killRemove";
    $query = 'INSERT INTO updates (updateGameId, updateType, updatePlacementId, updateHTML) VALUES (?, ?, ?, ?)';
    $query = $db->prepare($query);
    $query->bind_param("isis", $gameId, $updateType, $placementId, $thingToEcho);
    $query->execute();
    echo $thingToEcho;
    exit;
} else {
    if ($placementUnitId == 0 || $placementUnitId == 3) {  //update pieces inside this piece's container
        $query = 'UPDATE placements SET placementPositionId = ? WHERE (placementContainerId = ?)';
        $query = $db->prepare($query);
        $query->bind_param("ii", $newPositionId, $placementId);
        $query->execute();
    }
    $query = 'UPDATE placements SET placementPositionId = ?, placementCurrentMoves = placementCurrentMoves - 1, placementContainerId = ? WHERE (placementId = ?)';
    $query = $db->prepare($query);
    $query->bind_param("iii", $newPositionId,  $newContainerId,  $placementId);
    $query->execute();
    $query = 'INSERT INTO movements (movementGameId, movementFromPosition, movementFromContainer, movementNowPlacement) VALUES (?, ?, ?, ?)';
    $query = $db->prepare($query);
    $query->bind_param("iiii", $gameId, $oldPositionId, $oldContainerId, $placementId);
    $query->execute();
    $battleUsedText = "";
    if ($placementBattleUsed == 1) {
        $battleUsedText = "\nUsed in Attack";
    }
    $newTitle = $placementUnitName."\nMoves: ".($placementCurrentMoves-1).$battleUsedText;
    $updateType = "pieceMove";
    $query = 'INSERT INTO updates (updateGameId, updateType, updatePlacementId, updateNewPositionId, updateNewContainerId, updateHTML) VALUES (?, ?, ?, ?, ?, ?)';
    $query = $db->prepare($query);
    $query->bind_param("isiiis", $gameId, $updateType, $placementId, $newPositionId, $newContainerId, $newTitle);
    $query->execute();
    $flagPositions = [75, 79, 85, 86, 90, 94, 97, 100, 103, 107, 111, 114, 55, 65];
    if (in_array($newPositionId, $flagPositions)) {
        $index = array_search($newPositionId, $flagPositions);
        if ($ownerships[$index] != $myTeam) {
            if (sizeof($listEnemyPiecesInPosition_UnitIds) == 0) {  //already have this from land/water blockade check
                if ($placementUnitId >= 4 && $placementUnitId <= 8) {  //land units that can capture
                    $query = 'UPDATE games SET gameIsland'.($index + 1).' = ?, game'.$myTeam.'Hpoints = game'.$myTeam.'Hpoints + 1 WHERE gameId = ?';
                    $query = $db->prepare($query);
                    $query->bind_param("si", $myTeam, $gameId);
                    $query->execute();
                    $islandToChange = $index + 1;
                    $updateType = "islandOwnerChange";
                    $query = 'INSERT INTO updates (updateGameId, updateType, updatePlacementId, updateHTML) VALUES (?, ?, ?, ?)';
                    $query = $db->prepare($query);
                    $query->bind_param("isis", $gameId, $updateType, $islandToChange, $myTeam);
                    $query->execute();
                    $query = 'DELETE FROM movements WHERE movementGameId = ?';
                    $query = $db->prepare($query);
                    $query->bind_param("i", $gameId);
                    $query->execute();
                }
            }
        }
    }
    echo "Moved the piece.";
    exit;
}
