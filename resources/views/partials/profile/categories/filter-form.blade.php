@props(['filter' => null, 'category'])

<form @if (isset($filter)) action="{{ route('profile.categories.filter.update', ['id' => $filter->id]) }}" @else action="{{ route('profile.categories.filter') }}" @endif method="POST" class="relative md:px-0 px-6 bg-main2 rounded-lg">
    @csrf
    <input type="hidden" name="category_id" value="{{ $category->id }}" />

    @if (isset($filter))
        @method('patch')
        <input type="hidden" name="id" value="{{ $filter->id }}" />
    @endif

    @if (!isset($filter))
        <h2 class="flex gap-4 items-center text-lg font-medium text-primary w-full sm:px-6 lg:px-8 pb-3">
            {{ __('Create Filter') }}
        </h2>
    @endif

    <!-- Show the account buttons -->
    <div class="grid grid-cols-2 gap-4 pb-6 sm:px-6 lg:px-8 w-full">
        <x-inputs.input name="value" type="text" label="{{ __('Value') }}" :value="isset($filter) ? $filter->value : ''" required autofocus />
        <x-inputs.select id="type" name="type" label="{{ __('Category Type') }}" required >
            <option selected disabled>{{ __('Select Type') }}</option>
            <option @if ($filter && $filter->type === 'exact') selected @endif value="exact">{{ __('Exact') }}</option>
            <option @if ($filter && $filter->type === 'contains') selected @endif value="contains">{{ __('Contains') }}</option>
            <option @if ($filter && $filter->type === 'starts_with') selected @endif value="starts_with">{{ __('Starts With') }}</option>
            <option @if ($filter && $filter->type === 'ends_with') selected @endif value="ends_with">{{ __('Ends With') }}</option>
        </x-inputs.select>

        <div class="col-span-3 md:col-span-1 flex !flex-col">
            <x-inputs.input-label for="enabled" value="{{ __('Enabled') }}" />
            <x-inputs.checkbox id="enabled{{ $filter ? '-'.$filter->id : $category->id.'-'.$category->filters()->count() }}" class="mt-2" name="enabled" :active="isset($filter) ? $filter->enabled : false" />
            <x-inputs.input-error :messages="isset($errors) ? $errors->get('enabled') : null" class="mt-2" />
        </div>
    </div>

    @if (isset($filter))
        <div class="sm:px-6 lg:px-8 flex md:flex-row flex-col justify-between">
            <div class="mt-2 flex justify-center md:justify-start gap-4">
                <x-buttons.secondary-button type="submit">
                    {{ __('Update Filter') }}
                </x-buttons.secondary-button>
                <x-buttons.danger-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-category-filter-{{ $filter->id }}-deletion')">
                    {{ __('Delete Filter') }}
                </x-buttons.danger-button>
            </div>
        </div>
    @else
        <div class="sm:px-6 lg:px-8">
            <x-buttons.secondary-button type="submit">
                {{ __('Create Filter') }}
            </x-buttons.secondary-button>
        </div>
    @endif
</form>

@if (isset($filter))
    <x-ui.modal name="confirm-category-filter-{{ $filter->id }}-deletion" focusable>
        <form method="post" action="{{ route('profile.category.filter.destroy', ['id' => $filter->id]) }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-lg font-medium text-primary">
                {{ __('Are you sure you want to delete the filter?') }}
            </h2>

            <div class="mt-6 flex justify-end">
                <x-buttons.secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </x-buttons.secondary-button>

                <x-buttons.danger-button class="ms-3">
                    {{ __('Delete Filter') }}
                </x-buttons.danger-button>
            </div>
        </form>
    </x-ui.modal>
@endif
