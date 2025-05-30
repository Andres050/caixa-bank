<section>
    <header>
        <h2 class="text-lg font-medium text-primary">
            {{ __('Chars Info') }}
        </h2>

        <p class="mt-1 text-sm text-secondary">
            {{ __("Update your account's chars info") }}
        </p>
    </header>

    <form method="post" action="{{ route('profile.bank.chars') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-inputs.input-label for="name" :value="__('Chart Type')" />
            <select data-default="{{ __('-- Select an option --') }}" id="chars" name="chars" class="select2 form-control border-third bg-main2 text-primary rounded-md shadow-sm mt-1 block w-full" required>
                <option value="" disabled selected>{{ __('Select an type') }}</option>
                @foreach(\App\Models\User::$charsTypes as $type)
                    <option value="{{ $type }}" {{ $user->chars == $type ? 'selected' : '' }}>
                        {{ __('chars.' . $type) }}
                    </option>
                @endforeach
            </select>
            <x-inputs.input-error class="mt-2" :messages="$errors->get('chars')" />
        </div>

        <div class="flex items-center gap-4">
            <x-buttons.primary-button>{{ __('Save') }}</x-buttons.primary-button>

            @if (session('status') === 'chars-updated')
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
