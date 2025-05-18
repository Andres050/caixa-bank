@props([
    'balances',
    'account',
    'user',
    'noFooter' => false,
    'actions' => false,
    'type' => 'static',
])

<div class="bg-main2 p-4 rounded-xl shadow">
    <table class="datatable min-w-full table-auto nowrap text-left  @if (!$noFooter) u-footer @endif" data-type="{{ $type }}" {{ $attributes->merge() }}>
        <thead>
            <tr>
                <th class="py-2" data-column="iban" data-orderable="false">{{ __('IBAN') }}</th>
                <th class="py-2" data-column="reference_date">{{ __('Reference Date') }}</th>
                <th class="py-2" data-column="balance_type">{{ __('Balance Type') }}</th>
                <th class="py-2" data-column="currency">{{ __('Currency') }}</th>
                <th class="py-2 dt-low-priority" data-column="amount">{{ __('Amount') }}</th>

                @if ($actions)
                    <th class="py-2 dt-low-priority" data-column="actions" data-orderable="false" data-searchable="false">{{ __('Actions') }}</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @if ($type === 'static')
                @foreach($balances as $balance)
                    <tr>
                        <td class="py-2">{{ $balance->account->iban }}</td>
                        <td class="py-2">{{ $balance->reference_date->format('d-m-Y') }}</td>
                        <td class="py-2">{{ $balance->balance_type }}</td>
                        <td class="py-2">{{ $balance->currency }}</td>
                        <td class="py-2 @if (number_format($balance->amount, 2, ',', '.') < 0) !text-error @else !text-success @endif">{{ $balance->amount }} €</td>

                        @if ($actions)
                            <td class="py-2">
                                @include('partials.datatable.balance-actions', [
                                    'balance' => $balance,
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
                    <td class="py-2">Total:</td>
                    <td class="py-2"></td>
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
