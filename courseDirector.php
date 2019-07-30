<?php
session_start();
if (isset($_SESSION['gameId'])) {
	unset($_SESSION['gameId']);
}

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
	<a href="credits.html">Credits</a>
	<a href="https://gitreports.com/issue/island-rush/K2" target="_blank" style="float: right">Report an Issue</a>
	<a href="https://github.com/island-rush/K2/wiki" target="_blank" style="float: right">Wiki</a>
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

<table border="1">
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
<form name="gameAdd" method="post" id="gameDelete" onsubmit="return confirmDelete()" action="backend/admin/gameDelete.php">
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

<form name="ResetDatabase" method="post" id="databaseResetForm" onsubmit="return databaseReset()" action="backend/admin/databaseReset.php">
	<input type="submit" class="btn btn-danger" name="Submit" id="databaseResetButton" value="RESET DATABASE">
</form>

<script>
		console.log("Course Director Javascript");

		function databaseReset() {
			if (confirm("This button should only need to be pressed once, as a convenience of creating the database tables. It is assumed that the database itself already exists. This action will effect current tables if they already exist. BACKUP DATA")) {
				if (confirm("ARE YOU SURE?")) {
					return confirm("THIS WILL DELETE AND RESET EVERYTHING! ARE YOU ABSOLUTELY SURE?");
				}
			}
			return false;
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