<?php

class Events_Class {
    public static function Get_List(): array {
        if (apcu_exists(APCU_PREFIX.'Events_Time') && (apcu_fetch(APCU_PREFIX.'Events_Time') + Events_CacheTime > time())) {
            if (apcu_exists(APCU_PREFIX.'Events_List')){
                iFunctions::ConsoleLog("Events for CACHE.");
                return apcu_fetch(APCU_PREFIX.'Events_List');
            }
        }
        apcu_store(APCU_PREFIX.'Events_Time', time(), 3600);

        $post_url     = irisAuthUrl . "/iShop/Events";
        $response     = iFunctions::ApiPost($post_url, []);
        if ($response->responseCode != 0) {
            iFunctions::ConsoleLog("Events Get Error: ".$response->responseMessage);
            apcu_store(APCU_PREFIX.'Events_List', []);
            apcu_store(APCU_PREFIX.'Events_Time', time()-(Events_CacheTime-5));
            return [];
        }

        apcu_store(APCU_PREFIX.'Events_List', $response->events, 3600);
        iFunctions::ConsoleLog("Events for API.");
        return $response->events;
    }
    public static function Get_ByType(string $eventType) {
        $eList = self::Get_List();
        foreach ($eList as $line) {
            if ($line->type == $eventType) {
                return $line;
            }
        }

        return null;
    }
    // Belirtilen event ID'ye sahip event aktif mi kontrol eder.
    // NOT: Event listede bulunamazsa true döner — yani "kısıtlama yok" anlamına gelir.
    //      Ürün/paket üzerindeki event_ID alanı 0 olmayan bir event'e işaret ediyorsa
    //      ve o event silinmişse/bitmişse false döner; henüz listeye girmemişse true döner.
    // dateStart_Unix ve dateFinish_Unix cache'den anlık time() ile karşılaştırılır
    // (API'nin activeNow alanı kullanılmaz — cache süresi boyunca bayat kalabilir).
    public static function GetStatus_ByID(int $eventID): bool {
        $eList = self::Get_List();
        foreach ($eList as $line) {
            if ($line->id != $eventID)           { continue; }
            if ($line->dateStart_Unix  > time()) { return false; }
            if ($line->dateFinish_Unix < time()) { return false; }
            return true;
        }

        return true; // Listede yok → kısıtlama yok sayılır
    }
    // Belirtilen type'a sahip event aktif mi kontrol eder.
    // NOT: Type listede bulunamazsa true döner — yani "kısıtlama yok" anlamına gelir.
    //      event_Code → EventProto.type karşılaştırması yapar.
    //      Bilinen type değerleri: ItemShop_HapyHour | ItemShop_ItemSale | ItemShop_Wheel | ItemShop_Discount
    public static function GetStatus_ByType(string $eventType): bool {
        $eList = self::Get_List();
        foreach ($eList as $line) {
            if ($line->type != $eventType)       { continue; }
            if ($line->dateStart_Unix  > time()) { return false; }
            if ($line->dateFinish_Unix < time()) { return false; }
            return true;
        }

        return true; // Listede yok → kısıtlama yok sayılır
    }
}

// PS: Sadece itemShop eventleri listelenir.
// EventProto içeriği
    //int      id
    //string   name
    //string   type           (ItemShop_HapyHour, ItemShop_ItemSale, ItemShop_Wheel, ItemShop_Discount)
    //DateTime dateStart
    //long     dateStart_Unix
    //DateTime dateFinish
    //long     dateFinish_Unix
    //double   value
    //bool     activeNow