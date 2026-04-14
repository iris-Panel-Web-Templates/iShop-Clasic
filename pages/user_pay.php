<?php
$couponUse  = true;
$epinUse    = true;
$paywantUse = true;
?>

<!--suppress CssUnusedSymbol -->
<style>
    .payline { width: 512px; min-width: 512px; margin-bottom: 10px; background-color: #f4f1eb; border-radius: 5px; border: 1px solid rgba(106, 2, 2, 0.35); padding-left: 5px; }

    .payline_companys { display: block; margin-top: 10px; cursor: pointer; text-align: center; }
    .payline_companys .buttonBase { display: inline-block; position: relative; width: 115px; height: 35px; margin: 5px 5px; padding: 5px; background-position: center center; background-repeat: no-repeat; background-size: contain; background-color: #e1e1e1; border: 1px solid transparent; border-radius: 5px; filter: grayscale(100%); }
    .payline_companys .buttonBase:hover { opacity: 1; filter: grayscale(0); border: 1px solid rgba(139, 0, 0, 0.3); }
    .payline_companys .buttonBase .checkImg { display: none; position: absolute; bottom: 0; left: -5px; width: 26px; height: 26px; }
    .payline_companys .buttonCoupon  { background-image: url("/img/coupon_01.png"); }
    .payline_companys .buttonEpins   { background-image: url("/img/credit_card_01.png"); }
    .payline_companys .buttonPaywant { background-image: url("/img/paywant.png"); }

    .button-83 { appearance: button; background-color: transparent; background-image: linear-gradient(to bottom, #fff, #f8eedb); border: 0 solid #e5e7eb; border-radius: .5rem; box-sizing: border-box; color: #482307; column-gap: 1rem; cursor: pointer; display: flex; font-family: ui-sans-serif,system-ui,-apple-system,system-ui,"Segoe UI",Roboto,"Helvetica Neue",Arial,"Noto Sans",sans-serif,"Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol","Noto Color Emoji"; font-size: 100%; font-weight: 700; line-height: 24px; margin: 0 auto; outline: 2px solid transparent; padding: 1rem 1.5rem; text-align: center; text-transform: none; transition: all .1s cubic-bezier(.4, 0, .2, 1); user-select: none; -webkit-user-select: none; touch-action: manipulation; box-shadow: -6px 8px 10px rgba(81,41,10,0.1),0 2px 2px rgba(81,41,10,0.2); }
    .button-83:active { background-color: #f3f4f6; box-shadow: -1px 2px 5px rgba(81,41,10,0.15),0 1px 1px rgba(81,41,10,0.15); transform: translateY(0.125rem); }
    .button-83:focus { box-shadow: rgba(72, 35, 7, .46) 0 0 0 4px, -6px 8px 10px rgba(81,41,10,0.1), 0 2px 2px rgba(81,41,10,0.2); }
</style>

<div id="container">
	<?php include("pages/etc_user.php") ?>
	<div id="mainContent">
		<h1>EP Yükleme</h1>
		<div class="dynContent" style="position:relative; background-color: #f4f1eb">
            <div class="payline payline_companys">
                <?= (!$couponUse) ? "": "<div class=\"buttonBase buttonCoupon\" onclick=\"Select_Payment(this);\"><img class=\"checkImg\" src=\"/img/check_03.png\" alt=\"Kupon\"/></div>" ?>
                <?= (!$epinUse)   ? "": "<div class=\"buttonBase buttonEpins\" onclick=\"Select_Payment(this);\"><img class=\"checkImg\" src=\"/img/check_03.png\" alt=\"Epin\"/></div>" ?>
                <?= (!$paywantUse)? "": "<div class=\"buttonBase buttonPaywant\" onclick=\"Select_Payment(this);\"><img class=\"checkImg\" src=\"/img/check_03.png\" alt=\"PayWant\"/></div>" ?>
            </div>
            <?php if ($couponUse) { include("user_pay_coupon.php"); } ?>
            <?php if ($epinUse)   { include("user_pay_epins.php"); } ?>
            <?php if ($paywantUse){ include("user_pay_paywant.php"); } ?>
		</div>
		<div class="endContent"></div>
	</div>
</div>

<!--suppress JSUnresolvedReference -->
<script type="text/javascript" language="javascript" defer="defer" title="payment_base">
    function Select_Payment(object) {
        [].forEach.call(document.querySelectorAll('.buttonBase .checkImg'), function (el) {
            el.style.display = 'none';
        });
        [].forEach.call(document.querySelectorAll('.buttonBase'), function (el) {
            el.style.filter = 'grayscale(100%)';
        });
        object.style.filter = 'grayscale(0)';
        object.getElementsByClassName("checkImg")[0].style.display = 'block';

        const buttonClass = object.className.split(" ")[1];
        if (buttonClass !== "") {
            const payline_coupons = document.getElementsByClassName('payline_coupons')[0];
            if (payline_coupons !== null && payline_coupons !== undefined) { payline_coupons.style.display = 'none'; }

            const payline_epins_list = document.getElementsByClassName('payline_epins_list')[0];
            if (payline_epins_list !== null && payline_epins_list !== undefined) { payline_epins_list.style.display = 'none'; }

            const payline_epins_run = document.getElementsByClassName('payline_epins_run')[0];
            if (payline_epins_run !== null && payline_epins_run !== undefined) { payline_epins_run.style.display = 'none'; }

            const payline_epins_iframe = document.getElementsByClassName('payline_epins_iframe')[0];
            if (payline_epins_iframe !== null && payline_epins_iframe !== undefined) { payline_epins_iframe.style.display = 'none'; }

            const payline_paywant = document.getElementsByClassName('payline_paywant')[0];
            if (payline_paywant !== null && payline_paywant !== undefined) { payline_paywant.style.display = 'none'; }

            const payline_paywant_iframe = document.getElementsByClassName('payline_paywant_iframe')[0];
            if (payline_paywant_iframe !== null && payline_paywant_iframe !== undefined) { payline_paywant_iframe.style.display = 'none'; }

        }
        if (buttonClass === "buttonCoupon")  { document.getElementsByClassName('payline_coupons')[0].style.display = 'block'; }
        if (buttonClass === "buttonEpins")   {
            document.getElementsByClassName('payline_epins_list')[0].style.display = 'block';
            if (epinID !== 0) { document.getElementsByClassName('payline_epins_run')[0].style.display = 'block'; }
        }
        if (buttonClass === "buttonPaywant") {
            document.getElementsByClassName('payline_paywant')[0].style.display = 'block';
            const iFrameSrc = document.getElementById("iPaywant").src;
            if (iFrameSrc !== "" && iFrameSrc !== window.location.toString()) { document.getElementsByClassName('payline_paywant_iframe')[0].style.display = 'block'; }
        }
    }
</script>
