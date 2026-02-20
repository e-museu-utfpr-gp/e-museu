<?php

namespace App\Http\Controllers\Identity;

use App\Http\Controllers\Controller;
use App\Models\Identity\Lock;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class ReleaseLockController extends Controller
{
    /**
     * Release the current user's lock when they leave the edit page without saving.
     * Called via sendBeacon/fetch on beforeunload.
     */
    public function __invoke(Request $request): Response
    {
        $request->validate([
            'type' => 'required|string|in:items,sections,tags,categories,proprietaries,extras',
            'id' => 'required|integer|min:1',
        ]);

        $routePrefix = 'admin.' . $request->input('type');
        $config = config('lockable_routes', []);
        if (! isset($config[$routePrefix])) {
            return response()->noContent(Response::HTTP_BAD_REQUEST);
        }

        [, $modelClass] = $config[$routePrefix];
        $id = (int) $request->input('id');
        $subject = $modelClass::find($id);
        if (! $subject) {
            return response()->noContent(Response::HTTP_NOT_FOUND);
        }

        $lock = Lock::findByModel($subject);
        if ($lock && (string) $lock->user_id === (string) Auth::id()) {
            $lock->delete();
        }

        return response()->noContent(Response::HTTP_NO_CONTENT);
    }
}
