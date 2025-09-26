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

    // Issues Chart Configuration (Scatter)
    const issuesChartOptions = {
        series: [],
        chart: {
            height: 252,
            type: 'scatter',
            toolbar: { show: false }
        },
        colors: [CrocsAdminConfig.primary, CrocsAdminConfig.secondary],
        xaxis: {
            type: 'datetime',
            labels: {
                style: {
                    fontSize: '13px',
                    colors: '#959595',
                    fontFamily: 'Lexend, sans-serif'
                }
            }
        },
        yaxis: {
            labels: {
                formatter: (val) => Math.round(val),
                style: {
                    fontSize: '14px',
                    fontWeight: 500,
                    fontFamily: 'Lexend, sans-serif'
                }
            }
        },
        legend: {
            show: true,
            position: 'top'
        },
        tooltip: {
            x: {
                format: 'dd MMM yyyy'
            }
        }
    };

    // Users Growth Chart Configuration
    const usersGrowthOptions = {
        series: [],
        chart: {
            height: 330,
            type: 'area',
            toolbar: { show: false }
        },
        colors: [CrocsAdminConfig.secondary, CrocsAdminConfig.primary],
        stroke: {
            width: 3,
            curve: 'smooth'
        },
        dataLabels: { enabled: false },
        fill: {
            type: 'gradient',
            gradient: {
                shade: 'light',
                type: 'vertical',
                opacityFrom: 0.5,
                opacityTo: 0.1,
                stops: [0, 100]
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
            }
        },
        yaxis: {
            labels: {
                formatter: (val) => Math.round(val),
                style: {
                    fontSize: '14px',
                    fontWeight: 500,
                    fontFamily: 'Lexend, sans-serif'
                }
            }
        },
        legend: { show: true },
        tooltip: {
            y: {
                formatter: (val) => Math.round(val)
            }
        }
    };

    // Initialize Charts
    const revenueChart = new ApexCharts(document.querySelector("#revenue-chart"), revenueChartOptions);
    const issuesChart = new ApexCharts(document.querySelector("#Statistics"), issuesChartOptions);
    const usersGrowthChart = new ApexCharts(document.querySelector("#users-growth"), usersGrowthOptions);

    revenueChart.render();
    issuesChart.render();
    usersGrowthChart.render();

    // Recent Users Table
    const recentUsersTable = $('#team-members').DataTable({
        ajax: {
            url: `${baseUrl}/tables/recent-users`,
            dataSrc: 'users'
        },
        columns: [
            {
                data: null,
                render: function (data, type, row) {
                    const initial = data.name ? data.name[0].toUpperCase() : '-';
                    return `<div class="d-flex">
                        <div class="flex-shrink-0">
                            <div class="user-avatar">
                                ${initial}
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-2">
                            <a href="#">
                                <h6>${data.name || '-'}</h6>
                                <small>${data.username}</small>
                            </a>
                        </div>
                    </div>`;
                }
            },
            { data: 'type' },
            { data: 'joined' }
        ],
        pageLength: 5,
        ordering: false,
        info: false,
        lengthChange: false,
        searching: true,
    });

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

            // Rest of the update logic remains the same
            const issuesResponse = await fetch(`${baseUrl}/charts/new-issues`);
            const issuesData = await issuesResponse.json();
            issuesChart.updateSeries([
                { name: 'New Clients', data: issuesData.installations },
                { name: 'Outage', data: issuesData.troubles }
            ]);

            const usersResponse = await fetch(`${baseUrl}/charts/users-growth`);
            const usersData = await usersResponse.json();
            usersGrowthChart.updateOptions({
                xaxis: { categories: usersData.months }
            });
            usersGrowthChart.updateSeries([
                { name: 'Hotspot', data: usersData.hotspot },
                { name: 'PPPoE', data: usersData.pppoe }
            ]);

            // Update Recent Users Table
            recentUsersTable.ajax.reload();

        } catch (error) {
            console.error('Error updating dashboard:', error);
        }
    }

    // Initial update and set interval for periodic updates
    updateCharts();
    setInterval(updateCharts, 300000); // Update every 5 minutes
});
