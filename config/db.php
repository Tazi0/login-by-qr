<?php

/*      QR code (required)     */
/*          made by            */
/*           Tazio             */

$dblink="localhost";
$dbuser="root";
$dbpass="";
$db="qr-code";

// Create link with DB + utf8
$con = mysqli_connect($dblink, $dbuser, $dbpass, $db) or die('Cannot connect to database. '.mysqli_connect_error());
mysqli_set_charset($con, "utf8");


/* ----------2020------------- */

?>