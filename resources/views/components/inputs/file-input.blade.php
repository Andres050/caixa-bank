@props(['disabled' => false])
@php $disabledClass = $disabled ? ' opacity-50 cursor-not-allowed' : ''; @endphp

<input
    {{ $disabled ? 'disabled' : '' }}
    class="block w-full text-sm border rounded-lg cursor-pointer mt-2
           text-secondary focus:outline-none bg-main2 border-none
           file:mr-4 file:py-2 file:px-2
           file:rounded-lg file:border-0
           file:text-sm file:font-semibold
           file:bg-main3 file:text-primary hover:file:text-main3 hover:file:bg-primary"
    id="file_input"
    type="file"
/>
