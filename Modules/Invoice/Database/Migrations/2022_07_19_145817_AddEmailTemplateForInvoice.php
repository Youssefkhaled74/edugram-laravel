<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\SystemSetting\Entities\EmailTemplate;

class AddEmailTemplateForInvoice extends Migration
{

    public function up()
    {

        EmailTemplate::insert([
            'act' => 'invoice',
            'name' => 'Invoice',
            'subj' => 'Invoice',
            'email_body' => 'Thanks For Purchase . Your Invoice <br> {{invoice}}.  {{footer}} ',
            'shortcodes' => '{"invoice":"invoice"}',
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        EmailTemplate::insert([
            'act' => 'payment_link',
            'name' => 'Payment Link',
            'subj' => 'Payment Now',
            'email_body' => 'Hi {{student_name}}, You have  Received an Invoice <br> Invoice Number is:{{invoice_number}} .<br> Please click here for Payment {{payment_link}}   {{footer}} ',
            'shortcodes' => '{"student_name":"Student Name", "invoice_number":"invoice number", "payment_link", "Payment Link", "invoice":"Invoice PDF"}',
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('role_email_templates')->insert(
            [
                'role_id' => 3,
                'template_act' => 'invoice',
                'status' => 1,
            ]);
        DB::table('role_email_templates')->insert(

            [
                'role_id' => 3,
                'template_act' => 'payment_link',
                'status' => 1,
            ]);
    }

    public function down()
    {

    }
}
