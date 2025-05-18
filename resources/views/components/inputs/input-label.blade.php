@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-md text-primary']) }}>
    {{ $value ?? $slot }}
</label>
