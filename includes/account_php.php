<?php
require 'DBcontrol.php';

session_start();
$username = $_SESSION["username"];   

if (isset($_POST["Logout"])) {
    unset($_SESSION["username"]);
    unset($_SESSION["active_notuindex"]);
    unset($_SESSION["active_baslik"]);
    unset($_SESSION["active_icerik"]);
    unset($_SESSION["active_ustnotindex"]);
    unset($_SESSION["active_altnotadet"]);  
    unset($_SESSION["active_altnotlari_gizle"]);
    session_destroy();
}

if (!isset($_SESSION["username"])) {
    header("Location: index.php");
    exit();
} 

// if (isset($_POST['Test'])) {
//     Not_Islem(TEST);
// }

if(isset($_SESSION["session_yeni_acildi"])){
    unset($_SESSION["session_yeni_acildi"]);

    $hesap = Hesap_Bul($_SESSION["username"]);
    Not_Goster($hesap['active_notuindex']);
}

function Not_Goster($_aranan_index) {
    $result = Not_Goster_DB($_SESSION["username"], $_aranan_index);

    $_SESSION["active_notuindex"] = $result['not_uindex'];
    $_SESSION["active_baslik"] = $result['baslik'];
    $_SESSION["active_icerik"] = $result['icerik'];
    $_SESSION["active_ustnotindex"] = $result['ustnot_index'];
    $_SESSION["active_altnotadet"] = $result['altnot_adet'];
    $_SESSION["active_altnotlari_gizle"] = $result['altnotlari_gizle'];
}

if(isset($_POST['Not_Goster'])) {    
    Not_Goster(intval($_POST['Not_Goster']));
}

if (isset($_POST['Not_Olustur'])) {
    $not_uindex = 0;
    if($_SESSION["active_notuindex"] !== 0){
        $not_uindex = Not_Olustur_1($_SESSION["username"], $_SESSION["active_notuindex"], $_POST['baslik']);
    }
    else{
        $not_uindex = Not_Olustur_2($_SESSION["username"], $_POST['baslik']);
    }
    Not_Goster($not_uindex);
}

if (isset($_POST['AltNot_Olustur']) && $_SESSION["active_notuindex"] !== 0) {
    $not_uindex = Altnot_Olustur($_SESSION["username"], $_SESSION["active_notuindex"], $_POST['anbaslik']);
    Not_Goster($not_uindex);
}

if (isset($_POST['Not_Guncelle']) && $_SESSION["active_notuindex"] !== 0) {
    $result = Not_Guncelle($_SESSION["username"], $_SESSION["active_notuindex"], $_POST['baslik'], $_POST['icerik']);
    $_SESSION["active_baslik"] = $result['baslik'];       
    $_SESSION["active_icerik"] = $result['icerik'];
}

if (isset($_POST['Not_Sil']) && $_SESSION["active_notuindex"] !== 0) {
    Not_Sil($_SESSION["username"], $_SESSION["active_notuindex"]);
    Not_Goster(0);
}

if(isset($_POST['AltNotlari_Gizle'])) {    
    Altnotlari_Gizle($_SESSION["username"], $_POST['AltNotlari_Gizle']);
}   

if(isset($_POST['Yanina_Tasi'])) {    
    if(!Yanina_Tasi($_SESSION["username"], intval($_POST['tasidigim_not']), intval($_POST['alici_not']))){
        echo "Bir not kendi alt notuna tasinamaz..";
    }
} 

if(isset($_POST['Altina_Tasi'])) {    //&& ctype_digit($_POST['tasidigim_not']) && ctype_digit($_POST['alici_not'])
    if(!Altina_Tasi($_SESSION["username"], intval($_POST['tasidigim_not']), intval($_POST['alici_not']))){
        echo "Bir not kendi alt notuna tasinamaz..";
    }
} 

//___________________________________________________________________________________________________

function Not_Listele() {
    $hesap = Hesap_Bul($_SESSION["username"]);

    if(count($hesap['notlar']) > 0){
        echo "<form method='post'>";
        Not_Listele_X($hesap['notlar']);
        echo "</form>";
    }
    else{
        echo "Henüz hiç not yok.";              
    }
}
function Not_Listele_X($notlar){
    echo "<ul>";
    foreach ($notlar as $not) {
        echo "<li>";

        echo "<button type='submit' name='AltNotlari_Gizle' value='{$not['not_uindex']}'";     // ► ◄ ▲ ▼
        if ($not['altnot_adet'] === 0) {
            echo " class='button_disabled_v2'";
        }
        
        if($not['altnotlari_gizle'] === false){
            echo ">▼</button>";
            echo "<button id='notButon{$not['not_uindex']}' class='notButon' type='submit' name='Not_Goster' value='{$not['not_uindex']}' draggable='true' ondragstart='dragStart(event)' ondragend='dragEnd(event)' ondrop='drop(event)' ondragover='dragOver(event)'>{$not['baslik']}</button>";
            echo "<br><button id='notGizliButon{$not['not_uindex']}' class='notGizliButon' type='submit' value='{$not['not_uindex']}' ondragover='dragOverGizli(event)' ondragleave='dragLeaveGizli(event)' ondrop='drop(event)'></button>";
            // Eğer bu notun alt notları varsa, alt notları da listele
            if ($not['altnot_adet'] !== 0) {     
                Not_Listele_X($not['notlar']);
            }
        }
        else{
            echo ">►</button>";
            echo "<button id='notButon{$not['not_uindex']}' class='notButon' type='submit' name='Not_Goster' value='{$not['not_uindex']}' draggable='true' ondragstart='dragStart(event)' ondragend='dragEnd(event)' ondrop='drop(event)' ondragover='dragOver(event)'>{$not['baslik']}</button>";
            echo "<br><button id='notGizliButon{$not['not_uindex']}' class='notGizliButon' type='submit' value='{$not['not_uindex']}' ondragover='dragOverGizli(event)' ondragleave='dragLeaveGizli(event)' ondrop='drop(event)'></button>";
        }
        
        echo "</li>";
    }
    echo "</ul>";
}

//___________________________________________________________________________________________________

$yeninot_popup = 'display_none';
if (isset($_POST['Yeni_Not'])) {
    $yeninot_popup = '';
}
if (isset($_POST['Not_Olustur_Iptal'])) {
    $yeninot_popup = 'display_none';
}

$altyeninot_popup = 'display_none';
if (isset($_POST['AltYeni_Not'])) {
    $altyeninot_popup = '';
}
if (isset($_POST['AltNot_Olustur_Iptal'])) {
    $altyeninot_popup = 'display_none';
}

$not_duzenle_enable = '';
if($_SESSION["active_notuindex"] === 0){
    $not_duzenle_enable = 'button_disabled';
}
else {
    $not_duzenle_enable = '';
}