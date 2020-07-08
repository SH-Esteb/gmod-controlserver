<?php
    require ('steamauth/steamauth.php');
    require ('config/config.php');
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Gmod-ControlServer - By Esteb</title>
    <meta name="description" content="You can manage your server with this!">
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/fonts/ionicons.min.css">
    <link rel="stylesheet" href="assets/css/-Login-form-Page-BS4-.css">
    <link rel="stylesheet" href="assets/css/Animated-CSS-Waves-Background-SVG.css">
    <link rel="stylesheet" href="assets/css/Login-Form-Dark.css">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>

<body style="background-color: #dd6060;">
    <div class="login-dark" style="background-color: rgba(0,0,0,0);background-image: url(&quot;0.png&quot;);">
        <form method="post">
            <h2 class="sr-only">Login Form</h2>
            <div class="illustration"><i class="icon ion-ios-locked-outline"></i></div>
            <?php
            if(!isset($_SESSION['steamid'])) {
              loginbutton("rectangle");
            }else{
              include ('steamauth/userInfo.php'); 
              if($steamprofile['steamid'] == $steamid64) {
                header("Location: panel");
              }else{
                echo("You are not on the administrator account.");
              }
            }
            ?>
        </form>
    </div>
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
</body>

</html>