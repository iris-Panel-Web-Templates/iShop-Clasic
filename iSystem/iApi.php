<?php
require("iApi_Setting.php");
require("iFunctions.php");
require("iApi_Account.php");
require("iApi_Category.php");
require("iApi_Products.php");
require("iApi_Packets.php");
require("iApi_Shopping.php");
require("iApi_Events.php");
require("iApi_UserAgent.php");
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }

// irisApi — Tüm API modüllerine erişim için tek giriş noktası (Facade).
// Kullanım: irisApi::$Account->Login(...)  irisApi::$Products->Products_Get()  vb.
//
// Modüller:
//   $Account   — Giriş, token ile oturum açma, oturum yenileme, session yönetimi
//   $Category  — Ürün kategorileri (Ana kategori + Alt kategori); APCu cache'li
//   $Products  — Ürün listesi, kategori/vitrin/arama filtreleri, stok yönetimi; APCu cache'li
//   $Packets   — Paket listesi (birden fazla ürün içeren paketler); APCu cache'li
//   $Shopping  — Satın alma (ürün/paket), epin listesi, alışveriş/epin logları, kupon, ödeme URL
//   $Events    — ItemShop eventleri (HappyHour, ItemSale, Wheel, Discount); APCu cache'li
//   $UserAgent — Kullanıcı IP kaydı (API taraflı rate-limit için)
class irisApi {
    public static Account_Class    $Account;
    public static Category_Class   $Category;
    public static Products_Class   $Products;
    public static Packets_Class    $Packets;
    public static Shopping_Class   $Shopping;
    public static Events_Class     $Events;
    public static UserAgent_Class  $UserAgent;
    public static function init(): void {
        self::$Account    = new Account_Class();
        self::$Category   = new Category_Class();
        self::$Products   = new Products_Class();
        self::$Packets    = new Packets_Class();
        self::$Shopping   = new Shopping_Class();
        self::$Events     = new Events_Class();
        self::$UserAgent  = new UserAgent_Class();
    }
}

// Class yapısında varsayılan değerlerin atanabilmesi için gerekli.
irisApi::init();


