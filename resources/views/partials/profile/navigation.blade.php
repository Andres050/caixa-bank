<nav class="bg-main2 border-b border-third">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-links.nav-link :href="route('profile.edit')" :active="request()->routeIs(['profile.edit'])">
                        {{ __('Profile') }}
                    </x-links.nav-link>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-links.nav-link :href="route('profile.bank.edit')" :active="request()->routeIs(['profile.bank.edit'])">
                        {{ __('Bank') }}
                    </x-links.nav-link>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-links.nav-link :href="route('profile.accounts.edit')" :active="request()->routeIs(['profile.accounts.edit', 'profile.transaction.edit', 'profile.balance.edit'])">
                        {{ __('Accounts') }}
                    </x-links.nav-link>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-links.nav-link :href="route('profile.import.edit')" :active="request()->routeIs(['profile.import.edit'])">
                        {{ __('Import') }}
                    </x-links.nav-link>
                </div>
            </div>

            <!-- Responsive Navigation Links -->
            <div class="flex justify-center md:hidden w-full">
                <div class="pt-2 pb-3 space-y-1">
                    <x-links.responsive-nav-link :href="route('profile.edit')" :active="request()->routeIs(['profile.edit'])">
                        {{ __('Profile') }}
                    </x-links.responsive-nav-link>
                </div>

                <div class="pt-2 pb-3 space-y-1">
                    <x-links.responsive-nav-link :href="route('profile.bank.edit')" :active="request()->routeIs(['profile.bank.edit'])">
                        {{ __('Bank') }}
                    </x-links.responsive-nav-link>
                </div>

                <div class="pt-2 pb-3 space-y-1">
                    <x-links.responsive-nav-link :href="route('profile.accounts.edit')" :active="request()->routeIs(['profile.accounts.edit', 'profile.transaction.edit', 'profile.balance.edit'])">
                        {{ __('Accounts') }}
                    </x-links.responsive-nav-link>
                </div>

                <div class="pt-2 pb-3 space-y-1">
                    <x-links.responsive-nav-link :href="route('profile.import.edit')" :active="request()->routeIs(['profile.import.edit'])">
                        {{ __('Import') }}
                    </x-links.responsive-nav-link>
                </div>
            </div>
        </div>
    </div>
</nav>
