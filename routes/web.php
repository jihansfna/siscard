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
    
    Route::post('/password/verify-badge', [AuthController::class, 'verifyBadge'])->name('password.verify_badge');
    Route::post('/password/verify-answer', [AuthController::class, 'verifySecurityAnswer'])->name('password.verify_answer');
    Route::post('/password/reset', [AuthController::class, 'resetPassword'])->name('password.reset.submit');
});

// --- Public route: QR verification (no auth needed) ---
Route::get('/verify/{token}', [\App\Http\Controllers\CardController::class, 'verify'])->name('card.verify')->where('token', '[A-Za-z0-9_\-]+');
Route::get('/verify/{token}/pdf', [\App\Http\Controllers\CardController::class, 'verifyPdf'])->name('card.verify.pdf')->where('token', '[A-Za-z0-9_\-]+');
Route::get('/qr-image', [\App\Http\Controllers\CardController::class, 'qrImage'])->name('qr.image');

// --- Auth route (hanya bisa diakses jika sudah login) ---
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/password/change', [AuthController::class, 'changePassword'])->name('password.change');
});

// --- Admin routes (role: admin / HRD) ---
Route::middleware(['auth', 'role:admin', 'check.default.password'])->prefix('dashboard')->group(function () {
    Route::get('/', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

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
    Route::resource('employees', \App\Http\Controllers\MasterEmployeeController::class)->except(['create', 'edit'])->names('dashboard.employees');
    Route::patch('/employees/{employee}/set-inactive', [\App\Http\Controllers\MasterEmployeeController::class, 'setInactive'])->name('dashboard.employees.set_inactive');

    // Members
    Route::get('/members', [\App\Http\Controllers\MemberController::class, 'index'])->name('dashboard.members');
    Route::post('/members', [\App\Http\Controllers\MemberController::class, 'store'])->name('dashboard.members.store');
    Route::put('/members/{member}', [\App\Http\Controllers\MemberController::class, 'update'])->name('dashboard.members.update');
    Route::get('/members/{member}/logs', [\App\Http\Controllers\MemberController::class, 'logs'])->name('dashboard.members.logs');

    // Card download (admin)
    Route::get('/members/{id}/card/download', [\App\Http\Controllers\CardController::class, 'download'])->name('dashboard.members.card.download');
    Route::get('/members/{id}/card/preview', [\App\Http\Controllers\CardController::class, 'preview'])->name('dashboard.members.card.preview');

    Route::get('/feedbacks', [\App\Http\Controllers\FeedbackController::class, 'indexAdmin'])->name('dashboard.feedbacks');
    Route::post('/feedbacks/{feedback}/complete', [\App\Http\Controllers\FeedbackController::class, 'complete'])->name('dashboard.feedbacks.complete');

    Route::get('/history', [\App\Http\Controllers\HistoryController::class, 'index'])->name('dashboard.history');
    Route::get('/export/history/excel', [\App\Http\Controllers\HistoryController::class, 'exportExcel'])->name('dashboard.export.history.excel');
    Route::get('/export/history/pdf', [\App\Http\Controllers\HistoryController::class, 'exportPdf'])->name('dashboard.export.history.pdf');
});

// --- User routes (role: user / Employee) ---
Route::middleware(['auth', 'role:user', 'check.default.password'])->prefix('home')->group(function () {
    Route::get('/', [\App\Http\Controllers\UserController::class, 'index'])->name('user.home');
    Route::post('/confirm-membership/{id}', [\App\Http\Controllers\UserController::class, 'confirmMembership'])->name('user.confirm_membership');
    Route::post('/feedbacks', [\App\Http\Controllers\FeedbackController::class, 'store'])->name('user.feedbacks.store');
    Route::delete('/feedbacks/{feedback}', [\App\Http\Controllers\FeedbackController::class, 'destroyUser'])->name('user.feedbacks.destroy');
    
    // Card download (user - own card)
    Route::get('/card/download', [\App\Http\Controllers\CardController::class, 'downloadOwn'])->name('user.card.download');
});
