<?php

declare(strict_types=1);

namespace App\Providers\Filament\Central;

use Filament\Panel;
use Filament\PanelProvider;
use App\Filament\Pages\Auth\Login;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Http\Middleware\Authenticate;
use Illuminate\Session\Middleware\StartSession;
use App\Filament\Pages\Central\ListTenantsPage;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Filament\Http\Middleware\AuthenticateSession;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;

final class CentralPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->profile()
            ->id('central')
            ->path('welcome')
            ->topNavigation()
            ->login(Login::class)
            ->viteTheme('resources/css/filament/theme.css')
            ->pages([
                ListTenantsPage::class,
            ])
            ->colors([
                'primary' => Color::Blue,
            ])
            ->widgets([
                AccountWidget::class,
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
            ]);
    }
}
