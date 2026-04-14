<?php $_SESSION['csrcode_paywant'] = iFunctions::Random_String(12); ?>

<!--suppress CssUnusedSymbol -->
<style>
    .payline_paywant {
        display: none;
        text-align: center;
        margin-bottom: 0;
        padding: 117px 0;
    }
    .payline_paywant_iframe {
        display: none;
        text-align: center;
        height: 290px;
        margin-bottom: 0;
    }
    .payline_paywant_iframe iframe {
        display:block;
        width:512px;
        height:290px;
        border: none;
        overflow: auto;
    }
</style>

<div class="payline payline_paywant">
    <button id="btnPaywantGet" class="button-83" onclick="Run_PayWant()">Ödeme Sayfasını görüntüle</button>
</div>
<div class="payline payline_paywant_iframe"><iframe id="iPaywant" seamless="seamless" src=""></iframe></div>

<!--suppress JSUnresolvedReference -->
<script type="text/javascript" language="javascript" defer="defer" title="payment_base">
    function Run_PayWant() {
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "<?= iFunctions::IsLocal() ? "index.php?s=createurl" : "/createurl" ?>", true);
        xhr.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4) {
                console.log(xhr.response);
                const jResult = JSON.parse(xhr.response);
                if (jResult.ResponseCode === 0){
                    document.getElementsByClassName('payline_paywant')[0].style.display = 'none';
                    document.getElementsByClassName('payline_paywant_iframe')[0].style.display = 'block';
                    document.getElementById("iPaywant").src = jResult.PaymentUrl;
                }
                if (jResult.ResponseCode !== 0){
                    alert(jResult.ResponseMessage);
                }
            }
        };

        const postData = { CsrCode: '<?=$_SESSION['csrcode_epin']?>', EpinID : 0 };
        xhr.send(JSON.stringify(postData));
        return true;
    }
</script>
