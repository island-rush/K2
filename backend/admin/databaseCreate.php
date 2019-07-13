<?php
//REMOVE AFTER TESTING
putenv("DB_HOSTNAME=localhost");
putenv("DB_USERNAME=root");
putenv("DB_PASSWORD=");

$mysql_host = getenv('DB_HOSTNAME');
$mysql_database = "islandRushDB";  //Should always have this name
$mysql_user = getenv('DB_USERNAME');
$mysql_password = getenv("DB_PASSWORD=");
$db = new PDO("mysql:host=$mysql_host;dbname=$mysql_database", $mysql_user, $mysql_password);
$query = file_get_contents("../sql/db_create.sql");
$stmt = $db->prepare($query);
if ($stmt->execute())
     echo "Success";
else 
     echo "Fail";
