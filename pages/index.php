<?php require '../includes/index_php.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Not Defteri</title>
</head>
<body>
    <hr>
    <h2>Hesap Oluştur</h2>
    <form method="post"> 
        <input type="hidden" name="Hesap_Olustur">     
        <input type="text" id="cusername" name="username" placeholder="username"><br>
        <input type="text" id="cpassword" name="password" placeholder="password"><br>
        <input type="submit" value="Hesap Oluştur">
    </form><hr>

    <h2>Giriş Yap</h2>
    <form method="post">
        <input type="hidden" name="Giris_Yap">
        <input type="text" id="lusername" name="username" placeholder="username"><br>
        <input type="text" id="lpassword" name="password" placeholder="password"><br>
        <input type="submit" value="Giriş Yap">
    </form><hr>
</body>
</html>
