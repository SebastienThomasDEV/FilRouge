<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erreur de Développement</title>
    <style>
        :root {
            --primary: #4f46e5;
            --secondary: #ec4899;
            --error: #ef4444;
            --warning: #f59e0b;
            --info: #3b82f6;
            --success: #10b981;
            --text: #1f2937;
            --text-light: #6b7280;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .error-container {
            width: 80%;
            padding: 20px;
            background: white;
            border-radius: 24px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .error-type {
            color: #dc3545;
            font-size: 24px;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #dc3545;
        }
        .error-details {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .stack-trace {
            background: #212529;
            color: #e9ecef;
            padding: 15px;
            border-radius: 4px;
            overflow-x: auto;
            font-family: monospace;
            margin-top: 20px;
        }
        .file-line {
            color: #ffc107;
        }
        .error-message {
            font-weight: 500;
            color: #dc3545;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
<div class="error-container">
    <div class="error-type">
        Erreur PHP
    </div>
    <div class="error-details">
        <div class="error-message">
            <?= htmlspecialchars($message) ?>
        </div>
        <div>
            <strong>Fichier:</strong> <?= htmlspecialchars($file) ?><br>
            <strong>Ligne:</strong> <?= $line ?><br>
            <strong>Code:</strong> <?= $code ?>
        </div>
    </div>
    <div class="stack-trace">
        <pre><?= htmlspecialchars($trace) ?></pre>
    </div>
</div>
</body>
</html>