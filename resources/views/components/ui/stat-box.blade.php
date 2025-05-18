@props(['icon', 'title', 'value'])

<div class="bg-main2 p-4 rounded-xl shadow flex flex-col gap-2">
    <div class="text-2xl">{{ $icon }}</div>
    <div class="text-sm text-gray-400">{{ $title }}</div>
    <div class="text-xl font-semibold">{{ $value }}</div>
</div>
