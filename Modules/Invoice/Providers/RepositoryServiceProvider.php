<?php

namespace Modules\Invoice\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Invoice\Repositories\Eloquents\InvoiceRepository;
use Modules\Invoice\Repositories\Eloquents\SettingsRepository;
use Modules\Invoice\Repositories\Eloquents\OfflinePaymentRepository;
use Modules\Invoice\Repositories\Eloquents\OrderCertificateRepository;
use Modules\Invoice\Repositories\Interfaces\InvoiceRepositoryInterface;
use Modules\Invoice\Repositories\Eloquents\PrintedCertificateRepository;
use Modules\Invoice\Repositories\Interfaces\SettingsRepositoryInterface;
use Modules\Invoice\Repositories\Interfaces\OfflinePaymentRepositoryInterface;
use Modules\Invoice\Repositories\Interfaces\OrderCertificateRepositoryInterface;
use Modules\Invoice\Repositories\Interfaces\PrintedCertificateRepositoryInterface;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(InvoiceRepositoryInterface::class, InvoiceRepository::class);
        $this->app->bind(SettingsRepositoryInterface::class, SettingsRepository::class);
        $this->app->bind(OfflinePaymentRepositoryInterface::class, OfflinePaymentRepository::class);
        $this->app->bind(OrderCertificateRepositoryInterface::class, OrderCertificateRepository::class);
        $this->app->bind(PrintedCertificateRepositoryInterface::class, PrintedCertificateRepository::class);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
