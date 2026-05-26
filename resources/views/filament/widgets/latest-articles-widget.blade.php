<x-filament::widget>
    <x-filament::card>
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold">最近文章</h3>
            <a href="{{ route('articles.index') }}" class="text-sm text-primary hover:underline">
                查看全部
            </a>
        </div>
        {{ $this->table }}
    </x-filament::card>
</x-filament::widget>
