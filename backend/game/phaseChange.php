<?php
session_start();
include("../db.php");
$gameId = $_SESSION['gameId'];
$myTeam = $_SESSION['myTeam'];
$query = 'SELECT gameActive, gamePhase, gameTurn, gameCurrentTeam, gameBattleSection, gameIsland1, gameIsland2, gameIsland3, gameIsland4, gameIsland5, gameIsland6, gameIsland7, gameIsland8, gameIsland9, gameIsland10, gameIsland11, gameIsland12, gameIsland13, gameIsland14 FROM games WHERE gameId = ?';
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
if ($r['gameActive'] != 1) {
    header("location:index.php?err=1");
    exit;
}
if ($myTeam != $gameCurrentTeam) {
    echo "Not your team's turn.";
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
$arrayOfMoves = [2, 2, 2, 2, 1, 1, 1, 1, 2, 3, 1, 4, 6, 5, 5, 0];
switch ($newPhaseNum) {
    case 0:  //News Alerts
        $query = "SELECT newsId, newsEffect, newsZone, newsRollValue, newsTeam FROM newsAlerts WHERE newsGameId = ? AND newsActivated = 0 AND newsLength != 0 ORDER BY newsOrder";
        $preparedQuery = $db->prepare($query);
        $preparedQuery->bind_param("i", $gameId);
        $preparedQuery->execute();
        $results = $preparedQuery->get_result();
        $num_results5 = $results->num_rows;
        $decrementValue = 1;
        $query = 'UPDATE newsAlerts SET newsLength = newsLength - 1 WHERE (newsGameId = ?) AND (newsActivated = 1) AND (newsLength != 0)';
        $query = $db->prepare($query);
        $query->bind_param("i", $gameId);
        $query->execute();
        if ($num_results5 != 0) {
            $r = $results->fetch_assoc();
            $newsId = $r['newsId'];
            $newsEffect = $r['newsEffect'];
            $newsZone = $r['newsZone'];
            $newsRollValue = $r['newsRollValue'];
            $newsTeam = $r['newsTeam'];
            $query = 'UPDATE newsAlerts SET newsActivated = 1 WHERE (newsId = ?)';
            $query = $db->prepare($query);
            $query->bind_param("i", $newsId);
            $query->execute();
            if ($newsEffect == "rollDie") {
                $islandSpots = [[75, 76, 77, 78], [79, 80, 81, 82], [83, 84, 85], [86, 87, 88, 89], [90, 91, 92, 93], [94, 95, 96], [97, 98, 99], [100, 101, 102], [103, 104, 105, 106], [107, 108, 109, 110], [111, 112, 113], [114, 115, 116, 117], [55, 56, 57, 58, 59, 60, 61, 62, 63, 64], [65, 66, 67, 68, 69, 70, 71, 72, 73, 74]];
                $islandIndex = $newsZone - 101;
                $thisIslandSpots = $islandSpots[$islandIndex];
                for ($x = 0; $x < sizeof($thisIslandSpots); $x++) {
                    if ($newsTeam == "All") {
                        $team1 = "Red";
                        $team2 = "Blue";
                    } else {
                        $team1 = $newsTeam;
                        $team2 = $newsTeam;
                    }
                    $query = "SELECT placementId, placementUnitId FROM placements WHERE placementGameId = ? AND placementPositionId = ? AND (placementTeamId = ? OR placementTeamId = ?)";
                    $preparedQuery = $db->prepare($query);
                    $preparedQuery->bind_param("iiss", $gameId, $thisIslandSpots[$x], $team1, $team2);
                    $preparedQuery->execute();
                    $results = $preparedQuery->get_result();
                    $num_results = $results->num_rows;
                    for ($i = 0; $i < $num_results; $i++) {
                        $r = $results->fetch_assoc();
                        $RandRoll = rand(1, 6);
                        if ($RandRoll < $newsRollValue) {
                            $placementId = $r['placementId'];
                            $query = 'DELETE FROM placements WHERE placementId = ?';
                            $query = $db->prepare($query);
                            $query->bind_param("i", $placementId);
                            $query->execute();
                            $query = 'DELETE FROM placements WHERE placementContainerId = ?';
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
            }
        }
        $moveEffect = "addMove";
        $query4 = "SELECT newsId FROM newsAlerts WHERE newsGameId = ? AND newsActivated = 1 AND newsLength = 1 AND newsTeam != ? AND newsEffect = ? ORDER BY newsOrder";
        $preparedQuery4 = $db->prepare($query4);
        $preparedQuery4->bind_param("iss", $gameId, $myTeam, $moveEffect);
        $preparedQuery4->execute();
        $results = $preparedQuery4->get_result();
        $num_results1 = $results->num_rows;
        if ($num_results1 > 0) {
            $arrayOfPlacementMoves = [];
            $query = 'SELECT placementId, placementUnitId FROM placements WHERE placementGameId = ? AND placementTeamId != ?';
            $query = $db->prepare($query);
            $query->bind_param("is", $gameId, $myTeam);
            $query->execute();
            $results = $query->get_result();
            $num_results = $results->num_rows;
            for ($x = 0; $x < $num_results; $x++) {
                $r = $results->fetch_assoc();
                $placementId = $r['placementId'];
                $placementUnitId = $r['placementUnitId'];
                $placementMovesReset = $arrayOfMoves[$placementUnitId] + $num_results1;
                array_push($arrayOfPlacementMoves, array($placementId, $placementUnitId, $placementMovesReset, 0));
            }
            if (sizeof($arrayOfPlacementMoves) > 0) {
                $JSONArray = json_encode($arrayOfPlacementMoves);
                $updateType = "updateMoves";
                $query = 'INSERT INTO updates (updateGameId, updateType, updateHTML) VALUES (?, ?, ?)';
                $query = $db->prepare($query);
                $query->bind_param("iss", $gameId, $updateType, $JSONArray);
                $query->execute();
            }
            $query4 = 'UPDATE placements SET placementCurrentMoves = placementCurrentMoves + '.$num_results1.' WHERE placementGameId = ? AND placementTeamId != ?';
            $preparedQuery4 = $db->prepare($query4);
            $preparedQuery4->bind_param("is", $gameId, $myTeam);
            $preparedQuery4->execute();
        }
        break;
    case 1:  //Buy Reinforcements
        if ($gameTurn > 1) {
            $gameIslands = [$gameIsland1, $gameIsland2, $gameIsland3, $gameIsland4, $gameIsland5, $gameIsland6, $gameIsland7, $gameIsland8, $gameIsland9, $gameIsland10, $gameIsland11, $gameIsland12, $gameIsland13, $gameIsland14];
            $islandPoints = [4, 6, 4, 3, 8, 7, 7, 10, 8, 5, 5, 5, 15, 25];
            $totalPointsToAdd = 0;
            $otherTeamPointsToAdd = 0;
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
                    } else {
                        $otherTeamPointsToAdd += $islandPoints[$x];
                    }
                }
            }
            if ($gameIslands[12] == $gameCurrentTeam) {
                $totalPointsToAdd += 15;
            }
            if ($gameIslands[13] == $gameCurrentTeam) {
                $totalPointsToAdd += 25;
            }
            $otherTeamName = "Blue";
            if ($myTeam == "Blue") {
                $otherTeamName = "Red";
            }
            $query = 'UPDATE games SET game'.$myTeam.'Rpoints = game'.$myTeam.'Rpoints + ? WHERE (gameId = ?)';
            $query = $db->prepare($query);
            $query->bind_param("ii", $totalPointsToAdd, $gameId);
            $query->execute();
            
            $query = 'UPDATE games SET game'.$otherTeamName.'Rpoints = game'.$otherTeamName.'Rpoints + ? WHERE (gameId = ?)';
            $query = $db->prepare($query);
            $query->bind_param("ii", $otherTeamPointsToAdd, $gameId);
            $query->execute();
        }
        break;
    case 2:  //Combat (no change)
        break;
    case 3:  //Fortify
        $query = 'SELECT b.placementId, b.placementUnitId, b.placementCurrentMoves, b.placementBattleUsed FROM (SELECT placementPositionId FROM placements WHERE placementGameId = ? AND placementTeamId = ? AND placementUnitId = 14) a JOIN (SELECT placementId, placementPositionId, placementUnitId, placementCurrentMoves, placementBattleUsed FROM placements WHERE placementGameId = ? AND placementTeamId = ? AND (placementUnitId = 11 OR placementUnitId = 12 OR placementUnitId = 13)) b USING(placementPositionId) WHERE a.placementPositionId = b.placementPositionId';
        $query = $db->prepare($query);
        $query->bind_param("isis", $gameId, $myTeam, $gameId, $myTeam);
        $query->execute();
        $results = $query->get_result();
        $num_results = $results->num_rows;
        $arrayOfPlacementMoves = [];
        for ($i = 0; $i < $num_results; $i++) {
            $r = $results->fetch_assoc();
            $placementId = $r['placementId'];
            $placementUnitId = $r['placementUnitId'];
            $placementCurrentMoves = $r['placementCurrentMoves'];
            $placementBattleUsed = $r['placementBattleUsed'];
            $updateValue = 2;
            if ($placementUnitId >= 12) {
                $updateValue = 3;
            }
            $newMoves = $placementCurrentMoves + $updateValue;
            $query = 'UPDATE placements SET placementCurrentMoves = placementCurrentMoves + '.$updateValue.' WHERE (placementId = ?)';
            $query = $db->prepare($query);
            $query->bind_param("i", $placementId);
            $query->execute();
            array_push($arrayOfPlacementMoves, array($placementId, $placementUnitId, $newMoves, $placementBattleUsed));
        }
        if ($arrayOfPlacementMoves > 0) {
            $JSONArray = json_encode($arrayOfPlacementMoves);
            $updateType = "updateMoves";
            $query = 'INSERT INTO updates (updateGameId, updateType, updateHTML) VALUES (?, ?, ?)';
            $query = $db->prepare($query);
            $query->bind_param("iss", $gameId, $updateType, $JSONArray);
            $query->execute();
        }
        break;
    case 4:  //Reinforcement Place
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
        for ($i = 0; $i < $num_results; $i++) {
            $r = $results->fetch_assoc();
            $thisPosition = $r['placementId'];
            array_push($carrierSpots, $thisPosition);
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
        $arrayOfPlacementMoves = [];
        $query = 'SELECT placementId, placementUnitId FROM placements WHERE placementGameId = ? AND placementTeamId = ?';
        $query = $db->prepare($query);
        $query->bind_param("is", $gameId, $myTeam);
        $query->execute();
        $results = $query->get_result();
        $num_results = $results->num_rows;
        for ($x = 0; $x < $num_results; $x++) {
            $r = $results->fetch_assoc();
            $placementId = $r['placementId'];
            $placementUnitId = $r['placementUnitId'];
            $placementMovesReset = $arrayOfMoves[$placementUnitId];
            $query2 = 'UPDATE placements SET placementBattleUsed = 0, placementCurrentMoves = ? WHERE (placementId = ?)';
            $query2 = $db->prepare($query2);
            $query2->bind_param("ii", $placementMovesReset, $placementId);
            $query2->execute();
            array_push($arrayOfPlacementMoves, array($placementId, $placementUnitId, $placementMovesReset, 0));
        }
        if (sizeof($arrayOfPlacementMoves) > 0) {
            $JSONArray = json_encode($arrayOfPlacementMoves);
            $updateType = "updateMoves";
            $query = 'INSERT INTO updates (updateGameId, updateType, updateHTML) VALUES (?, ?, ?)';
            $query = $db->prepare($query);
            $query->bind_param("iss", $gameId, $updateType, $JSONArray);
            $query->execute();
        }
        $query = 'UPDATE games SET gameTurn = gameTurn + 1 WHERE (gameId = ?)';
        $query = $db->prepare($query);
        $query->bind_param("i", $gameId);
        $query->execute();
        break;
    default:
        echo "Failed to switch phase. (number outside phase range)";  //unlikely to occur, we mod phase number above
        exit;
}
$query = 'DELETE FROM movements WHERE movementGameId = ?';
$query = $db->prepare($query);
$query->bind_param("i", $gameId);
$query->execute();
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
