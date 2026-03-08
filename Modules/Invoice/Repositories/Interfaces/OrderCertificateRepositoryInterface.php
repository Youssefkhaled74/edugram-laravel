<?php

namespace Modules\Invoice\Repositories\Interfaces;

use App\Repositories\Interfaces\EloquentRepositoryInterface;

interface OrderCertificateRepositoryInterface extends EloquentRepositoryInterface
{
    public function index():array;
    public function orderNow($certificate_id);
    public function pdfPrint($modelId);
    public function changesStatus(int $modelId, string $status = null):bool;
}