<?php 
    require "../backend/response_post.php"; 

    if ($username) {
        header("Location: page_account.php");
        exit();
    } 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../styles/style_login.css">
</head>
<body>
    <div id="page_status" style="display:none"><?php echo $page_status; ?></div>
    <div id="container">
        <div id="hesap_container">
            <div id="karart_container"></div>
            <div id="hesap_menu_overflow">
                <div id="hesap_menu">                   
                    <form method="post" class="s_form" id="loginForm" style="display:flex">
                        <img id="userpng" class="loginpng" src="../images/user.png" width="130px">                       
                        <div class="inputBox"><input type="text" id="lnumber" name="number" maxlength="9" placeholder="" > <i>Number</i> </div>
                        <div class="inputBox"><input type="text" id="lusername" name="username" maxlength="30" placeholder="" > <i>Username</i> </div>
                        <div class="inputBox"><input type="password" id="lpassword" name="password" maxlength="30" placeholder="" > <i>Password</i> </div>
                        <div class="label_btns">
                            <label class="label_btn" onclick="FormDegistir(true);">Sign Up</label>
                            <label class="label_btn" onclick="TestAccountLogin();">Guest</label>    <!-- (Guest) test hesabi -->
                        </div>                       
                        <button class="submitBtn" type="submit" id="login" name="login">LOGIN</button> 
                        <label class="info_lbl" id="linfo_lbl"><?php echo $responsePost; ?></label> 
                    </form>
                    <form method="post" class="s_form" id="signupForm" style="display:none">
                        <img id="userartipng" class="loginpng" src="../images/user_arti.png" width="130px"> 
                        <div class="inputBox"><input type="text" id="cnumber" name="number" maxlength="9" placeholder="" required> <i>Number</i> </div>
                        <div class="inputBox"><input type="text" id="cusername" name="username" maxlength="30" placeholder="" required> <i>Username</i> </div>
                        <div class="inputBox"><input type="password" id="cpassword" name="password" maxlength="30" placeholder="" required> <i>Password</i> </div>
                        <div class="inputBox"><input type="password" id="crepassword" name="repassword" maxlength="30" placeholder="" required> <i>Password Again</i> </div>
                        <div class="label_btns">
                            <label class="label_btn" onclick="FormDegistir(false);">Login</label>
                            <label class="label_btn"></label>
                        </div>
                        <button class="submitBtn" type="submit" id="signup" name="signup">SIGNUP</button>  
                        <label class="info_lbl" id="cinfo_lbl"><?php echo $responsePost; ?></label> 
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="../js/script_login.js"></script>
</body>
</html>

<?php exit;