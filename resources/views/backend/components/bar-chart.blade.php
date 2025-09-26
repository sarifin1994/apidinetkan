<div x-load x-data="barChart({{ $chartOptions }})">
  <div class="card">
    <div class="card-header pb-0">
      <div class="header-top">
        <h5 x-text="title"></h5>
      </div>
    </div>
    <div class="card-body p-0">
      <div class="row">
        <div class="col-12">
          <div class="chart-legend revenue-legend" x-show="legends.length > 0">
            <ul>
              <template x-for="legend in legends" :key="legend.label">
                <li class="me-3">
                  <div class="circle" :class="legend.color + ' me-1'"></div>
                  <span x-text="legend.label"></span>
                </li>
              </template>
            </ul>
          </div>
          <div class="chart-container" x-ref="chart"></div>
        </div>
      </div>
    </div>
  </div>
</div>
