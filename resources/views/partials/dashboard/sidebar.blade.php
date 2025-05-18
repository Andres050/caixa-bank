<aside class="md:w-56 bg-main2 p-6">
    <h2 class="text-xl mb-6 font-semibold">{{ __('Dashboard') }}</h2>
    <nav class="md:block flex justify-center gap-4 space-y-3">
        @foreach($accounts as $account)
            <a href="{{ route('dashboard.show', ['id' => $account->code]) }}"
               class="block px-4 py-2 rounded-lg u-sidebar
               @if (isset($currentAccount) and $account->code == $currentAccount->code)
               bg-main1
               @endif hover:bg-main1 text-primary">
                {{ __('Account') }} <br/> <span class="text-[10px]">{{ $account->iban }}</span>
            </a>
        @endforeach
    </nav>
</aside>
