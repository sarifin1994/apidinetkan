export default (config = {}) => ({
    title: config.title || 'Line Chart',
    route: config.route || '',
    chart: null,
    options: null,

    async init() {
        this.options = {
            series: [],
            chart: {
                height: config.height || 330,
                type: 'area',
                toolbar: { show: config.toolbar || false }
            },
            colors: config.colors || ['#7366ff', '#f73164'],
            stroke: {
                width: 3,
                curve: config.curve || 'smooth'
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
                    formatter: config.yFormatter || ((val) => Math.round(val).toLocaleString('id-ID')),
                    style: {
                        fontSize: '14px',
                        fontWeight: 500,
                        fontFamily: 'Lexend, sans-serif'
                    }
                }
            },
            legend: { show: true },
            tooltip: config.tooltip || {
                y: { formatter: (val) => Math.round(val).toLocaleString() }
            }
        };

        this.chart = new ApexCharts(this.$refs.chart, this.options);
        this.chart.render();
        if (this.route) {
            await this.updateChart();
        }
    },
    async updateChart() {
        try {
            const response = await fetch(this.route);
            const data = await response.json();

            this.chart.updateOptions({
                xaxis: { categories: data.categories },
            });
            this.chart.updateSeries(data.series);
        } catch (error) {
            console.error('Failed to update chart:', error);
        }
    }

});
