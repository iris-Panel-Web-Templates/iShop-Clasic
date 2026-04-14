<?php
require("iApi.php");

// :::::: (Check Local Access) ::::::
if (!iFunctions::IsLocal() && strpos($_SERVER['REQUEST_URI'], 'iSystem/iPage_System.php') !== false){
    $response = [
        "ResponseCode"    => 0,
        "ResponseMessage" => "Sadece Local!",
        "ProcessTime"     => 0,
        "RequestURI"      => $_SERVER['REQUEST_URI'] ?? "",
        "RequestQueryPage"=> $_GET['s'] ?? ""
    ];

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit;
}

// :::::: (Check Post Method) ::::::
if($_SERVER['REQUEST_METHOD'] !== 'POST')  {
    $response = [
        "ResponseCode"    => 0,
        "ResponseMessage" => "Sadece POST istekler!",
        "ProcessTime"     => 0,
        "RequestURI"      => $_SERVER['REQUEST_URI'] ?? "",
        "RequestQueryPage"=> $_GET['s'] ?? ""
    ];

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit;
}

// :::::: (Check Post Data) ::::::
$postData = file_get_contents('php://input');
if(empty($postData)) {
    $response = [
        "ResponseCode"    => 0,
        "ResponseMessage" => "POST NULL!",
        "ProcessTime"     => 0,
        "RequestURI"      => $_SERVER['REQUEST_URI'] ?? "",
        "RequestQueryPage"=> $_GET['s'] ?? ""
    ];

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit;
}

// :::::: (Api Servis'den gelen Bilgi ve Emirler) ::::::
if($_SERVER['REQUEST_URI'] === '/localapi'  || (isset($_GET['s']) && $_GET['s'] === 'localapi')) {
    $authKey = $_SERVER["HTTP_AUTHKEY"];

    if(empty($authKey)) {
        $response = [
            "ResponseCode"    => 1001,
            "ResponseMessage" => "ShopPhp, AuthKey NULL!",
            "ProcessTime"     => 0
        ];

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }
    if($authKey !== irisAuthKey) {
        $response = [
            "ResponseCode"    => 1002,
            "ResponseMessage" => "ShopPhp, AuthKey hatalı!",
            "ProcessTime"     => 0
        ];

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }

    $postJson = json_decode($postData);
    $response = [
        "ResponseCode"    => 1003,
        "ResponseMessage" => "None",
        "ProcessTime"     => 0
    ];

    if ($postJson->ActionID ==  1){
        apcu_clear_cache();
        $response = [
            "ResponseCode"    => 0,
            "ResponseMessage" => "ShopPhp, Apcu Cache Cleared",
            "ProcessTime"     => 0
        ];
    }
    if ($postJson->ActionID ==  2){
        // NOT: session_destroy() sadece mevcut PHP sürecinin oturumunu siler.
        // Tüm kullanıcıların oturumunu silmek için session dizini manuel temizlenmelidir.
        // Bu komut yalnızca API sunucusunun kendi isteği sırasındaki session'ı sıfırlar.
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
            session_start();
        }
        $response = [
            "ResponseCode"    => 0,
            "ResponseMessage" => "ShopPhp, Current Session Destroyed",
            "ProcessTime"     => 0
        ];
    }
    if ($postJson->ActionID == 11){
        apcu_delete(APCU_PREFIX.'Category_Time');
        apcu_delete(APCU_PREFIX.'Category_Base');
        apcu_delete(APCU_PREFIX.'Category_Subs');
        apcu_delete(APCU_PREFIX.'Events_Time');
        apcu_delete(APCU_PREFIX.'Events_List');
        apcu_delete(APCU_PREFIX.'Packets_Time');
        apcu_delete(APCU_PREFIX.'Packets_Scan');
        apcu_delete(APCU_PREFIX.'Packets_List');
        apcu_delete(APCU_PREFIX.'Products_Time');
        apcu_delete(APCU_PREFIX.'Products_Scan');
        apcu_delete(APCU_PREFIX.'Products_List');
        apcu_delete(APCU_PREFIX.'Epins_Time');
        apcu_delete(APCU_PREFIX.'Epins_List');
        $response = [
            "ResponseCode"    => 0,
            "ResponseMessage" => "ShopPhp, Category & Products & Epins Cleared",
            "ProcessTime"     => 0
        ];
    }

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit;
}



