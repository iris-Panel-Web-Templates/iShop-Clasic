<?php
    if (!isset($_GET['id'])) { die("Bir kategori seçmelisiniz.."); }
	$categoryID = intval($_GET['id']);
?>

<!--suppress CssUnusedSymbol -->
<style>
    .thumbnailBgSmall{display: table; max-height: 64px; opacity: 0.9;}
    .thumbnailBgSmall:hover { opacity: 1; }
    .item:hover .thumbnailBgSmall { opacity: 1; }
    .thumbnailBgSmall a { display: table-cell; vertical-align: middle; text-align: center; }
    .thumbnailBgSmall a img { max-height: 64px; }
    .thumbnailBgSmall .discount { position: absolute; left: 0; top: 0; background-image: url("/img/discountPercentPromoted.png"); background-repeat: no-repeat; width: 55px; height: 48px; }
</style>
<div id="container">
	<?php include("pages/etc.php") ?>
	<div id="mainContent">
		<h1><?= ($categoryID != 0) ? irisApi::$Category->CategoryBase_GetName($categoryID) : "Arama sonuçları >> '".$_POST['txtSearchKey']."'" ?> </h1>
		<div class="dynContent" style="position:relative">
            <?php
            $products = [];
            if ($_SERVER['REQUEST_METHOD'] === 'GET') { $products = irisApi::$Products->Products_Get_ByCategory($categoryID,0); }
            if ($_SERVER['REQUEST_METHOD'] === 'POST'){ $products = irisApi::$Products->Products_Search($_POST['txtSearchKey']); }
            $eventDiscount = irisApi::$Events->Get_ByType("ItemShop_Discount");

            /** @noinspection DuplicatedCode */
            foreach ($products as $line) {
                /** @noinspection DuplicatedCode */
                $pName = iFunctions::LangDict_Get($line->name);
                $pDesc = iFunctions::LangDict_Get($line->desc);
                if (empty($pDesc)) { $pDesc = "<span>Ürünün açıklaması yok.</span>"; }

                $princeType  = ($line->price_Type == 0) ? "EP" : "MP";
                $visualValue = ($line->visual_Type == 0) ? "/img/item/".$line->visual_Value.".png" : $line->visual_Value;
                $detailUrl   = iFunctions::IsLocal() ? ("?s=detail&id=".$line->id) : ("/detail?id=".$line->id);
                $priceAmount = $line->price_Amount;
                if ($line->discount_Type == 1) { $priceAmount -= ($priceAmount/100) *$line->discount_Value; }
                if ($eventDiscount != null)    { $priceAmount -= ($priceAmount/100) *$eventDiscount->value; }
                ?>
                <div class="item">
                    <div class="itemDesc">
                        <div class="thumbnailBgSmall">
                            <?php if ($priceAmount != $line->price_Amount) { echo "<div class=\"discount\"></div>"; } ?>
                            <a href="<?= $detailUrl; ?>" title="Daha fazla bilgi" class="openinformation"><img src="<?= $visualValue; ?>" alt="Daha fazla bilgi"/></a>
                        </div>

                        <p>
                            <a href="<?= $detailUrl; ?>" title="Daha fazla bilgi" class="openinformation"><span class="itemTitle"><?= $pName; ?></span></a>
                            <span class="line"></span>
                            <?= $pDesc;  ?>
                            <?= ($line->stock_Use) ? ("<br/>Kalan Stok: ".($line->stock_Count - $line->stock_Saled)." adet") : ""; ?>
                        </p>
                    </div>
                    <div class="purchaseOptionsWrapper">
                        <div class="itemPrice"><div class="priceValue"><?= $line->item_Count; ?> adet fiyatı:<span class="price">&nbsp;<?= (number_format($priceAmount)." ".$princeType); ?> </span></div></div>
                        <a href="<?= $detailUrl; ?>" title="Daha fazla bilgi" class="purchaseInfo openinformation">Detaylar</a>
                        <br class="clearfloat" />
                    </div>
                </div>
            <?php } ?>
        </div>
		<div class="endContent"></div>
	</div>
</div>

