<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-1 bg-[#1c1d20] dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest dark:hover:text-white hover:bg-[#1a1b1e]  focus:bg-[#1a1b1e] dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
