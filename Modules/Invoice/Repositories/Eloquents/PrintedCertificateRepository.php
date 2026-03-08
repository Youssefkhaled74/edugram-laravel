<?php

namespace Modules\Invoice\Repositories\Eloquents;

use App\Repositories\Eloquents\BaseRepository;
use Modules\Invoice\Entities\PrintedCertificate;
use Modules\Invoice\Repositories\Interfaces\PrintedCertificateRepositoryInterface;

class PrintedCertificateRepository extends BaseRepository implements PrintedCertificateRepositoryInterface
{
    public function __construct(
        PrintedCertificate $model
    ) {
        parent::__construct($model);
    }
    public function index()
    {
        return $this->model->first();
    }
    public function update(int $modelId, array $payload): bool
    {
        $model = $this->findById($modelId);
        return $model->update($this->formatParam($payload, $modelId));
    }
    private function formatParam($payload, $modelId = null)
    {
        $formatParam = [
            'price'=>gv($payload, 'price'),
            'title'=>gv($payload, 'title')
        ];
        return $formatParam;
    }
}