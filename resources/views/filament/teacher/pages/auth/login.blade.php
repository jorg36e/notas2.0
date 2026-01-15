<x-filament-panels::page.simple>
    @php
        $settings = \App\Models\SchoolSetting::getAllSettings();
        $schoolName = $settings['school_name'] ?? 'NOTAS 2.0';
        $schoolSlogan = $settings['school_slogan'] ?? '';
        $logo = $settings['school_logo'] ?? null;
        $background = $settings['login_background'] ?? null;
        $showLogo = ($settings['login_show_logo'] ?? '1') === '1';
        $showSlogan = ($settings['login_show_slogan'] ?? '1') === '1';
        $welcomeTitle = $settings['login_welcome_title'] ?? 'Bienvenido';
        $welcomeMessage = $settings['login_welcome_message'] ?? 'Accede a tu portal educativo';
        $footerText = $settings['login_footer_text'] ?? '© ' . date('Y') . ' ' . $schoolName;
        $panelTitle = $settings['login_teacher_title'] ?? 'Portal Docente';
        $panelSubtitle = $settings['login_teacher_subtitle'] ?? 'Gestiona tus clases y calificaciones';
    @endphp

    <style>
        :root {
            --login-primary: #059669;
            --login-primary-dark: #047857;
            --login-primary-light: #10b981;
            --login-accent: #34d399;
        }
        
        * { box-sizing: border-box; }
        
        .fi-simple-layout { background: transparent !important; }
        .fi-simple-main { background: transparent !important; box-shadow: none !important; max-width: none !important; padding: 0 !important; }
        .fi-simple-main > div { max-width: none !important; }
        
        .login-wrapper {
            min-height: 100vh;
            display: flex;
            position: fixed;
            inset: 0;
            z-index: 50;
            font-family: 'Inter', system-ui, sans-serif;
        }
        
        /* ===== PANEL IZQUIERDO ===== */
        .login-hero {
            display: none;
            width: 55%;
            position: relative;
            overflow: hidden;
        }
        
        @media (min-width: 1024px) {
            .login-hero { display: flex; }
            .login-form-side { width: 45% !important; }
        }
        
        .hero-bg {
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, var(--login-primary-dark) 0%, var(--login-primary) 50%, var(--login-primary-light) 100%);
        }
        
        .hero-bg-image {
            position: absolute;
            inset: 0;
        }
        
        .hero-bg-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .hero-bg-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(4, 120, 87, 0.93) 0%, rgba(5, 150, 105, 0.88) 50%, rgba(16, 185, 129, 0.85) 100%);
        }
        
        /* Círculos decorativos animados */
        .hero-shapes {
            position: absolute;
            inset: 0;
            overflow: hidden;
            z-index: 1;
        }
        
        .shape {
            position: absolute;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.05) 100%);
            animation: floatShape 25s infinite ease-in-out;
        }
        
        .shape-1 { width: 500px; height: 500px; top: -150px; left: -150px; animation-delay: 0s; }
        .shape-2 { width: 350px; height: 350px; bottom: -100px; right: -100px; animation-delay: -8s; }
        .shape-3 { width: 200px; height: 200px; top: 45%; left: 55%; animation-delay: -16s; }
        .shape-4 { width: 120px; height: 120px; top: 70%; left: 15%; animation-delay: -12s; }
        .shape-5 { width: 80px; height: 80px; top: 20%; right: 20%; animation-delay: -20s; }
        
        @keyframes floatShape {
            0%, 100% { transform: translate(0, 0) scale(1); }
            25% { transform: translate(30px, -30px) scale(1.05); }
            50% { transform: translate(-20px, 20px) scale(0.95); }
            75% { transform: translate(25px, 15px) scale(1.02); }
        }
        
        /* Patrón de puntos */
        .hero-pattern {
            position: absolute;
            inset: 0;
            z-index: 2;
            opacity: 0.4;
            background-image: radial-gradient(rgba(255,255,255,0.15) 1px, transparent 1px);
            background-size: 30px 30px;
        }
        
        /* Contenido del hero */
        .hero-content {
            position: relative;
            z-index: 10;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            width: 100%;
            padding: 3rem;
            color: white;
            text-align: center;
        }
        
        .hero-logo {
            margin-bottom: 2.5rem;
            animation: fadeSlideDown 0.8s ease-out;
        }
        
        .hero-logo-box {
            width: 130px;
            height: 130px;
            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 28px;
            padding: 18px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 30px 60px -20px rgba(0, 0, 0, 0.3), inset 0 1px 0 rgba(255,255,255,0.1);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .hero-logo-box:hover {
            transform: translateY(-8px) scale(1.03);
            box-shadow: 0 40px 80px -25px rgba(0, 0, 0, 0.4), inset 0 1px 0 rgba(255,255,255,0.15);
        }
        
        .hero-logo-box img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        
        .hero-logo-placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .hero-logo-placeholder svg {
            width: 65px;
            height: 65px;
            color: white;
            opacity: 0.9;
        }
        
        .hero-title {
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 0.75rem;
            text-shadow: 0 4px 30px rgba(0, 0, 0, 0.3);
            letter-spacing: -0.02em;
            animation: fadeSlideUp 0.8s ease-out 0.15s both;
        }
        
        .hero-slogan {
            font-size: 1.15rem;
            color: rgba(255, 255, 255, 0.85);
            max-width: 380px;
            line-height: 1.7;
            font-weight: 400;
            animation: fadeSlideUp 0.8s ease-out 0.3s both;
        }
        
        .hero-divider {
            width: 80px;
            height: 4px;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.7), transparent);
            border-radius: 2px;
            margin: 2.5rem 0;
            animation: fadeIn 0.8s ease-out 0.45s both;
        }
        
        .hero-welcome {
            animation: fadeSlideUp 0.8s ease-out 0.6s both;
        }
        
        .hero-welcome-icon {
            width: 70px;
            height: 70px;
            background: rgba(255, 255, 255, 0.12);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            border: 1px solid rgba(255, 255, 255, 0.15);
            box-shadow: 0 10px 40px -10px rgba(0,0,0,0.2);
        }
        
        .hero-welcome-icon svg {
            width: 32px;
            height: 32px;
            opacity: 0.95;
        }
        
        .hero-welcome-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        .hero-welcome-text {
            color: rgba(255, 255, 255, 0.7);
            font-size: 1rem;
        }
        
        .hero-footer {
            position: absolute;
            bottom: 2rem;
            left: 0;
            right: 0;
            text-align: center;
            color: rgba(255, 255, 255, 0.45);
            font-size: 0.875rem;
        }
        
        /* ===== PANEL DERECHO (FORMULARIO) ===== */
        .login-form-side {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
            position: relative;
            overflow: hidden;
        }
        
        .dark .login-form-side {
            background: linear-gradient(180deg, #1f2937 0%, #111827 100%);
        }
        
        /* Decoración de fondo del formulario */
        .form-bg-decor {
            position: absolute;
            width: 400px;
            height: 400px;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(5, 150, 105, 0.05) 0%, rgba(16, 185, 129, 0.08) 100%);
            top: -100px;
            right: -100px;
            z-index: 0;
        }
        
        .form-bg-decor-2 {
            position: absolute;
            width: 300px;
            height: 300px;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(5, 150, 105, 0.03) 0%, rgba(16, 185, 129, 0.06) 100%);
            bottom: -80px;
            left: -80px;
            z-index: 0;
        }
        
        .login-form-content {
            width: 100%;
            max-width: 440px;
            position: relative;
            z-index: 1;
        }
        
        /* Header móvil */
        .mobile-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        @media (min-width: 1024px) {
            .mobile-header { display: none; }
        }
        
        .mobile-logo {
            width: 75px;
            height: 75px;
            margin: 0 auto 1rem;
            object-fit: contain;
        }
        
        .mobile-title {
            font-size: 1.625rem;
            font-weight: 700;
            color: #1f2937;
        }
        
        .dark .mobile-title {
            color: #f9fafb;
        }
        
        /* Card de Login */
        .login-card {
            background: white;
            border-radius: 24px;
            box-shadow: 0 25px 80px -20px rgba(0, 0, 0, 0.12), 0 0 0 1px rgba(0, 0, 0, 0.03);
            overflow: hidden;
            animation: fadeSlideUp 0.7s ease-out;
        }
        
        .dark .login-card {
            background: #1f2937;
            box-shadow: 0 25px 80px -20px rgba(0, 0, 0, 0.5), 0 0 0 1px rgba(255, 255, 255, 0.05);
        }
        
        .card-header {
            background: linear-gradient(135deg, var(--login-primary) 0%, var(--login-primary-light) 100%);
            padding: 1.75rem 2rem;
            position: relative;
            overflow: hidden;
        }
        
        .card-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -30%;
            width: 200px;
            height: 200px;
            background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 70%);
            border-radius: 50%;
        }
        
        .card-header::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -20%;
            width: 150px;
            height: 150px;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            border-radius: 50%;
        }
        
        .card-header-content {
            position: relative;
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .card-header-icon {
            width: 58px;
            height: 58px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 8px 20px -8px rgba(0,0,0,0.2);
        }
        
        .card-header-icon svg {
            width: 28px;
            height: 28px;
            color: white;
        }
        
        .card-header-text h2 {
            font-size: 1.4rem;
            font-weight: 700;
            color: white;
            margin-bottom: 0.25rem;
        }
        
        .card-header-text p {
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.85);
            font-weight: 400;
        }
        
        .card-body {
            padding: 2rem;
        }
        
        /* Estilos del formulario */
        .card-body .fi-fo-field-wrp { margin-bottom: 1.25rem; }
        
        .card-body .fi-input-wrp {
            border-radius: 14px !important;
            border: 2px solid #e5e7eb !important;
            transition: all 0.25s ease !important;
            overflow: hidden;
        }
        
        .card-body .fi-input-wrp:focus-within {
            border-color: var(--login-primary) !important;
            box-shadow: 0 0 0 4px rgba(5, 150, 105, 0.1) !important;
        }
        
        .dark .card-body .fi-input-wrp {
            border-color: #374151 !important;
            background: #111827 !important;
        }
        
        .dark .card-body .fi-input-wrp:focus-within {
            border-color: var(--login-primary-light) !important;
            box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.15) !important;
        }
        
        .card-body .fi-input {
            padding: 0.9rem 1rem !important;
            font-size: 1rem !important;
        }
        
        .card-body .fi-btn-primary {
            width: 100%;
            border-radius: 14px !important;
            padding: 0.9rem 1.5rem !important;
            font-size: 1rem !important;
            font-weight: 600 !important;
            background: linear-gradient(135deg, var(--login-primary) 0%, var(--login-primary-light) 100%) !important;
            border: none !important;
            box-shadow: 0 8px 25px -8px rgba(5, 150, 105, 0.4) !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
        }
        
        .card-body .fi-btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 35px -10px rgba(5, 150, 105, 0.5) !important;
        }
        
        .card-body .fi-btn-primary:active {
            transform: translateY(0);
        }
        
        /* Sección de ayuda */
        .help-section {
            margin-top: 1.75rem;
            text-align: center;
        }
        
        .help-text {
            font-size: 0.9rem;
            color: #6b7280;
        }
        
        .dark .help-text { color: #9ca3af; }
        
        .help-link {
            color: var(--login-primary);
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s;
        }
        
        .help-link:hover {
            color: var(--login-primary-dark);
            text-decoration: underline;
        }
        
        /* Footer móvil */
        .mobile-footer {
            margin-top: 2rem;
            text-align: center;
            font-size: 0.8rem;
            color: #9ca3af;
        }
        
        @media (min-width: 1024px) {
            .mobile-footer { display: none; }
        }
        
        /* Animaciones */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        @keyframes fadeSlideUp {
            from { opacity: 0; transform: translateY(25px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes fadeSlideDown {
            from { opacity: 0; transform: translateY(-25px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>

    <div class="login-wrapper">
        <!-- Panel Izquierdo - Hero -->
        <div class="login-hero">
            @if($background)
                <div class="hero-bg-image">
                    <img src="{{ asset('storage/' . $background) }}" alt="Fondo">
                </div>
                <div class="hero-bg-overlay"></div>
            @else
                <div class="hero-bg"></div>
            @endif
            
            <div class="hero-shapes">
                <div class="shape shape-1"></div>
                <div class="shape shape-2"></div>
                <div class="shape shape-3"></div>
                <div class="shape shape-4"></div>
                <div class="shape shape-5"></div>
            </div>
            
            <div class="hero-pattern"></div>
            
            <div class="hero-content">
                @if($showLogo)
                    <div class="hero-logo">
                        <div class="hero-logo-box">
                            @if($logo)
                                <img src="{{ asset('storage/' . $logo) }}" alt="{{ $schoolName }}">
                            @else
                                <div class="hero-logo-placeholder">
                                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    </svg>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
                
                <h1 class="hero-title">{{ $schoolName }}</h1>
                
                @if($showSlogan && $schoolSlogan)
                    <p class="hero-slogan">{{ $schoolSlogan }}</p>
                @endif
                
                <div class="hero-divider"></div>
                
                <div class="hero-welcome">
                    <div class="hero-welcome-icon">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                        </svg>
                    </div>
                    <h2 class="hero-welcome-title">{{ $welcomeTitle }}</h2>
                    <p class="hero-welcome-text">{{ $welcomeMessage }}</p>
                </div>
                
                <div class="hero-footer">{{ $footerText }}</div>
            </div>
        </div>
        
        <!-- Panel Derecho - Formulario -->
        <div class="login-form-side">
            <div class="form-bg-decor"></div>
            <div class="form-bg-decor-2"></div>
            
            <div class="login-form-content">
                <div class="mobile-header">
                    @if($logo)
                        <img src="{{ asset('storage/' . $logo) }}" alt="{{ $schoolName }}" class="mobile-logo">
                    @endif
                    <h1 class="mobile-title">{{ $schoolName }}</h1>
                </div>
                
                <div class="login-card">
                    <div class="card-header">
                        <div class="card-header-content">
                            <div class="card-header-icon">
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                            </div>
                            <div class="card-header-text">
                                <h2>{{ $panelTitle }}</h2>
                                <p>{{ $panelSubtitle }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <x-filament-panels::form wire:submit="authenticate">
                            {{ $this->form }}

                            <x-filament-panels::form.actions
                                :actions="$this->getCachedFormActions()"
                                :full-width="$this->hasFullWidthFormActions()"
                            />
                        </x-filament-panels::form>
                    </div>
                </div>
                
                <div class="help-section">
                    <p class="help-text">
                        ¿Problemas para acceder? <a href="#" class="help-link">Contacta soporte</a>
                    </p>
                </div>
                
                <div class="mobile-footer">{{ $footerText }}</div>
            </div>
        </div>
    </div>
</x-filament-panels::page.simple>
