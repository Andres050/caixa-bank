<x-app-layout>
    <x-slot name="header">
        @foreach($banks as $bank)
            <div class="flex gap-4 items-center">
                <img src="{{ $bank->institution->logo }}" alt="{{ $bank->institution->name }}" width="32" height="32" class="h-8 w-8 mr-2">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ $bank->institution->name }}
                </h2>
            </div>
        @endforeach
    </x-slot>

    @php
        // GET ALL ACCOUNTS FROM USER
        $accounts = auth()->user()->accounts;
    @endphp

    @if (auth()->user()->bank)
        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-[#1c1d20] overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8" style="padding-top: 1rem">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            {{ __('Session data') }}
                        </h2>

                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            {{ __("This will make the session data needed to process the api!") }}
                        </p>
                    </div>
                    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-4 flex gap-4">
                        <div class="w-full">
                            <x-input-label for="access_token" :value="__('Access Token')" />
                            <x-text-input id="access_token" name="access_token" type="text" class="mt-1 block w-full" :value="old('name', session('access_token'))" required autofocus autocomplete="access_token" />
                            <x-input-error class="mt-2" :messages="$errors->get('access_token')" />
                        </div>
                        <div class="w-full">
                            <x-input-label for="requisition" :value="__('Requisition')" />
                            <x-text-input id="requisition" name="requisition" type="text" class="mt-1 block w-full" :value="old('name', session('requisition_id'))" required autofocus autocomplete="requisition" />
                            <x-input-error class="mt-2" :messages="$errors->get('requisition')" />
                        </div>
                    </div>
                    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8" style="padding-bottom: 1rem">
                        <x-nav-link class="inline-flex items-center px-4 py-1 bg-[#1c1d20] dark:bg-gray-200 dark:hover:text-white border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700  focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150" :href="route('nordigen.auth')">
                            {{ __('UPDATE INFORMATION') }}
                        </x-nav-link>
                    </div>
                </div>
            </div>
        </div>
        @if (session('callback_url'))
            <div class="pb-6">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="bg-white dark:bg-[#1c1d20] overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8" style="padding-top: 1rem">
                            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                {{ __('Accounts update') }}
                            </h2>

                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                {{ __("This will add all the accounts!") }}
                            </p>
                        </div>
                        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8" style="padding-top: 1rem; padding-bottom: 1rem">
                            <x-nav-link class="inline-flex items-center px-4 py-1 bg-[#1c1d20] dark:bg-gray-200 dark:hover:text-white border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700  focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150" :href="session('callback_url')">
                                {{ __('UPDATE INFORMATION') }}
                            </x-nav-link>
                        </div>
                    </div>
                </div>
            </div>

            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8" style="padding-bottom: 1rem">
                @if (count($accounts) > 0)
                    @foreach ($accounts as $account)
                        <div class="max-w-7xl mx-auto dark:bg-[#1c1d20]" style="border: 1px solid white ;padding-top: 1rem; padding-bottom: 1rem; margin-top: 1rem">
                            <h2 class="flex gap-4 items-center text-lg font-medium text-gray-900 dark:text-gray-100 w-full sm:px-6 lg:px-8 pb-3" style="border-bottom: 2px solid white">
                                <img src="{{ $bank->institution->logo }}" alt="{{ $bank->institution->name }}" width="32" height="32" class="h-8 w-8 mr-2">
                                {{ $account->institution?->name }} - {{ $account->iban }} ({{ $account->status }})
                            </h2>
                            <div class="flex gap-4 py-6 sm:px-6 lg:px-8">
                                <div class="bg-white dark:bg-[#1c1d20] overflow-hidden shadow-sm sm:rounded-lg">
                                    @if ($account->transactions_disabled_date)
                                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                            <span style="color: red">[{{ __('Rate limit exceeded') }}: {{ $account->transactions_disabled_date->format('d-m-Y H:m:s') }}]</span>
                                        </p>
                                    @endif

                                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                        {{ __('Transactions update') }}
                                    </h2>

                                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                        {{ __("This will add all the transactions") }}
                                    </p>
                                    <div style="padding-top: 1rem;">
                                        <x-nav-link class="inline-flex items-center px-4 py-1 bg-[#1c1d20] dark:bg-gray-200 dark:hover:text-white border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700  focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150" href="{{ route('nordigen.transactions', ['accountId' => $account->code]) }}">
                                            {{ __('UPDATE INFORMATION') }}
                                        </x-nav-link>
                                    </div>
                                </div>
                                <div class="bg-white dark:bg-[#1c1d20] overflow-hidden shadow-sm sm:rounded-lg">
                                    @if ($account->balance_disabled_date)
                                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                            <span style="color: red">[{{ __('Rate limit exceeded') }}: {{ $account->balance_disabled_date->format('d-m-Y H:m:s') }}]</span>
                                        </p>
                                    @endif

                                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                        {{ __('Balances update') }}
                                    </h2>

                                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                        {{ __("This will add all the balances") }}
                                    </p>
                                    <div style="padding-top: 1rem;">
                                        <x-nav-link class="inline-flex items-center px-4 py-1 bg-[#1c1d20] dark:bg-gray-200 dark:hover:text-white border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700  focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150" href="{{ route('nordigen.balances', ['accountId' => $account->code]) }}">
                                            {{ __('UPDATE INFORMATION') }}
                                        </x-nav-link>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8" style="padding-top: 1rem; padding-bottom: 1rem">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            {{ __('No accounts found') }}
                        </h2>

                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            {{ __("Please add an account to see the session data.") }}
                        </p>
                    </div>
                @endif
            </div>
        @endif
    @else
        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-[#1c1d20] overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8" style="padding-top: 1rem; padding-bottom: 1rem">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            {{ __('No banks found') }}
                        </h2>

                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            {{ __("Please add a bank account to see the session data.") }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    @endif
</x-app-layout>
