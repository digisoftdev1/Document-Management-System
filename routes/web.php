<?php

use App\Http\Controllers\{
    BudgetTypeController,
    DashboardController,
    DocumentCategoryController,
    DocumentController,
    FiscalYearController,
    FolderController,
    GovernmentLevelController,
    PermissionController,
    ProfileController,
    RepositoryController,
    RoleController,
    SettingController,
    UserController
};
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => auth()->check() ? redirect('/admin') : redirect('/login'));

Route::get('/lang/{locale}', function ($locale) {
    if (!in_array($locale, ['en', 'ne'])) {
        abort(404);
    }
    session(['locale' => $locale]);
    return back();
})->name('language.switch');

Route::middleware('auth')->prefix('admin')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->middleware('can:view dashboard')->name('dashboard');

    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'edit')->name('profile.edit');
        Route::patch('/profile', 'update')->name('profile.update');
        Route::delete('/profile', 'destroy')->name('profile.destroy');
    });

    Route::resource('roles', RoleController::class)->except(['show']);

    Route::get('permissions', [PermissionController::class, 'index'])->name('permissions.index');
    Route::post('permissions', [PermissionController::class, 'store'])->name('permissions.store');
    Route::delete('permissions/{permission}', [PermissionController::class, 'destroy'])->name('permissions.destroy');

    Route::post('/repository/upload-folder', [RepositoryController::class, 'uploadFolder'])->middleware('module.permission:repository')->name('repository.uploadFolder');

    Route::prefix('repository')->name('repository.')->group(function () {
        Route::get('/dashboard', fn() => view('repository.index'))->middleware('can:view repository')->name('dashboard');
        Route::get('/records', [RepositoryController::class, 'records'])->middleware('module.permission:repository')->name('records');
        Route::delete('/bulk-delete', [RepositoryController::class, 'bulkDestroy'])->middleware('module.permission:repository')->name('bulkDestroy');
        Route::get('/{repository}/archived-view', [RepositoryController::class, 'archivedview'])->middleware('module.permission:repository')->name('archivedview');
        Route::get('/{repository}/record-view', [RepositoryController::class, 'recordsview'])->middleware('module.permission:repository')->name('recordview');
    });

    Route::resource('repository', RepositoryController::class)->middleware('module.permission:repository');
    Route::resource('documents', DocumentController::class)->middleware('module.permission:documents');
    Route::resource('users', UserController::class)->middleware('module.permission:users');
    Route::resource('fiscal-years', FiscalYearController::class)->middleware('module.permission:fiscal-years');
    Route::resource('government-levels', GovernmentLevelController::class)->middleware('module.permission:government-levels');
    Route::resource('budget-types', BudgetTypeController::class)->middleware('module.permission:budget-types');
    Route::resource('document-categories', DocumentCategoryController::class)->middleware('module.permission:document-categories');

    Route::controller(FolderController::class)->group(function () {
        Route::post('/folders', 'store')->middleware('can:create folders')->name('folders.store');
        Route::put('/folders/{folder}', 'update')->middleware('can:edit folders')->name('folders.update');
        Route::delete('/folders/{folder}', 'destroy')->middleware('can:delete folders')->name('folders.destroy');
    });

    Route::get('settings/website', [SettingController::class, 'edit'])->middleware('can:view settings')->name('settings.website');
    Route::put('settings/website', [SettingController::class, 'update'])->middleware('can:edit settings')->name('settings.website.update');
    Route::delete('/settings/website/logo', [SettingController::class, 'deleteLogo'])->middleware('can:edit settings')->name('settings.website.logo.delete');
    Route::delete('/settings/website/favicon', [SettingController::class, 'deleteFavicon'])->middleware('can:edit settings')->name('settings.website.favicon.delete');

    Route::get('/dispatch-management', fn() => view('maintenance', ['title' => 'Dispatch Management']))->name('dispatch.index');
    Route::get('/notes-approval', fn() => view('maintenance', ['title' => 'Notes & Approval']))->name('notes-approval.index');
    Route::get('/reports-dashboard', fn() => view('maintenance', ['title' => 'Reports & Dashboard']))->name('reports-dashboard.index');
});

require __DIR__ . '/auth.php';