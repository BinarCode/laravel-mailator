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

    /**
     * > The base model for the mail schedule.
     */
    'scheduler_model' => Binarcode\LaravelMailator\Models\MailatorSchedule::class,
];
