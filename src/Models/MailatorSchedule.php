<?php

namespace Binarcode\LaravelMailator\Models;

use Binarcode\LaravelMailator\Actions\Action;
use Binarcode\LaravelMailator\Constraints\SendScheduleConstraint;
use Binarcode\LaravelMailator\Jobs\SendMailJob;
use Binarcode\LaravelMailator\Models\Builders\MailatorSchedulerBuilder;
use Binarcode\LaravelMailator\Models\Concerns\ConstraintsResolver;
use Binarcode\LaravelMailator\Models\Concerns\HasFuture;
use Binarcode\LaravelMailator\Models\Concerns\HasTarget;
use Binarcode\LaravelMailator\Support\ClassResolver;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Closure;
use Illuminate\Contracts\Mail\Mailable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Opis\Closure\SerializableClosure;

/**
 * Class MailatorSchedule.
 *
 * @property string tags
 * @property string name
 * @property string targetable_type
 * @property string targetable_id
 * @property string mailable_class
 * @property string delay_minutes
 * @property string time_frame_origin
 * @property array constraints
 * @property Carbon timestamp_target
 * @property array recipients
 * @property string action
 * @property Closure when
 * @property Carbon last_failed_at
 * @property Carbon last_sent_at
 * @property Carbon completed_at
 * @property string frequency_option
 * @method static MailatorSchedulerBuilder query()
 */
class MailatorSchedule extends Model
{
    use ConstraintsResolver;
    use HasTarget;
    use HasFuture;
    use ClassResolver;

    public function getTable()
    {
        return config('mailator.schedulers_table_name', 'mailator_schedulers');
    }

    public const MINUTES_IN_HOUR = 60;
    public const MINUTES_IN_DAY = 60 * 60;
    public const MINUTES_IN_WEEK = 168 * 60;
    public const HOURS_IN_DAY = 24;
    public const HOURS_IN_WEEK = 168;

    public const FREQUENCY_IN_HOURS = [
        'single' => PHP_INT_MAX,
        'hourly' => 1,
        'daily' => self::HOURS_IN_DAY,
        'weekly' => self::HOURS_IN_WEEK,
    ];

    const DELAY_OPTIONS = [
        '24' => 'Days',
        '168' => 'Weeks',
    ];

    public const TIME_FRAME_ORIGIN_BEFORE = 'before';
    public const TIME_FRAME_ORIGIN_AFTER = 'after';

    public const FREQUENCY_OPTIONS_MANY = 'many';
    public const FREQUENCY_OPTIONS_ONCE = 'once';
    public const FREQUENCY_OPTIONS_HOURLY = 'hourly';
    public const FREQUENCY_OPTIONS_DAILY = 'daily';
    public const FREQUENCY_OPTIONS_WEEKLY = 'weekly';
    public const FREQUENCY_OPTIONS_NEVER = 'never';
    public const FREQUENCY_OPTIONS_MANUAL = 'manual';

    protected $fillable = [
        'name',
        'tags',
        'action',
        'recipients',
        'mailable_class',
        'delay_minutes',
        'time_frame_origin',
        'timestamp_target',
        'constraints',
        'when',
        'frequency_option',
        'last_sent_at',
        'last_failed_at',
        'completed_at',
        'failure_reason',
    ];

    protected $casts = [
        'constraints' => 'array',
        'recipients' => 'array',
        'timestamp_target' => 'datetime',
        'last_failed_at' => 'datetime',
        'last_sent_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    protected $attributes = [
        'frequency_option' => self::FREQUENCY_OPTIONS_ONCE,
    ];

    public static function init(string $name): self
    {
        return new static(['name' => $name]);
    }

    public function mailable(Mailable $mailable): self
    {
        $this->mailable_class = serialize($mailable);

        return $this;
    }

    public function once(): self
    {
        $this->frequency_option = static::FREQUENCY_OPTIONS_ONCE;

        return $this;
    }

    public function manual(): self
    {
        $this->frequency_option = static::FREQUENCY_OPTIONS_MANUAL;

        $this->markComplete();

        return $this;
    }

    public function never(): self
    {
        $this->frequency_option = static::FREQUENCY_OPTIONS_NEVER;

        $this->markComplete();

        return $this;
    }

    public function many(): self
    {
        $this->frequency_option = static::FREQUENCY_OPTIONS_MANY;

        return $this;
    }

    public function hourly(): self
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

    public function after(CarbonInterface $date = null): self
    {
        $this->time_frame_origin = static::TIME_FRAME_ORIGIN_AFTER;

        if ($date) {
            $this->timestamp_target = $date;
        }

        return $this;
    }

    public function contrained(SendScheduleConstraint $constraint): self
    {
        $this->constraint($constraint);

        return $this;
    }

    public function before(CarbonInterface $date = null): self
    {
        $this->time_frame_origin = static::TIME_FRAME_ORIGIN_BEFORE;

        if ($date) {
            $this->timestamp_target = $date;
        }

        return $this;
    }

    public function isDaily(): bool
    {
        return $this->frequency_option === static::FREQUENCY_OPTIONS_DAILY;
    }

    public function isWeekly(): bool
    {
        return $this->frequency_option === static::FREQUENCY_OPTIONS_WEEKLY;
    }

    public function isAfter(): bool
    {
        return $this->time_frame_origin === static::TIME_FRAME_ORIGIN_AFTER;
    }

    public function isBefore(): bool
    {
        return $this->time_frame_origin === static::TIME_FRAME_ORIGIN_BEFORE;
    }

    public function isOnce(): bool
    {
        return $this->frequency_option === static::FREQUENCY_OPTIONS_ONCE;
    }

    public function isNever(): bool
    {
        return $this->frequency_option === static::FREQUENCY_OPTIONS_NEVER;
    }

    public function isManual(): bool
    {
        return $this->frequency_option === static::FREQUENCY_OPTIONS_MANUAL;
    }

    public function isMany(): bool
    {
        return $this->frequency_option === static::FREQUENCY_OPTIONS_MANY;
    }

    public function toDays(): int
    {
        //let's say we have 1 day and 2 hours till day job ends
        //so we will floor it to 1, and will send the reminder in time
        return floor($this->delay_minutes / static::MINUTES_IN_DAY);
    }

    public function toHours(): int
    {
        return floor($this->delay_minutes / static::MINUTES_IN_HOUR);
    }

    public function minutes(int $number): self
    {
        $this->delay_minutes = $number;

        return $this;
    }

    public function hours(int $number): self
    {
        $this->delay_minutes = $number * static::MINUTES_IN_HOUR;

        return $this;
    }

    public function days(int $number): self
    {
        $this->delay_minutes = $number * static::MINUTES_IN_DAY;

        return $this;
    }

    public function weeks(int $number)
    {
        $this->delay_minutes = $number * static::MINUTES_IN_WEEK;

        return $this;
    }

    public function constraint(SendScheduleConstraint $constraint): self
    {
        $this->constraints = array_merge(Arr::wrap($this->constraints), [serialize($constraint)]);

        return $this;
    }

    public function recipients(...$recipients): self
    {
        $this->recipients = array_merge(collect($recipients)
            ->flatten()
            ->filter(fn ($email) => $this->ensureValidEmail($email))
            ->unique()
            ->toArray(), $this->recipients ?? []);

        return $this;
    }

    public function when(Closure $closure): self
    {
        $this->when = serialize(
            new SerializableClosure($closure)
        );

        return $this;
    }

    public function logs()
    {
        return $this->hasMany(MailatorLog::class, 'mailator_schedule_id');
    }

    public function shouldSend(): bool
    {
        $this->load('logs');

        return $this->configurationsPasses() && $this->whenPasses() && $this->eventsPasses();
    }

    public function execute(bool $now = false): void
    {
        $this->save();

        if ($this->hasCustomAction()) {
            unserialize($this->action)->handle($this);

            $this->markAsSent();

            static::garbageResolver()->handle($this);
        } else {
            if ($now) {
                dispatch_sync(new SendMailJob($this));
            } else {
                dispatch(new SendMailJob($this));
            }
        }
    }

    public static function run(): void
    {
        static::query()
            ->ready()
            ->cursor()
            ->filter(fn (self $schedule) => $schedule->shouldSend())
            ->each(fn (self $schedule) => $schedule->execute());
    }

    public function hasCustomAction(): bool
    {
        return ! is_null($this->action);
    }

    public function getMailable(): Mailable
    {
        return unserialize($this->mailable_class);
    }

    public function markAsSent(): self
    {
        $this->logs()
            ->create([
//                'recipients' => $this->getRecipients(),
                'status' => MailatorLog::STATUS_SENT,
                'action_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

        $this->last_sent_at = now();
        $this->save();

        return $this;
    }

    public function markAsFailed(string $failureReason): self
    {
        $this->logs()->create([
            'status' => MailatorLog::STATUS_FAILED,
            'action_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
            'exception' => $failureReason,
        ]);

        $this->update([
            'last_failed_at' => now(),
            'failure_reason' => Str::limit($failureReason, 250),
        ]);

        return $this;
    }

    public function getRecipients(): array
    {
        return collect($this->recipients)
            ->filter(fn ($email) => $this->ensureValidEmail($email))
            ->toArray();
    }

    protected function ensureValidEmail(string $email): bool
    {
        return ! Validator::make(
            compact('email'),
            ['email' => 'required|email']
        )->fails();
    }

    public function actionClass(Action $action): self
    {
        $this->action = serialize($action);

        return $this;
    }

    public function tag(string | array $tag): self
    {
        if (is_array($tag)) {
            $tag = implode(',', $tag);
        }

        $this->tags = $tag;

        return $this;
    }

    public function getReadableConditionAttribute(): string
    {
        if ($this->isManual()) {
            return __('manual');
        }

        $condition = $this->toDays().' day(s)';

        if ($this->toDays() < 1) {
            $condition = $this->toHours().' hour(s) ';
        }

        if ($this->toHours() < 1) {
            $condition = $this->delay_minutes.' minute(s) ';
        }

        $condition .= $this->time_frame_origin." ".$this->timestamp_target?->copy()->format('m/d/Y h:i A');

        return $condition;
    }

    public function newEloquentBuilder($query): MailatorSchedulerBuilder
    {
        return new MailatorSchedulerBuilder($query);
    }

    public function markComplete(): self
    {
        $this->completed_at = now();
        $this->save();

        return $this;
    }
}
