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
use Illuminate\Support\Facades\Route;

Route::get('/storage/{path}', StorageProxyController::class)->where('path', '.*')->name('storage.proxy');

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/about', function () {
    return view('about');
})->name('about');

Route::get('items', [ItemController::class, 'index'])->name('items.index');
Route::get('items/create', [ItemController::class, 'create'])->name('items.create');
Route::get('items/by-category', [ItemController::class, 'byCategory'])->name('items.byCategory');
Route::get('items/component-autocomplete', [ItemController::class, 'componentAutocomplete'])
    ->name('items.component-autocomplete');
Route::get('items/check-component-name', [ItemController::class, 'checkComponentName'])
    ->name('items.check-component-name');
Route::get('items/{id}', [ItemController::class, 'show'])->name('items.show');

Route::post('items', [ItemController::class, 'store'])->name('items.store');
Route::post('extras', [ExtraController::class, 'store'])->name('extras.store');

Route::get('/tags', [TagController::class, 'index'])->name('tags.index');
Route::get('/tags/autocomplete', [TagController::class, 'autocomplete'])->name('tags.autocomplete');
Route::get('/tags/check-name', [TagController::class, 'checkName'])->name('tags.check-name');
Route::get('/collaborators/check-contact', [CollaboratorController::class, 'checkContact'])
    ->name('collaborators.check-contact');
Route::group(['middleware' => 'authenticate'], function () {
    Route::redirect('/admin', '/admin/items');

    Route::resource('admin/items', AdminItemController::class)->names('admin.items');
    Route::delete('admin/items/{item}/images/{image}', [AdminItemController::class, 'destroyImage'])
        ->name('admin.items.images.destroy');
    Route::resource('admin/item-categories', AdminItemCategoryController::class)->names('admin.item-categories');
    Route::resource('admin/tags', AdminTagController::class)->names('admin.tags');
    Route::resource('admin/tag-categories', AdminTagCategoryController::class)->names('admin.tag-categories');
    Route::resource('admin/collaborators', AdminCollaboratorController::class)->names('admin.collaborators');
    Route::resource('admin/extras', AdminExtraController::class)->names('admin.extras');

    Route::resource('admin/item-components', AdminItemComponentController::class)
        ->only(['index', 'create', 'store', 'show', 'update', 'destroy'])
        ->names('admin.item-components');
    Route::resource('admin/item-tags', AdminItemTagController::class)
        ->only(['index', 'create', 'store', 'show', 'update', 'destroy'])
        ->names('admin.item-tags');

    Route::post('admin/release-lock', ReleaseLockController::class)->name('admin.release-lock');

    Route::resource('admin/admins', AdminController::class)
        ->only(['index', 'create', 'store', 'show', 'destroy'])
        ->names('admin.admins');

    Route::delete(
        '/admin/admins/{id}/delete-lock',
        [AdminController::class, 'destroyLock']
    )->name('admin.admins.delete-lock');
});

Route::middleware('redirectIfAuthenticated')->group(function () {
    Route::get('/login', [AdminLoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminLoginController::class, 'login']);
});
Route::post('/logout', [AdminLoginController::class, 'logout'])->name('logout')->middleware('authenticate');
