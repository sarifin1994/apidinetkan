export default (config = {}) => ({
    title: config.title || 'Bar Chart',
    legends: config.legends || [],
    route: config.route || '',
    chart: null,
    options: null,

    async init() {
        this.options = {
            series: [],
            chart: {
                type: 'bar',
                height: config.height || 316,
                stacked: config.stacked || true,
                toolbar: { show: config.toolbar || false }
            },
            colors: config.colors || ['#7366ff', '#f73164'],
            plotOptions: {
                bar: { columnWidth: config.columnWidth || '80%' }
            },
            dataLabels: { enabled: false },
            legend: { show: false },
            stroke: { width: 10, colors: ['#fff'] },
            grid: {
                show: false,
                xaxis: { lines: { show: true } },
                yaxis: { lines: { show: false } }
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
            tooltip: config.tooltip || {
                custom: ({ series, seriesIndex, dataPointIndex }) => {
                    const value = Math.abs(series[seriesIndex][dataPointIndex]);
                    const type = seriesIndex === 0 ? 'Data 1' : 'Data 2';
                    return `<div class="apex-tooltip p-2">
                            <span class="bg-${seriesIndex === 0 ? 'primary' : 'secondary'}"></span>
                            ${type}: ${value.toLocaleString()}
                        </div>`;
                }
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
                tooltip: {
                    custom: ({ series, seriesIndex, dataPointIndex }) => {
                        const value = Math.abs(series[seriesIndex][dataPointIndex]);
                        const type = data.tooltip.type;
                        return `<div class="apex-tooltip p-2">
                                <span class="bg-${seriesIndex === 0 ? 'primary' : 'secondary'}"></span>
                                ${type}${value.toLocaleString()}
                            </div>`;
                    }
                }
            });
            this.chart.updateSeries(data.series);
        } catch (error) {
            console.error('Failed to update chart:', error);
        }
    }
});
