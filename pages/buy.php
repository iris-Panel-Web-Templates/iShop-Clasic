<?php
    /** @noinspection DuplicatedCode */
    if (!isset($_GET['id'])) { die("Bir ürün seçmelisiniz."); }
    $productID = intval($_GET['id']);
    $product   = irisApi::$Products->Products_Detail($productID);
    $pName     = iFunctions::LangDict_Get($product->name);
    $pDesc     = iFunctions::LangDict_Get($product->desc);
    if (empty($pDesc)) { $pDesc = "<span>Ürünün açıklaması yok.</span>"; }

    $princeType  = ($product->price_Type == 0) ? "EP" : "MP";
    $buyCheck = false;
    if ($product->price_Type == 0 && $_SESSION['cash']    >= $product->price_Amount){ $buyCheck = true; }
    if ($product->price_Type == 1 && $_SESSION['mileage'] >= $product->price_Amount){ $buyCheck = true; }
?>
<div id="fancybox-content" style="border-width: 0; width: 540px; height: 500px;">
	<div style="width:540px;height:500px;overflow: hidden;position:relative;">
		<h1>Satın alma <?= $pName; ?></h1>
        <?php if (!$buyCheck) { ?>
        <div class="dynContent">
            <div id="confirmBox" class="item">
                <div class="itemDesc confirmDesc">
                    <div class="thumbnailBgSmall"><img width="63px" height="63px" src="/img/error.png" alt=""/></div>
                    <p>
                        <span class="confirmTitle"><span style="color:red;">Satın alma başarısız!</span></span>
                        <br />Yeterli Ejderha Paranız yok!
                        <br /><?= $_SESSION['cash']; ?> EP'niz var, öğenin maliyeti <?= $product->price_Amount;?> <?= $product->$princeType;?>.<br/>
                    </p>
                    <br class="clearfloat">
                </div>
            </div>
        </div>
        <?php } ?>
        <?php if ($buyCheck) {
            $accountID = $_SESSION['account_id'];
            $buyResult = irisApi::$Shopping->BuyProduct($accountID, $productID, 1);
            if ($buyResult->responseCode != 0) { ?>
            <div class="dynContent">
                <div id="confirmBox" class="item">
                    <div class="itemDesc confirmDesc">
                        <div class="thumbnailBgSmall"><img width="63px" height="63px" src="/img/error.png" alt=""/></div>
                        <p>
                            <span class="confirmTitle" style="color:red;">Satın alma başarısız!</span>
                            <br/><?= $buyResult->responseMessage ?>
                        </p>
                        <br class="clearfloat">
                    </div>
                </div>
            </div>
            <?php }
            if ($buyResult->responseCode == 0) { ?>
                <div class="dynContent">
                    <div id="confirmBox" class="item">
                        <div class="itemDesc confirmDesc">
                            <div class="thumbnailBgSmall">
                                <img width="63px" height="63px" src="/img/7227be80292ec244a17496ca9b2528.png" alt=""/>
                            </div>
                            <p>
                                <span class="confirmTitle">Başarılı satın alma</span>
                                <br/>Ürün, Nesne Market envanterinize eklendi!
                                <br/>
                                <br/><b>Sayfa 5 saniye sonra yeniden yüklenecek ve yönlendirilecektir.
                                <script> setTimeout(function(){ window.location.reload(); }, 5000); </script>
                            </p>
                            <br class="clearfloat"/>
                        </div>
                    </div>
                </div>
                <div class="hint">
                    <div class="itemDesc messageDesc">
                        <p><span class="hintTitle">Not</span><br/><br/>Eşyanızı, resimde gösterilen butonu kullanarak envanterinizden açabileceğiniz eşya pazarı deponuzda bulabilirsiniz.</p>
                        <br class="clearfloat"/>
                    </div>
                </div>
		    <?php }
        } ?>
	</div>
</div>
<div id="fancybox-title" style="display: block;"></div>
