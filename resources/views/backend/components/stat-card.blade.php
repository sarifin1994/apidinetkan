<a href="{{ $route }}">
  <div class="card total-sales">
    <div class="card-body">
      <div class="row">
        <div class="col-xl-8 xl-12 col-md-8 col-sm-12 col box-col-12">
          <div class="d-flex {{ $color }}">
            <span>
              {!! $icon !!}
            </span>
            <div class="flex-shrink-0">
              <h4>{{ $amount ?: 0 }}</h4>
              <h6>{{ $label }}</h6>

              @empty(!$growth)
                <div class="arrow-chart">
                  @if ($growth >= 0)
                    <svg>
                      <use href="{{ asset('assets/svg/icon-sprite.svg#arrow-chart-up') }}"></use>
                    </svg>
                    <h5 class="font-success">+{{ $growth }}%</h5>
                  @else
                    <svg>
                      <use href="{{ asset('assets/svg/icon-sprite.svg#arrow-chart') }}"></use>
                    </svg>
                    <h5 class="font-danger">{{ $growth }}%</h5>
                  @endif
                </div>
              @endempty
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</a>
