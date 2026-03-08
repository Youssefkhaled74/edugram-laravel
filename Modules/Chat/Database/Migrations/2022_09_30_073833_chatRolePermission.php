<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\RolePermission\Entities\Permission;

class ChatRolePermission extends Migration
{
    public function up()
    {
        $routes = [
            [
                'name' => 'Chat',
                'route' => 'chat',
                'type' => 1,
                'parent_route' => null,
                'module' => 'Chat'
            ],
            [
                'name' => 'Chat Box',
                'route' => 'chat.index',
                'type' => 2,
                'parent_route' => 'chat',
                'module' => 'Chat'
            ], [
                'name' => 'New Chat',
                'route' => 'chat.new',
                'type' => 3,
                'parent_route' => 'chat.index',
                'module' => 'Chat'
            ],
            [
                'name' => 'Invitation',
                'route' => 'chat.invitation',
                'type' => 2,
                'parent_route' => 'chat',
                'module' => 'Chat'
            ],
            [
                'name' => 'Blocked User',
                'route' => 'chat.blocked.users',
                'type' => 2,
                'parent_route' => 'chat',
                'module' => 'Chat'
            ],
            [
                'name' => 'Settings',
                'route' => 'chat.settings',
                'type' => 2,
                'parent_route' => 'chat',
                'module' => 'Chat'
            ],

        ];

        if (function_exists('permissionUpdateOrCreate')) {
            permissionUpdateOrCreate($routes);
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
