<?php
session_start();
include("../../db.php");
$gameId = $_SESSION['gameId'];
$myTeam = $_SESSION['myTeam'];
$query = 'SELECT gameActive, gamePhase, gameCurrentTeam, gameBattleSection, gameBattleSubSection, gameBattlePosSelected FROM GAMES WHERE gameId = ?';
$preparedQuery = $db->prepare($query);
$preparedQuery->bind_param("i", $gameId);
$preparedQuery->execute();
$results = $preparedQuery->get_result();
$r = $results->fetch_assoc();
$gamePhase = $r['gamePhase'];
$gameCurrentTeam = $r['gameCurrentTeam'];
$gameBattleSection = $r['gameBattleSection'];
$gameBattleSubSection = $r['gameBattleSubSection'];
$gameBattlePosSelected = $r['gameBattlePosSelected'];
if ($r['gameActive'] != 1) {
    header("location:home.php?err=1");
    exit;
}
if ($gamePhase != 2 || $myTeam == "Spec") {
    echo "It is not the right phase for this.";
    exit;
}
if ($gameBattleSubSection != "choosing_pieces" || $gameBattleSection == "none" || $gameBattleSection == "selectPos" || $gameBattleSection == "selectPieces") {
    echo "Unable to change section, wrong subsection/section.";
    exit;
}
if ((($gameBattleSection == "attack" || $gameBattleSection == "askRepeat") && $myTeam != $gameCurrentTeam) || ($gameBattleSection == "counter" && $myTeam == $gameCurrentTeam)) {
    echo "Not your turn to change section.";
    exit;
}
if ($gameBattleSection == "attack" || $gameBattleSection == "counter") {
    $query3 = "SELECT battlePieceId, battlePieceState FROM battlePieces WHERE battleGameId = ? AND (battlePieceState = 5 OR battlePieceState = 6)";
    $preparedQuery3 = $db->prepare($query3);
    $preparedQuery3->bind_param("i", $gameId);
    $preparedQuery3->execute();
    $results3 = $preparedQuery3->get_result();
    $numResults3 = $results3->num_rows;
    for ($i = 0; $i < $numResults3; $i++) {
        $r = $results3->fetch_assoc();
        $battlePieceId = $r['battlePieceId'];
        $battlePieceState = $r['battlePieceState'];
        $query = 'UPDATE battlePieces SET battlePieceState = battlePieceState - 4 WHERE battlePieceId = ?';
        $preparedQuery = $db->prepare($query);
        $preparedQuery->bind_param("i", $battlePieceId);
        $preparedQuery->execute();
        $battle_outcome = "";
        $updateType = "battleMove";
        $newPositionId = $battlePieceState - 4;
        $query = 'INSERT INTO updates (updateGameId, updateType, updatePlacementId, updateNewPositionId, updateHTML) VALUES (?, ?, ?, ?, ?)';
        $query = $db->prepare($query);
        $query->bind_param("isiis", $gameId, $updateType, $battlePieceId, $newPositionId, $battle_outcome);
        $query->execute();
    }
    if ($gameBattleSection == "attack") {
        $newSection = "counter";
    } else {
        $newSection = "askRepeat";
    }
    $query3 = "SELECT battlePieceId, battlePieceState FROM battlePieces WHERE battleGameId = ? AND (battlePieceState = 3 OR battlePieceState = 4)";
    $preparedQuery3 = $db->prepare($query3);
    $preparedQuery3->bind_param("i", $gameId);
    $preparedQuery3->execute();
    $results3 = $preparedQuery3->get_result();
    $numResults3 = $results3->num_rows;
    for ($i = 0; $i < $numResults3; $i++) {
        $r = $results3->fetch_assoc();
        $battlePieceId = $r['battlePieceId'];
        $battlePieceState = $r['battlePieceState'];
        $query = 'UPDATE battlePieces SET battlePieceState = battlePieceState - 2 WHERE battlePieceId = ?';
        $preparedQuery = $db->prepare($query);
        $preparedQuery->bind_param("i", $battlePieceId);
        $preparedQuery->execute();
        $battle_outcome = "";
        $updateType = "battleMove";
        $newPositionId = $battlePieceState - 2;
        $query = 'INSERT INTO updates (updateGameId, updateType, updatePlacementId, updateNewPositionId, updateHTML) VALUES (?, ?, ?, ?, ?)';
        $query = $db->prepare($query);
        $query->bind_param("isiis", $gameId, $updateType, $battlePieceId, $newPositionId, $battle_outcome);
        $query->execute();
    }
    $query = 'UPDATE games SET gameBattleSection = ? WHERE gameId = ?';
    $preparedQuery = $db->prepare($query);
    $preparedQuery->bind_param("si", $newSection, $gameId);
    $preparedQuery->execute();
    $updateType = "getBoard";
    $query = 'INSERT INTO updates (updateGameId, updateType) VALUES (?, ?)';  //need to make board look like selecting stuff
    $query = $db->prepare($query);
    $query->bind_param("is", $gameId, $updateType);
    $query->execute();
    echo "Switched Battle Turn.";
    exit;
} else {  //askRepeat, clicks to exit the game
    $query = 'DELETE FROM battlePieces WHERE battleGameId = ?';  //handled in html when getBoard section is none?
    $query = $db->prepare($query);
    $query->bind_param("i", $gameId);
    $query->execute();
    $newSection = "none";
    $newBattleSubSection = "choosing_pieces";
    $newBattleLastMessage = "Reset Message";
    $query = 'UPDATE games SET gameBattleSection = ?, gameBattleSubSection = ?, gameBattlePosSelected = -1, gameBattleLastRoll = 1, gameBattleTurn = 0, gameBattleLastMessage = ? WHERE gameId = ?';
    $preparedQuery = $db->prepare($query);
    $preparedQuery->bind_param("sssi", $newSection, $newBattleSubSection, $newBattleLastMessage, $gameId);
    $preparedQuery->execute();
    $flagPositions = [75, 79, 85, 86, 90, 94, 97, 100, 103, 107, 111, 114, 55, 65];
    if (in_array($gameBattlePosSelected, $flagPositions)) {
        $islandNum = array_search($gameBattlePosSelected, $flagPositions) + 1;
        $query = 'SELECT gameIsland' . $islandNum . ' FROM GAMES WHERE gameId = ?';
        $preparedQuery = $db->prepare($query);
        $preparedQuery->bind_param("i", $gameId);
        $preparedQuery->execute();
        $results = $preparedQuery->get_result();
        $r = $results->fetch_assoc();
        $islandOwner = $r['gameIsland' . $islandNum];
        if ($islandOwner != $myTeam) {
            $query = 'SELECT placementId FROM placements WHERE placementPositionId = ? AND placementTeamId != ?';  //get the other pieces that are there
            $preparedQuery = $db->prepare($query);
            $preparedQuery->bind_param("is", $gameBattlePosSelected, $myTeam);
            $preparedQuery->execute();
            $results = $preparedQuery->get_result();
            $num_results = $results->num_rows;
            if ($num_results == 0) {
                $query = 'UPDATE games SET gameIsland' . $islandNum . ' = ?, game' . $myTeam . 'Hpoints = game' . $myTeam . 'Hpoints + 1 WHERE gameId = ?';
                $query = $db->prepare($query);
                $query->bind_param("si", $myTeam, $gameId);
                $query->execute();
                $updateType = "islandOwnerChange";
                $query = 'INSERT INTO updates (updateGameId, updateType, updatePlacementId, updateHTML) VALUES (?, ?, ?, ?)';
                $query = $db->prepare($query);
                $query->bind_param("isis", $gameId, $updateType, $islandNum, $myTeam);
                $query->execute();
                $query = 'DELETE FROM movements WHERE movementGameId = ?';
                $query = $db->prepare($query);
                $query->bind_param("i", $gameId);
                $query->execute();
            }
        }
    }
    $updateType = "getBoard";
    $query = 'INSERT INTO updates (updateGameId, updateType) VALUES (?, ?)';  //need to make board look like selecting stuff
    $query = $db->prepare($query);
    $query->bind_param("is", $gameId, $updateType);
    $query->execute();
    echo "Battle Ended.";
    exit;
}
