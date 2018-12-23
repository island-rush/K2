<?php
session_start();
include("../db.php");

$gameId = $_SESSION['gameId'];

$zero = 0;
$query = "UPDATE GAMES SET gameActive = (gameActive + 1) % 2, gameRedJoined = ?, gameBlueJoined = ?  WHERE gameId = ?";
$preparedQuery = $db->prepare($query);
$preparedQuery->bind_param("iii",  $zero, $zero, $gameId);
$preparedQuery->execute();

$updateType = "logout";
$query = 'INSERT INTO updates (updateGameId, updateType) VALUES (?, ?)';
$query = $db->prepare($query);
$query->bind_param("is", $gameId, $updateType);
$query->execute();




