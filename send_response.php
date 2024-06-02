<?php    //Yalnizca ajax isleminde calisir
require 'session_control.php';
require 'DB_control.php';
$db = new DB_control\Database();

$operation=$_GET["operation"];
$responseData=array();
switch($operation){
    case 'sign_up':
        try {
            $numara = intval($_POST["numara"]);  // SQLi korumasi
            if ($numara <= 0 || $numara > 999999999999){      //int olamazsa 0 olur
                $responseData["error"]="Gecersiz Numara";
                echo json_encode($responseData);
                break;
            }

            $add = $db->Insert("INSERT INTO accounts (numara,username,password) VALUES (?,?,?)", array($numara, $_POST["username"], $_POST["password"])); 
            if($add){
                $responseData["success"]="Hesap Olusturuldu";
            }else{
                $responseData["error"]="Hesap Olusturulamadi";
            }
        } catch (Exception $e) {
            if($e->getMessage() == "UNIQUE_USERNAME"){
                $responseData["error"] = "Bu Username zaten mevcut.";
            } 
            else if($e->getMessage() == "UNIQUE_NUMARA"){
                $responseData["error"] = "Bu Numara zaten mevcut.";
            }
            else if($e->getMessage() == "TOOLONG"){
                $responseData["error"] = "Karakter sinirini astin (Max 30).";
            }  
            else{
                $responseData["error"] = $e->getMessage();
            }        
        }
        echo json_encode($responseData);
        break;
    case 'sign_in':
        try {
            $numara = intval($_POST["numara"]);    // SQLi korumasi
            if ($numara <= 0 || $numara > 999999999999){      //int olamazsa 0 olur
                $responseData["error"]="Giris Basarisiz";
                echo json_encode($responseData);
                break;
            }

            $add = $db->getRow("SELECT * FROM accounts WHERE numara = (?)", array($numara));
            if($add && $add->username === $_POST["username"] && $add->password === $_POST["password"]){
                Start_session($_POST['username']);
                $responseData["success"] = "Giris Basarili";
            }else{
                $responseData["error"]="Giris Basarisiz";
            }
        } catch (Exception $e) {
        
        }
        echo json_encode($responseData);
        break;
    case 'logout':
        Stop_session();
        $responseData["success"] = "Cikis Basarili";
        echo json_encode($responseData);
        break;
    default:break;
}