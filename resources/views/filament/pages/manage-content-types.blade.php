{{-- resources/views/filament/pages/manage-content-types.blade.php --}}
<x-filament-panels::page>
    <x-filament::section>
        <x-slot name="heading">
            内容类型管理
        </x-slot>
        <x-slot name="description">
            勾选需要启用的内容类型，对应的菜单和资源管理将自动出现。
        </x-slot>

        <div class="space-y-4" wire:sortable>
            @foreach(\App\Models\Category::getTypeOptions() as $key => $label)
                <label class="flex items-center gap-3">
                    <input type="checkbox" wire:model.live="enabledTypes" value="{{ $key }}" class="w-4 h-4 rounded border-gray-300 text-primary-600 shadow-sm focus:ring-primary-500" />
                    <span class="text-sm text-gray-900 dark:text-white">{{ $label }}</span>
                </label>
            @endforeach
        </div>

        <div class="mt-6">
            <x-filament::button wire:click="save">
                保存并刷新菜单
            </x-filament::button>
        </div>

    </x-filament::section>
</x-filament-panels::page>