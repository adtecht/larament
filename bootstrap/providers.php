<?php

declare(strict_types=1);

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\TenancyServiceProvider::class,
    App\Providers\Filament\Tenant\AdminPanelProvider::class,
    App\Providers\Filament\Central\CentralPanelProvider::class,
];
