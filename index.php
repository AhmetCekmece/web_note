<?php
session_start();
$loginError = false;

if (isset($_SESSION["username"])) {
    header("Location: account.php");
    exit();
}

if (isset($_POST['Giris_Yap']) && !isset($_SESSION["username"])) {
    $dosya = 'notlar.json';
    $notlar = file_exists($dosya) ? json_decode(file_get_contents($dosya), true) : array();

    $username = $_POST['username'];
    $password = $_POST['password'];
    $active_notuindex = 0;
    $active_baslik = '';
    $active_icerik = '';

    foreach ($notlar as $hesap) {
        if ($hesap['username'] === $username && $hesap['password'] === $password) {
            if (!empty($hesap['notlar'])){
                foreach ($hesap['notlar'] as &$not) {
                    if ($not['not_uindex'] === intval($hesap['active_notuindex'])) {
                        $active_notuindex = $hesap['active_notuindex'];
                        $active_baslik = $not['baslik'];
                        $active_icerik = $not['icerik'];
                        break;
                    }
                }
            }
            $_SESSION["username"] = $username;
            $_SESSION["active_notuindex"] = intval($active_notuindex);
            $_SESSION["active_baslik"] = $active_baslik;
            $_SESSION["active_icerik"] = $active_icerik;
            header("Location: account.php");
            exit;
        }
    }  
    echo "Kullanıcı adı veya parola yanlış. Lütfen tekrar deneyin.";
}

if (isset($_POST['Hesap_Olustur'])) {
    $dosya = 'notlar.json';
    $notlar = file_exists($dosya) ? json_decode(file_get_contents($dosya), true) : array();

    $hesap_varmi = false;
    $username = $_POST['username'];
    foreach ($notlar as $hesap) {
        if ($hesap['username'] === $username) {
            $hesap_varmi = true;
        }
    }
    if($hesap_varmi === false){
        $yeni_hesap = array(
            'username' => $_POST['username'],
            'password' => $_POST['password'], 
            'unique_index' => 0,
            'active_notuindex' => 0
        );
        $notlar[] = $yeni_hesap;
        file_put_contents($dosya, json_encode(array_values($notlar)));
        echo "hesap olusturuldu";
    }
    else{
        echo "bu hesap zaten mevcut";
    }
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
