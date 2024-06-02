<?php require '../includes/account_php.php' ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Not Defteri</title>
    <link rel="stylesheet" href="../assets/css/styles-account.css">
</head>
<body>
    <form method="post">
        <input type="submit" name="Test" value="Test"><br><br>
        <input type="submit" name="Logout" value="cikis">
        <?php echo $username;?>    
    </form><hr>

    <h2>Menu</h2>
    <form method="post">
        <input type="submit" id="Yeni_Not" name="Yeni_Not" value="Yeni Not" > 
        <input type="text" id="baslik" name="baslik" placeholder="baslik" class="<?php echo $yeninot_popup; ?>" >
        <input type="submit" id="Not_Olustur" name="Not_Olustur" value="Olustur" class="<?php echo $yeninot_popup; ?>" >
        <input type="submit" id="Not_Olustur_Iptal" name="Not_Olustur_Iptal" value="Iptal et" class="<?php echo $yeninot_popup; ?>" >
        <br>
        <input type="submit" id="AltYeni_Not" name="AltYeni_Not" value="Alt Not Olustur" class="<?php echo $not_duzenle_enable; ?>" >
        <input type="text" id="anbaslik" name="anbaslik" placeholder="baslik" class="<?php echo $altyeninot_popup; ?>" >
        <input type="submit" id="AltNot_Olustur" name="AltNot_Olustur" value="Olustur" class="<?php echo $altyeninot_popup; ?>" >
        <input type="submit" id="AltNot_Olustur_Iptal" name="AltNot_Olustur_Iptal" value="Iptal et" class="<?php echo $altyeninot_popup; ?>" >
    </form><hr>

    <span id="notlarim">
    <h2>Notlarim</h2>
    <?php Not_Listele(); ?><hr>
    </span>

    <span id="not_duzenle">
    <h2>Not Düzenle</h2>
    <form method="post">
        <input type="hidden" id="duzenle_notuindex" name="notuindex" value="<?php echo isset($_SESSION["active_notuindex"]) ? $_SESSION["active_notuindex"] : 0; ?>">
        <input type="text" id="duzenle_baslik" name="baslik" placeholder="baslik" value="<?php echo isset($_SESSION["active_baslik"]) ? $_SESSION["active_baslik"] : ''; ?>"><br>
        <textarea id="duzenle_icerik" name="icerik" placeholder="icerik"><?php echo isset($_SESSION["active_icerik"]) ? $_SESSION["active_icerik"] : ''; ?></textarea><br>
        <input type="submit" name="Not_Guncelle" value="Guncelle" class="<?php echo $not_duzenle_enable; ?>" >
        <input type="submit" name="Not_Sil" value="Sil" class="<?php echo $not_duzenle_enable; ?>" >
    </form><hr>
    </span>

    <script src="../assets/js/js-account.js"></script>
</body>
</html>

