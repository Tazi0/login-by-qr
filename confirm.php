<?php

session_start();
require "config/db.php";

if(isset($_GET['i']) && isset($_GET['e'])) {
    if(isset($_SESSION['id'])) {
        // echo "lets go";
        $key1 = $_GET['i'];
        $key2 = $_GET['e'];
        $res = mysqli_query($con, "SELECT * FROM `login-codes` WHERE key1='$key1' AND key2='$key2' AND confirmed=0");
        if(!$res) {
            echo "Nothing found";
        } else {
            $res = mysqli_fetch_assoc($res);
            $id = $_SESSION['id'];
            // var_dump($res);
            $res = mysqli_query($con, "UPDATE `login-codes` SET userID='$id', confirmed=1 WHERE key1='$key1' AND key2='$key2'");
            if(!$res) {
                echo "Updating went wrong!";
            } else {
                echo "All good, you can close the page now!";
            }
        }
    } else {
        echo "You are not logged in so can't verify <br><a href='index.php'>click here to login</a>";
    }
}

?>