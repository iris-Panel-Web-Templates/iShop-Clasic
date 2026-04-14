<?php
    /** @noinspection DuplicatedCode */
	if (!isset($_GET['id'])) { die("Bir ürün seçmelisiniz."); }
	$productID = intval($_GET['id']);
    $product   = irisApi::$Products->Products_Detail($productID);
    $pName     = iFunctions::LangDict_Get($product->name);
    $pDesc     = iFunctions::LangDict_Get($product->desc);
    if (empty($pDesc)) { $pDesc = "<span>Ürünün açıklaması yok.</span>"; }

    $buyCheck    = false;
    $princeType  = ($product->price_Type == 0) ? "EP" : "MP";
    $priceAmount = $product->price_Amount;
    if ($product->discount_Type == 1) { $priceAmount -= ($priceAmount/100) *$product->discount_Value; }
    $eventDiscount = irisApi::$Events->Get_ByType("ItemShop_Discount");
    if ($eventDiscount != null) { $priceAmount -= ($priceAmount/100) *$eventDiscount->value; }

    if ($product->price_Type == 0 && $_SESSION['cash']    >= $priceAmount){ $buyCheck = true; }
    if ($product->price_Type == 1 && $_SESSION['mileage'] >= $priceAmount){ $buyCheck = true; }
    $visualValue = ($product->visual_Type == 0) ? "/img/item/".$product->visual_Value.".png" : $product->visual_Value;

?>
<!--suppress CssUnusedSymbol -->
<style>
    .thumbnailBgSmall{ display: table; max-height: 64px; opacity: 0.9;}
    .thumbnailBgSmall:hover { opacity: 1; }
    .thumbnailBgSmall a { display: table-cell; vertical-align: middle; text-align: center; }
    .thumbnailBgSmall a img { max-height: 64px; }
    .thumbnailBgSmall .discountRnd { position: absolute; left: 0; top: 0; background-image: url("/img/discountPercentPromoted.png"); background-repeat: no-repeat; width: 55px; height: 48px; }

    .boxLeft{ display: table; width: 116px; height: 116px; }
    .boxLeft a { display: table-cell; vertical-align: middle; text-align: center; }
    .spDiscount { color: #c1bfbf; text-decoration: line-through; text-decoration-style: double; }
    .discountBig { position: absolute; left: 0; top: 0; background-image: url("/img/discountPercentOffer.png"); background-repeat: no-repeat; width: 55px; height: 48px; }
</style>
<?php

?>
<div id="fancybox-content" style="border-width: 0; width: 540px; height: 500px;">
	<div style="width:540px;height:500px;overflow: hidden;position:relative;">
		<h1 class="mainHeadline"><?= $pName; ?></h1>
		<div class="dynContent detail">
			<div class="box boxLeft" style="position: relative;">
                <?php if ($priceAmount != $product->price_Amount) { echo "<div class=\"discountBig\"></div>"; } ?>
                <a><!--suppress HtmlDeprecatedAttribute -->
                    <img alt="<?= $pName; ?>" onerror="this.src='img/error.png';" src="<?= $visualValue ?>"></a>
            </div>
			<div class="box desc descOnlyItem">
				<div class="detailBadge"><div class="detailBadgeInner"> … </div></div>
				<h2><?= $pName; ?></h2>
				<div class="scrollpane scrollpaneOnlyItem" style="overflow: hidden; padding: 0; width: 351px;">
					<div class="jspContainer" style="width: 351px; height: 140px;">
						<div class="jspPane" style="padding: 0; top: 0; width: 351px;">
							<p> <?= $pDesc ?> </p>
						</div>
					</div>
				</div>
			</div>
			<div class="box boxRight buy onlyItem">
				<div class="priceSelect">
                    <div class="sprice">
                        Fiyat:
                        <span id="priceAmount"><?= number_format($priceAmount) ?></span> <?= $princeType ?>
                        <?php if ($priceAmount != $product->price_Amount) { echo "<span class=\"spDiscount\">$product->price_Amount $princeType</span>"; } ?>
                    </div>
                </div>
				<?php
                    if( $buyCheck) { echo "<a id=\"buyItemLink\" class=\"tip assignMarks\" href=\"?s=buy&id=$productID\">Satın alacağım!</a>"; }
                    if(!$buyCheck) { echo "<a id=\"buyItemLink\" class=\"blank\" style=\"cursor: default\">Yeterli $princeType'niz yok.</a>"; }
				?>
				<div class="buyInfo"><b><span id="mileageAmount"><?= $_SESSION['cash'] ?></span></b> EP'nız var.</div>
                <?php
                if ($product->priceReturn_Type == 1) { echo "<div class=\"buyInfo\">Bu ürünü satın aldığınızda <b><span id=\"mileageAmount\">$product->priceReturn_Value</span></b> EP alacaksınız!</div>"; }
                if ($product->priceReturn_Type == 2) { echo "<div class=\"buyInfo\">Bu ürünü satın aldığınızda <b><span id=\"mileageAmount\">$product->priceReturn_Value</span></b> MP alacaksınız!</div>"; }
                ?>
			</div>
			<div class="box suggestions">
				<h2>Daha fazla teklif:</h2>
				<ol id="suggestions">
					<?php
                    $products = irisApi::$Products->Products_Get_Random(7);

                    foreach ($products as $line) {
                        $pName = iFunctions::LangDict_Get($line->name);
                        $visualValue = ($line->visual_Type == 0) ? "/img/item/".$line->visual_Value.".png" : $line->visual_Value;
                        $detailUrl   = iFunctions::IsLocal() ? ("?s=detail&id=".$line->id) : ("/detail?id=".$line->id);
                        $priceAmount = $line->price_Amount;
                        if ($line->discount_Type == 1) { $priceAmount -= ($priceAmount/100) *$line->discount_Value; }
                        if ($eventDiscount != null)    { $priceAmount -= ($priceAmount/100) *$eventDiscount->value; }
					?>
					<li class="thumbnailBgSmall" style="position: relative">

						<a class="suggestion" title="<?= $pName;?>" href="<?= $detailUrl ?>">
                            <?php if ($priceAmount != $line->price_Amount) { echo "<div class=\"discountRnd\"></div>"; } ?>
							<!--suppress HtmlDeprecatedAttribute -->
                            <img alt="<?= $pName ?>" onerror="this.src='img/error.png';" src="<?= $visualValue;?>"/>
						</a>
					</li>
					<?php } ?>
				</ol>
			</div>
		</div>
	</div>
</div>

<div id="fancybox-title" style="display: block;"></div>

