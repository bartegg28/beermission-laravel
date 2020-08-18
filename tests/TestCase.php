<?php

declare(strict_types=1);

namespace Yxvt\BeermissionLaravel\Test;

use CreateBermissionGrantsTable;
use Illuminate\Database\Schema\Blueprint;
use Orchestra\Testbench\TestCase as Orchestra;
use Yxvt\BeermissionLaravel\BermissionServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function setUp(): void {
        parent::setUp();

        $this->setUpDatabase($this->app);
    }

    protected function setUpDatabase($app): void {
        $app['config']->set('permission.column_names.model_morph_key', 'model_test_id');

        $app['db']->connection()->getSchemaBuilder()->create('users', static function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('bearer_id', 32);
        });

        include_once __DIR__ . '/../database/migrations/create_beermission_grants_table.php';

        (new CreateBermissionGrantsTable())->up();
    }

    protected function getPackageProviders($app) {
        return [
            BermissionServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app) {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }
}
