<?php 
    require "session_control.php";

    if (isset($_SESSION["username"])) {
        header("Location: page_account.php");
        exit();
    } 
?>

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
    <form method="post" id="signupForm"> 
        <input type="text" id="cnumara" name="numara" placeholder="numara"><br>
        <input type="text" id="cusername" name="username" placeholder="username"><br>
        <input type="text" id="cpassword" name="password" placeholder="password"><br>
        <button type="button" onclick="SendForm('signupForm','sign_up');">Hesap Oluştur</button>
    </form><hr>

    <h2>Giriş Yap</h2>
    <form method="post" id="loginForm">
        <input type="text" id="lnumara" name="numara" placeholder="numara"><br>
        <input type="text" id="lusername" name="username" placeholder="username"><br>
        <input type="text" id="lpassword" name="password" placeholder="password"><br>
        <button type="button" onclick="SendForm('loginForm','sign_in');">Giriş Yap</button>
    </form><hr>

    <script src="send_request.js"></script>
    <script src="proc_response.js"></script>
</body>
</html>
