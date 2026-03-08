<?php

namespace Modules\Invoice\Repositories\Interfaces;

use App\Repositories\Interfaces\EloquentRepositoryInterface;

interface PrintedCertificateRepositoryInterface extends EloquentRepositoryInterface
{
    public function index();
}
