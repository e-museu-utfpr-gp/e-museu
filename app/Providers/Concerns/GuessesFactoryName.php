<?php

namespace App\Providers\Concerns;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class GuessesFactoryName
{
    /**
     * @param  class-string<Model>  $modelName
     * @return class-string<Factory<Model>>
     */
    public static function forModel(string $modelName): string
    {
        $factoryMap = config('factory_model_map', []);

        if (isset($factoryMap[$modelName])) {
            /** @var class-string<Factory<Model>> */
            return $factoryMap[$modelName];
        }

        $modelBasename = Str::after($modelName, 'App\Models\\');
        /** @var class-string<Factory<Model>> */
        $factoryClass = 'Database\Factories\\' . $modelBasename . 'Factory';

        return $factoryClass;
    }
}
