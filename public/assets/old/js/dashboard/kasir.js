document.addEventListener('DOMContentLoaded', function () {
    const baseUrl = window.location.pathname;

    // Revenue Chart Configuration
    const revenueChartOptions = {
        series: [],
        chart: {
            type: 'bar',
            height: 316,
            stacked: true,
            toolbar: { show: false }
        },
        colors: [CrocsAdminConfig.primary, CrocsAdminConfig.secondary],
        plotOptions: {
            bar: { columnWidth: '80%' }
        },
        dataLabels: { enabled: false },
        legend: { show: false },
        stroke: {
            width: 10,
            colors: ['#fff']
        },
        grid: {
            show: false,
            xaxis: { lines: { show: true } },
            yaxis: { lines: { show: false } }
        },
        yaxis: {
            labels: {
                formatter: (val) => val,
                style: {
                    fontSize: '14px',
                    fontWeight: 500,
                    fontFamily: 'Lexend, sans-serif'
                }
            }
        },
        xaxis: {
            categories: [],
            labels: {
                style: {
                    fontSize: '13px',
                    colors: '#959595',
                    fontFamily: 'Lexend, sans-serif'
                }
            },
            axisBorder: { show: true },
            axisTicks: { show: false }
        },
        tooltip: {
            custom: function ({ series, seriesIndex, dataPointIndex }) {
                const value = Math.abs(series[seriesIndex][dataPointIndex]);
                const type = seriesIndex === 0 ? 'Income' : 'Expense';
                return `<div class="apex-tooltip p-2">
                    <span class="bg-${seriesIndex === 0 ? 'primary' : 'secondary'}"></span>
                    ${type}: Rp. ${value.toLocaleString()}
                </div>`;
            }
        }
    };

    const revenueChart = new ApexCharts(document.querySelector("#revenue-chart"), revenueChartOptions);
    revenueChart.render();

    // Fetch and Update Charts
    async function updateCharts() {
        try {
            // Revenue Data
            const revenueResponse = await fetch(`${baseUrl}/charts/revenue`);
            const revenueData = await revenueResponse.json();

            document.querySelector('.total-increase h2').textContent = `Rp. ${(revenueData.totalIncome - revenueData.totalExpense).toLocaleString('id-ID')}`;
            document.querySelector('.total-increase span').textContent = `Total : Rp. ${revenueData.totalIncome.toLocaleString('id-ID')}`;

            revenueChart.updateOptions({
                xaxis: { categories: revenueData.months }
            });
            revenueChart.updateSeries([
                { name: 'Income', data: revenueData.income },
                { name: 'Expense', data: revenueData.expense }
            ]);

        } catch (error) {
            console.error('Error updating dashboard:', error);
        }
    }

    // Initial update and set interval for periodic updates
    updateCharts();
    setInterval(updateCharts, 300000); // Update every 5 minutes
});
