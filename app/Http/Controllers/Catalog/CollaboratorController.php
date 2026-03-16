<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Services\Collaborator\CollaboratorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CollaboratorController extends Controller
{
    public function checkContact(Request $request, CollaboratorService $collaboratorService): JsonResponse
    {
        $contact = (string) ($request->input('contact') ?? '');

        $collaborator = $collaboratorService->findExternalByContact($contact);

        return response()->json($collaborator ?? false);
    }
}
