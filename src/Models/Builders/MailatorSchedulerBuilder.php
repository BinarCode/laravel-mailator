<?php

namespace Binarcode\LaravelMailator\Models\Builders;

use Illuminate\Database\Eloquent\Builder;

class MailatorSchedulerBuilder extends Builder
{
    public function ready(): self
    {
        return $this->whereNull('completed_at');
    }
}
