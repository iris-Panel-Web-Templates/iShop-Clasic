<div id="container">
	<?php include("pages/etc_user.php") ?>
	<div id="mainContent">
		<h1>Son 50 satın alımınız</h1>
		<div class="dynContent" style="position:relative">
			<?php
            $accountID = $_SESSION['account_id'];
            $logsData = irisApi::$Shopping->Logs_Buys($accountID, false, 50);
            foreach ($logsData as $line) {
                $pName = iFunctions::LangDict_Get($line->productData->name);
                $princeType  = ($line->productData->price_Type == 0) ? "EP" : "MP";
                $visualValue = ($line->productData->visual_Type == 0) ? "/img/item/".$line->productData->visual_Value.".png" : $line->productData->visual_Value;
			?>
			<div class="item">
				<div class="itemDesc">
					<div class="thumbnailBgSmall">
						<img src="<?= $visualValue; ?>" onerror="this.src='/img/error.png';" width="63px" height="63px" alt=""/>
					</div>
					<p>
						<span class="itemTitle"><?= $pName ?> (<?= $line->productData->item_Count ?>x)</span>
						<span class="line"></span>
                        Alış Fiyatı: <b><?= number_format($line->buyAmount)." ".$princeType ?></b>
                        <br/>
                        Alış Zamanı: <a><?= date_format(date_create($line->buyDate),"Y/m/d H:i:s"); ?></a>
					</p>
				</div>
			</div>
			<?php } ?>
		</div>
		<div class="endContent"></div>
	</div>
</div>

