<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Server Error</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Manrope:wght@200..800&family=Orbitron:wght@400..900&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Manrope, Arial, sans-serif;
            background: #f7f7f7;
            color: #333;
            line-height: 1.6;
        }

        .error-container {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .error-header {
            background: linear-gradient(135deg, #3f62ad 0%, #394e9f 100%);
            color: white;
            padding: 2rem 0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .error-header-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        .error-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            opacity: 0.95;
        }

        .error-subtitle {
            font-size: 0.875rem;
            opacity: 0.8;
        }

        .error-body {
            flex: 1;
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
            width: 100%;
        }

        .error-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            margin-bottom: 1rem;
            overflow: hidden;
        }

        .error-card-header {
            background: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
            padding: 1rem 1.5rem;
            font-weight: 600;
            font-size: 0.875rem;
            color: #495057;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .error-card-body {
            padding: 1.5rem;
        }

        .exception-message {
            font-size: 1.125rem;
            color: #e3342f;
            font-weight: 500;
            margin-bottom: 1rem;
            line-height: 1.5;
        }

        .exception-class {
            display: inline-block;
            background: #fff5f5;
            color: #c53030;
            padding: 0.25rem 0.75rem;
            border-radius: 4px;
            font-size: 0.875rem;
            font-family: "Courier New", monospace;
            margin-bottom: 1rem;
        }

        .file-location {
            background: #f8f9fa;
            border-left: 3px solid #667eea;
            padding: 1rem;
            margin: 1rem 0;
            border-radius: 4px;
            font-family: "Courier New", monospace;
            font-size: 0.875rem;
        }

        .file-path {
            color: #495057;
            word-break: break-all;
        }

        .line-number {
            color: #667eea;
            font-weight: 600;
            margin-left: 0.5rem;
        }

        .stack-trace {
            background: #2d3748;
            color: #e2e8f0;
            padding: 1.5rem;
            border-radius: 4px;
            overflow-x: auto;
            font-family: "Courier New", monospace;
            font-size: 0.813rem;
            line-height: 1.8;
        }

        .footer {
            text-align: center;
            padding: 1rem;
            color: #6c757d;
            font-size: 0.875rem;
        }

        @media (max-width: 768px) {
            .error-body {
                padding: 1rem;
            }

            .error-card-body {
                padding: 1rem;
            }

            .stack-trace {
                font-size: 0.75rem;
            }
        }
    </style>
</head>

<body>
    <div class="error-container">
        <div class="error-header">
            <div class="error-header-content">
                <div class="error-title">Server Error</div>
                <div class="error-subtitle">Whoops, looks like something went wrong.</div>
            </div>
        </div>

        <div class="error-body">
            <div class="error-card">
                <div class="error-card-header">Exception Details</div>
                <div class="error-card-body">
                    <div class="exception-class"><?= $exceptionClass ?></div>
                    <div class="exception-message"><?= $message ?></div>
                    <div class="file-location">
                        <div class="file-path">
                            📁 <?= $file ?><span class="line-number">: <?= $line ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="error-card">
                <div class="error-card-header">Stack Trace</div>
                <div class="error-card-body">
                    <pre class="stack-trace"><?= $trace ?></pre>
                </div>
            </div>
        </div>

        <div class="footer">
            <p>Error occurred on <?= $timestamp ?></p>
        </div>
    </div>
</body>

</html>