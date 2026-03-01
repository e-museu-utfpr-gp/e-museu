<?php

use App\Http\Controllers\Admin\Auth\AdminLoginController;
use App\Http\Controllers\Admin\Catalog\AdminComponentController;
use App\Http\Controllers\Admin\Catalog\AdminExtraController;
use App\Http\Controllers\Admin\Catalog\AdminItemController;
use App\Http\Controllers\Admin\Catalog\AdminItemTagController;
use App\Http\Controllers\Admin\Catalog\AdminSectionController;
use App\Http\Controllers\Catalog\ItemController;
use App\Http\Controllers\Catalog\QueryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\Identity\AdminUserController;
use App\Http\Controllers\Admin\Identity\ReleaseLockController;
use App\Http\Controllers\Admin\Proprietary\AdminProprietaryController;
use App\Http\Controllers\StorageProxyController;
use App\Http\Controllers\Admin\Taxonomy\AdminCategoryController;
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

Route::middleware('validate.proprietary')->group(function () {
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
    Route::resource('admin/sections', AdminSectionController::class)->names('admin.sections');
    Route::resource('admin/tags', AdminTagController::class)->names('admin.tags');
    Route::resource('admin/categories', AdminCategoryController::class)->names('admin.categories');
    Route::resource('admin/proprietaries', AdminProprietaryController::class)->names('admin.proprietaries');
    Route::resource('admin/extras', AdminExtraController::class)->names('admin.extras');

    Route::resource('admin/components', AdminComponentController::class)
        ->only(['index', 'create', 'store', 'show', 'update', 'destroy'])
        ->names('admin.components');
    Route::resource('admin/item-tags', AdminItemTagController::class)
        ->only(['index', 'create', 'store', 'show', 'update', 'destroy'])
        ->names('admin.item-tags');

    Route::post('admin/release-lock', ReleaseLockController::class)->name('admin.release-lock');

    Route::resource('admin/users', AdminUserController::class)
        ->only(['index', 'create', 'store', 'show', 'destroy'])
        ->names('admin.users');

    Route::delete(
        '/admin/users/{id}/delete-lock',
        [AdminUserController::class, 'destroyLock']
    )->name('admin.users.delete-lock');
});

Route::middleware('redirectIfAuthenticated')->group(function () {
    Route::get('/login', [AdminLoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminLoginController::class, 'login']);
});
Route::post('/logout', [AdminLoginController::class, 'logout'])->name('logout')->middleware('auth');
