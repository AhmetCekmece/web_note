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
    
        $add = $db->getRow("SELECT * FROM accounts WHERE numara = (?)", array($number));
        if ($add && $add->username === $_POST["username"] && $add->password === $_POST["password"]) {
            Start_session($_POST['username']);
            $responsePost = "Login successful";
            header("Location: page_account.php");
            exit();
        } else {
            $responsePost = "Login Failed";
        }
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

        $add = $db->Insert("INSERT INTO accounts (numara,username,password) VALUES (?,?,?)", array($number, $_POST["username"], $_POST["password"]));
        if ($add) {
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