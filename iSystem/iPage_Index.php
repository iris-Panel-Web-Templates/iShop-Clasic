<?php

if($_SERVER['REQUEST_METHOD'] == 'POST')  {
    // :::::: (Check Post Data) ::::::
    $postData = file_get_contents('php://input');
    if(empty($postData)) {
        $response = [
            "ResponseCode"    => 1000,
            "ResponseMessage" => "POST NULL!",
            "ProcessTime"     => 0,
            "RequestURI"      => $_SERVER['REQUEST_URI'] ?? "",
            "RequestQueryPage"=> $_GET['s'] ?? ""
        ];

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }

    // :::::: (Kullanıcının Browser zaman dilimini kaydet) ::::::
    if($_SERVER['REQUEST_URI'] === '/timezoneset'    || (isset($_GET['s']) && $_GET['s'] === 'timezoneset')){
        $postJson = json_decode($postData);
        if (isset($postJson->timezone) && in_array($postJson->timezone, DateTimeZone::listIdentifiers())) {
            global $_SESSION;
            $_SESSION['user_timezone']=$postJson->timezone;
            setcookie('user_timezone', $postJson->timezone, time() + 3600 * 24 * 30, "/");

            $response = [
                "ResponseCode"    => 0,
                "ResponseMessage" => "Time zone seted. Zone: ".$postJson->timezone,
                "ProcessTime"     => 0,
                "RequestURI"      => $_SERVER['REQUEST_URI'] ?? "",
                "RequestQueryPage"=> $_GET['s'] ?? ""
            ];
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
        }
    }

    // :::::: (Ödeme İşlemleri) ::::::
    if($_SERVER['REQUEST_URI'] === '/createurl'      || (isset($_GET['s']) && $_GET['s'] === 'createurl')){
        /** @noinspection DuplicatedCode */
        if (!isset($_SESSION['account_id']))   {
            $response = [
                "ResponseCode"    => 1001,
                "ResponseMessage" => "Üye girişi yapın!",
                "ProcessTime"     => 0
            ];

            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
        }
        if (!isset($_SESSION['csrcode_epin'])) {
            $response = [
                "ResponseCode"    => 1001,
                "ResponseMessage" => "CSR Code tanımsız!",
                "ProcessTime"     => 0
            ];

            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
        }

        $postJson = json_decode($postData);
        if ($_SESSION['csrcode_epin'] !== $postJson->CsrCode) {
            $response = [
                "ResponseCode"    => 1001,
                "ResponseMessage" => "CSR Code geçersiz!",
                "ProcessTime"     => 0
            ];

            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
        }


        $cResult = irisApi::$Shopping->CreateUrl($_SESSION['account_id'], 2, $postJson->EpinID);

        $response = [
            "ResponseCode"    => $cResult->responseCode,
            "ResponseMessage" => $cResult->responseMessage,
            "ProcessTime"     => $cResult->processTime,
            "PaymentUrl"      => $cResult->responseCode === 0 ? $cResult->paymentUrl: ""
        ];
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }
    if($_SERVER['REQUEST_URI'] === '/couponexchange' || (isset($_GET['s']) && $_GET['s'] === 'couponexchange')){
        /** @noinspection DuplicatedCode */
        if (!isset($_SESSION['account_id']))   {
            $response = [
                "ResponseCode"    => 1001,
                "ResponseMessage" => "Üye girişi yapın!",
                "ProcessTime"     => 0
            ];

            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
        }
        if (!isset($_SESSION['csrcode_coupon'])) {
            $response = [
                "ResponseCode"    => 1001,
                "ResponseMessage" => "CSR Code tanımsız!",
                "ProcessTime"     => 0
            ];

            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
        }

        $postJson = json_decode($postData);
        if ($_SESSION['csrcode_coupon'] !== $postJson->CsrCode) {
            $response = [
                "ResponseCode"    => 1001,
                "ResponseMessage" => "CSR Code geçersiz!",
                "ProcessTime"     => 0
            ];

            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
        }
        if ($postJson->CouponCode == null || $postJson->CouponCode == '') {
            $response = [
                "ResponseCode"    => 1001,
                "ResponseMessage" => "Kopon kodu eksik!",
                "ProcessTime"     => 0
            ];

            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
        }

        $cResult = irisApi::$Shopping->Coupon_Exchange($_SESSION['account_id'], $postJson->CouponCode);

        $response = [
            "ResponseCode"    => $cResult->responseCode,
            "ResponseMessage" => $cResult->responseMessage,
            "ProcessTime"     => $cResult->processTime,
        ];
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }
}

if($_SERVER['REQUEST_METHOD'] === 'GET') {
    // :::::: (Login Token) ::::::
    if(isset($_GET['ltoken'])) {
        $loginToken = $_GET['ltoken'];
        $lResult    = irisApi::$Account->Login_ByToken($loginToken);
        if ($lResult->responseCode == 0) { header("Location: ".(iFunctions::IsLocal()?"?s=home":"/home")); exit; }

        $_SESSION['last_error'] = $lResult->responseMessage;
        if (!isset($_GET['s']) && $_SERVER['REQUEST_URI'] !== '/login') { header("Location: ".(iFunctions::IsLocal()?"?s=login":"/login")); exit; }
        if ( isset($_GET['s']) && $_GET['s'] != "login")                { header("Location: ".(iFunctions::IsLocal()?"?s=login":"/login")); exit; }
    }

    // :::::: (Logout) ::::::
    if ($_SERVER['REQUEST_URI']         === '/logout'){ session_destroy(); header("location: ".(iFunctions::IsLocal() ? "?s=login" : "/login")); exit; }
    if (isset($_GET['s']) && $_GET['s'] === 'logout') { session_destroy(); header("location: ".(iFunctions::IsLocal() ? "?s=login" : "/login")); exit; }

    // :::::: (AccountID Check) ::::::
    if (!isset($_SESSION['account_id'])) {
        $goLogin = true;
        if ($_SERVER['REQUEST_URI'] === '/login')        { $goLogin = false; }
        if (isset($_GET['s']) && $_GET['s'] === 'login') { $goLogin = false; }
        if ($goLogin) { header("location: ".(iFunctions::IsLocal() ? "?s=login" : "/login")); exit; }
    }
    if ( isset($_SESSION['account_id'])) {
        irisApi::$Account->Account_Refresh();
        if (isset($_GET['s']) && $_GET['s'] === 'login') { header("location: ".(iFunctions::IsLocal() ? "?s=home" : "/home")); exit; }
        if ($_SERVER['REQUEST_URI'] === '/login')        { header("location: ".(iFunctions::IsLocal() ? "?s=home" : "/home")); exit; }
    }
}

