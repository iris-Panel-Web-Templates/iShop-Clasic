<?php

class Category_Class {

    public static function Category_Get(): array {
        if (apcu_exists(APCU_PREFIX.'Category_Time') && (apcu_fetch(APCU_PREFIX.'Category_Time') + Category_CacheTime > time())) {
            if (apcu_exists(APCU_PREFIX.'Category_Base') && apcu_exists(APCU_PREFIX.'Category_Subs')){
                iFunctions::ConsoleLog("Categorys for CACHE.");
                return [
                    'Base'  => apcu_fetch(APCU_PREFIX.'Category_Base'),
                    'Subs'  => apcu_fetch(APCU_PREFIX.'Category_Subs')
                ];
            }
        }
        apcu_store(APCU_PREFIX.'Category_Time', time(), 3600);

        $post_url     = irisAuthUrl . "/iShop/Categorys";
        $response     = iFunctions::ApiPost($post_url, []);
        if ($response->responseCode != 0) {
            iFunctions::ConsoleLog("Categorys Get Error: ".$response->responseMessage."\n");
            apcu_store(APCU_PREFIX.'Category_Base', []);
            apcu_store(APCU_PREFIX.'Category_Subs', []);
            apcu_store(APCU_PREFIX.'Category_Time', time()-(Category_CacheTime-5));
            return [ 'Base' => [], 'Subs' => []];
        }

        apcu_store(APCU_PREFIX.'Category_Base', $response->categorysBase, 3600);
        apcu_store(APCU_PREFIX.'Category_Subs', $response->categorysSubs, 3600);
        iFunctions::ConsoleLog("Categorys for API.");
        return [
            'Base'  => $response->categorysBase,
            'Subs'  => $response->categorysSubs
        ];
    }
    public static function CategoryBase_GetName(int $categoryID): string {
        $cBase = self::Category_Get()["Base"];
        foreach ($cBase as $line) {
            if ($line->id == $categoryID) {
                return iFunctions::LangDict_Get($line->name);
            }
        }

        return "";
    }
    public static function CategorySub_GetName(int $categoryID): string {
        $cBase = self::Category_Get()["Subs"];
        foreach ($cBase as $line) {
            if ($line->id == $categoryID) {
                return iFunctions::LangDict_Get($line->name);
            }
        }

        return "";
    }

}

// Category_Get() dönüş şekli:
//   array[ 'Base' => CategoryProto[], 'Subs' => CategorySubProto[] ]
//   Kullanım: $cats = irisApi::$Category->Category_Get();
//             $cats['Base']  → ana kategoriler
//             $cats['Subs']  → alt kategoriler (baseID ile ana kategoriye bağlı)

// CategoryProto içeriği
    //int                        id
    //int                        order
    //string                     icon
    //Dictionary<string, string> name   (çoklu dil; LangDict_Get() ile aktif dile göre okunur)

// CategorySubProto içeriği
    //int                        id
    //int                        baseID  (bağlı olduğu ana kategori ID'si)
    //int                        order
    //string                     icon
    //Dictionary<string, string> name