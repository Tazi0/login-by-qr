<?php

session_start();
require "config/db.php";

$config = require('config/configuration.php');

if(isset($_SESSION['keys']) && isset($_SESSION['verified'])) {
    $key1 = $_SESSION['keys'][0];
    $key2 = $_SESSION['keys'][1];
    $a = mysqli_query($con, "SELECT userID,confirmed,key2,key1 FROM `login-codes` WHERE key1='$key1'");
    $a = mysqli_fetch_assoc($a);
    if($a) {
        $check = intval($a['confirmed']);
        if($check) {
            if($a['key2'] != $key2 || $a['key1'] != $key1) {
                echo "The keys aren't the same, please try again";
            } else {
                list($crypted_token, $enc_iv) = explode("::", $a['key2']);
                $cipher_method = 'aes-128-ctr';
                $enc_key = openssl_digest($config['securityKey'], 'SHA256', TRUE);
                $a['key2'] = openssl_decrypt($crypted_token, $cipher_method, $enc_key, 0, hex2bin($enc_iv));

                if($_SERVER['REMOTE_ADDR'] != $a['key2']) {
                    echo "This is not the same IP address as the created request from the QR code";
                } else {
                    $id = $a['userID'];
                    $e = mysqli_query($con, "DELETE FROM `login-codes` WHERE key1='$key1'");
                    // var_dump($id);
                    unset($_SESSION['keys'], $_SESSION['verified']);
                    $_SESSION['verified'] = true;
                    $_SESSION['userID'] = $id;
                    header("Location: ". $config["page"]["welcome"]);
                    die();
                }
            }
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
    header("Location: ./logout.php");
    die();
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
            $key2 = $_SERVER['REMOTE_ADDR'];

            $cipher_method = 'aes-128-ctr';
            $enc_key = openssl_digest($config['securityKey'], 'SHA256', TRUE);
            $enc_iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($cipher_method));

            $key2 = openssl_encrypt($key2, $cipher_method, $enc_key, 0, $enc_iv) . "::" . bin2hex($enc_iv); // Replace IP to encrypted message
            unset($cipher_method, $enc_key, $enc_iv); // Remove back end of encryption

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
            loop()
        }
        function loop() {
            var inputs = document.querySelectorAll('input');
            setTimeout(() => {
                find = false
                for (let i = 0; i < inputs.length; i++) {
                    const e = inputs[i];
                    if(e.value != "" && e.type != "submit") [
                        find = true
                    ]
                }
                if(!find) location.reload();
                else loop();
            }, 10000);
        }
    </script>
</body>
</html>