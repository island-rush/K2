<?php
session_start();
include("backend/db.php");
if (!isset($_SESSION['secretCourseDirectorVariable'])) {
    header("location:index.php?err=8");
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Island Rush Course Director</title>
    <link rel="stylesheet" type="text/css" href="frontend/css/main.css">
    <script>
		console.log("Course Director Javascript");
		// function databaseCreate() {
		// 	if(confirm("ARE YOU SURE YOU WANT TO COMPLETELY RESET THE DATABASE?")){
		// 		if(confirm("This will remove ALL games and data associated with them, there is no reset for this action. Please backup all files and data before executing...")){
		// 			let phpGamePopulate = new XMLHttpRequest();
		// 			phpGamePopulate.open("GET", "backend/admin/databaseCreate.php", true);
		// 			phpGamePopulate.send();
		// 		}
		// 	}
		// }
    </script>
</head>
<body>
<h1>Island Rush Course Director</h1>
<nav>
    <a href="./index.php">Home</a>
	<a href="troubleshoot.html">Troubleshoot</a>
</nav>

<h1>Course Director Tools...</h1>

</body>
</html>
