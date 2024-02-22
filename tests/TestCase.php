<?php

namespace Binarcode\LaravelMailator\Tests;

use Binarcode\LaravelMailator\LaravelMailatorServiceProvider;
use Illuminate\Contracts\View\Factory;
use Mockery as m;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom([
            '--database' => 'sqlite',
            '--path' => realpath(__DIR__.DIRECTORY_SEPARATOR.'database/migrations'),
        ]);

        \Illuminate\Database\Eloquent\Factories\Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Binarcode\\LaravelMailator\\Tests\\database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function tearDown(): void
    {
        m::close();
    }

    protected function getPackageProviders($app)
    {
        return [
            LaravelMailatorServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'sqlite');

        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        include_once __DIR__.'/../database/migrations/create_mailator_tables.php.stub';
        (new \CreateMailatorTables())->up();
    }

    protected function getMocks()
    {
        return ['smtp', m::mock(Factory::class), m::mock(Swift_Mailer::class)];
    }
}
