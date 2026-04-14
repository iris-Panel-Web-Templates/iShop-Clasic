<?php
include("iFunctions.php");
include("iApi_Setting.php");

session_start();
global $_SESSION;

if (isset($_SESSION['csr_code']) && isset($_GET['csr_code']) && isset($_GET['action'])){
    global $_SESSION;
    $csrCode = $_SESSION['csr_code'];
    if ($csrCode == $_GET['csr_code']) {
        if ($_GET['action'] === 'session_destroy') {
            session_destroy();
            session_start();
            $_SESSION['dev_message'] = "Session Destroyed.";
            header("Location: ?action=none");
            exit;
        }
        if ($_GET['action'] === 'apcu_clear_cache') {
            apcu_clear_cache();
            $_SESSION['dev_message'] = "Apcu Cleared.";
            header("Location: ?action=none");
            exit;
        }
    }
}

const DeveloperPrint = true;
function Test_Extension(): array {
    if (!function_exists('apcu_store')) { return [ 'code' => 1001, 'message' => "APCu yüklü değil!" ]; }
    if (!ini_get('apc.enabled'))         { return [ 'code' => 1002, 'message' => "APCu CLI'da etkin değil!" ]; }
    if (php_sapi_name() === 'cli' && !ini_get('apc.enable_cli')) { return [ 'code' => 1002, 'message' => "APCu CLI'da etkin değil!" ]; }

    return [ 'code' => 0, 'message' => "APCu yüklü ve CLI'da etkin." ];
}
function Test_ApiServer(): mixed {
    return iFunctions::ApiPost(irisAuthUrl . "/StartupTest", [
        'IpAddress' => iFunctions::GetRequestIP()
    ]);
}

// Son $limit adet hata kaydını döndürür (en yeniden eskiye sıralı).
// Log formatı: her hata 3 satır — POST / DATA / ERROR.
// Dosya yoksa veya boşsa boş dizi döner.
function ReadErrorLog(int $limit = 50): array {
    if (!defined('ErrorLogFile') || !file_exists(ErrorLogFile)) { return []; }
    $lines = file(ErrorLogFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false || count($lines) === 0) { return []; }
    $entries = [];
    for ($i = 0, $total = count($lines); $i + 2 < $total; $i += 3) {
        $entries[] = ['post' => $lines[$i], 'data' => $lines[$i + 1], 'error' => $lines[$i + 2]];
    }
    return array_reverse(array_slice($entries, -$limit));
}
?>


<?php if (!DeveloperPrint) { exit; } ?>
<!DOCTYPE html>
<html lang="en" >
<head>
    <title>Developer</title>
    <style>
        body     { background-color: #333333; color: white; }
        a        { color: white; text-decoration: none; font-weight: bold; }
        pre      { color: #d8d8d8; border: 1px solid black; height: 500px; padding: 10px; overflow:auto; }
        .InfoBox { font-size: 12px; color: dimgray; }
        .devMessage { margin: 10px; max-width: 250px; text-align: center; font-size: 12px; color: white; font-weight: bold; padding: 25px; border: 1px solid darkgreen; border-radius: 4px; background-color: #3a3a3a;  }
    </style>
</head>
<body>
<?php
$apcuStatus  = Test_Extension();
$apiResponse = Test_ApiServer();
if (!iFunctions::IsLocal() && !$apiResponse->isDeveloper){
    echo("<p class='InfoBox'>");
    echo("<b>APCu Status:</b> <span style='color: ".($apcuStatus["code"] == 0 ? "green" : "red").";'>".$apiResponse->responseMessage."</span><br/>");
    echo("<b>Api Server Access Code/Message:</b> <span style='color: ".($apiResponse->responseCode == 0 ? "green" : "red").";'>".$apiResponse->responseCode." / ".$apiResponse->responseMessage."</span><br/>");
    echo("<b>Api Server Boot Status/Message:</b> <span style='color: ".($apiResponse->responseCode == 0 ? "green" : "red").";'>".$apiResponse->systemStatus." / ".$apiResponse->systemMessage."</span><br/>");
    echo("<b>Developer Message:</b> <span style='color: red;'>".$apiResponse->isDeveloperMessage."</span><br/>");
    echo("</p>");
}
if ( iFunctions::IsLocal() ||  $apiResponse->isDeveloper) {
    $csrCode = iFunctions::Random_String(6);
    $_SESSION['csr_code'] = $csrCode;
    echo "<a href='?action=session_destroy&csr_code=$csrCode'>- Session destroy</a><br/>";
    echo "<a href='?action=apcu_clear_cache&csr_code=$csrCode'>- APCU clear cache</a>";
    echo("<br/>");
    echo("<p class='InfoBox'>");
    echo("    <b>APCu Status:</b> <span style='color: ".($apcuStatus["code"] == 0 ? "green" : "red").";'>".$apcuStatus["message"]."</span><br/>");
    echo("    <b>Api Server Access Code/Message:</b> <span style='color: ".($apiResponse->responseCode == 0 ? "green" : "red").";'>".$apiResponse->responseCode." / ".$apiResponse->responseMessage."</span><br/>");
    echo("    <b>Api Server Boot Status/Message:</b> <span style='color: ".($apiResponse->responseCode == 0 ? "green" : "red").";'>".$apiResponse->systemStatus." / ".$apiResponse->systemMessage."</span><br/>");
    echo("    <b>Developer Message:</b> <span style='color: green;'>".$apiResponse->isDeveloperMessage."</span><br/>");
    echo("</p>");
}

if (isset($_SESSION['dev_message']) && $_SESSION['dev_message'] != ""){
    echo("<p class='devMessage'>".$_SESSION['dev_message']."</p>");
    echo("<script>setTimeout(() => { document.getElementsByClassName('devMessage')[0].style.display = 'none'; } , 3000);</script>");
    $_SESSION['dev_message'] = "";
}


// Error log bölümü — yalnızca local veya isDeveloper erişiminde gösterilir
if (iFunctions::IsLocal() || (isset($apiResponse) && $apiResponse->isDeveloper)) {
    $logEntries = ReadErrorLog(50);
    $logCount   = count($logEntries);
    echo "<hr style='border-color:#555; margin:20px 0;'>";
    echo "<p class='InfoBox'><b>Son Hatalar</b> ($logCount kayıt)";
    if ($logCount === 0) {
        echo " — log dosyası yok veya boş.</p>";
    } else {
        echo " — <span style='color:#888'>" . htmlspecialchars(ErrorLogFile) . "</span></p>";
        echo "<pre>";
        foreach ($logEntries as $i => $e) {
            $num = $logCount - $i;
            echo "<span style='color:#666'>--- #$num ---</span>\n";
            echo htmlspecialchars($e['post'])  . "\n";
            echo htmlspecialchars($e['data'])  . "\n";
            echo "<span style='color:#e07070'>" . htmlspecialchars($e['error']) . "</span>\n\n";
        }
        echo "</pre>";
    }
}
?>
</body>
</html>
