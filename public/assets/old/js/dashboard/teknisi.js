document.addEventListener('DOMContentLoaded', function () {
    const baseUrl = window.location.pathname;

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

    // Initialize Charts
    const issuesChart = new ApexCharts(document.querySelector("#Statistics"), issuesChartOptions);

    issuesChart.render();

    // Fetch and Update Charts
    async function updateCharts() {
        try {
            // Rest of the update logic remains the same
            const issuesResponse = await fetch(`${baseUrl}/charts/new-issues`);
            const issuesData = await issuesResponse.json();
            issuesChart.updateSeries([
                { name: 'New Clients', data: issuesData.installations },
                { name: 'Outage', data: issuesData.troubles }
            ]);

        } catch (error) {
            console.error('Error updating dashboard:', error);
        }
    }

    // Initial update and set interval for periodic updates
    updateCharts();
    setInterval(updateCharts, 300000); // Update every 5 minutes
});
