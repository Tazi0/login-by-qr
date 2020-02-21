<?php


session_start();
require "config/db.php";
require "phpqrcode.php";

if(isset($_GET['i']) && isset($_GET['e'])) {
    if(isset($_SESSION['id'])) {
        echo "lets go";
    } else {
        echo "You are not logged in so can't verify";
    }
} else {
    $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]index.php";
    $key1 = md5(microtime().rand());
    $key2 = md5(microtime().rand());
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
    } else {
        echo "The user has been made";
    }
}

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
        <input type="submit" name="sumit" value="Register">
    </form>
    <br>
    <img src="qrcode.png" alt="QR CODE">
</body>
</html>