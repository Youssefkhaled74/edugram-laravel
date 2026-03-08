<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\SystemSetting\Entities\EmailTemplate;

class AddEmailTemplateForCertificateOrder extends Migration
{

    public function up()
    {
        EmailTemplate::insert([
            'act' => 'certificate_order',
            'name' => 'Certificate Order',
            'subj' => 'Certificate Order',
            'email_body' => '{{student_name}} Order {{course}} Certificate and shipping {{address}}.  {{footer}} ',
            'shortcodes' => '{"student_name":"Student Name", "course":"Course Name"}',
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        EmailTemplate::insert([
            'act' => 'certificate_shipped',
            'name' => 'Certificate Shipped',
            'subj' => 'Certificate Shipped',
            'email_body' => '{{student_name}} Order {{course}} Certificate have been shipped .your shipping {{address}} .  {{footer}} ',
            'shortcodes' => '{"student_name":"Student Name", "course":"Course Name", "address":"Address"}',
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('role_email_templates')->insert(
            [
            'role_id' => 1,
            'template_act' => 'certificate_order',
            'status' => 1,
            ]);
        DB::table('role_email_templates')->insert(
            [
            'role_id' => 3,
            'template_act' => 'certificate_shipped',
            'status' => 1,
            ]);
    }

    public function down()
    {
        //
    }
}
