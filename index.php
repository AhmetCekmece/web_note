<?php
// Eğer form gönderildiyse
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // JSON dosyasına kaydedilecek veriyi hazırla
    $yeni_not = array(
        'baslik' => $_POST['baslik'],
        'icerik' => $_POST['icerik']
    );

    // JSON dosyasını oku veya oluştur
    $dosya = 'notlar.json';
    $notlar = file_exists($dosya) ? json_decode(file_get_contents($dosya), true) : array();

    // Yeni notu notlar dizisine ekle
    $notlar[] = $yeni_not;

    // Notları JSON dosyasına kaydet
    file_put_contents($dosya, json_encode(array_values($notlar)));

    // Başarılı bir şekilde kaydedildiğini kullanıcıya bildir
    echo "<p>Notunuz başarıyla kaydedildi!</p>";
}

// Notları JSON dosyasından oku
$dosya = 'notlar.json';
$notlar = file_exists($dosya) ? json_decode(file_get_contents($dosya), true) : array();

// Eğer silme işlemi isteği varsa
if (isset($_POST['sil_id'])) {
    $sil_id = $_POST['sil_id'];
    // Eğer sil_id geçerli bir anahtar ise ve o anahtar notlar dizisinde varsa
    if (array_key_exists($sil_id, $notlar)) {
        // Notu sil
        unset($notlar[$sil_id]);
        // JSON dosyasındaki boş olan notları kaldır
        $notlar = array_filter($notlar);
        // Notları JSON dosyasına kaydet
        file_put_contents($dosya, json_encode(array_values($notlar)));
        // Silindiğini kullanıcıya bildir
        echo "<p>Not başarıyla silindi!</p>";
    } else {
        echo "<p>Geçersiz not ID</p>";
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
    <h2>Not Ekle</h2>
    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <label for="baslik">Başlık:</label><br>
        <input type="text" id="baslik" name="baslik"><br>
        <label for="icerik">İçerik:</label><br>
        <textarea id="icerik" name="icerik"></textarea><br><br>
        <input type="submit" value="Kaydet">
    </form>

    <h2>Kayıtlı Notlar</h2>
    <ul>
        <?php foreach ($notlar as $key => $not): ?>
            <li>
                <a href="?not_id=<?php echo $key; ?>"><?php echo $not['baslik']; ?></a>
                <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" style="display:inline;">
                    <input type="hidden" name="sil_id" value="<?php echo $key; ?>">
                    <button type="submit">Sil</button>
                </form>
            </li>
        <?php endforeach; ?>
    </ul>

    <?php
    // Eğer bir not bağlantısına tıklanmışsa ve not_id parametresi varsa
    if (isset($_GET['not_id'])) {
        $not_id = $_GET['not_id'];
        // Eğer not_id geçerli bir anahtar ise ve o anahtar notlar dizisinde varsa
        if (array_key_exists($not_id, $notlar)) {
            // Notun başlığını ve içeriğini göster
            $secili_not = $notlar[$not_id];
            echo "<h2>{$secili_not['baslik']}</h2>";
            echo "<p>{$secili_not['icerik']}</p>";

            // Düzenleme formu
            echo '<form method="post" action="' . $_SERVER['PHP_SELF'] . '">';
            echo '<input type="hidden" name="duzenle_id" value="' . $not_id . '">';
            echo '<label for="duzenle_baslik">Yeni Başlık:</label><br>';
            echo '<input type="text" id="duzenle_baslik" name="duzenle_baslik" value="' . $secili_not['baslik'] . '"><br>';
            echo '<label for="duzenle_icerik">Yeni İçerik:</label><br>';
            echo '<textarea id="duzenle_icerik" name="duzenle_icerik">' . $secili_not['icerik'] . '</textarea><br><br>';
            echo '<button type="submit">Kaydet</button>';
            echo '</form>';
        } else {
            echo "<p>Geçersiz not ID</p>";
        }
    }

    // Eğer düzenleme işlemi isteği varsa
    if (isset($_POST['duzenle_id'])) {
        $duzenle_id = $_POST['duzenle_id'];
        // Eğer duzenle_id geçerli bir anahtar ise ve o anahtar notlar dizisinde varsa
        if (array_key_exists($duzenle_id, $notlar)) {
            // Yeni başlık ve içerikle notu güncelle
            $notlar[$duzenle_id]['baslik'] = $_POST['duzenle_baslik'];
            $notlar[$duzenle_id]['icerik'] = $_POST['duzenle_icerik'];
            // Notları JSON dosyasına kaydet
            file_put_contents($dosya, json_encode(array_values($notlar)));
            // Güncellendiğini kullanıcıya bildir
            echo "<p>Not başarıyla güncellendi!</p>";
        } else {
            echo "<p>Geçersiz not ID</p>";
        }
    }
    ?>
</body>
</html>
