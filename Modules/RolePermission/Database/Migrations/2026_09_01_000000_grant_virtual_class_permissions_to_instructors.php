<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class GrantVirtualClassPermissionsToInstructors extends Migration
{
    public function up()
    {
        $permissionIds = DB::table('permissions')
            ->whereIn('route', [
                'virtual-class.index',
                'virtual-class.create',
                'virtual-class.edit',
                'virtual-class.destroy',
                'virtual-class.details',
            ])
            ->pluck('id');

        foreach ($permissionIds as $permissionId) {
            $exists = DB::table('role_permission')
                ->where('role_id', 2)
                ->where('permission_id', $permissionId)
                ->exists();

            if (!$exists) {
                DB::table('role_permission')->insert([
                    'permission_id' => $permissionId,
                    'role_id' => 2,
                    'status' => 1,
                    'created_by' => 1,
                    'updated_by' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down()
    {
        $permissionIds = DB::table('permissions')->whereIn('route', [
            'virtual-class.index', 'virtual-class.create', 'virtual-class.edit',
            'virtual-class.destroy', 'virtual-class.details',
        ])->pluck('id');

        DB::table('role_permission')->where('role_id', 2)->whereIn('permission_id', $permissionIds)->delete();
    }
}
