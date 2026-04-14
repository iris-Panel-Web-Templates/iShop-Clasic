<?php

class Shopping_Class {
    // Tekil ürün satın alır.
    // Başarıda: SessionSet() ile cash/mileage güncellenir + Products_StockDown() ile yerel cache güncellenir.
    // Dönüş: object — responseCode==0 başarı | responseCode!=0 hata (yetersiz bakiye, stok yok vb.)
    public static function BuyProduct($accountID, $productID, $productCount) {
        $post_url     = irisAuthUrl . "/iShop/Shopping/BuyProduct";
        $post_data    = array(
            'AccountID'    => $accountID,
            'ProductID'    => $productID,
            'ProductCount' => $productCount,
            'Language'     => iFunctions::GetLanguage(),
            'IpAddress'    => iFunctions::GetRequestIP()
        );
        $response     = iFunctions::ApiPost($post_url, $post_data);
        if ($response->responseCode != 0) { return $response; }

        if ($response->accountInfo->accountID != 0) { irisApi::$Account->SessionSet($response->accountInfo); }
        irisApi::$Products->Products_StockDown($productID, $productCount);
        return $response;
    }
    // Paket satın alır (paket içindeki tüm ürünler tek seferde teslim edilir).
    // Başarıda: SessionSet() ile cash/mileage güncellenir + Packets_StockDown() ile yerel cache güncellenir.
    // Dönüş: object — responseCode==0 başarı | responseCode!=0 hata
    public static function BuyPacket($accountID, $packetID) {
        $post_url     = irisAuthUrl . "/iShop/Shopping/BuyPacket";
        $post_data    = array(
            'AccountID' => $accountID,
            'PacketID'  => $packetID,
            'Language'  => iFunctions::GetLanguage(),
            'IpAddress' => iFunctions::GetRequestIP()
        );
        $response     = iFunctions::ApiPost($post_url, $post_data);
        if ($response->responseCode != 0) { return $response; }

        if ($response->accountInfo->accountID != 0) { irisApi::$Account->SessionSet($response->accountInfo); }
        irisApi::$Packets->Packets_StockDown($packetID, 1);
        return $response;
    }

    // Aktif Epin (Elektronik Pin) ödeme yöntemlerini listeler; APCu cache'li.
    // Epin: Kullanıcının para yükleyebileceği ön ödemeli pin/kart sistemleri (örn: Paywant).
    // Dönüş: array — EpinProto nesneleri | [] hata durumunda
    public static function Epins_Get() {
        if (apcu_exists(APCU_PREFIX.'Epins_Time') && (apcu_fetch(APCU_PREFIX.'Epins_Time') + Epins_CacheTime > time())) {
            if (apcu_exists(APCU_PREFIX.'Epins_List')){
                iFunctions::ConsoleLog("Epins for CACHE.");
                return apcu_fetch(APCU_PREFIX.'Epins_List');
            }
        }
        apcu_store(APCU_PREFIX.'Epins_Time', time(), 3600);

        $post_url     = irisAuthUrl . "/iShop/Payment/Epins";
        $response     = iFunctions::ApiPost($post_url, []);
        if ($response->responseCode != 0) {
            iFunctions::ConsoleLog("Epins Get Error: ".$response->responseMessage);
            apcu_store(APCU_PREFIX.'Epins_List', []);
            apcu_store(APCU_PREFIX.'Epins_Time', time()-(Epins_CacheTime-5));
            return [];
        }


        apcu_store(APCU_PREFIX.'Epins_List', $response->epins, 3600);
        iFunctions::ConsoleLog("Epins for API.");
        return $response->epins;
    }

    // Hesabın alışveriş geçmişini getirir.
    // $detail = true  → her satırda ProductData ve ItemData alanları dolu gelir (daha ağır sorgu)
    // $detail = false → yalnızca temel log bilgileri gelir
    // $lineCount — döndürülecek maksimum kayıt sayısı
    // Dönüş: array — log nesneleri (productID, buyCount, buyAmount, buyDate, resultPrice, resultStatus, ...) | [] hata
    public static function Logs_Buys(int $accountID, bool $detail, int $lineCount) {
        $post_url     = irisAuthUrl . "/iShop/Shopping/Logs";
        $post_data    = array(
            'AccountID'    => $accountID,
            'Detail'       => $detail,
            'LineCount'    => $lineCount,
            'Language'     => iFunctions::GetLanguage(),
            'IpAddress'    => iFunctions::GetRequestIP()
        );
        $response     = iFunctions::ApiPost($post_url, $post_data);
        if ($response->responseCode != 0) {
            iFunctions::ConsoleLog("Logs Buys Get Error: ".$response->responseMessage);
            return [];
        }
        return $response->logsList;
    }
    // Hesabın epin/ödeme geçmişini getirir.
    // $lineCount — döndürülecek maksimum kayıt sayısı
    // Dönüş: array — ödeme log nesneleri | [] hata
    public static function Logs_Epins(int $accountID, int $lineCount) {
        $post_url     = irisAuthUrl . "/iShop/Payment/Logs";
        $post_data    = array(
            'AccountID'    => $accountID,
            'LineCount'    => $lineCount,
            'Language'     => iFunctions::GetLanguage(),
            'IpAddress'    => iFunctions::GetRequestIP()
        );
        $response     = iFunctions::ApiPost($post_url, $post_data);
        if ($response->responseCode != 0) {
            iFunctions::ConsoleLog("Logs Epins Get Error: ".$response->responseMessage);
            return [];
        }
        return $response->logsList;
    }

    // Ödeme sayfası URL'i oluşturur (Paywant entegrasyonu).
    // $paymentCompany — ödeme şirketi kodu: 0 = PayWant
    // $epinID         — 0 ise genel ödeme URL'i oluşturulur; >0 ise o epin'e özel URL
    // Dönüş: object — responseCode==0 → paymentUrl dolu | responseCode!=0 hata
    public static function CreateUrl($accountID, $paymentCompany, $epinID) {
        $post_url     = irisAuthUrl . "/iShop/Payment/CreateUrl";
        $post_data    = array(
            'AccountID'      => $accountID,
            'PaymentCompany' => $paymentCompany,
            'EpinID'         => $epinID,
            'Language'       => iFunctions::GetLanguage(),
            'IpAddress'      => iFunctions::GetRequestIP()
        );
        return iFunctions::ApiPost($post_url, $post_data);
    }
    // Kupon kodu kullanarak hesaba EP/MP yükler.
    // Başarıda SessionSet() çağrılır, bakiye anında güncellenir.
    // Dönüş: object — responseCode==0 başarı | responseCode!=0 hata (geçersiz/süresi dolmuş kupon vb.)
    public static function Coupon_Exchange($accountID, $couponCode) {
        $post_url     = irisAuthUrl . "/iShop/Payment/Coupon/Exchange";
        $post_data    = array(
            'AccountID'  => $accountID,
            'CouponCode' => $couponCode,
            'Language'   => iFunctions::GetLanguage(),
            'IpAddress'  => iFunctions::GetRequestIP()
        );
        $response     = iFunctions::ApiPost($post_url, $post_data);
        if ($response->responseCode == 0) { irisApi::$Account->SessionSet($response->accountInfo); }
        return $response;
    }


    // Hesabın EP veya MP bakiyesini manuel değiştirir (admin işlemi).
    // $cashType  — 0:Epin (EP), 1:Marka (MP)
    // $cashValue — Mevcut değer üzerine EKLENEN miktar (negatif verilebilir → düşme).
    //              Yeni bakiye DEĞİLDİR; delta (fark) değeridir!
    // $why       — Log kaydı için sebep açıklaması
    // Başarıda SessionSet() çağrılır.
    public static function ChangeCash(int $accountID,int $cashType,int $cashValue,string $why) {
        $post_url     = irisAuthUrl . "/iShop/ChangeCash";
        $post_data    = array(
            'AccountID' => $accountID,
            'CashType'  => $cashType,  // 0:Epin, 1:Marka
            'CashValue' => $cashValue, // Yeni değer değildir! Yazılan miktar var olanın üzerine eklenir.
            'Why'       => $why,
            'Language'  => iFunctions::GetLanguage(),
            'IpAddress' => iFunctions::GetRequestIP()
        );
        $response     = iFunctions::ApiPost($post_url, $post_data);
        if ($response->responseCode != 0) { return $response; }

        if ($response->accountInfo->accountID != 0) { irisApi::$Account->SessionSet($response->accountInfo); }
        return $response;
    }

}