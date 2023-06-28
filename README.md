<p align="center"><img src="https://github.com/BinarCode/laravel-mailator/blob/master/docs/logo.png"></p>

<p align="center">
<a href="https://github.com/binarcode/laravel-mailator"><img src="https://github.com/binarcode/laravel-mailator/workflows/Tests/badge.svg" alt="Build Status"></a>
<a href="https://github.com/binarcode/laravel-mailator"><img src="https://poser.pugx.org/binarcode/laravel-mailator/v" alt="Latest Stable Version."></a>
<a href="https://packagist.org/packages/binarcode/laravel-mailator"><img src="https://img.shields.io/packagist/dt/binarcode/laravel-mailator" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/binarcode/laravel-mailator"><img src="https://poser.pugx.org/binarcode/laravel-mailator/license" alt="License"></a>
</p>

Laravel Mailator provides a featherweight system for configure email scheduler and email templates based on application
events.

## Installation

You can install the package via composer:

```bash
composer require binarcode/laravel-mailator
```

## Publish

Publish migrations: `a vendor:publish --tag=mailator-migrations`

Publish config: `a vendor:publish --tag=mailator-config`

## Usage

It has mainly 2 directions of usage:

1. Schedule emails sending (or actions triggering)

2. Email Templates & Placeholders

## Scheduler

To set up a mail to be sent after or before an event, you can do this by using the `Scheduler` facade.

Here is an example of how to send the `invoice reminder email` `3 days` before the `$invoice->due_date`:

```php
use Binarcode\LaravelMailator\Tests\Fixtures\InvoiceReminderMailable;
use Binarcode\LaravelMailator\Tests\Fixtures\SerializedConditionCondition;

Binarcode\LaravelMailator\Scheduler::init('Invoice reminder.')
    ->mailable(new InvoiceReminderMailable($invoice))
    ->recipients('foo@binarcode.com', 'baz@binarcode.com')
    ->constraint(new SerializedConditionCondition($invoice))
    ->days(3)
    ->before($invoice->due_date)
    ->save();
```

Let's explain what each line means.

### Mailable

This should be an instance of laravel `Mailable`.

### Recipients

This should be a list or valid emails where the email will be sent.

It could be an array of emails as well.

### Weeks

This should be a number of weeks the email should be delayed. 

### Days

This should be a number of days the email should be delayed. 

### Hours

Instead of `days()` you can use `hours()` as well.

### Minutes

If your scheduler run by minute, you can also use `minutes()` to delay the email.

### Before

The `before` constraint accept a `CarbonInterface` and indicates from when scheduler should start run the mail or action. For instance:

```php
    ->days(1)
    ->before(Carbon::make('2021-02-06'))
```

says, send this email `1 day before 02 June 2021`, so basically the email will be scheduled for `01 June 2021`.

### After

The `after` constraint accept a `CarbonInterface` as well. The difference, is that it inform scheduler to send it `after` the specified timestamp. Say we want to send a survey email `1 week` after the order is placed:

```php
    ->weeks(1)
    ->after($order->created_at)
```

### Precision
Hour Precision

The `precision` method provides fine-grained control over when emails are sent using MailatorSchedule. It allows you to specify specific hours or intervals within a 24-hour period. Here's an example of how to use the precision method:
```php
    ->many()
    ->precision([3-4])
```
This will schedule the email dispatch between '03:00:00' AM and '04:59:59' AM.

or
```php
    ->once()
    ->precision([1])
```
This will schedule the email dispatch between '01:00:00' AM and '01:59:59'.

You can continue this pattern to specify the desired hour(s) within the range of 1 to 24.

**Important: When using the precision feature in the Mailator scheduler, it is recommended to set the scheduler to run at intervals that are less than an hour. You can choose intervals such as every 5 minutes, 10 minutes, 30 minutes, or any other desired duration.**
### Constraint

The `constraint()` method accept an instance of `Binarcode\LaravelMailator\Constraints\SendScheduleConstraint`. Each constraint will be called when the scheduler will try to send the email. If all constraints return true, the email will be sent.

The `constraint()` method could be called many times, and each constraint will be stored. 

Since each constraint will be serialized, it's very indicated to use `Illuminate\Queue\SerializesModels` trait, so the serialized models will be loaded properly, and the data stored in your storage system will be much less.

Let's assume we have this `BeforeInvoiceExpiresConstraint` constraint:

```php
class BeforeInvoiceExpiresConstraint implements SendScheduleConstraint
{
    public function canSend(MailatorSchedule $mailatorSchedule, Collection $log): bool
    {
        // your conditions
        return true;
    }
}
```

### Constraintable

Instead of defining the `constraint` from the mail definition, sometimes it could be more readable if you define it directly into the `mailable` class: 

```php
use Binarcode\LaravelMailator\Constraints\Constraintable;

class InvoiceReminderMailable extends Mailable implements Constraintable
{
    public function constraints(): array
    {
        return [
            new DynamicContraint
        ];
    }
}

```

### Action

Using `Scheduler` you can even define your custom action:

```php
$scheduler = Scheduler::init('Invoice reminder.')
    ->days(1)
    ->before(now()->addWeek())
    ->actionClass(CustomAction::class)
    ->save();
```

The `CustomAction` should implement the `Binarcode\LaravelMailator\Actions\Action` class.

### Target

You can link the scheduler with any entity like this:

```php
        Scheduler::init('Invoice reminder.')
            ->mailable(new InvoiceReminderMailable())
            ->days(1)
            ->target($invoice)
            ->save();
```

and then in the `Invoice` model you can get all emails related to it: 

```php
// app/Models/Invoice.php
public function schedulers() 
{
    return $this->morphMany(Binarcode\LaravelMailator\Models\MailatorSchedule::class, 'targetable');
}
...
```

Mailator provides the `Binarcode\LaravelMailator\Models\Concerns\HasMailatorSchedulers` trait you can put in your Invoice model, so the relations will be loaded.

### Daily

By default, scheduler run the action, or send the email only once. You can change that, and use a daily reminder till the constraint returns a truth condition:

```php
use Binarcode\LaravelMailator\Scheduler;

// 2021-20-06 - 20 June 2021
$expirationDate = $invoice->expire_at;

Scheduler::init('Invoice reminder')
->mailable(new InvoiceReminderMailable())
->daily()
->weeks(1)
->before($expirationDate)
```

This scheduler will send the `InvoiceReminderMailable` email daily starting with `13 June 2021` (one week before the expiration date).

How to stop the email sending if the invoice was paid meanwhile? Simply adding a constraint that will do not send it: 

```php
->constraint(new InvoicePaidConstraint($invoice))
```

and the constraint handle method could be something like this: 

```php
class InvoicePaidConstraint implements SendScheduleConstraint
{
    use SerializesModels;
    
    public function __construct(
        private Invoice $invoice
    ) { }

    public function canSend(MailatorSchedule $schedule, Collection $logs): bool
    {
        return is_null($this->invoice->paid_at);
    }
}
```

## Stop conditions

There are few ways email stop to be sent.

The first condition, is that if for some reason sending email fails 3 times, the `MailatorSchedule` will be marked as `completed_at`. Number of times could be configured in the config file `mailator.scheduler.mark_complete_after_fails_count`.

Any successfully sent mail, that should be sent only once, will be marked as `completed_at`.


### Stopable

You can configure your scheduler to be marked as `completed_at` if in the you custom constraint returns a falsy condition. Back to our `InvoiceReminderMailable`, say the invoice expires on `20 June`, we send the first reminder on `13 June`, then the second reminder on `14 June`, if the client pay the invoice on `14 June` the `InvoicePaidConstraint` will return a falsy value, so there is no reason to try to send the invoice reminder on `15 June` again. So the system could mark this scheduler as `completed_at`.


To do so, you can use the `stopable()` method.

### Unique

You can configure your scheduler to store a unique relationship with the target class for mailable by specifying: 

```php
->unique()
```

ie: 

```php
Scheduler::init()
    ->mailable(new InvoiceReminderMailable())
    ->target($user)
    ->unique()
    ->save();
    
Scheduler::init()
    ->mailable(new InvoiceReminderMailable())
    ->target($user)
    ->unique()
    ->save();
```

This will store a single scheduler for the `$user`. 

## Events

Mailator has few events you can use. 

If your mailable class extends the `Binarcode\LaravelMailator\Contracts\Beforable`, you will be able to inject the `before` method, that will be called right before the sending the email. 

If your mailable class extends the `Binarcode\LaravelMailator\Contracts\Afterable`, you will be able to inject the `after` method, that will be called right after the mail has being sent.


And latest, after each mail has being sent, mailator will fire the `Binarcode\LaravelMailator\Events\ScheduleMailSentEvent` event, so you can listen for it.

## Run

Now you have to run a scheduler command in your Kernel, and call:

```php
Binarcode\LaravelMailator\Scheduler::run();
```

Package provides the `Binarcode\LaravelMailator\Console\MailatorSchedulerCommand` command you can put in your Console Kernel: 

```php
$schedule->command(MailatorSchedulerCommand::class)->everyThirtyMinutes();
```


## Templating

To create an email template:

``` php
$template = Binarcode\LaravelMailator\Models\MailTemplate::create([
    'name' => 'Welcome Email.',
    'from_email' => 'from@bar.com',
    'from_name' => 'From Bar',
    'subject' => 'Welcome to Mailator.',
    'html' => '<h1>Welcome to the party!</h1>',
]);
```

Adding some placeholders with description to this template:

```php
$template->placeholders()->create(
    [
        'name' => '::name::',
        'description' => 'Name',
    ],
);
```

To use the template, you simply have to add the `WithMailTemplate` trait to your mailable.

This will enforce you to implement the `getReplacers` method, this should return an array of replacers to your template.
The array may contain instances of `Binarcode\LaravelMailator\Replacers\Replacer` or even `Closure` instances.

Mailator shipes with a builtin replacer `ModelAttributesReplacer`, it will automaticaly replace attributes from the
model you provide to placeholders.

The last step is how to say to your mailable what template to use. This could be done into the build method as shown
bellow:

```php
class WelcomeMailatorMailable extends Mailable
{
    use Binarcode\LaravelMailator\Support\WithMailTemplate;
    
    private Model $user;
    
    public function __construct(Model $user)
    {
        $this->user = $user;
    }
    
    public function build()
    {
        return $this->template(MailTemplate::firstWhere('name', 'Welcome Email.'));
    }

    public function getReplacers(): array
    {
        return [
            Binarcode\LaravelMailator\Replacers\ModelAttributesReplacer::makeWithModel($this->user),

            function($html) {
                //
            }       
        ];
    }
}
```

### Testing

``` bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email eduard.lupacescu@binarcode.com or [message me on twitter](https://twitter.com/LupacescuEuard) instead of using the issue tracker.

## Credits

- [Eduard Lupacescu](https://github.com/binaryk)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
