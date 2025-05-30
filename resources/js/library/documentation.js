import Shepherd from 'shepherd.js';
import 'shepherd.js/dist/css/shepherd.css';

document.addEventListener('DOMContentLoaded', () => {
    const routeName = document.querySelector("head").getAttribute('dir');
    const nextTranslation = document.querySelector("head").getAttribute('next-translation');
    const closeTranslation = document.querySelector("head").getAttribute('close-translation');
    let loadNavigation = true;

    let tour = new Shepherd.Tour({
        useModalOverlay: true,
        defaultStepOptions: {
            classes: 'c-shepherd',
            scrollTo: false,
        }
    });

    // Define navigation steps default
    if (loadNavigation) {
        addStep('navigation-panel-control');

        addStep('navigation-history');

        addStep('navigation-clock');

        addStep('navigation-configuration');

        if (routeName === "dashboard.index" || routeName === "dashboard.show") {
            addStep('navigation-month-selector');
        }

        addStep('navigation-lang-selector');

        addStep('navigation-user-dropdown');
    }

    // Navigation steps for profile
    if (loadNavigation && (routeName === "profile.edit" || routeName === "profile.bank.edit" || routeName === "profile.accounts.edit" || routeName === "profile.categories")) {
        addStep('profile-navigation-profile');
        addStep('profile-navigation-bank');
        addStep('profile-navigation-accounts');
        addStep('profile-navigation-categories');
    }

    // Trigger tour start
    function startTour() {
        // Check if the tour has steps
        if (tour.steps.length === 0) {
            // If no steps are defined add a default step
            addStep('default-step');
            return;
        }

        tour.start();
    }

    function addStep(id, position = 'top', text = null) {
        if (!text) {
            text = document.querySelector(`#${id}`) ? document.querySelector(`#${id}`).getAttribute('shepherd-text') : '';
        }

        tour.addStep({
            id: id,
            text: text,
            attachTo: {
                element: "#"+id,
                on: position
            },
            buttons: [
                {
                    text: closeTranslation,
                    action: tour.cancel
                },
                {
                    text: nextTranslation,
                    action: tour.next
                }
            ]
        });
    }

    window.tour = tour;
    window.routeName = routeName;
    window.startTour = startTour;
    window.addStep = addStep;

    console.log("Load documentation tour for route:", routeName);
    loadIndexTour();
    loadHistoryTour();
    loadConfigurationTour();
    loadProfileTour();
    loadProfileBankTour();
    loadProfileAccountsTour();
    loadProfileCategoriesTour();
})

// Index page
import '/resources/js/library/documentation/index.js';

// History page
import '/resources/js/library/documentation/history.js';

// Configuration page
import '/resources/js/library/documentation/configuration.js';

// Profile page
import '/resources/js/library/documentation/profile/profile.js';

// Profile Bank page
import '/resources/js/library/documentation/profile/bank.js';

// Profile Accounts page
import '/resources/js/library/documentation/profile/accounts.js';

// Profile Categories page
import '/resources/js/library/documentation/profile/categories.js';
