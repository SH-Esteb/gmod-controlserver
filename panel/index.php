<?php
    require("../config/config.php");
    require_once("verif_session.php");
    require_once 'lib/SourceQuery.php';

    $server = new SourceQueryEsteb($ip, $port);
    $infos  = $server->getInfos(); 
?>


<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Gmod-ControlServer - By Esteb</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/fonts/fontawesome-all.min.css">
    <link rel="stylesheet" href="assets/fonts/font-awesome.min.css">
    <link rel="stylesheet" href="assets/fonts/fontawesome5-overrides.min.css">
    <link rel="stylesheet" href="assets/css/Brands.css">
    <link rel="stylesheet" href="assets/css/Footer-Basic.css">
    <link rel="stylesheet" href="assets/css/Footer-with-social-media-icons.css">
    <link rel="stylesheet" href="assets/css/Highlight-Clean.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.1.1/aos.css">
    <link rel="stylesheet" href="assets/css/Popup-Element-Overlay-1.css">
    <link rel="stylesheet" href="assets/css/Popup-Element-Overlay.css">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>

<body style="color: #dd6060;background-color: #dd6060;">
    <nav class="navbar navbar-light navbar-expand" style="color: #ffffff;background-color: #242424;">
        <div class="container-fluid"><a class="navbar-brand" href="#" style="color: rgb(255,255,255);"><strong>Gmod ControlServer - Bêta 1.0</strong><br></a><button data-toggle="collapse" class="navbar-toggler" data-target="#navcol-1"><span class="sr-only">Toggle navigation</span><span class="navbar-toggler-icon"></span></button>
            <div
                class="collapse navbar-collapse" id="navcol-1">
                <ul class="nav navbar-nav"></ul>
        </div>
        </div>
    </nav>
    <div class="text-center highlight-clean" style="background-color: #202020;">
        <div class="container">
            <div class="intro">
                <h2 class="text-center" style="color: #dddddd;"><i class="fa fa-server"></i><strong>&nbsp;<?php if($infos['mod'] == "") { echo "OFF SERVER"; }else{ echo($infos['name']); } ?></strong></h2>
            </div>
            <div class="buttons"><a class="btn btn-primary" role="button" data-aos="fade" data-aos-once="true" href="#"><i class="fa fa-heartbeat"></i>&nbsp;<strong><?php if($infos['mod'] == "") { echo "OFF SERVER"; }else{ echo"ACTIVATE"; } ?></strong></a><a class="btn btn-primary" role="button" data-aos="fade" data-aos-once="true" href="#"><i class="fa fa-gamepad"></i>&nbsp;<?php if($infos['mod'] == "") { echo "0"; }else{ echo($infos['players']); } ?> / 20</a>
                <a
                    class="btn btn-primary" role="button" data-aos="fade" data-aos-once="true" href="#"><i class="fas fa-sitemap"></i>&nbsp;<?php if($infos['mod'] == "") { echo "0.0.0.0:27015"; }else{ echo($infos['ip']); echo(":"); echo($infos['port']); } ?></a><a class="btn btn-primary" role="button" data-aos="fade" data-aos-once="true" href="#"><i class="fa fa-map"></i>&nbsp;<?php if($infos['mod'] == "") { echo "off_server"; }else{ echo($infos['map']); } ?></a></div>
        </div>
    </div>
    <div class="brands"><a href="#" style="background-color: #242424;color: #0085a1;"> </a></div>
    <div class="highlight-clean" style="background-color: #262626;">
        <div class="container">
            <div class="intro">
                <h2 class="text-center" style="color: rgb(255,255,255);"><i class="fa fa-flash"></i><strong>&nbsp;Fast Action</strong></h2>
                <p class="text-center">Here are some things you can do on your server. </p>
            </div>
            <div class="buttons"><a class="btn btn-primary" role="button" href="?logout"><i class="fas fa-unlock-alt"></i>&nbsp;LOGOUT</a><a class="btn btn-primary" role="button" href="#"><i class="fa fa-power-off"></i>&nbsp;STOP SERVER</a><a class="btn btn-primary" role="button"
                    href="#"><i class="fa fa-cubes"></i>&nbsp;CLEAN UP</a><a class="btn btn-primary" role="button" href="#"><i class="fas fa-cube"></i>&nbsp; CLEAR DECALS</a>
                <div class="buttons"><a class="btn btn-primary" role="button" href="?logout"><i class="fas fa-lock"></i>&nbsp; LOCK SERVER</a><a class="btn btn-primary" role="button" href="#"><i class="fas fa-unlock"></i>&nbsp;UNLOCK SERVER<br></a><a class="btn btn-primary"
                        role="button" href="#"><i class="fa fa-money"></i>&nbsp;RESET MONEY</a><a class="btn btn-primary" role="button" href="https://www.paypal.me/EEcallard"><i class="fab fa-paypal"></i>&nbsp; MAKE A DONATION</a></div>
            </div>
        </div>
    </div>
    <div class="brands"><a style="background-color: #242424;"> </a></div>
    <footer id="footerpad" style="background-color: #202020;">
        <div class="container">
            <div class="row">
                <div class="col-md-6 col-lg-8 mx-auto">
                    <ul class="list-inline text-center">
                        <li class="list-inline-item"></li>
                        <li class="list-inline-item"><a href="#"></a></li>
                        <li class="list-inline-item"><a href="#"></a></li>
                        <li class="list-inline-item"><a href="#"></a></li>
                    </ul><p class="copyright text-muted text-center">Copyright &copy; Gmod ControlServer 2020 | by !Esteb.#0001</p></div>
            </div>
        </div>
    </footer>
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="assets/js/bs-init.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.1.1/aos.js"></script>
    <script src="assets/js/Popup-Element-Overlay.js"></script>
</body>

</html>