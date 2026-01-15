<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use App\Filament\Pages\Auth\Login;
use App\Models\SchoolSetting;
use Illuminate\Support\Facades\Schema;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        // Obtener configuraciones si la tabla existe
        $brandName = 'NOTAS 2.0';
        $brandLogo = null;
        $favicon = asset('favicon.ico');
        $primaryColor = Color::Blue;
        
        if (Schema::hasTable('school_settings')) {
            try {
                $brandName = SchoolSetting::get('school_name', 'NOTAS 2.0');
                $logo = SchoolSetting::get('school_logo');
                $brandLogo = $logo ? asset('storage/' . $logo) : null;
                
                $faviconSetting = SchoolSetting::get('school_favicon');
                $favicon = $faviconSetting ? asset('storage/' . $faviconSetting) : asset('favicon.ico');
                
                // Convertir color hex a array RGB para Filament
                $primaryHex = SchoolSetting::get('primary_color', '#3b82f6');
                $primaryColor = $this->hexToFilamentColor($primaryHex);
            } catch (\Exception $e) {
                // Usar valores por defecto si hay error
            }
        }

        $panelConfig = $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login(Login::class)
            ->brandName('')
            ->favicon($favicon)
            ->colors([
                'primary' => $primaryColor,
                'secondary' => Color::Purple,
                'danger' => Color::Red,
                'gray' => Color::Slate,
                'info' => Color::Blue,
                'success' => Color::Green,
                'warning' => Color::Amber,
            ])
            ->font('Instrument Sans')
            ->darkMode(true)
            ->sidebarCollapsibleOnDesktop()
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->navigationGroups([
                'Académico',
                'Gestión',
                'Sistema',
            ])
            ->renderHook(
                PanelsRenderHook::TOPBAR_START,
                fn () => Blade::render('<livewire:topbar-info panelType="admin" />')
            );
        
        return $panelConfig;
    }
    
    /**
     * Convierte un color hex a formato Filament
     */
    private function hexToFilamentColor(string $hex): array
    {
        $hex = ltrim($hex, '#');
        
        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }
        
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        
        return Color::rgb("rgb({$r}, {$g}, {$b})");
    }
}
