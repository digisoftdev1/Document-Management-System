<?php

use App\Http\Controllers\{
    BudgetTypeController,
    DashboardController,
    DocumentCategoryController,
    DocumentController,
    FiscalYearController,
    FolderController,
    GovernmentLevelController,
    ProfileController,
    RepositoryController,
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
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/repository/upload-folder', [RepositoryController::class, 'uploadFolder'])
        ->name('repository.uploadFolder');
    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'edit')->name('profile.edit');
        Route::patch('/profile', 'update')->name('profile.update');
        Route::delete('/profile', 'destroy')->name('profile.destroy');
    });

    Route::prefix('repository')->name('repository.')->group(function () {
        Route::get('/dashboard', fn() => view('repository.index'))->name('dashboard');
        Route::get('/records', [RepositoryController::class, 'records'])->name('records');
        Route::delete('/bulk-delete', [RepositoryController::class, 'bulkDestroy'])->name('bulkDestroy');
        Route::get('/{repository}/archived-view', [RepositoryController::class, 'archivedview'])->name('archivedview');
        Route::get('/{repository}/record-view', [RepositoryController::class, 'recordsview'])->name('recordview');
    });

    Route::resource('repository', RepositoryController::class);
    Route::resource('documents', DocumentController::class);
    Route::resource('users', UserController::class);
    Route::resource('fiscal-years', FiscalYearController::class);
    Route::resource('government-levels', GovernmentLevelController::class);
    Route::resource('budget-types', BudgetTypeController::class);
    Route::resource(
        'document-categories',
        DocumentCategoryController::class
    );

    Route::controller(FolderController::class)->group(function () {
        Route::post('/folders', 'store')->name('folders.store');
        Route::put('/folders/{folder}', 'update')->name('folders.update');
        Route::delete('/folders/{folder}', 'destroy')->name('folders.destroy');
    });

    Route::get('settings/website', [SettingController::class, 'edit'])->name('settings.website');
    Route::put('settings/website', [SettingController::class, 'update'])->name('settings.website.update');
    Route::delete('/settings/website/logo', [SettingController::class, 'deleteLogo'])->name('settings.website.logo.delete');
    Route::delete('/settings/website/favicon', [SettingController::class, 'deleteFavicon'])->name('settings.website.favicon.delete');
});

require __DIR__ . '/auth.php';
