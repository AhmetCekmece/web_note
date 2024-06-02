<?php
$dosya = '../assets/json/notlar.json';    //'notlar.json';
$notlar = file_exists($dosya) ? json_decode(file_get_contents($dosya), true) : array();

function Giris_Yap($_username, $_password) {
    $hesap = &Hesap_Bul($_username);
    if($hesap && $hesap['password'] === $_password){
        return true;
    }
    else {
        return false;
    }
}

function Hesap_Olustur($_username, $_password) {
    global $dosya, $notlar;
    $hesap = &Hesap_Bul($_username);

    if($hesap){
        return false;
    }
    else {
        $yeni_hesap = array(
            'username' => $_username,
            'password' => $_password, 
            'unique_index' => 0,
            'active_notuindex' => 0,
            'notlar' => array(), 
        );
        $notlar[] = $yeni_hesap;
        file_put_contents($dosya, json_encode(array_values($notlar), JSON_PRETTY_PRINT));
        return true;
    }
}

function &Hesap_Bul($_username) {
    global $notlar;
    foreach ($notlar as &$_hesap) {
        if ($_hesap['username'] === $_username) {
            return $_hesap;
        }
    }
    $_null = null;
    return $_null;
}

//__________________________________________________________

function &Not_Bulucu(&$_unot, $_uindex) {
    foreach ($_unot['notlar'] as &$not) {
        if ($not['not_uindex'] === intval($_uindex)) {
            $return_array = [&$_unot['notlar'], &$not, &$_unot];  // aranannotlist - aranannot - arananustnot
            return $return_array;
        }
        if ($not['altnot_adet'] !== 0) {
            $result = &Not_Bulucu($not, $_uindex);  //notu ustnot haline getirmek
            if ($result !== null) {
                return $result;
            }
        }
    }
    $null_var = null;
    return $null_var;
}

function Bagli_indexleri_bul($not) {
    $notlar[] = $not['not_uindex'];

    if ($not['altnot_adet'] > 0) {
        foreach ($not['notlar'] as $alt_not) {
            $alt_notlar = Bagli_indexleri_bul($alt_not);
            $notlar = array_merge($notlar, $alt_notlar);
        }
    }  
    return $notlar;
}

function Not_Goster_DB($_username, $_aranan_index) {    //active et
    global $dosya, $notlar;
    $hesap = &Hesap_Bul($_username);

    if($_aranan_index !== 0){
        $result = &Not_Bulucu($hesap, $_aranan_index);  
        $arananNot = &$result[1];     

        $hesap['active_notuindex'] = $_aranan_index;
        file_put_contents($dosya, json_encode($notlar, JSON_PRETTY_PRINT));
        return $arananNot;
    }
    else {
        $bos_not = array(
            'not_uindex' => 0,                       
            'baslik' => "",
            'icerik' => "",
            'ustnot_index' => 0, 
            'altnot_adet' => 0,
            'altnotlari_gizle' => false,
        );

        $hesap['active_notuindex'] = 0;
        file_put_contents($dosya, json_encode($notlar, JSON_PRETTY_PRINT));
        return $bos_not;
    }
}

function Not_Olustur_1($_username, $_active_notindex, $_baslik = "") {   //active_not varsa (0 değilse)
    global $dosya, $notlar;
    $hesap = &Hesap_Bul($_username);

    $result = &Not_Bulucu($hesap, $_active_notindex);  
    $arananNotList = &$result[0];
    $arananNot = &$result[1];
    $arananUstNot = &$result[2];

    $hesap['unique_index'] = $hesap['unique_index'] + 1;
    $yeni_not = array(
        'not_uindex' => $hesap['unique_index'],                       
        'baslik' => ($_baslik === "") ? "isimsiz" : $_baslik,
        'icerik' => "",
        'ustnot_index' => $arananNot['ustnot_index'], 
        'altnot_adet' => 0,
        'altnotlari_gizle' => false,
        'notlar' => array(),
    );

    if($arananNot['ustnot_index'] !== 0){
        $arananUstNot['altnot_adet'] = $arananUstNot['altnot_adet'] + 1;
    }
    
    $notlar_temp = array();
    foreach ($arananNotList as &$not) {
        $notlar_temp[] = $not;
        if ($not['not_uindex'] == $arananNot['not_uindex']) {   //active nottan bir sonrasina koy
            $notlar_temp[] = $yeni_not;
        }
    }
    $arananNotList = $notlar_temp;

    file_put_contents($dosya, json_encode($notlar, JSON_PRETTY_PRINT));
    return $yeni_not['not_uindex'];
}

function Not_Olustur_2($_username, $_baslik = "") {   //active_not yoksa (0 ise)
    global $dosya, $notlar;
    $hesap = &Hesap_Bul($_username);

    $hesap['unique_index'] = $hesap['unique_index'] + 1;
    $yeni_not = array(
        'not_uindex' => $hesap['unique_index'],                       
        'baslik' => ($_baslik === "") ? "isimsiz" : $_baslik,
        'icerik' => "",
        'ustnot_index' => 0, 
        'altnot_adet' => 0,
        'altnotlari_gizle' => false,
        'notlar' => array(),
    );

    $hesap['notlar'][] = $yeni_not;

    file_put_contents($dosya, json_encode($notlar, JSON_PRETTY_PRINT));
    return $yeni_not['not_uindex'];
}

function Altnot_Olustur($_username, $_active_notindex, $_baslik = "") {   
    global $dosya, $notlar;
    $hesap = &Hesap_Bul($_username);

    $result = &Not_Bulucu($hesap, $_active_notindex);  
    $arananNot = &$result[1];

    $hesap['unique_index'] = $hesap['unique_index'] + 1;
    $yeni_not = array(
        'not_uindex' => $hesap['unique_index'],                       
        'baslik' => ($_baslik === "") ? "isimsiz" : $_baslik,
        'icerik' => "",
        'ustnot_index' => $arananNot['not_uindex'], 
        'altnot_adet' => 0,
        'altnotlari_gizle' => false,
        'notlar' => array(),
    );

    $arananNot['notlar'][] = $yeni_not;
    $arananNot['altnot_adet'] = $arananNot['altnot_adet'] + 1;

    file_put_contents($dosya, json_encode($notlar, JSON_PRETTY_PRINT));
    return $yeni_not['not_uindex'];
}

function Not_Guncelle($_username, $_active_notindex, $_baslik, $_icerik) { 
    global $dosya, $notlar;
    $hesap = &Hesap_Bul($_username);

    $result = &Not_Bulucu($hesap, $_active_notindex);  
    $arananNot = &$result[1];

    $arananNot['baslik'] = $_baslik;
    $arananNot['icerik'] = $_icerik;

    file_put_contents($dosya, json_encode($notlar, JSON_PRETTY_PRINT));
    return $arananNot;
}

function Not_Sil($_username, $_active_notindex) {   
    global $dosya, $notlar;
    $hesap = &Hesap_Bul($_username);

    $result = &Not_Bulucu($hesap, $_active_notindex);  
    $arananNotList = &$result[0];
    $arananNot = &$result[1];
    $arananUstNot = &$result[2];


    foreach ($arananNotList as $sirasi => &$not) {
        if ($not['not_uindex'] === $_active_notindex) {
            unset($arananNotList[$sirasi]);
            //$hesap['notlar'] = array_values($hesap['notlar']);   //Indisleri yeniden sırala  -performans
            break; 
        }
    }
    if ($arananNot['ustnot_index'] !== 0){  
        $arananUstNot['altnot_adet'] = $arananUstNot['altnot_adet'] - 1;
    }

    file_put_contents($dosya, json_encode($notlar, JSON_PRETTY_PRINT));
}

function Altnotlari_Gizle($_username, $_aranan_index) {
    global $dosya, $notlar;
    $hesap = &Hesap_Bul($_username);

    $result = &Not_Bulucu($hesap, $_aranan_index);  
    $arananNot = &$result[1];

    if($arananNot['altnotlari_gizle'] === true){                        
        $arananNot['altnotlari_gizle'] = false;
    }
    else {
        $arananNot['altnotlari_gizle'] = true;
    }

    file_put_contents($dosya, json_encode($notlar, JSON_PRETTY_PRINT));
}

function Yanina_Tasi($_username, $_tasinan_index, $_alici_index) {
    global $dosya, $notlar;
    $hesap = &Hesap_Bul($_username);

    $result = &Not_Bulucu($hesap, $_tasinan_index);  
    $tasinanNotList = &$result[0];
    $tasinanNot = &$result[1];
    $tasinanUstNot = &$result[2];

    // echo json_encode(Bagli_indexleri_bul($tasinanNot));
    $bagli_indexler = Bagli_indexleri_bul($tasinanNot);
    if (in_array($_alici_index, $bagli_indexler)) {
        return false;
    }

    $result = &Not_Bulucu($hesap, $_alici_index);  
    $aliciNotList = &$result[0];
    $aliciNot = &$result[1];
    $aliciUstNot = &$result[2];
    
    if($aliciNot['ustnot_index'] !== 0){
        $aliciUstNot['altnot_adet'] = $aliciUstNot['altnot_adet'] + 1;
    }

    if($tasinanNot['ustnot_index'] !== 0){                        
        $tasinanUstNot['altnot_adet'] = $tasinanUstNot['altnot_adet'] - 1;
    }

    $tasinanNot['ustnot_index'] = $aliciNot['ustnot_index'];

    $temp_tasidigimnot = $tasinanNot;
    foreach ($tasinanNotList as $sirasi => &$not) {
        if ($not['not_uindex'] == $tasinanNot['not_uindex']) {
            unset($tasinanNotList[$sirasi]);
            //$hesap['notlar'] = array_values($hesap['notlar']);   //Indisleri yeniden sırala  -performans
            break; 
        }
    }

    $notlar_temp = array();
    foreach ($aliciNotList as &$not) {
        $notlar_temp[] = $not;
        if ($not['not_uindex'] == $aliciNot['not_uindex']) {
            $notlar_temp[] = $temp_tasidigimnot;
        }
    }
    $aliciNotList = $notlar_temp;

    file_put_contents($dosya, json_encode($notlar, JSON_PRETTY_PRINT));
    return true;
}

function Altina_Tasi($_username, $_tasinan_index, $_alici_index) {
    global $dosya, $notlar;
    $hesap = &Hesap_Bul($_username);

    $result = &Not_Bulucu($hesap, $_tasinan_index);  
    $tasinanNotList = &$result[0];
    $tasinanNot = &$result[1];
    $tasinanUstNot = &$result[2];

    // echo json_encode(Bagli_indexleri_bul($tasinanNot));
    $bagli_indexler = Bagli_indexleri_bul($tasinanNot);
    if (in_array($_alici_index, $bagli_indexler)) {
        return false;
    }

    $result = &Not_Bulucu($hesap, $_alici_index);  
    $aliciNot = &$result[1];

    $aliciNot['altnot_adet'] = $aliciNot['altnot_adet'] + 1;

    if($tasinanNot['ustnot_index'] !== 0){                        
        $tasinanUstNot['altnot_adet'] = $tasinanUstNot['altnot_adet'] - 1;
    }

    $tasinanNot['ustnot_index'] = $aliciNot['not_uindex'];

    $temp_tasidigimnot = $tasinanNot;
    foreach ($tasinanNotList as $sirasi => &$not) {
        if ($not['not_uindex'] == $tasinanNot['not_uindex']) {
            unset($tasinanNotList[$sirasi]);
            //$hesap['notlar'] = array_values($hesap['notlar']);   //Indisleri yeniden sırala  -performans
            break; 
        }
    }

    $aliciNot['notlar'][] = $temp_tasidigimnot;

    file_put_contents($dosya, json_encode($notlar, JSON_PRETTY_PRINT));
    return true;
}