<x-filament::widget>
    <x-filament::section>
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between" x-data="{
            name: @js(tenant('name') ?? __('Sin empresa')),
            id: @js(tenant('id')),
            copied: false,
            async copy() {
                const text = `Empresa: ${this.name} | ID: ${this.id ?? ''}`;
                try {
                    await navigator.clipboard.writeText(text);
                } catch (_) {
                    const ta = document.createElement('textarea');
                    ta.value = text;
                    ta.setAttribute('readonly', '');
                    ta.style.position = 'fixed';
                    ta.style.left = '-9999px';
                    document.body.appendChild(ta);
                    ta.select();
                    document.execCommand('copy');
                    document.body.removeChild(ta);
                }
                this.copied = true;
                setTimeout(() => this.copied = false, 1200);
            }
        }">
            <div class="flex items-start gap-3 min-w-0 sm:flex-1">
                <div class="shrink-0">
                    <x-filament::icon-button icon="heroicon-o-clipboard-document" color="gray" size="lg"
                        x-show="!copied" x-cloak x-tooltip.placement.top.raw="{{ __('Copiar') }}" x-on:click="copy()" />
                    <x-filament::icon-button icon="heroicon-o-document-duplicate" color="gray" size="lg"
                        x-show="copied" x-cloak x-tooltip.placement.top.raw="{{ __('Copiado') }}" x-on:click="copy()" />
                </div>

                <div class="min-w-0">
                    <p class="text-sm sm:text-base font-semibold text-gray-950 dark:text-white truncate">
                        {{ __('Empresa') }}: <span
                            class="font-semibold">{{ tenant('name') ?? __('Sin empresa') }}</span>
                    </p>
                    <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 truncate">
                        <span class="font-medium">ID:</span>
                        <span class="break-all">{{ tenant('id') }}</span>
                    </p>
                </div>
            </div>

            <x-filament::button icon="heroicon-o-building-office-2" color="gray" class="sm:w-auto justify-center"
                wire:click="changeTenant">
                {{ __('Cambiar de empresa') }}
            </x-filament::button>
        </div>
    </x-filament::section>
</x-filament::widget>
