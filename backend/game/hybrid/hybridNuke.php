<?php
session_start();
include("../../db.php");
$gameId = $_SESSION['gameId'];
$myTeam = $_SESSION['myTeam'];
$islandNum = (int) $_REQUEST['islandNum'];
$query = 'SELECT gameActive, gamePhase, gameCurrentTeam, game'.$myTeam.'Hpoints FROM GAMES WHERE gameId = ?';
$preparedQuery = $db->prepare($query);
$preparedQuery->bind_param("i", $gameId);
$preparedQuery->execute();
$results = $preparedQuery->get_result();
$r = $results->fetch_assoc();
$gamePhase = $r['gamePhase'];
$gameCurrentTeam = $r['gameCurrentTeam'];
$points = $r['game'.$myTeam.'Hpoints'];
if ($r['gameActive'] != 1) {
    header("location:home.php?err=7");
    exit;
}
if ($myTeam != $gameCurrentTeam) {
    echo "It is not your team's turn.";
    exit;
}
if ($gamePhase != 5) {
    echo "It is not the right phase for this.";
    exit;
}
if ($points < 12) {
    echo "Not enough hybrid points.";
    exit;
}
$islandSpots = [
    [0, 1, 9, 13, 14, 75, 76, 77, 78],
    [2, 3, 4, 10, 11, 15, 16, 79, 80, 81, 82, 121],
    [4, 5, 6, 11, 12, 16, 17, 18, 83, 84, 85],
    [10, 11, 16, 16, 22, 23, 24, 86, 87, 88, 89],
    [13, 14, 15, 21, 22, 27, 28, 34, 35, 90, 91, 92, 93],
    [16, 17, 18, 24, 25, 29, 30, 31, 94, 95, 96, 122],
    [19, 20, 21, 26, 27, 32, 33, 34, 97, 98, 99, 123],
    [22, 23, 24, 28, 29, 36, 37, 100, 101, 102],
    [28, 35, 36, 41, 42, 103, 104, 105, 106, 124],
    [29, 30, 31, 37, 38, 43, 44, 45, 107, 108, 109, 110],
    [33, 34, 35, 40, 41, 47, 48, 49, 111, 112, 113],
    [36, 37, 42, 43, 50, 51, 52, 114, 115, 116, 117]];
$thisIslandSpots = $islandSpots[$islandNum - 1];
for ($x = 0; $x < sizeof($thisIslandSpots); $x++) {
    $positionId = $thisIslandSpots[$x];
    $query = 'SELECT placementId FROM placements WHERE placementPositionId = ? AND placementGameId = ? ORDER BY placementContainerId DESC';
    $query = $db->prepare($query);
    $query->bind_param("ii", $positionId, $gameId);
    $query->execute();
    $results = $query->get_result();
    $num_results = $results->num_rows;
    for ($i = 0; $i < $num_results; $i++) {
        $b = $results->fetch_assoc();
        $placementId = $b['placementId'];
        $query2 = 'DELETE FROM placements WHERE placementId = ?';
        $query2= $db->prepare($query2);
        $query2->bind_param("i", $placementId);
        $query2->execute();
        $updateType = "pieceRemove";
        $query = 'INSERT INTO updates (updateGameId, updateType, updatePlacementId) VALUES (?, ?, ?)';
        $query = $db->prepare($query);
        $query->bind_param("isi", $gameId, $updateType, $placementId);
        $query->execute();
    }
}
$order = 0;
$length = 999995;
$activated = 1;
$zone = $islandNum + 100;
$disable = "disable";
$All = "All";
$Nuke = "Nuke";
$allPieces = '{"Transport":1, "Submarine":1, "Destroyer":1, "AircraftCarrier":1, "ArmyCompany":1, "ArtilleryBattery":1, "TankPlatoon":1, "MarinePlatoon":1, "MarineConvoy":1, "AttackHelo":1, "SAM":1, "FighterSquadron":1, "BomberSquadron":1, "StealthBomberSquadron":1, "Tanker":1}';
$query = 'INSERT INTO newsAlerts (newsGameId, newsOrder, newsTeam, newsPieces, newsEffect, newsZone, newsLength, newsActivated) VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iisssiii",$gameId, $order, $All, $allPieces, $disable, $zone, $length, $activated);
$query->execute();
$query = 'UPDATE games SET gameIsland'.$islandNum.' = ? WHERE (gameId = ?)';
$query = $db->prepare($query);
$query->bind_param("si", $Nuke, $gameId);
$query->execute();
$length = 7;
$nukeHuman = "nukeHuman";
$query = 'INSERT INTO newsAlerts (newsGameId, newsOrder, newsTeam, newsEffect, newsLength, newsActivated) VALUES(?, ?, ?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("iissii",$gameId, $order, $myTeam, $nukeHuman, $length, $activated);
$query->execute();
$updateType = "islandOwnerChange";
$query = 'INSERT INTO updates (updateGameId, updateType, updatePlacementId, updateHTML) VALUES (?, ?, ?, ?)';
$query = $db->prepare($query);
$query->bind_param("isis", $gameId, $updateType, $islandNum, $Nuke);
$query->execute();
$query = 'UPDATE games SET game'.$myTeam.'Hpoints = game'.$myTeam.'Hpoints - 12 WHERE gameId = ?';
$query = $db->prepare($query);
$query->bind_param("i",  $gameId);
$query->execute();
$updateType = "getBoard";
$query = 'INSERT INTO updates (updateGameId, updateType) VALUES (?, ?)';
$query = $db->prepare($query);
$query->bind_param("is", $gameId, $updateType);
$query->execute();
echo "Nuked the Island.";
exit;
