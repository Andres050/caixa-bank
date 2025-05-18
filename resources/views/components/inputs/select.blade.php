@php $required = $required ?? false; @endphp

<div class="col-span-3 md:col-span-1 flex !flex-col">
    <x-inputs.input-label for="{{ $name }}" value="{{ __($label) }}{{ $required ? '*' : '' }}" />
    <select class="w-full text-primary bg-main2 rounded-lg" name="{{ $name }}"
            placeholder="{{ __($label) }} {{ $required ? '*' : '' }}"
            @if ($required)required="required"@endif>
        {{ $slot }}
    </select>
    <x-inputs.input-error :messages="isset($errors) ? $errors->get($name) : null" class="mt-2" />
</div>
