<?php

namespace App\Http\Controllers\Admin\Identity;

use App\Http\Controllers\Admin\AdminBaseController;
use App\Http\Requests\Admin\Identity\AdminReleaseLockRequest;
use App\Services\Identity\LockService;
use Illuminate\Http\Response;

class ReleaseLockController extends AdminBaseController
{
    /**
     * Release the current user's lock when they leave the edit page without saving.
     * Called via sendBeacon/fetch on beforeunload.
     */
    public function __invoke(AdminReleaseLockRequest $request, LockService $lockService): Response
    {
        /** @var array{type: string, id: int} $data */
        $data = $request->validated();

        [$subject, $status] = $lockService->resolveSubject($data);

        if ($status !== null || $subject === null) {
            return response()->noContent($status ?? Response::HTTP_BAD_REQUEST);
        }

        $lockService->releaseLockForCurrentAdmin($subject);

        return response()->noContent(Response::HTTP_NO_CONTENT);
    }
}
