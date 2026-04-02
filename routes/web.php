<?php

use App\Http\Controllers\Admin\Auth\AdminLoginController;
use App\Http\Controllers\Admin\Catalog\AdminItemComponentController;
use App\Http\Controllers\Admin\Catalog\AdminExtraController;
use App\Http\Controllers\Admin\Catalog\AdminItemController;
use App\Http\Controllers\Admin\Catalog\AdminItemTagController;
use App\Http\Controllers\Admin\Catalog\AdminItemCategoryController;
use App\Http\Controllers\Catalog\CollaboratorController;
use App\Http\Controllers\Catalog\ExtraController;
use App\Http\Controllers\Catalog\ItemController;
use App\Http\Controllers\Catalog\TagController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\Identity\AdminController;
use App\Http\Controllers\Admin\Identity\ReleaseLockController;
use App\Http\Controllers\Admin\Collaborator\AdminCollaboratorController;
use App\Http\Controllers\StorageProxyController;
use App\Http\Controllers\Admin\Taxonomy\AdminTagCategoryController;
use App\Http\Controllers\Admin\Taxonomy\AdminTagController;
use App\Models\Language;
use Illuminate\Support\Facades\Route;

Route::get('/storage/{path}', StorageProxyController::class)->where('path', '.*')->name('storage.proxy');

Route::post('/locale', function (\Illuminate\Http\Request $request) {
    $locale = (string) $request->input('locale');
    if (Language::isValidSessionUiLocale($locale)) {
        $request->session()->put('locale', $locale);
    }

    return back();
})->name('locale.update');

Route::redirect('/', '/home');
Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::get('/about', function () {
    return view('pages.about.index');
})->name('about');

Route::prefix('catalog')->name('catalog.')->group(function () {
    Route::get('items', [ItemController::class, 'index'])->name('items.index');
    Route::get('items/create', [ItemController::class, 'create'])->name('items.create');
    Route::get('items/by-category', [ItemController::class, 'byCategory'])->name('items.byCategory');
    Route::get('items/component-autocomplete', [ItemController::class, 'componentAutocomplete'])
        ->name('items.component-autocomplete');
    Route::get('items/check-component-name', [ItemController::class, 'checkComponentName'])
        ->name('items.check-component-name');
    Route::get('items/{id}', [ItemController::class, 'show'])->name('items.show')->whereNumber('id');
    Route::post('items', [ItemController::class, 'store'])->name('items.store');
    Route::post('extras', [ExtraController::class, 'store'])->name('extras.store');
    Route::get('tags', [TagController::class, 'index'])->name('tags.index');
    Route::get('tags/autocomplete', [TagController::class, 'autocomplete'])->name('tags.autocomplete');
    Route::get('tags/check-name', [TagController::class, 'checkName'])->name('tags.check-name');
    Route::match(['get', 'post'], 'collaborators/check-contact', [CollaboratorController::class, 'checkContact'])
        ->name('collaborators.check-contact');
});

Route::middleware('authenticate')->prefix('admin')->name('admin.')->group(function () {
    Route::redirect('/', '/admin/catalog/items');

    Route::prefix('catalog')->name('catalog.')->group(function () {
        Route::delete('items/{item}/images/{image}', [AdminItemController::class, 'destroyImage'])
            ->name('items.images.destroy');
        Route::get('items/by-item-category', [AdminItemController::class, 'byItemCategory'])
            ->name('items.by-item-category');
        Route::resource('items', AdminItemController::class);
        Route::resource('item-categories', AdminItemCategoryController::class);
        Route::resource('extras', AdminExtraController::class);
        Route::resource('item-components', AdminItemComponentController::class)
            ->only(['index', 'create', 'store', 'show', 'update', 'destroy']);
        Route::resource('item-tags', AdminItemTagController::class)
            ->only(['index', 'create', 'store', 'show', 'update', 'destroy']);
    });

    Route::prefix('taxonomy')->name('taxonomy.')->group(function () {
        Route::resource('tags', AdminTagController::class);
        Route::resource('tag-categories', AdminTagCategoryController::class);
    });

    Route::resource('collaborators', AdminCollaboratorController::class);

    Route::prefix('identity')->name('identity.')->group(function () {
        Route::resource('admins', AdminController::class)
            ->only(['index', 'create', 'store', 'show', 'destroy']);
        Route::delete('admins/{id}/delete-lock', [AdminController::class, 'destroyLock'])
            ->name('admins.delete-lock');
        Route::post('release-lock', ReleaseLockController::class)->name('release-lock');
    });
});

Route::middleware('redirectIfAuthenticated')->prefix('admin/auth')->group(function () {
    Route::get('login', [AdminLoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AdminLoginController::class, 'login']);
});

Route::post('admin/auth/logout', [AdminLoginController::class, 'logout'])->name('logout')->middleware('authenticate');
