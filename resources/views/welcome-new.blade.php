<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>NOTAS 2.0 - Sistema de Gestión de Notas</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary: #3b82f6;
            --primary-dark: #1e40af;
            --secondary: #8b5cf6;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --dark: #1f2937;
            --light: #f9fafb;
            --text-primary: #111827;
            --text-secondary: #6b7280;
            --border-color: #e5e7eb;
        }

        @media (prefers-color-scheme: dark) {
            :root {
                --primary: #60a5fa;
                --primary-dark: #3b82f6;
                --secondary: #a78bfa;
                --text-primary: #f3f4f6;
                --text-secondary: #d1d5db;
                --dark: #111827;
                --light: #1f2937;
                --border-color: #374151;
            }
        }

        html {
            font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;
            scroll-behavior: smooth;
        }

        body {
            background: linear-gradient(135deg, var(--light) 0%, #f0f9ff 100%);
            color: var(--text-primary);
            line-height: 1.6;
        }

        @media (prefers-color-scheme: dark) {
            body {
                background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            }
        }

        header {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--border-color);
            padding: 1rem 2rem;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        @media (prefers-color-scheme: dark) {
            header {
                background: rgba(15, 23, 42, 0.8);
            }
        }

        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .logo:hover {
            color: var(--primary-dark);
        }

        nav {
            display: flex;
            gap: 2rem;
            align-items: center;
        }

        nav a {
            color: var(--text-secondary);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        nav a:hover {
            color: var(--primary);
        }

        .btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            border: 2px solid transparent;
            cursor: pointer;
            font-size: 1rem;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(59, 130, 246, 0.3);
        }

        .btn-secondary {
            background: transparent;
            color: var(--primary);
            border-color: var(--primary);
        }

        .btn-secondary:hover {
            background: var(--primary);
            color: white;
        }

        .hero {
            max-width: 1200px;
            margin: 0 auto;
            padding: 6rem 2rem;
            text-align: center;
        }

        .hero h1 {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero p {
            font-size: 1.25rem;
            color: var(--text-secondary);
            margin-bottom: 2rem;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        .cta-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-bottom: 4rem;
            flex-wrap: wrap;
        }

        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
            margin: 4rem 2rem;
            max-width: 1200px;
            margin-left: auto;
            margin-right: auto;
        }

        .feature-card {
            background: white;
            padding: 2rem;
            border-radius: 1rem;
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        @media (prefers-color-scheme: dark) {
            .feature-card {
                background: #1f2937;
            }
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px rgba(59, 130, 246, 0.15);
            border-color: var(--primary);
        }

        .feature-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        .feature-card h3 {
            font-size: 1.25rem;
            margin-bottom: 0.5rem;
            color: var(--text-primary);
        }

        .feature-card p {
            color: var(--text-secondary);
            line-height: 1.6;
        }

        footer {
            background: var(--dark);
            color: white;
            padding: 2rem;
            text-align: center;
            margin-top: 6rem;
        }

        @media (prefers-color-scheme: dark) {
            footer {
                background: #030712;
            }
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2.5rem;
            }

            .hero p {
                font-size: 1rem;
            }

            nav {
                gap: 1rem;
            }
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2rem;
            margin: 4rem 2rem;
            max-width: 1200px;
            margin-left: auto;
            margin-right: auto;
        }

        .stat-box {
            background: white;
            padding: 1.5rem;
            border-radius: 0.75rem;
            text-align: center;
            border: 1px solid var(--border-color);
        }

        @media (prefers-color-scheme: dark) {
            .stat-box {
                background: #1f2937;
            }
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary);
        }

        .stat-label {
            color: var(--text-secondary);
            margin-top: 0.5rem;
        }

        @media (max-width: 768px) {
            .stats {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <div class="nav-container">
            <a href="/" class="logo">
                📝 NOTAS 2.0
            </a>
            <nav>
                @auth
                    <a href="{{ route('filament.admin.pages.dashboard') }}">Panel Admin</a>
                    <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-primary" style="border: none;">
                            Cerrar Sesión
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}">Iniciar Sesión</a>
                    <a href="{{ route('login') }}" class="btn btn-primary">
                        Entrar al Sistema
                    </a>
                @endauth
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <h1>📚 Bienvenido a NOTAS 2.0</h1>
        <p>Tu sistema completo para gestionar, organizar y compartir notas de forma eficiente y segura.</p>
        
        <div class="cta-buttons">
            @auth
                <a href="{{ route('filament.admin.pages.dashboard') }}" class="btn btn-primary">
                    Ir al Panel de Control
                </a>
            @else
                <a href="{{ route('login') }}" class="btn btn-primary">
                    Iniciar Sesión
                </a>
                <a href="{{ route('login') }}" class="btn btn-secondary">
                    Explorar Demo
                </a>
            @endauth
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats">
        <div class="stat-box">
            <div class="stat-number">∞</div>
            <div class="stat-label">Notas Ilimitadas</div>
        </div>
        <div class="stat-box">
            <div class="stat-number">🔐</div>
            <div class="stat-label">100% Seguro</div>
        </div>
        <div class="stat-box">
            <div class="stat-number">⚡</div>
            <div class="stat-label">Súper Rápido</div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features">
        <div class="feature-card">
            <div class="feature-icon">✍️</div>
            <h3>Editor Potente</h3>
            <p>Crea y edita tus notas con un editor intuitivo y fácil de usar.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">🏷️</div>
            <h3>Organización</h3>
            <p>Categoriza y etiqueta tus notas para encontrarlas rápidamente.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">🔍</div>
            <h3>Búsqueda Avanzada</h3>
            <p>Encuentra cualquier nota con nuestro poderoso motor de búsqueda.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">👥</div>
            <h3>Colaboración</h3>
            <p>Comparte notas con otros usuarios de forma segura.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">📱</div>
            <h3>Responsive</h3>
            <p>Accede a tus notas desde cualquier dispositivo.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">🚀</div>
            <h3>Rendimiento</h3>
            <p>Velocidad optimizada para la mejor experiencia.</p>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <p>&copy; 2025 NOTAS 2.0. Todos los derechos reservados. Hecho con ❤️</p>
        </div>
    </footer>
</body>
</html>
