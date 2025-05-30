window.loadProfileTour = loadProfileTour;

function loadProfileTour() {
    let routeName = window.routeName;

    if (routeName === "profile.edit") {
        addStep('profile-information-form');
        addStep('profile-password-form');
        addStep('profile-delete-form');
    }
}
