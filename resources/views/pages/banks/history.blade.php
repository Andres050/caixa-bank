<x-app-layout>
    <header class="bg-[#1a1b1e] p-6 text-center">
        <p class="text-gray-400">{{ __('History') }}</p>
    </header>

    <!-- Show the currents accounts code -->
    <div class="w-full  text-white p-6">
        <h2 class="text-lg font-semibold mb-4">{{ __('Accounts') }}</h2>
        <div class="bg-[#1c1d20] p-4 rounded-xl shadow">
            <table class="datatable min-w-full table-auto text-left">
                <thead>
                    <tr>
                        <th class="py-2">{{ __('IBAN') }}</th>
                        <th class="py-2">{{ __('Owner Name') }}</th>
                        <th class="py-2">{{ __('Balance') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($user->accounts as $account)
                        <tr class="border-t border-gray-700" data-amount="{{ number_format($account->balances()->balanceTypeForward()->lastInstance()->amount, 2, ',', '.') }}">
                            <td class="py-2" width="25%">{{ $account->iban }}</td>
                            <td class="py-2">{{ $account->owner_name }}</td>
                            <td class="py-2 @if (number_format($account->balances()->balanceTypeForward()->lastInstance()->amount, 2, ',', '.') < 0) !text-red-600 @else !text-green-600 @endif" width="25%">{{ number_format($account->balances()->balanceTypeForward()->lastInstance()->amount, 2, ',', '.') }} €</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="border-t border-gray-700 w-full font-bold">
                        <td class="py-2 text-right">Total:</td>
                        <td class="py-2"></td>
                        <td class="py-2"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div class="w-full  text-white p-6">
        <h2 class="text-lg font-semibold mb-4">{{ __('Transactions') }}</h2>
        <div class="bg-[#1c1d20] p-4 rounded-xl shadow">
            <table class="datatable min-w-full table-auto text-left">
                <thead>
                    <tr>
                        <th class="py-2">{{ __('IBAN') }}</th>
                        <th class="py-2">{{ __('Date') }}</th>
                        <th class="py-2 md:block hidden">{{ __('Deptor Name') }}</th>
                        <th class="py-2">{{ __('Transaction') }}</th>
                        <th class="py-2">{{ __('Amount') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transactions as $transaction)
                        <tr class="border-t border-gray-700" data-amount="{{ number_format($transaction->transactionAmount_amount, 2, ',', '.') }}">
                            <td class="py-2">{{ $transaction->account->iban }}</td>
                            <td class="py-2" data-order="{{ $transaction->bookingDate->format('Y-m-d') }}">{{ $transaction->bookingDate->format('d-m-Y') }}</td>
                            <td class="py-2 md:block hidden">{{ $transaction->debtorName }}</td>
                            <td class="py-2">{{ json_decode($transaction->remittanceInformationUnstructured)[0] }}</td>
                            <td class="py-2 @if (number_format($transaction->transactionAmount_amount, 2, ',', '.') < 0) !text-red-600 @else !text-green-600 @endif">{{ number_format($transaction->transactionAmount_amount, 2, ',', '.') }} €</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="border-t border-gray-700 w-full font-bold">
                        <td class="py-2 text-right">Total:</td>
                        <td class="py-2"></td>
                        <td class="py-2"></td>
                        <td class="py-2"></td>
                        <td class="py-2"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</x-app-layout>
