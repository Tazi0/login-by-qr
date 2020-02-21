<?php

$dblink="localhost";
$dbuser="root";
$dbpass="";
$db="qr-code";

$con = mysqli_connect($dblink, $dbuser, $dbpass, $db) or die('Cannot connect to database. '.mysqli_connect_error());
mysqli_set_charset($con, "utf8");

?>