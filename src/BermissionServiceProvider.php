<?php

declare(strict_types=1);

namespace Yxvt\BeermissionLaravel;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;
use Yxvt\BeermissionLaravel\Contract\GrantSyncStrategy;
use Yxvt\BeermissionLaravel\GrantSyncStrategy\ReinsertSyncStrategy;

class BermissionServiceProvider extends ServiceProvider
{
    public function boot(Filesystem $fs): void {
        if (function_exists('config_path')) {
            $this->publishes([
                __DIR__ . '/../config/beermission.php' => config_path('beermission.php'),
            ]);

            $this->publishes([
                __DIR__ . '/../database/migrations/create_beermission_table.php' => $this->getMigrationFileName($fs),
            ], 'migrations');
        }

        $this->app->singleton(GrantSyncStrategy::class, config('beermission.grant_sync_strategy', ReinsertSyncStrategy::class));
    }

    public function register(): void {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/beermission.php',
            'beermission'
        );
    }

    protected function getMigrationFileName(Filesystem $filesystem): string {
        $timestamp = date('Y_m_d_His');
        $migrationsPath = $this->app->databasePath() . DIRECTORY_SEPARATOR . 'migrations' . DIRECTORY_SEPARATOR;

        return Collection::make($migrationsPath)
            ->flatMap(fn($p): array => $filesystem->glob($migrationsPath . '*_create_beermission_table.php'))
            ->push($this->app->databasePath() . "/migrations/{$timestamp}_create_beermission_table.php")
            ->first();
    }
}
