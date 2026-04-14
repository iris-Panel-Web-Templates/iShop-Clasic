# irisPanel — iShop Clasic

PHP tabanlı **Metin2 oyun sunucusu** item shop sistemi.  
[irisPanel](https://irispanel.com) altyapısı ile çalışır; tüm veri işlemleri SQL'e doğrudan bağlantı olmadan merkezi bir **REST API** üzerinden yürütülür.

---

## İçindekiler

- [Projeler](#projeler)
- [Mimari](#mimari)
- [Gereksinimler](#gereksinimler)
- [Kurulum](#kurulum)
- [Yapılandırma](#yapılandırma)
- [Proje Yapısı](#proje-yapısı)
  - [iShop\_Clasic](#ishop_clasic)
- [API Modülleri](#api-modülleri)
- [Güvenlik](#güvenlik)
- [Geliştirici Araçları](#geliştirici-araçları)

---

## Projeler

| Proje | Açıklama |
|---|---|
| **iShop_Clasic** | Item shop — kategori/ürün/paket satışı, epin, kupon, ödeme entegrasyonu |

Her iki proje de bağımsız olarak çalışabilir; aynı irisPanel API sunucusunu paylaşır.

---

## Mimari

```
Tarayıcı
   │
   ▼
index.php  (oturum başlatma, güvenlik başlıkları, sayfa yönlendirme)
   │
   ├── iSystem/iApi.php  ◄── Facade: tüm API modüllerinin tek giriş noktası
   │       │
   │       ├── iApi_Setting.php    ─ Yapılandırma sabitleri
   │       ├── iFunctions.php      ─ Yardımcı araçlar (IP, dil, oturum, cURL)
   │       └── iApi_*.php          ─ Modüller (Account, Rank, News, Shop…)
   │
   └── main/*.php / pages/*.php  ◄── Sayfa şablonları (?s= parametresiyle yüklenir)
```

**Temel prensipler:**

- **Doğrudan veritabanı bağlantısı yok.** Tüm okuma/yazma işlemleri irisPanel API'si üzerinden cURL ile yapılır.
- **APCu önbellekleme.** Sık okunan veriler (sıralama, istatistik, ürünler) bellek içinde tutulur; her istek için API çağrısı yapılmaz.
- **Temiz URL.** `mod_rewrite` ile `/sayfa` → `?s=sayfa` dönüşümü; yerel geliştirmede otomatik olarak sorgu parametresi formatına geçilir.
- **Facade deseni.** `irisApi::$Modül->Metod()` söz dizimi ile tüm modüllere merkezi erişim.

---

## Gereksinimler

| Bileşen | Sürüm |
|---|---|
| PHP | 8.3+ (Thread Safe) |
| APCu eklentisi | 5.1.24+ |
| Apache | mod_rewrite etkin |
| OpenSSL | php.ini'de etkin |
| mbstring | php.ini'de etkin |

**php.ini için gerekli ayarlar:**

```ini
extension=openssl
extension=php_apcu.dll
extension=mbstring
zend.multibyte = On
apc.enable_cli = 1          ; CLI test için
```

---

## Kurulum

1. Depoyu sunucuya klonlayın:
   ```bash
   git clone https://github.com/kullanici/irisPanel_WebPhp.git
   ```

2. Her proje için `iSystem/iApi_Setting.php` dosyasını yapılandırın (bkz. [Yapılandırma](#yapılandırma)).

3. Apache VirtualHost ayarlarında her proje için `DocumentRoot`'u ilgili klasöre yönlendirin.

4. `logs/` klasörünün PHP tarafından yazılabilir olduğunu doğrulayın (klasör yoksa otomatik oluşturulur).

---

## Yapılandırma

Her projenin `iSystem/iApi_Setting.php` dosyası bağımsız olarak yapılandırılır:

```php
// Admin Panel → Sistem Yönetimi → Web Ayarları → Web Api Ayarları
const irisAuthKey = "BURAYA_API_ANAHTARINIZI_YAZIN";

// API sunucusu adresi
define("irisAuthUrl", "https://api.siteniz.com");

// Varsayılan dil (oturum ve tarayıcı dili yoksa)
const Default_Language = "tr"; // tr, en, de, fr, es, pl, ro, it, pt, cz, hu, nl, gr, ae

// Hata loglama (logs/error.log dosyasına yazar)
const ErrorLogWrite = true;
```

> **IP İzni:** API sunucusuna erişebilmek için web sunucunuzun IP adresini Admin Panel → Web Api Ayarları bölümünden whitelist'e eklemeniz gerekir.

---

## Proje Yapısı

### iShop_Clasic

```
iShop_Clasic/
├── index.php                  # Ana giriş noktası
├── .htaccess                  # Temiz URL yönlendirme
├── frame.php                  # iframe sarmalayıcı
│
├── iSystem/                   # API ve sistem katmanı
│   ├── iApi.php               # Facade
│   ├── iApi_Setting.php       # Yapılandırma
│   ├── iFunctions.php         # Yardımcı fonksiyonlar
│   ├── iApi_Account.php       # Oturum yönetimi
│   ├── iApi_Category.php      # Kategoriler & alt kategoriler
│   ├── iApi_Products.php      # Ürün listesi & arama
│   ├── iApi_Packets.php       # İndirimli paketler
│   ├── iApi_Shopping.php      # Satın alma, epin, kupon, loglar
│   ├── iApi_Events.php        # HappyHour, ItemSale, Çark-ı Felek
│   └── iApi_UserAgent.php     # IP loglama
│
├── pages/                     # Sayfa şablonları
│   ├── home.php               # Vitrin / öne çıkan ürünler
│   ├── category.php           # Kategori & arama sonuçları
│   ├── detail.php             # Ürün detayı & satın alma
│   ├── buy.php                # Satın alma onay modalı
│   ├── user_pay.php           # Ödeme yöntemi seçimi
│   ├── user_pay_epins.php     # Epin / ön ödemeli kart
│   ├── user_pay_paywant.php   # PayWant ödeme entegrasyonu
│   ├── user_pay_coupon.php    # Kupon kodu kullanımı
│   ├── user_buylog.php        # Satın alma geçmişi
│   ├── user_paylog.php        # Ödeme geçmişi
│   └── user_faq.php           # Sıkça sorulan sorular
│
├── css/                       # Shop stil dosyaları
├── img/                       # Ürün görselleri & UI ikonları
├── js/                        # Shop JavaScript dosyaları
└── logs/                      # Hata logları (.htaccess ile web erişimi engelli)
```

---

## API Modülleri

### Web_Clasic

| Modül | Erişim | Açıklama |
|---|---|---|
| Account | `irisApi::$Account` | Giriş, kayıt, şifre sıfırlama, token doğrulama |
| RankList | `irisApi::$RankList` | Oyuncu & lonca sıralamaları (top 10 / top 100) |
| Statistics | `irisApi::$Statistics` | Anlık online sayısı, imparatorluk & karakter istatistikleri |
| News | `irisApi::$News` | Dile göre filtrelenmiş haber listesi |
| Events | `irisApi::$Events` | Etkinlik listesi & aktiflik kontrolü |
| Downloads | `irisApi::$Downloads` | Oyun istemcisi indirme linkleri |
| Ticket | `irisApi::$Ticket` | Destek talebi oluşturma & listeleme |
| BansList | `irisApi::$BansList` | Yasaklı oyuncu listesi |
| UserAgent | `irisApi::$UserAgent` | Tarayıcı saat dilimi, referans kodu takibi |

### iShop_Clasic

| Modül | Erişim | Açıklama |
|---|---|---|
| Account | `irisApi::$Account` | Shop oturum yönetimi, token girişi |
| Category | `irisApi::$Category` | Ana kategoriler & alt kategoriler |
| Products | `irisApi::$Products` | Ürün listesi, arama, vitrin, stok kontrolü |
| Packets | `irisApi::$Packets` | İndirimli paket listesi |
| Shopping | `irisApi::$Shopping` | Satın alma, epin kullanımı, kupon, cash & mileage logları |
| Events | `irisApi::$Events` | HappyHour, ItemSale, Çark-ı Felek & sezonsal indirimler |

---

## Güvenlik

- **Doğrudan SQL erişimi yok** — tüm işlemler API üzerinden, iki katmanlı doğrulama ile (AuthKey + IP whitelist).
- **Session fixation koruması** — giriş sonrası `session_regenerate_id(true)` ile oturum ID'si yenilenir.
- **Güvenli çerezler** — `HttpOnly`, `SameSite=Strict`, `Secure` bayrakları etkin.
- **Cloudflare desteği** — `CF-Connecting-IP` başlığı otomatik olarak algılanır; gerçek istemci IP'si doğru tespit edilir.
- **SSL doğrulama** — production ortamında cURL sertifika doğrulaması zorunludur; yalnızca yerel geliştirmede devre dışı bırakılır.
- **Hata loglama** — API hataları `logs/error.log` dosyasına yazılır; log klasörüne `.htaccess` ile web erişimi engellenir.
- **Hassas veri maskeleme** — log kayıtlarında parola, PIN, token gibi alanlar `***` olarak maskelenir.
- **XSS koruması** — `ConsoleLog()` çıktıları `json_encode()` ile güvenli biçimde kaçırılır.

---

## Geliştirici Araçları

Her iki proje de `/developer/` adresinde bir geliştirici paneli içerir.

**Erişim:** Yerel geliştirme ortamında (`localhost` / `127.0.0.1` / PhpStorm) veya API'den `isDeveloper: true` döndüğünde görüntülenir.

**Özellikler:**
- APCu durumu & PHP versiyon bilgisi
- API sunucusu bağlantı & başlatma durumu
- Oturum sıfırlama (CSRF korumalı)
- APCu önbellek temizleme (CSRF korumalı)
- **Son 50 API hatası** — URL, POST verisi (maskelenmiş), hata kodu ve mesajı

**Hata loglama etkin/devre dışı bırakma (`iApi_Setting.php`):**
```php
const ErrorLogWrite = true;   // true: logla | false: loglama
```
