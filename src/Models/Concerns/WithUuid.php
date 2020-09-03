<?php

namespace Binarcode\LaravelMailator\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait WithUuid
{
    public static function bootWithUuid()
    {
        static::creating(function (Model $model) {
            if (! $model->uuid) {
                $model->setAttribute('uuid', Str::uuid());
            }
        });
    }

    public static function whereUuid(string $uuid): Builder
    {
        return static::query()->where('uuid', $uuid);
    }

    public static function firstWhereUuid(string $uuid): Model
    {
        return static::where('uuid', $uuid)->firstOrFail();
    }
}
