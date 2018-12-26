<?php
session_start();
include("../../db.php");

$gameId = $_SESSION['gameId'];
$myTeam = $_SESSION['myTeam'];

$placementId = (int) $_REQUEST['placementId'];  //piece that was moved
$positionId = (int) $_REQUEST['positionId'];  //could be -1
$containerId = (int) $_REQUEST['containerId'];  //could be -1


//current game state
$query = 'SELECT gamePhase, gameCurrentTeam, gameBattleSection, gameIsland2, gameIsland6, gameIsland7, gameIsland9 FROM GAMES WHERE gameId = ?';
$preparedQuery = $db->prepare($query);
$preparedQuery->bind_param("i", $gameId);
$preparedQuery->execute();
$results = $preparedQuery->get_result();
$r = $results->fetch_assoc();

$gamePhase = $r['gamePhase'];
$gameCurrentTeam = $r['gameCurrentTeam'];
$gameBattleSection = $r['gameBattleSection'];

$gameIsland2 = $r['gameIsland2'];
$gameIsland6 = $r['gameIsland6'];
$gameIsland7 = $r['gameIsland7'];
$gameIsland9 = $r['gameIsland9'];

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


//info about the piece moving
$query = 'SELECT placementUnitId, placementTeamId, placementCurrentMoves, placementPositionId, placementContainerId, unitTerrain, unitName FROM (SELECT placementUnitId, placementTeamId, placementCurrentMoves, placementPositionId, placementContainerId FROM placements WHERE placementId = ?) a NATURAL JOIN units b WHERE placementUnitId = unitId';
$preparedQuery = $db->prepare($query);
$preparedQuery->bind_param("i", $placementId);
$preparedQuery->execute();
$results = $preparedQuery->get_result();
$r = $results->fetch_assoc();

$placementUnitId = $r['placementUnitId'];
$placementTeamId = $r['placementTeamId'];
$placementCurrentMoves = $r['placementCurrentMoves'];
$placementPositionId = $r['placementPositionId'];  //used for distance check
$placementContainerId = $r['placementContainerId'];
$placementUnitTerrain = $r['unitTerrain'];
$placementUnitName = $r['unitName'];

if ($myTeam != $placementTeamId) {
    echo "This piece does not belong to you";
    exit;
}
if ($placementCurrentMoves == 0 && $placementUnitId != 15) {  //exclude missile from this check
    echo "This piece is out of moves.";
    exit;
}
if (($placementPositionId == 118 && $gamePhase != 4)) {
    echo "Can only place Reinforcements during 'Reinforcement Place' phase.";
    exit;
}

if ($containerId != -1) {
    $query = 'SELECT placementUnitId, placementTeamId, placementPositionId FROM placements WHERE placementId = ?';
    $preparedQuery = $db->prepare($query);
    $preparedQuery->bind_param("i", $containerId);
    $preparedQuery->execute();
    $results = $preparedQuery->get_result();
    $r = $results->fetch_assoc();

    $positionId = $r['placementPositionId'];  //positionId was -1, now we know actual position going to
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
    $preparedQuery->bind_param("i", $containerId);
    $preparedQuery->execute();
    $results = $preparedQuery->get_result();
    $num_results = $results->num_rows;
    if ($num_results > 0) {
        for ($i = 0; $i < $num_results; $i++) {
            $r = $results->fetch_assoc();
            $thisPieceInsideTheContainer_UnitId = $r['placementUnitId'];
            array_push($containerContents_UnitIds, $thisPieceInsideTheContainer_UnitId);
        }
    }
    if ($containerUnitId == 0) {  //Transport
        $people = [4, 7];
        $machines = [5, 6, 8, 9, 10];
        if (sizeof($containerContents_UnitIds) == 3) {
            echo "Transport already filled with troops.";
            exit;
        }
        if (in_array($placementUnitId, $people)) {  //people going in
            if (sizeof($containerContents_UnitIds == 2)) {
                if (in_array($containerContents_UnitIds[0], $machines) || in_array($containerContents_UnitIds[1], $machines)) {
                    echo "This troop can't fit with this combination.";
                    exit;
                }
            }
        } elseif (in_array($placementUnitId, $machines)) {  //machine going in
            //needs to have 0, 1 people
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
    $preparedQuery->bind_param("is", $positionId, $myTeam);
    $preparedQuery->execute();
    $results = $preparedQuery->get_result();
    $num_results = $results->num_rows;

    if ($positionId == 121 || $positionId == 122 || $positionId == 123 || $positionId == 124) {  //missile position
        if ($placementUnitId != 15) {
            echo "Must be a missile to go here.";
            exit;
        }
        if (($positionId == 121 && $myTeam != $gameIsland2) || ($positionId == 122 && $myTeam != $gameIsland6) || ($positionId == 123 && $myTeam != $gameIsland7) || ($positionId == 124 && $myTeam != $gameIsland9)) {
            echo "Need to own the island to place a missile.";
            exit;
        }
        if ($num_results > 0) {
            echo "Missile already at this site.";
            exit;
        }
    } else {  //land or water position
        if ($placementUnitId == 15) {
            echo "Missiles only go on missile sites (red squares).";  //missile positions checked above
            exit;
        }
        if ($positionId <= 54) {  //water positions
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
        $listPiecesInPosition_UnitIds = [];  //checking blockade
        if ($num_results > 0) {
            for ($i = 0; $i < $num_results; $i++) {
                $r = $results->fetch_assoc();
                $thisPieceInPosition_UnitId = $r['placementUnitId'];
                array_push($listPiecesInPosition_UnitIds, $thisPieceInPosition_UnitId);
            }
        }
        if ($placementUnitId == 1 && in_array(1, $listPiecesInPosition_UnitIds)) {  //subs block subs
            echo "Blockaded by another sub.";
            exit;
        }
        if (($placementUnitId == 0 || $placementUnitId == 2 || $placementUnitId == 3) && (in_array(2, $listPiecesInPosition_UnitIds) || in_array(3, $listPiecesInPosition_UnitIds))) {
            echo "Blockaded by another ship.";
            exit;
        }
    }
}

if ($_SESSION['dist'][$placementPositionId][$positionId] > 1 && $placementUnitId != 15 && $placementCurrentMoves > 0) {  //don't care about missile dist
    echo "Can only move 1 space at a time.";
    exit;
}

if ($placementUnitId != 15) {
    if ($_SESSION['dist'][$placementPositionId][$positionId] > 1) {
        echo "Can only move 1 space at a time.";
        exit;
    }
    if ($placementCurrentMoves == 0 && $_SESSION['dist'][$placementPositionId][$positionId] != 0) {
        echo "Not enough moves for this piece.";
        exit;
    }
}

$one = 1;
$query = 'SELECT newsTeam, newsEffect, newsPieces, newsZone FROM newsAlerts WHERE (newsGameId = ?) AND (newsActivated = ?) AND (newsLength >= ?)';
$query = $db->prepare($query);
$query->bind_param("iii", $gameId, $one, $one);
$query->execute();
$results = $query->get_result();
$num_results = $results->num_rows;
if ($num_results > 0) {
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
                    ($newsZone == $positionId && $positionId < 100) ||
                    ($newsZone == $placementPositionId && $placementPositionId < 100) ||
                    (in_array(($positionId), ($islandPositions[$newsZone-101]))) ||
                    (in_array(($placementPositionId), ($islandPositions[$newsZone-101]))) ||
                    (($newsZone > 1000) && (($newsZone - 1000 == $positionId) || ($newsZone - 1000 == $placementPositionId)))) {
                    $decoded = json_decode($newsPieces, true);
                    if ((int) $decoded[$placementUnitName] == 1) {
                        if ((int) $placementPositionId != 118){  //purchased is exempt
                            $thingToEcho = -2;
                        }
                    }
                }
            }
        }
    }
}

$killed = 0;  //allowed to move at this point from all game rules / logic
$thingToEcho = "DEFAULT THING TO ECHO";

if ($placementUnitId == 11 || $placementUnitId == 12 || $placementUnitId == 13 || $placementUnitId == 14) {
    $adjSam = array();
    for ($i = 55; $i <= 117; $i++) {
        if ($_SESSION['dist'][$positionId][$i] <= 1) {
            array_push($adjSam, $i);
        }
    }
    for ($i = 0; $i < sizeof($adjSam); $i++) {
        $query = 'SELECT placementPositionId FROM placements WHERE (placementPositionId = ?) AND (placementTeamId != ?) AND (placementUnitId = 10) AND (placementGameId = ?)';
        $query = $db->prepare($query);
        $position = $adjSam[$i];
        $query->bind_param("isi", $position, $myTeam, $gameId);
        $query->execute();
        $results = $query->get_result();
        $num_results = $results->num_rows;

        for ($k = 0; $k < $num_results; $k++) {
            $diceRoll = rand(1, 6);
            $thisSam = $results->fetch_assoc();
            $samPosition = (int) $thisSam['placementPositionId'];
            if ($positionId == $samPosition || $placementUnitId != 13) {
                if ($diceRoll >= $_SESSION['attack'][10][$placementUnitId]) {
                    $killed = 1;
                    $thingToEcho = "Piece was destroyed by Sam.";
                    break;
                }
            }

        }


    }
}

//check missile
if ($placementUnitId == 0 || $placementUnitId == 2 || $placementUnitId == 3) {
    $missileTargets = [[2, 3, 4, 10, 11, 15, 16], [16, 17, 18, 24, 25, 29, 30, 31], [19, 20, 21, 26, 27, 32, 33, 34], [28, 35, 36, 41, 42]];
    for ($x = 0; $x < 4; $x++) {
        if (in_array($positionId, $missileTargets[$x])) {
            $missilePosition = $x + 121;
            $query = 'SELECT placementId FROM placements WHERE placementPositionId = ? AND placementGameId = ?';
            $query = $db->prepare($query);
            $query->bind_param("ii", $missilePosition, $gameId);
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
                    $query = 'DELETE FROM placements WHERE placementId = ?';
                    $query = $db->prepare($query);
                    $query->bind_param("i", $missilePlacementId);
                    $query->execute();

                    $updateType = "pieceKilled";
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

    $updateType = "pieceKilled";
    $query = 'INSERT INTO updates (updateGameId, updateType, updatePlacementId) VALUES (?, ?, ?)';
    $query = $db->prepare($query);
    $query->bind_param("isi", $gameId, $updateType, $placementId);
    $query->execute();

    echo $thingToEcho;
    exit;
} else {
    if ($placementUnitId == 0 || $placementUnitId == 3) {  //update pieces inside this piece's container
        $query = 'UPDATE placements SET placementPositionId = ? WHERE (placementContainerId = ?)';
        $query = $db->prepare($query);
        $query->bind_param("ii", $positionId, $placementId);
        $query->execute();
    }

    $query = 'UPDATE placements SET placementPositionId = ?, placementCurrentMoves = placementCurrentMoves - 1, placementContainerId = ? WHERE (placementId = ?)';
    $query = $db->prepare($query);
    $query->bind_param("iii", $positionId,  $containerId,  $placementId);
    $query->execute();

    $query = 'INSERT INTO movements (movementGameId, movementFromPosition, movementFromContainer, movementNowPlacement) VALUES (?, ?, ?, ?)';
    $query = $db->prepare($query);
    $query->bind_param("iiii", $gameId, $placementPositionId, $placementContainerId, $placementId);
    $query->execute();

    $updateType = "pieceMove";
    $query = 'INSERT INTO updates (updateGameId, updateType, updatePlacementId, updateNewPositionId, updateNewContainerId) VALUES (?, ?, ?, ?, ?)';
    $query = $db->prepare($query);
    $query->bind_param("isiii", $gameId, $updateType, $placementId, $positionId, $containerId);
    $query->execute();


    //TODO: flag capture check and updates


    echo "Moved the piece.";
    exit;
}


