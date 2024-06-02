<?php    //Yalnizca ajax isleminde calisir
require "../backend/control_session.php";
require '../backend/control_db.php';
$db = new control_db\Database();

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


function Not_Olustur(){
    global $userid, $db, $sorgu_1, $sorgu_2, $activenot;   
    $sorgu_1->unique_index = $sorgu_1->unique_index + 1; 
    if($activenot){      
        $srg = $db->Insert("INSERT INTO notlar (userid, not_uindex, baslik, ustnot_index, yan_ust, yan_alt) 
                            VALUES (?,?,?,?,?,?)", array($userid, $sorgu_1->unique_index, $_POST["not_ismi"], $activenot->ustnot_index, $activenot->not_uindex, $activenot->yan_alt));
        Sorgu2_Ekle($sorgu_1->unique_index, $_POST["not_ismi"], $activenot->ustnot_index, 0, false, $activenot->not_uindex, $activenot->yan_alt);
        Sorgu2_Guncelle($activenot->yan_alt, "yan_ust", $sorgu_1->unique_index);
        Sorgu2_Guncelle($activenot->not_uindex, "yan_alt", $sorgu_1->unique_index);

        // if($activenot->ustnot_index == 0 && $activenot->yan_alt == 0) {
        //     $sorgu_1->son_not = $sorgu_1->unique_index;
        // }
        // else {
        //     $activenot->yan_alt = $sorgu_1->unique_index;
        // }
    }
    else {
        $resp = Altnot_Bul_IlkSon(false, 0);
        $sonnot = isset($resp) ? $resp->not_uindex : 0;

        $srg = $db->Insert("INSERT INTO notlar (userid, not_uindex, baslik, yan_ust) 
                            VALUES (?,?,?,?)", array($userid, $sorgu_1->unique_index, $_POST["not_ismi"], $sonnot));
        Sorgu2_Ekle($sorgu_1->unique_index, $_POST["not_ismi"], 0, 0, false, $sonnot, 0);
        Sorgu2_Guncelle($sonnot, "yan_alt", $sorgu_1->unique_index);
        //$sorgu_1->son_not = $sorgu_1->unique_index;
    }
    
    $responseActivenot = Active_Not_Sec($sorgu_1->unique_index);
    $responseData["success"] = "Not Olusturuldu"; 
    $responseData["notlar"] = Not_Listele();
    $responseData = $responseData + $responseActivenot;
    return $responseData;
}

function Altnot_Olustur(){  
    global $userid, $db, $sorgu_1, $activenot;   
    if($activenot){
        $resp = Altnot_Bul_IlkSon(false, $activenot->not_uindex);
        $alt_sonot = isset($resp) ? $resp->not_uindex : 0;

        $sorgu_1->unique_index = $sorgu_1->unique_index + 1;
        $srg = $db->Insert("INSERT INTO notlar (userid, not_uindex, baslik, ustnot_index, yan_ust) 
                            VALUES (?,?,?,?,?)", array($userid, $sorgu_1->unique_index, $_POST["altnot_ismi"], $activenot->not_uindex, $alt_sonot)); 
        // if($activenot->alt_ilknot == 0){
        //     $activenot->alt_ilknot = $sorgu_1->unique_index;
        //     Sorgu2_Guncelle($activenot->not_uindex, "alt_ilknot", $sorgu_1->unique_index);
        // }
        Sorgu2_Ekle($sorgu_1->unique_index, $_POST["altnot_ismi"], $activenot->not_uindex, 0, false, $alt_sonot, 0);
        Sorgu2_Guncelle($alt_sonot, "yan_alt", $sorgu_1->unique_index);
        //Sorgu2_Guncelle($activenot->not_uindex, "alt_sonnot", $sorgu_1->unique_index);
        //$activenot->alt_sonnot = $sorgu_1->unique_index;
        
        $responseActivenot = Active_Not_Sec($sorgu_1->unique_index);
        $responseData["success"] = "Alt Not Olusturuldu"; 
        $responseData["notlar"] = Not_Listele();
        $responseData = $responseData + $responseActivenot;
        return $responseData;
    } 
    //error mesaji yollarsin
}

function Not_Kaydet(){
    global $userid, $db, $activenot;
    if($activenot){
        $srg = $db->Update("UPDATE notlar SET icerik = (?) WHERE userid = (?) AND not_uindex = (?)", array($_POST["icerik"], $userid, $activenot->not_uindex));       
        $activenot->icerik = $_POST["icerik"];  
    
        $responseData["success"] = "Not Guncellendi"; 
        return $responseData;
    }
    //error mesaji yollarsin
}

function Baslik_Kaydet(){
    global $userid, $db, $activenot;
    if($activenot != null){
        $srg = $db->Update("UPDATE notlar SET baslik = (?) WHERE userid = (?) AND not_uindex = (?)", array($_POST["baslik"], $userid, $activenot->not_uindex));
        Sorgu2_Guncelle($activenot->not_uindex, "baslik", $_POST["baslik"]);
        $activenot->baslik = $_POST["baslik"];
        
        $responseData["success"] = "Baslik Guncellendi"; 
        $responseData["baslik"] = $_POST["baslik"];
        return $responseData;
    }
    //error mesaji yollarsin
}

function Not_Sil(){
    global $userid, $db, $activenot, $sorgu_1, $sorgu_2;
    if($activenot){
        $bagli_notlar = Altnotlari_Bul($activenot);
        $placeholders = rtrim(str_repeat("?,", count($bagli_notlar)), ",");
        $srg = $db->Delete("DELETE FROM notlar WHERE not_uindex IN (" . $placeholders . ")", $bagli_notlar);
        Sorgu2_Sil($bagli_notlar);
        if($activenot->yan_ust != 0){
            $srg2 = $db->Update("UPDATE notlar SET yan_alt = (?) WHERE userid = (?) AND not_uindex = (?)", array($activenot->yan_alt, $userid, $activenot->yan_ust)); 
            Sorgu2_Guncelle($activenot->yan_ust, "yan_alt", $activenot->yan_alt);
        }
        if($activenot->yan_alt != 0){
            $srg3 = $db->Update("UPDATE notlar SET yan_ust = (?) WHERE userid = (?) AND not_uindex = (?)", array($activenot->yan_ust, $userid, $activenot->yan_alt)); 
            Sorgu2_Guncelle($activenot->yan_alt, "yan_ust", $activenot->yan_ust);
        }
        // if($activenot->not_uindex == $sorgu_1->son_not){
        //     $srg4 = $db->Update("UPDATE config SET active_notuindex = (?), son_not = (?) WHERE userid = (?)", array(0, $activenot->yan_ust, $userid)); 
        //     $sorgu_1->son_not = $activenot->yan_ust;
        // }
        // else {
        //     $srg5 = $db->Update("UPDATE config SET active_notuindex = (?) WHERE userid = (?)", array(0,  $userid)); 
        // }

        $srg5 = $db->Update("UPDATE config SET active_notuindex = (?) WHERE userid = (?)", array(0,  $userid));      

        // if($activenot->ustnot_index != 0){
        //     foreach ($sorgu_2 as $row) { 
        //         if($row->not_uindex == $activenot->ustnot_index){
        //             if($row->alt_ilknot == $activenot->not_uindex){
        //                 $srg6 = $db->Update("UPDATE notlar SET alt_ilknot = (?) WHERE userid = (?) AND not_uindex = (?)", array($activenot->yan_alt, $userid, $activenot->ustnot_index));
        //                 $row->alt_ilknot = $activenot->yan_alt;
        //             }
        //             else if ($row->alt_sonnot == $activenot->not_uindex){
        //                 $srg7 = $db->Update("UPDATE notlar SET alt_sonnot = (?) WHERE userid = (?) AND not_uindex = (?)", array($activenot->yan_ust, $userid, $activenot->ustnot_index));
        //                 $row->alt_sonnot = $activenot->yan_ust;
        //             }
        //             break;
        //         }
        //     }
        // }
        Active_Not_Guncelle();  // null olarak ayarlar

        $responseData["success"] = "Not silme basarili";
        $responseData["bagli_notlar"] = $bagli_notlar; 
        $responseData["notlar"] = Not_Listele();
        return $responseData;
    }
}

function Active_Not_Sec($_secilinot = null){
    $activenot = null;
    if($_secilinot){
        $activenot = $_secilinot;
    }
    else {
        $activenot = $_POST["not_uindex"];
    }

    global $userid, $db, $sorgu_1;
    $srg = $db->getRow("SELECT * FROM notlar WHERE userid = (?) AND not_uindex = (?)", array($userid, $activenot));
    $srg2 = $db->getRow("UPDATE config SET active_notuindex = (?) WHERE userid = (?)", array($srg->not_uindex, $userid));
    Active_Not_Guncelle($srg);
    $sorgu_1->active_notuindex = $srg->not_uindex;

    $responseData["success"] = "Not aktif edildi";
    $responseData["baslik"] = $srg->baslik;
    $responseData["icerik"] = $srg->icerik;
    $responseData["not_uindex"] = $srg->not_uindex;
    $responseData["notlar"] = Not_Listele();
    return $responseData;
}

function Altnot_Gizle(){
    global $userid, $db, $activenot;
    $srg = $db->Update("UPDATE notlar SET altnotlari_gizle = (?) WHERE userid = (?) AND not_uindex = (?)", array($_POST["istek_tipi"], $userid, $_POST["not_uindex"]));    
    Sorgu2_Guncelle($_POST["not_uindex"], "altnotlari_gizle", $_POST["istek_tipi"]);
    
    $responseActivenot = array();

    if($activenot){
        if($activenot->not_uindex == $_POST["not_uindex"]){
            $activenot->altnotlari_gizle = $_POST["istek_tipi"];
        }
        else {
            $bagli_notlar = Altnotlari_Bul(null, $_POST["not_uindex"]);
            if (in_array($activenot->not_uindex, $bagli_notlar)) {
                $responseActivenot = Active_Not_Sec($_POST["not_uindex"]);
            }
        }
    }
    
    $responseData["success"] = "Gizle-Goster Basarili"; 
    $responseData["notlar"] = Not_Listele();
    $responseData = $responseData + $responseActivenot;
    return $responseData;
}

function Not_Tasi(){
    global $userid, $db;

    $bagli_notlar = Altnotlari_Bul(null, $_POST['tasidigim_not']);
    if (in_array($_POST['alici_not'], $bagli_notlar)) {
        $responseData["error"] = "Bir not kendi alt notuna tasinamaz..";
        return $responseData;
    }

    $tasidigim_not = Not_Bul($_POST['tasidigim_not']);
    $tasidigim_yanalt_not = $tasidigim_not->yan_alt == 0 ? null : Not_Bul($tasidigim_not->yan_alt);
    $tasidigim_yanust_not = $tasidigim_not->yan_ust == 0 ? null : Not_Bul($tasidigim_not->yan_ust);
    $alici_not = Not_Bul($_POST['alici_not']);
    $alici_yanalt_not = $alici_not->yan_alt == 0 ? null : Not_Bul($alici_not->yan_alt);
    $alici_yanust_not = $alici_not->yan_ust == 0 ? null : Not_Bul($alici_not->yan_ust);

    if($tasidigim_not->not_uindex == $alici_not->not_uindex){
        $responseData["error"] = "Tasimaya gerek yok";
        return $responseData;
    }


    if(isset($_POST['Yanina_Tasi'])) {  //alt yan
        if($tasidigim_not->yan_ust == $alici_not->not_uindex){
            $responseData["error"] = "Tasimaya gerek yok";
            return $responseData;
        }

        $srg = $db->getRow("UPDATE notlar SET ustnot_index = (?), yan_ust = (?), yan_alt = (?) WHERE userid = (?) AND not_uindex = (?)", 
                            array($alici_not->ustnot_index, $alici_not->not_uindex, $alici_not->yan_alt, $userid, $tasidigim_not->not_uindex));
       
        if($alici_yanalt_not){
            $srg2 = $db->getRow("UPDATE notlar SET yan_ust = (?) WHERE userid = (?) AND not_uindex = (?)", 
                                array($tasidigim_not->not_uindex, $userid, $alici_yanalt_not->not_uindex));
        }

        $srg3 = $db->getRow("UPDATE notlar SET yan_alt = (?) WHERE userid = (?) AND not_uindex = (?)", 
                            array($tasidigim_not->not_uindex, $userid, $alici_not->not_uindex));
    }
    else if(isset($_POST['Altina_Tasi'])) {    //icine
        if($tasidigim_not->ustnot_index == $alici_not->not_uindex){
            $responseData["error"] = "Tasimaya gerek yok";
            return $responseData;
        }

        $resp = Altnot_Bul_IlkSon(false, $alici_not->not_uindex);
        $alt_sonot = isset($resp) ? $resp->not_uindex : 0;

        $srg8 = $db->getRow("UPDATE notlar SET ustnot_index = (?), yan_ust = (?), yan_alt = (?) WHERE userid = (?) AND not_uindex = (?)", 
                            array($alici_not->not_uindex, $alt_sonot, 0, $userid, $tasidigim_not->not_uindex));

        if($alt_sonot != 0){
            $srg9 = $db->getRow("UPDATE notlar SET yan_alt = (?) WHERE userid = (?) AND not_uindex = (?)", 
                                array($tasidigim_not->not_uindex, $userid, $alt_sonot));
        }       
    }
    else if(isset($_POST['Ustune_Tasi'])) {  //ust yan
        if($tasidigim_not->yan_alt == $alici_not->not_uindex){
            $responseData["error"] = "Tasimaya gerek yok";
            return $responseData;
        }

        $srg = $db->getRow("UPDATE notlar SET ustnot_index = (?), yan_ust = (?), yan_alt = (?) WHERE userid = (?) AND not_uindex = (?)", 
                            array($alici_not->ustnot_index, $alici_not->yan_ust, $alici_not->not_uindex, $userid, $tasidigim_not->not_uindex));
        
        if($alici_yanust_not){
            $srg2 = $db->getRow("UPDATE notlar SET yan_alt = (?) WHERE userid = (?) AND not_uindex = (?)", 
                                array($tasidigim_not->not_uindex, $userid, $alici_yanust_not->not_uindex));
        }

        $srg3 = $db->getRow("UPDATE notlar SET yan_ust = (?) WHERE userid = (?) AND not_uindex = (?)", 
                            array($tasidigim_not->not_uindex, $userid, $alici_not->not_uindex));
    } 

    if($tasidigim_yanalt_not){
        if($tasidigim_yanust_not){
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
    else if($tasidigim_yanust_not){
        $srg7 = $db->getRow("UPDATE notlar SET yan_alt = (?) WHERE userid = (?) AND not_uindex = (?)", 
                            array(0, $userid, $tasidigim_yanust_not->not_uindex));
    }

    $sorgu_2 = $db->getRows("SELECT not_uindex, baslik, ustnot_index, altnot_adet, altnotlari_gizle, yan_ust, yan_alt    
                            FROM notlar WHERE userid = (?)", array($userid));
    Sorgu2_Olustur($sorgu_2);   


    $responseData["success"] = "Not tasima basarili"; 
    $responseData["notlar"] = Not_Listele();
    return $responseData;
}

function Notlar_Width_Kaydet(){
    global $db, $userid, $sorgu_1;

    $srg = $db->getRow("UPDATE config SET notlar_width = (?) WHERE userid = (?)", array($_POST["notlarwidth"], $userid));
    $sorgu_1->notlar_width = $_POST["notlarwidth"];

    $responseData["success"] = "Width kaydedildi"; 
    return $responseData;
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