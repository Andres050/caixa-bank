import $ from "jquery";

$(document).ready(function () {
    console.log('DataTable is working!');
    staticDataTable();
    requestDataTable();
    defaultDataTable();
});

function defaultDataTable() {
    const $tables = $('.datatable[data-type=default]');

    $tables.each(function ($i, $table) {
        $($table).DataTable({
            pagingType: 'numbers',
            responsive: {
                details: {
                    type: 'inline',
                    target: 'tr'
                }
            },
            pageLength: 10,
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/' + window.currentLocale + '.json'
            },
            columnDefs: [
                {targets: 'dt-low-priority', responsivePriority: 10001},
                {targets: 'dt-high-priority', responsivePriority: 1},
                {targets: 'dt-medium-priority', responsivePriority: 2},

                {targets: 'dt-date', type: 'date'},
                {orderable: false, targets: 'no-sort'}
            ],
        });
    });
}

function requestDataTable() {
    const $tables = $('.datatable[data-type=request]');

    $tables.each(function ($i, $table) {
        $($table).DataTable({
            pagingType: 'numbers',
            processing: true,
            serverSide: true,
            responsive: {
                details: {
                    type: 'inline',
                    target: 'tr'
                }
            },
            pageLength: 10,
            columns: Array.from($table.querySelectorAll('thead th'))
                .map(th => ({
                    data: th.getAttribute('data-column'),
                    orderable: th.getAttribute('data-orderable') !== 'false',
                    searchable: th.getAttribute('data-searchable') !== 'false',
                })),
            ajax: {
                url: $table.getAttribute('data-url') || '',
                type: 'GET',
                data: function (data) {
                    if (!data || typeof data.start !== 'number' || typeof data.length !== 'number') {
                        return {};
                    }

                    const result = {
                        draw: data.draw,
                        page: Math.max(Math.floor(data.start / data.length) + 1, 1),
                        per_page: data.length,
                        search: data.search?.value || '',
                    };

                    if (data.order?.[0] && data.columns?.[data.order[0].column]?.data) {
                        const orderBy = data.columns[data.order[0].column].data;
                        if (typeof orderBy === 'string') {
                            result.order_by = orderBy;
                            result.order_dir = data.order[0].dir || 'asc';
                        }
                    }

                    return result;
                },
                dataSrc: function (json) {
                    const footer = $table.querySelector('tfoot');
                    if (footer) {
                        const footerCells = footer.querySelectorAll('td');
                        let lastCell = footerCells[footerCells.length - 1];
                        lastCell.innerHTML = json.totalAmount;
                    }

                    return json.data;
                }
            },
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/' + window.currentLocale + '.json'
            },
            columnDefs: [
                {targets: 'dt-low-priority', responsivePriority: 10001},
                {targets: 'dt-high-priority', responsivePriority: 1},
                {targets: 'dt-medium-priority', responsivePriority: 2},
                {targets: 'dt-date', type: 'date'},
                {orderable: false, targets: 'no-sort'}
            ],
            order: [[1, 'desc']]
        });
    })
}

function staticDataTable() {
    $('.datatable[data-type=static]').DataTable({
        pagingType: 'numbers',
        responsive: {
            details: {
                type: 'inline',
                target: 'tr'
            }
        },
        pageLength: 10,
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/' + window.currentLocale + '.json'
        },
        columnDefs: [
            {targets: 'dt-low-priority', responsivePriority: 10001},
            {targets: 'dt-high-priority', responsivePriority: 1},
            {targets: 'dt-medium-priority', responsivePriority: 2},

            {targets: 'dt-date', type: 'date'},
            {orderable: false, targets: 'no-sort'}
        ],
        order: [[1, 'desc']],
        drawCallback: function (settings) {
            let api = this.api();
            let rows = api.rows({search: 'applied'}).count();

            let tableContainer = $(api.table().container());

            let paginationNav = tableContainer.find('nav[aria-label="pagination"]');

            if (rows <= settings._iDisplayLength) {
                paginationNav.hide();
            } else {
                paginationNav.show();
            }
        },
        footerCallback: function (row, data, start, end, display) {
            let api = this.api();
            let total = 0;

            if (!$(api.table().node()).hasClass('u-footer')) {
                return;
            }

            api.rows({search: 'applied'}).nodes().each(function (row) {
                let amountStr = row.getAttribute('data-amount') || '0';
                let parsed = parseFloat(amountStr.replace(/\./g, '').replace(',', '.')) || 0;
                total += parsed;
            });

            $(row.querySelector('td:last-child')).html(total.toLocaleString(window.currentLocale, {
                style: 'currency',
                currency: 'EUR',
                minimumFractionDigits: 2
            }));
        }
    });
}
