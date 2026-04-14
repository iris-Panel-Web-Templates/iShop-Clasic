<?php
    //session_set_cookie_params([
    //    'lifetime' => 60*60*24*365, // Session ve Çerezler için saklanma süresini belirler
    //    'path'     => '/',
    //    'domain'   => $_SERVER['HTTP_HOST'],
    //    'secure'   => true, // Yanlızca HTTPS üzeridnen erişilebilmesini sağlar
    //    'httponly' => true, // Javascrip ve benzer 3. part yazılımların eişimini engeller
    //    'samesite' => 'Strict'
    //]);
	session_start();
    setcookie("power-x", "none", [
        'expires'  => time() + (60*60*24*30),
        'path'     => '/',
        'domain'   => $_SERVER['HTTP_HOST'], // dikkat: başında nokta olmalı
        'secure'   => true,
        'httponly' => true,
        'samesite' => 'Strict' // Başka siteler yada
    ]);
    include("iSystem/iApi.php");
    include("iSystem/iPage_Index.php");
    ob_start(); // Çıktı tamponlamayı başlat
?>
<!DOCTYPE html>
<html lang="<?= iFunctions::GetLanguage() ?>" >
	<head>
		<?php include("./head.php"); ?>
        <?php irisApi::$UserAgent->IncludePage(); ?>
        <title></title>
        <style> body { background-image: url('/img/bg.png'); background-repeat: no-repeat; background-position: left top; } </style>
    </head>
    <body class="twoColFixLtHdr">
		<?php
			if(!empty($_GET['s'])) {
				if(file_exists("./pages/".$_GET['s'].".php")) {
					include("./pages/".$_GET['s'].".php");
				}
                else { include("./pages/home.php"); }
			} else { include("./pages/home.php"); } ?>
	</body>
</html>
<?php ob_end_flush(); // Çıktı tamponlamayı tamamla ?>
