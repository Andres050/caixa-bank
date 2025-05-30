<x-app-layout>
    @include('partials.profile.navigation')

    <div class="py-6 md:px-0 px-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="flex items-center justify-start gap-4">
                <x-buttons.primary-button
                    class="py-2"
                    x-data=""
                    x-on:click.prevent="$dispatch('open-modal', 'confirm-create-category')"
                >{{ __('Create Category') }}</x-buttons.primary-button>
                <form id="update-transactions-form" action="{{ route('profile.categories.update-transactions') }}" method="POST" style="display: none;">
                    @csrf
                </form>

                <x-buttons.secondary-button class="py-2" onclick="document.getElementById('update-transactions-form').submit();">
                    {{ __('Update Transactions') }}
                </x-buttons.secondary-button>
            </div>

            <x-ui.modal name="confirm-create-category" focusable>
                @include('partials.profile.categories.form', ['user' => $user])
            </x-ui.modal>

            @if (count($categories) > 0)
                @foreach ($categories as $category)
                    <div class=" border-main3 border-2 bg-main2 shadow rounded-lg">
                        @include('partials.profile.categories.form', [
                            'category' => $category,
                            'user' => $user,
                        ])
                    </div>

                    <div class="shadow rounded-lg" x-data="{ show: false }">
                        <div class="flex gap-4 items-center justify-start">
                            <div class="pb-4 flex flex-col w-full justify-center md:justify-start gap-4">
                                <div class="flex gap-4">
                                    @if (count($category->filters) > 0)
                                        <x-buttons.primary-button x-on:click="show = !show">
                                            {{ __('Show Filters') }}
                                        </x-buttons.primary-button>
                                    @endif
                                    <x-buttons.primary-button x-on:click.prevent="$dispatch('open-modal', 'create-filter-{{ $category->id }}')">
                                        {{ __('Create Filter') }}
                                    </x-buttons.primary-button>
                                </div>

                                <div class="w-full" x-show="show">
                                    @if (count($category->filters) > 0)
                                        <div class="grid grid-cols-1 gap-4 w-full">
                                            @foreach($category->filters as $filter)
                                                <div class="bg-main2 border-primary border-2 p-4 shadow rounded-lg">
                                                    @include('partials.profile.categories.filter-form', [
                                                        'category' => $category,
                                                        'filter' => $filter
                                                    ])
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="bg-main2 shadow rounded-lg">
                    <x-ui.empty
                        :title="__('No categories found')"
                        :description="__('Please add a category from create category button.')" />
                </div>
            @endif
        </div>
    </div>

    <script>
        function ajaxUpdateTransactions()
        {
            const url = "{{ route('profile.categories.update-transactions') }}";

            fetch(url, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    alert(data.message);
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('{{ __('An error occurred while updating transactions.') }}');
            });
        }
    </script>
</x-app-layout>
