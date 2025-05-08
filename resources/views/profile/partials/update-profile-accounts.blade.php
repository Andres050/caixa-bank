<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Scheduled tasks') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __("Update your account's data (transactions and balances).") }}
        </p>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __("Max schedule times is") . ' ' . \App\Models\ScheduledTasks::$MAX_TIMES }}.
        </p>
    </header>

    <form method="post" action="{{ route('profile.accounts.schedule') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="schedule_times" :value="__('Schedule Times')" />
            <x-text-input id="schedule_times" name="schedule_times" type="number" max="{{ \App\Models\ScheduledTasks::$MAX_TIMES }}" class="mt-1 block w-full" :value="old('schedule_times', $user->schedule_times)" required autofocus autocomplete="schedule_times" />
            <x-input-error class="mt-2" :messages="$errors->get('schedule_times')" />
        </div>

        @if (count($user->schedule) > 0)
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                @foreach($user->schedule as $schedule)
                    <div>
                        <x-input-label for="schedule_time_{{ $loop->index }}" :value="__('Schedule Time') . ' ' . ($loop->index + 1)" />
                        <x-text-input id="schedule_time_{{ $loop->index }}" name="times[]" type="time" class="mt-1 block w-full" :value="$schedule->hour" required autofocus autocomplete="schedule_time_{{ $loop->index }}" />
                        <x-input-error class="mt-2" :messages="$errors->get('schedule_time_' . $loop->index)" />
                    </div>
                @endforeach
            </div>
        @else
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                @for($i = 0; $user->schedule_times > $i; $i++)
                    <div>
                        <x-input-label for="schedule_time_{{ $i }}" :value="__('Schedule Time') . ' ' . ($i + 1)" />
                        <x-text-input id="schedule_time_{{ $i }}" name="times[]" type="time" class="mt-1 block w-full" required autofocus autocomplete="schedule_time_{{ $i }}" />
                        <x-input-error class="mt-2" :messages="$errors->get('schedule_time_' . $i)" />
                    </div>
                @endfor
            </div>
        @endif

        <div @if ($user->schedule_times <= 0) class="!mt-0" @endif>
            <x-input-label for="execute_login" :value="__('Execute on login?')" />
            <input type="checkbox" name="execute_login" id="execute_login" @if ($user->execute_login) checked @endif />
            <x-input-error class="mt-2" :messages="$errors->get('execute_login')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>
            @if (session('status') === 'bank-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600 dark:text-gray-400"
                >{{ __('Saved.') }}</p>
            @endif

            <div>
                <x-nav-link :href="route('bank.clock')" :active="request()->routeIs('bank.clock')">
                    {{ __('Go to Clock') }}
                </x-nav-link>
            </div>
        </div>
    </form>
</section>
