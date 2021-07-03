<?php

namespace Binarcode\LaravelMailator\Models;

use Binarcode\LaravelMailator\Actions\Action;
use Binarcode\LaravelMailator\Actions\ResolveGarbageAction;
use Binarcode\LaravelMailator\Actions\RunSchedulersAction;
use Binarcode\LaravelMailator\Constraints\SendScheduleConstraint;
use Binarcode\LaravelMailator\Jobs\SendMailJob;
use Binarcode\LaravelMailator\Models\Builders\MailatorSchedulerBuilder;
use Binarcode\LaravelMailator\Models\Concerns\ConstraintsResolver;
use Binarcode\LaravelMailator\Models\Concerns\HasFuture;
use Binarcode\LaravelMailator\Models\Concerns\HasTarget;
use Binarcode\LaravelMailator\Support\ClassResolver;
use Binarcode\LaravelMailator\Support\ConverterEnum;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Closure;
use Exception;
use Illuminate\Contracts\Mail\Mailable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Opis\Closure\SerializableClosure;
use Throwable;
use TypeError;

/**
 * Class MailatorSchedule.
 *
 * @property string tags
 * @property string name
 * @property string stopable
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

    public const TIME_FRAME_ORIGIN_BEFORE = 'before';
    public const TIME_FRAME_ORIGIN_AFTER = 'after';

    public const FREQUENCY_OPTIONS_MANY = 'many';
    public const FREQUENCY_OPTIONS_ONCE = 'once';
    public const FREQUENCY_OPTIONS_HOURLY = 'hourly';
    public const FREQUENCY_OPTIONS_DAILY = 'daily';
    public const FREQUENCY_OPTIONS_WEEKLY = 'weekly';
    public const FREQUENCY_OPTIONS_NEVER = 'never';
    public const FREQUENCY_OPTIONS_MANUAL = 'manual';

    protected $guarded = [];

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
        'completed_at' => 'datetime',
        'stopable' => 'boolean',
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

    public function times(int $count): self
    {
        // todo
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

    public function daily(): static
    {
        $this->frequency_option = static::FREQUENCY_OPTIONS_DAILY;

        return $this;
    }

    public function weekly(): static
    {
        $this->frequency_option = static::FREQUENCY_OPTIONS_WEEKLY;

        return $this;
    }

    public function stopable(bool $stopable = true): self
    {
        $this->stopable = $stopable;

        return $this;
    }

    public function isStopable(): bool
    {
        return (bool) $this->stopable;
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
        return (int) floor($this->delay_minutes / ConverterEnum::MINUTES_IN_DAY);
    }

    public function toHours(): int
    {
        return (int) floor($this->delay_minutes / ConverterEnum::MINUTES_IN_HOUR);
    }

    public function minutes(int $number): self
    {
        $this->delay_minutes = $number;

        return $this;
    }

    public function hours(int $number): self
    {
        $this->delay_minutes = $number * ConverterEnum::MINUTES_IN_HOUR;

        return $this;
    }

    public function days(int $number): self
    {
        $this->delay_minutes = $number * ConverterEnum::MINUTES_IN_DAY;

        return $this;
    }

    public function weeks(int $number): static
    {
        $this->delay_minutes = $number * ConverterEnum::MINUTES_IN_WEEK;

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

    public function logs(): HasMany
    {
        return $this->hasMany(MailatorLog::class, 'mailator_schedule_id');
    }

    public function shouldSend(): bool
    {
        try {
            $this->load('logs');

            if (! $this->configurationsPasses()) {
                return false;
            }

            if (! $this->whenPasses()) {
                return false;
            }

            if (! $this->eventsPasses()) {
                $this->markComplete();

                return false;
            }

            return true;
        } catch (Exception | Throwable $e) {
            $this->markAsFailed($e->getMessage());

            app(ResolveGarbageAction::class)->handle($this);

            return false;
        }
    }

    public function executeWhenPasses(bool $now = false): void
    {
        $this->save();

        if ($this->shouldSend()) {
            $this->execute($now);
        }
    }

    public function execute(bool $now = false): void
    {
        $this->save();

        try {
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
        } catch (Exception | Throwable $e) {
            $this->markAsFailed($e->getMessage());
        }
    }

    public static function run(): void
    {
        app(RunSchedulersAction::class)();
    }

    public function hasCustomAction(): bool
    {
        return ! is_null($this->action);
    }

    public function getMailable(): ?Mailable
    {
        try {
            return unserialize($this->mailable_class);
        } catch (Throwable | TypeError $e) {
            $this->markAsFailed($e->getMessage());
        }

        return null;
    }

    public function markAsSent(): self
    {
        $this->logs()
            ->create([
                'recipients' => $this->getRecipients(),
                'name' => $this->name,
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
            'recipients' => $this->getRecipients(),
            'name' => $this->name,
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
            return (string) __('manual');
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

    public function isCompleted(): bool
    {
        return ! is_null($this->completed_at);
    }

    public function failedLastTimes(int $times): bool
    {
        return $this
                ->logs()
                ->latest()
                ->take($times)
                ->get()
                ->filter
                ->isFailed()
                ->count() === $times;
    }

    public function timestampTarget(): ?CarbonInterface
    {
        return $this->timestamp_target?->clone();
    }

    public function isRepetitive(): bool
    {
        return ! $this->isOnce();
    }
}
