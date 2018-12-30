<?php
session_start();
include("../db.php");

if (!isset($_SESSION['secretAdminSessionVariable'])) {
    header("location:home.php?err=4");
    exit;
}

$gameId = $_SESSION['gameId'];

$query = "UPDATE GAMES SET gameActive = (gameActive + 1) % 2, gameRedJoined = 0, gameBlueJoined = 0  WHERE gameId = ?";
$preparedQuery = $db->prepare($query);
$preparedQuery->bind_param("i",  $gameId);
$preparedQuery->execute();

$updateType = "logout";
$query = 'INSERT INTO updates (updateGameId, updateType) VALUES (?, ?)';
$query = $db->prepare($query);
$query->bind_param("is", $gameId, $updateType);
$query->execute();

