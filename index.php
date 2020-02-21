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
    $_SESSION['verified'] = false;
    QRcode::png($actual_link . "?i=$key1&e=$key2", 'qrcode.png', 'L', 4);
    $res = mysqli_query($con, "INSERT INTO `login-codes` (key1, key2)
                                VALUES ('$key1','$key2')");
    if(!$res) {
        echo "There was an error";
    }
}

if(isset($_SESSION['keys']) && isset($_SESSION['verified'])) {
    $key1 = $_SESSION['keys'][0];
    $key2 = $_SESSION['keys'][1];
    $a = mysqli_query($con, "SELECT userID,confirmed FROM `login-codes` WHERE key1='$key1' AND key2='$key2' LIMIT 1");
    if($a) {
        $a = mysqli_fetch_assoc($a);
        $check = intval($a['confirmed']);
        if($check) {
            $id = $a['userID'];
            // var_dump($id);
            unset($_SESSION['keys']);
            $_SESSION['verified'] = true;
            $_SESSION['userID'] = $id;
            header("Location: welcome.php");
        }
    }
} 

if(isset($_POST['submit'])) {
    $email = $_POST['email'];
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $id = uniqid();
    $res = mysqli_query($con, "INSERT INTO `users` (id, email, `password`, created_date)
                                VALUES ('$id','$email','$pass', now())");
    if($res) {
        $_SESSION['id'] = $id;
        echo "Register success";
    } else {
        echo "The user has not made";
    }
}

if(isset($_POST['login'])) {
    $email = $_POST['email'];
    $res = mysqli_fetch_assoc(mysqli_query($con, "SELECT id,`password` FROM users WHERE email='$email'"));
    if($res && password_verify($_POST['password'],$res['password'])) {
        $_SESSION['id'] = $res['id'];
        echo "Login success";
    } else {
        echo "Password & Email incorrect";
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
        <input type="submit" name="submit" value="Register">
        <input type="submit" name="login" value="Log in">
    </form>
    <br>
    <img src="qrcode.png" alt="QR CODE">

        <?php
            if(isset($_SESSION['keys'])) {
                echo "<script>r = true</script>";
            } else {
                echo "<script>r = false</script>";
            }
        ?>
        
    <script>
        if(r) {
            setTimeout(() => {
                location.reload();
            }, 10000);
        }
    </script>
</body>
</html>