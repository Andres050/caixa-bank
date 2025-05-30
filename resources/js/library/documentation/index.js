window.loadIndexTour = loadIndexTour;

function loadIndexTour() {
    let routeName = window.routeName;

    if (routeName === "dashboard.index" || routeName === "dashboard.show") {
        addStep('index-total-amount');
        addStep('index-dashboard-sidebar', 'right');

        addStep('index-stat-current');
        addStep('index-stat-expenses');
        addStep('index-stat-income');

        addStep('categoryChart');
        addStep('balanceChart');

        addStep('index-transactions-table');
    }
}
