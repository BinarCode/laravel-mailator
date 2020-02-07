<?php

namespace Binarcode\LaravelMailator\Tests;

use Binarcode\LaravelMailator\LaravelMailatorServiceProvider;
use Orchestra\Testbench\TestCase;

class ExampleTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [LaravelMailatorServiceProvider::class];
    }

    /** @test */
    public function true_is_true()
    {
        $this->assertTrue(true);
    }
}
