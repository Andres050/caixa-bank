import './bootstrap';
import Alpine from 'alpinejs';
import Sortable from 'sortablejs';

import $ from 'jquery';
window.$ = $;
window.jQuery = $;

import select2 from 'select2';
select2();

import DataTable from 'datatables.net-dt';
import moment from 'moment';
import 'datatables.net-dt/css/dataTables.dataTables.css';

import "/node_modules/select2/dist/css/select2.css";
import '/node_modules/datatables.net-responsive-dt';
import '/node_modules/datatables.net-responsive-dt/css/responsive.dataTables.css';

import '/resources/css/switch.css';
import '/resources/css/style.css';

import { Chart, ArcElement, Legend, CategoryScale, LineController, LineElement, PointElement, LinearScale, Title, DoughnutController } from 'chart.js';

Chart.register(ArcElement, Legend, CategoryScale, LineController, LineElement, PointElement, LinearScale, Title, DoughnutController);

window.Alpine = Alpine;

Alpine.start();

let currentLocale = document.querySelector('meta[name="locale"]').content ?? 'es-ES';

if (currentLocale) {
    //  Hot fixes
    if (currentLocale === 'en-EN') {
        currentLocale = 'en-GB';
    }
}

console.log("Current Locale: "+ currentLocale);
window.currentLocale = currentLocale;

// SELECT2 JS
import '/resources/js/library/select2.js';

// DATATABLES JS
import '/resources/js/library/datatable.js';

// FETCH REQUESTS JS
import '/resources/js/library/fetch.js';

// SORTABLE JS
import '/resources/js/library/sortable.js';

// SCHEDULE CHECKER JS
import '/resources/js/library/schedule.js';

// CHARTS JS
import '/resources/js/library/charts.js';

// CLOCK JS
import '/resources/js/library/clock.js';

// NOTIFICATION JS
import '/resources/js/library/notifications.js';

// INTERACTIVE DOCUMENTATION JS
import '/resources/js/library/documentation.js';
