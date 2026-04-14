<?php
$statusCode = $_SERVER['REDIRECT_STATUS'] ?? http_response_code();
$statusCode = (int)$statusCode;

if ($statusCode === 403) {
    http_response_code(403);
    $title   = "403 — Erişim Reddedildi";
    $heading = "Erişim Yasak";
    $message = "Bu sayfaya erişim yetkiniz bulunmuyor.";
    $sub     = "Hesabınıza giriş yapmanız gerekiyor olabilir.";
    $icon    = "🔒";
} else {
    http_response_code(404);
    $statusCode = 404;
    $title   = "404 — Sayfa Bulunamadı";
    $heading = "Sayfa Bulunamadı";
    $message = "Aradığınız sayfa mevcut değil ya da kaldırılmış olabilir.";
    $sub     = "Alışverişe devam etmek için mağazaya dönebilirsiniz.";
    $icon    = "🛒";
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            background-color: #111318;
            background-image:
                radial-gradient(ellipse at 50% 0%, rgba(30,60,120,0.25) 0%, transparent 65%);
            color: #c8cfe8;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 40px 20px;
        }

        .icon {
            font-size: 52px;
            margin-bottom: 12px;
            filter: grayscale(0.3);
        }

        .code {
            font-size: 96px;
            font-weight: 700;
            color: #2a4a8a;
            text-shadow:
                0 2px 4px rgba(0,0,0,0.9),
                0 0 50px rgba(42,74,138,0.4);
            line-height: 1;
            letter-spacing: -3px;
        }

        .divider {
            width: 240px;
            height: 1px;
            margin: 20px auto;
            background: linear-gradient(to right, transparent, #2a4a8a, #5a8ae8, #2a4a8a, transparent);
        }

        .heading {
            font-size: 22px;
            font-weight: 600;
            color: #e8ecff;
            letter-spacing: 1px;
            margin-bottom: 12px;
        }

        .message {
            font-size: 14px;
            color: #7a85a8;
            line-height: 1.8;
            max-width: 380px;
        }

        .message em {
            display: block;
            font-style: normal;
            color: #5a6280;
            font-size: 12px;
            margin-top: 4px;
        }

        .divider-sm {
            width: 60px;
            height: 1px;
            margin: 26px auto;
            background: linear-gradient(to right, transparent, #2a4a8a, transparent);
        }

        .btn {
            display: inline-block;
            padding: 11px 36px;
            background: linear-gradient(135deg, #2a4a8a 0%, #1a3060 100%);
            color: #c8d4ff;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: 1px;
            border: 1px solid #2a4a8a;
            border-radius: 4px;
            transition: background 0.2s, color 0.2s, border-color 0.2s, box-shadow 0.2s;
        }

        .btn:hover {
            background: linear-gradient(135deg, #3a5aaa 0%, #2a4a8a 100%);
            color: #ffffff;
            border-color: #5a7ad8;
            box-shadow: 0 0 16px rgba(90,122,216,0.25);
        }

        .footer-note {
            position: fixed;
            bottom: 18px;
            font-size: 11px;
            color: #1e2438;
            letter-spacing: 1px;
        }
    </style>
</head>
<body>
    <div class="icon"><?= $icon ?></div>
    <div class="code"><?= $statusCode ?></div>
    <div class="divider"></div>
    <div class="heading"><?= $heading ?></div>
    <div class="message">
        <?= $message ?>
        <em><?= $sub ?></em>
    </div>
    <div class="divider-sm"></div>
    <a href="/" class="btn">Mağazaya Dön</a>
    <div class="footer-note">iSHOP</div>
</body>
</html>
