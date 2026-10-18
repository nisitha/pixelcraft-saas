<?php

namespace Tests\Feature;

use App\Filament\App\Pages\Tenancy\EditCompanyProfile;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\Quotation;
use App\Models\User;
use App\Services\PdfService;
use Database\Seeders\DatabaseSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class CompanyLogoAndPdfMemoryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
        Storage::fake('public');
    }

    public function test_pdf_generation_with_company_logo_and_signature_succeeds(): void
    {
        // 1. Create a dummy PNG logo and signature
        $logoDir = storage_path('app/public/company-logos');
        $sigDir = storage_path('app/public/company-signatures');
        if (! file_exists($logoDir)) {
            mkdir($logoDir, 0755, true);
        }
        if (! file_exists($sigDir)) {
            mkdir($sigDir, 0755, true);
        }

        $logoPath = $logoDir.'/unit-test-logo.png';
        $sigPath = $sigDir.'/unit-test-sig.png';

        $img = imagecreatetruecolor(200, 100);
        imagepng($img, $logoPath);
        imagedestroy($img);

        $imgSig = imagecreatetruecolor(150, 60);
        imagepng($imgSig, $sigPath);
        imagedestroy($imgSig);

        $this->assertFileExists($logoPath);
        $this->assertFileExists($sigPath);

        $company = Company::where('slug', 'lanka-solar-solutions')->first();
        $company->logo_path = 'company-logos/unit-test-logo.png';
        $settings = $company->settings ?? [];
        $settings['signature_path'] = 'company-signatures/unit-test-sig.png';
        $company->settings = $settings;
        $company->save();

        $quotation = Quotation::where('company_id', $company->id)->first();
        $this->assertNotNull($quotation);

        $pdfService = app(PdfService::class);

        // Generate Quotation PDF
        $quotationPdf = $pdfService->generateQuotationPdf($quotation);
        $outputQuote = $quotationPdf->output();
        $this->assertNotEmpty($outputQuote);
        $this->assertStringStartsWith('%PDF-', $outputQuote);

        // Generate Invoice PDF
        $invoice = Invoice::where('company_id', $company->id)->first();
        $this->assertNotNull($invoice);

        $invoicePdf = $pdfService->generateInvoicePdf($invoice);
        $outputInvoice = $invoicePdf->output();
        $this->assertNotEmpty($outputInvoice);
        $this->assertStringStartsWith('%PDF-', $outputInvoice);

        // Clean up temporary images
        @unlink($logoPath);
        @unlink($sigPath);
    }

    public function test_pdf_generation_ignores_svg_gracefully(): void
    {
        $logoDir = storage_path('app/public/company-logos');
        if (! file_exists($logoDir)) {
            mkdir($logoDir, 0755, true);
        }
        $svgPath = $logoDir.'/fake-logo.svg';
        file_put_contents($svgPath, '<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100"><circle cx="50" cy="50" r="40" stroke="green" stroke-width="4" fill="yellow" /></svg>');

        $company = Company::where('slug', 'lanka-solar-solutions')->first();
        $company->logo_path = 'company-logos/fake-logo.svg';
        $company->save();

        $quotation = Quotation::where('company_id', $company->id)->first();
        $pdfService = app(PdfService::class);
        $pdf = $pdfService->generateQuotationPdf($quotation);
        $output = $pdf->output();

        $this->assertNotEmpty($output);
        $this->assertStringStartsWith('%PDF-', $output);

        @unlink($svgPath);
    }

    public function test_edit_company_profile_saves_logo_and_mirrors_to_settings(): void
    {
        $company = Company::where('slug', 'lanka-solar-solutions')->first();
        $owner = User::where('email', 'owner@lankasolar.lk')->first();
        $this->actingAs($owner);

        Filament::setCurrentPanel(Filament::getPanel('app'));
        Filament::setTenant($company);

        $file = UploadedFile::fake()->image('saved-logo.png', 200, 100);

        Livewire::test(EditCompanyProfile::class)
            ->fillForm([
                'name' => 'Lanka Solar Solutions Updated',
                'logo_path' => [$file],
                'settings' => [
                    'primary_color' => '#10b981',
                ],
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $company->refresh();
        $this->assertEquals('Lanka Solar Solutions Updated', $company->name);
        $this->assertNotNull($company->logo_path);
        $this->assertStringStartsWith('company-logos/', $company->logo_path);
        $this->assertIsArray($company->settings);
        $this->assertEquals($company->logo_path, $company->settings['logo_path']);
        $this->assertEquals('#10b981', $company->settings['primary_color']);
    }

    public function test_image_optimization_downscales_large_images(): void
    {
        $company = Company::where('slug', 'lanka-solar-solutions')->first();
        $owner = User::where('email', 'owner@lankasolar.lk')->first();
        $this->actingAs($owner);

        Filament::setCurrentPanel(Filament::getPanel('app'));
        Filament::setTenant($company);

        $largeFile = UploadedFile::fake()->image('large-test.png', 1600, 1200);

        Livewire::test(EditCompanyProfile::class)
            ->fillForm([
                'logo_path' => [$largeFile],
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $company->refresh();
        $this->assertNotNull($company->logo_path);

        $fullPath = Storage::disk('public')->path($company->logo_path);
        $this->assertFileExists($fullPath);

        [$wAfter, $hAfter] = getimagesize($fullPath);
        $this->assertLessThanOrEqual(800, $wAfter);
        $this->assertLessThanOrEqual(600, $hAfter);

        @unlink($fullPath);
    }
}
