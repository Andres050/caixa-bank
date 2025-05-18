<x-buttons.primary-button class="py-2" x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-transaction-update-{{ $balance->code }}')">
    {{ __('UPDATE') }}
</x-buttons.primary-button>
<x-buttons.danger-button class="py-2" x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-transaction-delete-{{ $balance->code }}')">
    {{ __('DELETE') }}
</x-buttons.danger-button>

<x-ui.modal name="confirm-transaction-update-{{ $balance->code }}" maxWidth="full" margin="sm:px-[50px]" focusable>
    @include('partials.profile.balance', [
        'balance' => $balance,
        'account' => $account,
        'user' => $user
    ])
</x-ui.modal>

<x-ui.modal name="confirm-transaction-delete-{{ $balance->code }}" focusable>
    <form method="post" action="{{ route('profile.balance.destroy') }}" class="p-6">
        @csrf
        @method('delete')

        <input type="hidden" name="account_id" value="{{ $account->code }}" />
        <input type="hidden" name="balance_id" value="{{ $balance->code }}" />

        <h2 class="text-lg font-medium text-primary">
            {{ __('Are you sure you want to delete your balance?') }}
        </h2>

        <div class="mt-6 flex justify-end">
            <x-buttons.secondary-button x-on:click="$dispatch('close')">
                {{ __('Cancel') }}
            </x-buttons.secondary-button>

            <x-buttons.danger-button class="ms-3">
                {{ __('Delete Balance') }}
            </x-buttons.danger-button>
        </div>
    </form>
</x-ui.modal>
