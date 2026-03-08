<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateGeneralSettingsTable extends Migration
{
    public function up()
    {
        if (!DB::table('general_settings')->where('key', 'pusher_app_id')->exists()) {
            $arr = [
                "pusher_app_id" => null,
                "pusher_app_key" => null,
                "pusher_app_secret" => null,
                "pusher_app_cluster" => null,
                "chatting_method" => "jquery",
                "chat_invitation_requirement" => "required",
                "chat_file_limit" => "200",
                "chat_can_upload_file" => "yes",
                "chat_can_make_group" => "yes",
                "chat_staff_or_teacher_can_ban_student" => null,
                "chat_teacher_can_pin_top_message" => null,
                "chat_generate" => "none",
                "chat_everyone_to_everyone" => "no",
                "chat_admin_can_chat_without_invitation" => "no",
                "chat_open" => "no",
                "chat_language_cache" => null
            ];
            foreach ($arr as $key => $value) {
                DB::table('general_settings')->insert([
                    'key' => $key,
                    'value' => $value
                ]);
            }
            GenerateGeneralSetting(SaasDomain());

        }
    }

    public function down()
    {

    }
}
