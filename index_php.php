<?php
require 'DBcontrol.php';

session_start();
 
if (isset($_SESSION["username"])) {
    header("Location: account.php");
    exit();
}

if (isset($_POST['Giris_Yap']) && !isset($_SESSION["username"])) {
    
    if(Giris_Yap($_POST['username'], $_POST['password'])){
        $_SESSION["username"] = $_POST['username'];
        $_SESSION["session_yeni_acildi"] = true;
        //$_SESSION["active_notuindex"] = 0;
        header("Location: account.php");
        exit;
    }
    else {
        echo "Kullanıcı adı veya parola yanlış.";
    }
}

if (isset($_POST['Hesap_Olustur'])) {
    if(Hesap_Olustur($_POST['username'], $_POST['password'])){
        echo "Hesap olusturuldu";
    }
    else{
        echo "Bu username zaten mevcut";
    }
}
