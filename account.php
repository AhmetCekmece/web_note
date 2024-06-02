<?php
    session_start();
    $username = $_SESSION["username"];

    if (isset($_POST["Logout"])) {
        unset($_SESSION["username"]);
        unset($_SESSION["active_notuindex"]);
        unset($_SESSION["active_baslik"]);
        unset($_SESSION["active_icerik"]);
        session_destroy();
    }

    if (!isset($_SESSION["username"])) {
        header("Location: index.php");
        exit();
    } 

    $yeninot_popup = 'display_none';
    if (isset($_POST['Yeni_Not'])) {
        $yeninot_popup = '';
    }

    if (isset($_POST['Not_Olustur_Iptal'])) {
        $yeninot_popup = 'display_none';
    }

    if (isset($_POST['Not_Olustur'])) {
        $dosya = 'notlar.json';
        $notlar = file_exists($dosya) ? json_decode(file_get_contents($dosya), true) : array();
    
        foreach ($notlar as &$hesap) {
            if ($hesap['username'] === $username) {
                $hesap['unique_index'] = $hesap['unique_index'] + 1;
                $yeni_not = array(
                    'not_uindex' => $hesap['unique_index'],
                    'baslik' => $_POST['baslik'],
                    'icerik' => ""
                );
                $hesap['notlar'][] = $yeni_not;
                file_put_contents($dosya, json_encode($notlar));
                Active_Not($hesap['unique_index']);
                break;
            }
        }     
    }

    if (isset($_POST['Not_Guncelle'])) {
        $dosya = 'notlar.json';
        $notlar = file_exists($dosya) ? json_decode(file_get_contents($dosya), true) : array();
    
        foreach ($notlar as &$hesap) {
            if ($hesap['username'] === $username) {
                foreach ($hesap['notlar'] as &$not) {
                    if ($not['not_uindex'] === intval($_POST['notuindex'])) {
                        $not['baslik'] = $_POST['baslik'];
                        $not['icerik'] = $_POST['icerik'];
                        $_SESSION["active_baslik"] = $_POST['baslik'];
                        $_SESSION["active_icerik"] = $_POST['icerik'];
                        file_put_contents($dosya, json_encode($notlar));
                        break 2; // iç içe iki döngüden de çık
                    }
                }
            }
        }    
    }

    if (isset($_POST['Not_Sil'])) {
        $dosya = 'notlar.json';
        $notlar = file_exists($dosya) ? json_decode(file_get_contents($dosya), true) : array();
    
        foreach ($notlar as &$hesap) {
            if ($hesap['username'] === $username) {
                foreach ($hesap['notlar'] as $sirasi => &$not) {
                    if ($not['not_uindex'] === intval($_POST['notuindex'])) {
                        unset($hesap['notlar'][$sirasi]);
                        //$hesap['notlar'] = array_values($hesap['notlar']); // Indisleri yeniden sırala  -performans
                        file_put_contents($dosya, json_encode($notlar));
                        Active_Not(0);
                        break 2; 
                    }
                }
            }
        }
    }

    if(isset($_POST['Not_Goster'])) {    
        $duzenle_notuindex = $_POST['Not_Goster'];
        Active_Not($duzenle_notuindex);
    }
  
    $not_duzenle_enable = '';
    if($_SESSION["active_notuindex"] === 0){
        $not_duzenle_enable = 'button_disabled';
    }
    else {
        $not_duzenle_enable = '';
    }

    function Active_Not($_uindex) {
        $dosya = 'notlar.json';
        $notlar = file_exists($dosya) ? json_decode(file_get_contents($dosya), true) : array();

        if (intval($_uindex) === 0){
            foreach ($notlar as &$hesap) {
                if ($hesap['username'] === $_SESSION["username"]) {
                    $hesap['active_notuindex'] = 0;
                    $_SESSION["active_notuindex"] = 0;
                    $_SESSION["active_baslik"] = '';
                    $_SESSION["active_icerik"] = '';
                    file_put_contents($dosya, json_encode($notlar));
                    break;
                }
            }
        }
        else {
            foreach ($notlar as &$hesap) {
                if ($hesap['username'] === $_SESSION["username"]) {
                    foreach ($hesap['notlar'] as &$not) {
                        if ($not['not_uindex'] === intval($_uindex)) {
                            $hesap['active_notuindex'] = intval($_uindex);
                            $_SESSION["active_notuindex"] = intval($_uindex);
                            $_SESSION["active_baslik"] = $not['baslik'];
                            $_SESSION["active_icerik"] = $not['icerik'];
                            file_put_contents($dosya, json_encode($notlar));
                            break 2; 
                        }
                    }
                }
            }
        }
    }
       
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Not Defteri</title>
    <link rel="stylesheet" href="./account_styles.css">
</head>
<body>
    <form method="post">
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
        <input type="submit" id="AltNot_Olustur" name="AltNot_Olustur" value="Alt Not Olustur">
    </form><hr>

    <h2>Notlarim</h2>
    <?php
        $dosya = 'notlar.json';
        $notlar = file_exists($dosya) ? json_decode(file_get_contents($dosya), true) : array();

        foreach ($notlar as &$hesap) {
            if ($hesap['username'] === $username) {
                if (!empty($hesap['notlar'])) {
                    echo "<form method='post'>";
                    echo "<ul>";
                    foreach ($hesap['notlar'] as &$not) {
                        echo "<li><button type='submit' name='Not_Goster' value='{$not['not_uindex']}'>{$not['baslik']}</button></li>";
                    }
                    echo "</ul>";
                    echo "</form>";
                }
                else{
                    echo "Henüz hiç not yok.";
                }               
                break;
            }
        } 
    ?><hr>

    <h2>Not Düzenle</h2>
    <form method="post">
        <input type="hidden" id="duzenle_notuindex" name="notuindex" value="<?php echo isset($_SESSION["active_notuindex"]) ? $_SESSION["active_notuindex"] : 0; ?>">
        <input type="text" id="duzenle_baslik" name="baslik" placeholder="baslik" value="<?php echo isset($_SESSION["active_baslik"]) ? $_SESSION["active_baslik"] : ''; ?>"><br>
        <textarea id="duzenle_icerik" name="icerik" placeholder="icerik"><?php echo isset($_SESSION["active_icerik"]) ? $_SESSION["active_icerik"] : ''; ?></textarea><br>
        <input type="submit" name="Not_Guncelle" value="Guncelle" class="<?php echo $not_duzenle_enable; ?>" >
        <input type="submit" name="Not_Sil" value="Sil" class="<?php echo $not_duzenle_enable; ?>" >
    </form><hr>

</body>
</html>

