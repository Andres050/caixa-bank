import $ from "jquery";
import {Chart} from "chart.js";
import tailwindConfig from '/tailwind.config.js';

$(document).ready(function () {
    const categoryCtx = document.getElementById('categoryChart');
    if (categoryCtx) {
        setCategoryChart();
    }

    function setCategoryChart() {
        var labels = categoryCtx.getAttribute('data-labels').split(",");
        var values = categoryCtx.getAttribute('data-values').split(",").map(Number);
        var colors = categoryCtx.getAttribute('data-colors').split(",");
        var defaultLabel = categoryCtx.getAttribute('data-default-label');

        console.log('Chart DONUT is working!');
        let chartCategoryCTX = new Chart(categoryCtx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: values,
                    backgroundColor: colors,
                }]
            },
            options: {
                plugins: {
                    legend: {
                        display: false,
                    }
                }
            }
        });

        // Generar leyenda personalizada
        const legendContainer = document.getElementById('categoryChart-legend');
        chartCategoryCTX.data.labels.forEach((label, index) => {
            const color = colors[index];
            const legendItem = document.createElement('div');
            legendItem.className = 'legend-item';

            if (!label) {
                label = defaultLabel;
            }

            legendItem.innerHTML = `
                <div class="legend-color-box" style="background-color:${color}"></div>
                <span>${label}</span>
              `;
            legendItem.setAttribute('data-index', index);
            legendContainer.appendChild(legendItem);
        });

        // Agregar evento de hover a los elementos de la leyenda
        const legendItems = legendContainer.getElementsByClassName('legend-item');
        for (let i = 0; i < legendItems.length; i++) {
            const index = parseInt(legendItems[i].getAttribute('data-index'));
            const meta = chartCategoryCTX.getDatasetMeta(0);
            const item = meta.data[index];

            legendItems[i].style.cursor = 'pointer';
            item.hidden = null;

            legendItems[i].addEventListener('mouseover', function (evt) {
                handleHover(evt, parseInt(legendItems[i].getAttribute('data-index')), chartCategoryCTX.legend);
            });
            legendItems[i].addEventListener('mouseout', function (evt) {
                handleLeave(evt, parseInt(legendItems[i].getAttribute('data-index')), chartCategoryCTX.legend);
            });
            legendItems[i].addEventListener('click', function (evt) {
                // Toggle visibility of the corresponding dataset
                const index = parseInt(legendItems[i].getAttribute('data-index'));
                const meta = chartCategoryCTX.getDatasetMeta(0);
                const item = meta.data[index];

                if (item.hidden === null || item.hidden === false) {
                    item.hidden = true;
                    legendItems[i].classList.add('line-through');
                } else {
                    item.hidden = null;
                    legendItems[i].classList.remove('line-through');
                }

                chartCategoryCTX.update();
            });
        }
    }

    const balanceCtx = document.getElementById('balanceChart');
    if (balanceCtx) {
        setBalanceChart();
    }

    function setBalanceChart() {
        var labels = balanceCtx.getAttribute('data-labels').split(",");
        var values = balanceCtx.getAttribute('data-values').split(",").map(Number);
        var color = balanceCtx.getAttribute('data-color');

        console.log('Chart BALANCE is working!');
        let chartBalanceChart = new Chart(balanceCtx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    data: values,
                    borderColor: color,
                    tension: 0.3,
                    pointBackgroundColor: tailwindConfig.theme.extend.colors.primary,
                    fill: false
                }]
            },
            options: {
                plugins: {
                    legend: {
                        display: false,
                    },
                }
            }
        });

        // Generar leyenda personalizada
        const legendContainer = document.getElementById('balanceChart-legend');
        chartBalanceChart.data.labels.forEach((label, index) => {
            const value = chartBalanceChart.data.datasets[0].data[index];
            const legendItem = document.createElement('div');
            legendItem.className = 'legend-item';
            legendItem.innerHTML = `
                <div class="legend-color-box" style="background-color:${tailwindConfig.theme.extend.colors.main3}"></div>
                <span>${label} (${value} €)</span>
              `;
            legendItem.setAttribute('data-index', index);
            legendContainer.appendChild(legendItem);
        });

        // Agregar evento de hover a los elementos de la leyenda
        const legendItems = legendContainer.getElementsByClassName('legend-item');
        for (let i = 0; i < legendItems.length; i++) {
            const index = parseInt(legendItems[i].getAttribute('data-index'));
            const meta = chartBalanceChart.getDatasetMeta(0);
            const item = meta.data[index];

            legendItems[i].style.cursor = 'pointer';
            item.hidden = null;

            legendItems[i].addEventListener('click', function (evt) {
                // Toggle visibility of the corresponding dataset
                const index = parseInt(legendItems[i].getAttribute('data-index'));
                const meta = chartBalanceChart.getDatasetMeta(0);
                const item = meta.data[index];

                // if desktop
                if (item.hidden === null || item.hidden === false) {
                    item.hidden = true;
                    legendItems[i].classList.add('line-through');
                } else {
                    item.hidden = null;
                    legendItems[i].classList.remove('line-through');
                }

                chartBalanceChart.update();
            });
        }
    }
})

function handleHover(evt, itemIndex, legend) {
    legend.chart.data.datasets[0].backgroundColor.forEach((color, index, colors) => {
        colors[index] = index === itemIndex || color.length === 9 ? color : color + '00';
    });
    legend.chart.update();
}

function handleLeave(evt, item, legend) {
    legend.chart.data.datasets[0].backgroundColor.forEach((color, index, colors) => {
        colors[index] = color.length === 9 ? color.slice(0, -2) : color;
    });
    legend.chart.update();
}
