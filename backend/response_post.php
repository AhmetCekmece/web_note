<?php
require "../backend/control_session.php";
require '../backend/control_db.php';

$responsePost = "";
$page_status = "login";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    $db = null;
    try {
        $db = new control_db\Database();
    } catch (Exception $e) {
        $responsePost = "Baglanti Kurulamadi!"; 
        return;
    }

    if (isset($_POST['login'])) {
        global $responsePost, $giris_deneme, $son_deneme_zamani ,$page_status;
        $page_status = "login";

        if($giris_deneme === null){
            Giris_Denemesi(0);
        }

        if ($giris_deneme >= 3) {
            if (empty($son_deneme_zamani)) throw new Exception();
            $kalan_saniye = $son_deneme_zamani + 60 - time();
            if ($kalan_saniye > 0) {
                $responsePost = "Try again in $kalan_saniye seconds.";
                return;
            } else {
                Giris_Denemesi(0);
            }
        }

        if(!isset($_POST['number']) || !ctype_digit($_POST["number"]) || !isset($_POST['username']) || !isset($_POST['password'])){
            $responsePost = "Login Failed";
            return;
        }
    
        $number = intval($_POST["number"]);   // SQLi korumasi
        if ($number <= 0 || $number > 999999999 || strlen($_POST["username"]) > 30 || strlen($_POST["password"]) > 30) {      
            $responsePost = "Login Failed 2";
            return;
        } 

        try {
            $sorgu_1 = $db->getRow("SELECT accounts.userid, accounts.username, accounts.password, config.unique_index, config.active_notuindex, config.notlar_width
                                    FROM accounts LEFT JOIN config ON accounts.userid = config.userid 
                                    WHERE accounts.numara = (?)", array($number));  
                                    if (empty($sorgu_1)) throw new Exception();                
                                    if($sorgu_1->username !== $_POST["username"] || $sorgu_1->password !== $_POST["password"]) throw new Exception();

            $sorgu_2 = $db->getRows("SELECT not_uindex, baslik, ustnot_index, altnotlari_gizle, yan_ust, yan_alt    
                                    FROM notlar WHERE userid = (?)", array($sorgu_1->userid));
                                    //if (empty($sorgu_2)) throw new Exception();  //Bos olabilir
            
            $activenot = null;
            if($sorgu_1->active_notuindex !== 0){  //not varmi?
                $activenot = $db->getRow("SELECT * FROM notlar WHERE userid = (?) AND not_uindex = (?)", array($sorgu_1->userid, $sorgu_1->active_notuindex)); 
                if (empty($activenot)) throw new Exception();                    
            }

            $responsePost = "Login successful";
            Giris_Denemesi(0);
            Start_session($_POST['username'], $sorgu_1->userid, $sorgu_1, $sorgu_2, $activenot);           
            header("Location: page_account.php");
            exit();  

        } catch (Exception $e) {
            if(2 - $giris_deneme === 0){
                $responsePost = "Try again in 60 seconds.";
            }
            else {
                $responsePost = "Login Failed 3";
            }
            
            Giris_Denemesi($giris_deneme + 1);                     
            return;
        }
    }

    else if (isset($_POST['logout'])) {
        global $responsePost;
    
        Stop_session();
        $responsePost = "Logout Successful";
        header("Location: page_login.php");
        exit();
    } 

    else if (isset($_POST['signup'])) {
        global $responsePost , $page_status;
        $page_status = "login";
        $responsePost = "FORBIDDEN";
    }


    //___________________________________ SIGNUP KODLARI SILME !! _____________________________________________

    // else if (isset($_POST['signup'])) {
    //     global $responsePost;
    //     $page_status = "signup";

    //     if(!isset($_POST['number']) || !ctype_digit($_POST["number"]) || !isset($_POST['username']) || !isset($_POST['password']) || !isset($_POST['repassword'])){
    //         $responsePost = "Signup Failed";
    //         return;
    //     }
    
    //     $number = intval($_POST["number"]);   // SQLi korumasi
    //     if ($number <= 0 || $number > 999999999 || strlen($_POST["username"]) > 30 || strlen($_POST["password"]) > 30 || strlen($_POST["repassword"]) > 30) {      
    //         $responsePost = "Signup Failed 2";
    //         return;
    //     } 

    //     if ($_POST["password"] !== $_POST["repassword"]){
    //         $responsePost = "Passwords are not the same";
    //         return;
    //     }

    //     try {
    //         $srg = $db->Insert("INSERT INTO accounts (numara,username,password) VALUES (?,?,?)", array($number, $_POST["username"], $_POST["password"]));
    //         if (empty($srg)) throw new Exception();

    //         $responsePost = "Account Created";
    //         $page_status = "login";
    //         return;

    //     } catch (Exception $e) {

	// 		if (strpos($e->getMessage(), 'accounts_username_key') !== false) {    // Unique olmasi gerekirse bu hatayi aliriz
	// 			$responsePost = "This Username already exists";
	// 		}
    //         else if (strpos($e->getMessage(), 'accounts_numara_key') !== false) {
	// 			$responsePost = "This Number already exists";    
	// 		}
    //         else {				
	// 			$responsePost = "Signup Failed 3";
	// 		}
    //         return;
    //     }
    // }    

}





