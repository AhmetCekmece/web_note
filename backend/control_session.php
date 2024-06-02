<?php
session_start();
$username = isset($_SESSION["username"]) ? $_SESSION["username"] : null;
$userid = isset($_SESSION["userid"]) ? $_SESSION["userid"] : null;
$sorgu_1 = isset($_SESSION["sorgu_1"]) ? $_SESSION["sorgu_1"] : null;       // userid - username - password - unique_index - active_notuindex - son_not
$sorgu_2 = isset($_SESSION["sorgu_2"]) ? $_SESSION["sorgu_2"] : null;       // (array) not_uindex - baslik - ustnot_index - altnot_adet - altnotlari_gizle
$activenot = isset($_SESSION["activenot"]) ? $_SESSION["activenot"] : null; // (active) not_uindex - baslik - icerik - ustnot_index - altnot_adet - altnotlari_gizle


function Start_session ($_username, $_userid, $_sorgu_1, $_sorgu_2, $_activenot)
{   
    $_SESSION["userid"] = $_userid;
    $_SESSION["username"] = $_username;
    $_SESSION["sorgu_1"] = $_sorgu_1;
    $_SESSION["sorgu_2"] = $_sorgu_2;
    $_SESSION["activenot"] = $_activenot;
    
    //$_SESSION["session_yeni_acildi"] = true;
}

function Stop_session (){
    session_unset();
}

function Active_Not_Guncelle($_activenot = null){
    global $activenot, $sorgu_1;
    $activenot = $_activenot;
    $_SESSION["activenot"] = $_activenot;
    if($_activenot){
        $sorgu_1->active_notuindex = $_activenot->not_uindex;
    }
    else {
        $sorgu_1->active_notuindex = 0;
    }    
}

function Sorgu2_Guncelle($_not_uindex, $_hedefsutun, $_data){  //Calisiyor. elleme
    global $sorgu_2;
    foreach ($sorgu_2 as $row) { 
        if($row->not_uindex == $_not_uindex){
            $row->$_hedefsutun = $_data;
            break;
        }
    }
    $_SESSION["sorgu_2"] = $sorgu_2;
}

function  Sorgu2_Ekle($_not_uindex, $_baslik = 'isimsiz', $_ustnot_index = 0, $_altnot_adet = 0, $_altnotlari_gizle = false, $_yan_ust = 0, $_yan_alt = 0, $_alt_ilknot = 0, $_alt_sonnot = 0){
    global $sorgu_2;
    $yeni_satir = new stdClass();
    $yeni_satir->not_uindex = $_not_uindex;
    $yeni_satir->baslik = $_baslik;
    $yeni_satir->ustnot_index = $_ustnot_index;
    $yeni_satir->altnot_adet = $_altnot_adet;
    $yeni_satir->altnotlari_gizle = $_altnotlari_gizle;
    $yeni_satir->yan_ust = $_yan_ust;
    $yeni_satir->yan_alt = $_yan_alt;
    $yeni_satir->alt_ilknot = $_alt_ilknot;
    $yeni_satir->alt_sonnot = $_alt_sonnot;

    $sorgu_2[] = $yeni_satir;
    $_SESSION["sorgu_2"] = $sorgu_2;
}

function Sorgu2_Sil($not_uindexler) {
    global $sorgu_2;

    foreach ($not_uindexler as $not_uindex) {
        foreach ($sorgu_2 as $key => $row) {
            if ($row->not_uindex == $not_uindex) {
                unset($sorgu_2[$key]);
                break;
            }
        }
    }

    $_SESSION["sorgu_2"] = $sorgu_2;
}


// _________________________________________________________________________

function Not_Listele(){
    global $sorgu_2;

    $html = "";
    if(!empty($sorgu_2)){
        $html .= "<div id='test'>"; 
        $html .= "<ul id='bas_ul'>"; // Liste başlangıcı
        $n_uindex = 0;
        foreach ($sorgu_2 as $row) {  //ilknot
            if($row->yan_ust == 0 && $row->ustnot_index == 0){
                $n_uindex = $row->not_uindex;
                break;
            }
        }
        while (true) {
            foreach ($sorgu_2 as $row) {  
                if($row->not_uindex == $n_uindex){                                      
                    $html .= "<li>";                           
                    $html .= "<button class='notgizle_btns'>▼</button>";
                    $html .= "<button class='notbaslik_btns' not_uindex='" . $row->not_uindex . "' onclick='Activenot_Sec_Post(" . $row->not_uindex . ");'>" . $row->baslik . "</button>";                    
                    $html .= "</li>";                    

                    if ($row->alt_ilknot != 0) {
                        $n_uindex = $row->alt_ilknot; // alt not 
                        $html .= "<ul>";
                        break;
                    }
                    else if ($row->yan_alt != 0){     // yan not
                        $n_uindex = $row->yan_alt;
                        break;
                    } 
                    else if ($row->ustnot_index != 0) {
                        while (true) {
                            $html .= "</ul>"; 
                            $n_uindex = $row->ustnot_index;
                            foreach ($sorgu_2 as $row) {
                                if ($row->not_uindex == $n_uindex) {
                                    if ($row->yan_alt != 0) {
                                        $n_uindex = $row->yan_alt;
                                        break 3;
                                    } else if ($row->ustnot_index != 0) {
                                        break;
                                    } else {
                                        break 4;
                                    }
                                }
                            }
                        }
                    }
                    else {
                        break 2;
                    }
                }
            }
        }
        $html .= "</ul>"; // Liste sonu
        $html .= "</div>";
    }
    else{
        $html .= "<div style='height:100%; display:flex; align-items:center; justify-content: center;'><div style='opacity:0.6;'>Henüz not yok.</div></div>"; 
    }
    return $html;
}


function Altnotlari_Bul($_not){ //kendisi dahil  (not listeledeki ul etiketini filtreleyerek bul ilerde)
    global $sorgu_2;
    $secilen_not = $_not->not_uindex;
    $notlar[] = $_not->not_uindex;

    while (true) {
        if($_not->alt_ilknot != 0){
            $notlar = array_merge($notlar, array($_not->alt_ilknot));
            foreach ($sorgu_2 as $row) {
                if($row->not_uindex == $_not->alt_ilknot){
                    $_not = $row;
                    break;
                }
            }
        }
        else if ($_not->yan_alt != 0 && $_not->not_uindex != $secilen_not){
            $notlar = array_merge($notlar, array($_not->yan_alt));
            foreach ($sorgu_2 as $row) {
                if($row->not_uindex == $_not->yan_alt){
                    $_not = $row;
                    break;
                }
            }
        }
        else if ($_not->ustnot_index != $secilen_not && $_not->not_uindex != $secilen_not){
            while (true) {
                foreach ($sorgu_2 as $row) {
                    if($row->not_uindex == $_not->ustnot_index){
                        if($row->yan_alt != 0){
                            foreach ($sorgu_2 as $row2) {
                                if($row2->not_uindex == $row->yan_alt){
                                    $notlar = array_merge($notlar, array($row2->not_uindex));
                                    $_not = $row2;
                                    break 3;
                                }
                            }                            
                        }
                        else if($row->ustnot_index != $secilen_not){                          
                            $_not = $row;
                            break;
                        }
                        else {
                            break 3;
                        }
                    }
                }
            }
        }
        else {
            break;
        }
    }
    return $notlar;
}