<?php
session_start();
$username = isset($_SESSION["username"]) ? $_SESSION["username"] : null;

function Start_session ($_username){
    $_SESSION["username"] = $_username;
    $_SESSION["session_yeni_acildi"] = true;
}

function Stop_session (){
    session_unset();
}