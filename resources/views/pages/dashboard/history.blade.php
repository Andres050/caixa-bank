<x-app-layout>
    <x-site.top-bar :text="__('History')" />

    <div class="w-full text-primary p-6">
        <h2 class="text-lg font-semibold mb-4">{{ __('Accounts') }}</h2>
        <x-tables.accounts-table type="request" :accounts="$accounts" data-url="{{ route('api.datatable.accounts') }}" />
    </div>

    <div class="w-full text-primary p-6">
        <h2 class="text-lg font-semibold mb-4">{{ __('Balances') }}</h2>
        <x-tables.balances-table type="request" :balances="$balances" data-url="{{ route('api.datatable.balances') }}" />
    </div>

    <div class="w-full text-primary p-6">
        <h2 class="text-lg font-semibold mb-4">{{ __('Transactions') }}</h2>
        <x-tables.transaction-table type="request" :transactions="$transactions" data-url="{{ route('api.datatable.transactions') }}" />
    </div>
</x-app-layout>
