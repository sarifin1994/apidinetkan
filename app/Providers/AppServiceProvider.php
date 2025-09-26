<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Payments\Repositories\AdminDinetkanInvoiceRepository;
use Modules\Payments\Repositories\Contracts\AdminDinetkanInvoiceRepositoryInterface;
use Modules\Payments\Repositories\Contracts\LicenseDinetkanRepositoryInterface;
use Modules\Payments\Repositories\LicenseDinetkanRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {

        $singletons = [
            InvoiceRepositoryInterface::class => InvoiceRepository::class,
            AdminInvoiceRepositoryInterface::class => AdminInvoiceRepository::class,
            LicenseRepositoryInterface::class => LicenseRepository::class,
            TransaksiRepositoryInterface::class => TransaksiRepository::class,
            AdminDinetkanInvoiceRepositoryInterface::class => AdminDinetkanInvoiceRepository::class,
            LicenseDinetkanRepositoryInterface::class => LicenseDinetkanRepository::class,
        ];

        foreach ($singletons as $abstract => $concrete) {
            $this->app->bind(
                $abstract,
                $concrete
            );
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
