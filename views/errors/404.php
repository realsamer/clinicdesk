<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../core/helpers.php';
http_response_code(404);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 Not Found - <?= e(APP_NAME) ?></title>
    <style>
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            color: #333;
        }
        .error-box {
            width: 90%;
            max-width: 560px;
            padding: 30px;
            background: #fff;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        }
        h1 {
            margin: 0;
            font-size: 64px;
            color: #ffc107;
        }
        a {
            color: #007bff;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="error-box">
        <h1>404</h1>
        <h2>Page not found</h2>
        <p>The requested page does not exist.</p>
        <p><a href="<?= e(url()) ?>">Back to home</a></p>
    </div>
</body>
</html>
