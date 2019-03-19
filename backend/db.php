<?php
@ $db = new mysqli('islandrushserver.database.windows.net', 'islandrushadmin', '@#DFCSadmin2019', 'islandrushdb');
if (mysqli_connect_errno()) {
    echo 'ERROR2: Could not connect to database.  Error is '.mysqli_connect_error();
    exit;
}

// echo "testing";
// exit;


// PHP Data Objects(PDO) Sample Code:
// try {
//     $conn = new PDO("sqlsrv:server = tcp:islandrushdbserver.database.windows.net,1433; Database = islandrushdb", "islandrushadmin", "{your_password_here}");
//     $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
// }
// catch (PDOException $e) {
//     print("Error connecting to SQL Server.");
//     die(print_r($e));
// }

// SQL Server Extension Sample Code:
//$connectionInfo = array("UID" => "islandrushadmin@islandrushdbserver", "pwd" => "{your_password_here}", "Database" => "islandrushdb", "LoginTimeout" => 30, "Encrypt" => 1, "TrustServerCertificate" => 0);
//$serverName = "tcp:islandrushdbserver.database.windows.net,1433";
//$db = sqlsrv_connect($serverName, $connectionInfo);