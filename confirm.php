<?php

session_start();
require "config/db.php";

$config = require('config/configuration.php');

if(isset($_GET['i'])) {
    if(isset($_SESSION['id'])) {
        $key1 = $_GET['i'];
        $res = mysqli_query($con, "SELECT * FROM `login-codes` WHERE key1='$key1' AND confirmed=0");
        if(!$res) {
            echo "Nothing found";
        } else {
            $res = mysqli_fetch_assoc($res);
            $id = $_SESSION['id'];
            $res = mysqli_query($con, "UPDATE `login-codes` SET userID='$id',confirmed=1 WHERE key1='$key1'");
            if(!$res) {
                echo "Updating went wrong!";
            } else {
                if($config['closeTabAfterConfirm']) {
                    echo "<script>window.close();</script>";
                } else {
                    echo "All good, you can close the page now!";
                }
            }
        }
    } else {
        if($config['notLoggedInGotoLogin']) {
            header("Location: " . $config['pages']['login']);
            die();
        } else {
            echo "You are not logged in so can't verify <br><a href='index.php'>click here to login</a>";
        }
    }
}

?>