<?php

class Packets_Class {
    private static function Packets_Get_Api() {
        if (apcu_exists(APCU_PREFIX.'Packets_Time') && (apcu_fetch(APCU_PREFIX.'Packets_Time') + Products_CacheTime > time())) { // Products_CacheTime intentional (aynı değer, ayrı sabit yok)
            if (apcu_exists(APCU_PREFIX.'Packets_List')){
                iFunctions::ConsoleLog("Packets for CACHE.");
                return apcu_fetch(APCU_PREFIX.'Packets_List');
            }
        }
        apcu_store(APCU_PREFIX.'Packets_Time', time(), 3600);

        $post_url     = irisAuthUrl . "/iShop/Packets";
        $response     = iFunctions::ApiPost($post_url, []);
        if ($response->responseCode != 0) {
            iFunctions::ConsoleLog("Packets Get Error: ".$response->responseMessage);
            apcu_store(APCU_PREFIX.'Packets_List', []);
            apcu_store(APCU_PREFIX.'Packets_Time', time()-(Products_CacheTime-5));
            return [];
        }

        apcu_store(APCU_PREFIX.'Packets_Scan', true, 3600*24);
        apcu_store(APCU_PREFIX.'Packets_List', $response->packets, 3600);
        iFunctions::ConsoleLog("Packets for API.");
        return $response->packets;
    }
    public  static function Packets_Get(): array {
        $products = self::Packets_Get_Api();
        if (apcu_exists(APCU_PREFIX.'Packets_Scan') && apcu_fetch(APCU_PREFIX.'Packets_Scan') === false) { return $products; }

        /** @noinspection DuplicatedCode */
        $scanPacket  = false;
        $dateNow     = time();
        $lProducts   = [];
        foreach ($products as $line) {
            if ($line->stock_Use)          {
                if ($line->stock_Saled >= $line->stock_Count) { /*print_r("SKIP SALED! Now: ".$line->stock_Saled." >= ".$line->stock_Count."<br/>");*/ continue; }
                $scanPacket = true;
            }
            if ($line->scheduledSale_Use)  {
                if ($dateNow < $line->scheduledSale_Start_Unix)  { /*print_r("SKIP Start! Now: ".($dateNow-1751000000).", Start: ".($line->scheduledSale_Start_Unix-1751000000)."<br/>");*/ continue; }
                if ($dateNow >=$line->scheduledSale_Finish_Unix) { /*print_r("SKIP FINISH! Now: ".($dateNow-1751000000).", Start: ".($line->scheduledSale_Finish_Unix-1751000000)."<br/>");*/ continue; }
                $scanPacket = true;
            }
            if ($line->event_ID   != 0  && !irisApi::$Events->GetStatus_ByID($line->event_ID))     { continue; }
            if ($line->event_Code != "" && !irisApi::$Events->GetStatus_ByType($line->event_Code)) { continue; }

            $lProducts[] = $line;
        }
        apcu_store(APCU_PREFIX.'Packets_Scan', $scanPacket, 3600*24);
        apcu_store(APCU_PREFIX.'Packets_List', $lProducts, 3600);
        return $lProducts;
    }
    public  static function Packet_Detail(int $packetID): mixed {
        $packets  = self::Packets_Get();
        foreach ($packets as $line) {
            if ($line->id == $packetID) { return $line; }
        }
        return [];
    }
    public  static function Packets_StockDown(int $packetID, int $packetCount): void {
        $packets = self::Packets_Get();

        foreach ($packets as $line) {
            if ($line->id != $packetID) { continue; }
            if (!$line->stock_Use)      { return; }
            $line->stock_Saled+=$packetCount;
            break;
        }
        apcu_store(APCU_PREFIX.'Packets_List', $packets, 3600);
    }
}

// PacketProto içeriği
    //int                        id
    //string                     param
    //string                     visual_Value
    //Dictionary<string, string> name
    //Dictionary<string, string> desc
    //PriceTypes                 price_Type      (0:Epin, 1:Marka)
    //int                        price_Amount
    //ReturnTypes                priceReturn_Type (0:None, 1:Epin, 2:Marka)
    //double                     priceReturn_Value
    //int                        price_Discount  (indirim tutarı)
    //int                        event_ID
    //string                     event_Code
    //bool                       stock_Use
    //int                        stock_Count
    //int                        stock_Saled
    //bool                       scheduledSale_Use
    //DateTime                   scheduledSale_Start
    //long                       scheduledSale_Start_Unix
    //DateTime                   scheduledSale_Finish
    //long                       scheduledSale_Finish_Unix
    //List<ProductProto>         products
