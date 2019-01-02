<?php
session_start();
include("backend/db.php");

if (!isset($_SESSION['secretAdminSessionVariable']) || !isset($_SESSION['gameId']) || !isset($_SESSION['gameSection']) || !isset($_SESSION['gameInstructor'])) {
    header("location:home.php?err=4");
    exit;
}

$gameId = $_SESSION['gameId'];
$section = $_SESSION['gameSection'];
$instructor = $_SESSION['gameInstructor'];

$query = "SELECT gameActive, gameCurrentTeam, gameTurn FROM GAMES WHERE gameId = ?";
$preparedQuery = $db->prepare($query);
$preparedQuery->bind_param("i", $gameId);
$preparedQuery->execute();
$results = $preparedQuery->get_result();
$r = $results->fetch_assoc();
$gameActive = (int) htmlentities($r['gameActive']);
$gameCurrentTeam = htmlentities($r['gameCurrentTeam']);
$gameTurn = $r['gameTurn'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Island Rush Admin</title>
    <link rel="stylesheet" type="text/css" href="frontend/css/main.css">
    <script>
		console.log("Admin Javascript");

		function populateGame() {
			if(confirm("ARE YOU SURE YOU WANT TO COMPLETELY RESET THIS GAME?")){
				if(confirm("This will delete all information for the game and set it back to the initial start state of the game.\n\n   ARE YOU SURE YOU WANT TO RESET?")){
					let phpGamePopulate = new XMLHttpRequest();
					phpGamePopulate.open("GET", "backend/admin/gamePopulate.php", true);
					phpGamePopulate.send();
					document.getElementById("populateButton").disabled = true;
					document.getElementById("activeToggle").checked = false;
				}
			}
		}

		function toggleActive() {
			let phpGamePopulate = new XMLHttpRequest();
			phpGamePopulate.open("GET", "backend/admin/gameToggleActive.php", true);
			phpGamePopulate.send();
		}
    </script>
</head>

<body>
<h1>Island Rush Admin</h1>

<nav>
    <a href="./home.php">Home</a>
    <a href="rulebook.html">Rule Book</a>
</nav>

<h2>Admin Tools</h2>
<span class="important" id="section">Section: <?php echo htmlentities($section); ?></span>
<span class="important" id="instructor">Instructor: <?php echo htmlentities($instructor); ?></span>

<br>
<hr>
<h3>Activate / Deactivate Game</h3>
<span>Inactive</span>
<label class="switch">
    <input id="activeToggle" type="checkbox" <?php if ($gameActive == 1) {echo "checked";}?> onclick="toggleActive()">
    <span id="slider1" class="slider round"></span>
</label>
<span>Active</span>

<hr>
<h3>News Alerts for this game:</h3>

<?php
$query = "SELECT * FROM newsAlerts WHERE newsGameId = ? AND newsActivated = 0 ORDER BY newsOrder ASC";
$preparedQuery = $db->prepare($query);
$preparedQuery->bind_param("i", $gameId);
$preparedQuery->execute();
$newsAlerts = $preparedQuery->get_result();
$news_rows = $newsAlerts->num_rows;
$firstRow = $newsAlerts->fetch_assoc();
$firstOrder = $firstRow['newsOrder'];
$newsAlerts->data_seek(0);
if ($news_rows == 0) {
    if ($gameTurn == 0) {
        echo "<h4>There Are no News Alerts... (Reset the Game to create them)</h4><br><br><br>";
    } else {
        echo "<h4>Game has run out of News Alerts.</h4><br><br><br>";
    }
} else {
    echo "<div id='newsAlertsContainer'>
    <h4>Current team: <".$gameCurrentTeam.">".$gameCurrentTeam."</".$gameCurrentTeam.">'s turn</h4>
    <form id='swapNewsForm' method='post' action='backend/admin/newsAlertSwap.php'>
        <div>Use this form to swap two news alerts. Refresh the page to show the most up-to-date news alerts for this game.</div>
        <label>Swap #</label>
        <input type='hidden' name='gameId' id='gameId' value='".htmlentities($gameId)."'>
        <input name='swap1order' type='number' id='swap1' required min='".htmlentities($firstOrder)."' max='".($news_rows + $firstOrder - 1)."'>
        <label> with #</label>
        <input name='swap2order' type='number' id='swap2' required min='".htmlentities($firstOrder)."' max='".($news_rows + $firstOrder - 1)."'>
        <input type='submit' value='swap'>
    </form>
    <table id='newsAlertTable' class='".$gameCurrentTeam."Next' ><tr><th>Order</th> <th>Name</th><th>Effect</th></tr>";
    for($i = 0; $i < $news_rows; $i++){
            $news = $newsAlerts->fetch_assoc();
            $order = htmlentities($news['newsOrder']);
            $name = htmlentities($news['newsText']);
            $effect = htmlentities($news['newsEffectText']);
            echo "<tr><td>".$order."</td><td>".$name."</td><td>".$effect."</td></tr>";
        }
    echo "</table></div>";
}
?>

<hr>
<button class="btn btn-danger" id="populateButton" onclick="populateGame();">RESET GAME (Refresh After)</button>
<br>
</body>
</html>
