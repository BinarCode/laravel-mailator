<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Binarcode\LaravelMailator\Models\MailatorLog;
use Binarcode\LaravelMailator\Models\MailatorSchedule;

class AlterMailLogsTableAddCascadeDelete extends Migration
{
    public function up()
    {
        Schema::table(config('mailator.logs_table', 'mailator_logs'), function (Blueprint $table) {
            $table->dropForeign(['mailator_schedule_id']);

            $table->foreign('mailator_schedule_id')
                ->references('id')
                ->on(config('mailator.schedulers_table_name', 'mailator_schedulers'))
                ->cascadeOnDelete();
        });
    }
}
