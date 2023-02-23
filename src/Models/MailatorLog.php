<?php

namespace Binarcode\LaravelMailator\Models;

use Binarcode\LaravelMailator\Models\Concerns\WithPrune;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MailatorLog
 * @property string $name
 * @property string $status
 * @property Carbon $created_at
 * @property Arrayable|array $recipients
 * @property Carbon $action_at
 * @property Carbon $updated_at
 * @property Carbon $exception
 * @package Binarcode\LaravelMailator\Models
 */
class MailatorLog extends Model
{
    use WithPrune;
    public function getTable()
    {
        return config('mailator.logs_table', 'mailator_logs');
    }

    public const STATUS_FAILED = 'failed';
    public const STATUS_SENT = 'sent';

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

    protected $casts = [
        'action_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'recipients' => 'array',
    ];

    public function isSent(): bool
    {
        return $this->status === static::STATUS_SENT;
    }

    public function isFailed(): bool
    {
        return $this->status === static::STATUS_FAILED;
    }
}
