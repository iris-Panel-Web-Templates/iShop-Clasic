<?php

?>
<style>
    input[type=text], input[type=password]{
        width: 150px;
        height: 24px;
        line-height: 24px;
        border: 1px solid #c4ad89;
        background-color: #fefae9;
        color: #403f3f;
        font-size: 11px;
        padding-left: 5px;
        margin-left: 5px;
    }
    input[type=text]::placeholder, input[type=password]::placeholder{
        color: #bfbfbf;
    }
    .inputLine {
        height: 28px;
        line-height: 28px;
        margin-top: 3px;
    }
    .inputButton{
        position: absolute;
        right: 5px;
        top: 5px;
    }


    .button-83 {
        appearance: button;
        background-color: transparent;
        background-image: linear-gradient(to bottom, #fff, #f8eedb);
        border: 0 solid #e5e7eb;
        border-radius: .5rem;
        box-sizing: border-box;
        color: #482307;
        column-gap: 1rem;
        cursor: pointer;
        display: flex;
        font-family: ui-sans-serif,system-ui,-apple-system,system-ui,"Segoe UI",Roboto,"Helvetica Neue",Arial,"Noto Sans",sans-serif,"Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol","Noto Color Emoji";
        font-size: 100%;
        font-weight: 700;
        line-height: 24px;
        margin: 0;
        outline: 2px solid transparent;
        padding: 1rem 1.5rem;
        text-align: center;
        text-transform: none;
        transition: all .1s cubic-bezier(.4, 0, .2, 1);
        user-select: none;
        -webkit-user-select: none;
        touch-action: manipulation;
        box-shadow: -6px 8px 10px rgba(81,41,10,0.1),0 2px 2px rgba(81,41,10,0.2);
    }
    .button-83:active {
        background-color: #f3f4f6;
        box-shadow: -1px 2px 5px rgba(81,41,10,0.15),0 1px 1px rgba(81,41,10,0.15);
        transform: translateY(0.125rem);
    }
    .button-83:focus {
        box-shadow: rgba(72, 35, 7, .46) 0 0 0 4px, -6px 8px 10px rgba(81,41,10,0.1), 0 2px 2px rgba(81,41,10,0.2);
    }
</style>
<div id="container">
	<?php include("pages/etc.php") ?>
	<div id="mainContent">
		<h1>Giriş yap</h1>
		<div class="dynContent">
			<div class="item" id="confirmBox">
				<div class="itemDesc confirmDesc">
					<div class="thumbnailBgSmall" style="padding-left: 0;"><img src="/img/7227be80292ec244a17496ca9b2528.png" alt="" /></div>
						<form action="?s=login" method="post" style="position: relative;">
                            <div class="inputLine">
                                <div style="float: left;"><label><input type="text" placeholder="kullanıcı adı" name="loginName" size="20" required /></label></div>
                                <div style="float: left; padding-left: 5px; color: dimgrey;"> « Kullanıcı adı</div>
                            </div>
                            <div class="inputLine">
                                <div style="float: left;"><label><input type="password" placeholder="şifre" name="loginPass" required /></label></div>
                                <div style="float: left; padding-left: 5px; color: dimgrey;">« Şifre</div>
                            </div>
							<div class="inputButton"><input type="submit" name="login" value="Giriş Yap" class="button-83" /></div>
						</form>
                        <?php
                        if (isset($_GET['fail'])) {
                            echo '<div class="boxui box-title"></div><div class="boxui box-con"><div class="wrap"><br /><br /><span style="color: red;"><b>'.$_SESSION['last_error'].'</b></span><br /><br /><br /></div></div><div class="boxui box-end"></div>';
                            $_SESSION['last_error'] = "";
                        }
                        ?>
					<br class="clearfloat" />
				</div>
			</div>
		</div>
		<div class="endContent"></div>
	</div>
</div>
<?php
if(isset($_POST['login']) && $_POST['login'] == 'Giriş Yap') {
    $lResult = irisApi::$Account->Login($_POST['loginName'], $_POST['loginPass'], "");
    if($lResult->responseCode != 0) {
        $_SESSION['last_error'] = $lResult->responseMessage;
        header("Location: ".(iFunctions::IsLocal()?"?s=login&fail=true":"/login?fail=true"));
        exit;
    }
    header("Location: ".(iFunctions::IsLocal()?"?s=home":"/home"));
}
?>
