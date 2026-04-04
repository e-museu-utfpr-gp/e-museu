<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Services\Collaborator\CollaboratorService;
use Illuminate\Http\{JsonResponse, Request};

class CollaboratorController extends Controller
{
    public function checkContact(Request $request, CollaboratorService $collaboratorService): JsonResponse
    {
        $request->validate([
            'contact' => 'nullable|string|max:200',
        ]);

        $contact = trim((string) $request->input('contact', ''));
        if ($contact === '') {
            return response()->json([
                'exists' => false,
                'full_name' => '',
            ]);
        }

        $collaborator = $collaboratorService->findExternalByContact($contact);

        return response()->json([
            'exists' => $collaborator !== null,
            'full_name' => $collaborator !== null ? (string) $collaborator->full_name : '',
        ]);
    }
}
