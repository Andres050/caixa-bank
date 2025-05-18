<div class="bg-main2 p-4 rounded-xl shadow flex justify-between flex-col {{ $containerClass ?? '' }}">
    <div>
        <h2 class="text-xl mb-4">{{ $title }}</h2>
        <canvas id="{{ $id }}" class="{{ $class ?? '' }}" {{ $attributes }}></canvas>
    </div>
    <div x-data="{ showTip: false }" class="relative inline-block mt-4">
        <x-links.nav-link
            @click="showTip = !showTip"
            class="inline-flex items-center px-4 pt-1 border-b-2 leading-5 !text-primary hover:!text-main3 hover:bg-primary  hover:border-primary py-1 bg-main3 border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest focus:ring-2 transition ease-in-out duration-150"
        >
            {{ __("Show Legend") }}
        </x-links.nav-link>

        <div
            x-show="showTip"
            x-transition
            style="display: none;"
        >
            <div id="{{ $id }}-legend" class="legend-container"></div>
        </div>
    </div>
</div>
