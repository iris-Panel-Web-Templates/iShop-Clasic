<?php $_SESSION['csrcode_epin'] = iFunctions::Random_String(12); ?>

<!--suppress CssUnusedSymbol -->
<style>
    .payline_epins_list {
        display: none;
    }
    .payline_epins_list .epinBox{
        display: inline-block;
        position: relative;
        width: 154px;
        height: 80px;
        margin: 10px 6px;
        background-image: url("/img/toResellingItems.png");
        background-size: auto;
        background-position: center center;
        background-repeat: no-repeat;
        cursor: pointer;
    }
    .payline_epins_list .epinBox:hover { background-image: url("/img/toResellingItems-hover.png"); }
    .payline_epins_list .epinBox .epinBoxIn { padding: 10px 0 0 10px; }
    .payline_epins_list .epinBox .epinBoxIn .discount {
        width: 43px;
        height: 36px;
        background-image: url("/img/discount_01.png");
        background-repeat: no-repeat;
        top: -10px;
        right: -15px;
        position: absolute;
        color: white;
        font-weight: bold;
        font-size: 13px;
        text-align: center;
        line-height: 36px;
    }
    .payline_epins_list .epinBox .epinBoxIn .epin {
        font-weight: bold;
        font-size: 14px;
        color: #f1f1f1;
    }
    .payline_epins_list .epinBox .epinBoxIn .epin span {
        font-weight: bold;
        font-size: 12px;
        color: #c1bfbf;
        text-decoration: line-through;
        text-decoration-style: double;
    }
    .payline_epins_list .epinBox .epinBoxIn .name {
        font-weight: normal;
        font-size: 11px;
        color: #dcdcdc;
    }
    .payline_epins_list .epinBox .epinBoxIn .amount {
        position: absolute;
        bottom: 8px;
        left: 0;
        width: 100px;
        text-align: right;
        font-weight: bold;
        font-size: 16px;
        color: #f1f1f1;
    }
    .payline_epins_list .epinBox .epinBoxIn .amount span {
        font-weight: bold;
        font-size: 14px;
        color: #c1bfbf;
        text-decoration: line-through;
        text-decoration-style: double;
    }
    .payline_epins_list .epinBox .epinBoxIn .amount .sembol {
        color: #d5d5d5;
    }
    .payline_epins_list .epinBox .epinBoxIn .checkImg {
        display: none;
        position: absolute;
        bottom: 0;
        left: -5px;
        width: 26px;
        height: 26px;
    }

    .payline_epins_run {
        display: none;
        position: relative;
        height: 70px;
    }
    .payline_epins_run .payNow {
        position: absolute;
        top: 10px;
        right: 10px;
        width: 230px;
        height: 50px;
        background-image: url("/img/buy_pay_01.png");
        background-repeat: no-repeat;
        background-size: contain;
        cursor: pointer;
        transition: 0.3s;
    }
    .payline_epins_run .payNow:hover{
        -webkit-filter: brightness(110%) contrast(105%);
        filter: brightness(110%) contrast(105%);
        /*-webkit-transform: scale(1.02);*/
        /*transform: scale(1.02)*/
    }
    .payline_epins_run .payNow b{
        color: #fefe;
        font-size: 25px;
        padding: 9px 30px 0 0;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .payline_epins_run .payDesc {
        font-family: Tahoma, Verdana, sans-serif;
        color: #52100e;
        padding-top: 15px;
        padding-left: 15px;
    }

    .payline_epins_iframe {
        display: none;
        text-align: center;
        height: 290px;
        margin-bottom: 0;
    }
    .payline_epins_iframe iframe {
        display:block;
        width:512px;
        height:290px;
        border: none;
        overflow: auto;
    }
</style>

<div class="payline payline_epins_list">
    <?php
    $epins = irisApi::$Shopping->Epins_Get();
    foreach ($epins as $line) {
        $epinValue        = $line->value_Base  + $line->value_Bonus;
        $epinBonusPercent = (($line->value_Bonus / $line->value_Base) * 100)+100;


        $amountValue = $line->amount_Base - $line->amount_Discount;
        $amountPart1 = $amountValue - fmod($amountValue,1.0);
        $amountPart2 = fmod($amountValue, 1) * 100;
        ?>
        <div class="epinBox" onclick="Select_Epin(this);" data-id="<?= $line->id ?>" data-epin="<?= $epinValue ?>" data-amount="<?= $amountValue ?>" data-hh="<?= $line->value_HapyHour ?>">
            <div class="epinBoxIn">
                <div class="epin"><?=($line->value_Base==$epinValue) ? "" : ("<span>".number_format($line->value_Base)."</span>&nbsp;") ?><?= number_format($epinValue) ?></div>
                <div class="name">Ejder Parası</div>
                <div class="amount">
                    <?=($line->amount_Base==$amountValue) ? "" : ("<span>".number_format($line->amount_Base)." ₺</span>&nbsp;") ?>
                    <?= number_format($amountPart1) ?><small class="sembol">.<?= ($amountPart2>=10) ? $amountPart2 : ("0".$amountPart2) ?> ₺</small>
                </div>
                <img class="checkImg" src="/img/check_03.png" alt=""/>
                <?php if ($epinBonusPercent) { echo "<div class=\"discount\" title='".number_format($epinBonusPercent)."% daha fazla Ejder Parası'>".number_format($epinBonusPercent)."%</div>"; } ?>
            </div>
        </div>

    <?php } ?>
</div>
<div class="payline payline_epins_run">
    <div class="payDesc">
        <b id="payInfo">80 Ejder parası 11.99<small> ₺</small></b>
        <br/>
        <small>Tüm fiyatlara KDV dahildir.</small>
    </div>
    <div class="payNow" onclick='Run_Epin()'><b>Şimdi Satın Al</b></div>
</div>
<div class="payline payline_epins_iframe"><iframe id="iEpins" seamless="seamless" src=""></iframe></div>


<!--suppress JSUnresolvedReference -->
<script type="text/javascript" language="javascript" defer="defer" title="payment_epins">
    let epinID = 0, epinValue = 0, epinAmount = 0, epinHapyHour = 0;
    function Select_Epin(object) {
        [].forEach.call(document.querySelectorAll('.payline_epins_list .checkImg'), function (el) {
            el.style.display = 'none';
        });
        const epinBoxIn = object.getElementsByClassName("epinBoxIn")[0];
        epinBoxIn.getElementsByClassName("checkImg")[0].style.display = 'block';
        document.getElementsByClassName('payline_epins_run')[0].style.display = 'block';

        epinID       = object.dataset.id;
        epinValue    = Number.parseFloat(object.dataset.epin).toLocaleString("en-US",{ minimumFractionDigits: 0 });
        epinHapyHour = Number.parseFloat(object.dataset.hh);
        epinAmount   = Number.parseFloat(object.dataset.amount).toFixed(2);
        document.getElementById('payInfo').innerHTML = epinValue + (epinHapyHour <=0 ? "" : ("+<span style='color: darkred;' title='"+epinHapyHour+" EP Hapy Hour Bonus'>" + epinHapyHour +"</span>")) + " Ejder parası " + epinAmount + "<small> ₺</small>"
    }
    function Run_Epin() {
        if (epinID     <= 0) { alert("Bir epin seçin!");      return false; }
        if (epinValue  <= 0) { alert("Bir değeri tanımsız!"); return false; }
        if (epinAmount <= 0) { alert("Bir tutarı tanımsız!"); return false; }

        const xhr = new XMLHttpRequest();
        xhr.open("POST", "<?= iFunctions::IsLocal() ? "index.php?s=createurl" : "/createurl" ?>", true);
        xhr.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4) {
                console.log(xhr.response);
                const jResult = JSON.parse(xhr.response);
                if (jResult.ResponseCode === 0){
                    document.getElementsByClassName('payline_epins_list')[0].style.display = 'none';
                    document.getElementsByClassName('payline_epins_run')[0].style.display = 'none';
                    document.getElementsByClassName('payline_epins_iframe')[0].style.display = 'block';
                    document.getElementById("iEpins").src = jResult.PaymentUrl;
                }
            }
        };

        const postData = { CsrCode: '<?=$_SESSION['csrcode_epin']?>', EpinID : epinID };
        xhr.send(JSON.stringify(postData));
        return true;
    }
</script>
