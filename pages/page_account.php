<?php 
    require "../backend/response_post.php"; 

    if (!$username) {
        header("Location: page_login.php");
        exit();
    } 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Not Defteri</title>
    <link rel="stylesheet" href="styles-account.css">
</head>
<body>
    <form method="post" id="logoutForm">
        <button type="submit" id="logout" name="logout">LOGOUT</button>
        <?php echo $username;?>  
    </form><hr>


</body>
</html>