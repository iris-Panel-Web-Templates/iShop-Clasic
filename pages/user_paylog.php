<style>
    .logLine {
        height: 26px;
        width: 520px;
        font-size: 12px;
        line-height: 12px;
        border-bottom: 1px solid #e1b1b1;
        border-radius: 4px;
        clear: both;
        margin: 5px 0;
        padding-bottom: 2px;
        color: dimgrey;
    }
    .logLine:hover { border-bottom: 1px solid rgb(228, 160, 160); background-color: rgba(221, 148, 148, 0.19); }
    .logLine .logDate { float: left; padding: 2px 5px; width: 80px; text-align: center; }
    .logLine .logType { float: left; padding: 2px 5px; width: 130px; }
    .logLine .logUser { float: left; padding: 2px 5px; width: 75px; min-width: 75px; }
    .logLine .logWhy  { float: left; padding: 2px 5px; width: 140px; font-size: 10px; color: dimgrey; }
    .logLine .logValue{ float: right; padding: 2px 5px; width: 45px; text-align: right; line-height: 26px; color: coral }
    .logLine .logValue small { color: darkgrey; padding-left: 1px; }
</style>
&
<div id="container">
    <?php include("pages/etc_user.php") ?>
    <div id="mainContent">
        <h1>Epin & Marka Yükleme Geçmişi</h1>
        <div class="dynContent" style="position:relative; padding-top: 5px;">
            <?php
            $accountID = $_SESSION['account_id'];
            $epinLogs = irisApi::$Shopping->Logs_Epins($accountID, 50);
            foreach ($epinLogs as $line) {
                $lType = $line->type;
                $lName = "none";
                $valueType = "₺";
                if ($lType == "CASH_SHOP")  { $lName = "Epin yükleme (Shop)";     $valueType = "EP"; }
                if ($lType == "CASH_GAME")  { $lName = "Epin yükleme (Oyun içi)"; $valueType = "EP"; }
                if ($lType == "CASH_PANEL") { $lName = "Epin yükleme (Admin)";    $valueType = "EP"; }

                if ($lType == "MILEAGE_SHOP")  { $lName = "Marka yükleme (Admin)";    $valueType = "MP"; }
                if ($lType == "MILEAGE_GAME")  { $lName = "Marka yükleme (Oyun içi)"; $valueType = "MP"; }
                if ($lType == "MILEAGE_PANEL") { $lName = "Marka yükleme (Admin)";    $valueType = "MP"; }
                if ($lType == "ITEM_PANEL")    { $lName = "Item Teslim (Admin)";      $valueType = ""; }

                echo "<div class='logLine'>
                            <div class='logDate'>".date_format(date_create($line->date),"Y/m/d H:i:s")."</div>
                            <div class='logType'>$lName</div>
                            <div class='logUser'>$line->playerName&nbsp;</div>
                            <div class='logWhy'>$line->why</div>
                            <div class='logValue'>$line->value<small>$valueType</small></div>
                      </div>";

            } ?>
        </div>
        <div class="endContent"></div>
    </div>
</div>

