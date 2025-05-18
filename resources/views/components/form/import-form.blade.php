<form action="{{ $action }}" method="POST" enctype="multipart/form-data" class="relative md:px-0 px-6 bg-main2 rounded-lg py-6">
    @csrf

    <!-- Show the bank logo and name -->
    <h2 class="flex gap-4 items-center text-lg font-medium text-primary w-full sm:px-6 lg:px-8 pb-3">
        <img src="{{ $user->bank->institution->logo }}" alt="{{ $user->bank->institution->name }}" width="32" height="32" class="h-8 w-8 mr-2">
        {{ __('Import') }} {{ $type }}
    </h2>

    <!-- Show the account buttons -->
    <div class="grid grid-cols-2 gap-4 py-6 sm:px-6 lg:px-8 w-full">
        <div class="col-span-2 lg:col-span-1">
            <x-inputs.input-label for="file_csv" value="{{ $type }} CSV" />
            <x-inputs.file-input
                name="file_csv_{{ $typeField }}"
                class="w-full"
                placeholder="{{ $type }} CSV" />
            <x-inputs.input-error :messages="$errors->get('file_csv_'.$typeField)" class="mt-2" />
        </div>

        <div class="col-span-2 lg:col-span-1">
            <x-inputs.input-label for="file_xlsx" value="{{ $type }} XLSX" />
            <x-inputs.file-input
                name="file_xlsx_{{ $typeField }}"
                class="w-full"
                placeholder="{{ $type }} XLSX" />
            <x-inputs.input-error :messages="$errors->get('file_xlsx_'.$typeField)" class="mt-2" />
        </div>
    </div>

    <div class="flex gap-4 items-center justify-left sm:px-6 lg:px-8">
        <x-buttons.secondary-button type="submit">
            {{ __('Import') }} {{ $type }}
        </x-buttons.secondary-button>
        <x-buttons.secondary-button type="submit"
                                    x-data=""
                                    x-on:click.prevent="$dispatch('open-modal', 'example-{{ strtolower($type) }}-import')">
            {{ __('Example import') }}
        </x-buttons.secondary-button>
    </div>

    <x-ui.modal name="example-{{ strtolower($type) }}-import" focusable maxWidth="full" margin="sm:px-[50px]">
        <div class="text-white p-6 lg:mx-12">
            {{ $slot }}
        </div>
    </x-ui.modal>
</form>
