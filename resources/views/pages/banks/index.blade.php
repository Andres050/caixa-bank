<x-app-layout>
    <!-- Topbar -->
    <header class="bg-[#1a1b1e] p-6 text-center">
        <p class="text-gray-400">{{ __('The total of the accounts balances is') }} {{ auth()->user()->totalAccountSum }} €</p>
    </header>

    <div class="block md:flex min-h-screen bg-[#0e0f11] text-white">
        <!-- Sidebar -->
        <aside class="md:w-56 bg-[#1a1b1e] p-6">
            <h2 class="text-xl mb-6 font-semibold">{{ __('Dashboard') }}</h2>
            <nav class="md:block flex justify-center gap-4 space-y-3">
                @foreach($accounts as $account)
                    <a style="--tw-space-y-reverse: 0;margin-top: calc(.75rem * calc(1 - var(--tw-space-y-reverse)));margin-bottom: calc(.75rem * var(--tw-space-y-reverse));" href="{{ route('bank.show', ['id' => $account->code]) }}" class="block px-4 py-2 rounded-lg  @if (isset($currentAccount) and $account->code == $currentAccount->code) bg-[#2b2d30] @endif hover:bg-[#2b2d30] text-gray-300">
                        {{ __('Account') }} <br/> <span style="font-size: 10px">{{ $account->iban }}</span>
                    </a>
                @endforeach
            </nav>
        </aside>

        <div class="min-h-screen w-full  text-white p-6">
            @if ($currentAccount)
                @php
                    $balance = $currentAccount->balances()->balanceTypeForward()->lastInstance();
                @endphp

                @if ($balance)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                        <x-stat-box icon="💳" title="{{ __('Current Balance') }}" value="{{ number_format($balance->amount, 2, ',', '.') }} €" />
                        <x-stat-box icon="💸" title="{{ __('Expenses This Month') }}" value="{{ number_format($currentAccount->expenses, 2, ',', '.') }} €" />
                        <x-stat-box icon="📈" title="{{ __('Income This Month') }}" value="{{ number_format($currentAccount->income, 2, ',', '.') }} €" />
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                        <div class="bg-[#1c1d20] p-4 rounded-xl shadow">
                            <h2 class="text-xl mb-4">{{ __('Expenses by Category') }}</h2>
                            <canvas id="categoryChart" class="!w-full md:px-[25%] md:!h-96"></canvas>
                        </div>
                        <div class="bg-[#1c1d20] p-4 rounded-xl shadow">
                            <h2 class="text-xl mb-4">{{ __('Balance History') }}</h2>
                            <canvas id="balanceChart" class="!w-full md:!h-96"></canvas>
                        </div>
                    </div>

                    <div class="bg-[#1c1d20] p-4 rounded-xl shadow">
                        <h2 class="text-xl mb-4">{{ __('Recent Transactions') }}</h2>
                        <table class="min-w-full table-auto text-left">
                            <thead>
                                <tr>
                                    <th class="py-2">{{ __('Date') }}</th>
                                    <th class="py-2 md:block hidden">{{ __('Deptor Name') }}</th>
                                    <th class="py-2">{{ __('Transaction') }}</th>
                                    <th class="py-2">{{ __('Amount') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($currentAccount->transactionsCurrentMonth as $transaction)
                                    <tr class="border-t border-gray-700">
                                        <td class="py-2">{{ $transaction->bookingDate->format('d-m-Y') }}</td>
                                        <td class="py-2 md:block hidden">{{ $transaction->debtorName }}</td>
                                        <td class="py-2">{{ json_decode($transaction->remittanceInformationUnstructured)[0] }}</td>
                                        <td class="py-2 @if (number_format($transaction->transactionAmount_amount, 2, ',', '.') < 0) !text-red-600 @else !text-green-600 @endif">{{ number_format($transaction->transactionAmount_amount, 2, ',', '.') }} €</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="bg-[#1c1d20] p-4 rounded-xl shadow">
                        <h2 class="text-xl mb-4">{{ __('No Balance Data Available') }}</h2>
                        <p class="text-gray-400">{{ __('It seems like there is no balance data available for this account. Go to configuration to import the balance and transfers.') }}</p>
                    </div>
                @endif
            @else
                <div class="bg-[#1c1d20] p-4 rounded-xl shadow">
                    <h2 class="text-xl mb-4">{{ __('No Account Selected') }}</h2>
                    <p class="text-gray-400">{{ __('Please select an account from the sidebar to view your dashboard. If there is no account on the sidebar go to configuration to import the accounts.') }}</p>
                </div>
            @endif
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        @php
            $usedColors = [];
            function randomColorHex(&$usedColors) {
                do {
                    $r = mt_rand(0, 180);
                    $g = mt_rand(0, 255);
                    $b = mt_rand(180, 255);
                    $color = sprintf("#%02X%02X%02X", $r, $g, $b);
                } while (in_array($color, $usedColors));
                $usedColors[] = $color;
                return $color;
            }
        @endphp

        const categoryCtx = document.getElementById('categoryChart');
        new Chart(categoryCtx, {
            type: 'doughnut',
            data: {
                labels: [{!! $currentAccount->transactionsCurrentMonth->groupBy('remittanceInformationUnstructured')->keys()->map(fn($key) => "'" . trim((string) $key, '[]"') . "'")->implode(',') !!}],
                datasets: [{
                    label: 'Expenses',
                    data: [@foreach($currentAccount->transactionsCurrentMonth->groupBy('remittanceInformationUnstructured') as $group) {{ $group->sum('transactionAmount_amount') }} {{ !$loop->last ? ',' : '' }} @endforeach],
                    backgroundColor: [@foreach($currentAccount->transactionsCurrentMonth->groupBy('remittanceInformationUnstructured') as $group) '{{ randomColorHex($usedColors) }}' {{ !$loop->last ? ',' : '' }} @endforeach],
                }]
            }
        });

        const balanceCtx = document.getElementById('balanceChart');
        new Chart(balanceCtx, {
            type: 'line',
            data: {
                labels: [{!! $currentAccount->balances()->balanceTypeForward()->pluck('reference_date')->map(fn($key) => "'" . trim((string) $key, '[]"') . "'")->implode(',') !!}],
                datasets: [{
                    label: 'Balance',
                    data: [{{ $currentAccount->balances()->balanceTypeForward()->pluck('amount')->implode(',') }}],
                    borderColor: '#108cb9',
                    backgroundColor: 'transparent',
                    tension: 0.4
                }]
            }
        });
    </script>
</x-app-layout>
