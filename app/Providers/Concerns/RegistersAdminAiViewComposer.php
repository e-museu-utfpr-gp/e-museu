<?php

declare(strict_types=1);

namespace App\Providers\Concerns;

use App\Support\Admin\Ai\AdminAi;
use Illuminate\Support\Facades\View;

final class RegistersAdminAiViewComposer
{
    public static function register(): void
    {
        View::composer('components.layouts.admin', function ($view): void {
            $enabled = AdminAi::translationUiEnabled();

            $view->with('adminAiEnabled', $enabled);
            $view->with('adminAiTranslateUrl', $enabled ? route('admin.ai.translate-content') : '');
        });
    }
}
