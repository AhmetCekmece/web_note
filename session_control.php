<?php
session_start();

function Start_session ($_username){
    $_SESSION["username"] = $_username;
    $_SESSION["session_yeni_acildi"] = true;
}

function Stop_session (){
    session_unset();
}

