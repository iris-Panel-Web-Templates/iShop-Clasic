<?php

class UserAgent_Class {
    public static function IncludePage(): void {
        global $_SESSION;
        $userTimezone = $_SESSION['user_timezone'] ?? 'UTC';
        if ($userTimezone == 'UTC') {
            echo "
                <script type='text/javascript' language='javascript' defer='defer' title='useragent_timezone'>
                    const timeZone = Intl.DateTimeFormat().resolvedOptions().timeZone;
                    fetch('".(iFunctions::IsLocal() ? "index.php?s=timezoneset" : "/timezoneset")."', {
                    method: 'POST', headers: {'Content-Type': 'application/json'},
                    body  : JSON.stringify({ timezone: timeZone })
                    }).then(() => { });
                </script>";
        }
    }
}

