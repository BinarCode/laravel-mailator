<?php

return [
    /**
     * > The table name for the main schedulers.
     */
    'schedulers_table_name' => 'mailator_schedulers',

    /*
     > The table name for the logs of sends history.
     */
    'logs_table' => 'mailator_logs',

    'scheduler' => [
        /**
         * > The base model for the mail schedule.
         */
        'model' => Binarcode\LaravelMailator\Models\MailatorSchedule::class,

        /**
        * > The queue used for sending emails.
        */
        'send_mail_job_queue' => 'default',

        /**
        > The email sender class. It will be executed from the sender job.
         */
        'send_mail_action' => Binarcode\LaravelMailator\Actions\SendMailAction::class,

        /**
         * Class that will mark the scheduled action as being completed so the action will do not be counted into the next iteration.
         */
        'garbage_resolver' => Binarcode\LaravelMailator\Actions\ResolveGarbageAction::class,

        /**
         * Mark action completed after this count of fails.
         */
        'mark_complete_after_fails_count' => env('MAILATOR_FAILED_COUNTS', 3),
    ],

    'log_model' => Binarcode\LaravelMailator\Models\MailatorLog::class,

    'templates' => [
        /**
         * > The base model for the mail templates.
         */
        'template_model' => Binarcode\LaravelMailator\Models\MailTemplate::class,

        /**
         * > The base model for the mail template placeholders.
         */
        'placeholder_model' => Binarcode\LaravelMailator\Models\MailTemplatePlaceholder::class,

        /**
         > The email layout, used to wrap the template.
         */
        'template_layout' => 'laravel-mailator::laravel',

        /**
        > The default list with replacers for the template.
         */
        'replacers' => [
            Binarcode\LaravelMailator\Replacers\SampleReplacer::class,
        ],
    ],
];
