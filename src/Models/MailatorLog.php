<?php

namespace Binarcode\LaravelMailator\Models;

use Illuminate\Database\Eloquent\Model;

class MailatorLog extends Model
{
    public function getTable()
    {
        return config('mailator.logs_table', 'mailator_logs');
    }

    const STATUS_FAILED = 'failed';
    const STATUS_SENT = 'sent';

    protected $fillable = [
        'name',
        'recipients',
        'mailator_schedule_id',
        'status',
        'action_at',
        'exception',
        'created_at',
        'updated_at',
    ];

    protected $cast = [
        'action_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'recipients' => 'array',
    ];
}
