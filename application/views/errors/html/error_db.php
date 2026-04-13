<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/* Clear any previous output (like PHP warnings) so only this error page shows.
 * We use ob_clean() instead of ob_end_clean() to avoid breaking the 
 * buffer level managed by CI_Exceptions::show_error().
 */
for ($i = 0; $i < ob_get_level(); $i++) {
    @ob_clean();
}
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <link rel="icon" href="<?php echo config_item('base_url'); ?>/public/icon/favicon.ico" type="image/x-icon">
    <title>Database Error</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style type="text/css">
        body {
            background-color: #f8fafc;
            color: #1e293b;
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            max-width: 500px;
            width: 100%;
            background: #fff;
            padding: 40px 24px;
            border-radius: 24px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
            text-align: center;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }
        .icon-circle {
            width: 80px;
            height: 80px;
            background-color: #fee2e2;
            color: #ef4444;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            font-size: 32px;
        }
        h1 {
            font-size: 24px;
            font-weight: 700;
            margin: 0 0 12px;
            color: #0f172a;
        }
        p {
            font-size: 16px;
            line-height: 1.6;
            color: #64748b;
            margin: 0 0 32px;
        }
        .debug-info {
            text-align: left;
            background-color: #f1f5f9;
            padding: 16px;
            border-radius: 12px;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
            font-size: 13px;
            color: #475569;
            margin-bottom: 32px;
            overflow-x: auto;
            border: 1px solid #e2e8f0;
            word-break: break-all;
            white-space: pre-wrap;
        }
        .btn {
            display: inline-block;
            background-color: #4f46e5;
            color: #ffffff;
            padding: 12px 32px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            font-size: 15px;
            transition: all 0.2s ease;
            box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.2);
        }
        .btn:hover {
            background-color: #4338ca;
            transform: translateY(-1px);
            box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.3);
        }
        .btn:active {
            transform: translateY(0);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon-circle">
            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22a10 10 0 1 0 0-20 10 10 0 0 0 0 20z"></path><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
        </div>
        <h1>Data Connection Issue</h1>
        <p>
            <?php 
                if (ENVIRONMENT === 'development') {
                    echo "We encountered a database error during your request.";
                } else {
                    echo "We're having trouble connecting to our data service. Please try again in a few moments or contact support if the problem persists.";
                }
            ?>
        </p>

        <?php if (ENVIRONMENT === 'development'): ?>
            <div class="debug-info">
                <strong>Debug Details:</strong><br>
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <a href="<?php echo config_item('base_url'); ?>" class="btn">Return to Dashboard</a>
    </div>
</body>
</html>