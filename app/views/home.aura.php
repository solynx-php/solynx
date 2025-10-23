<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solynx - Welcome</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Orbitron:wght@400..900&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=Manrope:wght@200..800&family=Orbitron:wght@400..900&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --bg-primary: #0a0e27;
            --bg-secondary: rgba(17, 24, 39, 0.8);
            --bg-feature: rgba(31, 41, 55, 0.5);
            --bg-feature-hover: rgba(31, 41, 55, 0.8);
            --text-primary: #e5e7eb;
            --text-secondary: #9ca3af;
            --text-muted: #6b7280;
            --border-color: rgba(102, 126, 234, 0.2);
            --gradient-start: #667eea;
            --gradient-end: #a78bfa;
            --shadow-color: rgba(0, 0, 0, 0.5);
            --glow-color: rgba(102, 126, 234, 0.15);
        }

        body.light-mode {
            --bg-primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --bg-secondary: rgba(255, 255, 255, 0.95);
            --bg-feature: linear-gradient(135deg, #f6f8fb 0%, #ffffff 100%);
            --bg-feature-hover: #ffffff;
            --text-primary: #2d3748;
            --text-secondary: #718096;
            --text-muted: #a0aec0;
            --border-color: #e2e8f0;
            --gradient-start: #667eea;
            --gradient-end: #764ba2;
            --shadow-color: rgba(0, 0, 0, 0.3);
            --glow-color: rgba(255, 255, 255, 0.1);
        }

        body {
            font-family: Manrope, sans-serif;
            background: var(--bg-primary);
            min-height: 100vh;
            padding: 40px 20px;
            position: relative;
            overflow-x: hidden;
            overflow-y: auto;
            transition: background 0.3s ease;
        }

        body::before {
            content: '';
            position: fixed;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle, var(--glow-color) 0%, transparent 70%);
            animation: pulse 15s ease-in-out infinite;
            pointer-events: none;
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
                opacity: 0.5;
            }

            50% {
                transform: scale(1.2);
                opacity: 0.8;
            }
        }

        .theme-toggle {
            position: fixed;
            top: 30px;
            right: 30px;
            z-index: 100;
            background: var(--bg-feature);
            border: 2px solid var(--border-color);
            border-radius: 50px;
            padding: 12px 24px;
            cursor: pointer;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--text-primary);
            font-weight: 600;
            box-shadow: 0 4px 15px var(--shadow-color);
        }

        .theme-toggle:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px var(--shadow-color);
        }

        .theme-icon {
            font-size: 20px;
            transition: transform 0.3s ease;
        }

        .theme-toggle:hover .theme-icon {
            transform: rotate(20deg);
        }

        .container {
            background: var(--bg-secondary);
            backdrop-filter: blur(10px);
            border-radius: 24px;
            box-shadow: 0 20px 60px var(--shadow-color);
            max-width: 900px;
            width: 100%;
            padding: 60px 40px;
            text-align: center;
            position: relative;
            z-index: 1;
            animation: fadeIn 0.8s ease-out;
            margin: 0 auto;
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .logo {
            font-family: Orbitron, sans-serif;
            font-size: 64px;
            font-weight: 800;
            background: linear-gradient(135deg, var(--gradient-start) 0%, var(--gradient-end) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 20px;
            letter-spacing: -2px;
        }

        .tagline {
            font-size: 24px;
            color: var(--text-secondary);
            margin-bottom: 40px;
            font-weight: 300;
        }

        .version {
            display: inline-block;
            background: linear-gradient(135deg, var(--gradient-start) 0%, var(--gradient-end) 100%);
            color: white;
            padding: 8px 20px;
            border-radius: 50px;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 50px;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }

        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin-bottom: 50px;
        }

        .feature {
            padding: 30px;
            background: var(--bg-feature);
            border-radius: 16px;
            transition: all 0.3s ease;
            cursor: pointer;
            border: 2px solid var(--border-color);
        }

        .feature:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
            border-color: var(--gradient-start);
            background: var(--bg-feature-hover);
        }

        .feature-icon {
            font-size: 48px;
            margin-bottom: 15px;
            display: inline-block;
            transition: transform 0.3s ease;
        }

        .feature:hover .feature-icon {
            transform: scale(1.1) rotate(5deg);
        }

        .feature-title {
            font-size: 20px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 10px;
        }

        .feature-desc {
            font-size: 14px;
            color: var(--text-secondary);
            line-height: 1.6;
        }

        .cta-section {
            margin-top: 50px;
            padding-top: 40px;
            border-top: 2px solid var(--border-color);
        }

        .cta-title {
            font-size: 28px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 20px;
        }

        .links {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .link {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 15px 30px;
            background: linear-gradient(135deg, var(--gradient-start) 0%, var(--gradient-end) 100%);
            color: white;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }

        .link:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5);
        }

        .link.secondary {
            background: var(--bg-feature);
            color: var(--gradient-end);
            border: 2px solid var(--gradient-start);
        }

        .link.secondary:hover {
            background: var(--bg-feature-hover);
        }

        .footer {
            margin-top: 40px;
            margin-bottom: 20px;
            color: var(--text-muted);
            font-size: 14px;
        }

        @media (max-width: 768px) {
            body {
                padding: 20px 15px;
            }

            .theme-toggle {
                top: 20px;
                right: 20px;
                padding: 10px 20px;
                font-size: 14px;
            }

            .container {
                padding: 40px 30px;
            }

            .logo {
                font-size: 48px;
            }

            .tagline {
                font-size: 18px;
            }

            .features {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <button class="theme-toggle" onclick="toggleTheme()">
        <span class="theme-icon">☀️</span>
    </button>

    <div class="container">
        <div class="logo">SOLYNX</div>
        <div class="tagline">The Elegant PHP Framework for Modern Web Applications</div>
        <div class="version">v1.0.0</div>

        <div class="features">
            <div class="feature">
                <div class="feature-icon">⚡</div>
                <div class="feature-title">Lightning Fast</div>
                <div class="feature-desc">Optimized performance with intelligent caching and minimal overhead for rapid development.</div>
            </div>

            <div class="feature">
                <div class="feature-icon">🛠️</div>
                <div class="feature-title">Developer Friendly</div>
                <div class="feature-desc">Intuitive syntax and powerful CLI tools Create building applications a breeze.</div>
            </div>

            <div class="feature">
                <div class="feature-icon">🔒</div>
                <div class="feature-title">Secure by Default</div>
                <div class="feature-desc">Built-in protection against common vulnerabilities with best-practice security patterns.</div>
            </div>

            <div class="feature">
                <div class="feature-icon">🎨</div>
                <div class="feature-title">Elegant Architecture</div>
                <div class="feature-desc">Clean MVC structure with modern design patterns for maintainable code.</div>
            </div>

            <div class="feature">
                <div class="feature-icon">📦</div>
                <div class="feature-title">Rich Ecosystem</div>
                <div class="feature-desc">Extensive package library and seamless integration with modern tools.</div>
            </div>

            <div class="feature">
                <div class="feature-icon">🌐</div>
                <div class="feature-title">Modern Stack</div>
                <div class="feature-desc">Built for PHP 8+ with support for the latest features and technologies.</div>
            </div>
        </div>

        <div class="cta-section">
            <div class="cta-title">Ready to Build Something Amazing?</div>
            <div class="links">
                <a href="#" class="link">📚 Documentation</a>
                <a href="#" class="link secondary">🎓 Tutorial</a>
                <a href="#" class="link secondary">💬 Community</a>
            </div>
        </div>

        <div class="footer">
            Solynx Framework © 2025. All rights reserved.
        </div>
    </div>

    <script>
        let isDark = true;

        function toggleTheme() {
            isDark = !isDark;
            const body = document.body;
            const themeIcon = document.querySelector('.theme-icon');

            if (isDark) {
                body.classList.remove('light-mode');
                themeIcon.textContent = '☀️';
            } else {
                body.classList.add('light-mode');
                themeIcon.textContent = '🌙';
            }
        }

        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        if (!prefersDark) {
            toggleTheme();
        }
    </script>
</body>

</html>