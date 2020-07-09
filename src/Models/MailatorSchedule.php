<?php

namespace Binarcode\LaravelMailator\Models;

use Illuminate\Database\Eloquent\Model;

class MailatorSchedule extends Model
{
    public function getTable()
    {
        return config('mailator.schedulers_table_name', 'mailator_schedulers');
    }
}
