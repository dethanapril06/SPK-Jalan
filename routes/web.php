<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;

// Admin
use App\Http\Controllers\Admin\AssessmentAspectController as AdminAssessmentAspectController;
use App\Http\Controllers\Admin\AssessmentReportController as AdminAssessmentReportController;
use App\Http\Controllers\Admin\AlternativeController as AdminAlternativeController;
use App\Http\Controllers\Admin\CriteriaController as AdminCriteriaController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\MfepCalculationController as AdminMfepCalculationController;
use App\Http\Controllers\Admin\SurveyorAssignmentController as AdminSurveyorAssignmentController;
use App\Http\Controllers\Admin\SurveyorController as AdminSurveyorController;
use App\Http\Controllers\Admin\SubCriteriaController as AdminSubCriteriaController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\KepalaDinas\AssessmentReportController as KepalaDinasAssessmentReportController;
use App\Http\Controllers\KepalaDinas\DashboardController as KepalaDinasDashboardController;
use App\Http\Controllers\KepalaDinas\MfepController as KepalaDinasMfepController;

// Surveyor
use App\Http\Controllers\Surveyor\DashboardController as SurveyorDashboardController;
use App\Http\Controllers\Surveyor\AssessmentController as SurveyorAssessmentController;

Route::get('/', fn() => redirect()->route('login'));

// Ambil notifikasi belum dibaca (untuk polling)
Route::get('/notifikasi', function () {
    return response()->json(auth()->user()->unreadNotifications);
})->middleware('auth');

// Tandai sudah dibaca
Route::post('/notifikasi/baca', function () {
    auth()->user()->unreadNotifications->markAsRead();
    return response()->json(['message' => 'ok']);
})->middleware('auth');

require __DIR__.'/auth.php';

// Profile (semua role yang sudah login)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Surveyor
Route::middleware(['auth', 'role:surveyor'])
    ->prefix('surveyor')
    ->name('surveyor.')
    ->group(function () {
        Route::get('/dashboard', [SurveyorDashboardController::class, 'index'])->name('dashboard');
        Route::get('/tasks/{assignment}', [SurveyorDashboardController::class, 'showTask'])->name('task.show');
        Route::resource('assessments', SurveyorAssessmentController::class)
            ->parameters(['assessments' => 'assignment'])
            ->only(['show', 'edit', 'update']);
        
        Route::get('/notifications', function () {
            $notifications = auth()->user()->notifications()->latest()->paginate(10);
            auth()->user()->unreadNotifications->markAsRead();
            return view('surveyor.notifications.index', compact('notifications'));
        })->name('notifications.index');

        Route::post('/notifications/read', function () {
            auth()->user()->unreadNotifications->markAsRead();
            return response()->json(['message' => 'ok']);
        })->name('notifications.read');
    });

// Kepala Dinas
Route::middleware(['auth', 'role:kepala_dinas'])
    ->prefix('kepala-dinas')
    ->name('kepala-dinas.')
    ->group(function () {
        Route::get('/dashboard', [KepalaDinasDashboardController::class, 'index'])->name('dashboard');

        Route::get('/reports/assessments', [KepalaDinasAssessmentReportController::class, 'index'])
            ->name('reports.assessments');
        Route::get('/reports/assessments/excel', [KepalaDinasAssessmentReportController::class, 'exportExcel'])
            ->name('reports.assessments.excel');
        Route::get('/reports/assessments/pdf', [KepalaDinasAssessmentReportController::class, 'exportPdf'])
            ->name('reports.assessments.pdf');

        Route::get('/mfep/ranking', [KepalaDinasMfepController::class, 'ranking'])
            ->name('mfep.ranking');
        Route::get('/mfep/{calculation}', [KepalaDinasMfepController::class, 'show'])
            ->name('mfep.show');
        Route::get('/mfep/{calculation}/pdf', [KepalaDinasMfepController::class, 'exportPdf'])
            ->name('mfep.pdf');
    });

// Admin
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/reports/assessments', [AdminAssessmentReportController::class, 'index'])
            ->name('reports.assessments');
        Route::get('/reports/assessments/excel', [AdminAssessmentReportController::class, 'exportExcel'])
            ->name('reports.assessments.excel');
        Route::get('/reports/assessments/pdf', [AdminAssessmentReportController::class, 'exportPdf'])
            ->name('reports.assessments.pdf');
        
        // MFEP Calculation Routes
        Route::get('/mfep/ranking', [AdminMfepCalculationController::class, 'ranking'])
            ->name('mfep.ranking');
        Route::get('/mfep/calculate', [AdminMfepCalculationController::class, 'create'])
            ->name('mfep.create');
        Route::post('/mfep/calculate', [AdminMfepCalculationController::class, 'store'])
            ->name('mfep.store');
        Route::get('/mfep/{calculation}', [AdminMfepCalculationController::class, 'show'])
            ->name('mfep.show');
        Route::get('/mfep/{calculation}/pdf', [AdminMfepCalculationController::class, 'exportPdf'])
            ->name('mfep.pdf');
        Route::delete('/mfep/{calculation}', [AdminMfepCalculationController::class, 'destroy'])
            ->name('mfep.destroy');
        
        Route::resource('criteria', AdminCriteriaController::class)
            ->parameters(['criteria' => 'criteria']);
        Route::resource('sub-criteria', AdminSubCriteriaController::class)
            ->parameters(['sub-criteria' => 'subCriteria']);
        Route::resource('assessment-aspects', AdminAssessmentAspectController::class)
            ->parameters(['assessment-aspects' => 'assessmentAspect']);
        Route::resource('alternatives', AdminAlternativeController::class)
            ->parameters(['alternatives' => 'alternative']);
        Route::resource('assignments', AdminSurveyorAssignmentController::class)
            ->parameters(['assignments' => 'assignment']);
        Route::resource('surveyors', AdminSurveyorController::class)
            ->parameters(['surveyors' => 'surveyor']);
        Route::post('surveyors/{surveyor}/reset-password', [AdminSurveyorController::class, 'resetPassword'])
            ->name('surveyors.reset-password');
        Route::resource('users', AdminUserController::class)
            ->parameters(['users' => 'user']);
        Route::post('users/{user}/reset-password', [AdminUserController::class, 'resetPassword'])
            ->name('users.reset-password');
    });