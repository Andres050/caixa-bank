<div {{ $attributes }}>
    <div class="{{ $class ?? 'max-w-7xl mx-auto sm:px-6 lg:px-8 pt-4' }} ">
        <h2 class="text-lg font-medium text-gray-100">
            {{ $title }}
        </h2>

        <p class="mt-1 text-sm text-gray-400">
            {{ $description }}
        </p>
    </div>

    @if (isset($slot))
        {{ $slot }}
    @endif

    @if (isset($button) && isset($link))
        <div class="{{ $class ?? 'max-w-7xl mx-auto sm:px-6 lg:px-8 pb-4' }} pt-4">
            <x-links.nav-link class="inline-flex items-center px-4 py-1 bg-primary text-third  hover:text-primary hover:bg-secondary hover:border-secondary border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest transition ease-in-out duration-150" :href="$link">
                {{ $button }}
            </x-links.nav-link>
        </div>
    @endif
</div>
