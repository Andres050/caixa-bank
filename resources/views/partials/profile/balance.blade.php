@props(['user', 'account', 'balance' => null])

<form @if ($balance) action="{{ route('profile.balance.update') }}" data-id="{{ $balance->code }}" @else action="{{ route('profile.balance.create') }}" @endif method="POST" class="relative md:px-0 px-6 bg-main2 rounded-lg py-6">
    @csrf

    @if ($balance)
        @method('patch')
        <input type="hidden" name="balance_id" value="{{ $balance->code }}" />
    @endif

    <input type="hidden" name="account_id" value="{{ $account->code }}" />

    <!-- Show the bank logo and name -->
    <h2 class="flex gap-4 items-center text-lg font-medium text-primary w-full sm:px-6 lg:px-8 pb-3">
        <img src="{{ $user->bank->institution->logo }}" alt="{{ $user->bank->institution->name }}" width="32" height="32" class="h-8 w-8 mr-2">
        {{ $account->institution?->name }} - {{ $account->iban }} <span class="md:block hidden">({{ $account->type }})</span>
    </h2>

    <!-- Show the account buttons -->
    <div class="grid grid-cols-3 gap-4 py-6 sm:px-6 lg:px-8 w-full">
        <x-inputs.input required="required" :value="$balance ? $balance->amount : null" step="0.01" type="number" name="amount" :label="__('Amount')"/>
        <x-inputs.input required="required" maxlength="3" minlength="3" :value="$balance ? $balance->currency : null" type="text" name="currency" :label="__('Currency')" />
        <x-inputs.select required="required" :value="$balance ? $balance->balance_type : null" name="balance_type" :label="__('Balance Type')">
            <option value="forwardAvailable" @if ($balance && $balance->balance_type == 'forwardAvailable') selected @endif>{{ __('forwardAvailable') }}</option>
            <option value="closingBooked" @if ($balance && $balance->balance_type == 'closingBooked') selected @endif>{{ __('closingBooked') }}</option>
        </x-inputs.select>
        <x-inputs.input required="required" :value="$balance ? $balance->reference_date->format('Y-m-d') : null" type="date" name="reference_date" :label="__('Reference Date')"/>
    </div>

    @if (isset($balance))
        <div class="sm:px-6 lg:px-8 flex md:flex-row flex-col justify-between">
            <div class="mt-2 flex justify-center md:justify-start gap-4">
                <x-buttons.secondary-button type="submit">
                    {{ __('Update Balance') }}
                </x-buttons.secondary-button>
            </div>
        </div>
    @else
        <div class="sm:px-6 lg:px-8">
            <x-buttons.secondary-button type="submit">
                {{ __('Create Balance') }}
            </x-buttons.secondary-button>
        </div>
    @endif
</form>
