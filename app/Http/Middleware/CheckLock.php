<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Lock;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckLock
{
    public function handle(Request $request, Closure $next): Response
    {
        $routeName = Route::currentRouteName();
        if (! $routeName) {
            abort(400, 'Rota inválida');
        }
        $modelName = $this->getModelClassFromRouteName($routeName);

        if (! $modelName) {
            abort(400, 'Rota ou modelo inválido');
        }

        $subjectId = $request->route($modelName[0]);

        $lock = Lock::where('lockable_type', $modelName[1])
            ->where('lockable_id', $subjectId)
            ->first();

        if ($lock && $lock->expiresAt() && $lock->user_id !== Auth::id()) {
            $message = 'Não é possível fazer alterações enquanto outro administrador estiver editando o mesmo.';

            return back()->withErrors([$message]);
        }

        return $next($request);
    }

    /**
     * @return array{0: string, 1: string}|null
     */
    private function getModelClassFromRouteName(string $routeName): ?array
    {
        $routeToModel = [
            'admin.items' => ['item', 'App\Models\Item'],
            'admin.tags' => ['tag', 'App\Models\Tag'],
            'admin.categories' => ['category', 'App\Models\Category'],
            'admin.proprietaries' => ['proprietary', 'App\Models\Proprietary'],
            'admin.extras' => ['extra', 'App\Models\Extra'],
            'admin.sections' => ['section', 'App\Models\Section'],
        ];

        foreach ($routeToModel as $routePrefix => $modelClass) {
            if (Str::startsWith($routeName, $routePrefix)) {
                return $modelClass;
            }
        }

        return null;
    }
}
