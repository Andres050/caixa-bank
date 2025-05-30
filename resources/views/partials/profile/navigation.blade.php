<nav class="sticky top-0 left-0 z-10 bg-main2 border-b border-third">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-links.nav-link id="profile-navigation-profile" :shepherd-text="trans('shepherd.profile-navigation-profile')" :href="route('profile.edit')" :active="request()->routeIs(['profile.edit'])">
                        {{ __('Profile') }}
                    </x-links.nav-link>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-links.nav-link id="profile-navigation-bank" :shepherd-text="trans('shepherd.profile-navigation-bank')" :href="route('profile.bank.edit')" :active="request()->routeIs(['profile.bank.edit'])">
                        {{ __('Bank') }}
                    </x-links.nav-link>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-links.nav-link id="profile-navigation-accounts" :shepherd-text="trans('shepherd.profile-navigation-accounts')" :href="route('profile.accounts.edit')" :active="request()->routeIs(['profile.accounts.edit', 'profile.transaction.edit', 'profile.balance.edit' ,'profile.import.edit'])">
                        {{ __('Accounts') }}
                    </x-links.nav-link>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-links.nav-link id="profile-navigation-categories" :shepherd-text="trans('shepherd.profile-navigation-categories')" :href="route('profile.categories')" :active="request()->routeIs(['profile.categories'])">
                        {{ __('Categories') }}
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
                    <x-links.responsive-nav-link :href="route('profile.accounts.edit')" :active="request()->routeIs(['profile.accounts.edit', 'profile.transaction.edit', 'profile.balance.edit', 'profile.import.edit'])">
                        {{ __('Accounts') }}
                    </x-links.responsive-nav-link>
                </div>

                <div class="pt-2 pb-3 space-y-1">
                    <x-links.responsive-nav-link :href="route('profile.categories')" :active="request()->routeIs(['profile.categories'])">
                        {{ __('Categories') }}
                    </x-links.responsive-nav-link>
                </div>
            </div>
        </div>
    </div>
</nav>
