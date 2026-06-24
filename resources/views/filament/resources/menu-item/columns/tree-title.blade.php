<div class="flex items-center gap-1">
    <span style="width: {{ $depth * 24 }}px; display: inline-block;"></span>
    
    @if ($hasChildren)
        <button 
            type="button" 
            class="menu-item-toggle w-5 h-5 flex items-center justify-center cursor-pointer hover:bg-gray-100 rounded transition-colors"
            data-id="{{ $recordId }}"
            onclick="event.stopPropagation(); event.preventDefault(); toggleMenuItem(event, this);"
            onmousedown="event.stopPropagation(); event.preventDefault();"
        >
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-500">
                <polyline points="6 9 12 15 18 9"></polyline>
            </svg>
        </button>
    @else
        <span class="w-5"></span>
    @endif
    
    <span class="menu-item-title flex-1 {{ $isActive ? '' : 'text-gray-400' }}" data-id="{{ $recordId }}" data-depth="{{ $depth }}" data-parent-id="{{ $parentId ?? '' }}">
        {{ $title }}
    </span>
</div>