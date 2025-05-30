window.loadHistoryTour = loadHistoryTour;

function loadHistoryTour() {
    let routeName = window.routeName;

    if (routeName === "dashboard.history") {
        addStep('history-accounts-table');
        addStep('history-balances-table');
        addStep('history-transactions-table');
    }
}
