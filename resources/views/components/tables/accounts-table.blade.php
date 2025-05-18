@props([
    'accounts',
    'noFooter' => false,
    'actions' => false,
    'static' => false,
    'type' => 'static'
])

<div class="bg-main2 p-4 rounded-xl shadow">
    <table class="datatable min-w-full table-auto nowrap text-left @if (!$noFooter) u-footer @endif" data-type="{{ $type }}" {{ $attributes->merge() }}>
        <thead>
            <tr>
                <th class="py-2 dt-low-priority" data-column="iban">{{ __('IBAN') }}</th>
                <th class="py-2" data-column="owner_name">{{ __('Owner Name') }}</th>
                <th class="py-2" data-column="balance" data-orderable="false">{{ __('Balance') }}</th>
            </tr>
        </thead>
        <tbody>
            @if ($type === 'static')
                @foreach($accounts as $account)
                    @php $lastInstance = $account->balances()->balanceTypeForward()->lastInstance()->first(); @endphp
                    <tr class="" data-amount="{{ number_format($lastInstance ? $lastInstance->amount : 0, 2, ',', '.') }}">
                        <td class="py-2" width="25%">{{ $account->iban }}</td>
                        <td class="py-2">{{ $account->owner_name }}</td>
                        <td class="py-2 @if (number_format($lastInstance ? $lastInstance->amount : 0, 2, ',', '.') < 0) !text-error @else !text-success @endif" width="25%">{{ number_format($lastInstance ? $lastInstance->amount : 0, 2, ',', '.') }} €</td>
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
                </tr>
            </tfoot>
        @endif
    </table>
</div>
