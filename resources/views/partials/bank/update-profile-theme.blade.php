<section>
    <header>
        <h2 class="text-lg font-medium text-primary">
            {{ __('Theme Active') }}
        </h2>

        <p class="mt-1 text-sm text-secondary">
            {{ __("Update your theme for your current user") }}
        </p>
    </header>

    <form method="post" action="{{ route('profile.bank.theme') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div class="grid grid-cols-2 gap-4">
            <x-inputs.input type="color" name="theme[main3]" id="theme[main3]" :value="$user->themeMain3" label="{{ __('Main Color') }}" />
            <div></div>
            <x-inputs.input type="color" name="theme[navActive]" id="theme[navActive]" :value="$user->themeNavActive" label="{{ __('Nav Active Color') }}" />
            <x-inputs.input type="color" name="theme[navActiveBg]" id="theme[navActiveBg]" :value="$user->themeNavActiveBg" label="{{ __('Nav Active Background Color') }}" />
        </div>

        <div class="flex items-center gap-4">
            <x-buttons.primary-button>{{ __('Save') }}</x-buttons.primary-button>

            @if (session('status') === 'theme-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-secondary"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
