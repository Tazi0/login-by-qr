<?php


/*      QR code (required)     */
/*          made by            */
/*           Tazio             */

session_start();
require "config/db.php";

$config = require('config/configuration.php');

// Loop to check if it's verified yet
if(isset($_SESSION['keys']) && isset($_SESSION['verified'])) {
    $key1 = $_SESSION['keys'][0];
    $key2 = $_SESSION['keys'][1];

    // Fetch all info of the items
    $a = mysqli_query($con, "SELECT * FROM `login-codes` WHERE key1='$key1'");
    $a = mysqli_fetch_assoc($a);
    if($a) {
        $check = intval($a['confirmed']);
        if($check) {
            // Check if the keys are still the same
            if($a['key2'] != $key2 || $a['key1'] != $key1) {
                echo "The keys aren't the same, please try again";
            } else {
                // Decrypting the key2
                list($crypted_token, $enc_iv) = explode("::", $a['key2']);
                $cipher_method = 'aes-128-ctr';
                $enc_key = openssl_digest($config['securityKey'], 'SHA256', TRUE);
                $a['key2'] = openssl_decrypt($crypted_token, $cipher_method, $enc_key, 0, hex2bin($enc_iv));

                // Checking if the key2 is the same as the current ip from the user
                if($_SERVER['REMOTE_ADDR'] != $a['key2']) {
                    echo "This is not the same IP address as the created request from the QR code";
                } else {
                    // Last step deletes the row and unset the keys
                    $id = $a['userID'];
                    $e = mysqli_query($con, "DELETE FROM `login-codes` WHERE key1='$key1'");
                    unset($_SESSION['keys'], $_SESSION['verified']);

                    // Relocate the current page (this happends if everything works)
                    $_SESSION['id'] = $id;
                    header("Location: ". $config["page"]["welcome"]);
                    die();
                }
            }
        }
    }
} else if(isset($_GET['i']) && isset($_SESSION['id'])) {
    // If the url has a ?i=.... and is logged in it will redirect it to the confirm page
    $i = $_GET['i'];
    header("Location: confirm.php?i=$i");
    die();
}

/* ----------2020------------- */

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
        /*      QR code (required)     */
        // Check if there isn't a key yet or isn't logged in
        if(!isset($_SESSION['keys']) && !isset($_SESSION['id'])) {
            // Random number + current user ip
            $key1 = md5(microtime().rand());
            $key2 = $_SERVER['REMOTE_ADDR'];

            // Encrypting
            $cipher_method = 'aes-128-ctr';
            $enc_key = openssl_digest($config['securityKey'], 'SHA256', TRUE);
            $enc_iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($cipher_method));

            // Replace IP to encrypted message + Remove back end of encryption
            $key2 = openssl_encrypt($key2, $cipher_method, $enc_key, 0, $enc_iv) . "::" . bin2hex($enc_iv);
            unset($cipher_method, $enc_key, $enc_iv); 

            // Set keys + verification
            $_SESSION['keys'] = array($key1, $key2);
            $_SESSION['verified'] = false;

            // Insert into mysql
            $res = mysqli_query($con, "INSERT INTO `login-codes` (key1, key2, created_date)
                                        VALUES ('$key1','$key2', now())");
        }

        // Shows QR image
        if(!isset($_SESSION['id'])) {
            echo "<img src='qr.php' alt='qr code' />";
        }

        // Debugging
        if($config['debug']) {
            var_dump($_SESSION);
        }

        // Check if the loop needs to activate
        if(isset($_SESSION['keys']) && !isset($_SESSION['id'])) {
            echo "<script>r = true</script>";
        } else {
            echo "<script>r = false</script>";
        }

    ?>
        
    <script>
        // Javascript reload the page if the inputs are empty (every 0.5 seconds)
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