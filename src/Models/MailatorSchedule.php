<?php

namespace Binarcode\LaravelMailator\Models;

use Binarcode\LaravelMailator\Actions\Action;
use Binarcode\LaravelMailator\Constraints\SendScheduleConstraint;
use Binarcode\LaravelMailator\Exceptions\InstanceException;
use Binarcode\LaravelMailator\Jobs\SendMailJob;
use Binarcode\LaravelMailator\Models\Concerns\ConstraintsResolver;
use Binarcode\LaravelMailator\Models\Concerns\HasTarget;
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
 * @property string tag
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
 * @property string frequency_option
 */
class MailatorSchedule extends Model
{
    use ConstraintsResolver;
    use HasTarget;

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

    const FREQUENCY_OPTIONS_MANY = 'many';
    const FREQUENCY_OPTIONS_ONCE = 'once';
    const FREQUENCY_OPTIONS_HOURLY = 'hourly';
    const FREQUENCY_OPTIONS_DAILY = 'daily';
    const FREQUENCY_OPTIONS_WEEKLY = 'weekly';

    protected $fillable = [
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
        'failure_reason',
    ];

    protected $casts = [
        'constraints' => 'array',
        'recipients' => 'array',
        'timestamp_target' => 'datetime',
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

    public function recipients(array $recipients): self
    {
        $this->recipients = collect($recipients)
            ->filter(fn ($email) => $this->ensureValidEmail($email))
            ->toArray();

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

    public function execute(): void
    {
        $this->save();

        if ($this->hasCustomAction()) {
            app($this->action)->handle($this);
        } else {
            dispatch(new SendMailJob($this));
        }
    }

    public static function run(): void
    {
        static::query()
            ->cursor()
            ->filter(fn (self $schedule) => $schedule->shouldSend())
            ->each(fn (self $schedule) => $schedule->execute());
    }

    public function hasCustomAction(): bool
    {
        return ! is_null($this->action) && is_subclass_of($this->action, Action::class);
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

    public function actionClass(string $action): self
    {
        if (! is_subclass_of($action, Action::class)) {
            throw InstanceException::throw(Action::class);
        }

        $this->action = $action;

        return $this;
    }

    public function tag(string | array $tag): self
    {
        if (is_array($tag)) {
            $tag = implode(',', $tag);
        }

        $this->tag = $tag;

        return $this;
    }
}
