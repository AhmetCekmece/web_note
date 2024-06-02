<?php    //Yalnizca ajax isleminde calisir
require "../backend/control_session.php";
require '../backend/control_db.php';
$db = new control_db\Database();

if($db === null || $username === null || $userid === null || $sorgu_1 === null || $sorgu_2 === null){
    $responseData["error"] = "Baglanti Kurulamadi !"; 
    echo json_encode($responseData);
    exit; 
}

$operation=$_GET["operation"];
$responseData=array();
switch($operation){
    case 'not_olustur':
        $responseData = Not_Olustur();       
        break;

    case 'altnot_olustur':
        $responseData = Altnot_Olustur();       
        break;

    case 'not_kaydet':
        $responseData = Not_Kaydet();
        break;
    
    case 'baslik_kaydet':
        $responseData = Baslik_Kaydet();
        break;

    case 'not_sil':
        $responseData = Not_Sil();
        break;
    
    case 'activenot_sec':
        $responseData = Active_Not_Sec();
        break;

    case 'altnot_gizle':
        $responseData = Altnot_Gizle();
        break;

    case 'not_tasi':
        $responseData = Not_Tasi();
        break;

    case 'notlar_width':
        $responseData = Notlar_Width_Kaydet();
        break;
    
    case 'test':
        $responseData = Test();
        break;
            
    default:break;
}

echo json_encode($responseData);
exit;


function Not_Olustur(){
    global $userid, $db, $sorgu_1, $activenot; 

    if(isset($_POST['not_ismi'])){ 

        $_POST["not_ismi"] = trim($_POST["not_ismi"]);
        if ($_POST["not_ismi"] === "") {
            $_POST["not_ismi"] = "isimsiz";
        }
        else if(strlen($_POST["not_ismi"]) > 20){
            $responseData["error"] = "Not Olusturulamadi!"; 
            return $responseData;
        }
        
        $new_uindex = $sorgu_1->unique_index + 1; 
        if($activenot !== null){   
            $srg = $db->Insert("INSERT INTO notlar (userid, not_uindex, baslik, ustnot_index, yan_ust, yan_alt) 
                                VALUES (?,?,?,?,?,?)", array($userid, $new_uindex, $_POST["not_ismi"], $activenot->ustnot_index, $activenot->not_uindex, $activenot->yan_alt));
            Sorgu2_Ekle($new_uindex, $_POST["not_ismi"], $activenot->ustnot_index, false, $activenot->not_uindex, $activenot->yan_alt);
            Sorgu2_Tek_Guncelle($activenot->yan_alt, "yan_ust", $new_uindex);
            Sorgu2_Tek_Guncelle($activenot->not_uindex, "yan_alt", $new_uindex);
        }
        else {
            $resp = Altnot_Bul_IlkSon(false, 0);
            $sonnot = isset($resp) ? $resp->not_uindex : 0;
    
            $srg = $db->Insert("INSERT INTO notlar (userid, not_uindex, baslik, yan_ust) 
                                VALUES (?,?,?,?)", array($userid, $new_uindex, $_POST["not_ismi"], $sonnot));
            Sorgu2_Ekle($new_uindex, $_POST["not_ismi"], 0, false, $sonnot, 0);
            Sorgu2_Tek_Guncelle($sonnot, "yan_alt", $new_uindex);
        }     
        Sorgu1_Tek_Guncelle("unique_index", $new_uindex);

        $responseActivenot = Active_Not_Sec($new_uindex);
        $responseData["success"] = "Not Olusturuldu"; 
        $responseData["baslik"] = $responseActivenot["baslik"];
        $responseData["icerik"] = $responseActivenot["icerik"];
        $responseData["not_uindex"] = $responseActivenot["not_uindex"];
        $responseData["notlar"] = $responseActivenot["notlar"];
        return $responseData;        
    }
    else {
        $responseData["error"] = "Not Olusturulamadi"; 
        return $responseData;
    }
}

function Altnot_Olustur(){  
    global $userid, $db, $sorgu_1, $activenot;

    if(isset($_POST['altnot_ismi']) && $activenot !== null){
          
        $_POST["altnot_ismi"] = trim($_POST["altnot_ismi"]);
        if ($_POST["altnot_ismi"] === "") {
            $_POST["altnot_ismi"] = "isimsiz";
        }
        else if(strlen($_POST["altnot_ismi"]) > 20){
            $responseData["error"] = "Alt Not Olusturulamadi!"; 
            return $responseData;
        }

        $resp = Altnot_Bul_IlkSon(false, $activenot->not_uindex);
        $alt_sonot = isset($resp) ? $resp->not_uindex : 0;

        $new_uindex = $sorgu_1->unique_index + 1;
        $srg = $db->Insert("INSERT INTO notlar (userid, not_uindex, baslik, ustnot_index, yan_ust) 
                            VALUES (?,?,?,?,?)", array($userid, $new_uindex, $_POST["altnot_ismi"], $activenot->not_uindex, $alt_sonot)); 
        Sorgu2_Ekle($new_uindex, $_POST["altnot_ismi"], $activenot->not_uindex, false, $alt_sonot, 0);
        Sorgu2_Tek_Guncelle($alt_sonot, "yan_alt", $new_uindex);
        Sorgu1_Tek_Guncelle("unique_index", $new_uindex);
        
        $responseActivenot = Active_Not_Sec($new_uindex);
        $responseData["success"] = "Alt Not Olusturuldu"; 
        $responseData["baslik"] = $responseActivenot["baslik"];
        $responseData["icerik"] = $responseActivenot["icerik"];
        $responseData["not_uindex"] = $responseActivenot["not_uindex"];
        $responseData["notlar"] = $responseActivenot["notlar"];
        return $responseData;
    }
    else {
        $responseData["error"] = "Alt Not Olusturulamadi"; 
        return $responseData;
    }
}

function Not_Kaydet(){
    global $userid, $db, $activenot;
    if(isset($_POST['icerik']) && $activenot !== null){
        $srg = $db->Update("UPDATE notlar SET icerik = (?) WHERE userid = (?) AND not_uindex = (?)", array($_POST["icerik"], $userid, $activenot->not_uindex));       
        Activenot_Tek_Guncelle("icerik", $_POST["icerik"]);
    
        $responseData["success"] = "Not Kaydedildi"; 
        return $responseData;
    }
    else {
        $responseData["error"] = "Not Kaydedilemedi!"; 
        return $responseData;
    }
}

function Baslik_Kaydet(){
    global $userid, $db, $activenot;
    if(isset($_POST['baslik']) && $activenot !== null){
        $_POST["baslik"] = trim($_POST["baslik"]);
        if ($_POST["baslik"] === "") {
            $_POST["baslik"] = "isimsiz";
        }
        else if(strlen($_POST["baslik"]) > 20){
            $responseData["error"] = "Baslik Guncellenemedi!"; 
            return $responseData;
        }

        $srg = $db->Update("UPDATE notlar SET baslik = (?) WHERE userid = (?) AND not_uindex = (?)", array($_POST["baslik"], $userid, $activenot->not_uindex));
        Sorgu2_Tek_Guncelle($activenot->not_uindex, "baslik", $_POST["baslik"]);
        Activenot_Tek_Guncelle("baslik", $_POST["baslik"]);
        
        $responseData["success"] = "Baslik Guncellendi"; 
        $responseData["baslik"] = $_POST["baslik"];
        return $responseData;
    }
    else {
        $responseData["error"] = "Baslik Guncellenemedi!"; 
        return $responseData;
    }
}

function Not_Sil(){
    global $userid, $db, $activenot;

    if($activenot !== null){
        $bagli_notlar = Altnotlari_Bul($activenot);
        $placeholders = rtrim(str_repeat("?,", count($bagli_notlar)), ",");

        $srg = $db->Delete("DELETE FROM notlar WHERE not_uindex IN (" . $placeholders . ")", $bagli_notlar);
        Sorgu2_Sil($bagli_notlar);
        if($activenot->yan_ust !== 0){
            $srg2 = $db->Update("UPDATE notlar SET yan_alt = (?) WHERE userid = (?) AND not_uindex = (?)", array($activenot->yan_alt, $userid, $activenot->yan_ust)); 
            Sorgu2_Tek_Guncelle($activenot->yan_ust, "yan_alt", $activenot->yan_alt);
        }
        if($activenot->yan_alt !== 0){
            $srg3 = $db->Update("UPDATE notlar SET yan_ust = (?) WHERE userid = (?) AND not_uindex = (?)", array($activenot->yan_ust, $userid, $activenot->yan_alt)); 
            Sorgu2_Tek_Guncelle($activenot->yan_alt, "yan_ust", $activenot->yan_ust);
        }
        $srg5 = $db->Update("UPDATE config SET active_notuindex = (?) WHERE userid = (?)", array(0,  $userid));      

        Activenot_Guncelle();  // null olarak ayarlar

        $responseData["success"] = "Not silindi";
        $responseData["bagli_notlar"] = $bagli_notlar; 
        $responseData["notlar"] = Not_Listele();
        return $responseData;
    }
    else {
        $responseData["error"] = "Not silinemedi!"; 
        return $responseData;
    }
}

function Active_Not_Sec($_secilinot = null){
    $activenot = null;
    if($_secilinot !== null){
        $activenot = $_secilinot;
    }
    else if(isset($_POST['not_uindex']) && ctype_digit($_POST["not_uindex"]) && Uindex_Varmi(intval($_POST["not_uindex"]))) {
        $activenot = intval($_POST["not_uindex"]);
    }
    else {
        $responseData["error"] = "Not aktif edilemedi!";
        return $responseData;
    }

    global $userid, $db;
    $srg = $db->getRow("SELECT * FROM notlar WHERE userid = (?) AND not_uindex = (?)", array($userid, $activenot));
    $srg2 = $db->getRow("UPDATE config SET active_notuindex = (?) WHERE userid = (?)", array($activenot, $userid));
    Activenot_Guncelle($srg);
    Sorgu1_Tek_Guncelle("active_notuindex", $activenot);

    $responseData["success"] = "Not aktif edildi";
    $responseData["baslik"] = $srg->baslik;
    $responseData["icerik"] = $srg->icerik;
    $responseData["not_uindex"] = $srg->not_uindex;
    $responseData["notlar"] = Not_Listele();
    return $responseData;
}

function Altnot_Gizle(){
    if(isset($_POST['not_uindex']) && isset($_POST['istek_tipi']) && ctype_digit($_POST["not_uindex"]) && Uindex_Varmi(intval($_POST["not_uindex"]))) {
        $istek_tipi = $_POST['istek_tipi'] === "true" ? true : ($_POST['istek_tipi'] === "false" ? false : null);
        if($istek_tipi === null){
            $responseData["error"] = "Altnot Gizle-Goster Basarisiz!";
            return $responseData;
        }
        $not_uindex = intval($_POST["not_uindex"]);

        global $userid, $db, $activenot;
        $srg = $db->Update("UPDATE notlar SET altnotlari_gizle = (?) WHERE userid = (?) AND not_uindex = (?)", array($istek_tipi, $userid, $not_uindex));    
        Sorgu2_Tek_Guncelle($not_uindex, "altnotlari_gizle", $istek_tipi);
        
        $responseActivenot = array();
    
        if($activenot !== null){
            if($activenot->not_uindex === $not_uindex){
                Activenot_Tek_Guncelle("altnotlari_gizle", $istek_tipi);
            }
            else {
                $bagli_notlar = Altnotlari_Bul(null, $not_uindex);
                if (in_array($activenot->not_uindex, $bagli_notlar)) {
                    $responseActivenot = Active_Not_Sec($not_uindex);
                }
            }
        }
        
        $responseData["success"] = "Altnot Gizle-Goster Basarili"; 
        if (!empty($responseActivenot)) {
            $responseData["baslik"] = $responseActivenot["baslik"];
            $responseData["icerik"] = $responseActivenot["icerik"];
            $responseData["not_uindex"] = $responseActivenot["not_uindex"];
            $responseData["notlar"] = $responseActivenot["notlar"];
        }
        else {
            $responseData["notlar"] = Not_Listele();
        }
        return $responseData;
    }
    else {
        $responseData["error"] = "Altnot Gizle-Goster Basarisiz!";
        return $responseData;
    }
}

function Not_Tasi(){
    if(isset($_POST['tasidigim_not']) && isset($_POST['alici_not']) && (isset($_POST['Yanina_Tasi']) || isset($_POST['Altina_Tasi']) || isset($_POST['Ustune_Tasi']))
        && ctype_digit($_POST["tasidigim_not"]) && Uindex_Varmi(intval($_POST["tasidigim_not"])) && ctype_digit($_POST["alici_not"]) && Uindex_Varmi(intval($_POST["alici_not"]))){

        global $userid, $db;
        $tasidigim_uindex = intval($_POST['tasidigim_not']);
        $alici_uindex = intval($_POST['alici_not']);

        $bagli_notlar = Altnotlari_Bul(null, $tasidigim_uindex);
        if (in_array( $alici_uindex, $bagli_notlar)) {
            $responseData["error"] = "Bir not kendi alt notuna tasinamaz..";
            return $responseData;
        }
    
        $tasidigim_not = Not_Bul($tasidigim_uindex);
        $tasidigim_yanalt_not = $tasidigim_not->yan_alt === 0 ? null : Not_Bul($tasidigim_not->yan_alt);
        $tasidigim_yanust_not = $tasidigim_not->yan_ust === 0 ? null : Not_Bul($tasidigim_not->yan_ust);
        $alici_not = Not_Bul( $alici_uindex);
        $alici_yanalt_not = $alici_not->yan_alt === 0 ? null : Not_Bul($alici_not->yan_alt);
        $alici_yanust_not = $alici_not->yan_ust === 0 ? null : Not_Bul($alici_not->yan_ust);
    
        if($tasidigim_not->not_uindex === $alici_not->not_uindex){
            $responseData["error"] = "Tasimaya gerek yok";
            return $responseData;
        }
    
    
        if(isset($_POST['Yanina_Tasi'])) {  //alt yan
            if($tasidigim_not->yan_ust === $alici_not->not_uindex){
                $responseData["error"] = "Tasimaya gerek yok";
                return $responseData;
            }
    
            $srg = $db->getRow("UPDATE notlar SET ustnot_index = (?), yan_ust = (?), yan_alt = (?) WHERE userid = (?) AND not_uindex = (?)", 
                                array($alici_not->ustnot_index, $alici_not->not_uindex, $alici_not->yan_alt, $userid, $tasidigim_not->not_uindex));
            
            if($alici_yanalt_not !== null){
                $srg2 = $db->getRow("UPDATE notlar SET yan_ust = (?) WHERE userid = (?) AND not_uindex = (?)", 
                                    array($tasidigim_not->not_uindex, $userid, $alici_yanalt_not->not_uindex));
            }
    
            $srg3 = $db->getRow("UPDATE notlar SET yan_alt = (?) WHERE userid = (?) AND not_uindex = (?)", 
                                array($tasidigim_not->not_uindex, $userid, $alici_not->not_uindex));
        }
        else if(isset($_POST['Altina_Tasi'])) {    //icine
            if($tasidigim_not->ustnot_index === $alici_not->not_uindex){
                $responseData["error"] = "Tasimaya gerek yok";
                return $responseData;
            }
    
            $resp = Altnot_Bul_IlkSon(false, $alici_not->not_uindex);
            $alt_sonot = isset($resp) ? $resp->not_uindex : 0;
    
            $srg8 = $db->getRow("UPDATE notlar SET ustnot_index = (?), yan_ust = (?), yan_alt = (?) WHERE userid = (?) AND not_uindex = (?)", 
                                array($alici_not->not_uindex, $alt_sonot, 0, $userid, $tasidigim_not->not_uindex));
    
            if($alt_sonot !== 0){
                $srg9 = $db->getRow("UPDATE notlar SET yan_alt = (?) WHERE userid = (?) AND not_uindex = (?)", 
                                    array($tasidigim_not->not_uindex, $userid, $alt_sonot));
            }       
        }
        else if(isset($_POST['Ustune_Tasi'])) {  //ust yan
            if($tasidigim_not->yan_alt === $alici_not->not_uindex){
                $responseData["error"] = "Tasimaya gerek yok";
                return $responseData;
            }
    
            $srg = $db->getRow("UPDATE notlar SET ustnot_index = (?), yan_ust = (?), yan_alt = (?) WHERE userid = (?) AND not_uindex = (?)", 
                                array($alici_not->ustnot_index, $alici_not->yan_ust, $alici_not->not_uindex, $userid, $tasidigim_not->not_uindex));
            
            if($alici_yanust_not !== null){
                $srg2 = $db->getRow("UPDATE notlar SET yan_alt = (?) WHERE userid = (?) AND not_uindex = (?)", 
                                    array($tasidigim_not->not_uindex, $userid, $alici_yanust_not->not_uindex));
            }
    
            $srg3 = $db->getRow("UPDATE notlar SET yan_ust = (?) WHERE userid = (?) AND not_uindex = (?)", 
                                array($tasidigim_not->not_uindex, $userid, $alici_not->not_uindex));
        } 
    
        if($tasidigim_yanalt_not !== null){
            if($tasidigim_yanust_not !== null){
                $srg4 = $db->getRow("UPDATE notlar SET yan_ust = (?) WHERE userid = (?) AND not_uindex = (?)", 
                                    array($tasidigim_yanust_not->not_uindex, $userid, $tasidigim_yanalt_not->not_uindex));
                $srg5 = $db->getRow("UPDATE notlar SET yan_alt = (?) WHERE userid = (?) AND not_uindex = (?)", 
                                    array($tasidigim_yanalt_not->not_uindex, $userid, $tasidigim_yanust_not->not_uindex));
            }
            else{
                $srg6 = $db->getRow("UPDATE notlar SET yan_ust = (?) WHERE userid = (?) AND not_uindex = (?)", 
                                    array(0, $userid, $tasidigim_yanalt_not->not_uindex));
            }
        }
        else if($tasidigim_yanust_not !== null){
            $srg7 = $db->getRow("UPDATE notlar SET yan_alt = (?) WHERE userid = (?) AND not_uindex = (?)", 
                                array(0, $userid, $tasidigim_yanust_not->not_uindex));
        }
    
        $sorgu_2 = $db->getRows("SELECT not_uindex, baslik, ustnot_index, altnotlari_gizle, yan_ust, yan_alt    
                                FROM notlar WHERE userid = (?)", array($userid));
        Sorgu2_Guncelle($sorgu_2);   
    
    
        $responseData["success"] = "Not tasima basarili"; 
        $responseData["notlar"] = Not_Listele();
        return $responseData;
    }
    else {
        $responseData["error"] = "Not tasima basarisiz"; 
        return $responseData;
    }
}

function Notlar_Width_Kaydet(){
    if(isset($_POST['notlarwidth']) && ctype_digit($_POST["notlarwidth"])) {
        global $db, $userid, $sorgu_1;
        $notlarwidth = intval($_POST["notlarwidth"]);
        $notlarwidth = $notlarwidth > 395 ? 395 : ($notlarwidth < 100 ? 100 : $notlarwidth);

        $srg = $db->getRow("UPDATE config SET notlar_width = (?) WHERE userid = (?)", array($notlarwidth, $userid));
        Sorgu1_Tek_Guncelle("notlar_width", $notlarwidth);
    
        $responseData["success"] = "Width kaydedildi"; 
        return $responseData;
    }
    else {
        $responseData["error"] = "Width kaydedilemedi!"; 
        return $responseData;
    }
}

// -------------------------------------

function Test(){
    $test1 = null; $test2 = null;
    global $activenot, $sorgu_1, $sorgu_2;

    $test1 = Altnotlari_Bul(null, 370);
    $test2 = Altnotlari_Bul(null, 370);

    $responseData["success"] = "Test sonucu:";
    $responseData["test1"] = $test1;
    $responseData["test2"] = $test2;
    return $responseData;
}