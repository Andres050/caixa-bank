<div {{ $attributes->merge(['class' => 'sm:flex sm:items-center']) }}>
    <x-inputs.dropdown align="right" width="auto">
        <x-slot name="trigger">
            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-secondary bg-main1 hover:text-primary transition ease-in-out duration-150">
                <div class="uppercase">{{ app()->getLocale() }}</div>

                <div class="ms-1">
                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </div>
            </button>
        </x-slot>

        <x-slot name="content">
            @foreach(['es', 'en'] as $lang)
                @if ($lang === app()->getLocale())
                    @continue
                @endif
                <x-inputs.dropdown-link href="{{ route('lang.switch', $lang) }}" class="uppercase !w-12">
                    {{ $lang }}
                </x-inputs.dropdown-link>
            @endforeach
        </x-slot>
    </x-inputs.dropdown>
</div>
