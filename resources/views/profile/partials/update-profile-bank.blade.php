<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Bank Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __("Update your account's bank information.") }}
        </p>

        <form method="post" action="{{ route('nordigen.institutions') }}">
            @csrf
            <x-primary-button class="mt-2">{{ __('Update List') }}</x-primary-button>
        </form>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.bank.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        @php
            $user = auth()->user();
            $institutions = \App\Models\Institution::all();
            $bank = \App\Models\Bank::where('user_id', $user->id)->first();
        @endphp

        <div>
            <x-input-label for="name" :value="__('Institution')" />
            <select id="institution" name="institution" class="select2 form-control border-gray-300 dark:border-gray-700 dark:bg-[#1c1d20] dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm mt-1 block w-full" required>
                <option value="" disabled selected>{{ __('Select an institution') }}</option>
                @foreach ($institutions as $institution)
                    <option value="{{ $institution->id }}" {{ $bank && $bank->institution_id == $institution->id ? 'selected' : '' }}>
                        {{ $institution->name }}
                    </option>
                @endforeach
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('institution')" />
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
        </div>
    </form>
</section>
