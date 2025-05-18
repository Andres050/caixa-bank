<section>
    <header>
        <h2 class="text-lg font-medium text-primary">
            {{ __('Scheduled tasks') }}
        </h2>

        <p class="mt-1 text-sm text-secondary">
            {{ __("Update your account's data (transactions and balances) on x hours.") }}
        </p>
    </header>

    <form method="post" action="{{ route('profile.accounts.schedule') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-inputs.input-label for="schedule_times" :value="__('Schedule Times')" />
            <x-inputs.text-input id="schedule_times" name="schedule_times" type="number" max="{{ \App\Models\ScheduledTasks::$MAX_TIMES }}" class="mt-1 block w-full" :value="old('schedule_times', $user->schedule_times)" required autocomplete="schedule_times" />
            <x-inputs.input-error class="mt-2" :messages="$errors->get('schedule_times')" />
        </div>

        @if (count($user->schedule) > 0)
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                @foreach($user->schedule as $schedule)
                    <div>
                        <x-inputs.input-label for="schedule_time_{{ $loop->index }}" :value="__('Schedule Time') . ' ' . ($loop->index + 1)" />
                        <x-inputs.text-input id="schedule_time_{{ $loop->index }}" name="times[]" type="time" class="mt-1 block w-full" :value="$schedule->hour" autocomplete="schedule_time_{{ $loop->index }}" />
                        <x-inputs.input-error class="mt-2" :messages="$errors->get('schedule_time_' . $loop->index)" />
                    </div>
                @endforeach
            </div>
        @else
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                @for($i = 0; $user->schedule_times > $i; $i++)
                    <div>
                        <x-inputs.input-label for="schedule_time_{{ $i }}" :value="__('Schedule Time') . ' ' . ($i + 1)" />
                        <x-inputs.text-input id="schedule_time_{{ $i }}" name="times[]" type="time" class="mt-1 block w-full" autocomplete="schedule_time_{{ $i }}" />
                        <x-inputs.input-error class="mt-2" :messages="$errors->get('schedule_time_' . $i)" />
                    </div>
                @endfor
            </div>
        @endif

        <div @if ($user->schedule_times <= 0) class="!mt-0" @endif>
            <x-inputs.input-label for="execute_login" :value="__('Execute on login?')" />
            <x-inputs.checkbox id="execute_login" :active="$user->execute_login" name="execute_login" class="mt-2" />
            <x-inputs.input-error class="mt-2" :messages="$errors->get('execute_login')" />
        </div>

        <div class="flex items-center gap-4">
            <x-buttons.primary-button>{{ __('Save') }}</x-buttons.primary-button>
            @if (session('status') === 'bank-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-secondary"
                >{{ __('Saved.') }}</p>
            @endif

            <div>
                <x-links.nav-link :href="route('dashboard.clock')" :active="request()->routeIs('dashboard.clock')">
                    {{ __('Go to Clock') }}
                </x-links.nav-link>
            </div>
        </div>
    </form>
</section>
