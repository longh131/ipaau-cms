<div class="flex items-center gap-1">
    @php
        $depth = 0;
        $parentId = $getRecord()->parent_id;
        while ($parentId != 0) {
            $parent = \App\Models\Category::find($parentId);
            if (!$parent) break;
            $depth++;
            $parentId = $parent->parent_id;
        }
        $hasChildren = \App\Models\Category::where('parent_id', $getRecord()->id)->exists();
    @endphp
    
    @if ($depth > 0)
        <span style="margin-left: {{ $depth * 20 }}px; display: inline-block;"></span>
    @endif
    
    @if ($hasChildren)
        <button 
            type="button" 
            class="category-toggle p-1 hover:bg-gray-100 rounded transition-colors"
            data-id="{{ $getRecord()->id }}"
            title="展开/折叠子栏目"
        >
            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>
    @else
        <span class="w-6"></span>
    @endif
    
    <span class="category-item" data-parent-id="{{ $getRecord()->parent_id }}" data-id="{{ $getRecord()->id }}">
        {{ $getRecord()->name }}
    </span>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.category-toggle').forEach(button => {
                button.addEventListener('click', function() {
                    const parentId = this.getAttribute('data-id');
                    const icon = this.querySelector('svg');
                    
                    document.querySelectorAll('.category-item').forEach(item => {
                        const itemParentId = item.getAttribute('data-parent-id');
                        if (itemParentId == parentId) {
                            const row = item.closest('tr');
                            if (row.style.display === 'none') {
                                row.style.display = '';
                                icon.style.transform = 'rotate(0deg)';
                            } else {
                                row.style.display = 'none';
                                icon.style.transform = 'rotate(-90deg)';
                            }
                        }
                    });
                });
            });
        });
    </script>
@endpush