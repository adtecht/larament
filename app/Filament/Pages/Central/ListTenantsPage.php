<?php

declare(strict_types=1);

namespace App\Filament\Pages\Central;

use App\Models\Tenant;
use Filament\Pages\Page;
use Filament\Tables\Table;
use Filament\Actions\Action;
use App\Models\Tenant\TenantUser;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Support\Facades\Session;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\Layout\Split;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Concerns\InteractsWithTable;

final class ListTenantsPage extends Page implements HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;

    protected string $view                                   = 'filament.pages.central.list-tenants-page';
    protected static ?string $slug                           = '/';
    protected static ?string $title                          = 'Selecciona tu empresa';
    protected static ?int $navigationSort                    = -1;
    protected static ?string $navigationLabel                = 'Empresas';
    protected static bool $shouldRegisterNavigation          = false;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-office-2';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('crearEmpresa')
                ->label('Crear empresa')
                ->icon('heroicon-m-plus')
                ->color('primary')
                ->modalHeading('Crear empresa')
                ->modalSubmitActionLabel('Crear')
                ->schema([
                    TextInput::make('name')
                        ->label('Nombre de la empresa')
                        ->required()
                        ->maxLength(255),
                ])
                ->action(fn(array $data) => $this->createTenant($data)),
        ];
    }

    public function getViewData(): array
    {
        $tenants = Auth::user()?->tenants()->get() ?? collect();

        return ['tenants' => $tenants];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Tenant::query()
                    ->whereHas('users', fn($q) => $q->whereKey(Auth::id()))
            )
            ->columns([
                Split::make([
                    TextColumn::make('name')
                        ->label('Empresa')
                        ->state(fn(Tenant $record) => $record->name ?? $record->id)
                        ->searchable(query: function (Builder $query, string $search): Builder
                        {
                            return $query->where('data->name', 'like', "%{$search}%")
                                ->orWhere('id', 'like', "%{$search}%");
                        })
                        ->weight('bold'),
                ])->from('md'),
            ])
            ->recordActions([
                Action::make('entrar')
                    ->icon('heroicon-m-arrow-right-end-on-rectangle')
                    ->color('primary')
                    ->action(fn(Tenant $record) => $this->switchTenant((string) $record->id)),
            ]);
    }

    public function switchTenant(string $tenant_id)
    {
        $tenant = Tenant::findOrFail($tenant_id);

        tenancy()->initialize($tenant);
        $central_user = Auth::user();
        if ($central_user)
        {
            TenantUser::firstOrCreate(
                ['email' => $central_user->email],
                [
                    'name'              => $central_user->name,
                    'password'          => $central_user->getAttribute('password'),
                    'email_verified_at' => $central_user->getAttribute('email_verified_at'),
                ],
            );
        }
        tenancy()->end();
        Session::put('tenant_id', $tenant->id);
        return redirect()->to('/');
    }

    public function createTenant(array $data)
    {
        //This is for local testing, for production we need to improve the way of creating tenants
        //Also improve the logic of InitializeTenancyBySession, to add the user to the tenant and not take me to login when creating a tenant for the first time
        $tenant = Tenant::create([
            'name' => $data['name'],
        ]);

        $tenant->users()->attach(Auth::id());
        $central_user = Auth::user();
        tenancy()->initialize($tenant);
        if ($central_user)
        {
            TenantUser::firstOrCreate(
                ['email' => $central_user->email],
                [
                    'name'              => $central_user->name,
                    'password'          => $central_user->getAttribute('password'),
                    'email_verified_at' => $central_user->getAttribute('email_verified_at'),
                ],
            );
        }

        tenancy()->end();

        Session::put('tenant_id', $tenant->id);

        return redirect()->to('/');
    }
}
