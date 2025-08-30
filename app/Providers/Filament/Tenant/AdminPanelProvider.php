<?php

declare(strict_types=1);

namespace App\Providers\Filament\Tenant;

use Filament\Panel;
use Filament\PanelProvider;
use Filament\Actions\Action;
use Filament\Pages\Dashboard;
use Filament\Support\Enums\Width;
use App\Filament\Pages\Auth\Login;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Support\Enums\Platform;
use Filament\Http\Middleware\Authenticate;
use App\Filament\Widgets\CurrentTenantWidget;
use App\Http\Middleware\RequireSelectedTenant;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Filament\Http\Middleware\AuthenticateSession;
use App\Http\Middleware\InitializeTenancyBySession;
use Filament\Auth\MultiFactor\App\AppAuthentication;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;

final class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->profile()
            ->id('admin')
            ->path('/')
            ->login(Login::class)
            ->maxContentWidth(Width::Full)
            ->sidebarCollapsibleOnDesktop()
            ->resourceEditPageRedirect('index')
            ->resourceCreatePageRedirect('index')
            ->viteTheme('resources/css/filament/theme.css')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->userMenuItems([
                Action::make('change-tenant')
                    ->label('Cambiar empresa')
                    ->icon('heroicon-o-building-office-2')
                    ->action(function ()
                    {
                        session()->forget('tenant_id');
                        return redirect()->to('/welcome');
                    }),
            ])
            ->pages([
                Dashboard::class,
            ])
            ->multiFactorAuthentication(
                AppAuthentication::make()
                    ->recoverable(),
            )
            ->colors([
                'primary' => Color::Blue,
            ])
            ->widgets([
                AccountWidget::class,
                CurrentTenantWidget::class,
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
                InitializeTenancyBySession::class,
            ])
            ->authMiddleware([
                Authenticate::class,
                RequireSelectedTenant::class,
            ])
            ->globalSearchFieldSuffix(fn(): ?string => match (Platform::detect())
            {
                Platform::Windows, Platform::Linux => 'CTRL + K',
                Platform::Mac => '⌘ + K',
                default => null,
            });
    }
}
