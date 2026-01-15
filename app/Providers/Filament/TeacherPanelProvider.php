<?php

namespace App\Providers\Filament;

use App\Filament\Teacher\Pages\Auth\Login;
use App\Filament\Teacher\Pages\Dashboard;
use App\Models\SchoolSetting;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
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
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class TeacherPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        // Obtener logo y favicon de configuración
        $brandLogo = null;
        $favicon = asset('favicon.ico');
        
        if (Schema::hasTable('school_settings')) {
            try {
                $logo = SchoolSetting::get('school_logo');
                $brandLogo = $logo ? asset('storage/' . $logo) : null;
                
                $faviconSetting = SchoolSetting::get('school_favicon');
                $favicon = $faviconSetting ? asset('storage/' . $faviconSetting) : asset('favicon.ico');
            } catch (\Exception $e) {}
        }
        
        $panelConfig = $panel
            ->id('teacher')
            ->path('teacher')
            ->login(Login::class)
            ->brandName('')
            ->favicon($favicon)
            ->colors([
                'primary' => Color::Emerald,
            ])
            ->discoverResources(in: app_path('Filament/Teacher/Resources'), for: 'App\\Filament\\Teacher\\Resources')
            ->discoverPages(in: app_path('Filament/Teacher/Pages'), for: 'App\\Filament\\Teacher\\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Teacher/Widgets'), for: 'App\\Filament\\Teacher\\Widgets')
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
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->renderHook(
                PanelsRenderHook::TOPBAR_START,
                fn () => Blade::render('<livewire:topbar-info panelType="teacher" />')
            );
        
        return $panelConfig;
    }
}
