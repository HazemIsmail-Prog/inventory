<div
    x-data="{ show: false }" 
    class="border rounded-lg cursor-pointer shadow-md"
    x-on:mouseenter="show = true" 
    x-on:mouseleave="show = false"
    x-on:click="show = !show"
    >
    <div class="h-32">{{ $slot }}</div>
    <div x-cloak x-show="show" x-collapse.duration.150ms>
        {{ $childList ?? '' }}
    </div>
</div>
