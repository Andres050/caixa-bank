<x-app-layout>
    @include('partials.profile.navigation')

    <div class="py-6 md:px-0 px-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if ($user->bank)
                <x-buttons.primary-button
                    class="py-2"
                    x-data=""
                    x-on:click.prevent="$dispatch('open-modal', 'confirm-user-create')"
                >{{ __('Create Account') }}</x-buttons.primary-button>

                <x-ui.modal name="confirm-user-create" focusable>
                    @include('partials.profile.account', ['user' => $user])
                </x-ui.modal>

                <!-- Show the accounts -->
                @if (count($accounts) > 0)
                    @foreach ($accounts as $account)
                        @include('partials.profile.account', ['user' => $user, 'account' => $account])
                    @endforeach
                @else
                    <x-ui.empty
                        :title="__('No accounts found')"
                        :description="__('Please add an account from update accounts.')" />
                @endif
            @else
                <x-ui.empty
                    :title="__('No bank found')"
                    :description="__('Please add a bank from update bank.')" />
            @endif
        </div>
    </div>
</x-app-layout>
