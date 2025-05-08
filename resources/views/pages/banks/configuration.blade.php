<x-app-layout>
    <x-slot name="header">
        @foreach($banks as $bank)
            <div class="flex gap-4 items-center">
                <img src="{{ $bank->institution->logo }}" alt="{{ $bank->institution->name }}" width="32" height="32" class="h-8 w-8 mr-2">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ $bank->institution->name }} API
                </h2>
            </div>
        @endforeach
    </x-slot>

    @php
        // GET ALL ACCOUNTS FROM USER
        $accounts = auth()->user()->accounts()->sortOrder()->get();
    @endphp

    <div class="md:px-0 px-4">
        @if (auth()->user()->bank)
            <div class="py-6">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="bg-white dark:bg-[#1c1d20] px-4 md:px-0 overflow-hidden shadow-sm rounded-lg">
                        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 pt-4">
                            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                {{ __('Session data') }}
                            </h2>

                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                {{ __("This will make the session data needed to process the api!") }}
                            </p>
                        </div>
                        <div class="max-w-7xl md:flex-row flex-col mx-auto sm:px-6 lg:px-8 py-4 flex gap-4">
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
                        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 pb-4">
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
                        <div class="grid grid-cols-1 lg:grid-cols-2 w-full lg:gap-16 bg-white dark:bg-[#1c1d20] px-4 md:px-0 overflow-hidden shadow-sm rounded-lg">
                            <div>
                                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 pt-4">
                                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                        {{ __('Accounts update') }}
                                    </h2>

                                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                        {{ __("This will add all the accounts!") }}
                                    </p>
                                </div>
                                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-4">
                                    <x-nav-link class="inline-flex items-center px-4 py-1 bg-[#1c1d20] dark:bg-gray-200 dark:hover:text-white border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700  focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150" :href="session('callback_url')">
                                        {{ __('UPDATE Accounts') }}
                                    </x-nav-link>
                                </div>
                            </div>

                            @php
                                $showUpdate = true;
                                foreach ($accounts as $account) {
                                    if ($account->transactionsDisabled || $account->balanceDisabled) {
                                        $showUpdate = false;
                                        break;
                                    }
                                }
                            @endphp

                            @if ($showUpdate)
                                <div>
                                    <div class="max-w-7xl pt-4">
                                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                            {{ __('Transactions and balances update') }}
                                        </h2>

                                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                            {{ __("This will add all the accounts balances and trasactions!") }}
                                        </p>
                                    </div>
                                    <div class="max-w-7xl py-4">
                                        <x-nav-link class="inline-flex items-center px-4 py-1 bg-[#1c1d20] dark:bg-gray-200 dark:hover:text-white border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700  focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150" :href="route('nordigen.all_accounts')">
                                            {{ __('UPDATE ALL') }}
                                        </x-nav-link>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div id="sortable-accounts" class="max-w-7xl mx-auto sm:px-6 lg:px-8 pb-4">
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        {{ __("You can reorder the accounts!") }}
                    </p>

                    @if (count($accounts) > 0)
                        @foreach ($accounts as $account)
                            <div data-id="{{ $account->code }}" class="max-w-7xl md:px-0 px-4 mx-auto dark:bg-[#1c1d20] rounded-lg border-t-white border-b-white border-l-white border-r-white border-2 py-4 mt-4">
                                <h2 class="flex gap-4 items-center text-lg font-medium text-gray-900 dark:text-gray-100 w-full sm:px-6 lg:px-8 pb-3 border-b-white border-t-0 border-l-0 border-r-0 border-2">
                                    <img src="{{ $bank->institution->logo }}" alt="{{ $bank->institution->name }}" width="32" height="32" class="h-8 w-8 mr-2">
                                    {{ $account->institution?->name }} - {{ $account->iban }} <span class="md:block hidden">({{ $account->status }})</span>
                                </h2>
                                <div class="flex md:flex-row flex-col gap-4 py-6 sm:px-6 lg:px-8">
                                    <div class="bg-white dark:bg-[#1c1d20] overflow-hidden shadow-sm rounded-lg">
                                        @if ($account->transactionsDisabled)
                                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                                <span class="text-red-600">[{{ __('Rate limit exceeded') }}: {{ $account->transactions_disabled_date->format('d-m-Y H:i:s') }}]</span>
                                            </p>
                                        @endif

                                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                            {{ __('Transactions update') }}
                                        </h2>

                                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                            {{ __("This will add all the transactions") }}
                                        </p>

                                        <!-- Show the date of the last transaction -->
                                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                            {{ __('Last update transactions') }}: {{ $account->bankDataSync()->dataTypeTransaction()->latest()->first() ? $account->bankDataSync()->dataTypeTransaction()->latest()->first()->created_at->format('d-m-Y H:i:s') : __('No transactions found') }}
                                        </p>

                                        <div class="pt-4">
                                            <x-nav-link class="inline-flex items-center px-4 py-1 bg-[#1c1d20] dark:bg-gray-200 dark:hover:text-white border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700  focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150" href="{{ route('nordigen.transactions', ['accountId' => $account->code]) }}">
                                                {{ __('UPDATE INFORMATION') }}
                                            </x-nav-link>
                                        </div>
                                    </div>
                                    <div class="bg-white dark:bg-[#1c1d20] overflow-hidden shadow-sm rounded-lg">
                                        @if ($account->balanceDisabled)
                                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                                <span class="text-red-600">[{{ __('Rate limit exceeded') }}: {{ $account->balance_disabled_date->format('d-m-Y H:i:s') }}]</span>
                                            </p>
                                        @endif

                                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                            {{ __('Balances update') }}
                                        </h2>

                                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                            {{ __("This will add all the balances") }}
                                        </p>

                                        <!-- Show the date of the last balance -->
                                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                            {{ __('Last update balance') }}: {{ $account->bankDataSync()->dataTypeBalance()->latest()->first() ? $account->bankDataSync()->dataTypeBalance()->latest()->first()->created_at->format('d-m-Y H:i:s') : __('No balances found') }}
                                        </p>

                                        <div class="pt-4">
                                            <x-nav-link class="inline-flex items-center px-4 py-1 bg-[#1c1d20] dark:bg-gray-200 dark:hover:text-white border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700  focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150" href="{{ route('nordigen.balances', ['accountId' => $account->code]) }}">
                                                {{ __('UPDATE INFORMATION') }}
                                            </x-nav-link>
                                        </div>
                                    </div>
                                    @if (!$account->transactionsDisabled && !$account->balanceDisabled)
                                        <div class="bg-white dark:bg-[#1c1d20] overflow-hidden shadow-sm rounded-lg">
                                            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                                {{ __('Update all') }}
                                            </h2>

                                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                                {{ __("This will add all the transactions and balances at the same time") }}
                                            </p>
                                            <div class="pt-4">
                                                <x-nav-link class="inline-flex items-center px-4 py-1 bg-[#1c1d20] dark:bg-gray-200 dark:hover:text-white border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700  focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150" href="{{ route('nordigen.all', ['accountId' => $account->code]) }}">
                                                    {{ __('UPDATE INFORMATION') }}
                                                </x-nav-link>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-4">
                            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                {{ __('No accounts found') }}
                            </h2>

                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                {{ __("Please add an account from update accounts.") }}
                            </p>
                        </div>
                    @endif
                </div>
            @endif
        @else
            <div class="py-6">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="bg-white dark:bg-[#1c1d20] overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-4">
                            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                {{ __('No banks found') }}
                            </h2>

                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                {{ __("Please add a bank from profile to see the accounts.") }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
