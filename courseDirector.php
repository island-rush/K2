<?php
session_start();
if(isset($_SESSION['gameId'])) {
	unset($_SESSION['gameId']);
}

include("backend/db.php");
if (!isset($_SESSION['secretCourseDirectorVariable'])) {
    header("location:index.php?err=8");
    exit;
}

include("backend/db.php");

$gamesArray = array();
$query = "SELECT * FROM games";
$preparedQuery = $db->prepare($query);
$preparedQuery->execute();
$results = $preparedQuery->get_result();
$num_results = $results->num_rows;
for ($i = 0; $i < $num_results; $i++) {
	$r = $results->fetch_assoc();
	array_push($gamesArray, [
		'gameId' => $r['gameId'],
		'gameSection' => $r['gameSection'],
		'gameInstructor' => $r['gameInstructor'],
		'gameActive' => $r['gameActive']
	]);
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Island Rush Course Director</title>
    <link rel="stylesheet" type="text/css" href="frontend/css/main.css">
</head>
<body>
<h1>Island Rush Course Director</h1>
<nav>
    <a href="./index.php">Home</a>
	<a href="troubleshoot.html">Troubleshoot</a>
</nav>

<h1>Add a Game</h1>
<form name="gameAdd" method="post" id="gameAdd" action="backend/admin/gameAdd.php">
	<table border="0" cellpadding="3" cellspacing="1">
		<tr>
			<td>Section</td>
			<td>Teacher Last Name</td>
			<td>Password</td>
			<td>Password Confirm</td>
		</tr>
		<tr>
			<td>
				<input name="adminSection" type="text" id="adminSection" placeholder="ex: m1a1" autofocus="autofocus" required>
			</td>
			<td>
				<input name="adminInstructor" type="text" id="adminInstructor" placeholder="ex: Smith" required>
			</td>
			<td>
				<input name="adminPassword" type="password" id="adminPassword" required>
			</td>
			<td>
				<input name="adminPasswordConfirm" type="password" id="adminPasswordConfirm" required>
			</td>
			<td colspan="2">
				<input type="submit" name="Submit" value="Add Game">
			</td>
		</tr>
	</table>
</form>

<h1>Current Games</h1>

<!-- all the games are listed -->
<table>
	<tr>
		<td>Game Id</td>
		<td>Section</td>
		<td>Teacher Last Name</td>
		<td>Game Active</td>
	</tr>

	<?php 
		for($i = 0; $i < sizeof($gamesArray); $i++){
			$thisGame = $gamesArray[$i];
			$gameActive = $thisGame['gameActive'] == 0 ? "False" : "True";
			echo "<tr>
			<td>".$thisGame['gameId']."</td>
			<td>".$thisGame['gameSection']."</td>
			<td>".$thisGame['gameInstructor']."</td>
			<td>".$gameActive."</td>
			</tr>";
		}
	?>

</table>

<h1>Delete a Game</h1>
<form name="gameAdd" method="post" id="gameDelete" onsubmit="confirmDelete()" action="backend/admin/gameDelete.php">
	<table border="0" cellpadding="3" cellspacing="1">
		<tr>
			<td>Game Id</td>
		</tr>
		<tr>
			<td>
				<input name="gameId" type="text" id="gameId" placeholder="Game Id #" required>
			</td>
			<td colspan="2">
				<input type="submit" name="Submit" value="Delete Game">
			</td>
		</tr>
	</table>
</form>

<br>

<button class="btn btn-danger" id="databaseResetButton" onclick="databaseReset();">RESET DATABASE</button>

<script>
		console.log("Course Director Javascript");

		function databaseReset() {
			if(confirm("ARE YOU SURE YOU WANT TO COMPLETELY RESET THE DATABASE?")){
				if(confirm("This will remove ALL games and data associated with them, there is no reset for this action. Please backup all files and data before executing...")){
					let phpDatabaseReset = new XMLHttpRequest();
					phpDatabaseReset.onreadystatechange = function () {
						if (this.readyState === 4 && this.status === 200) {
							if (this.responseText === "Fail") {
								alert("FAILED TO RESET THE DATABASE!...Please make sure database is online and environment variables are set up.");
							}
						}
					};
					phpDatabaseReset.open("GET", "backend/admin/databaseReset.php", true);
					phpDatabaseReset.send();
				}
			}
		}

		function confirmDelete() {
			return confirm("ARE YOU SURE YOU WANT TO DELETE THIS GAME?");
		}

		var password = document.getElementById("adminPassword");
		var confirm_password = document.getElementById("adminPasswordConfirm");
		function validatePassword(){
			if(password.value != confirm_password.value) {
				confirm_password.setCustomValidity("Passwords Don't Match");
			} else {
				confirm_password.setCustomValidity('');
			}
		}
		password.onchange = validatePassword;
		confirm_password.onkeyup = validatePassword;	
    </script>
</body>
</html>