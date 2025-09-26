@extends('backend.layouts.app')

@section('title', 'Account Licensing')

@section('css')
  <style>
    .text-gradient {
      background: linear-gradient(45deg, #f3ec78, #af4261);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }

    .btn-gradient {
      background: linear-gradient(45deg, #434343, #2c2c54);
      color: #ffffff;
      border: none;
      border-radius: 10px;
      transition: all 0.3s ease-in-out;
    }

    .btn-gradient:hover {
      background: linear-gradient(45deg, #1f1f1f, #464775);
      color: #ffffff;
      box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
      transform: scale(1.05);
    }

    .hover-zoom:hover .icon,
    .hover-zoom:hover .animated-icon {
      transform: scale(1.2);
      transition: transform 0.3s ease-in-out;
    }

    .icon {
      color: #af4261;
      transition: transform 0.3s ease-in-out, color 0.3s ease-in-out;
    }

    .card {
      border-radius: 15px;
      overflow: hidden;
      transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
      background-color: #1f1f1f;
    }

    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 12px 24px rgba(0, 0, 0, 0.25);
    }

    .price-box {
      color: #f3ec78;
      animation: fadeInUp 0.5s;
    }

    /* Custom animations */
    .animated-icon {
      animation: pulse 2s infinite alternate;
    }

    @keyframes pulse {
      from {
        color: #af4261;
      }

      to {
        color: #f3ec78;
      }
    }

    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(20px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
  </style>
@endsection

@section('main')
  <div class="container-fluid">
    <div class="page-title">
      <div class="row">
        <div class="col-sm-6 ps-0">
          <h3>Account Licensing</h3>
        </div>
        <div class="col-sm-6 pe-0">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">
                <svg class="stroke-icon">
                  <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                </svg></a></li>
            <li class="breadcrumb-item">Account</li>
            <li class="breadcrumb-item active">Licensing</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <!-- Container-fluid starts-->
  <div class="container-fluid">
    <div class="card">
      <div class="card-header pb-0">
        <h4>Choose Your Plan</h4>
      </div>
      <div class="card-body pricing-content">
        <div class="row pricing-col gy-4">
          @foreach ($licenses as $plan)
            <div class="col-lg-4 col-sm-6 box-col-3">
              <div class="pricingtable">
                <div class="pricingtable-header">
                  <h3 class="title">{{ $plan['title'] }}</h3>
                </div>
                <div class="price-value">
                  <span class="currency">{{ $plan['price'] !== '0rb' ? 'Rp' : '' }}</span><span
                    class="amount">{{ $plan['price'] !== '0rb' ? $plan['price'] : 'Free' }}</span>
                  <span class="duration d-block">{{ $plan['price'] !== '0rb' ? '/mo' : '1 Month' }}</span>
                </div>
                <ul class="pricing-content">
                  @if ($plan['price'] === '0rb')
                    <li class="text-muted">Only Available for New Activated Accounts</li>
                  @endif

                  @foreach ($plan['features'] as $feature)
                    @if (is_array($feature))
                      @if (count($feature) === 3 && $feature[2] === true)
                        <li class="{{ $feature[1] ? '' : 'text-muted' }}">
                          {{ $feature[0] }}
                        </li>
                      @elseif (count($feature) === 2)
                        <li class="{{ $feature[1] ? '' : 'text-muted' }}">
                          {{ $feature[0] }}
                        </li>
                      @endif
                    @else
                      <li>{{ $feature }}</li>
                    @endif
                  @endforeach
                </ul>
                <div class="pricingtable-signup">
                  <a class="btn btn-primary btn-lg"
                    href="{{ url()->current() }}/{{ $plan['url'] }}">{{ $plan['button_text'] }}</a>
                </div>
              </div>
            </div>
          @endforeach
        </div>
      </div>
    </div>
  </div>
  <!-- Container-fluid Ends-->
@endsection

@section('scripts')
@endsection
