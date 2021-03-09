<p align="center"><img src="https://github.com/BinarCode/laravel-mailator/blob/master/docs/logo.png"></p>

<p align="center">
<a href="https://github.com/binarcode/laravel-mailator"><img src="https://github.com/binarcode/laravel-mailator/workflows/Tests/badge.svg" alt="Build Status"></a>
<a href="https://github.com/binarcode/laravel-mailator"><img src="https://poser.pugx.org/binarcode/laravel-mailator/v" alt="Latest Stable Version."></a>
<a href="https://packagist.org/packages/binarcode/laravel-mailator"><img src="https://poser.pugx.org/binarcode/laravel-mailator/license" alt="License"></a>
</p>

Laravel Mailator provides a featherweight system for configure email scheduler and email templates based on application events.

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

1. Email Templates & Placeholders

2. Email Scheduler


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

This will enforce you to implement the `getReplacers` method, this should return an array of replacers to your template. The array may contain instances of `Binarcode\LaravelMailator\Replacers\Replacer` or even `Closure` instances.  

Mailator shipes with a builtin replacer `ModelAttributesReplacer`, it will automaticaly replace attributes from the model you provide to placeholders.

The last step is how to say to your mailable what template to use. This could be done into the build method as shown bellow:

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


## Scheduler

To setup a mail to be sent after or before an event, you can do this by using `MailatorSchedule`. 

Firstly lets setup a mail scheduler:

```php
use Binarcode\LaravelMailator\Tests\Fixtures\InvoiceReminderMailable;use Binarcode\LaravelMailator\Tests\Fixtures\SerializedConditionCondition;

Binarcode\LaravelMailator\Models\MailatorSchedule::init('Invoice reminder.')
    ->mailable(new InvoiceReminderMailable())
    ->recipients(['baz@binarcode.com'])
    ->days(1)
    ->constraint(new SerializedConditionCondition)
    ->before(now()->addYear())
    ->when(function () {
        return 'Working.';
    })
    ->save();
```

The `constraint` mutator accept an instance of `Binarcode\LaravelMailator\Constraints\SendScheduleConstraint`, based on this the Mailator will decide to send or to not send the email.

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

Now you have to run a scheduler command in your Kernel, and call:

```php
Binarcode\LaravelMailator\Models\MailatorSchedule::run();
```

The Mailator will take care of all your mails that needs to be sent, and it will send them.

### Testing

``` bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email eduard.lupacescu@binarcode.com instead of using the issue tracker.

## Credits

- [Eduard Lupacescu](https://github.com/binaryk)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

