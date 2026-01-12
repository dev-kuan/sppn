<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\InmateController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AssessmentController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */
    Route::middleware('permission:view-dashboard')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/chart-data', [DashboardController::class, 'getChartData'])
            ->name('dashboard.chart-data');
    });

    /*
    |--------------------------------------------------------------------------
    | Profile (SEMUA USER LOGIN BOLEH)
    |--------------------------------------------------------------------------
    */
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    /*
    |--------------------------------------------------------------------------
    | Narapidana
    |--------------------------------------------------------------------------
    */
    Route::middleware('permission:create-narapidana')->group(function () {
        Route::get('inmates/create', [InmateController::class, 'create'])->name('inmates.create');
        Route::post('inmates', [InmateController::class, 'store'])->name('inmates.store');
    });
    Route::middleware('permission:view-narapidana')->group(function () {
        Route::get('inmates', [InmateController::class, 'index'])->name('inmates.index');
        Route::get('inmates/{inmate}', [InmateController::class, 'show'])->name('inmates.show');
        Route::get('inmates-trashed', [InmateController::class, 'trashed'])->name('inmates.trashed');
    });


    Route::middleware('permission:edit-narapidana')->group(function () {
        Route::get('inmates/{inmate}/edit', [InmateController::class, 'edit'])->name('inmates.edit');
        Route::put('inmates/{inmate}', [InmateController::class, 'update'])->name('inmates.update');
    });

    Route::middleware('permission:delete-narapidana')->group(function () {
        Route::delete('inmates/{inmate}', [InmateController::class, 'destroy'])->name('inmates.destroy');
        Route::post('inmates/{id}/restore', [InmateController::class, 'restore'])->name('inmates.restore');
    });

    /*
    |--------------------------------------------------------------------------
    | Penilaian
    |--------------------------------------------------------------------------
    */
    Route::middleware('permission:create-penilaian')->group(function () {
        Route::get('assessments/create', [AssessmentController::class, 'create'])->name('assessments.create');
        Route::post('assessments', [AssessmentController::class, 'store'])->name('assessments.store');
        Route::get('/{assessment}/export-template', [AssessmentController::class, 'exportTemplate'])->name('assessments.export-template');
    Route::post('/{assessment}/import', [AssessmentController::class, 'import'])->name('assessments.import');
    });
    Route::middleware('permission:view-penilaian')->group(function () {
        Route::get('assessments', [AssessmentController::class, 'index'])->name('assessments.index');
        Route::get('assessments/{assessment}', [AssessmentController::class, 'show'])->name('assessments.show');
    });


    Route::middleware('permission:edit-penilaian')->group(function () {
        Route::get('assessments/{assessment}/edit', [AssessmentController::class, 'edit'])->name('assessments.edit');
        Route::put('assessments/{assessment}', [AssessmentController::class, 'update'])->name('assessments.update');
        Route::post('assessments/{assessment}/observation',
            [AssessmentController::class, 'updateObservation']
        )->name('assessments.update-observation');
        Route::get('/{assessment}/export-template', [AssessmentController::class, 'exportTemplate'])->name('assessments.export-template');
    Route::post('/{assessment}/import', [AssessmentController::class, 'import'])->name('assessments.import');
    });

    Route::middleware('permission:submit-penilaian')->post(
        'assessments/{assessment}/submit',
        [AssessmentController::class, 'submit']
    )->name('assessments.submit');

    Route::middleware('permission:approve-penilaian')->group(function () {
        Route::post('assessments/{assessment}/approve', [AssessmentController::class, 'approve'])
            ->name('assessments.approve');
        Route::post('assessments/{assessment}/reject', [AssessmentController::class, 'reject'])
            ->name('assessments.reject');
    });

    /*
    |--------------------------------------------------------------------------
    | Laporan
    |--------------------------------------------------------------------------
    */
    Route::middleware('permission:view-laporan')->group(function () {
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    });

    Route::middleware('permission:export-laporan')->group(function () {
        Route::get('/reports/export-assessments', [ReportController::class, 'exportAssessments'])
            ->name('reports.export-assessments');
        Route::get('/reports/export-inmates', [ReportController::class, 'exportInmates'])
            ->name('reports.export-inmates');

        Route::post('/reports/assessment-pdf', [ReportController::class, 'generateAssessmentReport'])
            ->name('reports.assessment-pdf');
        Route::post('/reports/monthly-pdf', [ReportController::class, 'generateMonthlyReport'])
            ->name('reports.monthly-pdf');
        Route::post('/reports/inmate-progress-pdf', [ReportController::class, 'generateInmateProgressReport'])
            ->name('reports.inmate-progress-pdf');
    });

    /*
    |--------------------------------------------------------------------------
    | Manajemen User (ADMIN ONLY)
    |--------------------------------------------------------------------------
    */
    Route::middleware('permission:view-users')->get('users', [UserController::class, 'index'])->name('users.index');
    Route::middleware('permission:create-users')->get('users/create', [UserController::class, 'create'])->name('users.create');
    Route::middleware('permission:create-users')->post('users', [UserController::class, 'store'])->name('users.store');
    Route::middleware('permission:edit-users')->get('users/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::middleware('permission:view-users')->group(function () {
        Route::resource('users', UserController::class)->except(['create', 'store', 'edit', 'update', 'destroy']);
    });

    // Route::middleware('permission:edit-users')->post('users/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::middleware('permission:edit-users')->put('users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::middleware('permission:delete-users')->delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    /*
    |--------------------------------------------------------------------------
    | Settings
    |--------------------------------------------------------------------------
    */
    Route::middleware('permission:view-settings')->prefix('settings')->name('settings.')->group(function () {

        Route::get('/', [SettingController::class, 'index'])->name('index');

        Route::middleware('permission:manage-observation-items')->group(function () {
            Route::get('/variabels', [SettingController::class, 'variabels'])->name('variabels');
            Route::post('/variabels', [SettingController::class, 'storeVariabel'])->name('variabels.store');
            Route::put('/variabels/{variabel}', [SettingController::class, 'updateVariabel'])->name('variabels.update');

            Route::get('/aspects', [SettingController::class, 'aspects'])->name('aspects');
            Route::post('/aspects', [SettingController::class, 'storeAspect'])->name('aspects.store');
            Route::put('/aspects/{aspect}', [SettingController::class, 'updateAspect'])->name('aspects.update');

            Route::get('/observation-items', [SettingController::class, 'observationItems'])->name('observation-items');
            Route::post('/observation-items', [SettingController::class, 'storeObservationItem'])->name('observation-items.store');
            Route::put('/observation-items/{observationItem}', [SettingController::class, 'updateObservationItem'])->name('observation-items.update');
            Route::post('/observation-items/{observationItem}/toggle', [SettingController::class, 'toggleObservationItem'])
                ->name('observation-items.toggle');
        });

        Route::middleware('permission:backup-restore')->group(function () {
            Route::get('/backup', [SettingController::class, 'backup'])->name('backup');
            Route::get('/logs', [SettingController::class, 'logs'])->name('logs');
        });
    });
});

require __DIR__.'/auth.php';

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

// Route::middleware('auth')->group(function () {
//     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
// });

// require __DIR__.'/auth.php';
