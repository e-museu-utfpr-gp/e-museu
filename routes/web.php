<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\Auth\AdminLoginController;
use App\Http\Controllers\Admin\Catalog\AdminItemComponentController;
use App\Http\Controllers\Admin\Catalog\AdminExtraController;
use App\Http\Controllers\Admin\Catalog\Item\AdminItemController;
use App\Http\Controllers\Admin\Catalog\Item\AdminItemImageController;
use App\Http\Controllers\Admin\Catalog\Item\AdminItemQrCodeController;
use App\Http\Controllers\Admin\Catalog\AdminItemTagController;
use App\Http\Controllers\Admin\Catalog\AdminItemCategoryController;
use App\Http\Controllers\Catalog\CollaboratorController;
use App\Http\Controllers\Catalog\ExtraController;
use App\Http\Controllers\Catalog\IdentificationCodeRedirectController;
use App\Http\Controllers\Catalog\ItemController;
use App\Http\Controllers\Catalog\TagController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\Identity\AdminController;
use App\Http\Controllers\Admin\Identity\ReleaseLockController;
use App\Http\Controllers\Admin\Collaborator\AdminCollaboratorController;
use App\Http\Controllers\StorageProxyController;
use App\Http\Controllers\Admin\Taxonomy\AdminTagCategoryController;
use App\Http\Controllers\Admin\Taxonomy\AdminTagController;
use App\Http\Controllers\Admin\Ai\AdminContentTranslationController;
use App\Models\Language;
use Illuminate\Support\Facades\Route;

Route::get('/storage/{path}', StorageProxyController::class)
    ->where('path', '.*')
    ->middleware('throttle:web-storage')
    ->name('storage.proxy');

/**
 * JSON probe while typing — dedicated limiter (not shared with catalog form POST bucket).
 * Same JSON contract as `admin.catalog.collaborators.check-contact` (see `CollaboratorController::checkContact`).
 */
Route::middleware('throttle:collaborator-check-contact')->group(function () {
    Route::post('catalog/collaborators/check-contact', [CollaboratorController::class, 'checkContact'])
        ->name('catalog.collaborators.check-contact');
});

/** Registered before `items/{id}` so `{id}` does not capture these paths. */
Route::middleware('throttle:web-catalog-light')->prefix('catalog')->name('catalog.')->group(function () {
    Route::get('items/component-autocomplete', [ItemController::class, 'componentAutocomplete'])
        ->name('items.component-autocomplete');
    Route::get('items/check-component-name', [ItemController::class, 'checkComponentName'])
        ->name('items.check-component-name');
    Route::get('tags/autocomplete', [TagController::class, 'autocomplete'])->name('tags.autocomplete');
    Route::get('tags/check-name', [TagController::class, 'checkName'])->name('tags.check-name');
});

Route::middleware('throttle:web-public')->group(function () {
    Route::post('/locale', function (\Illuminate\Http\Request $request) {
        $locale = (string) $request->input('locale');
        if (Language::isValidSessionUiLocale($locale)) {
            $request->session()->put('locale', $locale);
        }

        $previous = url()->previous();
        $appUrl = rtrim((string) config('app.url'), '/');

        if ($appUrl !== '' && str_starts_with($previous, $appUrl)) {
            return redirect()->to($previous);
        }

        return redirect()->route('home');
    })->name('locale.update');

    Route::redirect('/', '/home');
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/about', function () {
        return view('pages.about.index');
    })->name('about');

    Route::get('codes/{code}', IdentificationCodeRedirectController::class)
        ->where('code', '[^/]+')
        ->name('codes.show');

    Route::prefix('catalog')->name('catalog.')->group(function () {
        Route::redirect('/', '/catalog/items')->name('root');
        Route::get('items', [ItemController::class, 'index'])->name('items.index');
        Route::get('items/create', [ItemController::class, 'create'])->name('items.create');
        Route::get('items/by-category', [ItemController::class, 'byCategory'])->name('items.byCategory');
        Route::get('items/{id}', [ItemController::class, 'show'])->name('items.show')->whereNumber('id');
        Route::post('items', [ItemController::class, 'store'])
            ->middleware('throttle:catalog-item-contribution-store')
            ->name('items.store');
        Route::post('extras', [ExtraController::class, 'store'])->name('extras.store');
        Route::get('tags', [TagController::class, 'index'])->name('tags.index');
        Route::post(
            'collaborators/request-verification-code',
            [CollaboratorController::class, 'requestVerificationCode'],
        )
            ->middleware(['throttle:collaborator-verification-email', 'antibot:verification-request'])
            ->name('collaborators.request-verification-code');
        Route::post(
            'collaborators/confirm-verification-code',
            [CollaboratorController::class, 'confirmVerificationCode'],
        )
            ->middleware('throttle:collaborator-verification-confirm')
            ->name('collaborators.confirm-verification-code');
        Route::post(
            'collaborators/clear-contribution-session',
            [CollaboratorController::class, 'clearContributionSession'],
        )
            ->middleware('throttle:collaborator-clear-session')
            ->name('collaborators.clear-contribution-session');
    });
});

Route::middleware(['authenticate', 'throttle:web-admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::redirect('/', '/admin/catalog/items');

    Route::post('ai/translate-content', [AdminContentTranslationController::class, 'translate'])
        ->middleware('throttle:admin-ai-translate')
        ->name('ai.translate-content');

    Route::prefix('catalog')->name('catalog.')->group(function () {
        Route::redirect('/', '/admin/catalog/items');
        Route::delete('items/{item}/images/{image}', [AdminItemImageController::class, 'destroy'])
            ->name('items.images.destroy');
        Route::post('items/{item}/qrcode/regenerate', [AdminItemQrCodeController::class, 'regenerate'])
            ->name('items.qrcode.regenerate');
        Route::delete('items/{item}/qrcode', [AdminItemQrCodeController::class, 'deleteQrCode'])
            ->name('items.qrcode.delete');
        Route::get('items/by-item-category', [AdminItemController::class, 'byItemCategory'])
            ->name('items.by-item-category');
        Route::resource('items', AdminItemController::class);
        Route::resource('item-categories', AdminItemCategoryController::class);
        Route::resource('extras', AdminExtraController::class);
        Route::resource('item-components', AdminItemComponentController::class)
            ->only(['index', 'create', 'store', 'show', 'update', 'destroy']);
        Route::get('tags/by-category', [AdminItemTagController::class, 'tagsByCategory'])
            ->name('tags.by-category');
        Route::resource('item-tags', AdminItemTagController::class)
            ->only(['index', 'create', 'store', 'show', 'update', 'destroy']);
        Route::post('collaborators/check-contact', [CollaboratorController::class, 'checkContact'])
            ->name('collaborators.check-contact');
    });

    Route::prefix('taxonomy')->name('taxonomy.')->group(function () {
        Route::redirect('/', '/admin/taxonomy/tags');
        Route::resource('tags', AdminTagController::class);
        Route::resource('tag-categories', AdminTagCategoryController::class);
    });

    Route::resource('collaborators', AdminCollaboratorController::class);

    Route::prefix('identity')->name('identity.')->group(function () {
        Route::redirect('/', '/admin/identity/admins');
        Route::resource('admins', AdminController::class)
            ->only(['index', 'create', 'store', 'show', 'destroy']);
        Route::delete('admins/{id}/delete-lock', [AdminController::class, 'destroyLock'])
            ->name('admins.delete-lock');
        Route::post('release-lock', ReleaseLockController::class)->name('release-lock');
    });
});

Route::middleware('redirectIfAuthenticated')->prefix('admin/auth')->group(function () {
    Route::get('login', [AdminLoginController::class, 'showLoginForm'])
        ->middleware('throttle:web-public')
        ->name('login');
    Route::post('login', [AdminLoginController::class, 'login'])
        ->middleware(['throttle:admin-login', 'antibot']);
});

Route::post('admin/auth/logout', [AdminLoginController::class, 'logout'])
    ->name('logout')
    ->middleware(['authenticate', 'throttle:web-admin']);
