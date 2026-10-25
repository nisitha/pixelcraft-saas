<?php

namespace App\Http\Controllers;

use App\Models\Payroll;
use App\Models\PayrollItem;
use App\Services\PdfService;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PayrollPdfController extends Controller
{
    public function downloadPayslip(PayrollItem $payrollItem, Request $request, PdfService $pdfService): Response
    {
        // Enforce tenant boundary check if tenant is present
        $tenant = Filament::getTenant();
        if ($tenant && $payrollItem->company_id !== $tenant->id) {
            abort(403, 'Unauthorized access to tenant payroll document.');
        }

        $pdf = $pdfService->generatePayslipPdf($payrollItem);
        $fileName = PdfService::getPayslipFileName($payrollItem);

        if ($request->boolean('stream')) {
            return $pdf->stream($fileName);
        }

        return $pdf->download($fileName);
    }

    public function downloadAllPayslips(Payroll $payroll, Request $request, PdfService $pdfService): Response
    {
        $tenant = Filament::getTenant();
        if ($tenant && $payroll->company_id !== $tenant->id) {
            abort(403, 'Unauthorized access to tenant payroll document.');
        }

        $pdf = $pdfService->generateCombinedPayslipsPdf($payroll);
        $fileName = PdfService::getAllPayslipsFileName($payroll);

        if ($request->boolean('stream')) {
            return $pdf->stream($fileName);
        }

        return $pdf->download($fileName);
    }
}
