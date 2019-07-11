<?php
session_start();
include("../../db.php");
$gameId = $_SESSION['gameId'];
$myTeam = $_SESSION['myTeam'];
$query = 'SELECT gameActive, gamePhase, gameCurrentTeam, gameBattleSection, gameBattleSubSection FROM GAMES WHERE gameId = ?';
$preparedQuery = $db->prepare($query);
$preparedQuery->bind_param("i", $gameId);
$preparedQuery->execute();
$results = $preparedQuery->get_result();
$r = $results->fetch_assoc();
$gamePhase = $r['gamePhase'];
$gameCurrentTeam = $r['gameCurrentTeam'];
$gameBattleSection = $r['gameBattleSection'];
$gameBattleSubSection = $r['gameBattleSubSection'];
if ($r['gameActive'] != 1) {
    header("location:index.php?err=1");
    exit;
}
if ($gamePhase != 2 || $myTeam == "Spec") {
    echo "It is not the right phase for this.";
    exit;
}
if ($gameBattleSubSection == "choosing_pieces") {
    echo "Not right subsection for this.";
    exit;
}
if ($gameBattleSubSection == "defense_bonus") {
    if ($myTeam == $gameCurrentTeam) {
        echo "Not allowed to defense bonus.";
        exit;
    }
    $query = 'SELECT placementId, placementUnitId FROM battlePieces RIGHT JOIN placements ON battlePieceId WHERE battlePieceId = placementId AND battleGameId = ? AND (battlePieceState = 3 or battlePieceState = 4) ORDER BY battlePieceState DESC';
    $preparedQuery = $db->prepare($query);
    $preparedQuery->bind_param("i", $gameId);
    $preparedQuery->execute();
    $results = $preparedQuery->get_result();
    $numResults = $results->num_rows;
    if ($numResults != 2) {
        echo "Failed to get both battle pieces.";
        exit;
    }
    $lastRoll = rand(1, 6);
    $r = $results->fetch_assoc();
    $attackId = $r['placementId'];  //4
    $attackUnitId = $r['placementUnitId'];
    $r = $results->fetch_assoc();
    $defendId = $r['placementId'];  //3
    $defendUnitId = $r['placementUnitId'];
    $needToHit = $_SESSION['attack'][$attackUnitId][$defendUnitId];
    $unitNames = ['Transport', 'Submarine', 'Destroyer', 'AircraftCarrier', 'ArmyCompany', 'ArtilleryBattery', 'TankPlatoon', 'MarinePlatoon', 'MarineConvoy', 'AttackHelo', 'SAM', 'FighterSquadron', 'BomberSquadron', 'StealthBomberSquadron', 'Tanker', 'LandBasedSeaMissile'];
    if ($needToHit == 0 && $lastRoll == 6) {
        $query = 'UPDATE battlePieces SET battlePieceWasHit = 0 WHERE battlePieceId = ?';
        $query = $db->prepare($query);
        $query->bind_param("i", $attackId);
        $query->execute();
        $gameBattleLastMessage = "Piece Survived the hit!";
    } elseif ($needToHit != 0 && $lastRoll >= $needToHit) {
        $query = 'UPDATE battlePieces SET battlePieceWasHit = 1 WHERE battlePieceId = ?';
        $query = $db->prepare($query);
        $query->bind_param("i", $defendId);
        $query->execute();
        $unitNames = ['Transport', 'Submarine', 'Destroyer', 'AircraftCarrier', 'ArmyCompany', 'ArtilleryBattery', 'TankPlatoon', 'MarinePlatoon', 'MarineConvoy', 'AttackHelo', 'SAM', 'FighterSquadron', 'BomberSquadron', 'StealthBomberSquadron', 'Tanker', 'LandBasedSeaMissile'];
        $gameBattleLastMessage = $unitNames[$attackUnitId]." Hit back ".$unitNames[$defendUnitId];
    } else {
        if ($needToHit == 0) {
            $gameBattleLastMessage = "Piece did not survive the hit.";
        } else {
            $gameBattleLastMessage = "Piece did not hit back.";
        }
    }
    $nextSubSection = "continue_choosing";
    $query = 'UPDATE games SET gameBattleSubSection = ?, gameBattleLastMessage = ?, gameBattleLastRoll = ? WHERE gameId = ?';
    $query = $db->prepare($query);
    $query->bind_param("ssii", $nextSubSection, $gameBattleLastMessage, $lastRoll, $gameId);
    $query->execute();
    $updateType = "rollBoard";
    $query = 'INSERT INTO updates (updateGameId, updateType) VALUES (?, ?)';
    $query = $db->prepare($query);
    $query->bind_param("is", $gameId, $updateType);
    $query->execute();
    echo "Attacked!";
    exit;
} else {
    if (($gameBattleSection == "attack" && $myTeam != $gameCurrentTeam) || ($gameBattleSection == "counter" && $myTeam == $gameCurrentTeam)) {
        echo "Not your turn to go back to choosing.";
        exit;
    }
    if ($gameBattleSection == "attack") {  //always going to be choosing pieces, otherwise wouldn't hit this button
        $order = "ASC";  // 3 attacking 4
    } else {
        $order = "DESC"; // 4 attacking 3
    }
    $query = 'SELECT battlePieceId, battlePieceWasHit, battlePieceState FROM battlePieces WHERE battleGameId = ? AND (battlePieceState = 3 or battlePieceState = 4) ORDER BY battlePieceState '.$order;
    $preparedQuery = $db->prepare($query);
    $preparedQuery->bind_param("i", $gameId);
    $preparedQuery->execute();
    $results = $preparedQuery->get_result();
    $numResults = $results->num_rows;
    if ($numResults != 2) {
        echo "Failed to get both center pieces.";  //unlikely, checked in the attack button before getting here
        exit;
    }
    $r = $results->fetch_assoc();
    $attackId = $r['battlePieceId'];
    $attackWasHit = $r['battlePieceWasHit'];
    $attackState = $r['battlePieceState'];
    $r = $results->fetch_assoc();
    $defendId = $r['battlePieceId'];
    $defendWasHit = $r['battlePieceWasHit'];
    $defendState = $r['battlePieceState'];
    if ($attackWasHit == 1) {
        $query = 'DELETE FROM battlePieces WHERE battlePieceId = ?';
        $query = $db->prepare($query);
        $query->bind_param("i", $attackId);
        $query->execute();
        $battle_outcome = "";
        $updateType = "battleRemove";
        $query = 'INSERT INTO updates (updateGameId, updateType, updatePlacementId, updateHTML) VALUES (?, ?, ?, ?)';
        $query = $db->prepare($query);
        $query->bind_param("isis", $gameId, $updateType, $attackId, $battle_outcome);
        $query->execute();
        
        //need to get fighters (11) out of the container (if it is a container)
        $query = 'SELECT * FROM placements WHERE (placementContainerId = ?) AND (placementUnitId = 11)';
        $query = $db->prepare($query);
        $query->bind_param("i", $attackId);
        $query->execute();
        $results = $query->get_result();
        $numresults = $results->num_rows;
        if ($numresults > 0){
            for ($i = 0; $i < $numresults; $i++) {

                $r = $results->fetch_assoc();
                $thisPlacementId = $r['placementId'];
                $thisPosition = $r['placementPositionId'];
                $thisMoves = $r['placementCurrentMoves'];
                $thisUnitId = $r['placementUnitId'];
    
                $noContainer = -1;
                $query = 'UPDATE placements SET placementContainerId = ? WHERE placementId = ?';
                $query = $db->prepare($query);
                $query->bind_param("ii", $noContainer, $thisPlacementId);
                $query->execute();
                
                $unitNames = ['Transport', 'Submarine', 'Destroyer', 'AircraftCarrier', 'ArmyCompany', 'ArtilleryBattery', 'TankPlatoon', 'MarinePlatoon', 'MarineConvoy', 'AttackHelo', 'SAM', 'FighterSquadron', 'BomberSquadron', 'StealthBomberSquadron', 'Tanker', 'LandBasedSeaMissile'];
    
                $newTitle = $unitNames[$thisUnitId]."\nMoves: ".$thisMoves;

                $updateType = "pieceMove";
                $query = 'INSERT INTO updates (updateGameId, updateType, updatePlacementId, updateNewPositionId, updateNewContainerId, updateHTML) VALUES (?, ?, ?, ?, ?, ?)';
                $query = $db->prepare($query);
                $query->bind_param("isiiis", $gameId, $updateType, $thisPlacementId, $thisPosition, $noContainer, $newTitle);
                $query->execute();
            }
        }
        $query = 'DELETE FROM placements WHERE placementId = ?';
        $query = $db->prepare($query);
        $query->bind_param("i", $attackId);
        $query->execute();

        $query = 'DELETE FROM placements WHERE placementContainerId = ?';
        $query = $db->prepare($query);
        $query->bind_param("i", $attackId);
        $query->execute();

        $updateType = "pieceRemove";
        $query = 'INSERT INTO updates (updateGameId, updateType, updatePlacementId) VALUES (?, ?, ?)';
        $query = $db->prepare($query);
        $query->bind_param("isi", $gameId, $updateType, $attackId);
        $query->execute();
    } else {
        $query = 'UPDATE battlePieces SET battlePieceState = battlePieceState + 2 WHERE battlePieceId = ?';
        $preparedQuery = $db->prepare($query);
        $preparedQuery->bind_param("i", $attackId);
        $preparedQuery->execute();
        $battle_outcome = "";
        $updateType = "battleMove";
        $newPositionId = $attackState + 2;
        $query = 'INSERT INTO updates (updateGameId, updateType, updatePlacementId, updateNewPositionId, updateHTML) VALUES (?, ?, ?, ?, ?)';
        $query = $db->prepare($query);
        $query->bind_param("isiis", $gameId, $updateType, $attackId, $newPositionId, $battle_outcome);
        $query->execute();
    }
    if ($defendWasHit == 1) {
        $query = 'DELETE FROM battlePieces WHERE battlePieceId = ?';
        $query = $db->prepare($query);
        $query->bind_param("i", $defendId);
        $query->execute();
        $battle_outcome = "";
        $updateType = "battleRemove";
        $query = 'INSERT INTO updates (updateGameId, updateType, updatePlacementId, updateHTML) VALUES (?, ?, ?, ?)';
        $query = $db->prepare($query);
        $query->bind_param("isis", $gameId, $updateType, $defendId, $battle_outcome);
        $query->execute();

        $query = 'SELECT * FROM placements WHERE (placementContainerId = ?) AND (placementUnitId = 11)';
        $query = $db->prepare($query);
        $query->bind_param("i", $defendId);
        $query->execute();
        $results = $query->get_result();
        $numresults = $results->num_rows;
        if ($numresults > 0){
            for ($i = 0; $i < $numresults; $i++) {

                $r = $results->fetch_assoc();
                $thisPlacementId = $r['placementId'];
                $thisPosition = $r['placementPositionId'];
                $thisMoves = $r['placementCurrentMoves'];
                $thisUnitId = $r['placementUnitId'];
    
                $noContainer = -1;
                $query = 'UPDATE placements SET placementContainerId = ? WHERE placementId = ?';
                $query = $db->prepare($query);
                $query->bind_param("ii", $noContainer, $thisPlacementId);
                $query->execute();
                
                $unitNames = ['Transport', 'Submarine', 'Destroyer', 'AircraftCarrier', 'ArmyCompany', 'ArtilleryBattery', 'TankPlatoon', 'MarinePlatoon', 'MarineConvoy', 'AttackHelo', 'SAM', 'FighterSquadron', 'BomberSquadron', 'StealthBomberSquadron', 'Tanker', 'LandBasedSeaMissile'];
    
                $newTitle = $unitNames[$thisUnitId]."\nMoves: ".$thisMoves;

                $updateType = "pieceMove";
                $query = 'INSERT INTO updates (updateGameId, updateType, updatePlacementId, updateNewPositionId, updateNewContainerId, updateHTML) VALUES (?, ?, ?, ?, ?, ?)';
                $query = $db->prepare($query);
                $query->bind_param("isiiis", $gameId, $updateType, $thisPlacementId, $thisPosition, $noContainer, $newTitle);
                $query->execute();
            }
        }

        $query = 'DELETE FROM placements WHERE placementId = ?';
        $query = $db->prepare($query);
        $query->bind_param("i", $defendId);
        $query->execute();

        $query = 'DELETE FROM placements WHERE placementContainerId = ?';
        $query = $db->prepare($query);
        $query->bind_param("i", $defendId);
        $query->execute();

        $updateType = "pieceRemove";
        $query = 'INSERT INTO updates (updateGameId, updateType, updatePlacementId) VALUES (?, ?, ?)';
        $query = $db->prepare($query);
        $query->bind_param("isi", $gameId, $updateType, $defendId);
        $query->execute();
    } else {
        $query = 'UPDATE battlePieces SET battlePieceState = battlePieceState - 2 WHERE battlePieceId = ?';
        $preparedQuery = $db->prepare($query);
        $preparedQuery->bind_param("i", $defendId);
        $preparedQuery->execute();
        $battle_outcome = "";
        $updateType = "battleMove";
        $newPositionId = $defendState - 2;
        $query = 'INSERT INTO updates (updateGameId, updateType, updatePlacementId, updateNewPositionId, updateHTML) VALUES (?, ?, ?, ?, ?)';
        $query = $db->prepare($query);
        $query->bind_param("isiis", $gameId, $updateType, $defendId, $newPositionId, $battle_outcome);
        $query->execute();
    }
    $nextSubSection = "choosing_pieces";
    $query = 'UPDATE games SET gameBattleSubSection = ? WHERE gameId = ?';
    $query = $db->prepare($query);
    $query->bind_param("si", $nextSubSection, $gameId);
    $query->execute();
    $updateType = "getBoard";
    $query = 'INSERT INTO updates (updateGameId, updateType) VALUES (?, ?)';
    $query = $db->prepare($query);
    $query->bind_param("is", $gameId, $updateType);
    $query->execute();
    echo "Select Pieces to attack.";
    exit;
}
