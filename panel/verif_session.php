<?php
    require ('../steamauth/steamauth.php');
    require ('../config/config.php');

if(!isset($_SESSION['steamid'])) {

    header("Location: " . $domain);
    
}  else {
    include ('../steamauth/userInfo.php');

    if($steamprofile['steamid'] != $steamid64) {
        header("Location: " . $domain);
    }
}    
?>  

