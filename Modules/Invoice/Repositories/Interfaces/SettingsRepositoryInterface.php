<?php

namespace Modules\Invoice\Repositories\Interfaces;

use App\Repositories\Interfaces\EloquentRepositoryInterface;

interface SettingsRepositoryInterface extends EloquentRepositoryInterface
{
    public function index():array;
    public function settings():object;
}
