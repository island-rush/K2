<?php

//manually put for local hosting
putenv("DB_HOSTNAME=localhost");
putenv("DB_USERNAME=root");
putenv("DB_PASSWORD=");

// putenv("CD_LASTNAME=Smith");
// putenv("CD_PASSWORD=5f4dcc3b5aa765d61d8327deb882cf99");

//Environment Variables for Database Connection
$hostname = getenv('DB_HOSTNAME');
$user = getenv('DB_USERNAME');
$password = getenv('DB_PASSWORD');

$database = 'islandRushDB';  //This should always be the database name (but could put in environment variable)
@ $db = new mysqli($hostname, $user, $password, $database);
if (mysqli_connect_errno()) {
    echo 'ERROR: Could not connect to database. Error is '.mysqli_connect_error();
    exit;
}