<?php

declare(strict_types=1);

namespace App\Providers;

use Filament\Tables\Table;
use Illuminate\Support\ServiceProvider;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Contracts\View\View;

final class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        FilamentView::registerRenderHook(
            PanelsRenderHook::GLOBAL_SEARCH_BEFORE,
            fn(): View => view('components.tenant.name'),
        );

        $this->configureTable();
    }

    private function configureTable(): void
    {
        Table::configureUsing(function (Table $table): void
        {
            $table->striped()
                ->deferLoading();
        });
    }
}
