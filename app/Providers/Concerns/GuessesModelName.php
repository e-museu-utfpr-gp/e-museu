<?php

namespace App\Providers\Concerns;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class GuessesModelName
{
    /**
     * @param  Factory<Model>  $factory
     * @return class-string<Model>
     */
    public static function forFactory(Factory $factory): string
    {
        $factoryMap = config('factory_model_map', []);
        $modelMap = array_flip($factoryMap);
        $factoryClass = $factory::class;

        if (isset($modelMap[$factoryClass])) {
            /** @var class-string<Model> */
            return $modelMap[$factoryClass];
        }

        $factoryBasename = Str::replaceLast('Factory', '', class_basename($factory));
        /** @var class-string<Model> */
        $modelClass = 'App\Models\\' . $factoryBasename;

        return $modelClass;
    }
}
