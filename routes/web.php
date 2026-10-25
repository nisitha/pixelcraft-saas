<?php

use App\Http\Controllers\PayrollPdfController;
use App\Http\Controllers\PublicDocumentController;
use App\Models\Plan;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

Route::get('/', function () {
    $plans = Schema::hasTable('plans')
        ? Plan::where('is_active', true)->orderBy('price_monthly')->get()
        : collect();

    return view('welcome', compact('plans'));
});

Route::get('/public/documents/{type}/{id}', [PublicDocumentController::class, 'show'])
    ->name('public.document.show');

Route::get('/public/documents/{type}/{id}/download', [PublicDocumentController::class, 'download'])
    ->name('public.document.download');

Route::get('/payroll/items/{payrollItem}/payslip', [PayrollPdfController::class, 'downloadPayslip'])
    ->name('payroll.payslip.download');

Route::get('/payrolls/{payroll}/payslips-all', [PayrollPdfController::class, 'downloadAllPayslips'])
    ->name('payroll.payslips.all');
