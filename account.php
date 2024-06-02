<?php require 'account_php.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Not Defteri</title>
    <!-- <link rel="stylesheet" href="./account_styles.css"> -->
    <style> 
        ul{
            list-style-type: none;
            padding-inline-start: 30px;
        }

        li{
            display: block;
        }

        .button_disabled {
            opacity: 0.5; 
            pointer-events: none;
        }

        .button_disabled_v2 {
            opacity: 0.2;
            pointer-events: none;
        }

        .display_none {
            display: none;
        }

        .notGizliButon{
            transition: height 0.2s; 
            margin-left: 30px;
            height: 8px;
            width: 100px;
            display: block; 
            opacity: 0.2;
        }

    </style>
</head>
<body>
    <form method="post">
        <input type="submit" name="Test" value="Test"><br><br>
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
        <input type="submit" id="AltYeni_Not" name="AltYeni_Not" value="Alt Not Olustur" class="<?php echo $not_duzenle_enable; ?>" >
        <input type="text" id="anbaslik" name="anbaslik" placeholder="baslik" class="<?php echo $altyeninot_popup; ?>" >
        <input type="submit" id="AltNot_Olustur" name="AltNot_Olustur" value="Olustur" class="<?php echo $altyeninot_popup; ?>" >
        <input type="submit" id="AltNot_Olustur_Iptal" name="AltNot_Olustur_Iptal" value="Iptal et" class="<?php echo $altyeninot_popup; ?>" >
    </form><hr>

    <span id="notlarim">
    <h2>Notlarim</h2>
    <?php
    $dosya = 'notlar.json';
    $notlar = file_exists($dosya) ? json_decode(file_get_contents($dosya), true) : array();

    function list_notes($notes) {
        echo "<ul>";
        foreach ($notes as $note) {
            echo "<li>";

            echo "<button type='submit' name='AltNotlari_Gizle' value='{$note['not_uindex']}'";     // ► ◄ ▲ ▼
            if ($note['altnot_adet'] === 0) {
                echo " class='button_disabled_v2'";
            }
            
            if($note['altnotlari_gizle'] === false){
                echo ">▼</button>";
                //echo "<button type='submit' name='Not_Goster' value='{$note['not_uindex']}'>{$note['baslik']}</button>";
                echo "<button id='notButon{$note['not_uindex']}' class='notButon' type='submit' name='Not_Goster' value='{$note['not_uindex']}' draggable='true' ondragstart='dragStart(event)' ondragend='dragEnd(event)' ondrop='drop(event)' ondragover='dragOver(event)'>{$note['baslik']}</button>";
                echo "<br><button id='notGizliButon{$note['not_uindex']}' class='notGizliButon' type='submit' value='{$note['not_uindex']}' ondragover='dragOverGizli(event)' ondragleave='dragLeaveGizli(event)' ondrop='drop(event)'></button>";
                // Eğer bu notun alt notları varsa, alt notları da listele
                if ($note['altnot_adet'] !== 0) {     
                    list_notes($note['notlar']);
                }
            }
            else{
                echo ">►</button>";
                //echo "<button type='submit' name='Not_Goster' value='{$note['not_uindex']}'>{$note['baslik']}</button>";
                echo "<button id='notButon{$note['not_uindex']}' class='notButon' type='submit' name='Not_Goster' value='{$note['not_uindex']}' draggable='true' ondragstart='dragStart(event)' ondragend='dragEnd(event)' ondrop='drop(event)' ondragover='dragOver(event)'>{$note['baslik']}</button>";
                echo "<br><button id='notGizliButon{$note['not_uindex']}' class='notGizliButon' type='submit' value='{$note['not_uindex']}' ondragover='dragOverGizli(event)' ondragleave='dragLeaveGizli(event)' ondrop='drop(event)'></button>";
            }
            
            echo "</li>";
        }
        echo "</ul>";
    }

    echo "<form method='post'>";        
    foreach ($notlar as $hesap) {
        if ($hesap['username'] === $username) {
            if(count($hesap['notlar']) > 0){
                list_notes($hesap['notlar']);                
            }
            else{
                echo "Henüz hiç not yok.";              
            }
            break;
        }
    }
    echo "</form>";
    ?><hr>
    </span>

    <span id="not_duzenle">
    <h2>Not Düzenle</h2>
    <form method="post">
        <input type="hidden" id="duzenle_notuindex" name="notuindex" value="<?php echo isset($_SESSION["active_notuindex"]) ? $_SESSION["active_notuindex"] : 0; ?>">
        <input type="text" id="duzenle_baslik" name="baslik" placeholder="baslik" value="<?php echo isset($_SESSION["active_baslik"]) ? $_SESSION["active_baslik"] : ''; ?>"><br>
        <textarea id="duzenle_icerik" name="icerik" placeholder="icerik"><?php echo isset($_SESSION["active_icerik"]) ? $_SESSION["active_icerik"] : ''; ?></textarea><br>
        <input type="submit" name="Not_Guncelle" value="Guncelle" class="<?php echo $not_duzenle_enable; ?>" >
        <input type="submit" name="Not_Sil" value="Sil" class="<?php echo $not_duzenle_enable; ?>" >
    </form><hr>
    </span>



    <script>
        function dragStart(event) {   //tasima basladiginda butonun id sini text olarak tasir.
            event.dataTransfer.setData("text", event.target.id);
        }

        function dragEnd(event) {     //tasima durdugunda islemi durdurur.
            event.preventDefault();
        }

        function dragOver(event) {    //uzerine geldiginde ne yapsin
            event.preventDefault();
        }

        function dragOverGizli(event) {    //uzerine geldiginde ne yapsin
            event.preventDefault();
            var targetButton = event.target;
            targetButton.style.height = '30px';
        }

        function dragLeaveGizli(event) {         //uzerinden ayrildiginda ne yapsin
            event.preventDefault();
            var targetButton = event.target;
            targetButton.style.height = '8px';
        }

        function drop(event) {
            event.preventDefault();  //tasidigim butonun pozisyon degistirmesini engeller
            var data = event.dataTransfer.getData("text");
            var draggedButton = document.getElementById(data);   //tasidigim buton  
            var dropTarget = event.target;                       //alici buton
            if (dropTarget.classList.contains('notGizliButon') && dropTarget.id !== draggedButton.id && dropTarget.value !== draggedButton.value) {
                dropTarget.style.height = '5px';
                Not_Tasi_Post("Yanina_Tasi", draggedButton, dropTarget);
            }
            else if(dropTarget.classList.contains('notButon') && dropTarget.id !== draggedButton.id && dropTarget.value !== draggedButton.value){
                dropTarget.style.height = '5px';
                Not_Tasi_Post("Altina_Tasi", draggedButton, dropTarget);
            }
            else if(dropTarget.classList.contains('notGizliButon')){
                dropTarget.style.height = '5px';
            }
        }

        function Not_Tasi_Post(_tasimaTuru, _draggedButton, _dropTarget){   //tasimaTuru = altnota - yanina
            // Gizli bir form oluşturma
            var form = document.createElement('form');
            form.setAttribute('method', 'post');
            form.setAttribute('action', 'account.php'); 

            // Gizli input alanları oluşturma ve form içine yerleştirme
            var input1 = document.createElement('input');
            input1.setAttribute('type', 'hidden');
            input1.setAttribute('name', _tasimaTuru);
            input1.setAttribute('value', 'true');
            form.appendChild(input1);

            var input2 = document.createElement('input');
            input2.setAttribute('type', 'hidden');
            input2.setAttribute('name', 'tasidigim_not');
            input2.setAttribute('value', _draggedButton.value);
            form.appendChild(input2);

            var input3 = document.createElement('input');
            input3.setAttribute('type', 'hidden');
            input3.setAttribute('name', 'alici_not');
            input3.setAttribute('value', _dropTarget.value);
            form.appendChild(input3);

            // Formu sayfaya ekleyip otomatik olarak gönderme
            document.body.appendChild(form);
            setTimeout(function() {
                form.submit();
            }, 50);
        }

    </script>
</body>
</html>

