# Login through a QR code

## WARNING!!
on iPhone when you scan the code it brings up a "safari" look alike, make sure the user opens the normal safari by pressing the button right bottom. (if that user is logged in by the normal safari)

## Data information
userID = random generated identity (`uniqid()`) <br>
key1(i) =  random number (`md5(microtime().rand())`)<br>
key2(e) = encrypted ipaddress

## Database
|login-codes|users|
|-----|--|
|userID|id|
|key1|email|
|key2|password|
|confirmed
|created_date

## Dependencies
 - [PHP QR Code](http://phpqrcode.sourceforge.net/)
 - [PHP](https://www.php.net/)
 - [Database](https://www.mysql.com/)
 - [Hosting (apache recommended)](https://www.apache.org/)

# Installation
1. Clone/download the github.
2. Upload the tables to your desired database.
3. Check the `config/configuration.php` to configure your personal settings.
4. Configure your database connection in `cconfig/db.php`.
5. Drag in the files to your website.
6. Style it or edit it any way shape or form.

if you incounter any errors please report it here.

## Usage
1. Register/Login through your phone with any browser
2. Goto the register/login page on your pc
3. Scan the qr-code and make sure you open it with the same browser your logged in too
4. Wait a few seconds and poof
