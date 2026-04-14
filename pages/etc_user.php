<!--suppress DuplicatedCode -->
<div id="header">
    <div class="boxSigns">
        <span class="heading">Marka Parası (MP):</span>
        <span class="marksValue"><?= number_format($_SESSION['mileage']);?></span>
        <a href="<?= iFunctions::IsLocal() ? "?s=user_faq": "/faq" ?>" class="tip helpSmallIcon" title="Yardım sayfasına buradan gidin." style="right: 23px;"><img src="/img/helpSmallIcon.png" alt="" /></a>
    </div>
    <div class="boxCoins">
        <span class="heading">Ejderha Parası (EP):</span>
        <span class="coinsValue"><?= number_format($_SESSION['cash']);?></span>
        <a href="<?= iFunctions::IsLocal() ? "?s=user_faq": "/faq" ?>" class="tip helpSmallIcon" title="Yardım sayfasına buradan gidin." style="right: 7px;"><img src="/img/helpSmallIcon.png" alt="" /></a>
        <a href="<?= iFunctions::IsLocal() ? "?s=user_pay": "/pay" ?>" class="purchaseButton" title="Ejderha Paraları Alın">Ejderha parası sipariş edin</a>
    </div>
</div>
<div class="userdataDiv"><a title="Satın alma geçmişiniz" href="<?= iFunctions::IsLocal() ? "?s=user_buylog": "/buylog" ?>" class="tip userdataIcon"></a></div>

<ul id="breadcrumb">
    <li><a href="<?= iFunctions::IsLocal() ? "?s=home": "/home" ?>">Ana sayfa</a></li>
    <li><a>-</a></li>
    <li><a href="<?= iFunctions::IsLocal() ? "?s=logout": "/logout" ?>">Çıkış</a></li>
</ul>
<div id="sidebar1">
	<ul id="mainMenu">
        <li><a href="<?= iFunctions::IsLocal() ? "?s=user_pay": "/pay" ?>">Ejder Parası Al</a></li>
        <li><a href="<?= iFunctions::IsLocal() ? "?s=user_paylog": "/paylog" ?>">Ejder Parası Geçmişi</a></li>

        <li style="margin-top: 5px;"><a href="<?= iFunctions::IsLocal() ? "?s=user_buylog": "/buylog" ?>">Satın Alma Geçmişi</a></li>
        <li style="margin-top: 5px;"><a href="<?= iFunctions::IsLocal() ? "?s=user_faq": "/faq" ?>">Yardım</a></li>
        <li><a href="<?= iFunctions::IsLocal() ? "?s=logout": "/logout" ?>">Çıkış</a></li>
	</ul>
</div>
