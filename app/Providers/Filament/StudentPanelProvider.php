<?php

namespace App\Providers\Filament;

use App\Filament\Student\Pages\Auth\Login;
use App\Filament\Student\Pages\MyGrades;
use App\Models\SchoolSetting;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
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

class StudentPanelProvider extends PanelProvider
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
            ->id('student')
            ->path('student')
            ->login(Login::class)
            ->brandName('')
            ->favicon($favicon)
            ->colors([
                'primary' => Color::Violet,
                'danger' => Color::Rose,
                'warning' => Color::Amber,
                'success' => Color::Green,
            ])
            ->discoverResources(in: app_path('Filament/Student/Resources'), for: 'App\\Filament\\Student\\Resources')
            ->discoverPages(in: app_path('Filament/Student/Pages'), for: 'App\\Filament\\Student\\Pages')
            ->pages([
                MyGrades::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Student/Widgets'), for: 'App\\Filament\\Student\\Widgets')
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
            ->topNavigation()
            ->sidebarCollapsibleOnDesktop(false)
            ->renderHook(
                PanelsRenderHook::TOPBAR_START,
                fn () => Blade::render('<livewire:topbar-info panelType="student" />')
            );
        
        return $panelConfig;
    }
}
