<?php

class Products_Class {
    private static function Products_Get_Api() {
        if (apcu_exists(APCU_PREFIX.'Products_Time') && (apcu_fetch(APCU_PREFIX.'Products_Time') + Products_CacheTime > time())) {
            if (apcu_exists(APCU_PREFIX.'Products_List')){
                iFunctions::ConsoleLog("Products for CACHE.");
                return apcu_fetch(APCU_PREFIX.'Products_List');
            }
        }
        apcu_store(APCU_PREFIX.'Products_Time', time(), 3600);

        $post_url     = irisAuthUrl . "/iShop/Products";
        $response     = iFunctions::ApiPost($post_url, []);
        if ($response->responseCode != 0) {
            iFunctions::ConsoleLog("Products Get Error: ".$response->responseMessage);
            apcu_store(APCU_PREFIX.'Products_List', []);
            apcu_store(APCU_PREFIX.'Products_Time', time()-(Products_CacheTime-5));
            return [];
        }

        apcu_store(APCU_PREFIX.'Products_Scan', true, 3600*24);
        apcu_store(APCU_PREFIX.'Products_List', $response->products, 3600);
        iFunctions::ConsoleLog("Products for API.");
        return $response->products;
    }
    public  static function Products_Get(): array {
        $products = self::Products_Get_Api();
        if (apcu_exists(APCU_PREFIX.'Products_Scan') && apcu_fetch(APCU_PREFIX.'Products_Scan') === false) { return $products; }

        $scanProduct = false;
        $dateNow     = time();
        $lProducts   = [];
        foreach ($products as $line) {
            if ($line->stock_Use)          {
                if ($line->stock_Saled >= $line->stock_Count) { /*print_r("SKIP SALED! Now: ".$line->stock_Saled." >= ".$line->stock_Count."<br/>");*/ continue; }
                $scanProduct = true;
            }
            if ($line->scheduledSale_Use)  {
                if ($dateNow < $line->scheduledSale_Start_Unix)  { /*print_r("SKIP Start! Now: ".($dateNow-1751000000).", Start: ".($line->scheduledSale_Start_Unix-1751000000)."<br/>");*/ continue; }
                if ($dateNow >=$line->scheduledSale_Finish_Unix) { /*print_r("SKIP FINISH! Now: ".($dateNow-1751000000).", Start: ".($line->scheduledSale_Finish_Unix-1751000000)."<br/>");*/ continue; }
                $scanProduct = true;
            }
            if ($line->event_ID   != 0  && !irisApi::$Events->GetStatus_ByID($line->event_ID)) { continue; }
            if ($line->event_Code != "" && !irisApi::$Events->GetStatus_ByType($line->event_Code)) { continue; }

            $lProducts[] = $line;
        }
        apcu_store(APCU_PREFIX.'Products_Scan', $scanProduct, 3600*24);
        apcu_store(APCU_PREFIX.'Products_List', $lProducts, 3600);
        return $lProducts;
    }
    public  static function Products_Get_ByCategory(int $baseID, int $subID): array {
        $cProducts = [];
        $products  = self::Products_Get();
        foreach ($products as $line) {
            if ($baseID != 0 && $baseID != $line->categoryBase) { continue; }
            if ($subID  != 0 && $subID  != $line->categorySub)  { continue; }
            $cProducts[] = $line;
        }

        $jsonString = json_encode($cProducts);
        return json_decode($jsonString);
    }
    public  static function Products_Get_ShowCase(): array {
        $cProducts = [];
        $products  = self::Products_Get();
        foreach ($products as $line) {
            if (!$line->showcase) { continue; }
            $cProducts[] = $line;
        }

        $jsonString = json_encode($cProducts);
        return json_decode($jsonString);
    }
    public  static function Products_Get_Random(int $count): array {
        $cProducts = [];
        $products  = self::Products_Get();
        shuffle($products);
        $pCount = count($products);
        for ($i = 0; $i < $count && $i < $pCount; $i++) { $cProducts[] = $products[$i]; }

        $jsonString = json_encode($cProducts);
        return json_decode($jsonString);
    }
    public  static function Products_Search($searchText): array {
        $searchText = mb_strtolower($searchText, 'utf8');
        $fProducts = [];
        $products  = self::Products_Get();
        foreach ($products as $line) {
            $pName = mb_strtolower(iFunctions::LangDict_Get($line->name), 'utf8');
            $fPos = strpos($pName, $searchText);
            if ($fPos !== false) { $fProducts[] = $line; }
        }
        return $fProducts;
    }
    public  static function Products_Detail(int $productID): mixed {
        $products  = self::Products_Get();
        foreach ($products as $line) {
            if ($line->id == $productID) { return $line; }
        }
        return [];
    }
    public  static function Products_StockDown(int $productID, int $productCount): void {
        $products = self::Products_Get();

        foreach ($products as $line) {
            if ($line->id != $productID) { continue; }
            if (!$line->stock_Use)       { return; }
            $line->stock_Saled+=$productCount;
            break;
        }
        apcu_store(APCU_PREFIX.'Products_List', $products, 3600);
    }
}

// ProductProto içeriği
    //int                        id
    //int                        categoryBase
    //int                        categorySub
    //bool                       showcase
    //bool                       packet_Only        (true ise ürün tek başına satılmaz, sadece paket içinde)
    //int                        packet_Count       (paket içindeki adet, default 1)
    //List<string>               status_Language
    //Dictionary<string, string> name
    //Dictionary<string, string> desc
    //PriceTypes                 price_Type         (0:Epin, 1:Marka)
    //int                        price_Amount
    //ReturnTypes                priceReturn_Type   (0:None, 1:Epin, 2:Marka)
    //double                     priceReturn_Value
    //int                        event_ID
    //string                     event_Code
    //DiscountTypes              discount_Type      (0:None, 1:AmountPercent, 2:CountList)
    //double                     discount_Value
    //List<DiscountLine>         discount_List
    //bool                       stock_Use
    //int                        stock_Count
    //int                        stock_Saled
    //bool                       scheduledSale_Use
    //DateTime                   scheduledSale_Start
    //long                       scheduledSale_Start_Unix
    //DateTime                   scheduledSale_Finish
    //long                       scheduledSale_Finish_Unix
    //VisualTypes                visual_Type        (0:ItemIcon, 1:ImagePath)
    //string                     visual_Value
    //int                        item_Vnum
    //Dictionary<string, string> item_Tooltip
    //int                        item_Size
    //int                        item_Count
    //List<int>                  item_Sockets
    //List<AttrLine>             item_Attrs
    //List<AttrLine>             item_Apply
    //List<int>                  item_Values

// DiscountLine içeriği
    //int    count
    //int    discount
    //int    amount

// AttrLine içeriği
    //int    type
    //int    value