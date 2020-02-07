<?php

namespace Binarcode\LaravelMailator;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Binarcode\LaravelMailator\Skeleton\SkeletonClass
 */
class LaravelMailatorFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'laravel-mailator';
    }
}
