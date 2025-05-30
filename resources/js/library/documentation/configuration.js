window.loadConfigurationTour = loadConfigurationTour;

function loadConfigurationTour() {
    let routeName = window.routeName;

    if (routeName === "dashboard.configuration") {
        addStep('configuration-session-data');

        addStep('configuration-accounts-update');
        addStep('configuration-update-all');
        addStep('sortable-accounts');
    }
}
