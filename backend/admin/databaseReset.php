<?php
session_start();
//Verify Course Director Logged On
if (!isset($_SESSION['secretCourseDirectorVariable'])) {
     header("location:../../index.php?err=8");
     exit;
}
$mysql_host = getenv('DB_HOSTNAME');
$mysql_database = getenv('DB_NAME');
$mysql_user = getenv('DB_USERNAME');
$mysql_password = getenv("DB_PASSWORD");
$db = new PDO("mysql:host=$mysql_host;dbname=$mysql_database", $mysql_user, $mysql_password);
$query = file_get_contents("../sql/db_reset.sql");
$stmt = $db->prepare($query);
if ($stmt->execute())
     echo "Success";
else 
     echo "Fail";
