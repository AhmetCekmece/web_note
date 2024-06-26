<?php    //Yalnizca ajax isleminde cagirilir
require "../backend/control_session.php";
require '../backend/control_db.php';

$db = null;
try {
    $db = new control_db\Database();
} catch (Exception $e) {
    $responseData["error"] = "Baglanti Kurulamadi!"; 
    echo json_encode($responseData);
    exit; 
}
if(empty($_GET["operation"]) || empty($db) || empty($username) || empty($userid) || empty($sorgu_1) || $sorgu_2 === null){
    $responseData["error"] = "HATA!"; 
    echo json_encode($responseData);
    exit; 
}

$operation=$_GET["operation"];
$responseData=array();
switch ($operation) {
    case 'not_olustur':
    case 'altnot_olustur':
    case 'not_kaydet':
    case 'baslik_kaydet':
    case 'not_sil':
    case 'altnot_gizle':
    case 'not_tasi':
    case 'notlar_width':
        if ($sorgu_1->role !== "guest") {
            switch ($operation) {
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
                case 'altnot_gizle':
                    $responseData = Altnot_Gizle();
                    break;
                case 'not_tasi':
                    $responseData = Not_Tasi();
                    break;
                case 'notlar_width':
                    $responseData = Notlar_Width_Kaydet();
                    break;
            }
        } else {
            $responseData["error"] = "Bu islem icin yetkiniz yok!";
        }
        break;

    case 'activenot_sec':
        $responseData = Active_Not_Sec();
        break;

    default:
        $responseData["error"] = "Islem Tanimli Degil!";
        break;
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
        
        $sorgu_kontrol = 0;
        $new_uindex = $sorgu_1->unique_index + 1; 
        $sonnot = 0;

        try {
            if($activenot !== null){   
                $sorgu_kontrol = 1;
    
                $srg = $db->Insert("INSERT INTO notlar (userid, not_uindex, baslik, ustnot_index, yan_ust, yan_alt) 
                                    VALUES (?,?,?,?,?,?)", array($userid, $new_uindex, $_POST["not_ismi"], $activenot->ustnot_index, $activenot->not_uindex, $activenot->yan_alt));
                                    if (empty($srg)) throw new Exception();
            }
            else {
                $sorgu_kontrol = 2;
    
                $resp = Altnot_Bul_IlkSon(false, 0);
                $sonnot = isset($resp) ? $resp->not_uindex : 0;
        
                $srg2 = $db->Insert("INSERT INTO notlar (userid, not_uindex, baslik, yan_ust) 
                                    VALUES (?,?,?,?)", array($userid, $new_uindex, $_POST["not_ismi"], $sonnot));
                                    if (empty($srg2)) throw new Exception();
            } 
        } catch (Exception $e) {
            $responseData["error"] = "Not Olusturulamadi! (DB)"; 
            return $responseData;
        }     

        if($sorgu_kontrol === 1){
            Sorgu2_Ekle($new_uindex, $_POST["not_ismi"], $activenot->ustnot_index, false, $activenot->not_uindex, $activenot->yan_alt);
            Sorgu2_Tek_Guncelle($activenot->yan_alt, "yan_ust", $new_uindex);
            Sorgu2_Tek_Guncelle($activenot->not_uindex, "yan_alt", $new_uindex);
        }
        else if($sorgu_kontrol === 2){
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
        $responseData["error"] = "Not Olusturulamadi!"; 
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

        $new_uindex = $sorgu_1->unique_index + 1;
        $resp = Altnot_Bul_IlkSon(false, $activenot->not_uindex);
        $alt_sonot = isset($resp) ? $resp->not_uindex : 0;

        try {
            $srg = $db->Insert("INSERT INTO notlar (userid, not_uindex, baslik, ustnot_index, yan_ust) 
                                VALUES (?,?,?,?,?)", array($userid, $new_uindex, $_POST["altnot_ismi"], $activenot->not_uindex, $alt_sonot)); 
                                if (empty($srg)) throw new Exception();
        } catch (Exception $e) {
            $responseData["error"] = "Alt Not Olusturulamadi! (DB)"; 
            return $responseData;
        }

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
        $responseData["error"] = "Alt Not Olusturulamadi!"; 
        return $responseData;
    }
}

function Not_Kaydet(){
    global $userid, $db, $activenot;
    if(isset($_POST['icerik']) && $activenot !== null){
        try {
            $srg = $db->Update("UPDATE notlar SET icerik = (?) WHERE userid = (?) AND not_uindex = (?)", array($_POST["icerik"], $userid, $activenot->not_uindex)); 
            if ($srg === 0) throw new Exception();

        } catch (Exception $e) {
            $responseData["error"] = "Not Kaydedilemedi! (DB)"; 
            return $responseData;
        }

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

        //XSS KORUMASI
        $_POST["baslik"] = htmlspecialchars($_POST["baslik"], ENT_QUOTES, 'UTF-8');

        if ($_POST["baslik"] === "") {
            $_POST["baslik"] = "isimsiz";
        }
        else if(strlen($_POST["baslik"]) > 500){
            $responseData["error"] = "Baslik Guncellenemedi!"; 
            return $responseData;
        }

        try {
            $srg = $db->Update("UPDATE notlar SET baslik = (?) WHERE userid = (?) AND not_uindex = (?)", array($_POST["baslik"], $userid, $activenot->not_uindex));
            if ($srg === 0) throw new Exception();  

        } catch (Exception $e) {
            $responseData["error"] = "Baslik Guncellenemedi! (DB)"; 
            return $responseData;
        }
        
        Sorgu2_Tek_Guncelle($activenot->not_uindex, "baslik", $_POST["baslik"]);
        Activenot_Tek_Guncelle("baslik", $_POST["baslik"]);
        
        $responseData["success"] = "Baslik Guncellendi"; 
        $responseData["baslik"] = htmlspecialchars_decode($_POST["baslik"]);
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

        $sorgu_kontrol_1 = false;
        $sorgu_kontrol_2 = false;
        $db->MyTransaction();
        try {
            $srg = $db->Delete("DELETE FROM notlar WHERE not_uindex IN (" . $placeholders . ")", $bagli_notlar);           
            if ($srg === 0) throw new Exception();  
            
            if($activenot->yan_ust !== 0){
                $sorgu_kontrol_1 = true;

                $srg2 = $db->Update("UPDATE notlar SET yan_alt = (?) WHERE userid = (?) AND not_uindex = (?)", array($activenot->yan_alt, $userid, $activenot->yan_ust)); 
                if ($srg2 === 0) throw new Exception(); 
            }

            if($activenot->yan_alt !== 0){
                $sorgu_kontrol_2 = true;

                $srg3 = $db->Update("UPDATE notlar SET yan_ust = (?) WHERE userid = (?) AND not_uindex = (?)", array($activenot->yan_ust, $userid, $activenot->yan_alt)); 
                if ($srg3 === 0) throw new Exception();
            }

            $srg4 = $db->Update("UPDATE config SET active_notuindex = (?) WHERE userid = (?)", array(0,  $userid));
            if ($srg4 === 0) throw new Exception();

            $db->MyCommit(); // Islemler Basarili oldu, transaction'i onayla

        } catch (Exception $e) {
            $db->MyRollBack();  // Bir hata durumunda, islemi geri al

            $responseData["error"] = "Not silinemedi! (DB)"; 
            return $responseData;
        }

        Sorgu2_Sil($bagli_notlar);
        if($sorgu_kontrol_1 === true) {
            Sorgu2_Tek_Guncelle($activenot->yan_ust, "yan_alt", $activenot->yan_alt);
        }
        if($sorgu_kontrol_2 === true) {
            Sorgu2_Tek_Guncelle($activenot->yan_alt, "yan_ust", $activenot->yan_ust);
        }     
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

    global $userid, $db, $sorgu_1;

    $srg = null;
    $db->MyTransaction();
    try {
        $srg = $db->getRow("SELECT * FROM notlar WHERE userid = (?) AND not_uindex = (?)", array($userid, $activenot));
        if (empty($srg)) throw new Exception();

        if($sorgu_1->role !== "guest"){
            $srg2 = $db->Update("UPDATE config SET active_notuindex = (?) WHERE userid = (?)", array($activenot, $userid));
            if ($srg2 === 0) throw new Exception();
        }

        $db->MyCommit(); 

    } catch (Exception $e) {
        $db->MyRollBack();  

        $responseData["error"] = "Not aktif edilemedi! (DB)"; 
        return $responseData;
    }
      
    Activenot_Guncelle($srg);
    Sorgu1_Tek_Guncelle("active_notuindex", $activenot);

    $responseData["success"] = "Not aktif edildi";
    $responseData["baslik"] = htmlspecialchars_decode($srg->baslik);
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

        try {
            $srg = $db->Update("UPDATE notlar SET altnotlari_gizle = (?) WHERE userid = (?) AND not_uindex = (?)", array($istek_tipi, $userid, $not_uindex));
            if ($srg === 0) throw new Exception();

        } catch (Exception $e) {
            $responseData["error"] = "Altnot Gizle-Goster Basarisiz! (DB)"; 
            return $responseData;
        }
          
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

        $sorgu_2 = null;
        $db->MyTransaction();
        try {                   
            if(isset($_POST['Yanina_Tasi'])) {  //alt yan
                if($tasidigim_not->yan_ust === $alici_not->not_uindex){
                    $responseData["error"] = "Tasimaya gerek yok";
                    return $responseData;
                }
        
                $srg = $db->Update("UPDATE notlar SET ustnot_index = (?), yan_ust = (?), yan_alt = (?) WHERE userid = (?) AND not_uindex = (?)", 
                                    array($alici_not->ustnot_index, $alici_not->not_uindex, $alici_not->yan_alt, $userid, $tasidigim_not->not_uindex));
                                    if ($srg === 0) throw new Exception();
                
                if($alici_yanalt_not !== null){
                    $srg2 = $db->Update("UPDATE notlar SET yan_ust = (?) WHERE userid = (?) AND not_uindex = (?)", 
                                        array($tasidigim_not->not_uindex, $userid, $alici_yanalt_not->not_uindex));
                                        if ($srg2 === 0) throw new Exception();
                }
        
                $srg3 = $db->Update("UPDATE notlar SET yan_alt = (?) WHERE userid = (?) AND not_uindex = (?)", 
                                    array($tasidigim_not->not_uindex, $userid, $alici_not->not_uindex));
                                    if ($srg3 === 0) throw new Exception();
            }
            else if(isset($_POST['Altina_Tasi'])) {    //icine
                if($tasidigim_not->ustnot_index === $alici_not->not_uindex){
                    $responseData["error"] = "Tasimaya gerek yok";
                    return $responseData;
                }
        
                $resp = Altnot_Bul_IlkSon(false, $alici_not->not_uindex);
                $alt_sonot = isset($resp) ? $resp->not_uindex : 0;
        
                $srg4 = $db->Update("UPDATE notlar SET ustnot_index = (?), yan_ust = (?), yan_alt = (?) WHERE userid = (?) AND not_uindex = (?)", 
                                    array($alici_not->not_uindex, $alt_sonot, 0, $userid, $tasidigim_not->not_uindex));
                                    if ($srg4 === 0) throw new Exception();
        
                if($alt_sonot !== 0){
                    $srg5 = $db->Update("UPDATE notlar SET yan_alt = (?) WHERE userid = (?) AND not_uindex = (?)", 
                                        array($tasidigim_not->not_uindex, $userid, $alt_sonot));
                                        if ($srg5 === 0) throw new Exception();
                }       
            }
            else if(isset($_POST['Ustune_Tasi'])) {  //ust yan
                if($tasidigim_not->yan_alt === $alici_not->not_uindex){
                    $responseData["error"] = "Tasimaya gerek yok";
                    return $responseData;
                }
        
                $srg6 = $db->Update("UPDATE notlar SET ustnot_index = (?), yan_ust = (?), yan_alt = (?) WHERE userid = (?) AND not_uindex = (?)", 
                                    array($alici_not->ustnot_index, $alici_not->yan_ust, $alici_not->not_uindex, $userid, $tasidigim_not->not_uindex));
                                    if ($srg6 === 0) throw new Exception();
                
                if($alici_yanust_not !== null){
                    $srg7 = $db->Update("UPDATE notlar SET yan_alt = (?) WHERE userid = (?) AND not_uindex = (?)", 
                                        array($tasidigim_not->not_uindex, $userid, $alici_yanust_not->not_uindex));
                                        if ($srg7 === 0) throw new Exception();
                }
        
                $srg8 = $db->Update("UPDATE notlar SET yan_ust = (?) WHERE userid = (?) AND not_uindex = (?)", 
                                    array($tasidigim_not->not_uindex, $userid, $alici_not->not_uindex));
                                    if ($srg8 === 0) throw new Exception();
            } 
        
            if($tasidigim_yanalt_not !== null){
                if($tasidigim_yanust_not !== null){
                    $srg9 = $db->Update("UPDATE notlar SET yan_ust = (?) WHERE userid = (?) AND not_uindex = (?)", 
                                        array($tasidigim_yanust_not->not_uindex, $userid, $tasidigim_yanalt_not->not_uindex));
                                        if ($srg9 === 0) throw new Exception();

                    $srg10 = $db->Update("UPDATE notlar SET yan_alt = (?) WHERE userid = (?) AND not_uindex = (?)", 
                                        array($tasidigim_yanalt_not->not_uindex, $userid, $tasidigim_yanust_not->not_uindex));
                                        if ($srg10 === 0) throw new Exception();
                }
                else{
                    $srg11 = $db->Update("UPDATE notlar SET yan_ust = (?) WHERE userid = (?) AND not_uindex = (?)", 
                                        array(0, $userid, $tasidigim_yanalt_not->not_uindex));
                                        if ($srg11 === 0) throw new Exception();
                }
            }
            else if($tasidigim_yanust_not !== null){
                $srg12 = $db->Update("UPDATE notlar SET yan_alt = (?) WHERE userid = (?) AND not_uindex = (?)", 
                                    array(0, $userid, $tasidigim_yanust_not->not_uindex));
                                    if ($srg12 === 0) throw new Exception();
            }
        
            $sorgu_2 = $db->getRows("SELECT not_uindex, baslik, ustnot_index, altnotlari_gizle, yan_ust, yan_alt    
                                    FROM notlar WHERE userid = (?)", array($userid));
                                    if (empty($sorgu_2)) throw new Exception();

            $db->MyCommit();

        } catch (Exception $e) {
            $db->MyRollBack();

            $responseData["error"] = "Not tasima basarisiz! (DB)";
            return $responseData;
        }
        
        Sorgu2_Guncelle($sorgu_2);   
    
        $responseData["success"] = "Not tasima basarili"; 
        $responseData["notlar"] = Not_Listele();
        return $responseData;
    }
    else {
        $responseData["error"] = "Not tasima basarisiz!"; 
        return $responseData;
    }
}

function Notlar_Width_Kaydet(){
    if(isset($_POST['notlarwidth']) && ctype_digit($_POST["notlarwidth"])) {
        global $db, $userid, $sorgu_1;
        $notlarwidth = intval($_POST["notlarwidth"]);
        $notlarwidth = $notlarwidth > 395 ? 395 : ($notlarwidth < 100 ? 100 : $notlarwidth);

        try {
            $srg = $db->Update("UPDATE config SET notlar_width = (?) WHERE userid = (?)", array($notlarwidth, $userid));
            if ($srg === 0) throw new Exception();

        } catch (Exception $e) {
            $responseData["error"] = "Width kaydedilemedi! (DB)"; 
            return $responseData;
        }
        
        Sorgu1_Tek_Guncelle("notlar_width", $notlarwidth);
    
        $responseData["success"] = "Width kaydedildi"; 
        return $responseData;
    }
    else {
        $responseData["error"] = "Width kaydedilemedi!"; 
        return $responseData;
    }
}
