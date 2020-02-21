<?php

session_start();
require "config/db.php";

if(isset($_SESSION['keys']) && isset($_SESSION['verified'])) {
    $key1 = $_SESSION['keys'][0];
    $key2 = $_SESSION['keys'][1];
    $a = mysqli_query($con, "SELECT userID,confirmed FROM `login-codes` WHERE key1='$key1' AND key2='$key2'");
    $a = mysqli_fetch_assoc($a);
    if($a) {
        $check = intval($a['confirmed']);
        if($check) {
            $id = $a['userID'];
            $e = mysqli_query($con, "DELETE FROM `login-codes` WHERE key1='$key1' AND key2='$key2'");
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
    $res = mysqli_query($con, "INSERT INTO `users` (id, email, `password`)
                                VALUES('$id','$email','$pass')");
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

if(isset($_POST['logout'])) {
    session_unset();
    echo "Logged out";
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
        <input type="submit" name="logout" value="Log out">
    </form>
    <br>
    <?php
        if(!isset($_SESSION['keys']) && !isset($_SESSION['id'])) {
            $key1 = md5(microtime().rand());
            $key2 = md5(microtime().rand());
            $_SESSION['keys'] = array($key1, $key2);
            $_SESSION['verified'] = false;
            $res = mysqli_query($con, "INSERT INTO `login-codes` (key1, key2, created_date)
                                        VALUES ('$key1','$key2', now())");
        }
        if(!isset($_SESSION['id'])) {
            echo "<img src='qr.php' alt='qr code' />";
        }
        var_dump($_SESSION);
    ?>

    <?php
        if(isset($_SESSION['keys']) && !isset($_SESSION['id'])) {
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