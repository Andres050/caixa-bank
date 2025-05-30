window.loadProfileBankTour = loadProfileBankTour;

function loadProfileBankTour() {
    let routeName = window.routeName;

    if (routeName === "profile.bank.edit") {
        addStep('bank-profile-bank');
        addStep('bank-profile-chars');
        addStep('bank-profile-theme');
        addStep('bank-profile-accounts-update');
        addStep('bank-profile-accounts-info');
    }
}
