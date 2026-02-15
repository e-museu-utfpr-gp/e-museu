<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $factoryMap = config('factory_model_map', []);

        /** @phpstan-ignore argument.type (callback return type is class-string in practice) */
        Factory::guessFactoryNamesUsing(function (string $modelName) use ($factoryMap): string {
            if (isset($factoryMap[$modelName])) {
                return $factoryMap[$modelName];
            }

            $modelBasename = Str::after($modelName, 'App\Models\\');

            return 'Database\Factories\\' . $modelBasename . 'Factory';
        });

        $modelMap = array_flip($factoryMap);

        /** @phpstan-ignore argument.type (callback return type is class-string in practice) */
        Factory::guessModelNamesUsing(function (Factory $factory) use ($modelMap): string {
            $factoryClass = $factory::class;

            if (isset($modelMap[$factoryClass])) {
                return $modelMap[$factoryClass];
            }

            $factoryBasename = Str::replaceLast('Factory', '', class_basename($factory));

            return 'App\Models\\' . $factoryBasename;
        });
    }
}
