<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/* Clear any previous output buffer to ensure this is the only page shown.
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
    <title>Error</title>
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
            padding: 40px;
            border-radius: 24px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
            text-align: center;
        }
        .icon-circle {
            width: 80px;
            height: 80px;
            background-color: #ffedd5;
            color: #f97316;
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
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon-circle">
            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
        </div>
        <h1>Something went wrong</h1>
        <p>
            <?php 
                if (ENVIRONMENT === 'development') {
                    echo "A general error was encountered.";
                } else {
                    echo "An unexpected error occurred. Our team has been notified and we are working to resolve it.";
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
