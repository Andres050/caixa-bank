@props(['user', 'category' => null])

<form @if (isset($category)) action="{{ route('profile.category.update', ['id' => $category->id]) }}" @else action="{{ route('profile.category.create') }}" @endif method="POST" class="relative md:px-0 px-6 py-6 bg-main2 rounded-lg">
    @csrf

    @if (isset($category))
        @method('patch')
        <input type="hidden" name="category_id" value="{{ $category->id }}" />
    @endif

    @if (!isset($category))
        <h2 class="flex gap-4 items-center text-lg font-medium text-primary w-full sm:px-6 lg:px-8 pb-3">
            {{ __('Create Category') }}
        </h2>
    @endif

    <!-- Show the account buttons -->
    <div class="grid grid-cols-1 gap-4 sm:px-6 lg:px-8 w-full">
        <x-inputs.input required="required" :value="isset($category) ? $category->name : null" type="text" name="name" :label="__('Name')"/>

        @if (isset($category))
            <div class="flex md:flex-row flex-col justify-between">
                <div class="mt-2 flex justify-center md:justify-start gap-4">
                    <x-buttons.secondary-button type="submit">
                        {{ __('Update Category') }}
                    </x-buttons.secondary-button>
                    <x-buttons.danger-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-category-{{ $category->id }}-deletion')">
                        {{ __('Delete Category') }}
                    </x-buttons.danger-button>
                </div>
            </div>
        @endif
    </div>

    @if (!isset($category))
        <div class="sm:px-6 lg:px-8 pt-4">
            <x-buttons.secondary-button type="submit">
                {{ __('Create Category') }}
            </x-buttons.secondary-button>
        </div>
    @endif
</form>

@if (isset($category))
    <x-ui.modal name="confirm-category-{{ $category->id }}-deletion" focusable>
        <form method="post" action="{{ route('profile.category.destroy', ['id' => $category->id]) }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-lg font-medium text-primary">
                {{ __('Are you sure you want to delete your category?') }}
            </h2>

            <div class="mt-6 flex justify-end">
                <x-buttons.secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </x-buttons.secondary-button>

                <x-buttons.danger-button class="ms-3">
                    {{ __('Delete Category') }}
                </x-buttons.danger-button>
            </div>
        </form>
    </x-ui.modal>

    <x-ui.modal name="create-filter-{{ $category->id }}" focusable>
        <div class="py-6">
            @include('partials.profile.categories.filter-form', ['category' => $category, 'filter' => null])
        </div>
    </x-ui.modal>
@endif
