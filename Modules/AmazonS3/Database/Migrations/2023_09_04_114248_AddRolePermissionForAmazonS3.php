<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRolePermissionForAmazonS3 extends Migration
{
    public function up()
    {
        $routes = [
            ['name' => 'Amazon S3', 'route' => 'amazons3', 'type' => 1, 'parent_route' => null, 'module' => 'AmazonS3'],
            ['name' => 'Setting', 'route' => 'AwsS3Setting', 'type' => 2, 'parent_route' => 'amazons3', 'module' => 'AmazonS3'],
            ['name' => 'Submit', 'route' => 'AwsS3SettingSubmit', 'type' => 3, 'parent_route' => 'AwsS3Setting', 'module' => 'AmazonS3'],
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
