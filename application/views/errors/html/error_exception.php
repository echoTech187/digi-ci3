<?php
defined('BASEPATH') OR exit('No direct script access allowed');
// Membersihkan buffer agar error ini tidak nyangkut di dalam container flex halaman aslinya
for ($i = 0; $i < ob_get_level(); $i++) {
    @ob_clean();
}
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <link rel="icon" href="<?php echo config_item('base_url'); ?>/public/icon/favicon.ico" type="image/x-icon">
    <title>System Exception</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@500;600&family=Montserrat:wght@800;900&display=swap" rel="stylesheet">
    <style type="text/css">
        :root {
            --bg-color: #ffdd00;
            --text-dark: #00214d;
            --text-white: #ffffff;
        }
        body {
            background-color: var(--bg-color);
            margin: 0;
            padding: 0;
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow-x: hidden;
        }
        .error-wrapper {
            max-width: 1200px;
            width: 100%;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 40px;
            box-sizing: border-box;
            gap: 60px;
        }
        .content-section {
            flex: 1;
            max-width: 480px;
        }
        .image-section {
            flex: 1.2;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .image-section img {
            max-width: 100%;
            height: auto;
            mix-blend-mode: multiply;
        }
        .error-code {
            font-family: 'Montserrat', sans-serif;
            font-size: 130px;
            font-weight: 900;
            color: var(--text-dark);
            margin: 0;
            line-height: 0.9;
            letter-spacing: -6px;
        }
        .error-title {
            font-family: 'Montserrat', sans-serif;
            font-size: 40px;
            font-weight: 800;
            color: var(--text-white);
            margin: 0 0 30px 0;
            line-height: 1.1;
            letter-spacing: -1.5px;
        }
        .error-message {
            font-size: 16px;
            line-height: 1.6;
            color: var(--text-dark);
            font-weight: 500;
            margin-bottom: 40px;
        }
        .btn {
            display: inline-block;
            background-color: var(--text-dark);
            color: var(--text-white);
            padding: 16px 32px;
            text-decoration: none;
            font-weight: 700;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-radius: 4px;
            transition: transform 0.2s, background-color 0.2s;
        }
        .btn:hover {
            background-color: #00122e;
            transform: translateY(-2px);
        }
        @media (max-width: 900px) {
            .error-wrapper {
                flex-direction: column-reverse;
                text-align: center;
                padding: 20px;
            }
            .content-section {
                max-width: 100%;
                display: flex;
                flex-direction: column;
                align-items: center;
            }
            .error-code { font-size: 90px; letter-spacing: -4px; }
            .error-title { font-size: 32px; }
            .image-section { margin-bottom: 20px; }
        }
    </style>
</head>
<body>
    <div class="error-wrapper">
        <div class="image-section">
            <img src="<?php echo config_item('base_url'); ?>/public/img/exception_error.png" alt="Exception Error Illustration">
        </div>
        <div class="content-section">
            <h1 class="error-code">Exception</h1>
            <h2 class="error-title">System Error</h2>
            <p class="error-message">
                A system exception has occurred. Our engineers have been alerted and are investigating the issue to ensure the stability of your integrations.
            </p>
            <a href="javascript:window.location.reload();" class="btn">TRY AGAIN</a>
        </div>
    </div>
    
    <?php
    // Jangan pernah membuang log error exception, kita log diam-diam
    log_message('error', "Exception: " . $exception->getMessage() . " in " . $exception->getFile() . " on line " . $exception->getLine());
    ?>
</body>
</html>
