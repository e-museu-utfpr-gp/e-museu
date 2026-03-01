<?php

use App\Http\Controllers\Admin\Auth\AdminLoginController;
use App\Http\Controllers\Admin\Catalog\AdminItemComponentController;
use App\Http\Controllers\Admin\Catalog\AdminExtraController;
use App\Http\Controllers\Admin\Catalog\AdminItemController;
use App\Http\Controllers\Admin\Catalog\AdminItemTagController;
use App\Http\Controllers\Admin\Catalog\AdminItemCategoryController;
use App\Http\Controllers\Catalog\ItemController;
use App\Http\Controllers\Catalog\QueryController;
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
Route::get('items/by-section', [ItemController::class, 'bySection'])->name('items.bySection');
Route::get('items/{id}', [ItemController::class, 'show'])->name('items.show')->middleware('validate.item');

Route::middleware('validate.collaborator')->group(function () {
    Route::post('items', [ItemController::class, 'store'])->name('items.store');
    Route::post('items/extras', [ItemController::class, 'storeSingleExtra'])->name('items.store-extra');
});

Route::get(
    '/component-name-auto-complete',
    [QueryController::class, 'componentNameAutoComplete']
)->name('component-name-auto-complete');
Route::get('/check-component-name', [QueryController::class, 'checkComponentName'])->name('check-component-name');
Route::get('/tag-name-auto-complete', [QueryController::class, 'tagNameAutoComplete'])->name('tag-name-auto-complete');
Route::get('/check-tag-name', [QueryController::class, 'checkTagName'])->name('check-tag-name');
Route::get('/check-contact', [QueryController::class, 'checkContact'])->name('check-contact');
Route::get('/get-tags', [QueryController::class, 'getTags'])->name('get-tags');
Route::group(['middleware' => 'auth'], function () {
    Route::redirect('/admin', '/admin/items');

    Route::resource('admin/items', AdminItemController::class)->names('admin.items');
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
Route::post('/logout', [AdminLoginController::class, 'logout'])->name('logout')->middleware('auth');
