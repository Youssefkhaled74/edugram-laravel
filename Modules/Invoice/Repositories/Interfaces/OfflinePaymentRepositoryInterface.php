<?php

namespace Modules\Invoice\Repositories\Interfaces;

use App\Repositories\Interfaces\EloquentRepositoryInterface;

interface OfflinePaymentRepositoryInterface extends EloquentRepositoryInterface
{
    public function index():array;
    public function approve($modelId);
    public function offlinePayments($status = null);
}
