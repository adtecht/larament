<div>
    @if (filament()->getCurrentPanel()->getId() !== 'central')
        <x-filament::badge class="w-max" color="primary">
            <span>{{ tenant('name') ?? tenant('id') }}</span>
        </x-filament::badge>
    @endif
</div>
