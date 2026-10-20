<?php

namespace App\Filament\App\Resources\Invoices\Pages;

use App\Filament\App\Concerns\HasZohoDocumentEditor;
use App\Filament\App\Resources\CompletionCertificates\CompletionCertificateResource;
use App\Filament\App\Resources\Invoices\InvoiceResource;
use App\Models\CompletionCertificate;
use App\Models\Invoice;
use App\Services\PdfService;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

class EditInvoice extends EditRecord
{
    use HasZohoDocumentEditor;

    protected static string $resource = InvoiceResource::class;

    public function mount(int|string $record): void
    {
        $this->documentType = 'invoice';
        parent::mount($record);
        $this->initZohoDocument();
    }

    protected function getFormActions(): array
    {
        return [];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('downloadPdf')
                ->label('Download PDF')
                ->icon(Heroicon::ArrowDownTray)
                ->color('primary')
                ->action(function () {
                    /** @var Invoice $record */
                    $record = $this->record;

                    $safeNumber = str_replace(['/', '\\'], '-', $record->invoice_number);
                    $filename = "Invoice-{$safeNumber}.pdf";

                    return response()->streamDownload(
                        fn () => print (app(PdfService::class)->generateInvoicePdf($record, 'itemized')->output()),
                        $filename,
                        ['Content-Type' => 'application/pdf']
                    );
                }),

            Action::make('downloadCustomerPdf')
                ->label('Customer Package PDF')
                ->icon(Heroicon::DocumentText)
                ->color('gray')
                ->action(function () {
                    /** @var Invoice $record */
                    $record = $this->record;

                    $safeNumber = str_replace(['/', '\\'], '-', $record->invoice_number);
                    $filename = "Invoice-{$safeNumber}-Package.pdf";

                    return response()->streamDownload(
                        fn () => print (app(PdfService::class)->generateInvoicePdf($record, 'package_total_only')->output()),
                        $filename,
                        ['Content-Type' => 'application/pdf']
                    );
                }),

            Action::make('issueCertificate')
                ->label('Issue Certificate')
                ->icon(Heroicon::AcademicCap)
                ->color('info')
                ->action(function (Action $action) {
                    /** @var Invoice $record */
                    $record = $this->record;

                    $certificate = CompletionCertificate::create([
                        'company_id' => $record->company_id,
                        'contact_id' => $record->contact_id,
                        'invoice_id' => $record->id,
                        'quotation_id' => $record->quotation_id,
                        'certificate_number' => 'CERT-VHE/'.date('Y').'/RU/'.str_pad((string) rand(1, 999), 3, '0', STR_PAD_LEFT).'/'.str_pad((string) rand(1, 99), 3, '0', STR_PAD_LEFT),
                        'customer_name' => strtoupper($record->contact?->name ?? 'Customer'),
                        'system_capacity' => ($record->quotation?->system_kw ? $record->quotation->system_kw.' kW Solar System' : '05 kW+05kw Battery offgrid Solar System'),
                        'site_address' => $record->contact?->address ?: '260/20, Negombo Road, Sri Lanka',
                        'commissioning_date' => now()->toDateString(),
                        'grid_connection' => 'On-Grid Net Metering',
                        'warranty_period' => '30 Years (Panels) / 10 Years (Inverter+Battery)',
                        'system_status' => 'Commissioned & Operational',
                        'verification_status' => 'Engineering Safety Compliant',
                        'compliance_note' => 'Installed, tested, and commissioned in accordance with standard safety regulations.',
                        'lead_engineer_name' => 'Lead Engineer',
                        'lead_engineer_title' => 'Technical Operations',
                        'director_name' => 'Managing Director',
                        'director_title' => $record->company?->name ?: 'Vertex Home Solutions (Pvt) Ltd',
                        'signed_date' => now()->toDateString(),
                        'paper_size' => 'a4',
                        'orientation' => 'landscape',
                        'status' => 'draft',
                    ]);

                    Notification::make()
                        ->title("Completion Certificate created: {$certificate->certificate_number}")
                        ->success()
                        ->send();

                    return $action->redirect(CompletionCertificateResource::getUrl('edit', ['record' => $certificate->id]));
                }),

            DeleteAction::make(),
        ];
    }
}
