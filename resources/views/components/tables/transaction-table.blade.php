@props([
    'transactions',
    'account',
    'user',
    'noFooter' => false,
    'actions' => false,
    'type' => 'static',
])

<div class="bg-main2 p-4 rounded-xl shadow">
    <table class="datatable min-w-full table-auto nowrap @if (!$noFooter) u-footer @endif" data-type="{{ $type }}" {{ $attributes->merge() }}>
        <thead>
            <tr>
                <th class="py-2 dt-low-priority" data-column="iban" data-orderable="false">{{ __('IBAN') }}</th>
                <th class="py-2" data-column="bookingDate">{{ __('Date') }}</th>
                <th class="py-2 dt-low-priority" data-column="debtorName" data-orderable="false">{{ __('Deptor Name') }}</th>
                <th class="py-2 dt-low-priority" data-column="remittanceInformationUnstructured">{{ __('Transaction') }}</th>
                <th class="py-2" data-column="transactionAmount_amount">{{ __('Amount') }}</th>

                @if ($actions)
                    <th class="py-2 dt-low-priority" data-column="actions" data-orderable="false" data-searchable="false">{{ __('Actions') }}</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @if ($type === 'static')
                @foreach($transactions as $transaction)
                    <tr data-amount="{{ number_format($transaction->transactionAmount_amount, 2, ',', '.') }}">
                        <td class="py-2">{{ $transaction->account->iban }}</td>
                        <td class="py-2 dt-date" data-order="{{ $transaction->bookingDate->format('Y-m-d') }}">{{ $transaction->bookingDate->format('d-m-Y') }}</td>
                        <td class="py-2">{{ $transaction->debtorNameFormat }}</td>
                        <td class="py-2">{{ json_decode($transaction->remittanceInformationUnstructured) ? json_decode($transaction->remittanceInformationUnstructured)[0] : '--' }}</td>
                        <td class="py-2 @if (number_format($transaction->transactionAmount_amount, 2, ',', '.') < 0) !text-error @else !text-success @endif">{{ number_format($transaction->transactionAmount_amount, 2, ',', '.') }} €</td>

                        @if ($actions)
                            <td class="py-2">
                                @include('partials.datatable.transaction-actions', [
                                    'transaction' => $transaction,
                                    'account' => $account,
                                    'user' => $user
                                ])
                            </td>
                        @endif
                    </tr>
                @endforeach
            @endif
        </tbody>
        @if (!$noFooter)
            <tfoot>
                <tr class="w-full font-bold">
                    <td class="py-2"><span class="md:block hidden">Total:</span></td>
                    <td class="py-2"><span class="md:hidden">Total:</span></td>
                    <td class="py-2"></td>
                    <td class="py-2"></td>
                    <td class="py-2"></td>
                    @if ($actions)
                        <td class="py-2"></td>
                    @endif
                </tr>
            </tfoot>
        @endif
    </table>
</div>
