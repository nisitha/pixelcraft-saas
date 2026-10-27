<?php

use App\Http\Controllers\PayrollPdfController;
use App\Http\Controllers\PublicDocumentController;
use App\Models\Plan;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

Route::get('/', function () {
    try {
        $plans = Schema::hasTable('plans')
            ? Plan::where('is_active', true)->orderBy('price_monthly')->get()
            : collect();
    } catch (Throwable $e) {
        $plans = collect();
    }

    return response()
        ->view('welcome', compact('plans'))
        ->header('Cache-Control', 'no-cache, no-store, must-revalidate, max-age=0')
        ->header('Pragma', 'no-cache')
        ->header('Expires', '0');
});

Route::get('/public/documents/{type}/{id}', [PublicDocumentController::class, 'show'])
    ->name('public.document.show');

Route::get('/public/documents/{type}/{id}/download', [PublicDocumentController::class, 'download'])
    ->name('public.document.download');

Route::get('/payroll/items/{payrollItem}/payslip', [PayrollPdfController::class, 'downloadPayslip'])
    ->name('payroll.payslip.download');

Route::get('/payrolls/{payroll}/payslips-all', [PayrollPdfController::class, 'downloadAllPayslips'])
    ->name('payroll.payslips.all');

Route::get('/app/{tenant}/billing', function ($tenant) {
    return redirect("/app/{$tenant}/subscription-billing");
})->middleware(['web', 'auth'])->name('tenant.billing');
