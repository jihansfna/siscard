<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

// --- Guest routes (hanya bisa diakses jika belum login) ---
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
    
    Route::post('/password/reset', [AuthController::class, 'resetPassword'])->name('password.reset.submit');
});

// --- Auth route (hanya bisa diakses jika sudah login) ---
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// --- Admin routes (role: admin / HRD) ---
Route::middleware(['auth', 'role:admin'])->prefix('dashboard')->group(function () {
    Route::get('/', function () {
        return view('dashboard');
    })->name('dashboard');

    // Bulk Delete Routes
    Route::post('/employees/bulk-delete', [\App\Http\Controllers\MasterEmployeeController::class, 'bulkDestroy'])->name('dashboard.employees.bulk_destroy');
    Route::post('/members/bulk-delete', [\App\Http\Controllers\MemberController::class, 'bulkDestroy'])->name('dashboard.members.bulk_destroy');
    Route::post('/feedbacks/bulk-delete', [\App\Http\Controllers\FeedbackController::class, 'bulkDestroy'])->name('dashboard.feedbacks.bulk_destroy');

    // Export & Import Routes
    Route::prefix('export')->group(function () {
        Route::get('/employees/excel', [\App\Http\Controllers\ExportImportController::class, 'exportEmployeesExcel'])->name('dashboard.export.employees.excel');
        Route::get('/employees/pdf', [\App\Http\Controllers\ExportImportController::class, 'exportEmployeesPdf'])->name('dashboard.export.employees.pdf');
        Route::get('/members/excel', [\App\Http\Controllers\ExportImportController::class, 'exportMembersExcel'])->name('dashboard.export.members.excel');
        Route::get('/members/pdf', [\App\Http\Controllers\ExportImportController::class, 'exportMembersPdf'])->name('dashboard.export.members.pdf');
        Route::get('/feedbacks/excel', [\App\Http\Controllers\ExportImportController::class, 'exportFeedbacksExcel'])->name('dashboard.export.feedbacks.excel');
        Route::get('/feedbacks/pdf', [\App\Http\Controllers\ExportImportController::class, 'exportFeedbacksPdf'])->name('dashboard.export.feedbacks.pdf');
    });
    Route::get('/import/employees/template', [\App\Http\Controllers\ExportImportController::class, 'importEmployeesTemplate'])->name('dashboard.import.employees.template');
    Route::post('/import/employees', [\App\Http\Controllers\ExportImportController::class, 'importEmployees'])->name('dashboard.import.employees');

    // Master Employee CRUD
    Route::resource('employees', \App\Http\Controllers\MasterEmployeeController::class)->names('dashboard.employees');


    Route::get('/members', [\App\Http\Controllers\MemberController::class, 'index'])->name('dashboard.members');
    Route::post('/members', [\App\Http\Controllers\MemberController::class, 'store'])->name('dashboard.members.store');

    Route::get('/feedbacks', [\App\Http\Controllers\FeedbackController::class, 'indexAdmin'])->name('dashboard.feedbacks');
    Route::post('/feedbacks/{feedback}/complete', [\App\Http\Controllers\FeedbackController::class, 'complete'])->name('dashboard.feedbacks.complete');

    Route::get('/history', function () {
        return view('dashboard.history');
    })->name('dashboard.history');
});

// --- User routes (role: user / Employee) ---
Route::middleware(['auth', 'role:user'])->prefix('home')->group(function () {
    Route::get('/', [\App\Http\Controllers\UserController::class, 'index'])->name('user.home');
    Route::post('/confirm-membership/{id}', [\App\Http\Controllers\UserController::class, 'confirmMembership'])->name('user.confirm_membership');
    Route::post('/feedbacks', [\App\Http\Controllers\FeedbackController::class, 'store'])->name('user.feedbacks.store');
    Route::delete('/feedbacks/{feedback}', [\App\Http\Controllers\FeedbackController::class, 'destroyUser'])->name('user.feedbacks.destroy');
});
