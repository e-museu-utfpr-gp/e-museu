<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\Catalog\ItemService;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(ItemService $itemService): View
    {
        $items = $itemService->getRandomValidatedItemsForHome();

        $deployDebug = [
            'config_deploy_secret' => $this->fingerprint(config('deploy.secret')),
            'env_deploy_hook_secret' => $this->fingerprint(env('DEPLOY_HOOK_SECRET')),
            'env_deploy_hook_bearer' => $this->fingerprint(env('DEPLOY_HOOK_BEARER')),
            'config_coolify_deploy_url' => $this->fingerprint(config('deploy.coolify.deploy_url')),
            'env_coolify_deploy_url' => $this->fingerprint(env('COOLIFY_DEPLOY_URL')),
            'config_coolify_deploy_token' => $this->fingerprint(config('deploy.coolify.token')),
            'env_coolify_deploy_token' => $this->fingerprint(env('COOLIFY_DEPLOY_TOKEN')),
        ];

        return view('pages.home.index', compact('items', 'deployDebug'));
    }

    /**
     * @return array{present: bool, len: int, sha12: string, preview: string}
     */
    private function fingerprint(mixed $value): array
    {
        if (! is_string($value)) {
            return [
                'present' => false,
                'len' => 0,
                'sha12' => '-',
                'preview' => '-',
            ];
        }

        $trimmed = trim($value);

        if ($trimmed == '') {
            return [
                'present' => false,
                'len' => 0,
                'sha12' => '-',
                'preview' => '(empty)',
            ];
        }

        return [
            'present' => true,
            'len' => strlen($trimmed),
            'sha12' => substr(hash('sha256', $trimmed), 0, 12),
            'preview' => substr($trimmed, 0, 8) . '...',
        ];
    }
}
