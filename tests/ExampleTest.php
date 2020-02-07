<?php

namespace Binarcode\LaravelMailator\Tests;

use Orchestra\Testbench\TestCase;
use Binarcode\LaravelMailator\LaravelMailatorServiceProvider;

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
