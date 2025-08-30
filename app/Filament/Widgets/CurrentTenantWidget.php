<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\Tenant;
use Filament\Widgets\Widget;

final class CurrentTenantWidget extends Widget
{
    protected  string $view = 'filament.widgets.current-tenant-widget';

    public function changeTenant()
    {
        session()->forget('tenant_id');

        return redirect()->to('/welcome');
    }
}
