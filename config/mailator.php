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
        > The queue used for sending emails.
         */
        'send_mail_job_queue' => 'default',

        /**
        > The email sender class. It will be executed from the sender job.
         */
        'send_mail_action' => Binarcode\LaravelMailator\Actions\SendMailAction::class,
    ],

    'log_model' => Binarcode\LaravelMailator\Models\MailatorLog::class,

    'templates' => [
        /**
         > The email layout, used to wrap the template.
         */
        'laravel-mailator::mails.template_layout' => 'laravel-mailator::mails.template_layout',

        /**
        > The default list with replacers for the template.
         */
        'replacers' => [
            Binarcode\LaravelMailator\Replacers\SampleReplacer::class,
        ],
    ],
];
