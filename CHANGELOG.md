# Changelog

All notable changes to `laravel-mailator` will be documented in this file

## 2.0.0 - 2021-08-03

- The `before` and `after` methods now accept only an instance of Carbon 
- You can add custom contraints using `->contraint( Binarcode\LaravelMailator\Constraints\SendScheduleConstraint)` method.

- The scheduler will now take care of your configurations, so if you don't specify any constraint, it will send the email based on the scheduler configurations.
- Changed `events` column name to `constraints`
- Added `timestamp_target` timestamp column



## 1.0.0 - 2020-02-07

- initial release
