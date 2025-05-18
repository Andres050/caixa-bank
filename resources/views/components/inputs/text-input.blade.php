@props(['disabled' => false])
@php $disabledClass = $disabled ? ' opacity-50 cursor-not-allowed' : ''; @endphp

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'border-third bg-main2 text-primary rounded-md shadow-sm' . $disabledClass ]) !!}>
