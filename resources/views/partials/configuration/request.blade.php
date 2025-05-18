<h2 class="text-lg font-medium text-primary flex items-center gap-2">
    {{ $title  }}
    <x-ui.times
        :count="$dataSyncCount"
        :disabled="$disabled"
        :maxTimes="\App\Models\ScheduledTasks::$MAX_TIMES"
        :warningTimes="\App\Models\ScheduledTasks::$WARNING_TIMES" />
</h2>

<p class="mt-1 text-sm text-secondary">
    {{ $description }}
</p>

<!-- Show the date of the last transaction -->
<p class="mt-1 text-sm text-secondary">
    {{ __('Last update') }}: {{ $last }}
</p>

@if ($dataSyncCount <= \App\Models\ScheduledTasks::$MAX_TIMES || $disabled)
    <div class="pt-4">
        <div x-data="{ showTip: false }" class="relative inline-block">
            <x-links.nav-link
                @mouseenter="showTip = true"
                @mouseleave="showTip = false"
                class="inline-flex items-center px-4 pt-1 leading-5 !text-primary py-1 bg-main3 hover:bg-primary hover:!text-main3 border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest transition ease-in-out duration-150"
                href="{{ $link }}"
            >
                {{ $button }}
            </x-links.nav-link>

            <div
                x-show="showTip"
                x-transition
                class="absolute z-10 bottom-full mb-2 w-64 p-2 text-sm bg-third text-primary rounded shadow-lg"
                style="display: none;">
                {{ __('Remember: You can update a maximum of :times times per day.', ['times' => \App\Models\ScheduledTasks::$MAX_TIMES]) }}
            </div>
        </div>
    </div>
@endif
