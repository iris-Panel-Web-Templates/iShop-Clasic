<?php

class Account_Class {
    // Kullanıcı adı ve şifre ile iShop oturumu açar.
    // $pinCode — PinCode kullanımı kapalıysa boş string gönderin: ""
    // Başarıda SessionSet() çağrılır; sonraki isteklerde $_SESSION üzerinden hesap bilgisine erişilir.
    public static function Login($loginName, $loginPass, $pinCode): mixed {
        /** @noinspection DuplicatedCode */
        $post_url     = irisAuthUrl . "/iShop/Login";
        $post_data    = array(
            'LoginName' => $loginName,
            'LoginPass' => $loginPass,
            'PinCode'   => $pinCode,
            'Language'  => iFunctions::GetLanguage(),
            'IpAddress' => iFunctions::GetRequestIP()
        );
        $response     = iFunctions::ApiPost($post_url, $post_data);
        if ($response->responseCode != 0) { return $response; }

        session_regenerate_id(true); // Session fixation koruması: yeni ID üret, eskiyi sil
        self::SessionSet($response->accountInfo);
        return $response;
        //int            ResponseCode
        //string         ResponseMessage
        //long           ProcessTime
        //AccountProto   AccountInfo
    }
    // ShopToken ile sessionsız oturum açar (örn: URL parametresi üzerinden geçiş).
    // $loginToken — AccountProto.shopToken değeri (AES-CBC128 şifreli hex string).
    //               Format: Hex( AES-CBC128( "accountID|ipAddress|timestamp" ) )
    //               API tarafı çözümleyip doğrular; IP eşleşmezse hata döner.
    public static function Login_ByToken($loginToken) {
        /** @noinspection DuplicatedCode */
        $post_url     = irisAuthUrl . "/iShop/LoginToken";
        $post_data    = array(
            'Token'     => $loginToken,
            'Language'  => iFunctions::GetLanguage(),
            'IpAddress' => iFunctions::GetRequestIP()
        );
        $response     = iFunctions::ApiPost($post_url, $post_data);
        if ($response->responseCode != 0){
            iFunctions::ConsoleLog("Account Refresh Error: ".$response->responseMessage);
            return $response;
        }

        session_regenerate_id(true); // Session fixation koruması: yeni ID üret, eskiyi sil
        self::SessionSet($response->accountInfo);
        return $response;
        //int            ResponseCode
        //string         ResponseMessage
        //long           ProcessTime
        //AccountProto   AccountInfo
    }
    // Oturumdaki hesap bilgisini API'den yeniler ve SessionSet() ile günceller.
    // $accountID = 0 → session'daki mevcut account_id kullanılır ve zaman kontrolü yapılır
    //                  (timeStamp + Account_RefreshTime > now ise API çağrısı atlanır).
    // $accountID > 0 → belirtilen hesap zorla yenilenir, zaman kontrolü yapılmaz.
    // Dönüş: true → başarılı veya zaman kontrolü geçildi | false → oturum yok veya API hatası
    public static function Account_Refresh($accountID=0): bool {
        global $_SESSION;
        $timeCheck = false;
        if ($accountID == 0) { $accountID = iFunctions::GetAccountID(); $timeCheck = true; }
        if ($accountID == 0) { return false; }
        if ($timeCheck) {
            $timeStamp = $_SESSION['time_stamp'] * 1;
            if ($timeStamp+Account_RefreshTime > time()) { return true; }
        }

        $post_url     = irisAuthUrl . "/Account/Get";
        $post_data    = array(
            'AccountID' => $accountID,
            'Detail'    => false,
            'Language'  => iFunctions::GetLanguage()
        );
        $response     = iFunctions::ApiPost($post_url, $post_data);
        if ($response->responseCode != 0){
            iFunctions::ConsoleLog("Account Refresh Error: ".$response->responseMessage);
            return false;
        }
        self::SessionSet($response->accountInfo);
        iFunctions::ConsoleLog("Account Refreshed.");
        return true;
        //int            ResponseCode
        //string         ResponseMessage
        //long           ProcessTime
        //AccountProto   AccountInfo
    }
    // API'den dönen AccountProto nesnesini $_SESSION'a yazar.
    // Login(), Login_ByToken(), Account_Refresh() ve başarılı alışveriş sonrası otomatik çağrılır.
    // Doğrudan çağrılması gerekmez; sadece cash/mileage güncellemesi gerektiğinde kullanılabilir.
    public static function SessionSet($accountInfo): void {
        global /** @noinspection DuplicatedCode */
        $_SESSION;
        $_SESSION['account_id']   = $accountInfo->accountID;
        $_SESSION['account_name'] = $accountInfo->accountName;
        $_SESSION['login_name']   = $accountInfo->loginName;
        $_SESSION['login_pass']   = $accountInfo->password;
        $_SESSION['cash']         = $accountInfo->cash;
        $_SESSION['mileage']      = $accountInfo->mileage;
        $_SESSION['status']       = $accountInfo->status;
        $_SESSION['phone']        = $accountInfo->phone;
        $_SESSION['email']        = $accountInfo->email;
        $_SESSION['delete_code']  = $accountInfo->deleteCode;
        $_SESSION['pin_code']     = $accountInfo->pinCode;
        $_SESSION['players']      = $accountInfo->players;

        $_SESSION['shop_token']   = $accountInfo->shopToken;
        $_SESSION['time_stamp']   = $accountInfo->timeStamp;
        $_SESSION['cache_time']   = $accountInfo->cacheTime;
    }
}


// :::::: (AccountProto yapısı — JSON camelCase döner) ::::::
    //int               accountID
    //string            accountName
    //string            loginName
    //string            phone
    //string            email
    //string            status      — "OK", "ATC" (aktivasyon bekliyor), "BAN"
    //string            language
    //string            passwordMd5
    //string            password
    //string            pinCode     — 6 haneli güvenlik kodu
    //string            deleteCode  — 7 haneli karakter silme kodu
    //string            shopToken   — iShop oturum token'ı (AES-CBC128 şifreli)
    //int               cash        — EP (Ejderha Parası)
    //int               mileage     — MP (Marka Parası)
    //List<PlayerProto> players
    //long              timeStamp   — Unix zaman damgası (Account_Refresh zamanlama için)
    //DateTime          cacheTime
