<?php
session_start();
$username = isset($_SESSION["username"]) ? $_SESSION["username"] : null;
$userid = isset($_SESSION["userid"]) ? $_SESSION["userid"] : null;
$sorgu_1 = isset($_SESSION["sorgu_1"]) ? $_SESSION["sorgu_1"] : null;       // userid - username - password - unique_index - active_notuindex
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

function  Sorgu2_Ekle($_not_uindex, $_baslik = 'isimsiz', $_ustnot_index = 0, $_altnot_adet = 0, $_altnotlari_gizle = false, $_yan_ust = 0, $_yan_alt = 0){
    global $sorgu_2;
    $yeni_satir = new stdClass();
    $yeni_satir->not_uindex = $_not_uindex;
    $yeni_satir->baslik = $_baslik;
    $yeni_satir->ustnot_index = $_ustnot_index;
    $yeni_satir->altnot_adet = $_altnot_adet;
    $yeni_satir->altnotlari_gizle = $_altnotlari_gizle;
    $yeni_satir->yan_ust = $_yan_ust;
    $yeni_satir->yan_alt = $_yan_alt;

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
    $katman_sayac=0;
    if(!empty($sorgu_2)){
        //$html .= "<ul id='notlar_ul'>"; // Liste başlangıcı
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
                    $resp = Altnot_Bul_IlkSon(true, $row->not_uindex);
                    $alt_ilkot = isset($resp) ? $resp->not_uindex : 0;

                    $display_btn = "";      
                    if($alt_ilkot == 0){
                        $display_btn = "button_disabled";
                    }
                    $isim_btn = "▼";
                    $istek_tipi = 1; 
                    if($row->altnotlari_gizle == true){
                        $isim_btn = "►";
                        $istek_tipi = 0;
                    }

                    $ul_ekle="";
                    $ul_bitir="";
                    for ($i=0; $i < $katman_sayac; $i++) { 
                        $ul_ekle .= "<ul>";
                        $ul_bitir .= "</ul>";
                    }
                    
                    $html .= "<div class='notlar_divs' not_uindex='" . $row->not_uindex . "'>";
                    $html .= $ul_ekle;
                    $html .= "<li>";                           
                    $html .= "<button class='notgizle_btns " . $display_btn . "' not_uindex='" . $row->not_uindex . "' onclick='Altnot_Gizle_Post(" . $row->not_uindex . "," . $istek_tipi . ");'>" . $isim_btn ."</button>";
                    $html .= '<img src="../images/notepin.png" width="20px" height="25px">';
                    $html .= "<button class='notbaslik_btns' id='notbaslik" . $row->not_uindex . "' not_uindex='" . $row->not_uindex . "' onclick='Activenot_Sec_Post(" . $row->not_uindex . ");' draggable='true' ondragstart='dragStart(event)' ondragend='dragEnd(event)' ondrop='drop(event)' ondragover='dragOver(event)'>" . $row->baslik . "</button>";                    
                    $html .= "</li>";  
                    $html .= $ul_bitir;
                    $html .= "<div class='not_altcizgi' id='not_altcizgi" . $row->not_uindex . "' not_uindex='" . $row->not_uindex . "' ondragover='dragOverGizli(event)' ondragleave='dragLeaveGizli(event)' ondrop='drop(event)'></div>";
                    $html .= "</div>";
                    
                    if ($alt_ilkot != 0 && $row->altnotlari_gizle == false) {
                        $n_uindex = $alt_ilkot; // alt not 
                        //$html .= "<ul>";
                        $katman_sayac += 1;
                        break;
                    }
                    else if ($row->yan_alt != 0){     // yan not
                        $n_uindex = $row->yan_alt;
                        break;
                    } 
                    else if ($row->ustnot_index != 0) {
                        while (true) {
                            //$html .= "</ul>"; 
                            $katman_sayac -= 1;
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
        //$html .= "</ul>"; // Liste sonu
    }
    else{
        $html .= "<div style='height:100%; display:flex; align-items:center; justify-content: center;'><div style='opacity:0.6;'>Henüz not yok.</div></div>"; 
    }
    return $html;
}

function Not_Bul($_not_uindex){
    global $sorgu_2;
    
    foreach ($sorgu_2 as $row) {
        if($row->not_uindex == $_not_uindex){
            return $row;
        }
    }
    return null;
}

function Altnot_Bul_IlkSon($_nottipi, $_ustnot_index){   // true ilknot - false sonnot   /   0 en ust not
    global $sorgu_2;

    if($_nottipi){
        foreach ($sorgu_2 as $row) {
            if($row->ustnot_index == $_ustnot_index && $row->yan_ust == 0){
                return $row;
            }
        }
    }
    else {
        foreach ($sorgu_2 as $row) {
            if($row->ustnot_index == $_ustnot_index && $row->yan_alt == 0){
                return $row;
            }
        }
    }
    return null;
}

function Altnotlari_Bul($_not, $_not_uindex = null){  //kendisi dahil  (not listeledeki ul etiketini filtreleyerek bul ilerde)
    global $sorgu_2;
    if($_not_uindex != null){     //parametre olarak notun kendisi de olur index numarasi da hic farketmez 
        foreach ($sorgu_2 as $row) {
            if($row->not_uindex == $_not_uindex){
                $_not = $row;
                break;
            }
        }
    }
    $secilen_not = $_not->not_uindex;
    $notlar[] = $_not->not_uindex;

    while (true) {
        $resp = Altnot_Bul_IlkSon(true, $_not->not_uindex);
        $alt_ilkot = isset($resp) ? $resp->not_uindex : 0;

        if($alt_ilkot != 0){
            $notlar = array_merge($notlar, array($alt_ilkot));
            foreach ($sorgu_2 as $row) {
                if($row->not_uindex == $alt_ilkot){
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