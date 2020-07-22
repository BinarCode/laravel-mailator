<?php

namespace Binarcode\LaravelMailator\Models;

use Binarcode\LaravelMailator\Exceptions\InstanceException;
use Binarcode\LaravelMailator\MailatorEvent;
use Closure;
use Illuminate\Contracts\Mail\Mailable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Opis\Closure\SerializableClosure;

/**
 * Class MailatorSchedule.
 *
 * @property string mailable_class
 * @property string delay_minutes
 * @property string delay_option
 * @property string time_frame_origin
 * @property array events
 * @property Closure when
 * @property string frequency_option
 */
class MailatorSchedule extends Model
{
    public function getTable()
    {
        return config('mailator.schedulers_table_name', 'mailator_schedulers');
    }

    const MINUTES_IN_HOUR = 60;
    const MINUTES_IN_DAY = 60 * 60;
    const MINUTES_IN_WEEK = 168 * 60;
    const HOURS_IN_DAY = 24;
    const HOURS_IN_WEEK = 168;

    const FREQUENCY_IN_HOURS = [
        'single' => PHP_INT_MAX,
        'hourly' => 1,
        'daily' => self::HOURS_IN_DAY,
        'weekly' => self::HOURS_IN_WEEK,
    ];

    const DELAY_OPTIONS = [
        '24' => 'Days',
        '168' => 'Weeks',
    ];

    const TIME_FRAME_ORIGIN_BEFORE = 'before';
    const TIME_FRAME_ORIGIN_AFTER = 'after';

    const FREQUENCY_OPTIONS_ONCE = 'once';
    const FREQUENCY_OPTIONS_HOURLY = 'hourly';
    const FREQUENCY_OPTIONS_DAILY = 'daily';
    const FREQUENCY_OPTIONS_WEEKLY = 'weekly';

    public $events = [];

    protected $fillable = [
        'mailable_class',
        'delay_minutes',
        'delay_option',
        'time_frame_origin',
        'events',
        'when',
        'frequency_option',
    ];

    protected $casts = [
        'events' => 'array',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'start_at',
        'end_at',
    ];

    public static function init(string $name): self
    {
        return new static(['name' => $name]);
    }

    public function mailable(Mailable $mailable)
    {
        $this->mailable_class = serialize($mailable);

        return $this;
    }

    public function once()
    {
        $this->frequency_option = static::FREQUENCY_OPTIONS_ONCE;

        return $this;
    }

    public function hourly()
    {
        $this->frequency_option = static::FREQUENCY_OPTIONS_HOURLY;

        return $this;
    }

    public function daily()
    {
        $this->frequency_option = static::FREQUENCY_OPTIONS_DAILY;

        return $this;
    }

    public function weekly()
    {
        $this->frequency_option = static::FREQUENCY_OPTIONS_WEEKLY;

        return $this;
    }

    public function after(string $event = null)
    {
        $this->time_frame_origin = static::TIME_FRAME_ORIGIN_AFTER;

        if ($event) {
            $this->event($event);
        }

        return $this;
    }

    public function before(string $event = null)
    {
        $this->time_frame_origin = static::TIME_FRAME_ORIGIN_BEFORE;

        if ($event) {
            $this->event($event);
        }

        return $this;
    }

    public function minutes(int $number)
    {
        $this->delay_minutes = $number;

        return $this;
    }

    public function hours(int $number)
    {
        $this->delay_minutes = $number * static::MINUTES_IN_HOUR;

        return $this;
    }

    public function days(int $number)
    {
        $this->delay_minutes = $number * static::MINUTES_IN_DAY;

        return $this;
    }

    public function weeks(int $number)
    {
        $this->delay_minutes = $number * static::MINUTES_IN_WEEK;

        return $this;
    }

    public function event(string $event)
    {
        if (! is_a(MailatorEvent::class, $event)) {
            InstanceException::throw($event);
        }

        $this->events = Arr::wrap($this->events) + [$event];

        return $this;
    }

    public function when(Closure $closure)
    {
        $this->when = serialize(
            new SerializableClosure($closure)
        );

        return $this;
    }
}
