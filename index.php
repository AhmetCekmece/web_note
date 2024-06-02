<?php
    if (isset($_POST['Hesap_olustur'])) {
        $dosya = 'notlar.json';
        $notlar = file_exists($dosya) ? json_decode(file_get_contents($dosya), true) : array();

        $yeni_not = array(
            'hesapadi' => $_POST['hesapadi'],
            'unique_index' => 0
        );
        $notlar[] = $yeni_not;
        file_put_contents($dosya, json_encode(array_values($notlar)));
    }

    if (isset($_POST['Not_Olustur'])) {
        $dosya = 'notlar.json';
        $notlar = file_exists($dosya) ? json_decode(file_get_contents($dosya), true) : array();

        $hesap = $_POST['hesap'];
        $hesap_adlari = array_column($notlar, 'hesapadi');
        if (in_array($hesap, $hesap_adlari)) {
            foreach ($notlar as &$not) {
                if ($not['hesapadi'] === $hesap) {
                    $not['unique_index'] = $not['unique_index'] + 1;
                    $yeni_not = array(
                        'not_uindex' => $not['unique_index'],
                        'baslik' => $_POST['baslik'],
                        'icerik' => $_POST['icerik']
                    );
                    $not['notlar'][] = $yeni_not;                   
                }
            }
            file_put_contents($dosya, json_encode($notlar));
        }
        else {
            echo "gecersiz hesapadi";
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
    <h2>Hesap Olustur</h2>
    <form method="post"> 
        <input type="hidden" name="Hesap_olustur">     
        <input type="text" id="hesapadi" name="hesapadi"><br>
        <input type="submit" value="Kaydet">
    </form><hr>

    <h2>Not Ekle</h2>
    <form method="post">
        <input type="hidden" name="Not_Olustur">
        <label for="hesap">Hesap Adı:</label><br>
        <input type="text" id="hesap" name="hesap"><br>
        <label for="baslik">Başlık:</label><br>
        <input type="text" id="baslik" name="baslik"><br>
        <label for="icerik">İçerik:</label><br>
        <textarea id="icerik" name="icerik"></textarea><br>
        <input type="submit" value="Kaydet">
    </form><hr>

    <h2>Not Listele</h2>
    <form method="post">
        <input type="hidden" name="Not_Listele">
        <label for="lhesap">Hesap Adı:</label><br>
        <input type="text" id="lhesap" name="lhesap"><br>       
        <input type="submit" value="Listele">
    </form>
    <?php
    if (isset($_POST['Not_Listele'])) {
        $dosya = 'notlar.json';
        $notlar = file_exists($dosya) ? json_decode(file_get_contents($dosya), true) : array();

        $lhesap = $_POST['lhesap'];

        // Belirtilen hesap adına göre notları filtrele
        $filtrelenmis_notlar = array_filter($notlar, function ($not) use ($lhesap) {
            return $not['hesapadi'] === $lhesap;
        });

        // Filtrelenmiş notların başlıklarını yazdır
        echo "<h2>$lhesap Hesabına Ait Notlar</h2>";
        echo "<ul>";
        foreach ($filtrelenmis_notlar as $not) {
            foreach ($not['notlar'] as $n) {
                echo "<li>{$n['baslik']}</li>";
            }
        }
        echo "</ul>";
    }
    ?><hr>
</body>
</html>