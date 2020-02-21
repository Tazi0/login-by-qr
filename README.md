**not done yet**

# Login through a QR code

## Data information
userID = random generated identity (`uniqid()`)
i = random generated key code (`md5(microtime().rand())`)
key = same as i (but not i)

## Database
empty

## Todo
 - [ ] Find a qrcode api
 - [ ] generate a random link with users ID (confirm.php?id=userID&i=key&e=key2)
 - [ ] paste generated keys + userID + confirmed = 0 in db
 - [ ] show link as qr code
 - [ ] check each minute if it's confirmed yet
 - [ ] phone confirmes if it's opened (ofcourse checks if the user is logged in there)
