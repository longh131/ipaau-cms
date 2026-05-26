<x-filament-panels::page>
    <form wire:submit="save" class="space-y-4">
        {{ $this->form }}
        
        <div class="flex justify-end pt-4">
            <button 
                type="submit" 
                class="inline-flex items-center justify-center px-4 py-2 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700 focus:ring-4 focus:ring-primary-300 transition-colors"
            >
                保存设置
            </button>
        </div>
    </form>
</x-filament-panels::page>