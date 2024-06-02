<?php
    require "../backend/control_session.php";

    if($sorgu_1->role !== 'admin'){
        header("Location: page_account.php");
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
        switch($operation){
            case 'kullanici_bul':
                $responseData = Kullanici_Bul($db);       
                break;

            case 'kullanici_sil':
                $responseData = Kullanici_Sil($db);       
                break;
                    
            default:
                $responseData["error"] = "Islem Tanimli Degil!";
                break;
        }
        echo json_encode($responseData);   
        exit;
    }

    $query_name = "";
    function Kullanici_Bul($_db){
        global $query_name;
        $query_name = $_POST["username_inp"];

        $srg = $_db->getRows("SELECT userid, numara, username, password, role FROM accounts WHERE username LIKE (?) ORDER BY numara", array('%' . $query_name . '%'));

        $responseData["success"] = "Basarili";
        $responseData["abc"] = $srg;
        return $responseData;
    }

    function Kullanici_Sil($_db){
        global $query_name;

        $srg = $_db->getRows("DELETE FROM notlar WHERE userid = (?)", array($_POST["sil_userid"]));
        $srg2 = $_db->getRows("DELETE FROM accounts WHERE userid = (?)", array($_POST["sil_userid"]));
        $srg3 = $_db->getRows("SELECT userid, numara, username, password, role FROM accounts WHERE username LIKE (?) ORDER BY numara", array('%' . $query_name . '%'));

        $responseData["success"] = "Basarili";
        $responseData["abc"] = $srg3;
        return $responseData;
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Arial, Helvetica, sans-serif;
            color: white;
            user-select:none;
        }
        *::-webkit-scrollbar-track
        {
            -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3);
            background-color: #333;
        }

        *::-webkit-scrollbar
        {
            background-color: black;
            width: 10px; /* Dikey scroll barın genişliği */
            height: 10px; /* Yatay scroll barın yüksekliği */
        }

        *::-webkit-scrollbar-thumb
        {
            -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,.3);
            background-color: #c00;
        }
        *::-webkit-scrollbar-corner{
            -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,.3);
            background-color: #333;
        }
        body{
            overflow: hidden;
        }
        td{
            text-align: center;
            height: 40px;
        }
        th{
            color: yellowgreen;
            height: 30px;
        }

        #hesap_container{ 
        width: 100%;
        height: 100%;      
        position: absolute;
        left: 0;
        top: 0; 
        z-index: 2;
        background-color: black;
        }
            .karart_container{
                width: 100%;
                height: 100%;  
                position: absolute;      
                background-color: black;
                opacity: 0.8;
            }
            #hesap_menu_overflow{
                width: 100%;
                height: 100%;
                position: absolute;
                display: flex;
                flex-direction:row;      
                overflow: auto;
            }
                #menu_sifredegis{   
                    flex-direction:column; 
                    margin: auto; 
                    border-radius: 10px;
                    background-color: #333;  
                    width: 500px;             
                    min-width: 500px;
                    min-height: 100px;  /* burası */
                    border: 1px solid darkred;
                }
                    #sifredegisForm{   
                        flex-grow: 1;  
                        margin: 10px;
                        display: flex;
                        flex-direction:column;
                        justify-content: center;               
                    }   
                        .menu_btns{
                            width: 100px;
                            height: 30px;
                            background-color: rgb(139, 0, 0);
                            border: none;
                            border-bottom: 2px outset rgb(120, 0, 0);
                            border-right: 1px outset rgb(120, 0, 0);
                            border-radius: 4px;
                            margin-top: 4px;
                        }
                        .menu_btns:hover{
                            opacity: 0.7;
                        }
                        .menu_btns:active{
                            border-top: 2px outset transparent;
                            border-left: 2px outset transparent;
                        }
                        .menu_inputbox{
                            border: none;
                            outline: none;
                            padding-left: 2px;
                            height: 25px;
                            margin-top: 5px;
                            background-color: #555;
                            border-radius: 4px;
                            width: 370px;   /* Burası */
                            font-size: medium;
                        }
                        .menu_baslik{
                            text-align: center;
                            margin-top: 25px;
                            font-size: large;
                        }
                        .menu_altbtns{
                            margin-top: 10px;
                            display: flex;
                            flex-direction: row;
                            justify-content: space-between;
                        }
    </style>
</head>
<body>
    <div id="hesap_container">
        <div class="karart_container"></div>
        <div id="hesap_menu_overflow">

            <div id="menu_sifredegis">
                <div class="menu_baslik">Admin Panel</div>
                <form id="sifredegisForm">
                    <div class="menu_altbtns">
                        <input id="username_inp" type="text" class="menu_inputbox" placeholder="Kullanıcı Adı" >
                        <button id="kullanicibul_btn" class="menu_btns" type="button" onclick="Kullanici_Bul_Post();">Bul</button> 
                    </div>    
                    <div id="tablo_div" style="margin-top:15px;"></div>
                </form>
            </div>
            
        </div>
    </div>
</body>
<script>
    const username_inp = document.getElementById('username_inp');
    const tabloDiv = document.getElementById('tablo_div');

    function SendForm(_FormID, _operation) {
        var form = document.getElementById(_FormID);
        var formData = new FormData(form);

        var xhr = new XMLHttpRequest();
        xhr.open("POST", 'page_adminpanel.php?operation=' + _operation);
        xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    var responseData = "";
                    try {
                        responseData = JSON.parse(xhr.responseText);
                        if(responseData.test1){
                            console.log("Test1: " + responseData.test1);
                        }
                        if(responseData.test2){
                            console.log("Test2: " + responseData.test2);
                        }
    
                        if (responseData.error) {
                            console.log("ERROR: " + responseData.error);
                        } else if (responseData.success) {
                            console.log("SUCCESS: " + responseData.success);
                            form.reset();  
                            Response_Islem(_operation, responseData);
                        }

                    } catch (error) {
                        console.log("BIGHATA: " + xhr.responseText)
                    }
                } else {
                    console.error('HATA: ' + xhr.status);
                }
            }
        };
        xhr.send(formData);
    }

    function Response_Islem(_operation, _responseData) {
        switch (_operation) {
            case 'kullanici_bul':
                //document.getElementById('testtext').innerHTML = _responseData.abc; 

                tabloDiv.innerHTML = '';
                var table = document.createElement('table');
                table.setAttribute('border', '1');

                var thead = document.createElement('thead');
                var headerRow = document.createElement('tr');
                var headers = ['Numara', 'Username', 'Password', 'Role', ''];
                var wd = 0;
                headers.forEach(function(headerText) {
                    var th = document.createElement('th');
                    th.appendChild(document.createTextNode(headerText));
                    th.style.width = (wd === 0 ? '200px' : '300px');
                    headerRow.appendChild(th);
                    wd++;
                });
                thead.appendChild(headerRow);
                table.appendChild(thead);

                var tbody = document.createElement('tbody');
                _responseData.abc.forEach(function(row) {
                    var tr = document.createElement('tr');

                    var numara = document.createElement('td');
                    numara.appendChild(document.createTextNode(row.numara));
                    tr.appendChild(numara);

                    var username = document.createElement('td');
                    username.appendChild(document.createTextNode(row.username));
                    tr.appendChild(username);

                    var password = document.createElement('td');
                    password.appendChild(document.createTextNode(row.password));
                    tr.appendChild(password);

                    var role = document.createElement('td');
                    role.appendChild(document.createTextNode(row.role));
                    tr.appendChild(role);

                    var silTd = document.createElement('td');
                    if(row.role !== 'admin' && row.role !== 'guest'){
                        var button = document.createElement('button');
                        button.type = "button";
                        button.classList.add('menu_btns');
                        button.style.width= '70px';
                        button.style.marginBottom= '5px';
                        button.textContent = 'SIL';
                        button.value = row.userid;
                        button.onclick = function() {
                            Kullanici_Sil_Post(this);
                        };
                        silTd.appendChild(button); 
                    }                  
                    tr.appendChild(silTd);

                    tbody.appendChild(tr);
                });
                table.appendChild(tbody);

                tabloDiv.appendChild(table);
                break;
            
            case 'kullanici_sil':
                Response_Islem("kullanici_bul", _responseData)
                break;
            
            default:
                break;
        }
    } 

    function Kullanici_Bul_Post(){
        let form = document.createElement('form');
        form.setAttribute('method', 'post');
        form.setAttribute('id', 'kullanicibulForm');

        let input1 = document.createElement('input');
        input1.setAttribute('type', 'hidden');
        input1.setAttribute('name', 'username_inp');
        input1.setAttribute('value', username_inp.value);  
        form.appendChild(input1);

        document.body.appendChild(form);
        SendForm(form.id,'kullanici_bul');
        form.remove();
    }

    function Kullanici_Sil_Post(_silbtn){
        let form = document.createElement('form');
        form.setAttribute('method', 'post');
        form.setAttribute('id', 'kullanicisilForm');

        let input1 = document.createElement('input');
        input1.setAttribute('type', 'hidden');
        input1.setAttribute('name', 'sil_userid');
        input1.setAttribute('value', _silbtn.value);  
        form.appendChild(input1);

        document.body.appendChild(form);
        SendForm(form.id,'kullanici_sil');
        form.remove();
    }

</script>
</html>
