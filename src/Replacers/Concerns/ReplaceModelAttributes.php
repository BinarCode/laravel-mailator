<?php

namespace Binarcode\LaravelMailator\Replacers\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * This will replace the match $replaceText with an attribute from the model into the provided large email $text.
 *
 * E.g.
 * $text => "Welcome ::first_name::"
 * $replaceText => "first_name"
 * $model => {first_name: 'Eduard', ...}
 *
 * @return => "Welcome Eduard"
 */
trait ReplaceModelAttributes
{
    public function replaceModelAttributes(string $text, string $replaceText, Model $model)
    {
        return preg_replace_callback('/::' . $replaceText . '::/', function ($match) use ($model) {
            $parts = collect(explode('.', $match[0] ?? ''));

            $replace = $parts->reduce(function ($value, $part) {
                $part = Str::between($part, '::', '::');

                return $value->$part
                    ?? $value[$part]
                    ?? '';
            }, $model);

            return $replace ?? $match;
        }, $text);
    }
}
