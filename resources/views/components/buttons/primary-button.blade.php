<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-1 bg-primary border border-transparent rounded-md font-semibold text-xs text-third uppercase tracking-widest hover:text-primary hover:bg-main2 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
