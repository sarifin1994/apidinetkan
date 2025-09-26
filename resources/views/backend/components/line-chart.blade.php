<div x-load x-data="lineChart({{ $chartOptions }})">
  <div class="card">
    <div class="card-header pb-0">
      <div class="header-top">
        <h5 x-text="title"></h5>
      </div>
    </div>
    <div class="card-body p-0">
      <div x-ref="chart" class="line-chart"></div>
    </div>
  </div>
</div>
