<?php

namespace Modules\Invoice\Repositories\Eloquents;

use Modules\Invoice\Entities\InvoiceSetting;
use App\Repositories\Eloquents\BaseRepository;
use Modules\Invoice\Repositories\Interfaces\SettingsRepositoryInterface;

class SettingsRepository extends BaseRepository implements SettingsRepositoryInterface
{
    public function __construct(
        InvoiceSetting $model
    ) {
        parent::__construct($model);
    }
    public function index():array
    {
        $data = [];
        $data['settings'] = $this->settings();
        return $data;
    }
    public function update(int $modelId, array $payload): bool
    {
        $model = $this->findById($modelId);
        $updated = $model->update($this->formatParam($payload, $modelId));
        $updatedModel = $this->findById($modelId);
        if($updated) {
            session()->put('invoice_prefix', $updatedModel->prefix);
        }
        return $updated;
    }
    private function formatParam($payload, $modelId = null)
    {
        $formatParams = [
            'prefix'=>gv($payload, 'prefix'),
            'footer_text'=>gv($payload, 'footer_text'),
        ];
        return $formatParams;
    }
    public function settings():object
    {
        return $this->model->first();
    }
}
