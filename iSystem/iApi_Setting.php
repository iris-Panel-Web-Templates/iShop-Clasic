<?php /** @noinspection PhpUnused */
    // PHP Versiyon : 8.3.22 x64 Thread Safe (TS)
    // Apcu Versiyon: 5.1.24 x64
    // File Encoding: UTF-8
    // Line Spector : CRLF == \n\r

    // - SQL ve sistem güvenliği için işlemler Api üzerinden yapılır.
    // - Api sistemi ile 800%'e kadar performans sağlandığı görüldü.
    // - SQL'e direk erişim olmadığı için sistem güvenliği sağlanır.
    // - Performans ve yönetim kolaylığı için hazır fonksiyonları kullnamanızı tavsiye ederiz!
    // - Api sistemine erişebilmek için 'irisAuthKey' kullanılır, bu anahtar bir kaç tane olabilir.
    //      Admin panel üzerinden yeni keyler eklenebilir, var olanlar kaldırılabilir.
    //      Sadece key ile erişim olmaz, sitenin çalıştığı sunucunun IP Adresine Admin panelden izin vermelisiniz.
    //      Eğer IrisPanel hosting sunucunusu kullanıyor iseniz, IP Adresi iznine gerek yok.
    //      Eğer Local'de çalışıyor iseniz, IP Adresinize panel üzerinden izin vermelisiniz. 'https://www.ipsorgu.com' sitesidnen IP Adresinizi öğrenebilirsiniz.
    //      Eğer kendi hosting sunucunuzu kullanıyor iseniz, Hosting sunucusunun IP Adresine Admin panel üzerinden izin vermelisiniz. Sunucu hizmeti aldığınız yer ile görüşüp öğrenebilirsiniz.

    // Localde çalışacaklar için indirme ve ayarlar.
    // PHP  İndir: https://windows.php.net/downloads/releases/php-8.3.22-Win32-vs16-x64.zip
    // Apcu İndir: https://downloads.php.net/~windows/pecl/releases/apcu/5.1.24/php_apcu-5.1.24-8.3-ts-vs16-x64.zip
    // php.ini Ayarlar:
    //      1) [PHP] altında ';extension=openssl' satırını bulun ve 'extension=openssl' şeklinde düzenleyin, tırnakları eklemeyin!
    //      2) [PHP] altında 'extension=zip' satırını bulun ve altına 'extension=php_apcu.dll' satırını ekleyin, tırnakları eklemeyin!
    //      3) [CLI Server] altında 'cli_server.color = On' satırını bulun ve altına 'apc.enable_cli=1' satırını ekleyin, tırnakları eklemeyin!
    //      4) [PHP] altında altında ';extension=mbstring' satırını bulun ve 'extension=mbstring' şeklinde düzenleyin, , tırnakları eklemeyin!
    //      5) [PHP] altında altında ';zend.multibyte = Off' satırını bulun ve 'zend.multibyte = On' şeklinde düzenleyin, , tırnakları eklemeyin!
    // Editör İndir: https://www.jetbrains.com/phpstorm/download/?section=windows
    //      - Soldaki listeden en son tarihli seriali kullanın!


    // Çoklu dil kullanıyor iseniz tanımı Session'da tutmalısınız. Sessiondaki adı 'language' olmalı.
    //      - Alabileceği değerler CodePage kısaltmaları içermeli ve 2 karakter olmalı!
    //      - tr,en,de,pt,cz,fr,es,hu,pl,ro,it,nl,gr,pt,ae




    // Admin Panelden tanımladığınız erişim anahtarı.
    const irisAuthKey = "8OTWUXU4HTDH75A2";

    // https://api.SiteAdınız.com  şeklinde kullanılır.
    if (str_starts_with($_SERVER['SERVER_NAME'], 'PhpStorm')) {
        //define("irisAuthUrl", "https://localhost:7107");
        define("irisAuthUrl", "https://api.tugramt2.com");
    }
    else { define("irisAuthUrl", "https://api.tugramt2.com"); }

    // APCu cache anahtarlarına önek olarak eklenir; birden fazla site aynı sunucuda çalışıyorsa
    // cache çakışmasını önler. irisAuthKey zaten site başına benzersiz olduğu için prefix olarak kullanılır.
    const APCU_PREFIX = irisAuthKey;



    const Account_RefreshTime=  30; // second (min. 15sn)
    const Category_CacheTime =  3600; // second
    const Products_CacheTime =  3600; // second
    const Epins_CacheTime    =  3600; // second
    const Events_CacheTime   =  3600; // second

    // Hata loglamasını aktif eder. Production'da true bırakın; debug sonrası false yapın.
    const ErrorLogWrite = true;

    // Hata log dosyasının tam yolu. logs/ klasörü web erişimine kapalıdır (.htaccess ile).
    define('ErrorLogFile', __DIR__ . '/../logs/error.log');

    // Browser Consoluna debug bilgilerini yazdır.
    // UYARI: Production ortamında aşağıdaki satırı yorum satırı yapın!
    // apcu_store(APCU_PREFIX.'Console_Debug_Write', true);




