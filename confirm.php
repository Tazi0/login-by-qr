<?php


/*      QR code (required)     */
/*          made by            */
/*           Tazio             */

session_start();
require "config/db.php";
$config = require('config/configuration.php');

// if there is a ?i=.... in the URL
if(isset($_GET['i'])) {
    // If the user is logged in
    if(isset($_SESSION['id'])) {
        // easy key backing + Fetch all information
        $key1 = $_GET['i'];
        $res = mysqli_query($con, "SELECT * FROM `login-codes` WHERE key1='$key1' AND confirmed=0");

        // if nothing was found
        if(!$res) {
            echo "Nothing found";
        } else {
            // get the first + get current id + update it so it's confirmed (and sends the id of this current user that is logged in)
            $res = mysqli_fetch_assoc($res);
            $id = $_SESSION['id'];
            $res = mysqli_query($con, "UPDATE `login-codes` SET userID='$id',confirmed=1 WHERE key1='$key1'");

            // If updating went wrong
            if(!$res) {
                echo "Updating went wrong!";
            } else {
                echo "All good, you can close the page now!";
            }
        }
    } else {
        // Get key1 from URL
        $i = $_GET['i'];

        // redirect to user to login page with key1
        if($config['notLoggedInGotoLogin']) {
            header("Location: " . $config['pages']['login'] . "?i=$i");
            die();
        } else {
            echo "You are not logged in so can't verify <br><a href='index.php?i=$i'>click here to login</a>";
        }
    }
}


/* ----------2020------------- */

?>