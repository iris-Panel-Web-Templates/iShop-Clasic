<?php
	if(!isset($_SESSION['account_id'])) {
        echo "<div id='header'></div><div id='breadcrumb'>&nbsp;</div><div id='sidebar1'></div>";
        return;
    }
?>
<!--suppress CssUnusedSymbol -->
<style>
    .searchFocus{ filter: blur(1px) grayscale(55%); position: absolute; }
    .searchBlur { filter: blur(0)     grayscale(0); position: relative; }

    .hhBox {
        margin: 10px auto;
        width: 96px;
        height: 128px;
        background-image: url("/img/hayhour_01.png");
        background-repeat: no-repeat;
        background-size: 96px;
        background-position: center top;
        font-size: 10px;
        white-space: nowrap;
        padding-top: 96px;
        text-align: center;
        color: darkgray;
        animation-name: hhAnimate;
        animation-duration: 3s;
        animation-iteration-count: infinite;
    }
    .hhBox .hhValue { font-size: 14px; font-weight: bold; color: #52100e; }

    .allDiscountBox {
        margin: 10px auto;
        width: 128px;
        height: 128px;
        background-image: url("/img/event_discount_01.png");
        background-repeat: no-repeat;
        background-size: 128px;
        background-position: center top;
        font-size: 10px;
        white-space: nowrap;
        padding-top: 64px;
        text-align: center;
        color: darkgray;
        animation-name: hhAnimate;
        animation-duration: 3s;
        animation-iteration-count: infinite;
    }
    .allDiscountBox .adValue { font-size: 14px; font-weight: bold; color: #52100e; }


    @keyframes hhAnimate {
        0%   { filter: grayscale(0%);}
        12%  { filter: grayscale(15%);}
        25%  { filter: grayscale(30%);}
        37%  { filter: grayscale(45%);}
        50%  { filter: grayscale(60%);}
        62%  { filter: grayscale(45%);}
        75%  { filter: grayscale(30%);}
        87%  { filter: grayscale(15%);}
        100% { filter: grayscale(0%);}
    }

</style>
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
	<div id="search">
		<form action="<?= iFunctions::IsLocal() ? "?s=category&id=0": "/category?id=0" ?>" method="post" name="searchForm" onsubmit="return TrySearch()">
			<!--suppress HtmlFormInputWithoutLabel -->
            <input type="text" placeholder="Arama terimi" class="type" name="txtSearchKey" id="txtSearchKey" onfocus="SearchFocusGained();" onblur="SearchFocusLost();" maxlength="32" />
            <input type="submit" value="" class="send" />
		</form>
	</div>
	<ul id="mainMenu">
		<?php
        $cBase = irisApi::$Category->Category_Get()["Base"];
        foreach ($cBase as $line) {
            $cID   = $line->id;
            $cName = irisApi::$Category->CategoryBase_GetName($cID);
			if (iFunctions::IsLocal())
                echo '<li><a href="?s=category&id='.$cID.'" title="'.$cName.'">'.$cName.'</a></li>';
            else
                echo '<li><a href="/category?id='.$cID.'" title="'.$cName.'">'.$cName.'</a></li>';
		}
		?>
	</ul>

    <?php
    $eventInfo = irisApi::$Events->Get_ByType("ItemShop_HapyHour");
    if ($eventInfo != null && $eventInfo->dateStart_Unix < time() && $eventInfo->dateFinish_Unix > time()) {
        echo
            "<div class='hhBox'>
                <span class='hhValue'>$eventInfo->value%</span><br/>
                <span class='hhStart'>".iFunctions::DateTime_UtcToZone($eventInfo->dateStart, true)."</span><br/>
                <span class='hhFinish'>".iFunctions::DateTime_UtcToZone($eventInfo->dateFinish, true)."</span>
            </div>";
    }

    $eventInfo = irisApi::$Events->Get_ByType("ItemShop_Discount");
    if ($eventInfo != null && $eventInfo->dateStart_Unix < time() && $eventInfo->dateFinish_Unix > time()) {
        echo
            "<div class='allDiscountBox'>
                <span class='adValue'>$eventInfo->value%</span><br/>
                <span class='adStart'>".iFunctions::DateTime_UtcToZone($eventInfo->dateStart, true)."</span><br/>
                <span class='adFinish'>".iFunctions::DateTime_UtcToZone($eventInfo->dateFinish, true)."</span>
            </div>";
    }

    ?>
</div>
<!--suppress JSUnresolvedReference -->
<script type="text/javascript" language="javascript" defer="defer" title="etc">
    function TrySearch(){
        const searchKey = document.getElementById("txtSearchKey").value;
        if (searchKey === null)      { return false; }
        if (searchKey === undefined) { return false; }
        if (searchKey === "")        { return false; }
        return searchKey.length >= 2;
    }
    function SearchFocusGained(){
        document.getElementById('header').className = "searchFocus";
        document.getElementById('mainMenu').className = "searchFocus";
        document.getElementById('mainContent').className = "searchFocus";

        const eventHapyHour = document.getElementsByClassName('hhBox')[0];
        if (eventHapyHour !== null && eventHapyHour !== undefined) { eventHapyHour.style.display = 'none'; }
        const eventDiscount = document.getElementsByClassName('allDiscountBox')[0];
        if (eventDiscount !== null && eventDiscount !== undefined) { eventDiscount.style.display = 'none'; }
        console.log("On Focus");
    }
    function SearchFocusLost(){
        document.getElementById('header').className = "searchBlur";
        document.getElementById('mainMenu').className = "searchBlur";
        document.getElementById('mainContent').className = "searchBlur";

        const eventHapyHour = document.getElementsByClassName('hhBox')[0];
        if (eventHapyHour !== null && eventHapyHour !== undefined) { eventHapyHour.style.display = 'block'; }
        const eventDiscount = document.getElementsByClassName('allDiscountBox')[0];
        if (eventDiscount !== null && eventDiscount !== undefined) { eventDiscount.style.display = 'block'; }
        console.log("On Blur");
    }
</script>