<?php

namespace Binarcode\LaravelMailator\Replacers;

use Binarcode\LaravelMailator\Models\MailTemplateable;
use Binarcode\LaravelMailator\Replacers\Concerns\ReplaceModelAttributes;
use Illuminate\Database\Eloquent\Model;

class PlaceholdersReplacer implements Replacer
{
    use ReplaceModelAttributes;

    /** * @var Model */
    protected $model;

    public function replace(string $html, MailTemplateable $template): string
    {
        return $template->preparePlaceholders()
            ->flatten()
            ->reduce(function (string $html, $placeholder) {
                return $this->replaceModelAttributes($html, $placeholder, $this->model);
            }, $html);
    }

    public function usingModel(Model $model)
    {
        $this->model = $model;

        return $this;
    }

    public static function makeWithModel(Model  $model)
    {
        return (new static)->usingModel($model);
    }
}
