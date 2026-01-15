@php
    use App\Models\SchoolSetting;
    
    $settings = SchoolSetting::getAllSettings();
    $schoolName = $settings['school_name'] ?? 'NOTAS 2.0';
    $schoolSlogan = $settings['school_slogan'] ?? '';
    $logo = $settings['school_logo'] ?? null;
    $background = $settings['login_background'] ?? null;
    $showLogo = ($settings['login_show_logo'] ?? '1') === '1';
    $showSlogan = ($settings['login_show_slogan'] ?? '1') === '1';
    $welcomeTitle = $settings['login_welcome_title'] ?? 'Bienvenido';
    $welcomeMessage = $settings['login_welcome_message'] ?? 'Accede a tu portal educativo';
    $footerText = $settings['login_footer_text'] ?? '© ' . date('Y') . ' ' . $schoolName;
    
    // Textos específicos por rol
    $panelTitle = $settings['login_admin_title'] ?? 'Panel Administrativo';
    $panelSubtitle = $settings['login_admin_subtitle'] ?? 'Gestión académica integral';
    $panelColor = 'blue';
    $panelIcon = 'M12 14l9-5-9-5-9 5 9 5z M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z';
@endphp

<div class="min-h-screen flex">
    {{-- Panel Izquierdo - Decorativo --}}
    <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden">
        {{-- Fondo con gradiente o imagen --}}
        @if($background)
            <div class="absolute inset-0">
                <img src="{{ asset('storage/' . $background) }}" 
                     alt="Fondo" 
                     class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-br from-{{ $panelColor }}-900/90 to-{{ $panelColor }}-700/80"></div>
            </div>
        @else
            <div class="absolute inset-0 bg-gradient-to-br from-{{ $panelColor }}-600 via-{{ $panelColor }}-700 to-{{ $panelColor }}-900"></div>
            {{-- Patrón decorativo --}}
            <div class="absolute inset-0 opacity-10">
                <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                    <defs>
                        <pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse">
                            <path d="M 10 0 L 0 0 0 10" fill="none" stroke="white" stroke-width="0.5"/>
                        </pattern>
                    </defs>
                    <rect width="100" height="100" fill="url(#grid)"/>
                </svg>
            </div>
        @endif
        
        {{-- Contenido --}}
        <div class="relative z-10 flex flex-col justify-center items-center w-full p-12 text-white">
            {{-- Logo --}}
            @if($showLogo && $logo)
                <div class="mb-8">
                    <div class="w-32 h-32 bg-white/10 backdrop-blur-sm rounded-2xl p-4 border border-white/20 shadow-2xl">
                        <img src="{{ asset('storage/' . $logo) }}" 
                             alt="{{ $schoolName }}" 
                             class="w-full h-full object-contain">
                    </div>
                </div>
            @elseif($showLogo)
                <div class="mb-8">
                    <div class="w-32 h-32 bg-white/10 backdrop-blur-sm rounded-2xl flex items-center justify-center border border-white/20 shadow-2xl">
                        <svg class="w-16 h-16 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $panelIcon }}"/>
                        </svg>
                    </div>
                </div>
            @endif
            
            {{-- Nombre del colegio --}}
            <h1 class="text-4xl font-bold text-center mb-4 drop-shadow-lg">
                {{ $schoolName }}
            </h1>
            
            {{-- Eslogan --}}
            @if($showSlogan && $schoolSlogan)
                <p class="text-xl text-white/80 text-center mb-12 max-w-md">
                    {{ $schoolSlogan }}
                </p>
            @endif
            
            {{-- Bienvenida --}}
            <div class="text-center max-w-lg">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-white/10 rounded-full mb-6 border border-white/20">
                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                    </svg>
                </div>
                <h2 class="text-2xl font-semibold mb-3">{{ $welcomeTitle }}</h2>
                <p class="text-white/70">{{ $welcomeMessage }}</p>
            </div>
            
            {{-- Decoración inferior --}}
            <div class="absolute bottom-0 left-0 right-0 h-32 bg-gradient-to-t from-black/20 to-transparent"></div>
            
            {{-- Footer --}}
            <div class="absolute bottom-6 text-center text-white/50 text-sm">
                {{ $footerText }}
            </div>
        </div>
    </div>
    
    {{-- Panel Derecho - Formulario --}}
    <div class="w-full lg:w-1/2 flex items-center justify-center p-8 bg-gray-50 dark:bg-gray-900">
        <div class="w-full max-w-md">
            {{-- Logo móvil --}}
            <div class="lg:hidden text-center mb-8">
                @if($logo)
                    <img src="{{ asset('storage/' . $logo) }}" 
                         alt="{{ $schoolName }}" 
                         class="w-20 h-20 mx-auto object-contain mb-4">
                @endif
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $schoolName }}</h1>
            </div>
            
            {{-- Card de Login --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                {{-- Header del card --}}
                <div class="bg-gradient-to-r from-{{ $panelColor }}-500 to-{{ $panelColor }}-600 px-8 py-6 text-white">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $panelIcon }}"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold">{{ $panelTitle }}</h2>
                            <p class="text-white/80 text-sm">{{ $panelSubtitle }}</p>
                        </div>
                    </div>
                </div>
                
                {{-- Formulario --}}
                <div class="p-8">
                    {{ $slot }}
                </div>
            </div>
            
            {{-- Links adicionales --}}
            <div class="mt-6 text-center text-sm text-gray-500 dark:text-gray-400">
                <p>¿Necesitas ayuda? Contacta al administrador</p>
            </div>
            
            {{-- Footer móvil --}}
            <div class="lg:hidden mt-8 text-center text-xs text-gray-400">
                {{ $footerText }}
            </div>
        </div>
    </div>
</div>
