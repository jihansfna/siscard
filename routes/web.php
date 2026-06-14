<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MasterEmployeeController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\ExportImportController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\UserController;

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
Route::get('/verify/{token}', [CardController::class, 'verify'])->name('card.verify')->where('token', '[A-Za-z0-9_\-]+');
Route::get('/verify/{token}/pdf', [CardController::class, 'verifyPdf'])->name('card.verify.pdf')->where('token', '[A-Za-z0-9_\-]+');
Route::get('/qr-image', [CardController::class, 'qrImage'])->name('qr.image');

// --- Auth route (hanya bisa diakses jika sudah login) ---
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/password/change', [AuthController::class, 'changePassword'])->name('password.change');
});

// --- Admin routes (role: admin / HRD) ---
Route::middleware(['auth', 'role:admin', 'check.default.password'])->prefix('dashboard')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Bulk Delete Routes
    Route::post('/employees/bulk-delete', [MasterEmployeeController::class, 'bulkDestroy'])->name('dashboard.employees.bulk_destroy');
    Route::post('/members/bulk-delete', [MemberController::class, 'bulkDestroy'])->name('dashboard.members.bulk_destroy');
    Route::post('/feedbacks/bulk-delete', [FeedbackController::class, 'bulkDestroy'])->name('dashboard.feedbacks.bulk_destroy');

    // Export & Import Routes
    Route::prefix('export')->group(function () {
        Route::get('/employees/excel', [ExportImportController::class, 'exportEmployeesExcel'])->name('dashboard.export.employees.excel');
        Route::get('/employees/pdf', [ExportImportController::class, 'exportEmployeesPdf'])->name('dashboard.export.employees.pdf');
        Route::get('/members/excel', [ExportImportController::class, 'exportMembersExcel'])->name('dashboard.export.members.excel');
        Route::get('/members/pdf', [ExportImportController::class, 'exportMembersPdf'])->name('dashboard.export.members.pdf');
        Route::get('/feedbacks/excel', [ExportImportController::class, 'exportFeedbacksExcel'])->name('dashboard.export.feedbacks.excel');
        Route::get('/feedbacks/pdf', [ExportImportController::class, 'exportFeedbacksPdf'])->name('dashboard.export.feedbacks.pdf');
    });
    Route::get('/import/employees/template', [ExportImportController::class, 'importEmployeesTemplate'])->name('dashboard.import.employees.template');
    Route::post('/import/employees', [ExportImportController::class, 'importEmployees'])->name('dashboard.import.employees');

    // Master Employee CRUD
    Route::resource('employees', MasterEmployeeController::class)->except(['create', 'edit'])->names('dashboard.employees');
    Route::patch('/employees/{employee}/set-inactive', [MasterEmployeeController::class, 'setInactive'])->name('dashboard.employees.set_inactive');

    // Members
    Route::get('/members', [MemberController::class, 'index'])->name('dashboard.members');
    Route::post('/members', [MemberController::class, 'store'])->name('dashboard.members.store');
    Route::put('/members/{member}', [MemberController::class, 'update'])->name('dashboard.members.update');
    Route::get('/members/{member}/logs', [MemberController::class, 'logs'])->name('dashboard.members.logs');

    // Card download (admin)
    Route::get('/members/{id}/card/download', [CardController::class, 'download'])->name('dashboard.members.card.download');
    Route::get('/members/{id}/card/preview', [CardController::class, 'preview'])->name('dashboard.members.card.preview');

    Route::get('/feedbacks', [FeedbackController::class, 'indexAdmin'])->name('dashboard.feedbacks');
    Route::post('/feedbacks/{feedback}/complete', [FeedbackController::class, 'complete'])->name('dashboard.feedbacks.complete');

    Route::get('/history', [HistoryController::class, 'index'])->name('dashboard.history');
    Route::get('/export/history/excel', [HistoryController::class, 'exportExcel'])->name('dashboard.export.history.excel');
    Route::get('/export/history/pdf', [HistoryController::class, 'exportPdf'])->name('dashboard.export.history.pdf');
});

// --- User routes (role: user / Employee) ---
Route::middleware(['auth', 'role:user', 'check.default.password'])->prefix('home')->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('user.home');
    Route::post('/confirm-membership/{id}', [UserController::class, 'confirmMembership'])->name('user.confirm_membership');
    Route::post('/feedbacks', [FeedbackController::class, 'store'])->name('user.feedbacks.store');
    Route::delete('/feedbacks/{feedback}', [FeedbackController::class, 'destroyUser'])->name('user.feedbacks.destroy');
    
    // Card download (user - own card)
    Route::get('/card/download', [CardController::class, 'downloadOwn'])->name('user.card.download');
});
