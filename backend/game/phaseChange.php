<?php
session_start();
include("../db.php");

$gameId = $_SESSION['gameId'];
$myTeam = $_SESSION['myTeam'];

$query = 'SELECT * FROM GAMES WHERE gameId = ?';
$preparedQuery = $db->prepare($query);
$preparedQuery->bind_param("i", $gameId);
$preparedQuery->execute();
$results = $preparedQuery->get_result();
$r = $results->fetch_assoc();

$gamePhase = $r['gamePhase'];
$gameTurn = $r['gameTurn'];
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

$airFieldSpots = [];  //can only remain on the airfield if you own the island
if ($gameIsland1 == $myTeam) {
    array_push($airFieldSpots, 78);
}
if ($gameIsland3 == $myTeam) {
    array_push($airFieldSpots, 83);
}
if ($gameIsland4 == $myTeam) {
    array_push($airFieldSpots, 89);
}
if ($gameIsland11 == $myTeam) {
    array_push($airFieldSpots, 113);
}
if ($gameIsland12 == $myTeam) {
    array_push($airFieldSpots, 116);
}
if ($gameIsland13 == $myTeam) {
    array_push($airFieldSpots, 57);
    array_push($airFieldSpots, 56);
}
if ($gameIsland14 == $myTeam) {
    array_push($airFieldSpots, 66);
    array_push($airFieldSpots, 68);
}

if ($myTeam != $gameCurrentTeam) {
    echo "Not your teams turn.";
    exit;
}
if ($gameBattleSection != "none") {
    echo "Cannot change phase during battle.";
    exit;
}

$newPhaseNum = ($gamePhase + 1) % 7;
if ($newPhaseNum == 0) {
    if ($myTeam == "Red") {
        $newGameCurrentTeam = "Blue";
    } else {
        $newGameCurrentTeam = "Red";
    }
} else {
    $newGameCurrentTeam = $myTeam;
}


switch ($newPhaseNum) {

    case 0:  //News Alert
        //News Alert
        //do news alerts







        break;
    case 1:  //Buy Reinforcements
        //give the points for island ownership to this team (except if newsalert from bank drain prevents it)
        if ($gameTurn > 1) {
            $gameIslands = [$gameIsland1, $gameIsland2, $gameIsland3, $gameIsland4, $gameIsland5, $gameIsland6, $gameIsland7, $gameIsland8, $gameIsland9, $gameIsland10, $gameIsland11, $gameIsland12, $gameIsland13, $gameIsland14];
            $islandPoints = [4, 6, 4, 3, 8, 7, 7, 10, 8, 5, 5, 5, 15, 25];
            $totalPointsToAdd = 0;
            $bankDrain = "bankDrain";
            for ($x = 0; $x < 12; $x++) {
                if ($gameIslands[$x] == $myTeam) {
                    $zone = $x + 101;
                    $query4 = "SELECT newsId FROM newsAlerts WHERE newsGameId = ? AND newsActivated = 1 AND newsLength > 0 AND newsTeam != ? AND newsEffect = ? AND newsZone = ? ORDER BY newsOrder";
                    $preparedQuery4 = $db->prepare($query4);
                    $preparedQuery4->bind_param("issi", $gameId, $myTeam, $bankDrain, $zone);
                    $preparedQuery4->execute();
                    $results = $preparedQuery4->get_result();
                    $num_results = $results->num_rows;
                    if ($num_results == 0) {
                        $totalPointsToAdd += $islandPoints[$x];
                    }
                }
            }
            if ($gameIslands[12] == $gameCurrentTeam) {
                $totalPointsToAdd += 15;
            }
            if ($gameIslands[13] == $gameCurrentTeam) {
                $totalPointsToAdd += 25;
            }

            $query = 'UPDATE games SET game'.$myTeam.'Rpoints = game'.$myTeam.'Rpoints + ? WHERE (gameId = ?)';
            $query = $db->prepare($query);
            $query->bind_param("ii", $totalPointsToAdd, $gameId);
            $query->execute();
        }
        break;
    case 2:  //Combat (no change)
        break;
    case 3:  //Fortify
        //refuel planes
        $query = 'SELECT b.placementId, b.placementUnitId FROM (SELECT placementPositionId FROM placements WHERE placementGameId = ? AND placementTeamId = ? AND placementUnitId = 14) a JOIN (SELECT placementId, placementPositionId, placementUnitId, placementCurrentMoves FROM placements WHERE placementGameId = ? AND placementTeamId = ? AND (placementUnitId = 11 OR placementUnitId = 12 OR placementUnitId = 13)) b USING(placementPositionId) WHERE a.placementPositionId = b.placementPositionId';
        $query = $db->prepare($query);
        $query->bind_param("isis", $gameId, $myTeam, $gameId, $myTeam);
        $query->execute();
        $results = $query->get_result();
        $num_results = $results->num_rows;
        for ($i = 0; $i < $num_results; $i++) {
            $r = $results->fetch_assoc();
            $placementId = $r['placementId'];
            $placementUnitId = $r['placementUnitId'];

            $updateValue = 2;
            if ($placementUnitId >= 12) {
                $updateValue = 3;
            }

            $query = 'UPDATE placements SET placementCurrentMoves = placementCurrentMoves + '.$updateValue.' WHERE (placementId = ?)';
            $query = $db->prepare($query);
            $query->bind_param("i", $placementId);
            $query->execute();
        }
        break;
    case 4:  //Reinforcement Place
        //delete planes not in airfield or carriers
        $airfields = [56, 57, 66, 68, 78, 83, 89, 113, 116];
        $airFieldOwner = [$gameIsland13, $gameIsland13, $gameIsland14, $gameIsland14, $gameIsland1, $gameIsland3, $gameIsland4, $gameIsland11, $gameIsland12];
        $airFieldSpots = [];
        for ($x = 0; $x < 9; $x++) {
            if ($airFieldOwner[$x] == $myTeam) {
                array_push($airFieldSpots, $airfields[$x]);
            }
        }

        $carrierSpots = [];
        $query = 'SELECT placementId FROM placements WHERE (placementGameId = ?) AND (placementUnitId = 3) AND (placementTeamId = ?)';
        $query = $db->prepare($query);
        $query->bind_param("is", $gameId, $myTeam);
        $query->execute();
        $results = $query->get_result();
        $num_results = $results->num_rows;
        if ($num_results > 0) {
            for ($i = 0; $i < $num_results; $i++) {
                $r = $results->fetch_assoc();
                $thisPosition = $r['placementId'];
                array_push($carrierSpots, $thisPosition);
            }
        }

        $query = 'SELECT placementId, placementUnitId, placementContainerId, placementPositionId FROM placements WHERE (placementTeamId = ?) AND (placementGameId = ?) AND (placementUnitId = 9 OR placementUnitId = 11 OR placementUnitId = 12 OR placementUnitId = 13 OR placementUnitId = 14) AND (placementPositionId != 118)';
        $query = $db->prepare($query);
        $query->bind_param("si", $myTeam, $gameId);
        $query->execute();
        $results = $query->get_result();
        $num_results = $results->num_rows;
        if ($num_results > 0) {
            for ($i = 0; $i < $num_results; $i++) {
                $r = $results->fetch_assoc();
                $placementId = $r['placementId'];
                $placementUnitId = $r['placementUnitId'];
                $placementContainerId = $r['placementContainerId'];
                $placementPositionId = $r['placementPositionId'];
                if ((($placementUnitId == 9 && $placementPositionId < 55 && $placementContainerId == -1) || (($placementUnitId == 11 && (!in_array($placementContainerId, $carrierSpots) && !in_array($placementPositionId, $airFieldSpots)))) || (($placementUnitId > 11 && !in_array($placementPositionId, $airFieldSpots))))) {
                    //delete the real piece from database
                    $query = 'DELETE FROM placements WHERE placementId = ?';
                    $query = $db->prepare($query);
                    $query->bind_param("i", $placementId);
                    $query->execute();

                    $updateType = "pieceRemove";
                    $query = 'INSERT INTO updates (updateGameId, updateType, updatePlacementId) VALUES (?, ?, ?)';
                    $query = $db->prepare($query);
                    $query->bind_param("isi", $gameId, $updateType, $placementId);
                    $query->execute();
                }
            }
        }
        break;
    case 5:  //Hybrid Warfare
        //delete pieces not placed from 118
        $query = 'SELECT placementId FROM placements WHERE (placementPositionId = 118) AND (placementGameId = ?)';
        $query = $db->prepare($query);
        $query->bind_param("i", $gameId);
        $query->execute();
        $results = $query->get_result();
        $num_results = $results->num_rows;
        for ($i = 0; $i < $num_results; $i++) {
            $r = $results->fetch_assoc();
            $placementId = $r['placementId'];
            $query = 'DELETE FROM placements WHERE placementId = ?';
            $query = $db->prepare($query);
            $query->bind_param("i", $placementId);
            $query->execute();

            $updateType = "pieceRemove";
            $query = 'INSERT INTO updates (updateGameId, updateType, updatePlacementId) VALUES (?, ?, ?)';
            $query = $db->prepare($query);
            $query->bind_param("isi", $gameId, $updateType, $placementId);
            $query->execute();
        }
        break;
    case 6:  //Round Recap
        //reset moves / battle used
        $query = 'SELECT placementId, unitMoves FROM (SELECT placementId, placementUnitId FROM placements WHERE placementGameId = ? AND placementTeamId = ?) a NATURAL JOIN (SELECT unitId, unitMoves FROM units) b WHERE placementUnitId = unitId';
        $query = $db->prepare($query);
        $query->bind_param("is", $gameId, $myTeam);
        $query->execute();
        $results = $query->get_result();
        $num_results = $results->num_rows;
        for ($x = 0; $x < $num_results; $x++) {
            $r= $results->fetch_assoc();
            $placementId = $r['placementId'];
            $placementMovesReset = $r['unitMoves'];
            $query2 = 'UPDATE placements SET placementBattleUsed = 0, placementCurrentMoves = ? WHERE (placementId = ?)';
            $query2 = $db->prepare($query2);
            $query2->bind_param("ii", $placementMovesReset, $placementId);
            $query2->execute();
        }
        $query = 'UPDATE games SET gameTurn = gameTurn + 1 WHERE (gameId = ?)';
        $query = $db->prepare($query);
        $query->bind_param("i", $gameId);
        $query->execute();
        break;
    default:
        echo "Failed to switch phase. (number outside range)";  //unlikely to occur, we mod phase number above
        exit;
}

$query = 'UPDATE games SET gamePhase = ?, gameCurrentTeam = ? WHERE (gameId = ?)';
$query = $db->prepare($query);
$query->bind_param("isi", $newPhaseNum, $newGameCurrentTeam, $gameId);
$query->execute();

$updateType = "getBoard";
$query = 'INSERT INTO updates (updateGameId, updateType) VALUES (?, ?)';
$query = $db->prepare($query);
$query->bind_param("is", $gameId, $updateType);
$query->execute();

echo "Changed Phase.";
exit;
