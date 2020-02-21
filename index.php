<?php

require "config/db.php";
include "phpqrcode.php";

QRcode::png('https://', 'qrcode.png', 'Q', 4);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR code</title>
</head>
<body>
    <img src="qrcode.png" alt="QR CODE">
</body>
</html>