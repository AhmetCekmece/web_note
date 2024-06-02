<?php
require "../backend/control_session.php";
require '../backend/control_db.php';
$db = new control_db\Database();

$responsePost = "";
$page_status = "";

if (isset($_POST['login'])) {
    global $responsePost, $page_status;
    $page_status = "login";

    try {
        $number = intval($_POST["number"]);               // SQLi korumasi
        if ($number <= 0 || $number > 999999999999) {      //int olamazsa 0 olur
            $responsePost = "Login Failed";
            return;
        }

        $sorgu_1 = $db->getRow("SELECT accounts.userid, accounts.username, accounts.password, config.unique_index, config.active_notuindex, config.notlar_width
                                FROM accounts LEFT JOIN config ON accounts.userid = config.userid 
                                WHERE accounts.numara = (?)", array($number));

        if($sorgu_1 && $sorgu_1->username === $_POST["username"] && $sorgu_1->password === $_POST["password"]){
            $sorgu_2 = $db->getRows("SELECT not_uindex, baslik, ustnot_index, altnot_adet, altnotlari_gizle, yan_ust, yan_alt    
            FROM notlar WHERE userid = (?)", array($sorgu_1->userid));

            $activenot = "";
            if($sorgu_2 && $sorgu_1->active_notuindex != 0){  //not varmi?
                $activenot = $db->getRow("SELECT * FROM notlar WHERE userid = (?) AND not_uindex = (?)", array($sorgu_1->userid, $sorgu_1->active_notuindex));                     
            }
            Start_session($_POST['username'], $sorgu_1->userid, $sorgu_1, $sorgu_2, $activenot);           
            header("Location: page_account.php");
            exit();
        }
        else{
            $responsePost = "Login Failed";
        }     

        // $sorgu = $db->getRow("SELECT * FROM accounts WHERE numara = (?)", array($number));
        // if ($sorgu && $sorgu->username === $_POST["username"] && $sorgu->password === $_POST["password"]) {
        //     Start_session($_POST['username'], $sorgu->userid);
        //     $responsePost = "Login successful";
        //     header("Location: page_account.php");
        //     exit();
        // } else {
        //     $responsePost = "Login Failed";
        // }
    } catch (Exception $e) {
       //$responsePost = $e->getMessage();
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
    global $responsePost, $page_status;
    $page_status = "signup";

    try {
        $number = intval($_POST["number"]);               // SQLi korumasi
        if ($number <= 0 || $number > 999999999999) {      //int olamazsa 0 olur
            $responsePost = "Invalid Number";
            return;
        }
        if ($_POST["password"] != $_POST["repassword"]){
            $responsePost = "Passwords are not the same";
            return;
        }

        $sorgu = $db->Insert("INSERT INTO accounts (numara,username,password) VALUES (?,?,?)", array($number, $_POST["username"], $_POST["password"]));
        if ($sorgu) {
            $responsePost = "Account Created";
        } else {
            $responsePost = "Account Could Not Be Created";
        }
    } catch (Exception $e) {
        if ($e->getMessage() == "UNIQUE_USERNAME") {
            $responsePost = "This Username already exists";
        } else if ($e->getMessage() == "UNIQUE_NUMARA") {
            $responsePost = "This Number already exists";
        } else if ($e->getMessage() == "TOOLONG") {
            $responsePost = "You have exceeded the character limit";
        }
        // else{
        //     $responsePost = $e->getMessage();
        // } 
    }
}