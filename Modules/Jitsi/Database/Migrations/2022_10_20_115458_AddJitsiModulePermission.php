<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\RolePermission\Entities\Permission;

class AddJitsiModulePermission extends Migration
{
    public function up()
    {
        $routes = [
            ['name' => 'Jitsi', 'route' => 'jitsi', 'type' => 1, 'parent_route' => null, 'module' => 'Jitsi'],
            ['name' => 'Setting', 'route' => 'jitsi.settings', 'type' => 2, 'parent_route' => 'jitsi', 'module' => 'Jitsi'],

        ];

        if (function_exists('permissionUpdateOrCreate')) {
            permissionUpdateOrCreate($routes);
        }
    }

    public function down()
    {
        //
    }
}
