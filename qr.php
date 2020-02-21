<?php
session_start();
require "phpqrcode.php";

if(isset($_SESSION['keys'])) {
    $key1 = $_SESSION['keys'][0];
    $key2 = $_SESSION['keys'][1];
    $base = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http");
    $actual_link = $base . '://' . $_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']) . "/index.php";
    QRcode::png($actual_link . "?i=$key1&e=$key2");
}

?>