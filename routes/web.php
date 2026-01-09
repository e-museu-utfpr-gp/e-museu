<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\QueryController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminItemController;
use App\Http\Controllers\AdminSectionController;
use App\Http\Controllers\AdminTagController;
use App\Http\Controllers\AdminCategoryController;
use App\Http\Controllers\AdminProprietaryController;
use App\Http\Controllers\AdminComponentController;
use App\Http\Controllers\AdminItemTagController;
use App\Http\Controllers\AdminExtraController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;


Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/about', function () {
    return view('about');
})->name('about');

Route::get('/items', [ItemController::class, 'index'])->name('items.index');
Route::get('/items/create', [ItemController::class, 'create'])->name('items.create');
Route::get('/items/{id}', [ItemController::class, 'show'])->name('items.show')->middleware('validate.item');

Route::group(['middleware' => 'validate.proprietary'], function () {
    Route::post('/items/store', [ItemController::class, 'store'])->name('items.store');
    Route::post('/items/store-extra', [ItemController::class, 'storeSingleExtra'])->name('items.store-extra');
});

Route::get('/component-name-auto-complete', [QueryController::class, 'componentNameAutoComplete'])->name('component-name-auto-complete');
Route::get('/check-component-name', [QueryController::class, 'checkComponentName'])->name('check-component-name');
Route::get('/tag-name-auto-complete', [QueryController::class, 'tagNameAutoComplete'])->name('tag-name-auto-complete');
Route::get('/check-tag-name', [QueryController::class, 'checkTagName'])->name('check-tag-name');
Route::get('/check-contact', [QueryController::class, 'checkContact'])->name('check-contact');
Route::get('/get-tags', [QueryController::class, 'getTags'])->name('get-tags');
Route::get('/get-items', [QueryController::class, 'getItems'])->name('get-items');

Route::group(['middleware' => 'auth'], function () {
    Route::redirect('/admin', '/admin/items');

    Route::resource('admin/items', AdminItemController::class)->names([
        'index' => 'admin.items.index',
        'create' => 'admin.items.create',
        'store' => 'admin.items.store',
        'show' => 'admin.items.show',
        'edit' => 'admin.items.edit',
        'update' => 'admin.items.update',
        'destroy' => 'admin.items.destroy',
    ]);

    Route::resource('admin/sections', AdminSectionController::class)->names([
        'index' => 'admin.sections.index',
        'create' => 'admin.sections.create',
        'store' => 'admin.sections.store',
        'show' => 'admin.sections.show',
        'edit' => 'admin.sections.edit',
        'update' => 'admin.sections.update',
        'destroy' => 'admin.sections.destroy',
    ]);

    Route::resource('admin/tags', AdminTagController::class)->names([
        'index' => 'admin.tags.index',
        'create' => 'admin.tags.create',
        'store' => 'admin.tags.store',
        'show' => 'admin.tags.show',
        'edit' => 'admin.tags.edit',
        'update' => 'admin.tags.update',
        'destroy' => 'admin.tags.destroy',
    ]);

    Route::resource('admin/categories', AdminCategoryController::class)->names([
        'index' => 'admin.categories.index',
        'create' => 'admin.categories.create',
        'store' => 'admin.categories.store',
        'show' => 'admin.categories.show',
        'edit' => 'admin.categories.edit',
        'update' => 'admin.categories.update',
        'destroy' => 'admin.categories.destroy',
    ]);

    Route::resource('admin/proprietaries', AdminProprietaryController::class)->names([
        'index' => 'admin.proprietaries.index',
        'create' => 'admin.proprietaries.create',
        'store' => 'admin.proprietaries.store',
        'show' => 'admin.proprietaries.show',
        'edit' => 'admin.proprietaries.edit',
        'update' => 'admin.proprietaries.update',
        'destroy' => 'admin.proprietaries.destroy',
    ]);

    Route::resource('admin/components', AdminComponentController::class)->names([
        'index' => 'admin.components.index',
        'create' => 'admin.components.create',
        'store' => 'admin.components.store',
        'show' => 'admin.components.show',
        'update' => 'admin.components.update',
        'destroy' => 'admin.components.destroy',
    ]);

    Route::resource('admin/item-tags', AdminItemTagController::class)->names([
        'index' => 'admin.item-tags.index',
        'create' => 'admin.item-tags.create',
        'store' => 'admin.item-tags.store',
        'show' => 'admin.item-tags.show',
        'update' => 'admin.item-tags.update',
        'destroy' => 'admin.item-tags.destroy',
    ]);

    Route::resource('admin/extras', AdminExtraController::class)->names([
        'index' => 'admin.extras.index',
        'create' => 'admin.extras.create',
        'store' => 'admin.extras.store',
        'show' => 'admin.extras.show',
        'edit' => 'admin.extras.edit',
        'update' => 'admin.extras.update',
        'destroy' => 'admin.extras.destroy',
    ]);

    Route::resource('admin/users', AdminUserController::class)->names([
        'index' => 'admin.users.index',
        'create' => 'admin.users.create',
        'store' => 'admin.users.store',
        'show' => 'admin.users.show',
        'destroy' => 'admin.users.destroy',
    ]);

    Route::delete('/admin/users/{id}/delete-lock', [AdminUserController::class, 'destroyLock'])->name('admin.users.delete-lock');
});

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');


