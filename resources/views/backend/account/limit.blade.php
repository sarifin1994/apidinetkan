@extends('backend.layouts.app_new')

@section('title', 'Account')

@section('main')
<div class="container-lg py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg border-0 animate__animated animate__fadeIn">
                <div class="card-body text-center">
                    <div class="mb-4">
                        <i class="ti ti-lock-access text-danger" style="font-size: 48px;"></i>
                    </div>
                    <h2 class="fw-bold text-danger mb-2">Akses Ditolak 😢</h2>
                    <p class="text-muted fs-5 mb-4">Lisensi kamu saat ini tidak memiliki akses ke fitur ini.<br>Yuk upgrade lisensi kamu untuk menikmati fitur lengkapnya!</p>

                    <a href="/order/license" class="btn btn-primary btn-lg">
                        <i class="ti ti-arrow-up-right me-1"></i> Upgrade Lisensi
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
