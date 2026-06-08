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
        <span style="margin-left: {{ ($depth - 1) * 20 }}px; display: inline-block;"></span>
        <span class="text-gray-400">├──</span>
    @endif
    
    @if ($hasChildren)
        <button 
            type="button" 
            class="category-toggle px-1 hover:bg-gray-100 rounded transition-colors cursor-pointer"
            data-id="{{ $getRecord()->id }}"
            title="展开/折叠子栏目"
            onclick="toggleCategory(event, this)"
        >
            <span class="text-gray-600 font-bold text-sm">▼</span>
        </button>
    @else
        <span class="w-6"></span>
    @endif
    
    <span class="category-item" data-parent-id="{{ $getRecord()->parent_id }}" data-id="{{ $getRecord()->id }}">
        {{ $getRecord()->name }}
    </span>
</div>

<script>
    function toggleCategory(event, button) {
        event.preventDefault();
        event.stopPropagation();
        
        const parentId = button.getAttribute('data-id');
        const icon = button.querySelector('span');
        
        document.querySelectorAll('.category-item').forEach(item => {
            const itemParentId = item.getAttribute('data-parent-id');
            if (itemParentId == parentId) {
                const row = item.closest('tr');
                if (row.style.display === 'none') {
                    row.style.display = '';
                    icon.textContent = '▼';
                } else {
                    row.style.display = 'none';
                    icon.textContent = '▶';
                }
            }
        });
    }
</script>