<?php $_SESSION['csrcode_coupon'] = iFunctions::Random_String(12); ?>

<style>
    .payline_coupons { display: none; text-align: center; padding: 53px 0; }
    .payline_coupons .couponBox { padding: 40px; margin: 0 auto; }
    .payline_coupons .couponBox label { font-weight: bold; font-size: 13px; }
    .payline_coupons .couponBox input[type=text] { font-weight: bold; font-size: 16px; width: 250px; height: 30px; padding-left: 5px; color: dimgray; line-height: 20px; border: 1px solid #c4ad89; background-color: #fefae9; border-radius: 3px; }
    .payline_coupons .couponBox input[type=text]::placeholder { color: #cccccc; }
    .payline_coupons .couponBox .button-83 { margin-top: 10px; }
</style>


<div class="payline payline_coupons">
    <div class="couponBox">
        <label for="txtCouponCode"></label>
        <input type="text" placeholder="kupon kodu" class="type" name="txtCouponCode" id="txtCouponCode" maxlength="32" />
        <br/>
        <button class="button-83" onclick='Run_Coupon()'>Kupon Bozdur</button>
    </div>
</div>

<!--suppress JSUnresolvedReference -->
<script type="text/javascript" language="javascript" defer="defer" title="payment_coupons">
    function Run_Coupon() {
        let couponCode = document.getElementById('txtCouponCode').value;
        couponCode = couponCode.replaceAll(" ", "");
        couponCode = couponCode.replaceAll("\n", "");
        couponCode = couponCode.replaceAll("\r", "");
        couponCode = couponCode.replaceAll("\t", "");
        if (couponCode.length < 12) { alert("Kupon Kodu çok kısa!"); return false; }

        const xhr = new XMLHttpRequest();
        xhr.open("POST", "<?= iFunctions::IsLocal() ? "index.php?s=couponexchange" : "/couponexchange" ?>", true);
        xhr.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4) {
                console.log(xhr.response);
                const jResult = JSON.parse(xhr.response);
                if (jResult.ResponseCode === 0) { document.getElementsByClassName('couponBox')[0].innerHTML = '<span style="color: darkgreen;">'+jResult.ResponseMessage+'</span>'; }
                if (jResult.ResponseCode !== 0) { document.getElementsByClassName('couponBox')[0].innerHTML = '<span style="color: darkred;">'+jResult.ResponseMessage+'</span>'; }
            }
        };

        const postData = { CsrCode: '<?=$_SESSION['csrcode_coupon']?>', CouponCode : couponCode };
        xhr.send(JSON.stringify(postData));

        return true;
    }
</script>
