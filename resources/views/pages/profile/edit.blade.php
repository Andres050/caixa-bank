<x-app-layout>
    @include('partials.profile.navigation')

    <div class="py-6 md:px-0 px-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div id="profile-information-form" shepherd-text="{{trans('shepherd.profile-information-form')}}" class="p-4 sm:p-8 bg-main2 shadow rounded-lg">
                <div class="max-w-xl">
                    @include('partials.profile.profile.update-profile-information-form')
                </div>
            </div>

            <div id="profile-password-form" shepherd-text="{{trans('shepherd.profile-password-form')}}" class="p-4 sm:p-8 bg-main2 shadow rounded-lg">
                <div class="max-w-xl">
                    @include('partials.profile.profile.update-password-form')
                </div>
            </div>

            <div id="profile-delete-form" shepherd-text="{{trans('shepherd.profile-delete-form')}}" class="p-4 sm:p-8 bg-main2 shadow rounded-lg">
                <div class="max-w-xl">
                    @include('partials.profile.profile.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
