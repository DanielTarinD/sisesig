<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FollowUpController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ComorbidityController;
use App\Http\Controllers\ColoniaController;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');

Route::post('/login', [AuthController::class, 'login'])
    ->name('login.store');

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')->group(function () {

    Route::get('/', fn() => redirect()->route('dashboard'));

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');
    
    
    /*
    |--------------------------------------------------------------------------
    | Pacientes - consultas
    |--------------------------------------------------------------------------
    */

    // Búsqueda AJAX
    Route::get('/patients/search', [PatientController::class, 'search'])
        ->name('patients.search');

    // Listado
    Route::get('/patients', [PatientController::class, 'index'])
        ->name('patients.index');
    
    Route::get('/patients/check-duplicate', [PatientController::class, 'checkDuplicate'])
    ->name('patients.checkDuplicate');


    /*
    |--------------------------------------------------------------------------
    | Pacientes - Capturista
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:capturista')->group(function () {

        // IMPORTANTE:
        // /patients/create debe estar antes de /patients/{patient}
        Route::get('/patients/create', [PatientController::class, 'create'])
            ->name('patients.create');

        Route::post('/patients', [PatientController::class, 'store'])
            ->name('patients.store');

        Route::get('/patients/{patient}/edit', [PatientController::class, 'edit'])
            ->name('patients.edit');

        Route::put('/patients/{patient}', [PatientController::class, 'update'])
            ->name('patients.update');

        Route::post('/patients/{patient}/follow-ups', [FollowUpController::class, 'store'])
            ->name('followups.store');

        Route::delete('/follow-ups/{followUp}', [FollowUpController::class, 'destroy'])
            ->name('followups.destroy');

        Route::get('/catalogos/comorbilidades', [ComorbidityController::class, 'index'])
            ->name('comorbidities.index');

        Route::get('/catalogos/comorbilidades/create', [ComorbidityController::class, 'create'])
            ->name('comorbidities.create');

        Route::post('/catalogos/comorbilidades', [ComorbidityController::class, 'store'])
            ->name('comorbidities.store');

        Route::get('/catalogos/comorbilidades/{comorbidity}/edit', [ComorbidityController::class, 'edit'])
            ->name('comorbidities.edit');

        Route::put('/catalogos/comorbilidades/{comorbidity}', [ComorbidityController::class, 'update'])
            ->name('comorbidities.update');

        Route::patch('/catalogos/comorbilidades/{comorbidity}/toggle', [ComorbidityController::class, 'toggle'])
            ->name('comorbidities.toggle');

        Route::get('/catalogos/colonias', [ColoniaController::class, 'index'])
            ->name('colonias.index');

        Route::get('/catalogos/colonias/create', [ColoniaController::class, 'create'])
            ->name('colonias.create');

        Route::post('/catalogos/colonias', [ColoniaController::class, 'store'])
            ->name('colonias.store');

        Route::get('/catalogos/colonias/{colonia}/edit', [ColoniaController::class, 'edit'])
            ->name('colonias.edit');

        Route::put('/catalogos/colonias/{colonia}', [ColoniaController::class, 'update'])
            ->name('colonias.update');

        Route::patch('/catalogos/colonias/{colonia}/toggle', [ColoniaController::class, 'toggle'])
            ->name('colonias.toggle');

        Route::post('/catalogos/colonias/quick-store', [ColoniaController::class, 'quickStore'])
            ->name('colonias.quickStore');
    });

    /*
    |--------------------------------------------------------------------------
    | Pacientes - consulta individual
    |--------------------------------------------------------------------------
    */

    // DEBE estar después de /patients/create y /patients/{patient}/edit
    Route::get('/patients/{patient}', [PatientController::class, 'show'])
        ->name('patients.show');

    /*
    |--------------------------------------------------------------------------
    | Reportes
    |--------------------------------------------------------------------------
    */

    Route::get('/reports/patient/{patient}', [ReportController::class, 'patient'])
        ->name('reports.patient');

    Route::get('/reports/global', [ReportController::class, 'global'])
        ->name('reports.global');

    Route::get('/reports/global/csv', [ReportController::class, 'globalCsv'])
        ->name('reports.global.csv');

    Route::get('/reports/global/excel', [ReportController::class, 'globalExcel'])
        ->name('reports.global.excel');


    /*
    |--------------------------------------------------------------------------
    | Usuarios - Capturista
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:capturista')->group(function () {

        Route::get('/users', [UserController::class, 'index'])
            ->name('users.index');

        Route::get('/users/create', [UserController::class, 'create'])
            ->name('users.create');

        Route::post('/users', [UserController::class, 'store'])
            ->name('users.store');

        Route::get('/users/{user}/edit', [UserController::class, 'edit'])
            ->name('users.edit');

        Route::put('/users/{user}', [UserController::class, 'update'])
            ->name('users.update');

        Route::delete('/users/{user}', [UserController::class, 'destroy'])
            ->name('users.destroy');
    });
});
