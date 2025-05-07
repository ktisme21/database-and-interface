<?php

//Database connection parameters
$dbhost = "localhost"; // or your host name/IP address
$dbuser = "root"; // your database username
$dbpass = ""; // your database password
$dbname = "COMP1044_database"; // your database name

$con = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);

if(!$con) {
    die("Failed to connect: " . mysqli_connect_error());
}

?>