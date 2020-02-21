<?php


session_start();
require "config/db.php";
require "phpqrcode.php";

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
            $res = mysqli_query($con, "UPDATE `login-codes` SET userID='$id', confirmed=1 WHERE key1='$key1' AND key2='$key2' AND confirmed=0");
            if(!$res) {
                echo "Updating went wrong!";
            } else {
                echo "All good, you can close the page now!";
            }
        }
    } else {
        echo "You are not logged in so can't verify";
    }
} else if(!isset($_POST['submit']) && !isset($_SESSION['keys'])) {
    $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]index.php";
    $key1 = md5(microtime().rand());
    $key2 = md5(microtime().rand());
    $_SESSION['keys'] = array($key1, $key2);
    QRcode::png($actual_link . "?i=$key1&e=$key2", 'qrcode.png', 'L', 4);
    $res = mysqli_query($con, "INSERT INTO `login-codes` (key1, key2)
                                VALUES ('$key1','$key2')");
    if(!$res) {
        echo "There was an error";
    }
}

if(isset($_POST['submit'])) {
    $email = $_POST['email'];
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $id = uniqid();
    $res = mysqli_query($con, "INSERT INTO `users` (id, email, `password`)
                                VALUES ('$id','$email','$pass')");
    if($res) {
        $_SESSION['id'] = $id;
        echo "Register success";
    } else {
        echo "The user has not made";
    }
}

$conf = false;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR code</title>
</head>
<body>
    <form action="" method="post">
        <input type="email" name="email" placeholder="Email">
        <input type="password" name="password" placeholder="Password">
        <input type="submit" name="submit" value="Register">
    </form>
    <br>
    <img src="qrcode.png" alt="QR CODE">
</body>
</html>