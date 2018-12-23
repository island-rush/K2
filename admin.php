<?php
session_start();
include("backend/db.php");

if (!isset($_SESSION['secretAdminSessionVariable'])) {
    header("location:index.php?err=4");
    exit;
}

$gameId = $_SESSION['gameId'];
$section = $_SESSION['gameSection'];
$instructor = $_SESSION['gameInstructor'];

$query = "SELECT gameActive FROM GAMES WHERE gameId = ?";
$preparedQuery = $db->prepare($query);
$preparedQuery->bind_param("i", $gameId);
$preparedQuery->execute();
$results = $preparedQuery->get_result();
$r = $results->fetch_assoc();
$gameActive = (int) $r['gameActive'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Island Rush Admin</title>
    <link rel="stylesheet" type="text/css" href="frontend/css/admin.css">
    <link rel="stylesheet" type="text/css" href="frontend/css/home.css">
</head>

<body>
<h1>Island Rush Admin</h1>

<nav>
    <a href="./home.php">Home</a>
    <a href="./rulebook.php">Rule Book</a>
</nav>


<h2>Admin Tools</h2>
<span class="important" id="section">Section: <?php echo $section; ?></span>
<span class="important" id="instructor">Instructor: <?php echo $instructor; ?></span>


<br>
<hr>
<h3>Activate / Deactivate Game</h3>
<span>Inactive</span>
<label  class="switch">
    <input id="activeToggle" type="checkbox" <?php if ($gameActive == 1) {echo "checked";}?> onclick="toggleActive()">
    <span id="slider1" class="slider round"></span>
</label>
<span>Active</span>


<br>
<hr>
<h3>Reset Game</h3>
<button class="btn btn-danger" id="populateButton" onclick="populateGame();">RESET GAME</button>
<br>


<script src="frontend/js/admin.js"></script>
</body>

</html>
